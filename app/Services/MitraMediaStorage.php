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

    public function store(Mitra $mitra, UploadedFile $file, string $purpose, bool $private): MediaAsset
    {
        $mime = $file->getMimeType();
        $extension = self::EXTENSIONS[$mime] ?? null;
        if (! $extension) {
            throw ValidationException::withMessages(['file' => 'Tipe file tidak didukung.']);
        }

        $disk = $private ? 'local' : 'public';
        $directory = $private ? 'kyc/'.$mitra->id : 'mitras/'.$mitra->id.'/'.$purpose;
        $name = str()->ulid().'.'.$extension;
        $objectKey = $file->storeAs($directory, $name, ['disk' => $disk, 'visibility' => $private ? 'private' : 'public']);
        if (! $objectKey) {
            throw ValidationException::withMessages(['file' => 'File gagal disimpan.']);
        }

        try {
            return DB::transaction(fn () => MediaAsset::create([
                'mitra_id' => $mitra->id,
                'disk' => $disk,
                'object_key' => $objectKey,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $mime,
                'size_bytes' => $file->getSize(),
                'checksum_sha256' => hash_file('sha256', $file->getRealPath()),
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
