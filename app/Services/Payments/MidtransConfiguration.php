<?php

namespace App\Services\Payments;

use LogicException;

class MidtransConfiguration
{
    public function enabled(): bool
    {
        return (bool) config('midtrans.enabled');
    }

    public function assertReady(): void
    {
        if (! $this->enabled()) {
            throw new LogicException('Midtrans dinonaktifkan secara eksplisit. Set MIDTRANS_ENABLED=true setelah kredensial tersedia.');
        }
        foreach (['server_key', 'client_key'] as $key) {
            if (blank(config('midtrans.'.$key))) {
                throw new LogicException('Konfigurasi Midtrans wajib tidak lengkap: '.$key.'.');
            }
        }
    }

    public function serverKey(): string
    {
        $this->assertReady();
        return (string) config('midtrans.server_key');
    }

    public function snapBaseUrl(): string
    {
        $this->assertReady();
        return rtrim((string) (config('midtrans.snap_base_url') ?: (config('midtrans.production') ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com')), '/');
    }

    public function apiBaseUrl(): string
    {
        $this->assertReady();
        return rtrim((string) (config('midtrans.api_base_url') ?: (config('midtrans.production') ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com')), '/');
    }
}
