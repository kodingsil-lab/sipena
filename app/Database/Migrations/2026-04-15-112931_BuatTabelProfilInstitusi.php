<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BuatTabelProfilInstitusi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_institusi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'singkatan_institusi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'lembaga_penjaminan_mutu' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'visi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'misi' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'tujuan' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'sasaran' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
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
        $this->forge->createTable('profil_institusi');
    }

    public function down()
    {
        $this->forge->dropTable('profil_institusi');
    }
}