<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCredential extends Model
{
    use HasUlids;

    protected $fillable = ['user_id', 'password_hash', 'mfa_secret_encrypted', 'mfa_confirmed_at', 'failed_login_count', 'locked_until'];

    protected $hidden = ['password_hash', 'mfa_secret_encrypted'];

    protected function casts(): array
    {
        return ['mfa_secret_encrypted' => 'encrypted', 'mfa_confirmed_at' => 'datetime', 'locked_until' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
