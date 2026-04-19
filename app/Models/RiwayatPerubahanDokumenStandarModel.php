<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatPerubahanDokumenStandarModel extends Model
{
    protected $table            = 'riwayat_perubahan_dokumen_standar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'dokumen_standar_id',
        'standar_mutu_id',
        'updated_by',
        'changed_fields',
        'rasional',
        'subjek_bertanggung_jawab',
        'definisi_istilah',
        'pernyataan_isi_standar',
        'indikator_ketercapaian',
        'strategi_pencapaian',
        'dokumen_terkait',
        'referensi',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

