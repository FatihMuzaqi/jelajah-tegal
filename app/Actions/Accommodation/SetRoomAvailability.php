<?php

namespace App\Actions\Accommodation;

use App\Models\AccommodationRoom;
use App\Models\Availability;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SetRoomAvailability
{
    public function execute(AccommodationRoom $room, array $data): int
    {
        return DB::transaction(function () use ($room, $data) {
            $room = AccommodationRoom::lockForUpdate()->with('offer')->findOrFail($room->id);
            $start = Carbon::parse($data['start_date'])->startOfDay();
            $end = Carbon::parse($data['end_date'])->startOfDay();
            $count = 0;
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $row = Availability::where('catalog_offer_id', $room->catalog_offer_id)->whereDate('service_date', $date)->whereNull('starts_at')->lockForUpdate()->first();
                $reserved = $row?->reserved_quantity ?? 0;
                $available = (int) $data['available_units'];
                if ($available + $reserved > $room->total_units) {
                    throw ValidationException::withMessages(['available_units' => 'Unit tersedia ditambah reservasi melebihi total unit kamar.']);
                }
                $values = ['mitra_id' => $room->offer->mitra_id, 'capacity' => $available + $reserved, 'price_override' => $data['price_override'] ?? null, 'status' => ($data['is_blocked'] ?? false) ? 'blocked' : ($available === 0 ? 'sold_out' : 'available')];
                if ($row) {
                    $row->update($values);
                } else {
                    Availability::create($values + ['catalog_offer_id' => $room->catalog_offer_id, 'service_date' => $date->toDateString()]);
                }
                $count++;
            }

            return $count;
        }, 3);
    }
}
