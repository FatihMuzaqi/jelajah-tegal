<?php

namespace App\Services\Vouchers;

use App\Models\User;
use App\Models\Voucher;
use App\Support\Money;
use Illuminate\Validation\ValidationException;

class VoucherEngine
{
    public function claim(User $user, string $code, ?string $mitraId = null): Voucher
    {
        $voucher = $this->findLocked($code, $mitraId);
        $this->assertActive($voucher);
        $voucher->claims()->firstOrCreate(['user_id' => $user->id], ['status' => 'claimed', 'claimed_at' => now(), 'expires_at' => $voucher->ends_at]);

        return $voucher;
    }

    public function apply(User $user, string $code, string $mitraId, string $serviceTypeId, int $subtotalMinor): VoucherResult
    {
        $voucher = $this->findLocked($code, $mitraId);
        $this->assertActive($voucher);
        if ($voucher->mitra_id && $voucher->mitra_id !== $mitraId) {
            throw ValidationException::withMessages(['voucher_code' => 'Voucher bukan milik Mitra ini.']);
        }if ($voucher->serviceTypes()->exists() && ! $voucher->serviceTypes()->whereKey($serviceTypeId)->exists()) {
            throw ValidationException::withMessages(['voucher_code' => 'Voucher tidak berlaku untuk layanan ini.']);
        }$claim = $voucher->claims()->where('user_id', $user->id)->where('status', 'claimed')->lockForUpdate()->first();
        if (! $claim || $claim->expires_at?->isPast()) {
            throw ValidationException::withMessages(['voucher_code' => 'Voucher belum diklaim atau klaim kedaluwarsa.']);
        }if ($subtotalMinor < Money::toMinor($voucher->minimum_order_amount)) {
            throw ValidationException::withMessages(['voucher_code' => 'Minimum order belum terpenuhi.']);
        }if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) {
            throw ValidationException::withMessages(['voucher_code' => 'Batas penggunaan voucher telah habis.']);
        }$userUsage = $voucher->usages()->where('user_id', $user->id)->where('status', 'applied')->count();
        if ($userUsage >= $voucher->per_user_limit) {
            throw ValidationException::withMessages(['voucher_code' => 'Batas penggunaan pengguna telah habis.']);
        }$discount = $voucher->discount_type === 'flat' ? Money::toMinor($voucher->flat_amount) : Money::basisPoints($subtotalMinor, $voucher->percentage_basis_points);
        if ($voucher->maximum_discount_amount !== null) {
            $discount = min($discount, Money::toMinor($voucher->maximum_discount_amount));
        }$discount = min($discount, $subtotalMinor);

        return new VoucherResult($voucher, $claim, $discount, ['id' => $voucher->id, 'code' => $voucher->code, 'name' => $voucher->name, 'sponsor' => $voucher->mitra_id ? 'mitra' : 'platform', 'discount_type' => $voucher->discount_type, 'flat_amount' => $voucher->flat_amount, 'percentage_basis_points' => $voucher->percentage_basis_points, 'maximum_discount_amount' => $voucher->maximum_discount_amount, 'discount_amount' => Money::fromMinor($discount)]);
    }

    private function findLocked(string $code, ?string $mitraId): Voucher
    {
        $voucher = Voucher::whereRaw('UPPER(code)=?', [strtoupper($code)])->where(fn ($q) => $q->whereNull('mitra_id')->when($mitraId, fn ($x) => $x->orWhere('mitra_id', $mitraId)))->orderByRaw('mitra_id IS NULL ASC')->lockForUpdate()->first();
        if (! $voucher) {
            throw ValidationException::withMessages(['voucher_code' => 'Voucher tidak ditemukan.']);
        }

return $voucher;
    }

    private function assertActive(Voucher $voucher): void
    {
        if ($voucher->status->value !== 'active' || $voucher->starts_at->isFuture() || $voucher->ends_at->isPast()) {
            throw ValidationException::withMessages(['voucher_code' => 'Voucher tidak aktif atau kedaluwarsa.']);
        }
    }
}
