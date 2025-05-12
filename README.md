
# 📰 PORTALBERITA115

**PORTALBERITA115** adalah sistem portal berita online berbasis web yang mendukung manajemen artikel oleh admin dan penulis, serta memberikan akses baca untuk pengunjung umum. Dibuat menggunakan PHP dan MySQL, portal ini mengusung tampilan modern dengan tema gelap (dark mode) untuk kenyamanan pengguna.

---

## ✨ Fitur Utama

### 🔒 Role Login (Admin & Penulis)
- **Admin:**
  - Tulis, edit, dan hapus artikel.
  - Akses ke laporan.
- **Penulis:**
  - Hanya dapat menulis artikel baru.
  - Tidak bisa mengedit/hapus artikel yang sudah terbit.

### 📝 Manajemen Artikel
- Tambah dan tampilkan artikel berita.
- Artikel tersedia untuk dibaca oleh pengunjung umum.
- Halaman artikel mendukung tampilan tema gelap.

### 🌐 Akses Publik
- Pengunjung dapat membaca semua artikel tanpa login.
- Desain halaman utama dan detail artikel konsisten dengan tema gelap.

---

## ⚙️ Teknologi yang Digunakan

- **Backend**: PHP (native)
- **Database**: MySQL
- **Frontend**: HTML, CSS (Dark Theme), JavaScript
- **Authentication**: Login berbasis peran (Role-Based Access Control)

---

## 📂 Struktur Proyek (Singkat)
PORTALBERITA115/
│
├── admin/ # Halaman dan fungsi admin
├── penulis/ # Halaman penulis
├── artikel.php # Tampilan artikel tunggal
├── index.php # Halaman utama portal berita
├── login.php # Halaman login
├── logout.php # Logout
├── db/ # Koneksi dan konfigurasi database
└── css/ # Style termasuk dark mode





---

## 🛠️ Cara Menggunakan

1. Clone atau download repositori ini.
2. Import file database `.sql` ke MySQL (terletak di folder `db` jika tersedia).
3. Ubah konfigurasi koneksi database di file koneksi (biasanya di folder `db/`).
4. Jalankan menggunakan server lokal seperti XAMPP/Laragon.
5. Akses halaman login untuk masuk sebagai admin atau penulis.

---

## 📌 Catatan

- Struktur CRUD tidak diubah, penambahan fitur role dan tampilan dark mode dilakukan tanpa mengganggu fungsionalitas yang ada.
- Cocok untuk belajar sistem manajemen konten, pengembangan web, atau sebagai bahan tugas akhir.

---

## 📧 Kontak

Untuk pertanyaan atau kontribusi, silakan hubungi melalui GitHub Issues atau pull request atau @kulo.beryl (INSTAGRAM)



