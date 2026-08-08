<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMitra;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaAsset extends Model
{
    use BelongsToMitra,HasFactory,HasUlids,SoftDeletes;

    protected $fillable = ['mitra_id', 'owner_user_id', 'is_platform_owned', 'disk', 'object_key', 'original_name', 'mime_type', 'size_bytes', 'checksum_sha256', 'visibility', 'purpose', 'status', 'metadata', 'uploaded_at'];

    protected function casts(): array
    {
        return ['is_platform_owned' => 'boolean', 'size_bytes' => 'integer', 'metadata' => 'array', 'uploaded_at' => 'datetime'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function catalogEntities(): BelongsToMany
    {
        return $this->belongsToMany(CatalogEntity::class, 'catalog_media')->withPivot(['role', 'sort_order', 'caption']);
    }

    public function accommodationRooms(): BelongsToMany
    {
        return $this->belongsToMany(AccommodationRoom::class, 'accommodation_room_media')->withPivot(['role', 'sort_order', 'caption']);
    }
}
