# Quick Start Guide - Deploy Precision Tasker ke Fly.io + Supabase

**Total waktu setup: ~15 menit** | **Biaya: FREE SELAMANYA** 🎉

---

## ✅ Checklist Cepat

### Prerequisites
- [ ] Akun GitHub (untuk push code)
- [ ] Akun Supabase (gratis)
- [ ] Akun Fly.io (gratis)
- [ ] Fly CLI terinstall

---

## 🚀 Langkah Deployment (Copy-Paste Ready)

### 1. Setup Supabase (5 menit)
1. Buka [https://supabase.com](https://supabase.com) → Login
2. **New Project** → Isi:
   - Name: `precision-tasker`
   - Password: `buatPasswordKuat123!` (simpan!)
   - Region: **Singapore**
3. Tunggu 2 menit sampai database ready
4. **Settings** → **Database** → Copy **Connection String**
5. Extract info:
   - Host: `db.xxxxxxxxxxxxx.supabase.co`
   - Password: yang tadi dibuat
postgresql://postgres:[buatPasswordKuat123!]@db.kokoixmydwpgtikofmbe.supabase.co:5432/postgres
---

### 2. Install Fly CLI (2 menit)

**Windows:**
```powershell
powershell -Command "iwr https://fly.io/install.ps1 -useb | iex"
```

**macOS/Linux:**
```bash
curl -L https://fly.io/install.sh | sh
```

**Login:**
```bash
fly auth login
```

---

### 3. Push Code ke GitHub (2 menit)

```bash
cd D:\Precision-Tasker

git add .
git commit -m "Setup for Fly.io deployment"
git push origin main
```

---

### 4. Deploy ke Fly.io (6 menit)

#### Step 1: Generate APP_KEY
```bash
php artisan key:generate --show
```
Copy output (format: `base64:xxxxx...`)

#### Step 2: Launch App
```bash
fly launch
```
- **App name**: `precision-tasker-<namamu>` (harus unik)
- **Region**: `sin` (Singapore)
- **Setup Postgres?**: **No**
- **Setup Redis?**: **No**
- **Deploy now?**: **No**

#### Step 3: Set Secrets (Ganti nilai yang di <...>)
```bash
fly secrets set \
  APP_KEY="<paste_hasil_key_generate>" \
  APP_URL="https://<nama-app-anda>.fly.dev" \
  DB_HOST="<host_supabase>" \
  DB_PASSWORD="<password_supabase>" \
  DB_CONNECTION="pgsql" \
  DB_PORT="5432" \
  DB_DATABASE="postgres" \
  DB_USERNAME="postgres" \
  DB_SSLMODE="require" \
  SESSION_DRIVER="database" \
  CACHE_STORE="database"
```

#### Step 4: Deploy!
```bash
fly deploy
```

Tunggu ~3-5 menit untuk build & deploy.

#### Step 5: Buka App
```bash
fly open
```

---

## 🎉 Selesai! 

Aplikasi Anda sudah LIVE di:
```
https://nama-app-anda.fly.dev
```

---

## 📊 Monitoring

**Lihat logs:**
```bash
fly logs
```

**Cek status:**
```bash
fly status
```

**Jalankan migration manual (jika perlu):**
```bash
fly ssh console -C "php artisan migrate --force"
```

---

## 🔄 Update Code (Push Changes)

Setiap kali ada perubahan code:
```bash
git add .
git commit -m "Update feature X"
git push origin main
fly deploy
```

---

## 💡 Tips

1. **Database penuh?**
   - Free tier Supabase = 500MB (cukup untuk ribuan tasks)
   - Upgrade $25/bulan → 8GB

2. **App lambat?**
   - Free tier = 256MB RAM (cukup untuk traffic ringan-sedang)
   - Upgrade: `fly scale memory 512`

3. **Custom domain?**
   ```bash
   fly certs add yourdomain.com
   ```
   Tambahkan CNAME: `yourdomain.com` → `nama-app-anda.fly.dev`

---

## ⚠️ Troubleshooting Cepat

**Error: "Database connection failed"**
→ Cek password Supabase & `DB_HOST` benar

**Error: "No APP_KEY"**
→ Generate ulang & set secret

**CSS/JS tidak load**
→ Pastikan `APP_URL` sesuai domain Fly.io

---

Untuk panduan lengkap, baca **FLY_DEPLOYMENT.md** 📖
