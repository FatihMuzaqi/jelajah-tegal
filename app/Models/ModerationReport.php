<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMitra;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModerationReport extends Model
{
    use BelongsToMitra, HasFactory, HasUlids;

    protected $fillable = ['reporter_user_id', 'mitra_id', 'catalog_entity_id', 'review_id', 'reason_code', 'description', 'status', 'assigned_to', 'resolved_at'];

    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }

    public function catalogEntity(): BelongsTo
    {
        return $this->belongsTo(CatalogEntity::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(ModerationAction::class, 'report_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }
}
