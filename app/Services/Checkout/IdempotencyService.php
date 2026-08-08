<?php

namespace App\Services\Checkout;

use App\Models\IdempotencyKey;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IdempotencyService
{
    public function execute(User $user, string $scope, string $key, array $payload, callable $callback): Order
    {
        $fingerprint = hash('sha256', json_encode($this->canonical($payload), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        try {
            return DB::transaction(function () use ($user, $scope, $key, $fingerprint, $callback) {
                $record = IdempotencyKey::where('user_id', $user->id)->where('scope', $scope)->where('key_value', $key)->lockForUpdate()->first();
                if ($record) {
                    $this->assertFingerprint($record, $fingerprint);
                    if ($record->order_id) {
                        return Order::with(['items', 'payments'])->findOrFail($record->order_id);
                    }
                } else {
                    $record = IdempotencyKey::create(['user_id' => $user->id, 'scope' => $scope, 'key_value' => $key, 'fingerprint' => $fingerprint, 'expires_at' => now()->addDay()]);
                }$order = $callback();
                $record->update(['order_id' => $order->id, 'response_status' => 201, 'response_payload' => ['order_id' => $order->id, 'order_number' => $order->order_number, 'status' => $order->status->value, 'total_amount' => $order->total_amount]]);

                return $order;
            }, 5);
        } catch (UniqueConstraintViolationException) {
            $record = IdempotencyKey::where('user_id', $user->id)->where('scope', $scope)->where('key_value', $key)->firstOrFail();
            $this->assertFingerprint($record, $fingerprint);
            if (! $record->order_id) {
                throw ValidationException::withMessages(['idempotency_key' => 'Request identik masih diproses.']);
            }

return Order::with(['items', 'payments'])->findOrFail($record->order_id);
        }
    }

    private function assertFingerprint(IdempotencyKey $record, string $fingerprint): void
    {
        if (! hash_equals($record->fingerprint, $fingerprint)) {
            throw ValidationException::withMessages(['idempotency_key' => 'Key telah digunakan untuk payload berbeda.']);
        }
    }

    private function canonical(array $value): array
    {
        ksort($value);
        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = $this->canonical($item);
            }
        }

return $value;
    }
}
