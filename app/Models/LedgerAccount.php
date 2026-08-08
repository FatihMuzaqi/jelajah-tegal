<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LedgerAccount extends Model
{
    use HasUlids;

    protected $fillable = ['mitra_id', 'user_id', 'system_code', 'account_type', 'currency', 'status'];

    public function lines(): HasMany
    {
        return $this->hasMany(LedgerLine::class);
    }
}
