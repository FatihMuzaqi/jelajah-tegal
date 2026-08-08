<?php
namespace Database\Factories;
use App\Models\CatalogEntity; use App\Models\CatalogOffer; use Illuminate\Database\Eloquent\Factories\Factory;
class CatalogOfferFactory extends Factory { protected $model=CatalogOffer::class; public function definition(): array { return ['mitra_id'=>fn(array $a)=>CatalogEntity::find($a['catalog_entity_id'])->mitra_id,'catalog_entity_id'=>CatalogEntity::factory(),'offer_type'=>'generic','sku'=>str()->upper(str()->random(10)),'name'=>'Offer '.$this->faker->word(),'price'=>100000,'status'=>'active']; } }
