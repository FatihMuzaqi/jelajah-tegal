<?php

namespace App\Services;

use App\Models\MediaAsset;
use App\Models\Mitra;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MitraMediaStorage
{
    private const EXTENSIONS = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    public function __construct(protected WebpConverter $webpConverter) {}

    public function store(Mitra $mitra, UploadedFile $file, string $purpose, bool $private): MediaAsset
    {
        $mime = $file->getMimeType();
        $extension = self::EXTENSIONS[$mime] ?? null;
        if (! $extension) {
            throw ValidationException::withMessages(['file' => 'Tipe file tidak didukung.']);
        }

        $disk = $private ? 'local' : 'public';
        $directory = $private ? 'kyc/'.$mitra->id : 'mitras/'.$mitra->id.'/'.$purpose;

        // Auto-convert to WebP for public visual media (cover, gallery, logo, banner, etc.)
        $converted = null;
        if (! $private && in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
            $converted = $this->webpConverter->convert($file, quality: 82, maxWidth: 1920);
        }

        if ($converted) {
            $extension = 'webp';
            $mime = 'image/webp';
            $name = str()->ulid().'.webp';
            $objectKey = $directory.'/'.$name;

            $stored = Storage::disk($disk)->put($objectKey, file_get_contents($converted['path']), 'public');
            $sizeBytes = $converted['size_bytes'];
            $checksum = hash_file('sha256', $converted['path']);

            @unlink($converted['path']);

            if (! $stored) {
                throw ValidationException::withMessages(['file' => 'File WebP gagal disimpan.']);
            }
        } else {
            $name = str()->ulid().'.'.$extension;
            $objectKey = $file->storeAs($directory, $name, ['disk' => $disk, 'visibility' => $private ? 'private' : 'public']);
            if (! $objectKey) {
                throw ValidationException::withMessages(['file' => 'File gagal disimpan.']);
            }
            $sizeBytes = $file->getSize();
            $checksum = hash_file('sha256', $file->getRealPath());
        }

        try {
            return DB::transaction(fn () => MediaAsset::create([
                'mitra_id' => $mitra->id,
                'disk' => $disk,
                'object_key' => $objectKey,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $mime,
                'size_bytes' => $sizeBytes,
                'checksum_sha256' => $checksum,
                'visibility' => $private ? 'private' : 'public',
                'purpose' => $purpose,
                'status' => 'ready',
                'uploaded_at' => now(),
            ]));
        } catch (\Throwable $exception) {
            Storage::disk($disk)->delete($objectKey);

            throw $exception;
        }
    }

    public function discard(MediaAsset $media): void
    {
        Storage::disk($media->disk)->delete($media->object_key);
        $media->forceDelete();
    }
}
