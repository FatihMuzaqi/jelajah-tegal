<?php
namespace Database\Factories;
use App\Models\MediaAsset; use App\Models\RenterDocument; use App\Models\User; use Illuminate\Database\Eloquent\Factories\Factory;
class RenterDocumentFactory extends Factory { protected $model=RenterDocument::class; public function definition(): array { return ['user_id'=>User::factory(),'media_asset_id'=>MediaAsset::factory(),'document_type'=>'sim_a','document_number'=>'1234567890','expires_at'=>now()->addYear(),'status'=>'pending']; } }
