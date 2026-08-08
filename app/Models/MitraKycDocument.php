<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMitra;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MitraKycDocument extends Model
{
    use BelongsToMitra,HasFactory,HasUlids;

    protected $fillable = ['mitra_id', 'media_asset_id', 'document_type', 'version', 'document_number_encrypted', 'document_fingerprint', 'status', 'submitted_by', 'reviewed_by', 'reviewed_at', 'expires_on', 'rejection_reason', 'superseded_by_id'];

    protected $hidden = ['document_number_encrypted'];

    protected function casts(): array
    {
        return ['document_number_encrypted' => 'encrypted', 'version' => 'integer', 'reviewed_at' => 'datetime', 'expires_on' => 'date'];
    }

    public function mediaAsset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function supersededBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'superseded_by_id');
    }
}
