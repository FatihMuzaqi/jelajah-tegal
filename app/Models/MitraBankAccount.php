<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMitra;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MitraBankAccount extends Model
{
    use BelongsToMitra,HasFactory,HasUlids,SoftDeletes;

    protected $fillable = ['mitra_id', 'bank_code', 'account_name_encrypted', 'account_number_encrypted', 'account_fingerprint', 'status', 'is_primary', 'verified_by', 'verified_at'];

    protected $hidden = ['account_name_encrypted', 'account_number_encrypted'];

    protected function casts(): array
    {
        return ['account_name_encrypted' => 'encrypted', 'account_number_encrypted' => 'encrypted', 'is_primary' => 'boolean', 'verified_at' => 'datetime'];
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getDecryptedAccountNameAttribute(): string
    {
        $val = $this->account_name_encrypted;
        while (is_string($val) && str_starts_with($val, 'eyJpdiI')) {
            try {
                $val = \Illuminate\Support\Facades\Crypt::decryptString($val);
                if (is_string($val) && str_starts_with($val, 's:') && @unserialize($val) !== false) {
                    $val = unserialize($val);
                }
            } catch (\Throwable) {
                break;
            }
        }
        return (string) $val;
    }

    public function getDecryptedAccountNumberAttribute(): string
    {
        $val = $this->account_number_encrypted;
        while (is_string($val) && str_starts_with($val, 'eyJpdiI')) {
            try {
                $val = \Illuminate\Support\Facades\Crypt::decryptString($val);
                if (is_string($val) && str_starts_with($val, 's:') && @unserialize($val) !== false) {
                    $val = unserialize($val);
                }
            } catch (\Throwable) {
                break;
            }
        }
        return (string) $val;
    }

    public function getMaskedNumberAttribute(): string
    {
        $num = preg_replace('/\D+/', '', $this->decrypted_account_number);
        $last4 = substr($num, -4);
        return '•••• ' . ($last4 ?: '****');
    }
}
