<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mitra extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'owner_user_id',
        'category',
        'legal_name',
        'display_name',
        'slug',
        'registration_number',
        'tax_number_encrypted',
        'status',
        'description',
        'contact_email',
        'contact_phone',
        'region_id',
        'address',
        'logo_media_id',
        'banner_media_id',
        'approved_by',
        'approved_at',
        'suspended_at'
    ];

    protected $hidden = ['tax_number_encrypted'];

    protected function casts(): array
    {
        return ['tax_number_encrypted' => 'encrypted', 'approved_at' => 'datetime', 'suspended_at' => 'datetime'];
    }

    public function isDinas(): bool
    {
        return $this->category === 'dinas';
    }

    public function getCategoryLabelAttribute(): string
    {
        return $this->category === 'dinas' ? 'Dinas (Pemerintah)' : 'Non-Dinas (Swasta / Umum)';
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->whereNotNull('approved_at')
            ->whereNull('suspended_at');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(MitraMember::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(MitraFeature::class);
    }

    public function mediaAssets(): HasMany
    {
        return $this->hasMany(MediaAsset::class);
    }

    public function logoMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'logo_media_id');
    }

    public function bannerMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'banner_media_id');
    }

    public function featureRequests(): HasMany
    {
        return $this->hasMany(MitraFeatureRequest::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(MitraBankAccount::class);
    }

    public function balance(): HasOne
    {
        return $this->hasOne(MitraBalance::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(WithdrawalClaim::class);
    }

    public function kycDocuments(): HasMany
    {
        return $this->hasMany(MitraKycDocument::class);
    }

    public function gatekeeperAssignments(): HasMany
    {
        return $this->hasMany(GatekeeperAssignment::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(ApplicationSetting::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(DatabaseNotification::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(MitraInvitation::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function operatingHours(): HasMany
    {
        return $this->hasMany(MitraOperatingHour::class);
    }

    public function catalogEntities(): HasMany
    {
        return $this->hasMany(CatalogEntity::class);
    }

    public function orders(): HasMany { return $this->hasMany(Order::class); }
    public function vouchers(): HasMany { return $this->hasMany(Voucher::class); }
    public function ledgerJournals(): HasMany { return $this->hasMany(LedgerJournal::class); }
}
