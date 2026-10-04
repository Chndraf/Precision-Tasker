# 🚀 Panduan Deploy Laravel + Blade ke Vercel + Supabase

**Panduan lengkap untuk deploy fullstack Laravel dengan Blade template ke Vercel (serverless) dan Supabase (database PostgreSQL) tanpa kartu kredit.**

---

## 📋 Ringkasan Setup

| Komponen | Platform | Biaya |
|----------|----------|-------|
| Backend (Laravel PHP) | Vercel Serverless | **GRATIS** |
| Frontend (Blade + Vite) | Vercel CDN | **GRATIS** |
| Database (PostgreSQL) | Supabase | **GRATIS** (500MB) |
| SSL Certificate | Auto by Vercel | **GRATIS** |
| **Total Biaya** | | **Rp 0 / bulan** ✅ |

---

## 🗂️ Struktur File yang Dibutuhkan

Pastikan project Laravel Anda memiliki file-file berikut:

```
/project-root
├── api/
│   └── index.php              # Serverless entry point
├── .fly/                      # (optional, bisa diabaikan)
├── bootstrap/
│   └── app.php                # Update untuk Vercel storage path
├── config/
│   └── database.php           # Konfigurasi PostgreSQL
├── public/
│   └── build/                 # Compiled Vite assets
├── routes/
│   ├── web.php
│   └── setup.php              # Endpoint untuk migration (hapus setelah deploy)
├── .dockerignore
├── .vercelignore
├── vercel.json                # Konfigurasi Vercel
├── package.json
├── vite.config.js
└── composer.json
```

---

## 📝 Langkah 1: Setup Supabase Database

### 1.1. Buat Project Supabase
1. Buka [https://supabase.com](https://supabase.com) dan **login dengan GitHub**
2. Klik **"New Project"**
3. Isi form:
   - **Organization**: Pilih atau buat baru
   - **Name**: `nama-project-anda` (misal: `my-laravel-app`)
   - **Database Password**: Buat password kuat dan **SIMPAN** (sangat penting!)
   - **Region**: Pilih **Singapore (`ap-southeast-1`)** untuk latency rendah dari Indonesia
   - **Pricing Plan**: **Free**
4. Klik **"Create new project"**
5. Tunggu ~2-3 menit sampai status **"Project is ready"**

### 1.2. Dapatkan Connection Pooler Credentials
> **Penting:** Untuk serverless (Vercel), gunakan **Connection Pooler**, bukan direct connection!

1. Di dashboard Supabase, buka **Settings** (⚙️) > **Database**
2. Scroll ke bagian **"Connection Pooling"**
3. Pilih **Mode: Session** (untuk Laravel)
4. Catat informasi berikut:
   - **Host**: `aws-0-ap-southeast-1.pooler.supabase.com`
   - **Port**: `6543` (bukan 5432!)
   - **Database**: `postgres`
   - **User**: `postgres.[project-ref]` (misal: `postgres.abcdefghijk`)
   - **Password**: Password yang Anda buat tadi

---

## 📝 Langkah 2: Konfigurasi File Project

### 2.1. Buat File `api/index.php`
```php
<?php

// Error handling untuk debugging
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Cek apakah vendor/autoload.php ada
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    http_response_code(500);
    die(json_encode(['error' => 'Composer dependencies not installed']));
}

// Buat direktori storage untuk Vercel serverless
$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Set environment variables untuk cache di /tmp
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');

// Jalankan aplikasi Laravel
try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ], JSON_PRETTY_PRINT);
}
```

### 2.2. Buat File `vercel.json`
```json
{
  "version": 2,
  "framework": null,
  "buildCommand": "composer install --no-dev --optimize-autoloader && npm ci && npm run build",
  "outputDirectory": "public",
  "functions": {
    "api/index.php": {
      "runtime": "vercel-php@0.7.3"
    }
  },
  "routes": [
    {
      "src": "/build/(.*)",
      "dest": "/public/build/$1"
    },
    {
      "src": "/(favicon\\.ico|robots\\.txt|manifest\\.json)",
      "dest": "/public/$1"
    },
    {
      "src": "/(.*)",
      "dest": "/api/index.php"
    }
  ],
  "env": {
    "APP_ENV": "production",
    "APP_DEBUG": "false",
    "LOG_CHANNEL": "stderr",
    "SESSION_DRIVER": "database",
    "CACHE_STORE": "database"
  }
}
```

### 2.3. Update `bootstrap/app.php`
Tambahkan konfigurasi untuk Vercel storage:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\LocalizationMiddleware::class,
        ]);
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Gunakan /tmp/storage untuk Vercel serverless
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $app->useStoragePath('/tmp/storage');
}

return $app;
```

### 2.4. Update `config/database.php`
Pastikan default connection adalah PostgreSQL:

```php
'default' => env('DB_CONNECTION', 'pgsql'),

'connections' => [
    'pgsql' => [
        'driver' => 'pgsql',
        'url' => env('DB_URL'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '5432'),
        'database' => env('DB_DATABASE', 'postgres'),
        'username' => env('DB_USERNAME', 'postgres'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => env('DB_CHARSET', 'utf8'),
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => env('DB_SSLMODE', 'require'),
    ],
],
```

### 2.5. Buat File `routes/setup.php` (Temporary)
> File ini untuk migration pertama kali, **hapus setelah migration berhasil!**

```php
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/setup-database', function () {
    if (app()->environment('production')) {
        try {
            Artisan::call('migrate', ['--force' => true]);
            return response()->json([
                'status' => 'success',
                'message' => 'Database migrated successfully!',
                'output' => Artisan::output()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    return response()->json(['status' => 'error', 'message' => 'Only available in production']);
});
```

Lalu daftarkan di `bootstrap/app.php`:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
    then: function () {
        if (file_exists(__DIR__.'/../routes/setup.php')) {
            require __DIR__.'/../routes/setup.php';
        }
    },
)
```

### 2.6. Buat File `.vercelignore`
```
.git
.env
.env.*
!.env.example
/storage/logs/*
/storage/framework/cache/*
/storage/framework/sessions/*
/storage/framework/views/*
/node_modules
/tests
/vendor
```

---

## 📝 Langkah 3: Push Code ke GitHub

```bash
cd /path/to/your/laravel-project

# Initialize git (jika belum)
git init
git add .
git commit -m "Setup for Vercel + Supabase deployment"

# Push ke GitHub
git remote add origin https://github.com/username/repo-name.git
git branch -M main
git push -u origin main
```

---

## 📝 Langkah 4: Deploy ke Vercel

### 4.1. Buat Project di Vercel
1. Buka [https://vercel.com](https://vercel.com)
2. **Login dengan GitHub** (gratis, tanpa kartu kredit!)
3. Klik **"Add New..."** > **"Project"**
4. Pilih repository GitHub Anda
5. Klik **"Import"**

### 4.2. Konfigurasi Project Settings
Di halaman "Configure Project":
- **Framework Preset**: Pilih **"Other"**
- **Root Directory**: `./` (default)
- **Build Command**: *(kosongkan, sudah ada di vercel.json)*
- **Output Directory**: *(kosongkan, sudah ada di vercel.json)*

### 4.3. Tambahkan Environment Variables
Klik **"Environment Variables"** dan tambahkan **SEMUA** variabel berikut:

| Key | Value | Environment |
|-----|-------|-------------|
| `APP_NAME` | `Nama Aplikasi Anda` | Production |
| `APP_ENV` | `production` | Production |
| `APP_KEY` | `base64:xxx...` *(generate via `php artisan key:generate --show`)* | Production |
| `APP_DEBUG` | `false` | Production |
| `APP_URL` | `https://nama-project.vercel.app` | Production |
| `DB_CONNECTION` | `pgsql` | Production |
| `DB_HOST` | `aws-0-ap-southeast-1.pooler.supabase.com` | Production |
| `DB_PORT` | `6543` | Production |
| `DB_DATABASE` | `postgres` | Production |
| `DB_USERNAME` | `postgres.[project-ref]` | Production |
| `DB_PASSWORD` | `(password Supabase Anda)` | Production |
| `DB_SSLMODE` | `require` | Production |
| `SESSION_DRIVER` | `database` | Production |
| `CACHE_STORE` | `database` | Production |
| `LOG_CHANNEL` | `stderr` | Production |

> **Cara generate APP_KEY:** Jalankan `php artisan key:generate --show` di terminal lokal

### 4.4. Deploy!
Klik tombol **"Deploy"**. Vercel akan:
1. Clone repository dari GitHub
2. Install Composer dependencies
3. Install NPM dependencies
4. Build frontend assets dengan Vite
5. Deploy ke serverless infrastructure

Tunggu ~2-3 menit sampai status **"Ready"** ✅

---

## 📝 Langkah 5: Jalankan Database Migration

Setelah deployment berhasil:

1. **Akses endpoint setup:**
   ```
   https://nama-project.vercel.app/setup-database
   ```

2. **Verifikasi response JSON:**
   ```json
   {
     "status": "success",
     "message": "Database migrated successfully!",
     "output": "..."
   }
   ```

3. **Hapus endpoint setup** (keamanan):
   ```bash
   git rm routes/setup.php
   git commit -m "Remove setup endpoint after migration"
   git push origin main
   ```

4. **Akses aplikasi:**
   ```
   https://nama-project.vercel.app
   ```

---

## ✅ Verifikasi Deployment Berhasil

**Checklist:**
- [ ] Buka `https://nama-project.vercel.app` → Aplikasi tampil tanpa error
- [ ] Klik **Register** → Bisa membuat akun baru
- [ ] **Login** dengan akun yang baru dibuat
- [ ] Test semua fitur utama (CRUD, dll)
- [ ] Cek CSS/JS sudah load dengan benar
- [ ] Cek di Supabase Table Editor → Data user muncul

---

## 🔧 Troubleshooting Common Issues

### Issue 1: Error 500 - Database Connection Failed
**Gejala:**
```json
{
  "database": "❌ Failed: Cannot assign requested address"
}
```

**Solusi:** Gunakan **Connection Pooler** Supabase (port `6543`), bukan direct connection (port `5432`).

---

### Issue 2: Error 500 - Tables Not Found
**Gejala:**
```json
{
  "tables": []
}
```

**Solusi:** Akses `/setup-database` untuk menjalankan migration.

---

### Issue 3: CSS/JS Tidak Load (404)
**Gejala:** Halaman tampil tapi styling hilang.

**Solusi:**
1. Pastikan `npm run build` berjalan saat deployment
2. Cek `vercel.json` → `buildCommand` sudah include `npm run build`
3. Verifikasi routing `/build/(.*)` ada di `vercel.json`

---

### Issue 4: Session Tidak Persist / Logout Terus
**Solusi:**
1. Pastikan `SESSION_DRIVER=database` di environment variables
2. Pastikan tabel `sessions` sudah ada di database
3. Set `SESSION_DOMAIN` ke `.vercel.app` (dengan titik di depan)

---

## 🎯 Best Practices

### Security:
- ✅ Selalu gunakan `APP_DEBUG=false` di production
- ✅ Hapus endpoint debug/setup setelah deployment berhasil
- ✅ Jangan commit file `.env` ke GitHub
- ✅ Gunakan environment variables yang kuat (password, APP_KEY)

### Performance:
- ✅ Gunakan Connection Pooler Supabase untuk serverless
- ✅ Cache config dengan `php artisan config:cache` (otomatis di build)
- ✅ Optimize Composer dengan `--optimize-autoloader`
- ✅ Build assets production dengan `npm run build` (otomatis)

### Maintenance:
- ✅ Monitor error di Vercel Logs: `vercel logs`
- ✅ Monitor database di Supabase Dashboard > Database
- ✅ Backup database reguler (Supabase Pro plan: auto backup)

---

## 📚 Resources

- **Vercel Docs**: https://vercel.com/docs
- **Supabase Docs**: https://supabase.com/docs
- **Laravel Docs**: https://laravel.com/docs
- **Vercel PHP Runtime**: https://github.com/vercel-community/php

---

## 🎉 Selesai!

Aplikasi Laravel + Blade Anda sudah **LIVE** di Vercel dengan database Supabase, **100% GRATIS dan tanpa kartu kredit!**

**Domain:** `https://nama-project.vercel.app`

**Upgrade Options:**
- Custom domain: Gratis di Vercel Settings
- Supabase Pro: $25/bulan (8GB database, daily backups, priority support)
- Vercel Pro: $20/bulan (unlimited bandwidth, team features)

---

**Dibuat oleh:** Kiro AI Assistant
**Terakhir diupdate:** 2026-10-04
