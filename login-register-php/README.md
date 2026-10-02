# Sistem Login/Register — Tugas Rutin 7

Sistem login/register sederhana menggunakan **PHP Native** (tanpa framework) dengan **file JSON** sebagai penyimpanan data.

## 📁 Struktur Proyek

```
login-register-php/
├── index.php           # Entry point, redirect otomatis ke login/dashboard
├── register.php        # Halaman & proses registrasi
├── login.php           # Halaman & proses login
├── dashboard.php        # Halaman dashboard (terproteksi)
├── edit_profile.php     # Bonus: edit nama & ganti password
├── logout.php           # Proses logout
├── includes/
│   └── auth.php          # Fungsi bantu: load/save user, sanitasi, session
├── data/
│   └── users.json        # Penyimpanan data user (harus writable)
├── assets/
│   └── style.css          # Styling
└── README.md
```

## 🚀 Cara Menjalankan

1. Pastikan PHP terpasang (PHP 7.4+ atau 8.x, sudah diuji pada PHP 8.3).
2. Pastikan folder `data/` dan file `data/users.json` dapat ditulis (writable) oleh web server:
   ```bash
   chmod -R 755 login-register-php
   chmod 666 login-register-php/data/users.json
   ```
3. Jalankan dengan PHP built-in server dari dalam folder proyek:
   ```bash
   php -S localhost:8000
   ```
4. Buka `http://localhost:8000` di browser. Anda akan diarahkan ke halaman login.

## ✅ Pemenuhan Requirement

| # | Requirement | Implementasi |
|---|---|---|
| 1 | Form registrasi dengan validasi (nama, email, password) | `register.php` — validasi panjang nama, format email, panjang & konfirmasi password |
| 2 | Validasi email dengan `filter_var()` | `filter_var($email, FILTER_VALIDATE_EMAIL)` di `register.php` & `login.php` |
| 3 | Password di-hash dengan `password_hash()` | `password_hash($password, PASSWORD_DEFAULT)` saat registrasi & ganti password |
| 4 | Data disimpan di file JSON | `includes/auth.php` → `loadUsers()` / `saveUsers()` membaca/menulis `data/users.json` |
| 5 | Cek duplikasi email saat registrasi | `findUserByEmail()` dipanggil sebelum menyimpan user baru |
| 6 | Sistem login dengan session | `session_start()` + `$_SESSION['user_email']` di `login.php` |
| 7 | Dashboard yang diproteksi (redirect jika belum login) | `requireLogin()` di awal `dashboard.php` & `edit_profile.php` |
| 8 | Logout functionality | `logout.php` memanggil `session_destroy()` dan membersihkan `$_SESSION` |
| 9 | Sanitasi input dengan `htmlspecialchars()` | Fungsi `sanitize()` digunakan pada semua input teks sebelum diproses/ditampilkan |
| 10 | Pesan error & sukses yang jelas | Array `$errors[]` untuk validasi + sistem *flash message* (`setFlash()`/`getFlash()`) untuk notifikasi sukses setelah redirect |

## ⭐ Bonus yang Diimplementasikan

- **Remember Me (cookies):** Saat login, centang "Ingat saya" menyimpan token acak (`random_bytes`) di cookie HTTPOnly selama 30 hari. Token juga disimpan di `users.json` dan diverifikasi dengan `hash_equals()` (aman dari timing attack). Token dihapus otomatis saat logout.
- **Edit Profile:** Halaman `edit_profile.php` memungkinkan user mengubah nama dan/atau password (opsional, password lama tidak perlu diinput ulang selama sudah login).
- **Tampilan CSS rapi:** Desain dark theme modern dengan kartu terpusat, warna aksen pink (`#ec4899`), dan feedback visual (alert sukses/error).

## 🔒 Catatan Keamanan

- Password **tidak pernah** disimpan dalam bentuk plain text — selalu melalui `password_hash()`.
- Semua input ditampilkan kembali ke HTML melalui `htmlspecialchars()` untuk mencegah XSS.
- Perbandingan token "Remember Me" menggunakan `hash_equals()` untuk mencegah timing attack.
- Cookie token diset dengan flag `httponly` agar tidak bisa diakses lewat JavaScript.
- Pola *Post/Redirect/Get* (redirect setelah POST) digunakan agar refresh halaman tidak mengirim ulang form.

## 🧪 Pengujian yang Sudah Dilakukan

Sudah diuji end-to-end menggunakan PHP built-in server dan `curl`, mencakup:
- Registrasi user baru → data tersimpan & password ter-hash ✔️
- Registrasi dengan email duplikat → ditolak dengan pesan error ✔️
- Registrasi dengan format email salah → ditolak oleh `filter_var()` ✔️
- Login dengan kredensial benar → session terbentuk, redirect ke dashboard ✔️
- Login dengan password salah → ditolak dengan pesan error ✔️
- Akses dashboard tanpa login → redirect otomatis ke login ✔️
- Logout → session & cookie dibersihkan, dashboard tidak bisa diakses lagi ✔️
