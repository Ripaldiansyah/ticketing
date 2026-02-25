<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QrService
{
    /**
     * Generate QR code for a ticket
     *
     * @param string $code Ticket code
     * @param string $token Ticket token (content of QR)
     * @return string Path to QR image
     */
    public function generate(string $code, string $token): string
    {
        $filename = "tickets/{$code}.png";

        // Generate QR code with Ticket Code (User Request)
        $qrImage = QrCode::format('png')
            ->size(600)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($code);

        // Store to public disk
        Storage::disk('public')->put($filename, $qrImage);

        return $filename;
    }

    /**
     * Get full path to QR image
     */
    public function getPath(string $qrPath): string
    {
        return Storage::disk('public')->path($qrPath);
    }

    /**
     * Get URL to QR image
     */
    public function getUrl(string $qrPath): string
    {
        return Storage::disk('public')->url($qrPath);
    }

    /**
     * Get base64 encoded QR image
     */
    public function getBase64(string $qrPath): string
    {
        $content = Storage::disk('public')->get($qrPath);
        return base64_encode($content);
    }
}
