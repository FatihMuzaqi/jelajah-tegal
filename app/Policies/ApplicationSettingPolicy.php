<?php

namespace App\Policies;

use App\Models\ApplicationSetting;
use App\Models\User;
use App\Policies\Concerns\AuthorizesTenant;

class ApplicationSettingPolicy
{
    use AuthorizesTenant;

    public function view(User $u, ApplicationSetting $m): bool
    {
        return $m->mitra_id ? $this->tenantAllows($u, $m, 'settings.manage') : $u->can('settings.manage');
    }

    public function update(User $u, ApplicationSetting $m): bool
    {
        return $this->view($u, $m);
    }
}
