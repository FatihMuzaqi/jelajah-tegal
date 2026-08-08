<?php

namespace App\Policies;

use App\Models\DatabaseNotification;
use App\Models\User;

class DatabaseNotificationPolicy
{
    public function view(User $u, DatabaseNotification $n): bool
    {
        return $n->user_id === $u->id && (! $n->mitra_id || $u->mitraMemberships()->where('mitra_id', $n->mitra_id)->where('status', 'active')->exists());
    }
}
