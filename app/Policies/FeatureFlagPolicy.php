<?php

namespace App\Policies;

use App\Models\FeatureFlag;
use App\Models\User;

class FeatureFlagPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->can('feature-flags.manage');
    }

    public function update(User $u, FeatureFlag $flag): bool
    {
        return $u->can('feature-flags.manage');
    }
}
