<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Mitra;
use App\Models\MitraKycDocument;
use App\Models\Region;
use App\Models\ServiceType;
use App\Models\User;
use App\Services\MitraMediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MitraRegistrationController extends Controller
{
    public const BANKS = [
        'BCA' => 'Bank Central Asia (BCA)',
        'MANDIRI' => 'Bank Mandiri',
        'BNI' => 'Bank Negara Indonesia (BNI)',
        'BRI' => 'Bank Rakyat Indonesia (BRI)',
        'BSI' => 'Bank Syariah Indonesia (BSI)',
        'JATENG' => 'Bank Jateng',
        'CIMB' => 'CIMB Niaga',
        'PERMATA' => 'Bank Permata',
        'DANAMON' => 'Bank Danamon',
        'BTN' => 'Bank Tabungan Negara (BTN)',
    ];

    public function create(): View
    {
        $serviceTypes = ServiceType::with(['categories' => function ($q) {
            $q->where('is_active', true)->orderBy('name');
        }])->orderBy('sort_order')->get();

        $regions = Region::orderBy('name')->get();
        $banks = self::BANKS;

        return view('public.mitra-register', compact('serviceTypes', 'regions', 'banks'));
    }

    public function store(Request $request, MitraMediaStorage $mediaStorage): RedirectResponse
    {
        $validated = $request->validate([
            // 1. Data Akun & Penanggung Jawab
            'owner_name' => ['required', 'string', 'max:120'],
            'owner_nik' => ['required', 'digits:16'],
            'owner_phone' => ['required', 'string', 'min:10', 'max:16', 'unique:users,phone'],
            'owner_email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'ktp_file' => ['required', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:3072'],

            // 2. Data Usaha/Mitra
            'display_name' => ['required', 'string', 'max:150'],
            'legal_name' => ['nullable', 'string', 'max:150'],
            'service_type_id' => ['required', 'exists:service_types,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:2000'],
            'region_id' => ['required', 'exists:regions,id'],
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'founded_year' => ['nullable', 'integer', 'min:1900', 'max:' . (int) date('Y')],

            // 3. Legalitas
            'nib' => ['nullable', 'string', 'max:50'],
            'npwp' => ['nullable', 'string', 'max:50'],
            'business_license_file' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'],
            'situ_file' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'],

            // 4. Dokumen Pendukung / Verifikasi
            'location_photos' => ['required', 'array', 'min:2', 'max:6'],
            'location_photos.*' => ['required', 'file', 'mimes:jpeg,jpg,png,webp', 'max:3072'],
            'product_photos' => ['nullable', 'array', 'max:8'],
            'product_photos.*' => ['file', 'mimes:jpeg,jpg,png,webp', 'max:3072'],
            'asset_ownership_file' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'],

            // 5. Data Rekening & Pembayaran
            'bank_code' => ['required', 'string', 'max:30'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_name' => ['required', 'string', 'max:150'],

            // 6. Persetujuan
            'agree_truth' => ['accepted'],
            'agree_terms' => ['accepted'],
            'agree_commission' => ['accepted'],
        ], [
            'owner_name.required' => 'Nama lengkap penanggung jawab wajib diisi.',
            'owner_nik.required' => 'NIK wajib diisi untuk verifikasi identitas.',
            'owner_nik.digits' => 'NIK harus berjumlah 16 digit angka.',
            'owner_phone.required' => 'Nomor HP / WhatsApp aktif wajib diisi.',
            'owner_phone.unique' => 'Nomor HP / WhatsApp ini sudah terdaftar. Gunakan nomor lain.',
            'owner_email.required' => 'Alamat email wajib diisi.',
            'owner_email.unique' => 'Email ini sudah terdaftar. Gunakan email lain.',
            'password.required' => 'Kata sandi akun wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'ktp_file.required' => 'Foto KTP penanggung jawab wajib diunggah.',
            'display_name.required' => 'Nama usaha / bisnis wajib diisi.',
            'service_type_id.required' => 'Silakan pilih jenis layanan usaha Anda.',
            'description.required' => 'Deskripsi singkat profil usaha wajib diisi.',
            'region_id.required' => 'Silakan pilih wilayah / kecamatan di Tegal.',
            'address.required' => 'Alamat lengkap tempat usaha wajib diisi.',
            'location_photos.required' => 'Wajib mengunggah minimal 2 foto lokasi usaha (tampak depan).',
            'location_photos.min' => 'Wajib mengunggah minimal 2 foto lokasi usaha (tampak depan).',
            'bank_code.required' => 'Silakan pilih bank untuk penerimaan dana.',
            'account_number.required' => 'Nomor rekening bank wajib diisi.',
            'account_name.required' => 'Nama pemilik rekening bank wajib diisi.',
            'agree_truth.accepted' => 'Anda harus menyatakan bahwa data yang diisi adalah benar.',
            'agree_terms.accepted' => 'Anda harus menyetujui Syarat & Ketentuan Kemitraan.',
            'agree_commission.accepted' => 'Anda harus menyetujui kebijakan komisi platform.',
        ]);

        $mitra = DB::transaction(function () use ($validated, $request, $mediaStorage) {
            // 1. Buat User Owner
            $user = User::create([
                'name' => $validated['owner_name'],
                'email' => $validated['owner_email'],
                'phone' => $validated['owner_phone'],
                'status' => 'active',
            ]);

            $user->credential()->create([
                'password_hash' => Hash::make($validated['password']),
            ]);

            $user->profile()->create([
                'notification_preferences' => [],
            ]);

            // 2. Buat Slug Unik
            $baseSlug = Str::slug($validated['display_name']);
            $slug = $baseSlug;
            $counter = 1;
            while (Mitra::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . (++$counter);
            }

            // 3. Buat Mitra Record (Status: pending)
            $mitra = Mitra::create([
                'owner_user_id' => $user->id,
                'owner_nik_encrypted' => $validated['owner_nik'],
                'category' => 'non_dinas',
                'service_type_id' => $validated['service_type_id'],
                'category_id' => $validated['category_id'] ?? null,
                'legal_name' => $validated['legal_name'] ?: $validated['display_name'],
                'display_name' => $validated['display_name'],
                'slug' => $slug,
                'status' => 'pending',
                'is_verified' => false,
                'description' => $validated['description'],
                'contact_email' => $validated['owner_email'],
                'contact_phone' => $validated['owner_phone'],
                'region_id' => $validated['region_id'],
                'address' => $validated['address'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'founded_year' => $validated['founded_year'] ?? null,
                'nib' => $validated['nib'] ?? null,
                'npwp' => $validated['npwp'] ?? null,
                'tax_number_encrypted' => $validated['npwp'] ?? null,
            ]);

            // 4. Upload & Simpan Foto KTP (Private KYC)
            if ($request->hasFile('ktp_file')) {
                $ktpAsset = $mediaStorage->store($mitra, $request->file('ktp_file'), 'ktp', true);
                MitraKycDocument::create([
                    'mitra_id' => $mitra->id,
                    'media_asset_id' => $ktpAsset->id,
                    'document_type' => 'ktp',
                    'version' => 1,
                    'document_number_encrypted' => $validated['owner_nik'],
                    'document_fingerprint' => hash('sha256', 'ktp:' . $validated['owner_nik']),
                    'status' => 'pending',
                    'submitted_by' => $user->id,
                ]);
            }

            // 5. Upload Dokumen Legalitas Tambahan (Jika Ada)
            if ($request->hasFile('business_license_file')) {
                $licenseAsset = $mediaStorage->store($mitra, $request->file('business_license_file'), 'business_license', true);
                MitraKycDocument::create([
                    'mitra_id' => $mitra->id,
                    'media_asset_id' => $licenseAsset->id,
                    'document_type' => 'business_license',
                    'version' => 1,
                    'document_number_encrypted' => $validated['nib'] ?? null,
                    'status' => 'pending',
                    'submitted_by' => $user->id,
                ]);
            }

            if ($request->hasFile('situ_file')) {
                $situAsset = $mediaStorage->store($mitra, $request->file('situ_file'), 'situ', true);
                MitraKycDocument::create([
                    'mitra_id' => $mitra->id,
                    'media_asset_id' => $situAsset->id,
                    'document_type' => 'situ',
                    'version' => 1,
                    'status' => 'pending',
                    'submitted_by' => $user->id,
                ]);
            }

            // 6. Upload Dokumen Kepemilikan Aset (Jika Ada)
            if ($request->hasFile('asset_ownership_file')) {
                $assetDoc = $mediaStorage->store($mitra, $request->file('asset_ownership_file'), 'asset_ownership', true);
                MitraKycDocument::create([
                    'mitra_id' => $mitra->id,
                    'media_asset_id' => $assetDoc->id,
                    'document_type' => 'asset_ownership',
                    'version' => 1,
                    'status' => 'pending',
                    'submitted_by' => $user->id,
                ]);
            }

            // 7. Upload Foto Lokasi Usaha (Minimal 2-3 Foto, Public Gallery)
            if ($request->hasFile('location_photos')) {
                foreach ($request->file('location_photos') as $idx => $photo) {
                    $mediaStorage->store($mitra, $photo, 'business_location', false);
                }
            }

            // 8. Upload Foto Produk / Layanan (Jika Ada, Public Gallery)
            if ($request->hasFile('product_photos')) {
                foreach ($request->file('product_photos') as $photo) {
                    $mediaStorage->store($mitra, $photo, 'business_product', false);
                }
            }

            // 9. Simpan Data Rekening Bank
            $mitra->bankAccounts()->create([
                'bank_code' => $validated['bank_code'],
                'account_name_encrypted' => $validated['account_name'],
                'account_number_encrypted' => $validated['account_number'],
                'account_fingerprint' => hash('sha256', $validated['bank_code'] . ':' . $validated['account_number']),
                'status' => 'pending',
                'is_primary' => true,
            ]);

            // 10. Tambahkan Owner ke Keanggotaan Mitra
            $mitra->members()->create([
                'user_id' => $user->id,
                'status' => 'active',
                'joined_at' => now(),
            ]);

            // 11. Assign Role mitra-owner
            setPermissionsTeamId($mitra->id);
            $user->assignRole('mitra-owner');
            setPermissionsTeamId(null);

            // Login user secara otomatis
            Auth::login($user);

            return $mitra;
        });

        return redirect()->route('mitra.register.success', ['mitra' => $mitra->id]);
    }

    public function success(Request $request): View
    {
        $mitra = null;
        if ($request->filled('mitra')) {
            $mitra = Mitra::with(['owner', 'serviceType', 'categoryModel', 'region'])->find($request->query('mitra'));
        }

        return view('public.mitra-register-success', compact('mitra'));
    }

    public function pendingNotice(): View
    {
        $user = Auth::user();
        $mitra = $user?->ownedMitras()->latest()->first();

        return view('mitra.pending-notice', compact('mitra', 'user'));
    }
}
