<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SesuaikanMasterJenisKategoriTanpaKode extends Migration
{
    public function up()
    {
        $this->siapkanMasterJenisStandar();
        $this->siapkanMasterKategoriStandar();
    }

    public function down()
    {
        // Tidak mengembalikan struktur lama agar data tidak hilang.
    }

    private function siapkanMasterJenisStandar(): void
    {
        if (! $this->db->tableExists('master_jenis_standar')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'nama_jenis' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'deskripsi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'is_aktif' => [
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
            $this->forge->createTable('master_jenis_standar', true);

            return;
        }

        if ($this->db->fieldExists('kode_jenis', 'master_jenis_standar')) {
            $this->forge->dropColumn('master_jenis_standar', 'kode_jenis');
        }

        if (! $this->db->fieldExists('deskripsi', 'master_jenis_standar')) {
            $this->forge->addColumn('master_jenis_standar', [
                'deskripsi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);
        }

        if (! $this->db->fieldExists('is_aktif', 'master_jenis_standar')) {
            $this->forge->addColumn('master_jenis_standar', [
                'is_aktif' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
            ]);
        }
    }

    private function siapkanMasterKategoriStandar(): void
    {
        if (! $this->db->tableExists('master_kategori_standar')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'nama_kategori' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'deskripsi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'is_aktif' => [
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
            $this->forge->createTable('master_kategori_standar', true);

            return;
        }

        if ($this->db->fieldExists('kode_kategori', 'master_kategori_standar')) {
            $this->forge->dropColumn('master_kategori_standar', 'kode_kategori');
        }

        if (! $this->db->fieldExists('deskripsi', 'master_kategori_standar')) {
            $this->forge->addColumn('master_kategori_standar', [
                'deskripsi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);
        }

        if (! $this->db->fieldExists('is_aktif', 'master_kategori_standar')) {
            $this->forge->addColumn('master_kategori_standar', [
                'is_aktif' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
            ]);
        }
    }
}
