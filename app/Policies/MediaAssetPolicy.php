<?php

namespace App\Policies;

use App\Models\MediaAsset;
use App\Models\User;
use App\Policies\Concerns\AuthorizesTenant;

class MediaAssetPolicy
{
    use AuthorizesTenant;

    public function view(User $u, MediaAsset $m): bool
    {
        if ($m->owner_user_id === $u->id) {
            return true;
        }if ($m->mitra_id) {
            return $this->tenantAllows($u, $m, 'media.manage');
        }

        return $u->can('settings.manage');
    }

    public function update(User $u, MediaAsset $m): bool
    {
        return $this->view($u, $m);
    }
}
