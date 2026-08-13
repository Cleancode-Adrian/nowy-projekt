<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Blokuje pliki, które mogą zostać wykonane albo zinterpretowane przez przeglądarkę
 * po zapisaniu w publicznym katalogu storage.
 *
 * Sprawdzamy jednocześnie:
 *  - rozszerzenie zadeklarowane przez klienta (nazwa pliku),
 *  - rozszerzenie wyliczone z realnego typu MIME,
 *  - wszystkie człony nazwy (blokuje "shell.php.jpg" i podobne).
 */
class SafeUpload implements ValidationRule
{
    /** @param array<int, string> $allowedExtensions */
    public function __construct(private array $allowedExtensions = [])
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail('Nieprawidłowy plik.');
            return;
        }

        $blocked = array_map('strtolower', config('security.uploads.blocked_extensions', []));
        $allowed = array_map('strtolower', $this->allowedExtensions);

        $clientName = (string) $value->getClientOriginalName();

        // Każdy człon nazwy po kropce - "avatar.php.png" ma człony: php, png.
        $parts = array_map('strtolower', array_slice(explode('.', $clientName), 1));

        foreach ($parts as $part) {
            if (in_array($part, $blocked, true)) {
                $fail('Ten typ pliku jest zablokowany ze względów bezpieczeństwa.');
                return;
            }
        }

        $extension = strtolower($value->getClientOriginalExtension());
        $guessed = strtolower((string) $value->guessExtension());

        if ($extension === '' ) {
            $fail('Plik musi mieć rozszerzenie.');
            return;
        }

        if (in_array($guessed, $blocked, true)) {
            $fail('Zawartość pliku nie odpowiada dozwolonym typom.');
            return;
        }

        if ($allowed !== [] && !in_array($extension, $allowed, true)) {
            $fail('Dozwolone rozszerzenia: ' . implode(', ', $allowed) . '.');
            return;
        }

        // Pusty plik nie ma wykrywalnego MIME - odrzucamy, żeby nie ominąć kontroli.
        if ($value->getSize() === 0) {
            $fail('Plik jest pusty.');
        }
    }
}
