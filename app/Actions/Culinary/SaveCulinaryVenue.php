<?php
namespace App\Actions\Culinary;
use App\Models\CatalogEntity; use App\Models\Mitra; use App\Models\ServiceType; use App\Services\AuditLogger; use Illuminate\Support\Arr; use Illuminate\Support\Facades\DB; use Illuminate\Validation\ValidationException;
class SaveCulinaryVenue
{
    public function __construct(private AuditLogger $audit) {}
    public function execute(Mitra $mitra, array $data, $actor, ?CatalogEntity $entity=null): CatalogEntity
    {
        $service=ServiceType::where('code','culinary')->firstOrFail(); abort_unless($mitra->features()->where('service_type_id',$service->id)->where('status','enabled')->exists(),403);
        if($entity){ abort_unless($entity->mitra_id===$mitra->id && $entity->service_type_id===$service->id,403); if(!in_array($entity->status,['draft','rejected','published','pending_review'],true)) throw ValidationException::withMessages(['status'=>'Venue kuliner dengan status saat ini tidak dapat diubah.']); }
        return DB::transaction(function() use($mitra,$service,$data,$actor,$entity){ $before=$entity?->toArray()??[]; $entity??=new CatalogEntity(['mitra_id'=>$mitra->id,'service_type_id'=>$service->id]); $entity->fill(Arr::only($data,['category_id','region_id','name','slug','description','address','is_featured'])); $entity->status=$entity->status??'draft'; $entity->save(); $entity->culinary()->updateOrCreate([],Arr::only($data,['venue_type','accepts_reservations','phone','reservation_notes'])); if(isset($data['latitude'],$data['longitude'])){ $lat = (float) $data['latitude']; $lng = (float) $data['longitude']; DB::table('catalog_locations')->updateOrInsert(['catalog_entity_id' => $entity->id], ['location' => DB::raw("ST_GeomFromText('POINT({$lng} {$lat})', 4326)"), 'latitude' => $lat, 'longitude' => $lng, 'updated_at' => now(), 'created_at' => now()]); } $entity->facilities()->sync($data['facilities']??[]); $this->audit->record($before?'culinary.updated':'culinary.created',$entity,$before,$entity->fresh()->toArray(),$actor); return $entity->fresh(['culinary','location']); });
    }
}
