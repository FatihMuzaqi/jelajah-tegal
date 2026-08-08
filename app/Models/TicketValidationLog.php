<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class TicketValidationLog extends Model
{use HasUlids;protected $fillable=['ticket_id','gatekeeper_user_id','gatekeeper_assignment_id','result','device_reference','presented_token_hash','scanned_at','metadata'];protected function casts():array{return['scanned_at'=>'datetime','metadata'=>'array'];}}
