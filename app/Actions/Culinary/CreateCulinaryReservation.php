<?php
namespace App\Actions\Culinary;
use App\Models\CulinaryReservation; use App\Models\CulinaryTableSlot; use App\Models\User; use App\Notifications\DomainActivityNotification; use App\Services\AuditLogger; use Illuminate\Support\Facades\DB; use Illuminate\Validation\ValidationException;
class CreateCulinaryReservation
{
    public function __construct(private AuditLogger $audit) {}
    public function execute(CulinaryTableSlot $slot, User $user, array $data): CulinaryReservation
    {
        return DB::transaction(function() use($slot,$user,$data){ $slot=CulinaryTableSlot::lockForUpdate()->findOrFail($slot->id); if($slot->status!=='available'||$slot->service_date->isPast()) throw ValidationException::withMessages(['slot'=>'Slot tidak tersedia.']); $reserved=(int)$slot->reservations()->whereIn('status',['requested','confirmed'])->sum('party_size'); if($reserved+(int)$data['party_size']>$slot->capacity_guests) throw ValidationException::withMessages(['party_size'=>'Kapasitas slot tidak mencukupi.']); $reservation=$slot->reservations()->create($data+['reservation_number'=>'CR-'.str()->upper(str()->random(12)),'culinary_venue_id'=>$slot->culinary_venue_id,'user_id'=>$user->id,'status'=>'requested']); $this->audit->record('culinary.reservation_requested',$reservation,[],['status'=>'requested'],$user); return $reservation; });
    }
}
