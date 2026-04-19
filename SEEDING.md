# Panduan Seeder SIPENA (Clear & Cepat)

Panduan ini untuk menjalankan seeder data realistis SIPENA.

## Tujuan Seeder

1. `CleanDummySeeder`
Menghapus data modul (truncate) pada tabel:
`user_penanggung_jawab_proses`, `riwayat_perubahan_dokumen_standar`, `dokumen_standar`, `audit_mutu_internal`, `pedoman_ppepp`, `kebijakan_spmi`, `kebijakan_mutu`, `peraturan`, `standar_mutu`, `profil_institusi`, `users`.

2. `FullDummySeeder`
Menjalankan `CleanDummySeeder` lalu mengisi data realistis penuh, termasuk:
- Standar Mutu 24 item (Pendidikan, Penelitian, Pengabdian kepada Masyarakat)
- Dokumen Standar
- Peraturan
- Kebijakan Mutu
- Kebijakan SPMI
- Pedoman PPEPP (Dokumen + 20 SOP + 20 Formulir)
- Audit Mutu Internal
- Users dan profil institusi

## Jalankan dari Root Project

Pastikan posisi terminal di:
`C:\xampp\htdocs\sipena`

## Command Paling Direkomendasikan (Windows PowerShell)

```powershell
.\scripts\seed.ps1 -Mode clean
.\scripts\seed.ps1 -Mode full
.\scripts\seed.ps1 -Mode reset
```

Jika Execution Policy diblokir:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\seed.ps1 -Mode full
```

## Alternatif Command

1. Composer

```bash
composer seed:clean
composer seed:full
```

2. Spark langsung

```bash
php spark db:seed CleanDummySeeder
php spark db:seed FullDummySeeder
```

3. Linux/macOS (Bash script)

```bash
bash scripts/seed.sh clean
bash scripts/seed.sh full
bash scripts/seed.sh reset
```

## Catatan Penting

1. `full` sudah otomatis melakukan clean dulu.
2. Gunakan `reset` jika ingin eksplisit clean lalu full.
3. Untuk PowerShell, selalu gunakan prefix `.\` saat memanggil script lokal.
4. Jangan pakai titik di belakang nama file script (contoh salah: `.\scripts\seed.ps1.`).

## Troubleshooting PowerShell

Jika muncul pesan:
`The command scripts/seed.ps1 was not found ... type: ".\scripts/seed.ps1"`

Solusi:

```powershell
.\scripts\seed.ps1 -Mode full
```

## Akun Login Default Setelah Full Seed

- Username: `admin`
- Password: `password123`

