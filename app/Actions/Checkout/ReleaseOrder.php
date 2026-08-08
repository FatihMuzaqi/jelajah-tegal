<?php

namespace App\Actions\Checkout;

use App\Models\Availability;
use App\Models\CulinaryReservation;
use App\Models\EventTicketType;
use App\Models\DatabaseNotification;
use App\Models\Order;
use App\Models\RentalBooking;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;

class ReleaseOrder
{
    public function __construct(private AuditLogger $audit) {}

    public function execute(Order $order, string $target = 'cancelled'): Order
    {
        return DB::transaction(function () use ($order, $target) {
            $order = Order::with(['items.holds', 'voucherUsages.voucher', 'payments'])->lockForUpdate()->findOrFail($order->id);
            if (! in_array($order->status->value, ['pending_payment'], true)) {
                return $order;
            }foreach ($order->items as $item) {
                foreach ($item->holds->where('status', 'active') as $hold) {
                    if ($hold->resource_type === 'availability') {
                        $resource = Availability::lockForUpdate()->find($hold->resource_id);
                        if ($resource) {
                            $resource->update(['reserved_quantity' => max(0, $resource->reserved_quantity - $hold->quantity)]);
                        }
                    } elseif ($hold->resource_type === 'event_ticket_type') {
                        $resource = EventTicketType::lockForUpdate()->find($hold->resource_id);
                        if ($resource) {
                            $resource->update(['reserved_quantity' => max(0, $resource->reserved_quantity - $hold->quantity)]);
                        }
                    } elseif ($hold->resource_type === 'culinary_reservation') {
                        CulinaryReservation::whereKey($hold->resource_id)->whereIn('status', ['requested', 'confirmed'])->update(['status' => 'cancelled']);
                    } elseif ($hold->resource_type === 'rental_booking') {
                        RentalBooking::whereKey($hold->resource_id)->where('status', 'approved')->update(['status' => 'cancelled']);
                    }$hold->update(['status' => 'released']);
                }
            }$order->voucherUsages()->where('status', 'applied')->each(function ($usage) {
                $usage->update(['status' => 'reversed', 'reversed_at' => now()]);
                $voucher = $usage->voucher()->lockForUpdate()->first();
                if ($voucher) {
                    $voucher->update([
                        'used_count' => max(0, $voucher->used_count - 1),
                        'status' => $voucher->status->value === 'exhausted' ? 'active' : $voucher->status->value,
                    ]);
                }
            });
            $paymentStatus = $target === 'expired' ? 'expired' : 'cancelled';
            $order->payments()->whereIn('status', ['pending', 'authorized'])->update(['status' => $paymentStatus, $target === 'expired' ? 'expired_at' : 'cancelled_at' => now()]);
            $order->update(['status' => $target, 'payment_status' => $paymentStatus, 'cancelled_at' => $target === 'cancelled' ? now() : null]);
            $this->audit->record('checkout.'.$target, $order, [], ['holds_released' => true]);
            DatabaseNotification::create(['user_id' => $order->user_id, 'mitra_id' => $order->mitra_id, 'type' => 'order.'.$target, 'data' => ['order_id' => $order->id, 'order_number' => $order->order_number]]);

            return $order->fresh();
        });
    }
}
