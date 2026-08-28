<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = ['user_id', 'catalog_entity_id', 'order_item_id', 'rating', 'title', 'body', 'status', 'moderated_by', 'moderated_at'];

    protected function casts(): array
    {
        return ['rating' => 'integer', 'moderated_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function catalogEntity(): BelongsTo
    {
        return $this->belongsTo(CatalogEntity::class);
    }

    public function reply(): HasOne
    {
        return $this->hasOne(ReviewReply::class)->latestOfMany();
    }

    public function replies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReviewReply::class)->where('status', 'published')->oldest();
    }

    public function syncCatalogRating(): void
    {
        if ($entity = $this->catalogEntity) {
            $stats = $entity->reviews()
                ->where('status', 'published')
                ->selectRaw('COUNT(*) as total, COALESCE(AVG(rating), 0) as average')
                ->first();

            $entity->update([
                'rating_count' => (int) ($stats->total ?? 0),
                'rating_average' => round((float) ($stats->average ?? 0), 2),
            ]);
        }
    }
}
