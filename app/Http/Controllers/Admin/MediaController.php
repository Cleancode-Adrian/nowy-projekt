<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'avif'];

    public function index(Request $request)
    {
        $disk = Storage::disk('public');
        $currentPath = $this->sanitizeFolder($request->get('dir', ''));

        $directories = collect($disk->directories($currentPath))
            ->map(fn ($dir) => [
                'name' => basename($dir),
                'path' => $dir,
                'total' => count($disk->files($dir)),
            ])->sortBy('name')->values();

        $files = collect($disk->files($currentPath))
            ->map(function ($path) use ($disk) {
                try {
                    $media = $this->syncMediaRecord($path, $disk);
                    $modified = $disk->exists($path) ? $disk->lastModified($path) : time();

                    return [
                        'path' => $path,
                        'name' => basename($path),
                        'extension' => $media->extension ?? strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                        'size' => $this->formatSize($media->size ?? 0),
                        'size_raw' => $media->size ?? 0,
                        'modified' => Carbon::createFromTimestamp($modified)->diffForHumans(),
                        'modified_full' => Carbon::createFromTimestamp($modified)->format('d.m.Y H:i'),
                        'url' => asset('storage/' . ($media->webp_path ?? $path)),
                        'original_url' => asset('storage/' . $path),
                        'is_image' => $media->is_image ?? false,
                        'media' => $media,
                    ];
                } catch (\Exception $e) {
                    Log::error('Błąd przetwarzania pliku: ' . $e->getMessage(), ['path' => $path]);
                    return null;
                }
            })
            ->filter() // Usuń null values
            ->sortByDesc(fn ($file) => $file['media']->updated_at ?? now())
            ->values();

        $parentPath = null;
        if ($currentPath !== '') {
            $parentPath = trim(dirname($currentPath), '.\\/');
            if ($parentPath === '') {
                $parentPath = null;
            }
        }

        return view('admin.media.index', [
            'currentPath' => $currentPath,
            'parentPath' => $parentPath,
            'directories' => $directories,
            'files' => $files,
            'totalSize' => $this->formatSize($files->sum(fn ($file) => $file['size_raw'])),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'folder' => ['nullable', 'regex:/^[A-Za-z0-9_\-\/]*$/'],
            'files' => ['required', 'array'],
            'files.*' => ['file', 'max:5120'],
        ]);

        $folder = $this->sanitizeFolder($validated['folder'] ?? '');
        $disk = Storage::disk('public');
        $uploaded = 0;

        foreach ($request->file('files') as $file) {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = time() . '-' . Str::slug($originalName ?: 'plik');
            $extension = strtolower($file->getClientOriginalExtension());
            $storedName = $filename . ($extension ? '.' . $extension : '');
            $storedPath = ltrim(trim($folder . '/' . $storedName, '/'), '/');

            $file->storeAs($folder, $storedName, 'public');
            $this->syncMediaRecord($storedPath, $disk);
            $uploaded++;
        }

        return back()->with('success', "Dodano {$uploaded} plik(i).");
    }

    public function update(Request $request, MediaItem $media)
    {
        $validated = $request->validate([
            'alt_text' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'string', 'max:255'],
        ]);

        $tags = null;
        if (!empty($validated['tags'])) {
            $tags = collect(explode(',', $validated['tags']))
                ->map(fn ($tag) => trim($tag))
                ->filter()
                ->values()
                ->all();
        }

        $media->update([
            'alt_text' => $validated['alt_text'] ?? null,
            'tags' => $tags,
        ]);

        return back()->with('success', 'Metadane zostały zaktualizowane.');
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'path' => ['required', 'string'],
        ]);

        $path = trim($validated['path'], '/');
        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            return back()->with('error', 'Plik nie istnieje.');
        }

        $media = MediaItem::where('path', $path)->first();
        if ($media && $media->webp_path) {
            $disk->delete($media->webp_path);
        }

        $disk->delete($path);
        $media?->delete();

        return back()->with('success', 'Plik został usunięty.');
    }

    public function picker(Request $request)
    {
        $query = MediaItem::query()->orderByDesc('created_at');

        if ($request->boolean('only_images', true)) {
            $query->where('is_image', true);
        }

        $items = $query->limit(200)->get()->map(function (MediaItem $item) {
            return [
                'id' => $item->id,
                'path' => $item->path,
                'filename' => $item->filename,
                'alt_text' => $item->alt_text,
                'tags' => $item->tags ?? [],
                'size' => $this->formatSize($item->size),
                'width' => $item->width,
                'height' => $item->height,
                'extension' => $item->extension,
                'is_image' => $item->is_image,
                'url' => asset('storage/' . ($item->webp_path ?? $item->path)),
                'original_url' => asset('storage/' . $item->path),
            ];
        });

        return response()->json($items);
    }

    private function sanitizeFolder(?string $folder): string
    {
        $clean = trim($folder ?? '', '/');
        $clean = preg_replace('/[^A-Za-z0-9_\-\/]/', '', $clean ?? '');

        return $clean ?? '';
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes, 1024));

        return round($bytes / pow(1024, $i), 2) . ' ' . $sizes[$i];
    }

    private function syncMediaRecord(string $path, $disk): MediaItem
    {
        $path = trim($path, '/');

        // Sprawdź czy plik istnieje
        if (!$disk->exists($path)) {
            // Jeśli plik nie istnieje, zwróć pusty rekord lub utwórz podstawowy
            return MediaItem::firstOrCreate(
                ['path' => $path],
                [
                    'disk' => 'public',
                    'filename' => basename($path),
                    'extension' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                    'size' => 0,
                    'is_image' => false,
                ]
            );
        }

        try {
            $fullPath = $disk->path($path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $size = $disk->exists($path) ? ($disk->size($path) ?? 0) : 0;
            $isImage = in_array($extension, self::IMAGE_EXTENSIONS, true);
            $width = null;
            $height = null;
            $mime = null;
            $webpPath = null;

            if ($isImage && function_exists('getimagesize') && file_exists($fullPath)) {
                $info = @getimagesize($fullPath);
                if ($info) {
                    $width = $info[0];
                    $height = $info[1];
                    $mime = $info['mime'] ?? null;
                }

                $optimized = $this->optimizeImage($fullPath, $extension, $width, $height);
                if ($optimized) {
                    [$width, $height] = $optimized;
                }

                $webpPath = $this->createWebpVersion($fullPath, $extension);
            } elseif (function_exists('mime_content_type') && file_exists($fullPath)) {
                $mime = @mime_content_type($fullPath) ?: null;
            }

            return MediaItem::updateOrCreate(
                ['path' => $path],
                [
                    'disk' => 'public',
                    'filename' => basename($path),
                    'extension' => $extension,
                    'size' => $size,
                    'is_image' => $isImage,
                    'width' => $width,
                    'height' => $height,
                    'mime_type' => $mime,
                    'webp_path' => $webpPath,
                ]
            );
        } catch (\Exception $e) {
            // W przypadku błędu, zwróć podstawowy rekord
            Log::error('Błąd syncMediaRecord: ' . $e->getMessage(), ['path' => $path]);

            return MediaItem::firstOrCreate(
                ['path' => $path],
                [
                    'disk' => 'public',
                    'filename' => basename($path),
                    'extension' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                    'size' => 0,
                    'is_image' => false,
                ]
            );
        }
    }

    private function optimizeImage(string $fullPath, string $extension, ?int $width, ?int $height): ?array
    {
        $extension = strtolower($extension);
        if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            return $width && $height ? [$width, $height] : null;
        }

        if (!extension_loaded('gd')) {
            return $width && $height ? [$width, $height] : null;
        }

        if (!$width || !$height) {
            $info = @getimagesize($fullPath);
            if (!$info) {
                return null;
            }
            $width = $info[0];
            $height = $info[1];
        }

        $targetWidth = 1600;
        if ($width <= $targetWidth) {
            if ($extension === 'jpg' || $extension === 'jpeg') {
                $image = @imagecreatefromjpeg($fullPath);
                if ($image) {
                    imagejpeg($image, $fullPath, 85);
                    imagedestroy($image);
                }
            }
            return [$width, $height];
        }

        $ratio = $targetWidth / $width;
        $targetHeight = (int) round($height * $ratio);

        $createFn = $extension === 'png' ? 'imagecreatefrompng' : 'imagecreatefromjpeg';
        $saveFn = $extension === 'png' ? 'imagepng' : 'imagejpeg';

        if (!function_exists($createFn) || !function_exists($saveFn)) {
            return [$width, $height];
        }

        $source = @$createFn($fullPath);
        if (!$source) {
            return [$width, $height];
        }

        $destination = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($extension === 'png') {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
        }

        imagecopyresampled($destination, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        if ($extension === 'png') {
            $saveFn($destination, $fullPath, 6);
        } else {
            $saveFn($destination, $fullPath, 85);
        }

        imagedestroy($source);
        imagedestroy($destination);

        return [$targetWidth, $targetHeight];
    }

    private function createWebpVersion(string $fullPath, string $extension): ?string
    {
        if (!function_exists('imagewebp')) {
            return null;
        }

        $extension = strtolower($extension);
        if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            return null;
        }

        $info = pathinfo($fullPath);
        $destination = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.webp';

        $createFn = $extension === 'png' ? 'imagecreatefrompng' : 'imagecreatefromjpeg';
        if (!function_exists($createFn)) {
            return null;
        }

        $image = @$createFn($fullPath);
        if (!$image) {
            return null;
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        if (!@imagewebp($image, $destination, 85)) {
            imagedestroy($image);
            return null;
        }

        imagedestroy($image);

        $publicPath = Storage::disk('public')->path('');
        $relative = trim(str_replace($publicPath, '', $destination), DIRECTORY_SEPARATOR);

        return $relative;
    }
}

