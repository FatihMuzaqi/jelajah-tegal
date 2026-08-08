<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class PaymentReconciliation extends Model
{use HasUlids;protected $fillable=['payment_id','initiated_by','source','local_status','provider_status','matched','provider_payload','error','checked_at'];protected function casts():array{return['matched'=>'boolean','provider_payload'=>'array','checked_at'=>'datetime'];}}
