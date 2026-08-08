<?php
namespace Database\Factories;
use App\Models\CatalogOffer; use App\Models\Event; use App\Models\EventTicketType; use Illuminate\Database\Eloquent\Factories\Factory;
class EventTicketTypeFactory extends Factory { protected $model=EventTicketType::class; public function definition(): array { return ['event_id'=>Event::factory(),'catalog_offer_id'=>CatalogOffer::factory(),'name'=>'Reguler','quota'=>100,'issued_quantity'=>0]; } }
