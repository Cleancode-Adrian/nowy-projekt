<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'announcement_id',
        'filename',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getIconAttribute(): string
    {
        return match(true) {
            str_contains($this->mime_type, 'pdf') => 'fa-file-pdf text-red-500',
            str_contains($this->mime_type, 'image') => 'fa-file-image text-blue-500',
            str_contains($this->mime_type, 'zip') => 'fa-file-zipper text-yellow-500',
            str_contains($this->mime_type, 'word') => 'fa-file-word text-blue-600',
            default => 'fa-file text-gray-500',
        };
    }
}

