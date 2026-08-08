<?php

namespace App\Services;

use App\Models\FeatureFlag;
use Illuminate\Support\Facades\Cache;

class FeatureManager
{
    public function enabled(string $key): bool
    {
        return Cache::remember('feature:'.$key, 60, fn () => FeatureFlag::where('key_name', $key)->where('status', 'enabled')->where('rollout_percentage', '>', 0)->exists());
    }
}
