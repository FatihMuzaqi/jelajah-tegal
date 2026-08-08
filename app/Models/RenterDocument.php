<?php

namespace App\Models;

use App\Enums\RenterDocumentStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RenterDocument extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['user_id', 'media_asset_id', 'document_type', 'document_number', 'expires_at', 'status', 'reviewed_by', 'reviewed_at', 'rejection_reason'];

    protected $hidden = ['document_number_encrypted'];

    protected function casts(): array
    {
        return ['expires_at' => 'date', 'status' => RenterDocumentStatus::class, 'reviewed_at' => 'datetime'];
    }

    protected function documentNumber(): Attribute
    {
        return Attribute::make(get: fn () => $this->document_number_encrypted ? decrypt($this->document_number_encrypted) : null, set: fn ($value) => ['document_number_encrypted' => $value ? encrypt($value) : null]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'media_asset_id');
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(RentalBooking::class, 'rental_booking_documents')->withPivot('created_at');
    }
}
