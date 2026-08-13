<?php

namespace App\Http\Controllers\Mitra;

use App\Actions\Tourism\SaveTourismDestination;
use App\Actions\Tourism\SetTourismQuota;
use App\Actions\Tourism\SubmitTourismDestination;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;
use App\Http\Requests\Mitra\SaveTourismRequest;
use App\Models\CatalogEntity;
use App\Models\CatalogOffer;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Region;
use App\Models\ServiceType;
use App\Models\TourismTicketPackage;
use App\Services\AuditLogger;
use App\Services\MitraMediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TourismController extends Controller
{
    use ResolvesActiveMitra;

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('tourism.manage'), 403);
        $mitra = $this->activeMitra($request);
        $items = CatalogEntity::where('mitra_id', $mitra->id)->whereHas('serviceType', fn ($q) => $q->where('code', 'tourism'))->latest()->paginate(15);

        return view('mitra.tourism.index', compact('items'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->can('tourism.manage'), 403);
        $mitra = $this->activeMitra($request);
        $service = ServiceType::where('code', 'tourism')->firstOrFail();

        if (! $mitra->features()->where('service_type_id', $service->id)->where('status', 'enabled')->exists()) {
            return redirect()->route('mitra.features.index')->with('error', 'Fitur Destinasi Wisata belum aktif untuk Mitra Anda. Silakan ajukan aktivasi fitur di bawah ini.');
        }

        return view('mitra.tourism.form', $this->references());
    }

    public function store(SaveTourismRequest $request, SaveTourismDestination $action): RedirectResponse
    {
        $mitra = $this->activeMitra($request);
        $service = ServiceType::where('code', 'tourism')->firstOrFail();

        if (! $mitra->features()->where('service_type_id', $service->id)->where('status', 'enabled')->exists()) {
            return redirect()->route('mitra.features.index')->with('error', 'Fitur Destinasi Wisata belum aktif untuk Mitra Anda.');
        }

        $entity = $action->execute($mitra, $request->validated(), $request->user());

        return redirect()->route('mitra.tourism.show', $entity)->with('status', 'Draft wisata berhasil dibuat.');
    }

    public function show(Request $request, CatalogEntity $tourism): View
    {
        $this->owned($request, $tourism);

        return view('mitra.tourism.show', ['tourism' => $tourism->load(['tourism', 'location', 'category', 'region', 'facilities', 'operatingHours', 'media', 'offers.ticketPackage', 'offers.availabilities', 'moderationReports.actions.actor'])]);
    }

    public function edit(Request $request, CatalogEntity $tourism): View
    {
        $this->owned($request, $tourism);

        return view('mitra.tourism.form', $this->references() + compact('tourism'));
    }

    public function update(SaveTourismRequest $request, CatalogEntity $tourism, SaveTourismDestination $action): RedirectResponse
    {
        $this->owned($request, $tourism);
        $action->execute($this->activeMitra($request), $request->validated(), $request->user(), $tourism);

        return redirect()->route('mitra.tourism.show', $tourism)->with('status', 'Wisata diperbarui.');
    }

    public function submit(Request $request, CatalogEntity $tourism, SubmitTourismDestination $action): RedirectResponse
    {
        $this->owned($request, $tourism);
        abort_unless($request->user()->can('tourism.submit'), 403);
        $action->execute($tourism, $request->user());

        return back()->with('status', 'Wisata diajukan untuk moderasi.');
    }

    public function archive(Request $request, CatalogEntity $tourism, AuditLogger $audit): RedirectResponse
    {
        $this->owned($request, $tourism);
        abort_unless($request->user()->can('tourism.manage'), 403);
        abort_unless(in_array($tourism->status, ['draft', 'rejected', 'published'], true), 422);
        $before = $tourism->status;
        $tourism->update(['status' => 'archived', 'archived_at' => now()]);
        $audit->record('tourism.archived', $tourism, ['status' => $before], ['status' => 'archived'], $request->user());

        return redirect()->route('mitra.tourism.index')->with('status', 'Wisata diarsipkan.');
    }

    public function media(Request $request, CatalogEntity $tourism, MitraMediaStorage $storage, AuditLogger $audit): RedirectResponse
    {
        $this->owned($request, $tourism);
        abort_unless($request->user()->can('tourism.manage'), 403);
        $data = $request->validate(['image' => 'required|file|mimes:jpg,jpeg,png,webp|max:8192', 'role' => 'required|in:cover,gallery', 'caption' => 'nullable|string|max:255']);
        $asset = $storage->store($this->activeMitra($request), $request->file('image'), 'tourism', false);
        DB::transaction(function () use ($tourism, $asset, $data, $audit, $request) {
            if ($data['role'] === 'cover') {
                $tourism->media()->wherePivot('role', 'cover')->detach();
            } $order = (int) DB::table('catalog_media')->where('catalog_entity_id', $tourism->id)->max('sort_order') + 1;
            $tourism->media()->attach($asset->id, ['role' => $data['role'], 'sort_order' => $order, 'caption' => $data['caption'] ?? null]);
            $audit->record('tourism.media_added', $tourism, [], ['media_asset_id' => $asset->id], $request->user());
        });

        return back()->with('status', 'Media ditambahkan.');
    }

    public function hours(Request $request, CatalogEntity $tourism, AuditLogger $audit): RedirectResponse
    {
        $this->owned($request, $tourism);
        abort_unless($request->user()->can('tourism.manage'), 403);
        $hours = $request->validate(['hours' => 'required|array|min:1', 'hours.*.weekday' => 'required|integer|between:1,7', 'hours.*.is_closed' => 'sometimes|boolean', 'hours.*.opens_at' => 'nullable|date_format:H:i', 'hours.*.closes_at' => 'nullable|date_format:H:i|after:hours.*.opens_at'])['hours'];
        DB::transaction(function () use ($tourism, $hours, $audit, $request) {
            $tourism->operatingHours()->delete();
            foreach ($hours as $hour) {
                $tourism->operatingHours()->create(['weekday' => $hour['weekday'], 'sequence' => 1, 'is_closed' => (bool) ($hour['is_closed'] ?? false), 'opens_at' => $hour['opens_at'] ?? null, 'closes_at' => $hour['closes_at'] ?? null]);
            } $audit->record('tourism.hours_updated', $tourism, [], ['count' => count($hours)], $request->user());
        });

        return back()->with('status', 'Jam operasional disimpan.');
    }

    public function package(Request $request, CatalogEntity $tourism): RedirectResponse
    {
        $this->owned($request, $tourism);
        abort_unless($request->user()->can('tourism.manage'), 403);
        $data = $request->validate(['name' => 'required|string|max:150', 'sku' => 'nullable|string|max:100', 'price' => 'required|decimal:0,2|min:0', 'quota_per_day' => 'nullable|integer|min:0']);
        DB::transaction(function () use ($tourism, $data) {
            $offer = CatalogOffer::create(['mitra_id' => $tourism->mitra_id, 'catalog_entity_id' => $tourism->id, 'offer_type' => 'tourism_ticket', 'sku' => $data['sku'] ?? Str::upper(Str::random(10)), 'name' => $data['name'], 'price' => $data['price'], 'status' => 'active']);
            TourismTicketPackage::create(['tourism_destination_id' => $tourism->tourism->id, 'catalog_offer_id' => $offer->id, 'name' => $data['name'], 'quota_per_day' => $data['quota_per_day'] ?? null]);
        });

        return back()->with('status', 'Paket tiket dibuat.');
    }

    public function quota(Request $request, CatalogEntity $tourism, TourismTicketPackage $package, SetTourismQuota $action): RedirectResponse
    {
        $this->owned($request, $tourism);
        abort_unless($package->tourism_destination_id === $tourism->tourism->id, 404);
        $data = $request->validate(['service_date' => 'required|date|after_or_equal:today', 'capacity' => 'required|integer|min:0', 'price_override' => 'nullable|decimal:0,2|min:0', 'status' => 'nullable|in:available,closed,sold_out']);
        $action->execute($package, $data);

        return back()->with('status', 'Kuota disimpan.');
    }

    private function owned(Request $request, CatalogEntity $entity): void
    {
        abort_unless($entity->mitra_id === $this->activeMitra($request)->id, 403);
        Gate::authorize('update', $entity);
    }

    private function references(): array
    {
        $id = ServiceType::where('code', 'tourism')->value('id');

        return ['categories' => Category::where('service_type_id', $id)->where('is_active', true)->orderBy('name')->get(), 'facilities' => Facility::where('service_type_id', $id)->where('is_active', true)->orderBy('name')->get(), 'regions' => Region::orderBy('name')->get()];
    }
}
