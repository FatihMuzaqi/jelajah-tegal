<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory,HasRoles,HasUlids,Notifiable,SoftDeletes;

    protected $fillable = ['name', 'email', 'phone', 'status', 'preferred_locale', 'email_verified_at'];

    protected $hidden = ['remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'last_login_at' => 'datetime'];
    }

    public function credential(): HasOne
    {
        return $this->hasOne(UserCredential::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function mitraMemberships(): HasMany
    {
        return $this->hasMany(MitraMember::class);
    }

    public function mfaRecoveryCodes(): HasMany
    {
        return $this->hasMany(MfaRecoveryCode::class);
    }

    public function ownedMitras(): HasMany
    {
        return $this->hasMany(Mitra::class, 'owner_user_id');
    }

    public function mediaAssets(): HasMany
    {
        return $this->hasMany(MediaAsset::class, 'owner_user_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(DatabaseNotification::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function renterDocuments(): HasMany
    {
        return $this->hasMany(RenterDocument::class);
    }

    public function culinaryReservations(): HasMany
    {
        return $this->hasMany(CulinaryReservation::class);
    }

    public function rentalBookings(): HasMany
    {
        return $this->hasMany(RentalBooking::class);
    }

    public function eventTickets(): HasMany
    {
        return $this->hasMany(EventTicket::class);
    }

    public function orders(): HasMany { return $this->hasMany(Order::class); }
    public function voucherClaims(): HasMany { return $this->hasMany(VoucherClaim::class); }
}
