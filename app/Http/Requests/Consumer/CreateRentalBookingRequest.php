<?php
namespace App\Http\Requests\Consumer;
use Illuminate\Foundation\Http\FormRequest;
class CreateRentalBookingRequest extends FormRequest { public function authorize():bool{return $this->user()!==null;} public function rules():array{return ['rental_rate_id'=>'required|exists:rental_rates,id','pickup_at'=>'required|date|after:now','return_at'=>'required|date|after:pickup_at','pickup_location'=>'required|string|max:255','return_location'=>'required|string|max:255','drive_mode'=>'required|in:self_drive,with_driver','document_ids'=>'array','document_ids.*'=>'exists:renter_documents,id'];} }
