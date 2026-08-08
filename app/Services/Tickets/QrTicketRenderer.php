<?php

namespace App\Services\Tickets;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;

class QrTicketRenderer
{
    public function svg(string $token): string
    {
        $qr = new QrCode(data: $token, encoding: new Encoding('UTF-8'), errorCorrectionLevel: ErrorCorrectionLevel::High, size: 320, margin: 12, roundBlockSizeMode: RoundBlockSizeMode::Margin);
        return (new SvgWriter)->write($qr)->getString();
    }
}
