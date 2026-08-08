<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;
use App\Policies\Concerns\AuthorizesTenant;

class AuditLogPolicy
{
    use AuthorizesTenant;

    public function view(User $u, AuditLog $m): bool
    {
        return $m->mitra_id ? $this->tenantAllows($u, $m, 'audit.view') : $u->can('audit.view');
    }
}
