<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahRoleProsesPadaUsers extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        if (! in_array('role_proses', $db->getFieldNames('users'))) {
            $this->forge->addColumn('users', [
                'role_proses' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'role',
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'role_proses');
    }
}
