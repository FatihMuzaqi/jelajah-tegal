<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;
class MitraBalance extends Model
{protected $primaryKey='mitra_id';public $incrementing=false;public $timestamps=false;protected $fillable=['mitra_id','currency','available_amount','held_amount','total_earned_amount','last_journal_id','rebuilt_at','updated_at'];protected function casts():array{return['available_amount'=>'decimal:2','held_amount'=>'decimal:2','total_earned_amount'=>'decimal:2','rebuilt_at'=>'datetime','updated_at'=>'datetime'];}public function mitra():BelongsTo{return $this->belongsTo(Mitra::class);}public function lastJournal():BelongsTo{return $this->belongsTo(LedgerJournal::class,'last_journal_id');}}
