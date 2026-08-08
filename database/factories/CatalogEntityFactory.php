<?php
namespace Database\Factories;
use App\Models\CatalogEntity; use App\Models\Mitra; use App\Models\ServiceType; use Illuminate\Database\Eloquent\Factories\Factory;
class CatalogEntityFactory extends Factory { protected $model=CatalogEntity::class; public function definition(): array { $name=$this->faker->unique()->company(); return ['mitra_id'=>Mitra::factory(),'service_type_id'=>ServiceType::factory(),'name'=>$name,'slug'=>str()->slug($name).'-'.str()->lower(str()->random(6)),'description'=>$this->faker->sentence(),'status'=>'draft']; } }
