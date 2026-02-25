<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $gatewayUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->gatewayUrl = config('services.wa_gateway.url');
        $this->apiKey = config('services.wa_gateway.key');
    }

    /**
     * Send image via WhatsApp
     *
     * @param string $to Phone number
     * @param string $caption Message caption
     * @param string $filename Original filename
     * @param string $mime MIME type
     * @param string $base64 Base64 encoded image
     * @param string|null $sessionId Optional session ID
     * @return array Response from gateway
     * @throws \Exception
     */
    public function sendImage(string $to, string $caption, string $filename, string $mime, string $base64, ?string $sessionId = null): array
    {
        $url = $sessionId ? "{$this->gatewayUrl}/session/{$sessionId}/send-image" : "{$this->gatewayUrl}/send-image";

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
        ])->post($url, [
            'to' => $to,
            'caption' => $caption,
            'filename' => $filename,
            'mime' => $mime,
            'base64' => $base64,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('WhatsApp send failed', [
            'to' => $to,
            'session_id' => $sessionId,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        // Fallback or just throw error?
        // Since we are moving to multi-session, fallback to text might be complex if session specific endpoint is needed.
        // But let's try text fallback if image failed.

        $fallbackMessage = $caption . "\n\n(Note: Gagal mengirim gambar tiket, silakan download tiket melalui dashboard)";
        $textUrl = $sessionId ? "{$this->gatewayUrl}/session/{$sessionId}/send-text" : "{$this->gatewayUrl}/send-text";

        try {
            $textResponse = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->post($textUrl, [
                'to' => $to,
                'message' => $fallbackMessage,
            ]);

            if ($textResponse->successful()) {
                return $textResponse->json();
            }
        } catch (\Exception $e) {
            // ignore fallback error
        }

        throw new \Exception("WhatsApp send failed: " . $response->body());
    }

    /**
     * Check gateway health
     */
    public function health(): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
        ])->get("{$this->gatewayUrl}/health");

        return $response->json();
    }

    /**
     * Start a new session in gateway
     */
    public function startSession(string $sessionId): array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->post("{$this->gatewayUrl}/session/start", [
                'sessionId' => $sessionId
            ]);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            return ['error' => 'Gateway Error: ' . $response->status() . ' - ' . $response->body()];
        } catch (\Exception $e) {
            return ['error' => 'Connection Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get WA connection status for a session
     */
    public function getStatus(?string $sessionId = null): array
    {
        if (!$sessionId) {
            return ['connected' => false, 'qr' => null];
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->timeout(3)->get("{$this->gatewayUrl}/session/{$sessionId}/status");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // ignore error
        }

        return ['connected' => false, 'qr' => null];
    }

    /**
     * Logout from WhatsApp session
     */
    public function logout(?string $sessionId = null): bool
    {
        if (!$sessionId) return false;

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->post("{$this->gatewayUrl}/session/{$sessionId}/logout");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete session
     */
    public function deleteSession(string $sessionId): bool
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->delete("{$this->gatewayUrl}/session/{$sessionId}");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
