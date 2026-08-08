<?php

namespace App\Services\Vouchers;

use App\Models\Voucher;
use App\Models\VoucherClaim;

final readonly class VoucherResult
{
    public function __construct(public Voucher $voucher, public VoucherClaim $claim, public int $discountMinor, public array $snapshot) {}
}
