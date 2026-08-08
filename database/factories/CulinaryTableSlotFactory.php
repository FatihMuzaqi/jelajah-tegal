<?php
namespace Database\Factories;
use App\Models\CulinaryTableSlot; use App\Models\CulinaryVenue; use Illuminate\Database\Eloquent\Factories\Factory;
class CulinaryTableSlotFactory extends Factory { protected $model=CulinaryTableSlot::class; public function definition(): array { return ['culinary_venue_id'=>CulinaryVenue::factory(),'service_date'=>now()->addDay()->toDateString(),'starts_at'=>'18:00','ends_at'=>'20:00','capacity_tables'=>5,'capacity_guests'=>20,'status'=>'available']; } }
