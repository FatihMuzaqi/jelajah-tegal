<?php

namespace Database\Seeders;

use App\Models\CatalogEntity;
use App\Models\CatalogOffer;
use App\Models\Category;
use App\Models\GatekeeperAssignment;
use App\Models\Mitra;
use App\Models\MitraFeature;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Region;
use App\Models\ServiceType;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DinasSeeder extends Seeder
{
    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Roles & Permissions Dinas
        $dinasPermissions = [
            'access.dinas',
            'dinas.analytics.view',
            'dinas.reports.export',
            'dinas.visitors.monitor',
            'orders.view',
            'tickets.validate',
            'profile.update',
            'notifications.view',
        ];

        foreach ($dinasPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $dinasRole = Role::firstOrCreate(['name' => 'dinas-supervisor', 'guard_name' => 'web']);
        $dinasRole->syncPermissions($dinasPermissions);

        // Also give super-admin and admin access.dinas
        if ($superAdminRole = Role::where('name', 'super-admin')->first()) {
            $superAdminRole->givePermissionTo('access.dinas');
        }
        if ($adminRole = Role::where('name', 'admin')->first()) {
            $adminRole->givePermissionTo('access.dinas');
        }

        // 2. Akun Supervisor Dinas Pemda
        setPermissionsTeamId(null);
        $dinasUser = User::updateOrCreate(
            ['email' => 'supervisor.dinas@tegalkab.go.id'],
            [
                'name' => 'Dr. H. Ahmad Rasyid, M.Si (Kepala Bidang Pariwisata)',
                'status' => 'active',
                'email_verified_at' => now(),
                'phone' => '081223344556',
            ]
        );

        $dinasUser->credential()->updateOrCreate(
            ['user_id' => $dinasUser->id],
            ['password_hash' => Hash::make('password')]
        );

        $dinasUser->profile()->updateOrCreate(
            ['user_id' => $dinasUser->id],
            ['notification_preferences' => ['in_app' => true, 'email' => true]]
        );

        $dinasUser->syncRoles(['dinas-supervisor']);

        // Consumer & Gatekeeper user helpers
        $consumer = User::where('email', 'consumer@example.test')->first() ?? $dinasUser;
        $gatekeeperUser = User::where('email', 'gatekeeper@example.test')->first() ?? $dinasUser;
        $region = Region::first();
        $tourismType = ServiceType::where('code', 'tourism')->first() ?? ServiceType::first();
        $category = Category::where('service_type_id', $tourismType?->id)->first() ?? Category::first();

        // 3. Mitra-Mitra Kategori Dinas dengan Akun Pengelola Khusus Masing-Masing
        $dinasMitrasData = [
            [
                'slug' => 'uptd-wisata-guci-tegal',
                'legal_name' => 'UPTD Pengelolaan Objek Wisata Guci & Cacaban',
                'display_name' => 'Taman Wisata Air Panas Guci',
                'contact_email' => 'pengelola.guci@tegalkab.go.id',
                'contact_name' => 'Pengelola UPTD Wisata Guci',
                'contact_phone' => '0283-911223',
                'address' => 'Jl. Objek Wisata Guci, Bumijawa, Kabupaten Tegal',
                'description' => 'Destinasi wisata pemandian air panas alami di lereng Gunung Slamet yang dikelola resmi oleh Dinas Kepemudaan, Olahraga dan Pariwisata Pemkab Tegal.',
                'ticket_price' => 25000,
            ],
            [
                'slug' => 'uptd-pantai-alam-indah',
                'legal_name' => 'UPTD Balai Wisata Bahari Kota Tegal',
                'display_name' => 'Pantai Alam Indah (PAI) Tegal',
                'contact_email' => 'pengelola.pai@tegalkota.go.id',
                'contact_name' => 'Bambang Sudrajat (Pengelola PAI)',
                'contact_phone' => '0283-356789',
                'address' => 'Jl. Sangir No. 1, Mintaragen, Kota Tegal',
                'description' => 'Destinasi wisata bahari pesisir pantai utara dengan fasilitas anjungan dan museum bahari milik Dinas Pariwisata Pemkot Tegal.',
                'ticket_price' => 15000,
            ],
            [
                'slug' => 'uptd-waduk-cacaban',
                'legal_name' => 'UPTD Kawasan Konservasi & Wisata Waduk Cacaban',
                'display_name' => 'Wisata Waduk Cacaban',
                'contact_email' => 'pengelola.cacaban@tegalkab.go.id',
                'contact_name' => 'Pengelola Wisata Waduk Cacaban',
                'contact_phone' => '0283-619888',
                'address' => 'Desa Penujah, Kedungbanteng, Kabupaten Tegal',
                'description' => 'Objek wisata waduk buatan dengan panorama perbukitan asri, pulau terapung, dan dermaga wisata perahu milik Pemkab Tegal.',
                'ticket_price' => 20000,
            ],
        ];

        $allServiceTypes = ServiceType::all();

        foreach ($dinasMitrasData as $data) {
            // 3.1 Akun Pengelola Mandiri Mitra
            $manager = User::updateOrCreate(
                ['email' => $data['contact_email']],
                [
                    'name' => $data['contact_name'],
                    'phone' => $data['contact_phone'],
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            $manager->credential()->updateOrCreate(
                ['user_id' => $manager->id],
                ['password_hash' => Hash::make('password')]
            );

            $manager->profile()->updateOrCreate(
                ['user_id' => $manager->id],
                ['notification_preferences' => ['in_app' => true, 'email' => true]]
            );

            $mitra = Mitra::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category' => 'dinas',
                    'owner_user_id' => $manager->id,
                    'region_id' => $region?->id,
                    'legal_name' => $data['legal_name'],
                    'display_name' => $data['display_name'],
                    'status' => 'active',
                    'approved_at' => now(),
                    'contact_email' => $data['contact_email'],
                    'contact_phone' => $data['contact_phone'],
                    'address' => $data['address'],
                    'description' => $data['description'],
                ]
            );

            // Bersihkan membership lama agar user langsung menuju dashboard mitranya sendiri
            $manager->mitraMemberships()->where('mitra_id', '!=', $mitra->id)->delete();
            $mitra->members()->updateOrCreate(['user_id' => $manager->id], ['status' => 'active', 'joined_at' => now()]);

            setPermissionsTeamId($mitra->id);
            $manager->syncRoles(['mitra-owner']);
            setPermissionsTeamId(null);

            // Aktifkan seluruh modul fitur (Wisata, Penginapan, Kuliner, Event, Rental)
            foreach ($allServiceTypes as $st) {
                MitraFeature::updateOrCreate(
                    ['mitra_id' => $mitra->id, 'service_type_id' => $st->id],
                    ['status' => 'enabled', 'enabled_at' => now()]
                );
            }

            $gkMember = $mitra->members()->updateOrCreate(
                ['user_id' => $gatekeeperUser->id],
                ['status' => 'active', 'joined_at' => now()]
            );

            GatekeeperAssignment::updateOrCreate(
                ['mitra_id' => $mitra->id, 'member_id' => $gkMember->id],
                [
                    'scope_type' => 'mitra',
                    'valid_from' => now()->subMonth(),
                    'valid_until' => now()->addYear(),
                    'assigned_by' => $dinasUser->id,
                    'revoked_at' => null,
                ]
            );

            // Catalog Entity & Offer
            $entity = CatalogEntity::updateOrCreate(
                ['mitra_id' => $mitra->id, 'slug' => $data['slug']],
                [
                    'service_type_id' => $tourismType->id,
                    'category_id' => $category?->id,
                    'region_id' => $region?->id,
                    'name' => $data['display_name'],
                    'description' => $data['description'],
                    'address' => $data['address'],
                    'status' => 'published',
                    'is_featured' => true,
                    'rating_average' => 4.8,
                    'rating_count' => 50,
                    'published_at' => now(),
                ]
            );

            $offer = CatalogOffer::updateOrCreate(
                ['mitra_id' => $mitra->id, 'catalog_entity_id' => $entity->id, 'sku' => 'SKU-' . strtoupper(substr($mitra->slug, 0, 8))],
                [
                    'offer_type' => 'ticket_tier',
                    'name' => 'Tiket Masuk ' . $data['display_name'],
                    'currency' => 'IDR',
                    'price' => $data['ticket_price'],
                    'status' => 'active',
                    'min_quantity' => 1,
                    'max_quantity' => 10,
                ]
            );

            // 4. Sample Ticket Orders & Tickets for PAD
            if (Ticket::where('mitra_id', $mitra->id)->count() < 10) {
                for ($day = 1; $day <= 10; $day++) {
                    $orderDate = now()->subDays($day * 2);
                    $qty = ($day % 3) + 1;
                    $total = $data['ticket_price'] * $qty;

                    $order = Order::create([
                        'order_number' => 'ORD-PAD-' . substr($mitra->id, 0, 8) . '-' . $day . '-' . rand(1000, 9999),
                        'mitra_id' => $mitra->id,
                        'user_id' => $consumer->id,
                        'subtotal' => $total,
                        'admin_fee' => 0,
                        'discount_amount' => 0,
                        'total_amount' => $total,
                        'commission_basis_points' => 0,
                        'commission_amount' => 0,
                        'mitra_net_amount' => $total,
                        'status' => \App\Enums\OrderStatus::Paid,
                        'payment_status' => \App\Enums\PaymentStatus::Paid,
                        'placed_at' => $orderDate,
                        'paid_at' => $orderDate,
                        'user_snapshot' => [
                            'name' => $consumer->name,
                            'email' => $consumer->email,
                        ],
                        'mitra_snapshot' => [
                            'name' => $mitra->display_name,
                            'legal_name' => $mitra->legal_name,
                        ],
                        'created_at' => $orderDate,
                    ]);

                    $orderItem = OrderItem::create([
                        'order_id' => $order->id,
                        'mitra_id' => $mitra->id,
                        'catalog_offer_id' => $offer->id,
                        'resource_type' => 'tourism_ticket',
                        'reference_id' => $entity->id,
                        'item_name' => 'Retribusi Masuk ' . $mitra->display_name,
                        'sku' => $offer->sku,
                        'unit_price' => $data['ticket_price'],
                        'quantity' => $qty,
                        'subtotal' => $total,
                        'admin_fee' => 0,
                        'discount_amount' => 0,
                        'line_total' => $total,
                        'fulfillment_status' => 'fulfilled',
                        'booking_date' => $orderDate->toDateString(),
                        'starts_at' => $orderDate,
                        'ends_at' => $orderDate->copy()->addDays(7),
                    ]);

                    for ($k = 1; $k <= $qty; $k++) {
                        $isUsed = ($day % 2 === 0);
                        $ticketCode = 'PAD-' . strtoupper(str()->random(8));
                        Ticket::create([
                            'order_item_id' => $orderItem->id,
                            'mitra_id' => $mitra->id,
                            'holder_user_id' => $consumer->id,
                            'ticket_code' => $ticketCode,
                            'qr_token_hash' => hash('sha256', $ticketCode . '-lokantara-salt'),
                            'token_version' => 1,
                            'status' => $isUsed ? 'used' : 'unused',
                            'used_at' => $isUsed ? $orderDate->copy()->addHours(1) : null,
                            'valid_from' => $orderDate->copy()->startOfDay(),
                            'valid_until' => $orderDate->copy()->addDays(7)->endOfDay(),
                            'created_at' => $orderDate,
                        ]);
                    }
                }
            }
        }
    }
}
