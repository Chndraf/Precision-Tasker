# Panduan Deploy Precision Tasker ke Vercel + Supabase (100% GRATIS)

Deploy fullstack Laravel Blade ke **Vercel** dengan database **Supabase**. **100% GRATIS dan TANPA KARTU KREDIT**.

---

## 📋 Langkah-langkah Deployment

---

### Langkah 1: Setup Supabase Database (2 Menit)

1. Buka [https://supabase.com](https://supabase.com) dan login/signup dengan **GitHub**.
2. Klik **"New Project"**.
3. Isi:
   - **Name**: `precision-tasker`
   - **Database Password**: *(Buat password yang kuat dan catat!)*
   - **Region**: **Singapore (`ap-southeast-1`)**
4. Klik **"Create new project"** dan tunggu ~2 menit.
5. Buka **Project Settings (⚙️)** > **Database** > scroll ke **Connection parameters**:
   - **Host**: `db.xxxxxxxxxxxxxx.supabase.co`
   - **Port**: `5432`
   - **Database**: `postgres`
   - **User**: `postgres`
   - **Password**: `password_yang_anda_buat_tadi`

---

### Langkah 2: Jalankan Migrasi Database dari Komputer Lokal ke Supabase (1 Menit)

Sebelum deploy ke Vercel, kita jalankan migrasi tabel Laravel langsung ke Supabase dari terminal lokal Anda.

1. Buka file `.env` di project lokal Anda (`D:\Precision-Tasker\.env`), sesuaikan bagian database:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=db.xxxxxxxxxxxxxx.supabase.co
   DB_PORT=5432
   DB_DATABASE=postgres
   DB_USERNAME=postgres
   DB_PASSWORD=password_supabase_anda
   DB_SSLMODE=require
   ```

2. Buka terminal dan jalankan migration:
   ```bash
   php artisan migrate --force
   ```
   *(Semua tabel seperti `users`, `tasks`, `courses`, `sessions`, dll akan langsung otomatis terbuat di Supabase!)*

---

### Langkah 3: Push Perubahan ke GitHub

Buka terminal dan push konfigurasi Vercel:

```bash
cd D:\Precision-Tasker

git add .
git commit -m "Setup fullstack Laravel for Vercel deployment with Supabase"
git push origin main
```

---

### Langkah 4: Deploy ke Vercel (3 Menit)

1. Buka [https://vercel.com](https://vercel.com) dan Login menggunakan **GitHub** (Gratis, Tanpa Kartu Kredit!).
2. Klik **"Add New..."** > **"Project"**.
3. Cari repository **`Precision-Tasker`** dan klik **"Import"**.
4. Di bagian **Configure Project**:
   - **Framework Preset**: Pilih **Other** (jangan ubah)
   - **Root Directory**: `./` (default)
5. Buka dropdown **"Environment Variables"** dan tambahkan variabel berikut satu per satu:

| Key | Value |
| :--- | :--- |
| `APP_NAME` | `Precision Tasker` |
| `APP_ENV` | `production` |
| `APP_KEY` | *(Generate via: `php artisan key:generate --show`)* |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://nama-project-anda.vercel.app` |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | `db.xxxxxxxxxxxxxx.supabase.co` |
| `DB_PORT` | `5432` |
| `DB_DATABASE` | `postgres` |
| `DB_USERNAME` | `postgres` |
| `DB_PASSWORD` | `password_supabase_anda` |
| `DB_SSLMODE` | `require` |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `LOG_CHANNEL` | `stderr` |

6. Klik tombol **"Deploy"**!

---

### 🚀 Selesai!

Vercel akan meng-compile assets Vite (Tailwind CSS & JavaScript) dan menyalakan serverless PHP runtime. Dalam 1-2 menit, aplikasi Anda sudah aktif di domain:
`https://precision-tasker-xxxx.vercel.app`

---

## 💡 Keuntungan Setup Vercel Ini:
- ✅ **100% Gratis Selamanya**
- ✅ **Tanpa Perlu Kartu Kredit / Debit**
- ✅ **CDN Global Super Cepat**
- ✅ **SSL/HTTPS Otomatis Aktif**
- ✅ **Terkoneksi Langsung ke Supabase PostgreSQL**
