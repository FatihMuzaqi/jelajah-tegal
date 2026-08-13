<?php
namespace App\Actions\Culinary;
use App\Models\CatalogEntity; use App\Models\Mitra; use App\Models\ServiceType; use App\Services\AuditLogger; use Illuminate\Support\Arr; use Illuminate\Support\Facades\DB; use Illuminate\Validation\ValidationException;
class SaveCulinaryVenue
{
    public function __construct(private AuditLogger $audit) {}
    public function execute(Mitra $mitra, array $data, $actor, ?CatalogEntity $entity=null): CatalogEntity
    {
        $service=ServiceType::where('code','culinary')->firstOrFail(); abort_unless($mitra->features()->where('service_type_id',$service->id)->where('status','enabled')->exists(),403);
        if($entity){ abort_unless($entity->mitra_id===$mitra->id && $entity->service_type_id===$service->id,403); if(!in_array($entity->status,['draft','rejected'],true)) throw ValidationException::withMessages(['status'=>'Hanya draft atau venue ditolak yang dapat diubah.']); }
        return DB::transaction(function() use($mitra,$service,$data,$actor,$entity){ $before=$entity?->toArray()??[]; $entity??=new CatalogEntity(['mitra_id'=>$mitra->id,'service_type_id'=>$service->id]); $entity->fill(Arr::only($data,['category_id','region_id','name','slug','description','address','is_featured'])); $entity->status='draft'; $entity->save(); $entity->culinary()->updateOrCreate([],Arr::only($data,['venue_type','accepts_reservations','phone','reservation_notes'])); if(isset($data['latitude'],$data['longitude'])){ DB::statement('INSERT INTO catalog_locations (catalog_entity_id, location, latitude, longitude, created_at, updated_at) VALUES (?, ST_PointFromText(CONCAT("POINT(", ?, " ", ?, ")"), 4326), ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE location=VALUES(location), latitude=VALUES(latitude), longitude=VALUES(longitude), updated_at=NOW()',[$entity->id,$data['longitude'],$data['latitude'],$data['latitude'],$data['longitude']]); } $entity->facilities()->sync($data['facilities']??[]); $this->audit->record($before?'culinary.updated':'culinary.created',$entity,$before,$entity->fresh()->toArray(),$actor); return $entity->fresh(['culinary','location']); });
    }
}
