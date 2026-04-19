<?php

namespace App\Models;

use CodeIgniter\Model;

class PenugasanPenandatanganStandarModel extends Model
{
    protected $table            = 'penugasan_penandatangan_standar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'standar_mutu_id',
        'proses',
        'user_id',
        'tanggal_ttd',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
