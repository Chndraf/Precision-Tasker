# Precision Tasker - Deployment Guide (Fly.io + Supabase)

Panduan lengkap untuk deploy aplikasi Laravel ke **Fly.io** dengan database **Supabase** (100% FREE untuk production).

---

## 📋 Daftar Isi
1. [Setup Supabase Database](#1-setup-supabase-database)
2. [Install Fly CLI](#2-install-fly-cli)
3. [Deploy ke Fly.io](#3-deploy-ke-flyio)
4. [Konfigurasi Secrets (Environment Variables)](#4-konfigurasi-secrets)
5. [Post-Deployment Setup](#5-post-deployment-setup)
6. [Monitoring & Maintenance](#6-monitoring--maintenance)
7. [Troubleshooting](#7-troubleshooting)

---

## 1. Setup Supabase Database

### Langkah 1: Buat Project Supabase
1. Buka [https://supabase.com](https://supabase.com) dan login/signup
2. Klik **"New Project"**
3. Isi informasi project:
   - **Name**: `precision-tasker`
   - **Database Password**: Buat password yang kuat (⚠️ SIMPAN PASSWORD INI!)
   - **Region**: Pilih **Singapore (`ap-southeast-1`)** (terdekat dengan Indonesia)
4. Klik **"Create new project"** dan tunggu ~2 menit sampai database siap

### Langkah 2: Dapatkan Connection String
1. Di dashboard Supabase, buka menu **Settings (⚙️)** > **Database**
2. Scroll ke bagian **Connection String** > pilih tab **URI**
3. Format connection string:
   ```
   postgresql://postgres:[YOUR-PASSWORD]@db.xxxxxxxxxxxxxx.supabase.co:5432/postgres
   ```
4. Catat detail berikut:
   - **Host**: `db.xxxxxxxxxxxxxx.supabase.co`
   - **Port**: `5432`
   - **Database**: `postgres`
   - **User**: `postgres`
   - **Password**: Password yang Anda buat

---

## 2. Install Fly CLI

### Windows (PowerShell):
```powershell
powershell -Command "iwr https://fly.io/install.ps1 -useb | iex"
```

### macOS (Homebrew):
```bash
brew install flyctl
```

### Linux:
```bash
curl -L https://fly.io/install.sh | sh
```

Setelah install, verifikasi instalasi:
```bash
fly version
```

### Login ke Fly.io:
```bash
fly auth login
```
Browser akan terbuka untuk konfirmasi login/signup.

---

## 3. Deploy ke Fly.io

### Langkah 1: Buka Terminal di Folder Project
```bash
cd D:\Precision-Tasker
```

### Langkah 2: Launch App di Fly.io
Jalankan perintah launch:
```bash
fly launch
```

Ikuti prompt:
1. **App Name**: Masukkan nama unik, misalnya `precision-tasker-candra` (harus unik global)
2. **Select Region**: Pilih `sin` (Singapore)
3. **Would you like to set up a Postgres database?**: Pilih **No** (karena kita pakai Supabase)
4. **Would you like to set up an Upstash Redis database?**: Pilih **No**
5. **Would you like to deploy now?**: Pilih **No** (kita perlu setup secrets dulu)

*Catatan: Jika app name diubah, update field `app = "nama-app-anda"` di `fly.toml`.*

---

## 4. Konfigurasi Secrets (Environment Variables)

Generate `APP_KEY` terlebih dahulu di local:
```bash
php artisan key:generate --show
```
Copy output string yang diawali `base64:...`.

Set semua secrets sekaligus ke Fly.io:
```bash
fly secrets set \
  APP_KEY="base64:OUTPUT_DARI_KEY_GENERATE_TADI" \
  APP_URL="https://nama-app-anda.fly.dev" \
  DB_CONNECTION="pgsql" \
  DB_HOST="db.xxxxxxxxxxxxxx.supabase.co" \
  DB_PORT="5432" \
  DB_DATABASE="postgres" \
  DB_USERNAME="postgres" \
  DB_PASSWORD="password_supabase_anda" \
  DB_SSLMODE="require" \
  SESSION_DRIVER="database" \
  CACHE_STORE="database" \
  QUEUE_CONNECTION="database"
```

*(Opsional) Jika menggunakan Web Push Notifications:*
```bash
# Generate keys di local:
php artisan webpush:vapid

# Tambahkan ke secrets:
fly secrets set \
  VAPID_SUBJECT="mailto:email-anda@example.com" \
  VAPID_PUBLIC_KEY="public_key_hasil_generate" \
  VAPID_PRIVATE_KEY="private_key_hasil_generate"
```

---

## 5. Deploy App

Jalankan perintah deploy:
```bash
fly deploy
```

Fly.io akan:
1. Build Docker image (install dependencies, compile assets dengan Vite)
2. Push image ke registry Fly.io
3. Jalankan container di Singapore
4. Menjalankan auto-migration database Supabase
5. Cache config, routes, dan views

Setelah selesai, buka aplikasi:
```bash
fly open
```
Atau akses URL: `https://nama-app-anda.fly.dev`

---

## 6. Monitoring & Maintenance

### Melihat Logs Real-time:
```bash
fly logs
```

### Cek Status Aplikasi & Resource:
```bash
fly status
```

### Masuk ke Shell / SSH Server:
```bash
fly ssh console
```

### Jalankan Command Artisan di Production:
```bash
fly ssh console -C "php artisan migrate"
fly ssh console -C "php artisan cache:clear"
fly ssh console -C "php artisan route:list"
```

### Scale / Resize Memory (jika butuh):
Free tier mencakup sampai 256MB RAM per VM. Jika ingin upgrade:
```bash
fly scale memory 512
```

---

## 7. Custom Domain & SSL (Gratis)

Jika Anda memiliki domain sendiri (misal `tasker.domainanda.com`):

1. Daftarkan domain ke Fly.io:
   ```bash
   fly certs add tasker.domainanda.com
   ```
2. Tambahkan CNAME record di DNS provider Anda (Cloudflare, Niagahoster, dll):
   - Type: `CNAME`
   - Name: `tasker`
   - Target: `nama-app-anda.fly.dev`
3. Fly.io akan otomatis menerbitkan SSL Certificate (Let's Encrypt) gratis!

---

## 8. Troubleshooting

### 1. Database Connection Error / Timeout
- Pastikan password Supabase tidak mengandung karakter spesial yang tidak ter-escape, atau gunakan kutip saat `fly secrets set`
- Pastikan `DB_SSLMODE=require`
- Cek apakah project Supabase dalam status Active (bukan paused)

### 2. Migration Error saat Deploy
- Cek logs: `fly logs`
- Manual migrate via SSH:
  ```bash
  fly ssh console -C "php artisan migrate --force"
  ```

### 3. CSS/JS Tidak Load
- Assets sudah otomatis di-build via `npm run build` di Dockerfile
- Pastikan `APP_URL` sudah diset ke domain Fly.io Anda (`https://nama-app-anda.fly.dev`)

### 4. App Restart / Out of Memory
- Jika app butuh memory lebih saat build/boot, upgrade VM memory:
  ```bash
  fly scale memory 512
  ```

---

## 💰 Estimasi Biaya

| Service | Plan | Biaya |
|---------|------|-------|
| **Fly.io** | Free Tier (up to 3 shared VMs, 256MB) | **$0 / bulan** |
| **Supabase** | Free Tier (500MB DB, 2GB egress) | **$0 / bulan** |
| **SSL Certificate** | Automatic by Fly.io | **$0 / bulan** |
| **Total** | | **$0 / bulan (FREE FOREVER)** |

---

Selamat mendeploy! Jika ada pertanyaan atau kendala saat deployment, tanyakan saja langsung! 🚀
