<?php

namespace App\Policies;

use App\Models\GatekeeperAssignment;
use App\Models\User;
use App\Policies\Concerns\AuthorizesTenant;

class GatekeeperAssignmentPolicy
{
    use AuthorizesTenant;

    public function view(User $u, GatekeeperAssignment $m): bool
    {
        return $this->tenantAllows($u, $m, 'tickets.validate');
    }
}
