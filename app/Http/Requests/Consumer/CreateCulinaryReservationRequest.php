<?php
namespace App\Http\Requests\Consumer;
use Illuminate\Foundation\Http\FormRequest;
class CreateCulinaryReservationRequest extends FormRequest { public function authorize():bool{return $this->user()!==null;} public function rules():array{return ['party_size'=>'required|integer|min:1|max:100','contact_name'=>'required|string|max:150','contact_phone'=>'required|string|max:32','notes'=>'nullable|string|max:1000'];} }
