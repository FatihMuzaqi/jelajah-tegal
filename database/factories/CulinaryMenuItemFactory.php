<?php
namespace Database\Factories;
use App\Models\CulinaryMenuItem; use App\Models\CulinaryVenue; use Illuminate\Database\Eloquent\Factories\Factory;
class CulinaryMenuItemFactory extends Factory { protected $model=CulinaryMenuItem::class; public function definition(): array { return ['culinary_venue_id'=>CulinaryVenue::factory(),'name'=>$this->faker->unique()->words(2,true),'price'=>$this->faker->numberBetween(10000,150000),'status'=>'active']; } }
