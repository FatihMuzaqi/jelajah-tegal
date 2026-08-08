<?php

namespace App\Support;

use App\Models\FeatureFlag;
use Illuminate\Support\Carbon;

class FeatureFlags
{
    public function enabled(string $key, ?Carbon $at = null): bool
    {
        $flag = FeatureFlag::query()->where('key_name', $key)->first();

        if (! $flag || $flag->status !== 'enabled' || (float) $flag->rollout_percentage < 100) {
            return false;
        }

        $at ??= now();

        return (! $flag->starts_at || $flag->starts_at->lte($at))
            && (! $flag->ends_at || $flag->ends_at->gt($at));
    }
}
