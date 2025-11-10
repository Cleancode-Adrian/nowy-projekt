<?php

namespace App\Services;

class ProfanityFilter
{
    /**
     * Lista słów wulgarnych/obraźliwych (polskie)
     */
    private static $badWords = [
        // Wulgaryzmy
        'kurwa', 'kurwy', 'kurwą', 'kurwie', 'kurwo',
        'chuj', 'chuja', 'chujek', 'chuju', 'chujem',
        'pierdol', 'pierdolić', 'pierdolisz', 'pierdolony', 'pierdolnięty',
        'jebać', 'jebany', 'jebane', 'jeba', 'jebie',
        'dupa', 'dupy', 'dupie', 'dupą', 'dupek',
        'gówno', 'gówna', 'gównie', 'gównem',
        'pizda', 'pizdy', 'pizdzie', 'pizdą',
        'suka', 'suki', 'suko', 'sukinsyn',
        'skurwysyn', 'skurwiel', 'skurwysyny',
        'pedał', 'pedały', 'pedale', 'pedzio',
        'dziwka', 'dziwki', 'dziwko', 'dziwką',
        
        // Obelgi
        'debil', 'debilu', 'debile', 'debilizm',
        'idiota', 'idioto', 'idiocie',
        'kretyn', 'kretynie', 'kretyna',
        'głupek', 'głupi', 'głupie', 'głupia',
        'matołek', 'matole', 'matołu',
        'gnój', 'gnoja', 'gnoju',
        'śmieć', 'śmiecie', 'śmieciu',
        
        // Angielskie (popularne)
        'fuck', 'fucking', 'shit', 'bitch', 'asshole',
        'dick', 'pussy', 'cunt', 'bastard',
    ];

    /**
     * Sprawdza czy tekst zawiera wulgaryzmy
     */
    public static function containsProfanity(string $text): bool
    {
        $text = mb_strtolower($text);
        
        foreach (self::$badWords as $badWord) {
            // Sprawdzamy czy słowo występuje jako całe słowo (nie część innego słowa)
            if (preg_match('/\b' . preg_quote($badWord, '/') . '\b/u', $text)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Zwraca listę znalezionych wulgaryzmów
     */
    public static function findProfanity(string $text): array
    {
        $text = mb_strtolower($text);
        $found = [];
        
        foreach (self::$badWords as $badWord) {
            if (preg_match('/\b' . preg_quote($badWord, '/') . '\b/u', $text)) {
                $found[] = $badWord;
            }
        }
        
        return $found;
    }

    /**
     * Cenzuruje wulgaryzmy (zamienia na ***)
     */
    public static function censor(string $text): string
    {
        foreach (self::$badWords as $badWord) {
            $text = preg_replace(
                '/\b' . preg_quote($badWord, '/') . '\b/ui',
                str_repeat('*', mb_strlen($badWord)),
                $text
            );
        }
        
        return $text;
    }
}

