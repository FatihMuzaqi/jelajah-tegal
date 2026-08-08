<?php
namespace Database\Factories;
use App\Models\Mitra; use App\Models\RentalBooking; use App\Models\RentalRate; use App\Models\RentalVehicle; use App\Models\User; use Illuminate\Database\Eloquent\Factories\Factory;
class RentalBookingFactory extends Factory { protected $model=RentalBooking::class; public function definition(): array { return ['booking_number'=>'RB-'.str()->upper(str()->random(10)),'rental_vehicle_id'=>RentalVehicle::factory(),'rental_rate_id'=>RentalRate::factory(),'mitra_id'=>Mitra::factory(),'user_id'=>User::factory(),'pickup_at'=>now()->addDay(),'return_at'=>now()->addDays(2),'pickup_location'=>'Tegal','return_location'=>'Tegal','drive_mode'=>'self_drive','unit_price'=>350000,'deposit_amount'=>500000,'total_amount'=>850000,'status'=>'requested']; } }
