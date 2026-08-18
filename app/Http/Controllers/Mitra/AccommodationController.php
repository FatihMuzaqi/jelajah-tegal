<?php

namespace App\Http\Controllers\Mitra;

use App\Actions\Accommodation\SaveAccommodation;
use App\Actions\Accommodation\SaveAccommodationRoom;
use App\Actions\Accommodation\SetRoomAvailability;
use App\Actions\Accommodation\SubmitAccommodation;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;
use App\Http\Requests\Mitra\SaveAccommodationRequest;
use App\Http\Requests\Mitra\SaveAccommodationRoomRequest;
use App\Http\Requests\Mitra\SetRoomAvailabilityRequest;
use App\Models\AccommodationRoom;
use App\Models\CatalogEntity;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Region;
use App\Models\ServiceType;
use App\Services\AuditLogger;
use App\Services\MitraMediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    use ResolvesActiveMitra;

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('accommodation.manage'), 403);
        $items = CatalogEntity::where('mitra_id', $this->activeMitra($request)->id)->whereHas('serviceType', fn($q) => $q->where('code', 'accommodation'))->latest()->paginate(15);

        return view('mitra.accommodation.index', compact('items'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->can('accommodation.manage'), 403);
        $mitra = $this->activeMitra($request);
        $service = \App\Models\ServiceType::where('code', 'accommodation')->firstOrFail();

        if (! $mitra->features()->where('service_type_id', $service->id)->where('status', 'enabled')->exists()) {
            return redirect()->route('mitra.features.index')->with('error', 'Fitur Penginapan & Hotel belum aktif untuk Mitra Anda. Silakan ajukan aktivasi fitur di bawah ini.');
        }

        return view('mitra.accommodation.form', $this->references());
    }

    public function store(SaveAccommodationRequest $request, SaveAccommodation $action): RedirectResponse
    {
        $mitra = $this->activeMitra($request);
        $service = \App\Models\ServiceType::where('code', 'accommodation')->firstOrFail();

        if (! $mitra->features()->where('service_type_id', $service->id)->where('status', 'enabled')->exists()) {
            return redirect()->route('mitra.features.index')->with('error', 'Fitur Penginapan & Hotel belum aktif untuk Mitra Anda.');
        }

        $entity = $action->execute($mitra, $request->validated(), $request->user());

        return redirect()->route('mitra.accommodation.show', $entity)->with('status', 'Draft penginapan berhasil dibuat.');
    }

    public function show(Request $request, CatalogEntity $accommodation): View
    {
        $this->owned($request, $accommodation);

        return view('mitra.accommodation.show', ['accommodation' => $accommodation->load($this->relations()), 'facilities' => $this->references()['facilities']]);
    }

    public function edit(Request $request, CatalogEntity $accommodation): View
    {
        $this->owned($request, $accommodation);

        return view('mitra.accommodation.form', $this->references() + [
            'accommodation' => $accommodation->load(['accommodation', 'location', 'facilities']),
        ]);
    }

    public function update(SaveAccommodationRequest $request, CatalogEntity $accommodation, SaveAccommodation $action): RedirectResponse
    {
        $this->owned($request, $accommodation);
        $action->execute($this->activeMitra($request), $request->validated(), $request->user(), $accommodation);

        return redirect()->route('mitra.accommodation.show', $accommodation)->with('status', 'Penginapan diperbarui.');
    }

    public function submit(Request $request, CatalogEntity $accommodation, SubmitAccommodation $action): RedirectResponse
    {
        $this->owned($request, $accommodation);
        abort_unless($request->user()->can('accommodation.submit'), 403);
        $action->execute($accommodation, $request->user());

        return back()->with('status', 'Penginapan diajukan untuk moderasi.');
    }

    public function archive(Request $request, CatalogEntity $accommodation, AuditLogger $audit): RedirectResponse
    {
        $this->owned($request, $accommodation);
        abort_unless(in_array($accommodation->status, ['draft', 'rejected', 'published'], true), 422);
        $from = $accommodation->status;
        $accommodation->update(['status' => 'archived', 'archived_at' => now()]);
        $audit->record('accommodation.archived', $accommodation, ['status' => $from], ['status' => 'archived'], $request->user());

        return redirect()->route('mitra.accommodation.index')->with('status', 'Penginapan diarsipkan.');
    }

    public function media(Request $request, CatalogEntity $accommodation, MitraMediaStorage $storage, AuditLogger $audit): RedirectResponse
    {
        $this->owned($request, $accommodation);
        $data = $request->validate(['image' => 'required|file|mimes:jpg,jpeg,png,webp|max:8192', 'role' => 'required|in:cover,gallery', 'caption' => 'nullable|string|max:255']);
        $asset = $storage->store($this->activeMitra($request), $request->file('image'), 'accommodation', false);
        DB::transaction(function () use ($accommodation, $asset, $data, $audit, $request) {
            if ($data['role'] === 'cover') {
                $accommodation->media()->wherePivot('role', 'cover')->detach();
            }
            $sort = (int) DB::table('catalog_media')->where('catalog_entity_id', $accommodation->id)->max('sort_order') + 1;
            $accommodation->media()->attach($asset->id, ['role' => $data['role'], 'sort_order' => $sort, 'caption' => $data['caption'] ?? null]);
            $audit->record('accommodation.media_added', $accommodation, [], ['media_asset_id' => $asset->id], $request->user());
        });

        return back()->with('status', 'Media properti ditambahkan.');
    }

    public function storeRoom(SaveAccommodationRoomRequest $request, CatalogEntity $accommodation, SaveAccommodationRoom $action): RedirectResponse
    {
        $this->owned($request, $accommodation);
        $action->execute($accommodation->accommodation, $request->validated() + ['sku' => $request->validated('sku') ?? Str::upper(Str::random(10))], $request->user());

        return back()->with('status', 'Kamar dibuat.');
    }

    public function editRoom(Request $request, CatalogEntity $accommodation, AccommodationRoom $room): View
    {
        $this->roomOwned($request, $accommodation, $room);

        return view('mitra.accommodation.room-form', ['accommodation' => $accommodation, 'room' => $room->load(['offer', 'facilities']), 'facilities' => $this->references()['facilities']]);
    }

    public function updateRoom(SaveAccommodationRoomRequest $request, CatalogEntity $accommodation, AccommodationRoom $room, SaveAccommodationRoom $action): RedirectResponse
    {
        $this->roomOwned($request, $accommodation, $room);
        $action->execute($accommodation->accommodation, $request->validated(), $request->user(), $room);

        return redirect()->route('mitra.accommodation.show', $accommodation)->with('status', 'Kamar diperbarui.');
    }

    public function archiveRoom(Request $request, CatalogEntity $accommodation, AccommodationRoom $room, AuditLogger $audit): RedirectResponse
    {
        $this->roomOwned($request, $accommodation, $room);
        DB::transaction(function () use ($room, $accommodation, $audit, $request) {
            $room->update(['status' => 'archived']);
            $room->offer->update(['status' => 'archived']);
            $audit->record('accommodation.room_archived', $accommodation, [], ['room_id' => $room->id], $request->user());
        });

        return back()->with('status', 'Kamar diarsipkan.');
    }

    public function roomMedia(Request $request, CatalogEntity $accommodation, AccommodationRoom $room, MitraMediaStorage $storage): RedirectResponse
    {
        $this->roomOwned($request, $accommodation, $room);
        $data = $request->validate(['image' => 'required|file|mimes:jpg,jpeg,png,webp|max:8192', 'role' => 'required|in:cover,gallery', 'caption' => 'nullable|string|max:255']);
        $asset = $storage->store($this->activeMitra($request), $request->file('image'), 'accommodation-room', false);
        DB::transaction(function () use ($room, $asset, $data) {
            if ($data['role'] === 'cover') {
                $room->media()->wherePivot('role', 'cover')->detach();
            }
            $sort = (int) DB::table('accommodation_room_media')->where('accommodation_room_id', $room->id)->max('sort_order') + 1;
            $room->media()->attach($asset->id, ['role' => $data['role'], 'sort_order' => $sort, 'caption' => $data['caption'] ?? null]);
        });

        return back()->with('status', 'Media kamar ditambahkan.');
    }

    public function calendar(Request $request, CatalogEntity $accommodation, AccommodationRoom $room): View
    {
        $this->roomOwned($request, $accommodation, $room);
        $rows = $room->offer->availabilities()->whereDate('service_date', '>=', today())->orderBy('service_date')->paginate(31);

        return view('mitra.accommodation.calendar', compact('accommodation', 'room', 'rows'));
    }

    public function updateCalendar(SetRoomAvailabilityRequest $request, CatalogEntity $accommodation, AccommodationRoom $room, SetRoomAvailability $action): RedirectResponse
    {
        $this->roomOwned($request, $accommodation, $room);
        $count = $action->execute($room, $request->validated());

        return back()->with('status', $count . ' tanggal diperbarui.');
    }

    private function owned(Request $request, CatalogEntity $entity): void
    {
        abort_unless($request->user()->can('accommodation.manage') && $entity->mitra_id === $this->activeMitra($request)->id && $entity->serviceType()->where('code', 'accommodation')->exists(), 403);
    }

    private function roomOwned(Request $request, CatalogEntity $entity, AccommodationRoom $room): void
    {
        $this->owned($request, $entity);
        abort_unless($room->accommodation_id === $entity->accommodation?->id, 404);
    }

    private function references(): array
    {
        $id = ServiceType::where('code', 'accommodation')->value('id');

        return ['categories' => Category::where('service_type_id', $id)->where('is_active', true)->orderBy('name')->get(), 'facilities' => Facility::where('service_type_id', $id)->where('is_active', true)->orderBy('name')->get(), 'regions' => Region::orderBy('name')->get()];
    }

    private function relations(): array
    {
        return ['accommodation.rooms.offer.availabilities', 'accommodation.rooms.media', 'accommodation.rooms.facilities', 'location', 'category', 'region', 'facilities', 'media', 'moderationReports.actions.actor'];
    }
}
