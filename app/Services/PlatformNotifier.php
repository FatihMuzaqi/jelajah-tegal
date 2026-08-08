<?php

namespace App\Services;

use App\Models\DatabaseNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlatformNotifier
{
    public function administrators(string $type, string $mitraId, array $data): void
    {
        $userIds = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->whereNull('roles.mitra_id')
            ->whereIn('roles.name', ['admin', 'super-admin'])
            ->where('model_has_roles.model_type', User::class)
            ->distinct()
            ->pluck('model_has_roles.model_id');

        foreach ($userIds as $userId) {
            DatabaseNotification::create(['user_id' => $userId, 'mitra_id' => $mitraId, 'type' => $type, 'data' => $data]);
        }
    }
}
