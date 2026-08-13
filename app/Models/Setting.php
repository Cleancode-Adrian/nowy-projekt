<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    /**
     * Klucze traktowane jako sekrety - w bazie trzymane zaszyfrowane APP_KEY.
     */
    private const ENCRYPTED_KEYS = [
        'openai_api_key',
        'unsplash_access_key',
        'gemini_api_key',
    ];

    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->decryptIfNeeded($value),
            set: fn (?string $value) => $this->encryptIfNeeded($value),
        );
    }

    /**
     * Odczyt wartości ustawienia. Zawsze przez model - zapytania typu
     * Setting::where(...)->value('value') omijają deszyfrowanie.
     */
    public static function getValue(string $key, ?string $default = null): ?string
    {
        $setting = static::query()->where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Zwraca wartość przystosowaną do wyświetlenia w formularzu (np. sk-...a1b2).
     */
    public static function maskedValue(string $key): string
    {
        $value = (string) static::getValue($key);

        if ($value === '') {
            return '';
        }

        if (strlen($value) <= 8) {
            return str_repeat('•', strlen($value));
        }

        return substr($value, 0, 3) . str_repeat('•', 12) . substr($value, -4);
    }

    private function isSecret(): bool
    {
        return in_array($this->attributes['key'] ?? '', self::ENCRYPTED_KEYS, true);
    }

    private function encryptIfNeeded(?string $value): ?string
    {
        if ($value === null || $value === '' || !$this->isSecret()) {
            return $value;
        }

        return Crypt::encryptString($value);
    }

    /**
     * Toleruje wartości zapisane zanim szyfrowanie zostało włączone -
     * dzięki temu nie trzeba migrować bazy przed wdrożeniem.
     */
    private function decryptIfNeeded(?string $value): ?string
    {
        if ($value === null || $value === '' || !$this->isSecret()) {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return $value;
        }
    }
}
