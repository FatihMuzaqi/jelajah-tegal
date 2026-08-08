<?php

namespace App\Services\Checkout;

final readonly class CheckoutQuote
{
    public function __construct(public string $domain, public string $mitraId, public string $serviceTypeId, public string $offerId, public string $resourceType, public string $referenceId, public int $quantity, public string $itemName, public ?string $sku, public int $unitPriceMinor, public int $subtotalMinor, public ?string $bookingDate, public ?string $startsAt, public ?string $endsAt, public array $details, public array $holds) {}
}
