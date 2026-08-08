<?php

namespace App\Actions\Mitras;

use App\Models\MediaAsset;
use App\Models\Mitra;
use App\Models\MitraKycDocument;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmitKycDocument
{
    public function execute(Mitra $mitra, User $actor, MediaAsset $media, string $type, array $data = []): MitraKycDocument
    {
        if ($media->mitra_id !== $mitra->id || ! $actor->mitraMemberships()->where('mitra_id', $mitra->id)->where('status', 'active')->exists()) {
            throw ValidationException::withMessages(['media' => 'Media dan pengguna harus dimiliki Mitra aktif.']);
        }

        return DB::transaction(function () use ($mitra, $actor, $media, $type, $data) {
            $previous = MitraKycDocument::forMitra($mitra)->where('document_type', $type)->lockForUpdate()->latest('version')->first();
            $document = MitraKycDocument::create($data + ['mitra_id' => $mitra->id, 'media_asset_id' => $media->id, 'document_type' => $type, 'version' => ($previous?->version ?? 0) + 1, 'status' => 'submitted', 'submitted_by' => $actor->id]);
            if ($previous) {
                $previous->update(['status' => 'superseded', 'superseded_by_id' => $document->id]);
            }

            return $document;
        });
    }
}
