<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;

// Creates and validates JWT tokens embedded in QR codes for the attendance
// scan workflow.  A teacher generates a QR code containing a signed token
// that students scan to confirm their attendance.
class QRCodeService
{
    protected string $secret;

    public function __construct()
    {
        $this->secret = config('app.key');
    }

    public function generateToken(int $teacherId, int $sessionId, int $expiryMinutes = 15): string
    {
        $payload = [
            'teacher_id' => $teacherId,
            'session_id' => $sessionId,
            'iat' => time(),
            'exp' => time() + ($expiryMinutes * 60),
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return (array) $decoded;
        } catch (\Throwable $e) {
            Log::warning('QR Token validation failed: ' . $e->getMessage());
            return null;
        }
    }

    public function generateQRCode(string $data, int $size = 300): string
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($data)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size($size)
            ->margin(10)
            ->build();

        return $result->getDataUri();
    }

    public function generateAttendanceQR(int $teacherId, int $sessionId, string $scanUrl): array
    {
        $token = $this->generateToken($teacherId, $sessionId);
        $url = $scanUrl . '?token=' . $token;
        $qrDataUri = $this->generateQRCode($url);

        return [
            'token' => $token,
            'url' => $url,
            'qr_image' => $qrDataUri,
            'expires_at' => now()->addMinutes(15),
        ];
    }
}
