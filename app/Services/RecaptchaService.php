<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    private ?string $secretKey;
    private ?string $siteKey;
    private string $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct()
    {
        $this->secretKey = config('services.recaptcha.secret_key') ?? '';
        $this->siteKey = config('services.recaptcha.site_key') ?? '';
    }

    /**
     * Verify reCAPTCHA token
     *
     * @param string $token
     * @param float $minScore Minimum score (0.0 to 1.0). Default 0.5
     * @param string|null $expectedAction Expected action for v3 (e.g. "login")
     * @return bool
     */
    public function verify(string $token, float $minScore = 0.5, ?string $expectedAction = null): bool
    {
        // If reCAPTCHA is not configured, allow only in local/testing.
        if (empty($this->secretKey)) {
            if (app()->environment(['local', 'testing'])) {
                return true;
            }

            Log::error('reCAPTCHA secret key is missing in a non-local environment');
            return false;
        }

        // Token is required when secret key is configured.
        if (empty($token)) {
            Log::warning('reCAPTCHA token is missing');
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

            // Verify expected action for v3 (if present in the response)
            if ($expectedAction !== null && isset($data['action']) && $data['action'] !== $expectedAction) {
                Log::warning('reCAPTCHA action mismatch', [
                    'expected' => $expectedAction,
                    'actual' => $data['action'],
                ]);
                return false;
            }

            // Optional: verify hostname to reduce token abuse across domains
            $expectedHostname = config('services.recaptcha.expected_hostname');
            if (!empty($expectedHostname) && isset($data['hostname']) && $data['hostname'] !== $expectedHostname) {
                Log::warning('reCAPTCHA hostname mismatch', [
                    'expected' => $expectedHostname,
                    'actual' => $data['hostname'],
                ]);
                return false;
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
        return $this->siteKey ?? '';
    }
}

