<?php

namespace App\Services;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Log;
use Throwable;

class QrService
{
    /**
     * Generate data URI for given code.
     *
     * @param  string  $code  The text / ticket code to encode
     * @param  string  $format  'png' or 'svg'
     * @param  int  $size  Image dimension in pixels
     * @return string|null Returns data URI string (data:image/png;base64,...) or null on error
     */
    public function dataUri(string $code, string $format = 'png', int $size = 200): ?string
    {
        $code = trim($code);

        if ($code === '') {
            Log::warning('QrService::dataUri called with an empty code.');

            return null;
        }

        try {
            $qrCode = QrCode::create($code)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(ErrorCorrectionLevel::Medium)
                ->setSize($size)
                ->setMargin(6)
                ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
                ->setForegroundColor(new Color(77, 5, 112)) // Harfest Dark Purple #4D0570
                ->setBackgroundColor(new Color(255, 255, 255));

            $writer = match (strtolower($format)) {
                'svg' => new SvgWriter(),
                default => new PngWriter(),
            };

            $result = $writer->write($qrCode);

            return $result->getDataUri();
        } catch (Throwable $e) {
            Log::error("QrService failed to generate QR code for code [{$code}]: " . $e->getMessage(), [
                'code' => $code,
                'format' => $format,
                'exception' => $e,
            ]);

            return null;
        }
    }
}

