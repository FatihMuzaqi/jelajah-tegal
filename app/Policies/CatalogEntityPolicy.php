<?php

namespace App\Policies;

use App\Models\CatalogEntity;
use App\Models\User;

class CatalogEntityPolicy
{
    public function view(User $user, CatalogEntity $entity): bool
    {
        if ($entity->status === 'published') {
            return true;
        }

        return $entity->mitra_id === session('active_mitra_id') && $user->can('access.mitra');
    }

    public function update(User $user, CatalogEntity $entity): bool
    {
        return $entity->mitra_id === session('active_mitra_id') && $user->can('tourism.manage');
    }

    public function submit(User $user, CatalogEntity $entity): bool
    {
        return $entity->mitra_id === session('active_mitra_id') && $user->can('tourism.submit');
    }

    public function moderate(User $user, CatalogEntity $entity): bool
    {
        return $user->can('tourism.moderate');
    }
}
