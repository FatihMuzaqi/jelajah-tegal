<?php

namespace App\Http\Controllers\Mitra;

use App\Actions\Catalog\SubmitCatalogDomain;
use App\Actions\Event\IssueEventTicket;
use App\Actions\Event\SaveEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;
use App\Http\Requests\Mitra\SaveEventRequest;
use App\Models\CatalogEntity;
use App\Models\CatalogOffer;
use App\Models\Category;
use App\Models\EventTicketType;
use App\Models\Facility;
use App\Models\Region;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventController extends Controller
{
    use ResolvesActiveMitra;

    public function index(Request $r): View
    {
        $items = CatalogEntity::where('mitra_id', $this->activeMitra($r)->id)->whereHas('serviceType', fn ($q) => $q->where('code', 'event'))->latest()->paginate(15);

        return view('mitra.catalog-domain.index', ['items' => $items, 'title' => 'Event', 'routePrefix' => 'mitra.event']);
    }

    public function create(Request $r): View
    {
        return view('mitra.catalog-domain.form', $this->refs() + ['title' => 'Event', 'routePrefix' => 'mitra.event', 'domain' => 'event']);
    }

    public function store(SaveEventRequest $r, SaveEvent $a): RedirectResponse
    {
        $e = $a->execute($this->activeMitra($r), $r->validated(), $r->user());

        return redirect()->route('mitra.event.show', $e);
    }

    public function show(Request $r, CatalogEntity $event): View
    {
        $this->owned($r, $event);

        return view('mitra.catalog-domain.show', [
            'item' => $event->load(['event.schedules', 'event.ticketTypes.offer', 'event.ticketTypes.tickets', 'location', 'media', 'facilities', 'moderationReports.actions']),
            'title' => 'Event',
            'routePrefix' => 'mitra.event',
            'domain' => 'event'
        ]);
    }

    public function edit(Request $r, CatalogEntity $event): View
    {
        $this->owned($r, $event);

        return view('mitra.catalog-domain.form', $this->refs() + [
            'item' => $event->load(['event', 'location']),
            'title' => 'Event',
            'routePrefix' => 'mitra.event',
            'domain' => 'event'
        ]);
    }

    public function update(SaveEventRequest $r, CatalogEntity $event, SaveEvent $a): RedirectResponse
    {
        $this->owned($r, $event);
        $a->execute($this->activeMitra($r), $r->validated(), $r->user(), $event);

        return redirect()->route('mitra.event.show', $event);
    }

    public function submit(Request $r, CatalogEntity $event, SubmitCatalogDomain $a): RedirectResponse
    {
        $this->owned($r, $event);
        $a->execute($event, 'event', $r->user(), [$event->event->ticketTypes()->exists() => 'tipe tiket']);

        return back();
    }

    public function archive(Request $r, CatalogEntity $event, \App\Services\AuditLogger $audit): RedirectResponse
    {
        $this->owned($r, $event);
        abort_unless(in_array($event->status, ['draft', 'rejected', 'published'], true), 422);
        $before = $event->status;
        $event->update(['status' => 'archived', 'archived_at' => now()]);
        $audit->record('event.archived', $event, ['status' => $before], ['status' => 'archived'], $r->user());

        return redirect()->route('mitra.event.index')->with('status', 'Event diarsipkan.');
    }

    public function schedule(Request $r, CatalogEntity $event): RedirectResponse
    {
        $this->owned($r, $event);
        $d = $r->validate([
            'title' => 'required|string|max:191',
            'starts_at' => ['required', 'date', 'after_or_equal:'.$event->event->starts_at->toDateTimeString()],
            'ends_at' => ['required', 'date', 'after:starts_at', 'before_or_equal:'.$event->event->ends_at->toDateTimeString()],
            'description' => 'nullable|string',
            'location_note' => 'nullable|string|max:255',
        ]);
        $event->event->schedules()->create($d);

        return back();
    }

    public function ticketType(Request $r, CatalogEntity $event): RedirectResponse
    {
        $this->owned($r, $event);
        $d = $r->validate(['name' => 'required|string|max:150', 'price' => 'required|numeric|min:0', 'quota' => 'required|integer|min:1', 'sale_starts_at' => 'nullable|date', 'sale_ends_at' => 'nullable|date|after:sale_starts_at']);
        DB::transaction(function () use ($event, $d) {
            $offer = CatalogOffer::create(['mitra_id' => $event->mitra_id, 'catalog_entity_id' => $event->id, 'offer_type' => 'event_ticket', 'sku' => 'EV-'.str()->upper(str()->random(10)), 'name' => $d['name'], 'price' => $d['price'], 'status' => 'active']);
            $event->event->ticketTypes()->create($d + ['catalog_offer_id' => $offer->id]);
        });

        return back()->with('status', 'Tipe tiket event berhasil ditambahkan.');
    }

    public function updateTicketType(Request $r, CatalogEntity $event, EventTicketType $type): RedirectResponse
    {
        $this->owned($r, $event);
        abort_unless($type->event_id === $event->event->id, 404);
        $d = $r->validate([
            'name' => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'sale_starts_at' => 'nullable|date',
            'sale_ends_at' => 'nullable|date|after:sale_starts_at',
        ]);

        DB::transaction(function () use ($type, $d) {
            $type->update([
                'name' => $d['name'],
                'quota' => $d['quota'],
                'sale_starts_at' => $d['sale_starts_at'] ?? null,
                'sale_ends_at' => $d['sale_ends_at'] ?? null,
            ]);
            $type->offer->update([
                'name' => $d['name'],
                'price' => $d['price'],
            ]);
        });

        return back()->with('status', 'Tiket event berhasil diperbarui.');
    }

    public function destroyTicketType(Request $r, CatalogEntity $event, EventTicketType $type): RedirectResponse
    {
        $this->owned($r, $event);
        abort_unless($type->event_id === $event->event->id, 404);
        DB::transaction(function () use ($type) {
            $type->offer()->delete();
            $type->delete();
        });

        return back()->with('status', 'Tiket event berhasil dihapus.');
    }

    public function issue(Request $r, CatalogEntity $event, EventTicketType $type, IssueEventTicket $a): RedirectResponse
    {
        $this->owned($r, $event);
        abort_unless($type->event_id === $event->event->id, 404);
        $d = $r->validate(['email' => 'required|email|exists:users,email']);
        [$ticket,$token] = $a->execute($type, User::where('email', $d['email'])->firstOrFail(), $r->user());

        return back()->with('status', 'Tiket diterbitkan. Token QR hanya ditampilkan sekali: '.$token);
    }

    private function owned(Request $r, CatalogEntity $e): void
    {
        abort_unless($r->user()->can('event.manage') && $e->mitra_id === $this->activeMitra($r)->id && $e->serviceType()->where('code', 'event')->exists(), 403);
    }

    private function refs(): array
    {
        $id = ServiceType::where('code', 'event')->value('id');

        return ['categories' => Category::where('service_type_id',$id)->get(), 'facilities' => Facility::where('service_type_id',$id)->get(), 'regions' => Region::orderBy('name')->get()];
    }
}
