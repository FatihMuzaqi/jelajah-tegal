<?php

namespace App\Actions\Tourism;

use App\Models\Availability;
use App\Models\TourismTicketPackage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SetTourismQuota
{
    public function execute(TourismTicketPackage $package, array $data): Availability
    {
        return DB::transaction(function () use ($package, $data) {
            $package = TourismTicketPackage::lockForUpdate()->with('offer')->findOrFail($package->id);
            $row = Availability::where('catalog_offer_id', $package->catalog_offer_id)->whereDate('service_date', $data['service_date'])->whereNull('starts_at')->lockForUpdate()->first();
            $capacity = (int) $data['capacity'];
            if ($row && $capacity < $row->reserved_quantity) {
                throw ValidationException::withMessages(['capacity' => 'Kuota tidak boleh lebih kecil dari reservasi yang sudah tercatat.']);
            }
            if ($package->quota_per_day !== null && $capacity > $package->quota_per_day) {
                throw ValidationException::withMessages(['capacity' => 'Kuota tanggal melebihi batas paket per hari.']);
            }
            $values = ['mitra_id' => $package->offer->mitra_id, 'capacity' => $capacity, 'price_override' => $data['price_override'] ?? null, 'status' => $data['status'] ?? 'available'];
            if ($row) {
                $row->update($values);

                return $row->fresh();
            }

            return Availability::create($values + ['catalog_offer_id' => $package->catalog_offer_id, 'service_date' => $data['service_date']]);
        }, 3);
    }
}
