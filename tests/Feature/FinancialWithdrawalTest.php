<?php

namespace Tests\Feature;

use App\Actions\Withdrawals\SubmitWithdrawal;
use App\Actions\Withdrawals\TransitionWithdrawal;
use App\Models\LedgerJournal;
use App\Models\Mitra;
use App\Models\MitraBalance;
use App\Models\MitraBankAccount;
use App\Models\User;
use Database\Seeders\FoundationReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class FinancialWithdrawalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FoundationReferenceSeeder::class);
        setPermissionsTeamId(null);
    }

    public function test_full_withdrawal_flow_keeps_every_journal_balanced_and_records_transfer(): void
    {
        [$owner, $mitra, $bank] = $this->tenant('1000000.00');
        $claim = app(SubmitWithdrawal::class)->execute($mitra, $owner, $bank, '400000.00', 'withdraw-key-001');
        $this->assertBalance($mitra, '600000.00', '400000.00');

        $admin = User::factory()->create();
        $transition = app(TransitionWithdrawal::class);
        $transition->execute($claim, 'review', $admin);
        $transition->execute($claim->fresh(), 'approve', $admin);
        $transition->execute($claim->fresh(), 'processing', $admin);
        $this->assertBalance($mitra, '600000.00', '0.00');
        $transition->execute($claim->fresh(), 'paid', $admin, ['transfer_reference' => 'BANK-TRX-001', 'transferred_at' => now(), 'notes' => 'Transfer manual tervalidasi']);

        $this->assertSame('paid', $claim->fresh()->status->value);
        $this->assertDatabaseHas('withdrawal_transfers', ['withdrawal_claim_id' => $claim->id, 'transfer_reference' => 'BANK-TRX-001', 'amount' => '400000.00']);
        $this->assertDatabaseCount('ledger_journals', 3);
        LedgerJournal::with('lines')->get()->each(function ($journal) {
            $this->assertSame((string) $journal->lines->sum('debit_amount'), (string) $journal->lines->sum('credit_amount'));
        });
        $this->assertDatabaseHas('ledger_accounts', ['mitra_id' => $mitra->id, 'account_type' => 'mitra_available']);
        $this->assertDatabaseHas('ledger_accounts', ['mitra_id' => $mitra->id, 'account_type' => 'mitra_held']);
        $this->assertDatabaseHas('ledger_accounts', ['mitra_id' => $mitra->id, 'account_type' => 'withdrawal_payable']);
        $this->assertDatabaseHas('ledger_accounts', ['system_code' => 'platform_cash', 'account_type' => 'platform_cash']);
    }

    public function test_rejection_returns_held_funds_and_is_fully_audited(): void
    {
        [$owner, $mitra, $bank] = $this->tenant('500000.00');
        $claim = app(SubmitWithdrawal::class)->execute($mitra, $owner, $bank, '300000.00', 'withdraw-key-002');
        $admin = User::factory()->create();
        app(TransitionWithdrawal::class)->execute($claim, 'review', $admin);
        app(TransitionWithdrawal::class)->execute($claim->fresh(), 'reject', $admin, ['reason' => 'Dokumen transfer belum sesuai']);

        $this->assertBalance($mitra, '500000.00', '0.00');
        $this->assertSame('rejected', $claim->fresh()->status->value);
        foreach (['withdrawal.submitted', 'withdrawal.review', 'withdrawal.reject'] as $event) $this->assertDatabaseHas('audit_logs', ['auditable_id' => $claim->id, 'event' => $event]);
        $this->assertDatabaseHas('notifications', ['user_id' => $owner->id, 'type' => 'withdrawal.rejected']);
    }

    public function test_owner_can_cancel_before_processing_but_not_after_processing(): void
    {
        [$owner, $mitra, $bank] = $this->tenant('500000.00');
        $claim = app(SubmitWithdrawal::class)->execute($mitra, $owner, $bank, '200000.00', 'withdraw-key-003');
        app(TransitionWithdrawal::class)->execute($claim, 'cancel', $owner, ['reason' => 'Nominal perlu diperbaiki']);
        $this->assertBalance($mitra, '500000.00', '0.00');
        $this->assertSame('cancelled', $claim->fresh()->status->value);

        $second = app(SubmitWithdrawal::class)->execute($mitra, $owner, $bank, '200000.00', 'withdraw-key-004');
        $admin = User::factory()->create();
        app(TransitionWithdrawal::class)->execute($second, 'review', $admin);
        app(TransitionWithdrawal::class)->execute($second->fresh(), 'approve', $admin);
        app(TransitionWithdrawal::class)->execute($second->fresh(), 'processing', $admin);
        $this->expectException(ValidationException::class);
        app(TransitionWithdrawal::class)->execute($second->fresh(), 'cancel', $owner, ['reason' => 'Terlambat dibatalkan']);
    }

    public function test_idempotent_replay_and_competing_withdrawal_cannot_overdraw_balance(): void
    {
        [$owner, $mitra, $bank] = $this->tenant('500000.00');
        $first = app(SubmitWithdrawal::class)->execute($mitra, $owner, $bank, '400000.00', 'same-request-key');
        $replay = app(SubmitWithdrawal::class)->execute($mitra, $owner, $bank, '400000.00', 'same-request-key');
        $this->assertSame($first->id, $replay->id);
        $this->assertDatabaseCount('withdrawal_claims', 1);
        $this->assertBalance($mitra, '100000.00', '400000.00');

        try {
            app(SubmitWithdrawal::class)->execute($mitra, $owner, $bank, '200000.00', 'competing-request-key');
            $this->fail('Permintaan bersaing tidak boleh membuat saldo negatif.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('withdrawal_claims', 1);
            $this->assertBalance($mitra, '100000.00', '400000.00');
        }
    }

    public function test_unverified_and_cross_mitra_bank_accounts_are_rejected(): void
    {
        [$owner, $mitra, $bank] = $this->tenant('500000.00');
        $bank->update(['status' => 'pending', 'verified_at' => null]);
        try {
            app(SubmitWithdrawal::class)->execute($mitra, $owner, $bank, '100000.00', 'unverified-key');
            $this->fail('Rekening belum diverifikasi harus ditolak.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('withdrawal_claims', 0);
        }

        [, , $otherBank] = $this->tenant('500000.00');
        $this->expectException(ValidationException::class);
        app(SubmitWithdrawal::class)->execute($mitra, $owner, $otherBank, '100000.00', 'cross-mitra-key');
    }

    public function test_staff_requires_explicit_withdrawal_permission(): void
    {
        [$owner, $mitra, $bank] = $this->tenant('500000.00');
        $staff = User::factory()->create();
        $mitra->members()->create(['user_id' => $staff->id, 'status' => 'active', 'joined_at' => now()]);
        setPermissionsTeamId($mitra->id);
        $staff->assignRole('mitra-staff');
        setPermissionsTeamId(null);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $payload = ['bank_account_id' => $bank->id, 'amount' => '100000', 'idempotency_key' => 'staff-request-001'];
        $this->actingAs($staff)->withSession(['active_mitra_id' => $mitra->id])->post(route('mitra.withdrawals.store'), $payload)->assertForbidden();

        setPermissionsTeamId($mitra->id);
        $staff->givePermissionTo('withdrawals.submit');
        setPermissionsTeamId(null);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->actingAs($staff)->withSession(['active_mitra_id' => $mitra->id])->post(route('mitra.withdrawals.store'), $payload)->assertRedirect();
        $this->assertDatabaseHas('withdrawal_claims', ['mitra_id' => $mitra->id, 'submitted_by' => $staff->id]);
    }

    private function tenant(string $available): array
    {
        $owner = User::factory()->create();
        $mitra = Mitra::factory()->for($owner, 'owner')->create(['status' => 'active', 'approved_at' => now()]);
        $mitra->members()->create(['user_id' => $owner->id, 'status' => 'active', 'joined_at' => now()]);
        setPermissionsTeamId($mitra->id);
        $owner->assignRole('mitra-owner');
        setPermissionsTeamId(null);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $bank = MitraBankAccount::create(['mitra_id' => $mitra->id, 'bank_code' => 'BCA', 'account_name_encrypted' => 'Owner Test', 'account_number_encrypted' => '1234567890', 'account_fingerprint' => hash('sha256', $mitra->id), 'status' => 'verified', 'is_primary' => true, 'verified_by' => $owner->id, 'verified_at' => now()]);
        MitraBalance::create(['mitra_id' => $mitra->id, 'currency' => 'IDR', 'available_amount' => $available, 'held_amount' => '0.00', 'total_earned_amount' => $available, 'updated_at' => now()]);
        return [$owner, $mitra, $bank];
    }

    private function assertBalance(Mitra $mitra, string $available, string $held): void
    {
        $balance = MitraBalance::findOrFail($mitra->id);
        $this->assertSame($available, $balance->available_amount);
        $this->assertSame($held, $balance->held_amount);
        $this->assertGreaterThanOrEqual(0, (float) $balance->available_amount);
        $this->assertGreaterThanOrEqual(0, (float) $balance->held_amount);
    }
}
