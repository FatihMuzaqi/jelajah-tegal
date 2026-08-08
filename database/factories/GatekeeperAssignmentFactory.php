<?php

namespace Database\Factories;

use App\Models\GatekeeperAssignment;
use App\Models\Mitra;
use App\Models\MitraMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GatekeeperAssignmentFactory extends Factory
{
    protected $model = GatekeeperAssignment::class;

    public function definition(): array
    {
        return ['mitra_id' => Mitra::factory(), 'member_id' => MitraMember::factory(), 'scope_type' => 'mitra', 'valid_from' => now(), 'assigned_by' => User::factory()];
    }

    public function forMember(MitraMember $member): static
    {
        return $this->state(['mitra_id' => $member->mitra_id, 'member_id' => $member->id]);
    }
}
