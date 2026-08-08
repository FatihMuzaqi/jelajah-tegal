<?php
namespace Database\Factories;
use App\Models\CatalogOffer; use App\Models\RentalRate; use App\Models\RentalVehicle; use Illuminate\Database\Eloquent\Factories\Factory;
class RentalRateFactory extends Factory { protected $model=RentalRate::class; public function definition(): array { return ['rental_vehicle_id'=>RentalVehicle::factory(),'catalog_offer_id'=>CatalogOffer::factory(),'drive_mode'=>'self_drive','duration_unit'=>'day','duration_value'=>1]; } }
