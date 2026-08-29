<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class WebpConverter
{
    /**
     * Konversi file gambar yang diunggah ke format WebP teroptimasi.
     *
     * @param UploadedFile|string $file Objek berkas atau path absolut berkas gambar
     * @param int $quality Kualitas kompresi WebP (1-100, default 82)
     * @param int|null $maxWidth Lebar maksimal gambar dalam piksel (default 1920)
     * @return array|null Metadata berkas hasil konversi atau null jika dilewati/gagal
     */
    public function convert(UploadedFile|string $file, int $quality = 82, ?int $maxWidth = 1920): ?array
    {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            return null;
        }

        $realPath = is_string($file) ? $file : $file->getRealPath();
        if (! $realPath || ! file_exists($realPath)) {
            return null;
        }

        $mime = is_string($file) ? mime_content_type($realPath) : $file->getMimeType();

        // Hanya proses gambar format raster standar (abaikan PDF, SVG, dokumen KYC)
        if (! in_array($mime, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/bmp'])) {
            return null;
        }

        try {
            $image = match ($mime) {
                'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($realPath),
                'image/png' => @imagecreatefrompng($realPath),
                'image/webp' => @imagecreatefromwebp($realPath),
                'image/bmp' => @imagecreatefrombmp($realPath),
                default => null,
            };

            if (! $image) {
                return null;
            }

            // Tangani rotasi otomatis jika terdapat data EXIF Orientation (khusus JPG kamera HP)
            if (function_exists('exif_read_data') && in_array($mime, ['image/jpeg', 'image/jpg'])) {
                $exif = @exif_read_data($realPath);
                if (! empty($exif['Orientation'])) {
                    $image = match ($exif['Orientation']) {
                        3 => imagerotate($image, 180, 0),
                        6 => imagerotate($image, -90, 0),
                        8 => imagerotate($image, 90, 0),
                        default => $image,
                    };
                }
            }

            // Pertahankan transparansi (alpha channel) untuk format PNG dan WebP
            imagepalettetotruecolor($image);
            imagealphablending($image, false);
            imagesavealpha($image, true);

            // Resize proporsional jika gambar melebihi lebar maksimum
            $origWidth = imagesx($image);
            $origHeight = imagesy($image);

            if ($maxWidth && $origWidth > $maxWidth && $origWidth > 0) {
                $newWidth = $maxWidth;
                $newHeight = (int) round(($origHeight / $origWidth) * $maxWidth);

                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

                imagedestroy($image);
                $image = $resized;
            }

            // Simpan ke file temporary berformat .webp
            $tempDir = sys_get_temp_dir();
            $tempPath = $tempDir . DIRECTORY_SEPARATOR . 'webp_' . uniqid() . '_' . str()->random(8) . '.webp';

            $saved = imagewebp($image, $tempPath, $quality);
            imagedestroy($image);

            if (! $saved || ! file_exists($tempPath)) {
                return null;
            }

            return [
                'path' => $tempPath,
                'extension' => 'webp',
                'mime_type' => 'image/webp',
                'size_bytes' => filesize($tempPath),
            ];
        } catch (\Throwable $e) {
            Log::warning('WebpConverter failed to process image', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
