# Library Management Web System 📚

Sebuah sistem informasi manajemen perpustakaan berbasis web (Full-stack) yang dibangun menggunakan PHP Native dan MySQL. Aplikasi ini memfasilitasi pengelolaan katalog buku secara digital, sirkulasi peminjaman, serta pelacakan riwayat dan denda secara otomatis menggunakan *Role-Based Access Control* (RBAC) untuk Admin dan User (Mahasiswa).

## Fitur Utama

### Akses Admin (Pustakawan)
* **Dashboard Statistik:** Ringkasan jumlah buku, total anggota, dan status peminjaman aktif.
* **Manajemen Buku (CRUD):** Tambah, edit, hapus, dan kelola stok buku beserta gambar sampul.
* **Manajemen Anggota:** Menampilkan data seluruh pengguna (admin dan mahasiswa) dalam satu tabel, dengan fitur khusus untuk memperbarui profil akun admin.
* **Sirkulasi Peminjaman (Workflow):** Pemrosesan status peminjaman secara berurutan:
  `Menunggu Persetujuan` ➡️ `Siap Diambil` ➡️ `Dipinjam` ➡️ `Selesai` / `Dibatalkan`.
* **Kalkulasi Denda Otomatis:** Perhitungan nominal denda jika pengguna mengembalikan buku melewati batas tanggal jatuh tempo.
* **Pembatalan Otomatis:** Sistem membatalkan pesanan secara otomatis jika buku berstatus *Siap Diambil* tidak diambil dalam kurun waktu 3 hari.

### Akses User (Mahasiswa)
* **Katalog Digital & Live Search:** Pencarian judul buku dan filter berdasarkan kategori secara mulus dengan UI *sticky toolbar*.
* **Detail Buku & Stok Real-time:** Menampilkan sinopsis, penulis, ketersediaan stok, dan tombol pengajuan pinjam.
* **Pengajuan & Pembatalan Mandiri:** Fitur *request* pinjam buku otomatis mengurangi stok dan fitur batalkan pinjaman saat status masih menunggu/siap diambil otomatis memulihkan stok.
* **Riwayat Peminjaman:** Halaman khusus untuk melacak status buku yang sedang dipinjam, tanggal pengambilan, jatuh tempo, dan riwayat denda.

## Tech Stack
* **Frontend:** HTML, CSS, JavaScript.
* **Backend:** PHP.
* **Database:** MySQL.
* **Ikon:** FontAwesome.

## Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan aplikasi ini menggunakan XAMPP:

1. **Clone Repositori:** Buka terminal dan jalankan perintah git clone [https://github.com/sitifatiharh/perpus-web.git](https://github.com/sitifatiharh/perpus-web).
2. **Pindahkan Folder:** Pindahkan folder repositori tersebut ke dalam direktori XAMPP lokal, yaitu C:\xampp\htdocs\.
3. **Konfigurasi Database:** Nyalakan modul Apache dan MySQL melalui XAMPP Control Panel. Akses http://localhost/phpmyadmin di peramban, lalu buat database baru dengan nama db_pinjam_buku.
4. **Import File SQL:** Buka database yang baru dibuat, pilih tab Import, dan unggah file berekstensi .sql bawaan dari folder proyek tersebut.
5. **Koneksi Database:** Buka file config/db.php di teks editor dan pastikan konfigurasi koneksinya sesuai dengan kredensial server lokal:
```$host = "localhost";
\$user = "root";
\$pass = "";
\$db   = "db_pinjam_buku";
7. **Jalankan Aplikasi:** Buka tab baru pada peramban dan akses aplikasi melalui URL http://localhost/perpus-web (atau sesuaikan tepat dengan nama folder yang ada di dalam htdocs).
