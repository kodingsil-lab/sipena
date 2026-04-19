<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahJabatanPadaUsers extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('users')) {
            return;
        }

        if (! $this->db->fieldExists('jabatan', 'users')) {
            $this->forge->addColumn('users', [
                'jabatan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                    'after'      => 'role',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('users') && $this->db->fieldExists('jabatan', 'users')) {
            $this->forge->dropColumn('users', 'jabatan');
        }
    }
}

