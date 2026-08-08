<?php

namespace App\Policies\Concerns;

use App\Models\User;
use App\Support\MitraContext;

trait AuthorizesTenant
{
    private function tenantAllows(User $user, object $resource, string $permission): bool
    {
        $id = app(MitraContext::class)->id();

        return $id === $resource->mitra_id && $user->mitraMemberships()->where('mitra_id', $id)->where('status', 'active')->exists() && $user->can($permission);
    }
}
