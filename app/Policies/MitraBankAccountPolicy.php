<?php

namespace App\Policies;

use App\Models\MitraBankAccount;
use App\Models\User;
use App\Policies\Concerns\AuthorizesTenant;

class MitraBankAccountPolicy
{
    use AuthorizesTenant;

    public function view(User $u, MitraBankAccount $m): bool
    {
        return $this->tenantAllows($u, $m, 'bank-accounts.manage');
    }

    public function update(User $u, MitraBankAccount $m): bool
    {
        return $this->view($u, $m);
    }
}
