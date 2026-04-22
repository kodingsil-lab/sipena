# DEPLOY TEMPLATE SIPENA (Reusable Multi Domain)

Dokumen ini adalah template deploy SIPENA yang bisa dipakai untuk domain-domain lain dengan pola yang sama.

## 1) Parameter Wajib (Ganti Nilainya)

```bash
APP_BASE=/home/loaunisa/apps/sipena
SRC_DIR=$APP_BASE/sipena-app-src
APP_ROOT=$APP_BASE/sipena-app
SHARED_ENV=$APP_BASE/shared/.env
PUBLIC_ROOT=/home/loaunisa/DOMAIN_ANDA
REPO_URL=https://github.com/kodingsil-lab/sipena.git
DOMAIN_URL=https://DOMAIN_ANDA/
```

Contoh untuk target sekarang:

```bash
APP_BASE=/home/loaunisa/apps/sipena
SRC_DIR=$APP_BASE/sipena-app-src
APP_ROOT=$APP_BASE/sipena-app
SHARED_ENV=$APP_BASE/shared/.env
PUBLIC_ROOT=/home/loaunisa/sipena.demo.sil.web.id
REPO_URL=https://github.com/kodingsil-lab/sipena.git
DOMAIN_URL=https://sipena.demo.sil.web.id/
```

## 2) First Deploy (Server Baru / Domain Baru)

### 2.1 Clone source bersih

```bash
mkdir -p "$APP_BASE"
cd "$APP_BASE"

[ -d "$SRC_DIR" ] && mv "$SRC_DIR" "${SRC_DIR}.bak.$(date +%F-%H%M%S)"

git clone "$REPO_URL" "$SRC_DIR"
```

### 2.2 Verifikasi file deploy tersedia

```bash
ls -la "$SRC_DIR/scripts/sipena.sh"
ls -la "$SRC_DIR/.env.production.example"
```

### 2.3 Siapkan `.env` shared

```bash
mkdir -p "$APP_BASE/shared"
cp "$SRC_DIR/.env.production.example" "$SHARED_ENV"
nano "$SHARED_ENV"
```

Isi minimal `.env`:

```ini
CI_ENVIRONMENT = production
app.baseURL = 'https://sipena.demo.sil.web.id/'
app.forceGlobalSecureRequests = true
app.indexPage = ''
app.CSPEnabled = true

database.default.hostname = localhost
database.default.database = DB_NAME
database.default.username = DB_USER
database.default.password = DB_PASS
database.default.DBDriver = MySQLi
database.default.port = 3306
database.default.DBDebug = false
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_unicode_ci
```

### 2.4 Install composer.phar (jika composer global tidak ada)

```bash
cd "$SRC_DIR"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --filename=composer.phar
php -r "unlink('composer-setup.php');"
php composer.phar --version
```

### 2.5 Jalankan deploy pertama

```bash
cd "$SRC_DIR"
RUN_SEEDERS=0 RUN_MIGRATIONS=1 bash ./scripts/sipena.sh

cd "$APP_ROOT"
php spark cache:clear
```

### 2.6 Verifikasi online

```bash
curl -I "$DOMAIN_URL"
```

## 3) Deploy Rutin (Update Kode)

Jalankan blok ini utuh di setiap sesi shell (aman untuk copy-paste):

```bash
APP_BASE=/home/loaunisa/apps/sipena
SRC_DIR=$APP_BASE/sipena-app-src
APP_ROOT=$APP_BASE/sipena-app

[ -d "$SRC_DIR/.git" ] || { echo "Repo tidak ditemukan di $SRC_DIR"; exit 1; }

cd "$SRC_DIR"
git pull origin main
RUN_SEEDERS=0 RUN_MIGRATIONS=1 bash ./scripts/sipena.sh

cd "$APP_ROOT"
php spark cache:clear
```

Jika database sudah final dan tidak mau ubah skema:

```bash
APP_BASE=/home/loaunisa/apps/sipena
SRC_DIR=$APP_BASE/sipena-app-src
APP_ROOT=$APP_BASE/sipena-app

[ -d "$SRC_DIR/.git" ] || { echo "Repo tidak ditemukan di $SRC_DIR"; exit 1; }

cd "$SRC_DIR"
git pull origin main
RUN_SEEDERS=0 RUN_MIGRATIONS=0 bash ./scripts/sipena.sh

cd "$APP_ROOT"
php spark cache:clear
```

## 4) Catatan Penting

- Jalankan script deploy dari `SRC_DIR`, bukan dari `APP_ROOT`.
- Jalankan `php spark ...` dari `APP_ROOT`, bukan dari `SRC_DIR`.
- Jika error `Composer tidak ditemukan`, pastikan `composer.phar` ada di `SRC_DIR`.
- Jika server tidak punya `rsync`, script akan fallback ke metode `tar`.
- Jangan aktifkan seeder di production kecuali benar-benar diperlukan.

## 8) Troubleshooting Umum Cepat

### 8.1 `fatal: not a git repository`

```bash
APP_BASE=/home/loaunisa/apps/sipena
SRC_DIR=$APP_BASE/sipena-app-src

echo "SRC_DIR=$SRC_DIR"
ls -la "$SRC_DIR"
```

Jika folder tidak ada:

```bash
mkdir -p "$APP_BASE"
git clone https://github.com/kodingsil-lab/sipena.git "$SRC_DIR"
```

### 8.2 `No such file or directory: ./scripts/sipena.sh`

Pastikan kamu sedang di `SRC_DIR`:

```bash
cd "$SRC_DIR"
ls -la scripts/sipena.sh
```

### 8.3 `Failed opening ... vendor/codeigniter4/framework/system/Boot.php` saat `php spark`

Biasanya karena menjalankan `php spark` di `SRC_DIR`. Jalankan dari `APP_ROOT`:

```bash
cd "$APP_ROOT"
php spark
```

## 5) Cek Log Saat Error 500/Blank

```bash
# Apache domain log (umum)
tail -n 200 /home/loaunisa/sipena.demo.sil.web.id/error_log

# Cek log aplikasi CI4
tail -n 200 /home/loaunisa/apps/sipena/sipena-app/writable/logs/*.log
```

## 6) Checklist DNS/SSL Sebelum Go-Live

```bash
DOMAIN=sipena.demo.sil.web.id
dig +short "$DOMAIN"
curl -I "https://$DOMAIN"
```

## 7) Troubleshooting Redirect Domain Lama

```bash
APP_BASE=/home/loaunisa/apps/sipena
grep -n "app.baseURL" "$APP_BASE/shared/.env"
```

Jika belum sesuai domain aktif:

```bash
sed -i "s#^app\.baseURL.*#app.baseURL = 'https://sipena.demo.sil.web.id/'#" "$APP_BASE/shared/.env"
sed -i "s#^app\.forceGlobalSecureRequests.*#app.forceGlobalSecureRequests = true#" "$APP_BASE/shared/.env"
```

Redeploy lalu clear cache:

```bash
cd /home/loaunisa/apps/sipena/sipena-app-src
RUN_SEEDERS=0 RUN_MIGRATIONS=0 bash ./scripts/sipena.sh
cd /home/loaunisa/apps/sipena/sipena-app
php spark cache:clear
```
