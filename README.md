# Hospital RM Search - Sistem Pencarian Rekam Medis Pasien

Aplikasi web untuk sistem pencarian nomor rekam medis pasien rumah sakit yang dibangun dengan SvelteKit, TypeScript, dan Turso Cloud Database.

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

- **Framework**: SvelteKit
- **Language**: TypeScript
- **Database**: Turso Cloud (SQLite)
- **Deployment**: Vercel
- **Adapter**: @sveltejs/adapter-vercel

## 🛠️ Setup Lokal

### 1. Clone Repository

```bash
git clone <repository-url>
cd hospital-rm-search
```

### 2. Install Dependencies

```bash
npm install
```

### 3. Setup Database Turso

Buat database Turso:

```bash
# Install Turso CLI
curl -sSfL https://get.tur.so/install.sh | bash

# Login ke Turso
turso auth login

# Buat database baru
turso db create hospital-rm-db

# Dapatkan URL database
turso db show hospital-rm-db --url

# Buat auth token
turso db tokens create hospital-rm-db
```

### 4. Import Schema Database

```bash
# Connect ke database dan import schema
turso db shell hospital-rm-db < schema.sql
```

### 5. Setup Environment Variables

Buat file `.env` dari template:

```bash
cp .env.example .env
```

Edit file `.env` dan isi dengan kredensial Turso Anda:

```env
TURSO_DATABASE_URL=libsql://your-database-name.turso.io
TURSO_AUTH_TOKEN=your-auth-token-here
SESSION_SECRET=your-random-secret-key-here
```

**Generate SESSION_SECRET:**
```bash
node -e "console.log(require('crypto').randomBytes(32).toString('hex'))"
```

### 6. Jalankan Development Server

```bash
npm run dev
```

Aplikasi akan berjalan di `http://localhost:5173`

### 7. Login Default

- **Username**: `admin`
- **Password**: `admin123`

## 📦 Struktur Database

### Tabel `users`
- `id` - Primary key
- `username` - Username unik
- `password_hash` - Password terenkripsi
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

## 🚀 Deployment ke Vercel

### 1. Push ke Git Repository

```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin <your-git-repo-url>
git push -u origin main
```

### 2. Deploy ke Vercel

#### Via Vercel CLI:

```bash
# Install Vercel CLI
npm i -g vercel

# Login ke Vercel
vercel login

# Deploy
vercel
```

#### Via Vercel Dashboard:

1. Buka [vercel.com](https://vercel.com)
2. Klik "Add New Project"
3. Import repository Git Anda
4. Vercel akan otomatis mendeteksi SvelteKit
5. Tambahkan Environment Variables:
   - `TURSO_DATABASE_URL`
   - `TURSO_AUTH_TOKEN`
   - `SESSION_SECRET`
6. Klik "Deploy"

### 3. Setup Environment Variables di Vercel

Di Vercel Dashboard:
1. Pilih project Anda
2. Pergi ke Settings → Environment Variables
3. Tambahkan variabel berikut:
   - `TURSO_DATABASE_URL` = URL database Turso Anda
   - `TURSO_AUTH_TOKEN` = Auth token Turso Anda
   - `SESSION_SECRET` = Secret key untuk session

## 📝 Cara Penggunaan

### Login
1. Buka aplikasi
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

- Password disimpan dengan bcrypt hashing
- Session menggunakan HTTP-only cookies
- Protected routes dengan middleware
- Input validation untuk mencegah data tidak valid

## 🎨 Responsive Design

Aplikasi dioptimalkan untuk:
- 📱 Mobile (< 768px)
- 💻 Tablet (768px - 1024px)
- 🖥️ Desktop (> 1024px)

## 📄 File Penting

- `schema.sql` - Schema database
- `svelte.config.js` - Konfigurasi SvelteKit
- `src/lib/db.ts` - Koneksi database
- `src/lib/auth.ts` - Autentikasi
- `src/lib/session.ts` - Session management
- `src/hooks.server.ts` - Server hooks untuk proteksi route

## 🐛 Troubleshooting

### Error: Cannot connect to database
- Pastikan `TURSO_DATABASE_URL` dan `TURSO_AUTH_TOKEN` sudah benar
- Cek koneksi internet
- Verifikasi database Turso masih aktif

### Error: UNIQUE constraint failed
- Nomor rekam medis sudah digunakan
- Gunakan nomor rekam medis yang berbeda

### Login gagal
- Pastikan menggunakan kredensial yang benar
- Default: username `admin`, password `admin123`

## 📞 Support

Untuk pertanyaan atau masalah, silakan buat issue di repository ini.

## 📜 License

MIT License - bebas digunakan untuk keperluan komersial maupun non-komersial.
