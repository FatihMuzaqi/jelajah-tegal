# PANDUAN PENGGUNA (MANUAL BOOK) - JELAJAH TEGAL

Dokumen ini merupakan panduan resmi operasional dan penggunaan platform digital terpadu **Jelajah Tegal** (*Multi-Service Digital Tourism Platform*). Panduan ini disusun untuk memudahkan seluruh pemangku kepentingan—mulai dari masyarakat/wisatawan umum, petugas loket, pengelola unit usaha (Mitra), dinas pemerintah daerah, hingga tim administrator sistem.

---

## 1. INFORMASI UMUM & PRASYARAT SISTEM

### 1.1 Ringkasan Aplikasi
**Jelajah Tegal** adalah ekosistem digital satu pintu (*one-stop tourism platform*) Kabupaten Tegal yang mengintegrasikan lima pilar sektor pariwisata dan ekonomi kreatif:
1. **Destinasi Wisata**: Informasi tiket masuk, wahana rekreasi alam Guci, pantai, situs purbakala Semedo, dan agrowisata.
2. **Penginapan & Hotel**: Reservasi hotel berbintang, resort air panas, villa keluarga, homestay, hingga area glamping.
3. **Sentra Kuliner**: Direktori kuliner legendaris (Sate Kambing Batibul, Tahu Aci Slawi, Teh Poci) dan reservasi meja makan.
4. **Event Budaya**: Jadwal festival seni daerah, karnaval budaya, konser musik, dan pameran UMKM.
5. **Rental Kendaraan**: Penyewaan mobil lepas kunci/dengan supir dan rental motor wisata.

Selain katalog terpadu, Jelajah Tegal dilengkapi dengan fitur mutakhir seperti **Asisten Perencana Perjalanan AI (*AI Trip Navigator*)**, **Peta Rute Wisata Interaktif**, **Virtual Tour 360°**, **Pemesanan E-Tiket Digital berbasis QR Code**, serta **Dukungan Aplikasi Web Progresif (*Progressive Web App - PWA*)** yang responsif di smartphone.

---

### 1.2 Prasyarat Minimum Perangkat & Sistem
Untuk memastikan pengalaman terbaik, pengguna disarankan menggunakan perangkat dengan spesifikasi minimum sebagai berikut:

| Komponen | Spesifikasi Minimum Pengguna (Wisatawan) | Spesifikasi Minimum Pengelola (Mitra / Admin / Dinas) |
| :--- | :--- | :--- |
| **Perangkat** | Smartphone Android (OS 8.0+) / iPhone iOS (13+) / Komputer | Komputer Desktop / Laptop / Tablet |
| **Peramban Web (Browser)** | Google Chrome 100+, Mozilla Firefox 100+, Safari 14+, Microsoft Edge | Google Chrome versi terbaru (Direkomendasikan) |
| **Koneksi Internet** | 4G LTE / Wi-Fi stabil (Kecepatan min. 3 Mbps) | Broadband / Wi-Fi stabil (Kecepatan min. 10 Mbps) |
| **Perangkat Keras Tambahan** | Kamera HP (untuk memindai QR Code jika diperlukan) | Kamera Web / HP (khusus peran *Gatekeeper* pemindai tiket) |
| **Dukungan Layar** | Layar sentuh responsif (360px hingga 4K) | Resolusi layar minimum 1280 x 720 piksel |

---

## 2. DAFTAR PERAN (ROLE) & HAK AKSES

Sistem Jelajah Tegal menerapkan otorisasi berbasis peran terisolasi (*Role-Based Access Control*) untuk menjamin keamanan data dan ketertiban operasional:

| Peran (Role) | Kategori Pengguna | Deskripsi & Wewenang Menu Utama |
| :--- | :--- | :--- |
| **Publik (Guest)** | Wisatawan Umum | • Menjelajahi katalog 5 sektor wisata, kuliner, penginapan, event, rental.<br>• Menggunakan simulasi Asisten AI Perencana Liburan.<br>• Melihat direktori mitra resmi terverifikasi.<br>• Mengakses Virtual Tour 360° dan mengirim saran/kritik.<br>• Mendaftarkan unit usaha baru melalui formulir kemitraan. |
| **Konsumen (Consumer)** | Wisatawan Terdaftar | • Dashboard aktivitas personal & navigasi PWA mobile.<br>• Menyimpan rencana liburan AI (*Itinerary Plan*).<br>• Melakukan pemesanan tiket & kamar hotel dengan pembayaran Midtrans.<br>• Melihat riwayat transaksi & membuka E-Tiket QR Code resmi.<br>• Mengunggah dokumen identitas penyewa rental.<br>• Memberikan ulasan dan rating kepuasan. |
| **Petugas Lapangan (Gatekeeper)** | Petugas Loket Masuk | • Antarmuka pemindai (*Scanner*) QR Code tiket masuk.<br>• Validasi keabsahan tiket pengunjung secara *real-time*.<br>• Melihat status tiket (Valid, Sudah Digunakan, atau Kedaluwarsa). |
| **Pemilik Mitra (Mitra Owner)** | Pengelola Tenant | • Dashboard statistik kunjungan & omzet bisnis unit.<br>• Manajemen katalog layanan (Wisata, Kamar Hotel, Menu, Event, Armada).<br>• Manajemen pesanan masuk & status reservasi pelanggan.<br>• Pembuatan kode voucher promo diskon.<br>• Buku besar keuangan, mutasi saldo, dan pengajuan penarikan dana (*Payout*).<br>• Pengaturan profil usaha, logo brand, jam operasional, dan KYC legalitas.<br>• Manajemen anggota staf tim pengelola. |
| **Staf Mitra (Mitra Staff)** | Operator Tenant | • Mengelola ketersediaan slot kamar, kuota tiket harian, dan menu.<br>• Memantau pesanan masuk dan melayani check-in pelanggan. |
| **Pengawas Dinas (Dinas Supervisor)** | Instansi Pemda (Disporapar) | • Dashboard eksekutif monitoring destinasi aset daerah Pemkab/Pemkot.<br>• Rekapitulasi penjualan tiket retribusi daerah (Pendapatan Asli Daerah - PAD).<br>• Ekspor laporan keuangan real-time (PDF / Excel).<br>• Pengawasan operasional pintu gerbang wisata dinas. |
| **Administrator (Admin)** | Tim Operasional Platform | • Manajemen data pengguna seluruh platform.<br>• Pendaftaran & verifikasi mitra tenant baru (Auto-slug & Undangan Email).<br>• Moderasi penayangan konten produk sebelum terbit ke publik.<br>• Verifikasi dokumen identitas legalitas KYC mitra.<br>• Pemrosesan klaim penarikan dana saldo mitra.<br>• Manajemen master data kategori dan wilayah kecamatan.<br>• Rekapitulasi kotak aspirasi, saran, dan kritik masyarakat.<br>• Pemantauan log audit aktivitas sistem. |
| **Super Administrator** | Administrator Tertinggi Sistem | • Akses menyeluruh ke seluruh modul dan antarmuka peran (*Surface Switcher*).<br>• Manajemen akun Administrator & Staf Utama.<br>• Konfigurasi sistem hak akses & matriks izin (*Roles & Permissions Matrix*).<br>• Pengaturan sakelar fitur (*Feature Flags*) secara fleksibel.<br>• Konfigurasi global platform dan integrasi kecerdasan buatan (*AI Chatbot*). |

---

## 3. PANDUAN AKSES & AUTENTIKASI

### 3.1 Pendaftaran Akun Wisatawan Baru (Registrasi)
Bagi wisatawan yang belum memiliki akun Jelajah Tegal:
1. Akses halaman utama **Jelajah Tegal** di browser Anda.
2. Klik tombol **Daftar** pada bilah navigasi atas (atau menu navigasi bawah pada ponsel).
3. Masukkan **Nama Lengkap**, **Alamat Email**, dan buat **Kata Sandi** (minimal 8 karakter).
4. Masukkan ulang kata sandi pada kolom **Konfirmasi Kata Sandi**.
5. Centang persetujuan terhadap *Syarat dan Ketentuan* serta *Kebijakan Privasi*.
6. Klik tombol **Daftar Akun**.
7. Sistem akan mengirimkan email konfirmasi. Buka kotak masuk email Anda dan klik tautan verifikasi untuk mengaktifkan akun.

> [Screenshot: Formulir pendaftaran akun baru dengan tombol 'Daftar Akun' dan opsi 'Lanjutkan dengan Google']

---

### 3.2 Masuk ke Akun (Login)
1. Buka tautan portal Jelajah Tegal dan klik tombol **Masuk**.
2. Masukkan alamat email terdaftar dan kata sandi Anda.
3. Centang opsi **Ingat Saya** jika menggunakan perangkat pribadi.
4. Klik tombol **Masuk ke Akun**.
5. **Alternatif Cepat**: Anda dapat langsung menekan tombol **Lanjutkan dengan Akun Google** untuk masuk secara instan tanpa perlu mengetik kata sandi manual.

> [Screenshot: Halaman login modern Jelajah Tegal dengan input email, password, tombol masuk hijau emerald, dan tombol Google OAuth]

---

### 3.3 Pemulihan & Reset Kata Sandi (Lupa Password)
Jika Anda lupa kata sandi akun:
1. Pada halaman login, klik tautan **Lupa Kata Sandi?**.
2. Masukkan alamat email akun Anda yang terdaftar pada sistem.
3. Klik tombol **Kirim Tautan Pemulihan**.
4. Buka email dari **Jelajah Tegal**, lalu klik tombol **Setel Kata Sandi Baru**.
5. Masukkan kata sandi baru Anda (minimal 8 karakter) dan simpan.
6. Anda dapat langsung masuk kembali menggunakan kata sandi baru tersebut.

> [Screenshot: Tampilan halaman permintaan reset password dengan input email dan tombol konfirmasi]

---

### 3.4 Aktivasi Akun Pengelola Mitra Baru (Tenant Activation)
Bagi pemilik/penanggung jawab unit usaha yang didaftarkan oleh Administrator:
1. Anda akan menerima pesan resmi di kotak masuk email Anda berjudul: **`Undangan Aktivasi Akun Pengelola Mitra — Jelajah Tegal`**.
2. Buka email tersebut, lalu periksa rincian nama mitra usaha yang tertera.
3. Klik tombol utama **`AKTIFKAN AKUN MITRA SEKARANG →`** (Tautan berlaku selama 72 jam).
4. Anda akan diarahkan ke halaman resmi **Aktivasi Akses Mitra Jelajah Tegal**.
5. Masukkan kata sandi baru yang kuat pada kolom **Buat Kata Sandi Baru** dan ketik ulang pada **Konfirmasi Kata Sandi**.
6. Klik tombol **Aktifkan Akun & Masuk ke Dashboard**.
7. Akun Anda seketika aktif dan langsung masuk ke halaman operasional tenant bisnis Anda.

> [Screenshot: Tampilan email undangan kemitraan dengan tombol aktivasi hijau dan halaman form aktivasi kata sandi mitra]

---

### 3.5 Beralih Tampilan Antarmuka (Surface Switcher)
Bagi akun yang memiliki multi-peran (misalnya seorang Admin yang juga memiliki Mitra, atau Super Admin):
1. Klik foto profil Anda di pojok kanan atas bilah navigasi (*Topbar*).
2. Pilih menu **Ganti Surface / Peran**.
3. Klik kartu antarmuka yang diinginkan (misal: *Super Admin, Administrator, Mitra Pengelola, Dinas Pemda, atau Wisatawan*).
4. Sistem akan langsung mengalihkan sesi kerja Anda sesuai lingkungan kerja yang dipilih.

> [Screenshot: Grid modal pemilihan surface dengan kartu-kartu peran kerja yang tersedia]

---

### 3.6 Keluar dari Sistem (Logout)
1. Klik foto profil atau nama pengguna Anda di sudut kanan atas layar.
2. Klik opsi **Keluar (Logout)**.
3. Sistem akan menghapus sesi login aktif Anda secara aman dan mengembalikan Anda ke halaman utama.

---

## 4. PANDUAN FITUR UTAMA (LANGKAH DEMI LANGKAH)

---

### MODUL A: PORTAL PUBLIK & WISATAWAN (CONSUMER)

#### 1. Beranda & Pencarian Pintar (Home & Smart Search)
* **Fungsi**: Titik awal eksplorasi wisata, rekomendasi populer, kategori layanan, dan event terkini.
* **Langkah Penggunaan**:
  1. Buka alamat website utama Jelajah Tegal.
  2. Gunakan bilah pencarian (*Search Bar*) di bagian atas untuk mengetik nama destinasi, hotel, atau kuliner yang dicari (contoh: *Guci*, *Sate Tegal*, *Slawi*).
  3. Klik filter kategori cepat untuk menyaring hasil berdasarkan layanan (*Wisata, Penginapan, Kuliner, Event, Rental*).
  4. Klik tombol **Cari** untuk menampilkan daftar hasil yang relevan.

> [Screenshot: Halaman beranda Jelajah Tegal dengan kolom pencarian pintar dan pill kategori layanan]

---

#### 2. Asisten Perencana Liburan AI (AI Trip Navigator)
* **Fungsi**: Merancang susunan rencana perjalanan (itinerary) wisata otomatis berbasis kecerdasan buatan sesuai bujet, durasi hari, dan preferensi minat Anda.
* **Langkah Penggunaan**:
  1. Klik menu **AI Trip** pada navigasi atas atau bilah bawah PWA mobile.
  2. Tentukan **Durasi Liburan** (misal: *1 Hari*, *2 Hari 1 Malam*, *3 Hari 2 Malam*).
  3. Pilih **Preferensi Kategori Wisata** (contoh: *Wisata Alam, Sejarah & Budaya, Relaksasi Pemandian Air Panas, Wisata Kuliner*).
  4. Masukkan perkiraan **Bujet Per Orang**.
  5. Klik tombol **Rancang Rencana Perjalanan AI**.
  6. AI akan menyusun jadwal jam demi jam, rekomendasi rute terdekat, estimasi biaya tiket masuk, dan rekomendasi tempat makan.
  7. Klik **Simpan Itinerary** untuk menyimpan rencana ke profil akun Anda atau klik **Pesan Tiket Terjadwal** untuk reservasi instan.

> [Screenshot: Antarmuka formulir AI Trip Navigator dengan opsi durasi, preferensi minat, dan hasil rute perjalanan]

---

#### 3. Pemesanan Tiket Wisata & Akomodasi (Online Booking & E-Tiket)
* **Fungsi**: Memesan tiket masuk objek wisata, kamar hotel/homestay, atau paket event secara daring tanpa antre loket.
* **Langkah Penggunaan**:
  1. Buka halaman detail destinasi wisata atau hotel yang ingin dikunjungi.
  2. Pilih jenis tiket / tipe kamar yang tersedia.
  3. Tentukan **Tanggal Kunjungan** dan **Jumlah Tiket / Jumlah Kamar**.
  4. Masukkan kode voucher promo pada kolom yang tersedia (jika Anda memiliki voucher diskon), lalu klik **Gunakan**.
  5. Klik tombol **Pesan Sekarang / Lanjut ke Pembayaran**.
  6. Periksa kembali ringkasan pesanan Anda pada halaman Checkout.
  7. Pilih metode pembayaran yang diinginkan (QRIS otomatis, BCA/Mandiri/BRI/BNI Virtual Account, atau E-Wallet).
  8. Selesaikan pembayaran sebelum batas waktu berakhir.
  9. Setelah pembayaran terverifikasi otomatis, buka menu **Pesanan & E-Tiket**.
  10. Klik pesanan Anda untuk membuka **E-Tiket QR Code**. Anda dapat mengunduh tiket atau langsung menunjukkannya kepada petugas loket di lokasi.

> [Screenshot: Halaman rincian tiket dengan QR Code digital, status 'Lunas - Siap Digunakan', dan tombol 'Unduh Tiket']

---

#### 4. Asisten AI Pintar & Fitur Chatbot
* **Fungsi**: Layanan tanya jawab interaktif 24/7 mengenai info pariwisata, rute jalan, jam buka objek wisata, hingga tips kuliner lokal Tegal.
* **Langkah Penggunaan**:
  1. Klik tombol melayang (*Floating Widget*) ikon robot/bintang di pojok kanan bawah layar: **Tanya Asisten AI**.
  2. Ketik pertanyaan Anda pada kolom chat (contoh: *"Rekomendasi sate kambing muda terenak dekat Slawi"* atau *"Tiket masuk Guci terbaru berapa?"*).
  3. Tekan tombol kirim. AI akan memberikan jawaban akurat dan menyematkan tautan langsung ke halaman destinasi terkait.

> [Screenshot: Kotak obrolan AI Chatbot yang terbuka di pojok kanan bawah dengan percakapan interaktif]

---

#### 5. Virtual Tour 360° Interaktif
* **Fungsi**: Menjelajahi panorama 360 derajat pemandangan objek wisata secara virtual sebelum berkunjung langsung.
* **Langkah Penggunaan**:
  1. Pada halaman detail destinasi wisata yang memiliki lencana **360° Virtual Tour**, klik tombol **Buka Virtual Tour**.
  2. Gerakkan kursor tetikus (*mouse*) atau usap layar smartphone Anda untuk melihat ke sekeliling (kiri, kanan, atas, bawah).
  3. Klik titik navigasi (*Hotspot Pin*) untuk berpindah ke area pemandangan berikutnya.

> [Screenshot: Layar penuh Virtual Tour 360 derajat di kawasan wisata Guci Tegal]

---

### MODUL B: OPERASIONAL PETUGAS LOKET (GATEKEEPER)

#### 1. Pemindai QR Code Tiket Masuk (Ticket Scanner)
* **Fungsi**: Memindai dan memverifikasi keabsahan e-tiket pengunjung di pintu gerbang loket.
* **Langkah Penggunaan**:
  1. Masuk ke akun petugas loket (*Gatekeeper*).
  2. Buka menu **Scanner QR Tiket**.
  3. Izinkan akses peramban web ke kamera perangkat Anda saat muncul notifikasi izin kamera (*Allow Camera Access*).
  4. Arahkan kamera ke QR Code pada layar smartphone atau cetakan tiket pengunjung.
  5. Sistem akan seketika membunyikan notifikasi dan menampilkan hasil validasi:
     * **HIJAU (VALID)**: Tiket asli, lunas, dan belum pernah digunakan. Nama pengunjung dan kuota orang akan tampil. Petugas dapat mempersilakan pengunjung masuk.
     * **KUNING (SUDAH DIGUNAKAN)**: Tiket sudah pernah di-scan sebelumnya lengkap dengan waktu check-in terdahulu.
     * **MERAH (TIDAK VALID / KADALUWARSA)**: Kode tiket tidak terdaftar pada sistem atau tanggal kunjungan telah lewat.

> [Screenshot: Antarmuka pemindai kamera dengan kotak bidik QR Code dan kartu status hasil pemindaian]

---

### MODUL C: PENGELOLA UNIT USAHA / TENANT (MITRA)

#### 1. Dashboard Ringkasan Bisnis
* **Fungsi**: Menampilkan grafik performa penjualan, total pengunjung hari ini, omzet bulanan, dan saldo yang siap ditarik.
* **Langkah Penggunaan**:
  1. Buka menu **Dashboard**.
  2. Pantau kartu metrik: *Total Pendapatan, Jumlah Pesanan Masuk, Tiket Digunakan*, dan *Saldo Tersedia*.
  3. Gunakan filter periode (7 Hari Terakhir, Bulan Ini, Tahun Ini) untuk menganalisis tren performa usaha Anda.

> [Screenshot: Dashboard analitik Mitra dengan grafik garis omzet dan kartu statistik penjualan]

---

#### 2. Manajemen Katalog Produk & Layanan
* **Fungsi**: Menambahkan, mengubah, atau menonaktifkan layanan wisata, kamar penginapan, menu kuliner, tiket event, atau armada rental.
* **Langkah Penggunaan**:
  1. Pilih sub-menu sesuai kategori usaha Anda pada menu navigasi samping (contoh: **Wisata**, **Penginapan**, **Kuliner**, **Event**, atau **Rental**).
  2. Untuk menambah data baru, klik tombol **+ Tambah Data Baru**.
  3. Lengkapi formulir produk:
     * **Nama Layanan / Destinasi**: Nama produk/layanan yang jelas.
     * **Slug URL**: Terisi otomatis secara *real-time*.
     * **Deskripsi Lengkap**: Informasi fasilitas, syarat kunjungan, atau spesifikasi menu/kamar.
     * **Harga Tiket / Tarif**: Nominal tarif dasar.
     * **Unggah Foto Galeri**: Foto utama sampul (*Cover*) dan galeri pendukung beresolusi tinggi.
     * **Koordinat Peta (GPS)**: Klik tombol *Deteksi Lokasi GPS* untuk menyematkan koordinat lokasi secara presisi.
  4. Klik tombol **Simpan & Ajukan Publikasi**.
  5. Untuk mengedit data yang sudah ada, klik tombol **Edit** (ikon pensil).
  6. Untuk menghapus data, klik tombol **Hapus** (ikon tempat sampah) lalu konfirmasi tindakan.

> [Screenshot: Formulir penginputan katalog layanan wisata lengkap dengan input upload gambar dan pemilih titik koordinat GPS]

---

#### 3. Pemrosesan Pesanan Masuk (Order Management)
* **Fungsi**: Memantau daftar transaksi pelanggan, memvalidasi bukti reservasi, dan mengunduh bukti invoice.
* **Langkah Penggunaan**:
  1. Buka menu **Pesanan Masuk**.
  2. Gunakan filter tab status pesanan: *Semua, Menunggu Pembayaran, Berhasil / Lunas, Dibatalkan*.
  3. Klik nomor pesanan untuk membuka lembar rincian pesanan (*Detail Order*).
  4. Anda dapat mencetak invoice atau mengekspor daftar transaksi ke dalam berkas Spreadsheet Excel.

> [Screenshot: Tabel daftar pesanan masuk dengan label status warna dan tombol aksi detail]

---

#### 4. Pengelolaan Buku Besar & Penarikan Dana Saldo (Withdrawals)
* **Fungsi**: Menarik saldo pendapatan usaha ke rekening bank resmi pemilik mitra.
* **Langkah Penggunaan**:
  1. Pastikan Anda telah mendaftarkan nomor rekening bank valid pada menu **Rekening Bank**.
  2. Buka menu **Penarikan Saldo**.
  3. Periksa nominal **Saldo Tersedia**.
  4. Klik tombol **+ Ajukan Penarikan Dana**.
  5. Pilih rekening bank tujuan dan masukkan nominal penarikan (minimal Rp 50.000).
  6. Masukkan catatan pengajuan (opsional), lalu klik **Kirim Pengajuan Penarikan**.
  7. Tim Administrator akan memverifikasi dan mentransfer dana ke rekening Anda. Status pengajuan (*Pending, Diproses, Berhasil*) dapat dipantau langsung pada tabel riwayat.

> [Screenshot: Modal formulir pengajuan penarikan saldo dengan kolom nominal dan pilihan rekening bank tujuan]

---

#### 5. Pengaturan Profil Usaha & Brand Media
* **Fungsi**: Memperbarui informasi profil tenant, logo brand resmi, banner sampul publik, dan jam operasional buka/tutup.
* **Langkah Penggunaan**:
  1. Buka menu **Profil Mitra**.
  2. Unggah logo resmi usaha Anda (format PNG/JPG transparan persegi).
  3. Unggah gambar banner sampul untuk mempercantik halaman depan profil mitra Anda.
  4. Atur **Jam Operasional Buka - Tutup** untuk setiap hari (Senin s.d. Minggu).
  5. Klik tombol **Simpan Perubahan Profil**.

> [Screenshot: Halaman pengaturan profil mitra dengan preview upload logo, banner sampul, dan pengaturan jam operasional]

---

### MODUL D: PENGAWASAN EKSEKUTIF PEMERINTAH DAERAH (DINAS)

#### 1. Dashboard Eksekutif Retribusi Daerah (PAD Analytics)
* **Fungsi**: Memantau secara transparan perolehan Pendapatan Asli Daerah (PAD) dari sektor tiket pariwisata milik pemerintah kabupaten/kota secara *real-time*.
* **Langkah Penggunaan**:
  1. Masuk menggunakan akun **Dinas Supervisor**.
  2. Pada menu **Dashboard Eksekutif**, amati grafik penerimaan retribusi harian, mingguan, dan bulanan.
  3. Pantau distribusi kontribusi pendapatan per objek wisata dinas (contoh: *Pemandian Air Panas Guci, Waduk Cacaban, Pantai Purwahamba Indah*).

> [Screenshot: Dashboard eksekutif dinas dengan grafik batang kontribusi pendapatan PAD per destinasi daerah]

---

#### 2. Pelaporan & Ekspor Rekonsiliasi Keuangan
* **Fungsi**: Menghasilkan dokumen laporan keuangan resmi untuk keperluan audit dan pertanggungjawaban kas daerah.
* **Langkah Penggunaan**:
  1. Buka menu **Penjualan Tiket PAD** atau **Ekspor Laporan PAD**.
  2. Tentukan rentang **Tanggal Awal** dan **Tanggal Akhir** pembukuan.
  3. Pilih filter destinasi tertentu atau seluruh destinasi dinas.
  4. Klik tombol **Ekspor ke Excel** atau **Unduh Laporan PDF**.
  5. Berkas laporan resmi berisikan nomor seri tiket, nominal retribusi, rincian pajak, dan status rekonsiliasi gateway akan terunduh secara otomatis.

> [Screenshot: Halaman filter ekspor laporan keuangan tiket PAD dengan tombol download Excel dan PDF]

---

### MODUL E: ADMINISTRATOR & SUPER ADMINISTRATOR

#### 1. Pembuatan Mitra Baru & Pengiriman Undangan Otomatis
* **Fungsi**: Mendaftarkan entitas bisnis tenant baru (Dinas atau Swasta) dan menerbitkan undangan aktivasi mandiri ke email pemilik.
* **Langkah Penggunaan**:
  1. Masuk ke antarmuka **Admin**, buka menu **Kelola Mitra**.
  2. Klik tombol **+ Buat Mitra Baru**.
  3. Pilih kategori mitra: **Non-Dinas (Swasta / Umum)** atau **Dinas (Pemerintah / Instansi)**.
  4. Masukkan **Nama Legal / Badan Hukum** dan **Nama Tampil Publik**.
  5. Kolom **Slug URL** akan terisi otomatis secara *real-time*. Anda tetap dapat menyesuaikannya bila diperlukan.
  6. Pilih wilayah kecamatan lokasi usaha.
  7. Masukkan **Nama Lengkap Pemilik (Owner)** dan **Alamat Email Pemilik**.
  8. Klik tombol **Buat Mitra & Kirim Undangan**.
  9. Sistem akan membuatkan tenant dalam status draf dan otomatis mengirimkan surat undangan aktivasi akun ke email pemilik.

> [Screenshot: Formulir pendaftaran mitra baru di halaman Admin dengan fitur auto-slug dan tombol kirim undangan]

---

#### 2. Moderasi Penayangan Katalog Produk Mitra
* **Fungsi**: Meninjau (*review*) kelayakan konten produk yang didaftarkan mitra sebelum ditampilkan ke masyarakat umum.
* **Langkah Penggunaan**:
  1. Buka menu **Moderasi** sesuai sektor yang diinginkan (contoh: **Moderasi Wisata** atau **Moderasi Penginapan**).
  2. Periksa detail deskripsi, foto yang diunggah, dan kewajaran harga tiket.
  3. Klik tombol aksi:
     * **Setujui (Approve / Publish)**: Konten seketika terbit dan dapat dipesan oleh publik.
     * **Tolak (Reject)**: Berikan catatan alasan penolakan agar mitra dapat memperbaiki datanya.

> [Screenshot: Tabel moderasi konten dengan pratinjau data produk dan tombol aksi Setujui / Tolak]

---

#### 3. Verifikasi Dokumen Legalitas (KYC Verification)
* **Fungsi**: Memeriksa keaslian dokumen NIB, NPWP, atau KTP pengelola mitra untuk menjamin keamanan transaksi wisatawan.
* **Langkah Penggunaan**:
  1. Buka menu **Review Dokumen KYC**.
  2. Buka berkas dokumen yang diunggah oleh mitra.
  3. Jika data identitas valid dan cocok dengan data perizinan resmi, klik tombol **Verifikasi Mitra**.
  4. Status mitra akan berubah menjadi **Mitra Terverifikasi Resmi** (berlencana centang biru di portal publik).

> [Screenshot: Lembar peninjauan dokumen KYC dengan tombol 'Verifikasi Legalitas' dan 'Minta Dokumen Ulang']

---

#### 4. Pemrosesan Penarikan Saldo Mitra (Payout Processing)
* **Fungsi**: Memvalidasi dan mengeksekusi pencairan dana saldo hasil penjualan mitra ke rekening bank terdaftar.
* **Langkah Penggunaan**:
  1. Buka menu **Penarikan Dana**.
  2. Klik baris pengajuan penarikan yang berstatus *Pending*.
  3. Lakukan transfer dana ke nomor rekening bank pemilik mitra yang tertera.
  4. Unggah nomor referensi transfer bank / bukti transfer.
  5. Klik tombol **Konfirmasi Penarikan Berhasil**. Saldo mitra akan dipotong secara akurat dan notifikasi terkirim otomatis ke email mitra.

> [Screenshot: Detail transaksi payout penarikan saldo dengan tombol upload bukti transfer dan tombol konfirmasi lunas]

---

#### 5. Pengaturan Sistem & Konfigurasi AI Chatbot (Super Admin)
* **Fungsi**: Mengelola variabel global aplikasi, sakelar fitur, dan parameter kecerdasan buatan.
* **Langkah Penggunaan**:
  1. Buka menu **Pengaturan AI Chatbot** atau **Pengaturan Platform**.
  2. Pada pengaturan chatbot, Anda dapat mengaktifkan/menonaktifkan integrasi model AI, mengatur prompt instruksi kepribadian asisten, serta mengatur batas pesan per menit.
  3. Klik tombol **Simpan Konfigurasi**.

> [Screenshot: Panel konfigurasi pengaturan global Super Admin dengan toggle fitur dan input API key]

---

## 5. TROUBLESHOOTING & PESAN KESALAHAN (ERROR HANDLING)

Berikut adalah daftar kendala teknis umum, pesan validasi, dan langkah pemecahan masalah yang dapat dilakukan:

| Kasus / Pesan Kesalahan | Penyebab Umum | Solusi & Tindakan Pengguna |
| :--- | :--- | :--- |
| **"Gagal masuk dengan Google atau proses otentikasi dibatalkan."** | Koneksi internet terputus saat dialihkan ke Google, atau izin akun Google dibatalkan oleh pengguna. | 1. Muat ulang (*refresh*) halaman login.<br>2. Pastikan koneksi internet stabil.<br>3. Klik kembali tombol **Lanjutkan dengan Akun Google** dan pilih akun email yang benar. |
| **"Tautan aktivasi tidak valid atau telah kedaluwarsa."** | Tautan aktivasi email mitra sudah pernah digunakan sebelumnya atau telah melewati batas masa berlaku 72 jam. | 1. Jika sudah pernah membuat kata sandi, silakan langsung masuk melalui halaman login.<br>2. Jika tautan kedaluwarsa sebelum aktivasi, hubungi Administrator untuk mengirim ulang tautan undangan baru. |
| **"Kata sandi saat ini yang Anda masukkan salah."** | Pengguna salah mengetik kata sandi lama saat ingin memperbarui kata sandi di menu profil. | Periksa tombol Caps Lock pada keyboard, pastikan kata sandi lama diketik dengan benar, atau gunakan fitur *Lupa Kata Sandi*. |
| **"Peringatan: Kamera tidak dapat diakses untuk pemindaian QR."** | Peramban web (browser) belum diberikan izin untuk menggunakan sensor kamera HP/Laptop. | 1. Klik ikon gembok / izin situs pada bilah alamat browser di bagian atas.<br>2. Ubah opsi **Camera / Kamera** menjadi **Allow (Izinkan)**.<br>3. Muat ulang halaman pemindai tiket. |
| **"Batas waktu pembayaran pesanan telah habis."** | Pengguna tidak menyelesaikan pembayaran QRIS/Virtual Account dalam jendela waktu yang ditentukan Midtrans. | Pesanan otomatis dibatalkan sistem demi menjaga ketersediaan kuota tiket. Pengguna dipersilakan membuat pesanan tiket baru. |
| **"Format file tidak didukung atau ukuran melebihi batas."** | Foto produk atau dokumen KYC yang diunggah melebihi kapasitas maksimum (umumnya maks 5 MB) atau berformat non-standar. | Pastikan berkas berformat **JPG, PNG, WEBP, atau PDF** dengan ukuran berkas di bawah 5 Megabyte. |
| **"Menu bawah pada smartphone terasa lambat atau tidak muncul."** | Cache aplikasi peramban versi lama tersimpan di perangkat mobile. | 1. Bersihkan riwayat penjelajahan & cache browser Anda.<br>2. Buka kembali halaman Jelajah Tegal.<br>3. Klik opsi browser **"Tambahkan ke Layar Utama" / "Install Aplikasi"** untuk pengalaman PWA terbaik. |

---

## 6. PUSAT BANTUAN & INFORMASI KONTAK RESMI

Jika Anda memerlukan bantuan teknis lebih lanjut, klarifikasi dokumen operasional, atau bantuan darurat transaksi:

* **Alamat Kantor**: Dinas Kepemudaan, Olahraga dan Pariwisata (Disporapar) Kabupaten Tegal, Jawa Tengah, Indonesia.
* **Website Resmi**: [https://jelajahtegal.com](https://jelajahtegal.com)
* **Email Dukungan Resmi**: `support@jelajahtegal.com` / `admin@jelajahtegal.com`
* **Layanan Bantuan Digital**: Buka menu **Kontak** atau gunakan widget **Asisten AI Jelajah Tegal** di sudut kanan bawah portal.

---
*© 2026 Jelajah Tegal. Seluruh Hak Cipta Dilindungi Undang-Undang.*
