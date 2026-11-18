# SecureDocument

Aplikasi pengelolaan dokumen berbasis web yang dibuat untuk membantu menjaga keamanan isi dokumen menggunakan algoritma AES-256-GCM dan RSA 2048-bit.

## Deskripsi Aplikasi

SecureDocument merupakan aplikasi berbasis web yang memungkinkan administrator untuk mengelola dokumen dalam bentuk teks. Aplikasi ini menerapkan teknik kriptografi agar isi dokumen tidak disimpan secara langsung dalam bentuk teks asli di database.

Aplikasi ini dibuat menggunakan PHP dan MySQL dengan XAMPP sebagai server lokal.

## Fitur Aplikasi

- Login administrator.
- Dashboard pengelolaan dokumen.
- Menambahkan dokumen.
- Enkripsi isi dokumen menggunakan AES-256-GCM.
- Perlindungan kunci AES menggunakan RSA 2048-bit.
- Melihat dokumen melalui proses dekripsi.
- Menghapus dokumen.
- Logout administrator.

## Teknologi yang Digunakan

- PHP 8.3
- MySQL
- HTML
- CSS
- JavaScript
- Bootstrap 5.3.3
- Bootstrap Icons
- OpenSSL
- XAMPP

## Algoritma Keamanan

**AES-256-GCM**

Digunakan untuk mengenkripsi isi dokumen. Algoritma ini juga menghasilkan authentication tag untuk membantu memeriksa integritas data.

**RSA 2048-bit**

Digunakan untuk melindungi kunci AES menggunakan public key. Private key digunakan untuk mendapatkan kembali kunci AES ketika proses dekripsi dilakukan.

## Struktur Database

Database yang digunakan bernama `secure_document_db` dan memiliki dua tabel utama:

1. `admins`: menyimpan akun administrator.
2. `documents`: menyimpan informasi dokumen dan hasil enkripsinya.

File database tersedia dalam repository dengan nama `secure_document_db.sql`.

## Cara Menjalankan Aplikasi

1. Pastikan XAMPP sudah terpasang.
2. Aktifkan Apache dan MySQL.
3. Salin folder aplikasi ke dalam direktori `C:\xampp\htdocs\`.
4. Buka phpMyAdmin melalui `http://localhost/phpmyadmin`.
5. Buat database dengan nama `secure_document_db`.
6. Import file `secure_document_db.sql`.
7. Sesuaikan konfigurasi database pada file `config/database.php`.
8. Aktifkan extension OpenSSL pada PHP.
9. Buat pasangan kunci RSA 2048-bit berupa public key dan private key.
10. Simpan kedua kunci pada lokasi yang sesuai dengan konfigurasi di `includes/crypto.php`. Jangan simpan private key di dalam folder publik `htdocs`.
11. Buka browser dan akses `http://localhost/secure_document/`.

## Catatan Keamanan

- Password administrator disimpan menggunakan password hashing.
- Private key RSA tidak disertakan dalam repository.
- Jangan mengunggah private key atau informasi rahasia ke GitHub.
- Gunakan kunci RSA milik sendiri ketika menjalankan aplikasi.

## Database

File database dapat ditemukan pada:

`secure_document_db.sql`

## Repository

https://github.com/revadn-lgtm/secure-document

---

Dibuat sebagai proyek mata kuliah Sistem Keamanan Data.

Program Studi Teknik Informatika  
Universitas Muhammadiyah Sukabumi  
2026# secure-document
Aplikasi pengelolaan dokumen terenkripsi menggunakan AES-256-GCM dan RSA 2048-bit
