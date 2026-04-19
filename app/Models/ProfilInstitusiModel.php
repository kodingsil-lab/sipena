<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfilInstitusiModel extends Model
{
    protected $table            = 'profil_institusi';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'nama_institusi',
        'singkatan_institusi',
        'visi',
        'misi',
        'tujuan',
        'sasaran',
        'alamat',
        'logo',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}