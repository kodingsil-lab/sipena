<?php

namespace App\Models;

use CodeIgniter\Model;

class DokumenStandarModel extends Model
{
    protected $table            = 'dokumen_standar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'standar_mutu_id',
        'kode_dokumen',
        'tanggal_dokumen',
        'revisi',
        'halaman',
        'rasional',
        'subjek_bertanggung_jawab',
        'definisi_istilah',
        'pernyataan_isi_standar',
        'indikator_ketercapaian',
        'strategi_pencapaian',
        'dokumen_terkait',
        'referensi',
        'status_publikasi',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}