<?php

namespace App\Http\Controllers\Mitra;

use App\Actions\Catalog\SubmitCatalogDomain;
use App\Actions\Rental\SaveRentalVehicle;
use App\Actions\Rental\TransitionRentalBooking;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;
use App\Http\Requests\Mitra\SaveRentalVehicleRequest;
use App\Models\CatalogEntity;
use App\Models\CatalogOffer;
use App\Models\Category;
use App\Models\Region;
use App\Models\RentalBooking;
use App\Models\RenterDocument;
use App\Models\ServiceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RentalController extends Controller
{
    use ResolvesActiveMitra;

    public function index(Request $r): View
    {
        $items = CatalogEntity::where('mitra_id', $this->activeMitra($r)->id)->whereHas('serviceType', fn ($q) => $q->where('code', 'rental'))->latest()->paginate(15);

        return view('mitra.catalog-domain.index', ['items' => $items, 'title' => 'Rental', 'routePrefix' => 'mitra.rental']);
    }

    public function create(Request $r)
    {
        $mitra = $this->activeMitra($r);
        $service = \App\Models\ServiceType::where('code', 'rental')->firstOrFail();
        if (! $mitra->features()->where('service_type_id', $service->id)->where('status', 'enabled')->exists()) {
            return redirect()->route('mitra.features.index')->with('error', 'Fitur Rental belum aktif untuk Mitra Anda. Silakan ajukan aktivasi fitur di bawah ini.');
        }

        return view('mitra.catalog-domain.form', $this->refs() + ['title' => 'Rental', 'routePrefix' => 'mitra.rental', 'domain' => 'rental']);
    }

    public function store(SaveRentalVehicleRequest $r, SaveRentalVehicle $a): RedirectResponse
    {
        $mitra = $this->activeMitra($r);
        $service = \App\Models\ServiceType::where('code', 'rental')->firstOrFail();
        if (! $mitra->features()->where('service_type_id', $service->id)->where('status', 'enabled')->exists()) {
            return redirect()->route('mitra.features.index')->with('error', 'Fitur Rental belum aktif untuk Mitra Anda.');
        }

        $e = $a->execute($mitra, $r->validated(), $r->user());

        return redirect()->route('mitra.rental.show', $e);
    }

    public function show(Request $r, CatalogEntity $rental): View
    {
        $this->owned($r, $rental);

        return view('mitra.catalog-domain.show', [
            'item' => $rental->load(['rentalVehicle.rates.offer', 'rentalVehicle.availability', 'rentalVehicle.bookings.documents', 'location', 'media', 'moderationReports.actions']),
            'title' => 'Rental',
            'routePrefix' => 'mitra.rental',
            'domain' => 'rental'
        ]);
    }

    public function edit(Request $r, CatalogEntity $rental): View
    {
        $this->owned($r, $rental);

        return view('mitra.catalog-domain.form', $this->refs() + [
            'item' => $rental->load(['rentalVehicle', 'location']),
            'title' => 'Rental',
            'routePrefix' => 'mitra.rental',
            'domain' => 'rental'
        ]);
    }

    public function update(SaveRentalVehicleRequest $r, CatalogEntity $rental, SaveRentalVehicle $a): RedirectResponse
    {
        $this->owned($r, $rental);
        $a->execute($this->activeMitra($r), $r->validated(), $r->user(), $rental);

        return redirect()->route('mitra.rental.show', $rental);
    }

    public function submit(Request $r, CatalogEntity $rental, SubmitCatalogDomain $a): RedirectResponse
    {
        $this->owned($r, $rental);
        $a->execute($rental, 'rental', $r->user(), [$rental->rentalVehicle->rates()->exists() => 'tarif']);

        return back();
    }

    public function archive(Request $r, CatalogEntity $rental, \App\Services\AuditLogger $audit): RedirectResponse
    {
        $this->owned($r, $rental);
        abort_unless(in_array($rental->status, ['draft', 'rejected', 'published'], true), 422);
        $before = $rental->status;
        $rental->update(['status' => 'archived', 'archived_at' => now()]);
        $audit->record('rental.archived', $rental, ['status' => $before], ['status' => 'archived'], $r->user());

        return redirect()->route('mitra.rental.index')->with('status', 'Rental diarsipkan.');
    }

    public function rate(Request $r, CatalogEntity $rental): RedirectResponse
    {
        $this->owned($r, $rental);
        $d = $r->validate(['drive_mode' => 'required|in:self_drive,with_driver', 'duration_unit' => 'required|in:hour,day,week', 'duration_value' => 'required|integer|min:1', 'price' => 'required|numeric|min:0']);
        DB::transaction(function () use ($rental, $d) {
            $offer = CatalogOffer::create(['mitra_id' => $rental->mitra_id, 'catalog_entity_id' => $rental->id, 'offer_type' => 'rental_rate', 'sku' => 'RV-'.str()->upper(str()->random(10)), 'name' => $d['drive_mode'].' '.$d['duration_value'].' '.$d['duration_unit'], 'price' => $d['price'], 'status' => 'active']);
            $rental->rentalVehicle->rates()->create($d + ['catalog_offer_id' => $offer->id]);
        });

        return back()->with('status', 'Tarif sewa rental berhasil ditambahkan.');
    }

    public function updateRate(Request $r, CatalogEntity $rental, \App\Models\RentalRate $rate): RedirectResponse
    {
        $this->owned($r, $rental);
        abort_unless($rate->rental_vehicle_id === $rental->rentalVehicle->id, 404);
        $d = $r->validate([
            'drive_mode' => 'required|in:self_drive,with_driver',
            'price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($rate, $d) {
            $rate->update([
                'drive_mode' => $d['drive_mode'],
            ]);
            $rate->offer->update([
                'name' => $d['drive_mode'] . ' ' . $rate->duration_value . ' ' . $rate->duration_unit,
                'price' => $d['price'],
            ]);
        });

        return back()->with('status', 'Tarif sewa rental berhasil diperbarui.');
    }

    public function destroyRate(Request $r, CatalogEntity $rental, \App\Models\RentalRate $rate): RedirectResponse
    {
        $this->owned($r, $rental);
        abort_unless($rate->rental_vehicle_id === $rental->rentalVehicle->id, 404);
        DB::transaction(function () use ($rate) {
            $rate->offer()->delete();
            $rate->delete();
        });

        return back()->with('status', 'Tarif sewa rental berhasil dihapus.');
    }

    public function availability(Request $r, CatalogEntity $rental): RedirectResponse
    {
        $this->owned($r, $rental);
        $d = $r->validate(['service_date' => 'required|date|after_or_equal:today', 'status' => 'required|in:available,blocked', 'price_override' => 'nullable|numeric|min:0', 'notes' => 'nullable|string']);
        $rental->rentalVehicle->availability()->updateOrCreate(['service_date' => $d['service_date']], $d);

        return back();
    }

    public function transition(Request $r, CatalogEntity $rental, RentalBooking $booking, TransitionRentalBooking $a): RedirectResponse
    {
        $this->owned($r, $rental);
        abort_unless($booking->rental_vehicle_id === $rental->rentalVehicle->id, 404);
        $d = $r->validate(['status' => 'required|in:document_review,approved,active,completed,rejected,cancelled', 'reason' => 'nullable|required_if:status,rejected|string']);
        $a->execute($booking, $d['status'], $r->user(), $d['reason'] ?? null);

        return back();
    }

    public function reviewDocument(Request $r, RenterDocument $document): RedirectResponse
    {
        abort_unless(
            $r->user()->can('renter-documents.review')
            && $document->bookings()->where('rental_bookings.mitra_id', $this->activeMitra($r)->id)->exists(),
            403
        );
        $d = $r->validate(['decision' => 'required|in:approve,reject', 'reason' => 'nullable|required_if:decision,reject|string']);
        $document->update(['status' => $d['decision'] === 'approve' ? 'approved' : 'rejected', 'reviewed_by' => $r->user()->id, 'reviewed_at' => now(), 'rejection_reason' => $d['reason'] ?? null]);

        return back();
    }

    private function owned(Request $r, CatalogEntity $e): void
    {
        abort_unless($r->user()->can('rental.manage') && $e->mitra_id === $this->activeMitra($r)->id && $e->serviceType()->where('code', 'rental')->exists(), 403);
    }

    private function refs(): array
    {
        $id = ServiceType::where('code', 'rental')->value('id');

        return ['categories' => Category::where('service_type_id',$id)->get(), 'regions' => Region::orderBy('name')->get()];
    }
}
