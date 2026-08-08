<?php

namespace App\Services\Checkout;

use App\Models\CatalogEntity;

class CheckoutEligibility
{
    public function assert(CatalogEntity $entity, string $domain): void
    {
        abort_unless($entity->status === 'published' && $entity->serviceType()->where('code', $domain)->exists() && $entity->mitra()->publiclyVisible()->exists() && $entity->mitra->features()->where('status', 'enabled')->whereHas('serviceType', fn ($q) => $q->where('code', $domain))->exists(), 404);
    }
}
