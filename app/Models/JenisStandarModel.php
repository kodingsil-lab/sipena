<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisStandarModel extends Model
{
    protected $table            = 'master_jenis_standar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'nama_jenis',
        'deskripsi',
        'is_aktif',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
