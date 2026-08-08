<?php
namespace Database\Factories;
use App\Models\CulinaryReservation; use App\Models\CulinaryTableSlot; use App\Models\User; use Illuminate\Database\Eloquent\Factories\Factory;
class CulinaryReservationFactory extends Factory { protected $model=CulinaryReservation::class; public function definition(): array { $slot=CulinaryTableSlot::factory(); return ['reservation_number'=>'CR-'.str()->upper(str()->random(10)),'culinary_venue_id'=>fn(array $a)=>CulinaryTableSlot::find($a['culinary_table_slot_id'])->culinary_venue_id,'culinary_table_slot_id'=>$slot,'user_id'=>User::factory(),'party_size'=>2,'contact_name'=>$this->faker->name(),'contact_phone'=>'081234567890','status'=>'requested']; } }
