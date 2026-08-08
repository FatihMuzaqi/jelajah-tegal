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
        return ($u->can('access.admin') && $u->can('kyc.review')) || $this->tenantAllows($u, $m, 'kyc.submit');
    }
}
