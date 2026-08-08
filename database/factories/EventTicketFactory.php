<?php
namespace Database\Factories;
use App\Models\EventTicket; use App\Models\EventTicketType; use App\Models\Mitra; use App\Models\User; use Illuminate\Database\Eloquent\Factories\Factory;
class EventTicketFactory extends Factory { protected $model=EventTicket::class; public function definition(): array { return ['ticket_number'=>'EV-'.str()->upper(str()->random(12)),'event_ticket_type_id'=>EventTicketType::factory(),'mitra_id'=>Mitra::factory(),'user_id'=>User::factory(),'qr_token_hash'=>hash('sha256',str()->random(64)),'status'=>'issued']; } }
