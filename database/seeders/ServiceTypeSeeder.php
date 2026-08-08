<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['tourism', 'Tourism', 1], ['accommodation', 'Accommodation', 2], ['culinary', 'Culinary', 3], ['event', 'Event', 4], ['rental', 'Rental', 5]] as [$code,$name,$order]) {
            ServiceType::updateOrCreate(['code' => $code], ['name' => $name, 'is_transactional' => true, 'sort_order' => $order]);
        }
    }
}
