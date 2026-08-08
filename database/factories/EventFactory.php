<?php
namespace Database\Factories;
use App\Models\CatalogEntity; use App\Models\Event; use Illuminate\Database\Eloquent\Factories\Factory;
class EventFactory extends Factory { protected $model=Event::class; public function definition(): array { return ['catalog_entity_id'=>CatalogEntity::factory(),'event_type'=>'festival','starts_at'=>now()->addWeek(),'ends_at'=>now()->addWeek()->addHours(4)]; } }
