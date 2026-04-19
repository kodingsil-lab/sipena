<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BersihkanMasterPenandatanganLama extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if (in_array('role_proses', $db->getFieldNames('users'))) {
            $this->forge->dropColumn('users', 'role_proses');
        }

        if ($db->tableExists('penanggung_jawab_standar')) {
            $this->forge->dropTable('penanggung_jawab_standar');
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if (! in_array('role_proses', $db->getFieldNames('users'))) {
            $this->forge->addColumn('users', [
                'role_proses' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'role',
                ],
            ]);
        }

        if (! $db->tableExists('penanggung_jawab_standar')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'urutan_proses' => [
                    'type'       => 'INT',
                    'constraint' => 2,
                    'default'    => 1,
                ],
                'proses' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                ],
                'nama_penanggung_jawab' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'jabatan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'role_proses' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                ],
                'ttd_digital' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'is_active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->createTable('penanggung_jawab_standar');
        }
    }
}
