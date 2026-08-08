<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class PaymentWebhookEvent extends Model
{use HasUlids;protected $fillable=['provider','provider_event_id','payment_id','order_id','event_type','payload_hash','gross_amount','payload','source','received_at','processed_at','processing_error'];protected function casts():array{return['gross_amount'=>'decimal:2','payload'=>'array','received_at'=>'datetime','processed_at'=>'datetime'];}}
