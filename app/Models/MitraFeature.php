<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMitra;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MitraFeature extends Model
{
    use BelongsToMitra,HasFactory,HasUlids;

    protected $fillable = ['mitra_id', 'service_type_id', 'status', 'enabled_at', 'disabled_at', 'enabled_by'];

    protected function casts(): array
    {
        return ['enabled_at' => 'datetime', 'disabled_at' => 'datetime'];
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function enabler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enabled_by');
    }
}
