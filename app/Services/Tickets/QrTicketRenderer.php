<?php

namespace App\Services\Tickets;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;

class QrTicketRenderer
{
    public function svg(string $token, bool $isUsed = false, ?string $watermarkText = 'SUDAH DIGUNAKAN'): string
    {
        $qr = new QrCode(data: $token, encoding: new Encoding('UTF-8'), errorCorrectionLevel: ErrorCorrectionLevel::High, size: 320, margin: 12, roundBlockSizeMode: RoundBlockSizeMode::Margin);
        $svg = (new SvgWriter)->write($qr)->getString();

        if ($isUsed) {
            $stampText = htmlspecialchars($watermarkText ?? 'SUDAH DIGUNAKAN', ENT_QUOTES, 'UTF-8');
            $watermark = <<<SVG

        <!-- Watermark Overlay SUDAH DIGUNAKAN -->
        <g id="qr-watermark" transform="translate(160, 160) rotate(-28)">
            <rect x="-145" y="-28" width="290" height="56" rx="8" fill="#dc2626" fill-opacity="0.92" stroke="#ffffff" stroke-width="3" stroke-dasharray="6,4" />
            <text x="0" y="8" font-family="'Segoe UI', Helvetica, Arial, sans-serif" font-size="17" font-weight="900" fill="#ffffff" text-anchor="middle" letter-spacing="1.5">{$stampText}</text>
        </g>
SVG;
            $svg = str_replace('</svg>', $watermark . '</svg>', $svg);
        }

        return $svg;
    }
}
