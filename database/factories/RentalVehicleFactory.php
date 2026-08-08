<?php
namespace Database\Factories;
use App\Models\CatalogEntity; use App\Models\RentalVehicle; use Illuminate\Database\Eloquent\Factories\Factory;
class RentalVehicleFactory extends Factory { protected $model=RentalVehicle::class; public function definition(): array { return ['catalog_entity_id'=>CatalogEntity::factory(),'vehicle_type'=>'car','brand'=>'Toyota','model'=>'Avanza','year'=>2024,'plate_number'=>'G 1234 AB','seats'=>7,'self_drive_available'=>true,'driver_available'=>true,'deposit_amount'=>500000,'status'=>'active']; } }
