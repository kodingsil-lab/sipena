<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriStandarModel extends Model
{
    protected $table            = 'master_kategori_standar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'nama_kategori',
        'deskripsi',
        'is_aktif',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
