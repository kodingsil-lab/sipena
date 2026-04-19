# Petunjuk API Integrasi Dokumen Standar (AMI)

API ini dipakai aplikasi Audit Mutu Internal (AMI) eksternal untuk menarik data dokumen standar, khususnya `indikator_ketercapaian`.

## URL Dasar

Gunakan URL dasar aplikasi SIPENA Anda, contoh:

`https://sipena.example.ac.id/api/v1`

## Autentikasi

Wajib kirim token melalui salah satu header:

- `Authorization: Bearer <token>`
- `X-API-Key: <token>`

Token diambil dari:

- env: `API_INTEGRATION_TOKEN`
- atau app setting key: `api_integration_token`

## Daftar Endpoint

### 1) Daftar Dokumen Standar

`GET /api/v1/standar`

Parameter query:

- `page` (opsional, default `1`)
- `per_page` (opsional, default `20`, max `100`)
- `keyword` (opsional)
- `jenis_standar_id` (opsional)
- `kategori_standar_id` (opsional)

Contoh:

```bash
curl -H "Authorization: Bearer TOKEN_ANDA" \
  "https://sipena.example.ac.id/api/v1/standar?page=1&per_page=20"
```

### 2) Detail Dokumen Standar

`GET /api/v1/standar/{id}`

Contoh:

```bash
curl -H "Authorization: Bearer TOKEN_ANDA" \
  "https://sipena.example.ac.id/api/v1/standar/12"
```

### 3) Sinkronisasi Bertahap (Perubahan)

`GET /api/v1/standar/changes?since={datetime}`

Parameter query:

- `since` (wajib, ISO 8601 atau format datetime valid)
- `limit` (opsional, default `20`, max `100`)

Contoh:

```bash
curl -H "Authorization: Bearer TOKEN_ANDA" \
  "https://sipena.example.ac.id/api/v1/standar/changes?since=2026-04-01T00:00:00+07:00&limit=50"
```

## Bentuk Data

Field utama tiap item:

- `id`
- `standar_mutu_id`
- `kode_dokumen`
- `kode_standar`
- `nama_standar`
- `jenis_standar` (`id`, `nama`)
- `kategori_standar` (`id`, `nama`)
- `tanggal_dokumen`
- `revisi`
- `halaman`
- `status_publikasi`
- `indikator_ketercapaian_count`
- `updated_at`
- `created_at`
- `links.detail`
- `links.web`

Pada endpoint detail dan changes, field berikut juga tersedia:

- `indikator_ketercapaian` (array string)

## Contoh Respons Detail

```json
{
  "data": {
    "id": 12,
    "standar_mutu_id": 5,
    "kode_dokumen": "STD-001",
    "kode_standar": "SM-01",
    "nama_standar": "Standar Isi",
    "jenis_standar": {
      "id": 2,
      "nama": "Akademik"
    },
    "kategori_standar": {
      "id": 3,
      "nama": "Pendidikan"
    },
    "tanggal_dokumen": "2026-03-01",
    "revisi": "2",
    "halaman": "1-15",
    "status_publikasi": "publish",
    "indikator_ketercapaian_count": 3,
    "updated_at": "2026-04-19T10:00:00+07:00",
    "created_at": "2026-03-01T08:00:00+07:00",
    "links": {
      "detail": "https://sipena.example.ac.id/api/v1/standar/12",
      "web": "https://sipena.example.ac.id/publik/standar-mutu/detail/12"
    },
    "indikator_ketercapaian": [
      "RPS tersedia pada seluruh mata kuliah.",
      "Evaluasi kurikulum dilakukan minimal sekali per tahun.",
      "Capaian pembelajaran terdokumentasi."
    ]
  }
}
```

## Catatan Sinkronisasi AMI

- Simpan `id` sebagai `source_id` di aplikasi AMI.
- Untuk sinkron incremental, simpan `meta.next_since` dari endpoint `changes`.
- Jika ada kebutuhan hapus/arsip data, tambahkan endpoint khusus pada versi API berikutnya.

## Ringkasan Kontrak API

- Metode autentikasi: Bearer token atau `X-API-Key`.
- Format data: JSON.
- Zona waktu tanggal: mengikuti server SIPENA (disarankan konsisten `Asia/Jakarta`).
- Data yang dikirim hanya dokumen standar berstatus `publish`.

## Panduan Implementasi di Aplikasi AMI

### Langkah 1: Siapkan Token Integrasi di SIPENA

1. Tentukan token rahasia yang kuat (minimal 32 karakter).
2. Simpan token di SIPENA melalui salah satu cara:
   - `.env`: `API_INTEGRATION_TOKEN=token_rahasia_anda`
   - atau app setting key `api_integration_token`.
3. Simpan token yang sama di konfigurasi aplikasi AMI.

### Langkah 2: Uji Koneksi Dasar dari Aplikasi AMI

Lakukan uji awal endpoint daftar:

```bash
curl -H "Authorization: Bearer TOKEN_ANDA" \
  "https://sipena.example.ac.id/api/v1/standar?page=1&per_page=1"
```

Jika benar, API mengembalikan `meta` dan `data`.

### Langkah 3: Rancang Tabel Sinkron di Aplikasi AMI

Minimal siapkan tabel lokal untuk menyimpan hasil tarik data:

- `source_id` (id dokumen standar dari SIPENA, unik)
- `kode_dokumen`
- `nama_standar`
- `jenis_standar_nama`
- `kategori_standar_nama`
- `indikator_ketercapaian` (JSON/text)
- `updated_at_source`
- `last_synced_at`

Disarankan tambah tabel `sync_state` untuk menyimpan `last_since`.

### Langkah 4: Sinkronisasi Awal (Full Sync)

1. Panggil `GET /api/v1/standar` secara paging (`page=1,2,3,...`).
2. Simpan setiap item ke database AMI (upsert berdasarkan `source_id`).
3. Untuk tiap item, panggil detail `GET /api/v1/standar/{id}` jika butuh isi `indikator_ketercapaian` lengkap.
4. Setelah selesai, simpan waktu sync terakhir.

### Langkah 5: Sinkronisasi Berkala (Incremental)

1. Ambil nilai `last_since` dari tabel `sync_state`.
2. Panggil endpoint:
   - `GET /api/v1/standar/changes?since={last_since}&limit=50`
3. Proses data dengan metode upsert.
4. Simpan `meta.next_since` sebagai `last_since` baru.
5. Ulangi berkala (mis. setiap 5-15 menit via cron/queue worker).

### Langkah 6: Contoh Alur Cron Sederhana

1. Baca `last_since` (jika belum ada, gunakan tanggal awal).
2. Hit API `changes`.
3. Jika `data` kosong, selesai.
4. Jika ada data, upsert semuanya.
5. Simpan `next_since`.

### Langkah 7: Penanganan Error yang Disarankan

1. Jika `401 Unauthorized`: cek token.
2. Jika `400 Bad Request`: cek format parameter `since`.
3. Jika `500`: log error, retry dengan backoff.
4. Simpan log request/response ringkas untuk audit sinkronisasi.

### Langkah 8: Checklist Go-Live

1. Token tersimpan aman (tidak hardcode di source code).
2. Cron sinkron berjalan otomatis.
3. Ada monitoring jumlah data sinkron.
4. Ada notifikasi jika sinkron gagal berulang.
5. Terdapat fallback manual sync dari panel AMI.
