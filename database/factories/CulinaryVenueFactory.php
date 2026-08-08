<?php
namespace Database\Factories;
use App\Models\CatalogEntity;
use App\Models\CulinaryVenue;
use Illuminate\Database\Eloquent\Factories\Factory;
class CulinaryVenueFactory extends Factory { protected $model=CulinaryVenue::class; public function definition(): array { return ['catalog_entity_id'=>CatalogEntity::factory(),'venue_type'=>'restaurant','accepts_reservations'=>true,'phone'=>$this->faker->phoneNumber()]; } }
