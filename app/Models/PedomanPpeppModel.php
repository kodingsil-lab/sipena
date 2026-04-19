<?php

namespace App\Models;

use CodeIgniter\Model;

class PedomanPpeppModel extends Model
{
    protected $table            = 'pedoman_ppepp';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'jenis_dokumen',
        'judul',
        'nomor_dokumen',
        'tahun',
        'deskripsi',
        'file_pdf',
        'status_publikasi',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}