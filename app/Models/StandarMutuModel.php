<?php

namespace App\Models;

use CodeIgniter\Model;

class StandarMutuModel extends Model
{
    protected $table            = 'standar_mutu';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'kode_standar',
        'nama_standar',
        'deskripsi',
        'status_publikasi',
        'jenis_standar_id',
        'kategori_standar_id',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
