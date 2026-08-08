<?php
namespace App\Policies;
use App\Models\RentalVehicle; use App\Models\User;
class RentalVehiclePolicy { public function update(User $user,RentalVehicle $vehicle): bool { return $user->can('rental.manage')&&$vehicle->catalogEntity->mitra_id===session('active_mitra_id'); } public function manageBookings(User $user,RentalVehicle $vehicle): bool { return $user->can('rental.bookings.manage')&&$vehicle->catalogEntity->mitra_id===session('active_mitra_id'); } }
