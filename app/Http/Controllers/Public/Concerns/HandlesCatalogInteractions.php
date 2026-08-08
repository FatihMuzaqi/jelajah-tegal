<?php
namespace App\Http\Controllers\Public\Concerns;
use App\Models\CatalogEntity; use Illuminate\Http\RedirectResponse; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB;
trait HandlesCatalogInteractions
{
    protected function toggleFavorite(Request $request,CatalogEntity $entity,bool $add): RedirectResponse { abort_unless($entity->status==='published',404);if($add)$entity->favorites()->firstOrCreate(['user_id'=>$request->user()->id]);else $entity->favorites()->where('user_id',$request->user()->id)->delete();return back()->with('status',$add?'Ditambahkan ke favorit.':'Dihapus dari favorit.'); }
    protected function storeReview(Request $request,CatalogEntity $entity): RedirectResponse { abort_unless($entity->status==='published',404);$data=$request->validate(['rating'=>'required|integer|between:1,5','title'=>'nullable|string|max:191','body'=>'required|string|min:10|max:3000']);$entity->reviews()->create($data+['user_id'=>$request->user()->id,'status'=>'pending']);return back()->with('status','Ulasan menunggu moderasi.'); }
}
