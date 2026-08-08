<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;
class WithdrawalTransfer extends Model
{use HasUlids;protected $fillable=['withdrawal_claim_id','transfer_reference','amount','currency','bank_snapshot','recorded_by','transferred_at','notes'];protected function casts():array{return['amount'=>'decimal:2','bank_snapshot'=>'array','transferred_at'=>'datetime'];}public function withdrawal():BelongsTo{return $this->belongsTo(WithdrawalClaim::class,'withdrawal_claim_id');}}
