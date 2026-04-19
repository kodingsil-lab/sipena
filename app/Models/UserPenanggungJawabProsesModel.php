<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPenanggungJawabProsesModel extends Model
{
    protected $table            = 'user_penanggung_jawab_proses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'user_id',
        'proses',
        'is_active',
    ];

    protected $useTimestamps = false;
}
