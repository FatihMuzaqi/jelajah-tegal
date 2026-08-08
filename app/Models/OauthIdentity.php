<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class OauthIdentity extends Model
{
    use HasUlids;

    public $timestamps = false;

    protected $fillable = ['user_id', 'provider', 'provider_subject', 'provider_email', 'linked_at', 'last_used_at'];

    protected function casts(): array
    {
        return ['linked_at' => 'datetime', 'last_used_at' => 'datetime'];
    }
}
