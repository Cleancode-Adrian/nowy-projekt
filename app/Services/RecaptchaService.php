<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    private string $secretKey;
    private string $siteKey;
    private string $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct()
    {
        $this->secretKey = config('services.recaptcha.secret_key');
        $this->siteKey = config('services.recaptcha.site_key');
    }

    /**
     * Verify reCAPTCHA token
     * 
     * @param string $token
     * @param float $minScore Minimum score (0.0 to 1.0). Default 0.5
     * @return bool
     */
    public function verify(string $token, float $minScore = 0.5): bool
    {
        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->post($this->verifyUrl, [
                'secret' => $this->secretKey,
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

            $data = $response->json();

            if (!$response->successful() || !isset($data['success'])) {
                Log::warning('reCAPTCHA verification failed: Invalid response', [
                    'response' => $data,
                ]);
                return false;
            }

            if (!$data['success']) {
                Log::warning('reCAPTCHA verification failed', [
                    'error_codes' => $data['error-codes'] ?? [],
                ]);
                return false;
            }

            // Check score for v3 (score is only present in v3)
            if (isset($data['score'])) {
                if ($data['score'] < $minScore) {
                    Log::warning('reCAPTCHA score too low', [
                        'score' => $data['score'],
                        'min_score' => $minScore,
                    ]);
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get site key for frontend
     */
    public function getSiteKey(): string
    {
        return $this->siteKey;
    }
}

