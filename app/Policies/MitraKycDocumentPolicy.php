<?php

namespace App\Policies;

use App\Models\MitraKycDocument;
use App\Models\User;
use App\Policies\Concerns\AuthorizesTenant;

class MitraKycDocumentPolicy
{
    use AuthorizesTenant;

    public function view(User $u, MitraKycDocument $m): bool
    {
        return $u->can('kyc.review') 
            || $u->can('access.admin') 
            || $u->can('access.dinas') 
            || $this->tenantAllows($u, $m, 'kyc.submit');
    }
}
