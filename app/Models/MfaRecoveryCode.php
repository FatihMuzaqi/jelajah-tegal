<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MfaRecoveryCode extends Model
{
    use HasFactory, HasUlids;

    public $timestamps = false;

    protected $fillable = ['user_id', 'code_hash', 'used_at', 'created_at'];

    protected function casts(): array
    {
        return ['used_at' => 'datetime', 'created_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
