<?php
namespace Database\Factories;
use App\Models\CulinaryMenuCategory; use App\Models\CulinaryVenue; use Illuminate\Database\Eloquent\Factories\Factory;
class CulinaryMenuCategoryFactory extends Factory { protected $model=CulinaryMenuCategory::class; public function definition(): array { return ['culinary_venue_id'=>CulinaryVenue::factory(),'name'=>$this->faker->unique()->word(),'sort_order'=>0]; } }
