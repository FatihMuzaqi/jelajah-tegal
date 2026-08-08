<?php

namespace App\Services\Checkout;

use App\Models\ApplicationSetting;
use App\Models\Mitra;
use App\Support\Money;

class CommercialTerms
{
    public function commissionBasisPoints(Mitra $mitra): int
    {
        $value = ApplicationSetting::query()->forMitra($mitra)->where('key_name', 'finance.commission_rate')->where('is_secret', false)->value('value_json');
        if (is_array($value)) {
            if (isset($value['basis_points'])) {
                return max(0, min(10000, (int) $value['basis_points']));
            }if (isset($value['rate_percent'])) {
                // A percentage with two decimals maps exactly to basis points.
                return max(0, min(10000, Money::toMinor((string) $value['rate_percent'])));
            }
        }

return 0;
    }

    public function adminFeeMinor(): int
    {
        $value = ApplicationSetting::whereNull('mitra_id')->where('key_name', 'finance.admin_fee')->where('is_secret', false)->value('value_json');
        $amount = is_array($value) ? ($value['amount'] ?? '0') : '0';

        return Money::toMinor((string) $amount);
    }
}
