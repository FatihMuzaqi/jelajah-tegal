<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMitra;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MitraOperatingHour extends Model
{
    use BelongsToMitra, HasFactory, HasUlids;

    protected $fillable = ['mitra_id', 'day_of_week', 'opens_at', 'closes_at', 'is_closed'];

    protected function casts(): array
    {
        return ['day_of_week' => 'integer', 'is_closed' => 'boolean'];
    }
}
