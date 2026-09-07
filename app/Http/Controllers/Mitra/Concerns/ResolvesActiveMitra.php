<?php

namespace App\Http\Controllers\Mitra\Concerns;

use App\Models\Mitra;
use Illuminate\Http\Request;

trait ResolvesActiveMitra
{
    private function activeMitra(Request $request): Mitra
    {
        return Mitra::query()->findOrFail($request->session()->get('active_mitra_id'));
    }

    private function ensureKycVerified(Mitra $mitra): ?\Illuminate\Http\RedirectResponse
    {
        if (! $mitra->isKycVerified()) {
            return redirect()->route('mitra.kyc.index')->with('error', 'Dokumen legalitas (KYC) Anda belum diunggah atau belum diverifikasi oleh Admin. Silakan lengkapi dan tunggu verifikasi KYC untuk mulai menambahkan layanan/destinasi.');
        }

        return null;
    }
}
