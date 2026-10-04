# Precision Tasker - Deployment Guide

## 📋 Daftar Isi
- [Setup Supabase Database](#setup-supabase-database)
- [Deploy ke Railway](#deploy-ke-railway)
- [Konfigurasi Environment Variables](#konfigurasi-environment-variables)
- [Post-Deployment Setup](#post-deployment-setup)
- [Troubleshooting](#troubleshooting)

---

## 🗄️ Setup Supabase Database

### 1. Buat Project Supabase
1. Kunjungi [https://supabase.com](https://supabase.com)
2. Sign up / Login
3. Klik **"New Project"**
4. Isi detail project:
   - **Name**: precision-tasker
   - **Database Password**: Buat password yang kuat (simpan ini!)
   - **Region**: Pilih yang terdekat dengan Anda
5. Tunggu project selesai dibuat (~2 menit)

### 2. Dapatkan Database Credentials
1. Di dashboard Supabase, pilih project Anda
2. Klik **Settings** (ikon gear) di sidebar
3. Klik **Database**
4. Scroll ke bagian **Connection String**
5. Pilih tab **URI** dan copy connection string
6. Format akan seperti ini:
   ```
   postgresql://postgres:[YOUR-PASSWORD]@db.xxxxxxxxxxxxxx.supabase.co:5432/postgres
   ```

7. Extract informasi berikut:
   - **DB_HOST**: `db.xxxxxxxxxxxxxx.supabase.co`
   - **DB_PORT**: `5432`
   - **DB_DATABASE**: `postgres`
   - **DB_USERNAME**: `postgres`
   - **DB_PASSWORD**: password yang Anda buat tadi

---

## 🚂 Deploy ke Railway

### 1. Persiapan Repository GitHub
1. Push project ini ke GitHub repository Anda:
   ```bash
   git add .
   git commit -m "Setup for Railway deployment with Supabase"
   git push origin main
   ```

### 2. Setup Railway Account
1. Kunjungi [https://railway.app](https://railway.app)
2. Sign up dengan GitHub account
3. Anda akan mendapat $5 trial credit

### 3. Deploy Project
1. Di Railway dashboard, klik **"New Project"**
2. Pilih **"Deploy from GitHub repo"**
3. Authorize Railway untuk akses GitHub Anda
4. Pilih repository **Precision-Tasker**
5. Railway akan otomatis detect Laravel dan mulai build

### 4. Konfigurasi Environment Variables
1. Setelah deployment selesai, klik project Anda
2. Klik tab **"Variables"**
3. Tambahkan environment variables berikut:

```env
APP_NAME=Precision Tasker
APP_ENV=production
APP_KEY=base64:xxx  # Generate dulu (lihat di bawah)
APP_DEBUG=false
APP_URL=https://your-app-name.up.railway.app

# Supabase Database (dari langkah sebelumnya)
DB_CONNECTION=pgsql
DB_HOST=db.xxxxxxxxxxxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-password
DB_SSLMODE=require

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail (optional, untuk reset password)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Precision Tasker"
```

### 5. Generate APP_KEY
Cara 1 - Via Railway CLI:
```bash
railway run php artisan key:generate --show
```

Cara 2 - Local kemudian copy:
```bash
php artisan key:generate --show
# Copy output yang muncul (format: base64:xxxxx)
```

Paste hasil generate ke variable `APP_KEY` di Railway.

### 6. Generate Domain
1. Di Railway project, klik tab **"Settings"**
2. Scroll ke **"Domains"**
3. Klik **"Generate Domain"**
4. Anda akan dapat URL seperti: `https://precision-tasker-production-xxxx.up.railway.app`
5. Copy URL ini dan update variable `APP_URL` di Railway

### 7. Redeploy
1. Klik tab **"Deployments"**
2. Klik tombol **"Redeploy"** untuk apply semua environment variables

---

## 🔐 Post-Deployment Setup

### 1. Verifikasi Database Migration
Migrations akan otomatis berjalan saat deployment (via `Procfile`).

Cek logs di Railway:
1. Klik tab **"Deployments"**
2. Klik deployment terbaru
3. Lihat logs, cari baris:
   ```
   Running migrations...
   Migrating: 0001_01_01_000000_create_users_table
   Migrated:  0001_01_01_000000_create_users_table
   ...
   ```

### 2. Setup Web Push Notifications (Optional)
1. Generate VAPID keys locally:
   ```bash
   php artisan webpush:vapid
   ```

2. Copy output dan tambahkan ke Railway variables:
   ```env
   VAPID_SUBJECT=mailto:your-email@example.com
   VAPID_PUBLIC_KEY=xxx
   VAPID_PRIVATE_KEY=xxx
   ```

3. Redeploy aplikasi

### 3. Test Aplikasi
1. Buka URL Railway Anda di browser
2. Klik **Register** dan buat akun
3. Test fitur-fitur:
   - Create course
   - Create task
   - Complete task
   - Check dashboard statistics

---

## 🐛 Troubleshooting

### Error: "Connection refused" atau "Could not connect to database"
**Solusi:**
- Pastikan `DB_HOST`, `DB_PASSWORD`, dan credentials Supabase benar
- Pastikan `DB_SSLMODE=require` sudah diset
- Cek apakah Supabase project masih aktif

### Error: "No application encryption key has been specified"
**Solusi:**
- Generate APP_KEY: `php artisan key:generate --show`
- Paste ke Railway variables
- Redeploy

### Error: "Base table or view not found"
**Solusi:**
- Migrations belum berjalan
- Check logs deployment untuk error saat migration
- Manual migration via Railway CLI:
  ```bash
  railway run php artisan migrate --force
  ```

### CSS/JS tidak load (404 error)
**Solusi:**
- Pastikan build assets sudah berjalan
- Check `nixpacks.toml` sudah include `npm run build`
- Redeploy aplikasi

### Session/Login tidak persist
**Solusi:**
- Pastikan `SESSION_DRIVER=database` 
- Pastikan `sessions` table sudah ada di database
- Set `SESSION_DOMAIN` ke domain Railway Anda (tanpa https://)
  ```env
  SESSION_DOMAIN=.up.railway.app
  ```

---

## 📊 Monitoring & Maintenance

### Cek Resource Usage
- Di Railway dashboard, tab **"Metrics"**
- Monitor CPU, Memory, dan Network usage

### Cek Logs
- Tab **"Deployments"** > Pilih deployment > View logs
- Atau gunakan Railway CLI:
  ```bash
  railway logs
  ```

### Database Management
Gunakan Supabase Table Editor atau SQL Editor:
1. Buka Supabase dashboard
2. Pilih project Anda
3. Klik **"Table Editor"** atau **"SQL Editor"**

---

## 💡 Tips

1. **Backup Database**: Supabase otomatis backup setiap hari untuk plan berbayar
2. **Custom Domain**: Bisa setup custom domain di Railway Settings > Domains
3. **Monitor Errors**: Integrasikan dengan Sentry untuk error tracking
4. **Queue Workers**: Untuk production, consider menambah queue worker:
   ```
   # Tambah di Procfile
   worker: php artisan queue:work --tries=3
   ```

---

## 📞 Support

Jika ada masalah:
- Railway Docs: https://docs.railway.app
- Supabase Docs: https://supabase.com/docs
- Laravel Docs: https://laravel.com/docs

---

## ✅ Checklist Deployment

- [ ] Supabase project dibuat
- [ ] Database credentials sudah dicopy
- [ ] Repository di-push ke GitHub
- [ ] Railway project dibuat dari GitHub repo
- [ ] Environment variables dikonfigurasi
- [ ] APP_KEY di-generate
- [ ] Domain Railway di-generate
- [ ] APP_URL diupdate dengan domain Railway
- [ ] Aplikasi di-redeploy
- [ ] Migration berhasil dijalankan
- [ ] Test register & login berhasil
- [ ] Test create task berhasil
- [ ] (Optional) VAPID keys untuk push notifications

---

**Selamat! Aplikasi Precision Tasker Anda sudah live! 🎉**
