<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['tourism', 'Wisata (Destinasi Wisata)', 1],
            ['accommodation', 'Penginapan (Hotel & Akomodasi)', 2],
            ['culinary', 'Kuliner (Restoran & Kafe)', 3],
            ['event', 'Event (Acara & Festival)', 4],
            ['rental', 'Rental (Sewa Kendaraan)', 5],
        ] as [$code, $name, $order]) {
            ServiceType::updateOrCreate(['code' => $code], ['name' => $name, 'is_transactional' => true, 'sort_order' => $order]);
        }
    }
}
