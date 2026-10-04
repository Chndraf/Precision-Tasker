# Deployment Guide - Render.com + Supabase (100% FREE NO CREDIT CARD)

Panduan deploy Laravel ke **Render.com** dengan database **Supabase**. **100% GRATIS tanpa kartu kredit!** 🎉

---

## 📋 Daftar Isi
1. [Setup Supabase Database](#1-setup-supabase-database)
2. [Push Code ke GitHub](#2-push-code-ke-github)
3. [Deploy ke Render.com](#3-deploy-ke-rendercom)
4. [Konfigurasi Environment Variables](#4-konfigurasi-environment-variables)
5. [Monitoring & Maintenance](#5-monitoring--maintenance)
6. [Mengatasi Limitasi Free Tier](#6-mengatasi-limitasi-free-tier-app-sleep)

---

## 1. Setup Supabase Database

### Langkah 1: Buat Project Supabase
1. Buka [https://supabase.com](https://supabase.com) dan login/signup (bisa pakai GitHub)
2. Klik **"New Project"**
3. Isi informasi project:
   - **Name**: `precision-tasker`
   - **Database Password**: Buat password yang kuat (⚠️ SIMPAN PASSWORD INI!)
   - **Region**: Pilih **Singapore (`ap-southeast-1`)**
4. Klik **"Create new project"** dan tunggu ~2 menit sampai database siap

### Langkah 2: Dapatkan Connection Info
1. Di dashboard Supabase, buka menu **Settings (⚙️)** > **Database**
2. Scroll ke bagian **Connection parameters**:
   - **Host**: `db.xxxxxxxxxxxxxx.supabase.co`
   - **Port**: `5432`
   - **Database**: `postgres`
   - **User**: `postgres`
   - **Password**: Password yang Anda buat tadi
postgresql://postgres:[buatPasswordKuat123!]@db.kokoixmydwpgtikofmbe.supabase.co:5432/postgres
---

## 2. Push Code ke GitHub

Buka terminal di folder project dan push semua perubahan terbaru:

```bash
cd D:\Precision-Tasker

git add .
git commit -m "Setup for Render.com deployment"
git push origin main
```

---

## 3. Deploy ke Render.com

### Langkah 1: Buat Akun Render
1. Buka [https://render.com](https://render.com)
2. Sign up menggunakan akun **GitHub** Anda (tanpa credit card!)

### Langkah 2: Buat Web Service Baru
1. Di dashboard Render, klik tombol **"New +"** (kanan atas)
2. Pilih **"Web Service"**
3. Hubungkan repository GitHub Anda:
   - Jika belum terhubung, klik **"Connect account"**
   - Pilih repository **`Precision-Tasker`** (atau `Chndraf/Precision-Tasker`)
   - Klik **"Connect"**

### Langkah 3: Konfigurasi Web Service
Isi formulir dengan detail berikut:
- **Name**: `precision-tasker` (atau nama pilihan Anda)
- **Region**: **Singapore** (agar latency rendah dengan Supabase)
- **Branch**: `main`
- **Root Directory**: *(kosongkan)*
- **Runtime**: **Docker** *(Render akan otomatis membaca Dockerfile yang sudah dibuat!)*
- **Instance Type**: Pilih **Free** ($0/month)

---

## 4. Konfigurasi Environment Variables

Sebelum klik Deploy, scroll ke bagian **"Advanced"** > **"Add Environment Variable"**.

Tambahkan variabel-variabel berikut satu per satu:

| Key | Value | Keterangan |
|-----|-------|------------|
| `APP_NAME` | `Precision Tasker` | Nama aplikasi |
| `APP_ENV` | `production` | Environment |
| `APP_KEY` | *(Generate via: `php artisan key:generate --show`)* | Key encryption Laravel |
| `APP_DEBUG` | `false` | Disable debug mode |
| `APP_URL` | `https://precision-tasker.onrender.com` | Sesuaikan dengan nama service Anda |
| `DB_CONNECTION` | `pgsql` | Driver PostgreSQL |
| `DB_HOST` | `db.xxxxxxxxxxxxxx.supabase.co` | Host Supabase Anda |
| `DB_PORT` | `5432` | Port Postgres |
| `DB_DATABASE` | `postgres` | Database Supabase |
| `DB_USERNAME` | `postgres` | Username Supabase |
| `DB_PASSWORD` | `password_supabase_anda` | Password Supabase Anda |
| `DB_SSLMODE` | `require` | SSL connection |
| `SESSION_DRIVER` | `database` | Simpan session di database |
| `CACHE_STORE` | `database` | Simpan cache di database |
| `QUEUE_CONNECTION` | `database` | Queue worker |
| `LOG_CHANNEL` | `stderr` | Kirim log ke Render logs |

> **Tips Generate APP_KEY:**
> Jalankan perintah ini di terminal lokal Anda:
> ```bash
> php artisan key:generate --show
> ```
> Copy hasilnya (misal: `base64:XOnunq89dyFM/OPrQGbic/bJjTu2vLsvHLIHPJB0X/s=`) dan masukkan ke variabel `APP_KEY`.

### Langkah 4: Klik Deploy!
Klik tombol **"Create Web Service"** di bagian bawah.

Render akan mulai:
1. Men-download repository
2. Build Docker container (Composer, NPM build Vite)
3. Menjalankan migrasi database ke Supabase secara otomatis
4. Menyalakan web server

Tunggu proses build selesai (~3-5 menit). Setelah status berubah menjadi **"Live"**, klik URL yang diberikan (contoh: `https://precision-tasker.onrender.com`).

---

## 5. Monitoring & Maintenance

### Melihat Logs:
- Di dashboard Render, klik service Anda > klik tab **"Logs"**
- Semua aktivitas error atau request akan muncul secara live.

### Auto Deploy:
- Setiap kali Anda melakukan `git push origin main`, Render akan otomatis mendeteksi perubahan dan melakukan re-deploy.

---

## 6. Mengatasi Limitasi Free Tier (App Sleep)

Di Render Free Tier, server akan **"tidur" (sleep)** jika tidak ada traffic selama 15 menit. Request pertama setelah tidur akan memakan waktu ~30-50 detik (cold start).

### 💡 Trik Menjaga Server Selalu Aktif (Always-On):
Gunakan layanan cron/uptime monitor gratis seperti **UptimeRobot** atau **Cron-job.org**:

1. Buka [https://uptimerobot.com](https://uptimerobot.com) (Gratis)
2. Klik **"Add New Monitor"**
3. Monitor Type: **HTTP(s)**
4. Friendly Name: `Precision Tasker Ping`
5. URL: `https://precision-tasker.onrender.com/up`
6. Monitoring Interval: **Every 10 minutes**
7. Klik **"Create Monitor"**

Dengan cara ini, UptimeRobot akan me-request server setiap 10 menit sehingga **server Anda tidak akan pernah tidur (always on 24/7)**! 🚀

---

## 💰 Biaya Total
- **Render.com**: $0 (Free plan)
- **Supabase**: $0 (Free plan)
- **UptimeRobot**: $0 (Free plan)
- **Total**: **Rp 0 / GRATIS SELAMANYA** 🎉
