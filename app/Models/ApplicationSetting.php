<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMitra;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationSetting extends Model
{
    use BelongsToMitra,HasFactory,HasUlids;

    protected $fillable = ['mitra_id', 'key_name', 'value_encrypted', 'value_json', 'value_type', 'is_secret', 'updated_by'];

    protected $hidden = ['value_encrypted'];

    protected function casts(): array
    {
        return ['value_encrypted' => 'encrypted', 'value_json' => 'array', 'is_secret' => 'boolean'];
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
