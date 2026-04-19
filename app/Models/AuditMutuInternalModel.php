<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditMutuInternalModel extends Model
{
    protected $table            = 'audit_mutu_internal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
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