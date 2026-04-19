# Panduan Seeder Dummy SIPENA

Dokumen ini berisi cara menjalankan **clean seed** dan **full seed** untuk data dummy modul SIPENA.

## Seeder yang tersedia

- `CleanDummySeeder`
  - Menghapus data modul secara bersih (truncate) untuk tabel:
    - `user_penanggung_jawab_proses`
    - `dokumen_standar`
    - `audit_mutu_internal`
    - `pedoman_ppepp`
    - `kebijakan_spmi`
    - `kebijakan_mutu`
    - `peraturan`
    - `standar_mutu`
    - `profil_institusi`
    - `users`

- `FullDummySeeder`
  - Menjalankan `CleanDummySeeder` lalu mengisi data dummy **20 baris per modul**.
  - Modul yang diisi:
    - Users + Penanggung Jawab Proses
    - Profil Institusi
    - Standar Mutu
    - Dokumen Standar
    - Peraturan
    - Kebijakan Mutu
    - Kebijakan SPMI
    - Pedoman PPEPP
    - Audit Mutu Internal

## Cara menjalankan

Jalankan dari root project:

```bash
php spark db:seed CleanDummySeeder
```

Untuk isi data dummy lengkap:

```bash
php spark db:seed FullDummySeeder
```

> Catatan: `FullDummySeeder` sudah otomatis membersihkan data terlebih dahulu.

## Akun login dummy default

Setelah `FullDummySeeder`, akun admin default:

- Username: `admin`
- Password: `password123`

