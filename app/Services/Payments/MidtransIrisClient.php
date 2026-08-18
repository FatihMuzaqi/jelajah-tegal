<?php

namespace App\Services\Payments;

use App\Models\WithdrawalClaim;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransIrisClient
{
    public function enabled(): bool
    {
        return (bool) config('midtrans.iris.enabled', false);
    }

    private function apiKey(): string
    {
        $key = config('midtrans.iris.api_key') ?: config('midtrans.server_key');
        if (blank($key)) {
            throw new RuntimeException('Midtrans Iris API Key belum dikonfigurasi.');
        }
        return (string) $key;
    }

    private function baseUrl(): string
    {
        return rtrim((string) (config('midtrans.iris.base_url') ?: 'https://app.sandbox.midtrans.com/iris/api/v1'), '/');
    }

    private function http(): PendingRequest
    {
        return Http::withBasicAuth($this->apiKey(), '')
            ->acceptJson()
            ->timeout((int) config('midtrans.timeout_seconds', 15))
            ->withHeaders([
                'X-Idempotency-Key' => (string) str()->ulid(),
            ]);
    }

    /**
     * Dapatkan daftar bank yang didukung oleh Midtrans Iris
     */
    public function getBeneficiaryBanks(): array
    {
        if (! $this->enabled()) {
            return $this->fallbackBankList();
        }

        try {
            $response = $this->http()->get($this->baseUrl().'/beneficiary_banks');
            if ($response->successful()) {
                return $response->json('beneficiary_banks') ?? $this->fallbackBankList();
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return $this->fallbackBankList();
    }

    /**
     * Daftarkan penerima transfer (Beneficiary) ke sistem Midtrans Iris
     */
    public function createBeneficiary(string $name, string $accountNumber, string $bankCode, ?string $email = null): array
    {
        if (! $this->enabled()) {
            return ['status' => 'mock_created', 'message' => 'Iris sandbox simulation'];
        }

        $response = $this->http()->post($this->baseUrl().'/beneficiaries', [
            'name' => $name,
            'account' => $accountNumber,
            'bank' => strtolower($bankCode),
            'alias_name' => str()->slug($name.'-'.$bankCode),
            'email' => $email ?? 'mitra@lokantara.id',
        ]);

        $response->throw();
        return $response->json();
    }

    /**
     * Buat permintaan payout (penarikan saldo) ke Iris
     */
    public function createPayout(WithdrawalClaim $claim): array
    {
        if (! $this->enabled()) {
            return [
                'status' => 'mock_queued',
                'reference_no' => 'IRIS-MOCK-'.now()->format('ymdHis'),
                'notes' => 'Midtrans Iris simulasi (Sandbox)',
            ];
        }

        $bank = $claim->bankAccount;
        $response = $this->http()->post($this->baseUrl().'/payouts', [
            'payouts' => [
                [
                    'beneficiary_name' => $bank->account_name_encrypted,
                    'beneficiary_account' => $bank->account_number_encrypted,
                    'beneficiary_bank' => strtolower($bank->bank_code),
                    'beneficiary_email' => $claim->mitra->contact_email ?? $claim->submitter->email,
                    'amount' => (string) round((float) $claim->amount),
                    'notes' => 'Payout '.$claim->withdrawal_number.' Lokantara',
                ],
            ],
        ]);

        $response->throw();
        return $response->json();
    }

    /**
     * Cek status transaksi payout di Midtrans Iris
     */
    public function getPayoutStatus(string $referenceNo): array
    {
        if (! $this->enabled()) {
            return ['status' => 'completed', 'reference_no' => $referenceNo];
        }

        $response = $this->http()->get($this->baseUrl().'/payouts/'.$referenceNo);
        $response->throw();
        return $response->json();
    }

    /**
     * Cek saldo akun Midtrans Iris (Akun Agregator Platform)
     */
    public function getIrisBalance(): array
    {
        if (! $this->enabled()) {
            return ['balance' => '0.00', 'status' => 'disabled'];
        }

        $response = $this->http()->get($this->baseUrl().'/balance');
        $response->throw();
        return $response->json();
    }

    /**
     * Daftar bank default Indonesia
     */
    public function fallbackBankList(): array
    {
        return [
            ['code' => 'BCA', 'name' => 'Bank Central Asia (BCA)'],
            ['code' => 'MANDIRI', 'name' => 'Bank Mandiri'],
            ['code' => 'BNI', 'name' => 'Bank Negara Indonesia (BNI)'],
            ['code' => 'BRI', 'name' => 'Bank Rakyat Indonesia (BRI)'],
            ['code' => 'PERMATA', 'name' => 'Bank Permata'],
            ['code' => 'CIMB', 'name' => 'Bank CIMB Niaga'],
            ['code' => 'BSI', 'name' => 'Bank Syariah Indonesia (BSI)'],
            ['code' => 'DANAMON', 'name' => 'Bank Danamon'],
            ['code' => 'JATENG', 'name' => 'Bank Jateng'],
            ['code' => 'JAGO', 'name' => 'Bank Jago'],
            ['code' => 'SEABANK', 'name' => 'SeaBank'],
            ['code' => 'BTPN', 'name' => 'Bank BTPN / Jenius'],
        ];
    }
}
