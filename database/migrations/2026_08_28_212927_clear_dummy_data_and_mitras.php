<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete users with only mitra/mitra-staff role (dummy accounts)
        $usersToDelete = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['mitra', 'mitra-staff']);
        })->get();

        foreach ($usersToDelete as $user) {
            if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
                $user->forceDelete();
            }
        }
        
        // Truncate all operational / dummy data tables
        $tablesToTruncate = [
            'accommodations', 'accommodation_rooms', 'accommodation_room_facilities', 'accommodation_room_media', 'availabilities',
            'catalog_entities', 'catalog_facilities', 'catalog_locations', 'catalog_media', 'catalog_offers', 'catalog_operating_hours',
            'culinary_menu_categories', 'culinary_menu_items', 'culinary_reservations', 'culinary_table_slots', 'culinary_venues',
            'events', 'event_schedules', 'event_tickets', 'event_ticket_types', 'event_ticket_validation_logs',
            'favorites', 'gatekeeper_assignments', 'invoices', 'ledger_accounts', 'ledger_journals', 'ledger_lines',
            'mitras', 'mitra_balances', 'mitra_bank_accounts', 'mitra_features', 'mitra_feature_requests', 'mitra_invitations', 'mitra_kyc_documents', 'mitra_members', 'mitra_operating_hours',
            'orders', 'order_items', 'order_reservation_holds', 'payments', 'payment_reconciliations', 'payment_webhook_events',
            'rental_bookings', 'rental_booking_documents', 'rental_rates', 'rental_vehicles', 'rental_vehicle_availabilities', 'renter_documents',
            'reviews', 'review_replies', 'tickets', 'ticket_validation_logs', 'tourism_destinations', 'tourism_ticket_packages',
            'vouchers', 'voucher_claims', 'voucher_usages', 'withdrawal_claims', 'withdrawal_transfers',
            'audit_logs', 'chatbot_settings', 'notifications'
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tablesToTruncate as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot revert data truncation
    }
};
