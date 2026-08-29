<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMitra;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogEntity extends Model
{
    use BelongsToMitra, HasFactory, HasUlids, SoftDeletes;

    protected $fillable = ['mitra_id', 'service_type_id', 'category_id', 'region_id', 'name', 'slug', 'description', 'address', 'status', 'is_featured', 'has_virtual_tour', 'rating_average', 'rating_count', 'published_at', 'archived_at'];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'has_virtual_tour' => 'boolean', 'rating_average' => 'decimal:2', 'rating_count' => 'integer', 'published_at' => 'datetime', 'archived_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('chatbot_knowledge_base'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('chatbot_knowledge_base'));
        static::restored(fn () => \Illuminate\Support\Facades\Cache::forget('chatbot_knowledge_base'));
        static::forceDeleted(fn () => \Illuminate\Support\Facades\Cache::forget('chatbot_knowledge_base'));
    }

    public function scopePublicTourism(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereHas('serviceType', fn ($type) => $type->where('code', 'tourism'))
            ->whereHas('mitra', fn ($mitra) => $mitra->publiclyVisible())
            ->whereHas('mitra.features', fn ($feature) => $feature->where('status', 'enabled')->whereHas('serviceType', fn ($type) => $type->where('code', 'tourism')));
    }

    public function scopePublicAccommodation(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereHas('serviceType', fn ($type) => $type->where('code', 'accommodation'))
            ->whereHas('mitra', fn ($mitra) => $mitra->publiclyVisible())
            ->whereHas('mitra.features', fn ($feature) => $feature->where('status', 'enabled')->whereHas('serviceType', fn ($type) => $type->where('code', 'accommodation')));
    }

    public function scopePublicDomain(Builder $query, string $serviceCode): Builder
    {
        return $query->where('status', 'published')
            ->whereHas('serviceType', fn ($type) => $type->where('code', $serviceCode))
            ->whereHas('mitra', fn ($mitra) => $mitra->publiclyVisible())
            ->whereHas('mitra.features', fn ($feature) => $feature->where('status', 'enabled')->whereHas('serviceType', fn ($type) => $type->where('code', $serviceCode)));
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(CatalogLocation::class);
    }

    public function tourism(): HasOne
    {
        return $this->hasOne(TourismDestination::class);
    }

    public function accommodation(): HasOne
    {
        return $this->hasOne(Accommodation::class);
    }

    public function culinary(): HasOne
    {
        return $this->hasOne(CulinaryVenue::class);
    }

    public function event(): HasOne
    {
        return $this->hasOne(Event::class);
    }

    public function rentalVehicle(): HasOne
    {
        return $this->hasOne(RentalVehicle::class);
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(MediaAsset::class, 'catalog_media')->withPivot(['role', 'sort_order', 'caption']);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'catalog_facilities')->withPivot('notes');
    }

    public function operatingHours(): HasMany
    {
        return $this->hasMany(CatalogOperatingHour::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(CatalogOffer::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function moderationReports(): HasMany
    {
        return $this->hasMany(ModerationReport::class);
    }
}
