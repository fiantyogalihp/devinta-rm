# 🐳 Docker Setup - Hospital RM Search

Panduan lengkap untuk menjalankan aplikasi Hospital RM Search menggunakan Docker.

## 📋 Prerequisites

Pastikan sudah terinstall:
- **Docker**: Version 20.10 atau lebih baru
- **Docker Compose**: Version 2.0 atau lebih baru

### Cek Instalasi Docker

```bash
docker --version
docker-compose --version
```

### Install Docker (jika belum)

**Linux (Ubuntu/Debian)**:
```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER
```

**macOS**: Download [Docker Desktop for Mac](https://www.docker.com/products/docker-desktop)

**Windows**: Download [Docker Desktop for Windows](https://www.docker.com/products/docker-desktop)

## 🚀 Quick Start

```bash
# 1. Masuk ke direktori project
cd /home/satoru/Documents/devinta-rm

# 2. Build dan jalankan containers
docker-compose up -d

# 3. Tunggu beberapa detik untuk inisialisasi database

# 4. Buka browser
http://localhost:8080
```

**Login Credentials**:
- Username: `admin`
- Password: `admin123`

## 📖 Detailed Setup Steps

### 1. Clone/Navigate to Project

```bash
cd /home/satoru/Documents/devinta-rm
```

### 2. Build Docker Images

```bash
docker-compose build
```

Output yang diharapkan:
```
[+] Building 45.2s (12/12) FINISHED
 => [internal] load build definition from Dockerfile
 => => transferring dockerfile: 456B
 => [internal] load .dockerignore
 ...
```

### 3. Start Containers

```bash
docker-compose up -d
```

Flag `-d` menjalankan containers di background (detached mode).

Output yang diharapkan:
```
[+] Running 3/3
 ✔ Network devinta-rm_hospital-network  Created
 ✔ Container devinta-rm-db              Started
 ✔ Container devinta-rm-web             Started
```

### 4. Verify Containers are Running

```bash
docker-compose ps
```

Output yang diharapkan:
```
NAME                IMAGE              STATUS         PORTS
devinta-rm-db       mysql:8.0          Up 30 seconds  3306/tcp, 33060/tcp
devinta-rm-web      devinta-rm-web     Up 30 seconds  0.0.0.0:8080->80/tcp
```

### 5. Check Logs (Optional)

```bash
# Semua containers
docker-compose logs -f

# Hanya web container
docker-compose logs -f web

# Hanya database container
docker-compose logs -f db
```

Press `Ctrl+C` untuk keluar dari logs.

### 6. Access Application

Buka browser dan akses:
```
http://localhost:8080
```

## 🔧 Container Management

### Stop Containers

```bash
docker-compose stop
```

### Start Containers (setelah stop)

```bash
docker-compose start
```

### Restart Containers

```bash
docker-compose restart
```

### Stop dan Remove Containers

```bash
docker-compose down
```

### Stop, Remove Containers, dan Delete Volumes (HATI-HATI: Data akan hilang!)

```bash
docker-compose down -v
```

### Rebuild Containers (setelah perubahan Dockerfile)

```bash
docker-compose up -d --build
```

## 💾 Database Management

### Access MySQL CLI

```bash
docker exec -it devinta-rm-db mysql -uroot -psecret123 hospital_rm
```

### Backup Database

```bash
docker exec devinta-rm-db mysqldump -uroot -psecret123 hospital_rm > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restore Database

```bash
docker exec -i devinta-rm-db mysql -uroot -psecret123 hospital_rm < backup_file.sql
```

### View Database Tables

```bash
docker exec -it devinta-rm-db mysql -uroot -psecret123 -e "USE hospital_rm; SHOW TABLES;"
```

### Check Patient Records Count

```bash
docker exec -it devinta-rm-db mysql -uroot -psecret123 -e "USE hospital_rm; SELECT COUNT(*) FROM patients;"
```

## 🔍 Troubleshooting

### Problem: Port 8080 already in use

**Error**:
```
Error starting userland proxy: listen tcp4 0.0.0.0:8080: bind: address already in use
```

**Solution 1**: Stop aplikasi yang menggunakan port 8080
```bash
# Linux/Mac
sudo lsof -i :8080
sudo kill -9 <PID>

# Windows
netstat -ano | findstr :8080
taskkill /PID <PID> /F
```

**Solution 2**: Ubah port di `docker-compose.yml`
```yaml
services:
  web:
    ports:
      - "8081:80"  # Ubah dari 8080 ke 8081
```

### Problem: Database connection failed

**Error di browser**:
```
Connection failed: ...
```

**Solution**:
```bash
# 1. Cek status database container
docker-compose ps

# 2. Cek logs database
docker-compose logs db

# 3. Restart containers
docker-compose restart

# 4. Jika masih error, rebuild
docker-compose down
docker-compose up -d --build
```

### Problem: Database not initialized

**Symptoms**: Login gagal, tabel tidak ada

**Solution**:
```bash
# 1. Stop dan hapus semua
docker-compose down -v

# 2. Start ulang (akan re-initialize database)
docker-compose up -d

# 3. Tunggu 10-15 detik untuk inisialisasi
docker-compose logs -f db
```

### Problem: Permission denied errors

**Error**:
```
Permission denied: /var/www/html/...
```

**Solution**:
```bash
# Rebuild dengan proper permissions
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Problem: Changes not reflected

**Symptoms**: Edit code tapi tidak berubah di browser

**Solution**:
```bash
# 1. Hard refresh browser (Ctrl+Shift+R atau Cmd+Shift+R)

# 2. Restart web container
docker-compose restart web

# 3. Clear browser cache
```

### Problem: Container keeps restarting

**Solution**:
```bash
# 1. Check logs untuk error
docker-compose logs web
docker-compose logs db

# 2. Check container status
docker-compose ps

# 3. Inspect container
docker inspect devinta-rm-web
docker inspect devinta-rm-db
```

## 📊 Monitoring

### View Real-time Logs

```bash
# All containers
docker-compose logs -f

# Specific container
docker-compose logs -f web
docker-compose logs -f db
```

### Check Resource Usage

```bash
docker stats
```

### Inspect Container Details

```bash
docker inspect devinta-rm-web
docker inspect devinta-rm-db
```

## 🔐 Security Notes

### Production Deployment

**PENTING**: Jangan gunakan credentials default di production!

1. **Ubah Database Password**:
   Edit `docker-compose.yml`:
   ```yaml
   environment:
     - MYSQL_ROOT_PASSWORD=<strong_password_here>
     - DB_PASS=<strong_password_here>
   ```

2. **Ubah Admin Password**:
   ```bash
   docker exec -it devinta-rm-db mysql -uroot -p<your_password> hospital_rm
   ```
   ```sql
   UPDATE users SET password_hash = '$2y$10$...' WHERE username = 'admin';
   ```

3. **Use Environment Variables**:
   Buat file `.env` (jangan commit ke git):
   ```
   DB_PASS=your_secure_password
   MYSQL_ROOT_PASSWORD=your_secure_password
   ```

4. **Enable HTTPS**:
   Gunakan reverse proxy seperti Nginx atau Traefik dengan SSL certificate.

## 🌐 Network Configuration

### Container Network

Containers berkomunikasi melalui network `hospital-network`:
- **web** container dapat akses **db** container via hostname `db`
- **db** container tidak exposed ke host (hanya internal)

### Expose Database to Host (Optional)

Jika perlu akses MySQL dari host:

Edit `docker-compose.yml`:
```yaml
services:
  db:
    ports:
      - "3306:3306"  # Tambahkan ini
```

Kemudian:
```bash
docker-compose down
docker-compose up -d
```

Akses dari host:
```bash
mysql -h 127.0.0.1 -P 3306 -uroot -psecret123 hospital_rm
```

## 📁 Volume Management

### List Volumes

```bash
docker volume ls
```

### Inspect Volume

```bash
docker volume inspect devinta-rm_mysql-data
```

### Backup Volume

```bash
docker run --rm -v devinta-rm_mysql-data:/data -v $(pwd):/backup ubuntu tar czf /backup/mysql-backup.tar.gz /data
```

### Restore Volume

```bash
docker run --rm -v devinta-rm_mysql-data:/data -v $(pwd):/backup ubuntu tar xzf /backup/mysql-backup.tar.gz -C /
```

## 🧪 Development Workflow

### Edit Code

1. Edit files di `/home/satoru/Documents/devinta-rm`
2. Changes automatically reflected (volume mount)
3. Refresh browser untuk melihat perubahan

### Add New PHP Extensions

Edit `Dockerfile`:
```dockerfile
RUN docker-php-ext-install mysqli pdo pdo_mysql
```

Rebuild:
```bash
docker-compose up -d --build
```

### Debug PHP Errors

Enable error reporting di `index.php` atau `dashboard.php`:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
```

## 📞 Support

### Useful Commands Cheatsheet

```bash
# Start
docker-compose up -d

# Stop
docker-compose down

# Logs
docker-compose logs -f

# Restart
docker-compose restart

# Rebuild
docker-compose up -d --build

# Shell access
docker exec -it devinta-rm-web bash
docker exec -it devinta-rm-db bash

# MySQL access
docker exec -it devinta-rm-db mysql -uroot -psecret123 hospital_rm
```

### Clean Up Everything

```bash
# Stop dan hapus containers, networks, volumes
docker-compose down -v

# Hapus images
docker rmi devinta-rm-web mysql:8.0

# Hapus unused images, containers, networks
docker system prune -a
```

## ✅ Verification Checklist

- [ ] Docker dan Docker Compose terinstall
- [ ] `docker-compose up -d` berhasil
- [ ] Containers running: `docker-compose ps`
- [ ] Web accessible: http://localhost:8080
- [ ] Login berhasil (admin/admin123)
- [ ] Dapat menambah pasien baru
- [ ] Dapat mencari pasien
- [ ] Dapat edit/hapus pasien
- [ ] Data persist setelah restart

## 🎯 Next Steps

1. ✅ Setup Docker environment
2. 🔐 Ubah default passwords
3. 📝 Tambah data pasien
4. 🧪 Test semua fitur
5. 📚 Baca dokumentasi lengkap di README.md

---

**Happy Coding! 🚀**

Untuk pertanyaan atau issues, silakan buat issue di repository.
