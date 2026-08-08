<?php

namespace App\Policies;

use App\Models\MitraFeatureRequest;
use App\Models\User;
use App\Policies\Concerns\AuthorizesTenant;

class MitraFeatureRequestPolicy
{
    use AuthorizesTenant;

    public function view(User $u, MitraFeatureRequest $m): bool
    {
        return $this->tenantAllows($u, $m, 'mitras.view');
    }

    public function update(User $u, MitraFeatureRequest $m): bool
    {
        return $this->tenantAllows($u, $m, 'mitras.update');
    }
}
