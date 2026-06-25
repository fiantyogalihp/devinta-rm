# Hospital RM Search - Sistem Pencarian Rekam Medis Pasien

Aplikasi web untuk sistem pencarian nomor rekam medis pasien rumah sakit yang dibangun dengan PHP murni, MySQL, dan HTML/CSS/JS.

## 🚀 Fitur Utama

- ✅ Autentikasi login sederhana
- 🔍 Pencarian pasien fleksibel (nama, alamat, telepon, tanggal lahir, nomor identitas, usia)
- ➕ Tambah data pasien baru
- ✏️ Edit data pasien
- 🗑️ Hapus data pasien
- 📱 Responsive design untuk mobile dan desktop
- 🔒 Validasi input untuk data wajib
- 🎨 UI modern dan mudah digunakan

## 📋 Teknologi

- **Backend**: PHP 7.4+ (murni, tanpa framework)
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Server**: XAMPP/WAMP (untuk development lokal)

## 🛠️ Setup Lokal dengan XAMPP/WAMP

### 1. Install XAMPP/WAMP

Download dan install salah satu:
- **XAMPP**: https://www.apachefriends.org/
- **WAMP**: https://www.wampserver.com/

### 2. Copy Project ke htdocs

```bash
# Untuk XAMPP
cp -r hospital-rm-search C:/xampp/htdocs/

# Untuk WAMP
cp -r hospital-rm-search C:/wamp64/www/
```

### 3. Setup Database

1. Buka phpMyAdmin: http://localhost/phpmyadmin
2. Klik tab "SQL"
3. Copy isi file `database.sql` dan paste ke SQL editor
4. Klik "Go" untuk execute

Atau via command line:
```bash
mysql -u root -p < database.sql
```

### 4. Konfigurasi Database (Opsional)

Edit file `config/database.php` jika perlu mengubah kredensial:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Kosongkan untuk XAMPP default
define('DB_NAME', 'hospital_rm');
```

### 5. Jalankan Aplikasi

1. Start Apache dan MySQL di XAMPP/WAMP Control Panel
2. Buka browser: http://localhost/hospital-rm-search
3. Login dengan:
   - **Username**: `admin`
   - **Password**: `admin123`

## 📦 Struktur Database

### Tabel `users`
- `id` - Primary key
- `username` - Username unik
- `password_hash` - Password terenkripsi (PHP password_hash)
- `created_at` - Timestamp

### Tabel `patients`
- `id` - Primary key
- `no_rm` - Nomor rekam medis (unik)
- `nama` - Nama lengkap pasien
- `alamat` - Alamat lengkap
- `no_telepon` - Nomor telepon
- `tanggal_lahir` - Tanggal lahir (nullable)
- `nomor_identitas` - Nomor identitas KTP/SIM/Paspor (nullable)
- `usia` - Usia dalam tahun (nullable)
- `created_at` - Timestamp pembuatan
- `updated_at` - Timestamp update terakhir

## 📁 Struktur Folder

```
hospital-rm-search/
├── config/
│   ├── database.php       # Konfigurasi database
│   └── session.php        # Session management
├── assets/
│   └── css/
│       └── style.css      # Stylesheet utama
├── index.php              # Redirect ke login
├── login.php              # Halaman login
├── dashboard.php          # Dashboard & pencarian
├── patient_add.php        # Form tambah pasien
├── patient_edit.php       # Form edit pasien
├── logout.php             # Logout handler
├── database.sql           # Database schema
└── README.md              # Dokumentasi
```

## 📝 Cara Penggunaan

### Login
1. Buka http://localhost/hospital-rm-search
2. Masukkan username dan password
3. Klik "Login"

### Mencari Pasien
1. Pilih jenis pencarian (Nama, No. RM, Alamat, dll)
2. Masukkan kata kunci
3. Klik tombol "Cari"
4. Hasil akan ditampilkan dalam tabel

### Tambah Pasien Baru
1. Klik tombol "Tambah Pasien Baru"
2. Isi form dengan data pasien
3. Field dengan tanda (*) wajib diisi
4. Klik "Simpan Data Pasien"

### Edit Data Pasien
1. Pada tabel hasil pencarian, klik tombol "Edit"
2. Ubah data yang diperlukan
3. Klik "Update Data Pasien"

### Hapus Data Pasien
1. Pada tabel hasil pencarian, klik tombol "Hapus"
2. Konfirmasi penghapusan
3. Data akan dihapus dari database

## 🔐 Keamanan

- Password disimpan dengan PHP `password_hash()` (bcrypt)
- Session-based authentication
- Prepared statements untuk mencegah SQL injection
- Input validation dan sanitization
- Protected pages dengan session check

## 🎨 Responsive Design

Aplikasi dioptimalkan untuk:
- 📱 Mobile (< 768px)
- 💻 Tablet (768px - 1024px)
- 🖥️ Desktop (> 1024px)

## 🐛 Troubleshooting

### Error: "Connection failed"
**Solusi**: 
- Pastikan MySQL/MariaDB sudah running
- Cek kredensial database di `config/database.php`
- Pastikan database `hospital_rm` sudah dibuat

### Error: "Table doesn't exist"
**Solusi**:
- Import file `database.sql` ke phpMyAdmin
- Atau jalankan: `mysql -u root -p < database.sql`

### Login gagal
**Solusi**:
- Pastikan tabel `users` sudah ada
- Cek apakah user admin sudah ter-insert
- Default: username `admin`, password `admin123`

### Halaman blank/error 500
**Solusi**:
- Enable error reporting di php.ini
- Cek Apache error log
- Pastikan PHP version 7.4+

## 🔄 Update Password Admin

Untuk mengubah password admin:

```php
<?php
// generate_password.php
$password = 'password_baru_anda';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo $hash;
?>
```

Jalankan script di atas, copy hash-nya, lalu update di database:

```sql
UPDATE users SET password_hash = 'hash_dari_script' WHERE username = 'admin';
```

## 📞 Support

Untuk pertanyaan atau masalah, silakan buat issue di repository ini.

## 📜 License

MIT License - bebas digunakan untuk keperluan komersial maupun non-komersial.

## 🎯 Default Login

- **Username**: `admin`
- **Password**: `admin123`

**PENTING**: Segera ubah password default setelah instalasi pertama!
