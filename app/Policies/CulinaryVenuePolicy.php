<?php
namespace App\Policies;
use App\Models\CulinaryVenue; use App\Models\User;
class CulinaryVenuePolicy { public function update(User $user,CulinaryVenue $venue): bool { return $user->can('culinary.manage')&&$venue->catalogEntity->mitra_id===session('active_mitra_id'); } public function manageReservations(User $user,CulinaryVenue $venue): bool { return $user->can('culinary.reservations.manage')&&$venue->catalogEntity->mitra_id===session('active_mitra_id'); } }
