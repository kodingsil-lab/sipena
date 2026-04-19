<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SatukanMasterPenggunaDanProses extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if (! in_array('ttd_digital', $db->getFieldNames('users'))) {
            $this->forge->addColumn('users', [
                'ttd_digital' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'role_proses',
                ],
            ]);
        }

        if (! $db->tableExists('user_penanggung_jawab_proses')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'proses' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
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
            $this->forge->addKey(['user_id', 'proses']);
            $this->forge->createTable('user_penanggung_jawab_proses');
        }

        $this->normalisasiRoleSistem($db);
        $this->migrasiProsesDariUsers($db);
        $this->migrasiProsesDariPenandatanganLama($db);
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('user_penanggung_jawab_proses')) {
            $this->forge->dropTable('user_penanggung_jawab_proses');
        }

        if (in_array('ttd_digital', $db->getFieldNames('users'))) {
            $this->forge->dropColumn('users', 'ttd_digital');
        }
    }

    private function normalisasiRoleSistem($db): void
    {
        $map = [
            'admin_lpm'   => 'admin',
            'user_proses' => 'dosen',
            'kepala_lpm'  => 'kepala_lpm',
        ];

        foreach ($map as $lama => $baru) {
            $db->table('users')->where('role', $lama)->set(['role' => $baru])->update();
        }
    }

    private function migrasiProsesDariUsers($db): void
    {
        if (! in_array('role_proses', $db->getFieldNames('users'))) {
            return;
        }

        $rows = $db->table('users')
            ->select('id, role_proses')
            ->where('role_proses IS NOT NULL', null, false)
            ->where("TRIM(role_proses) <> ''", null, false)
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $this->insertProsesIfNotExists($db, (int) $row['id'], (string) $row['role_proses']);
        }
    }

    private function migrasiProsesDariPenandatanganLama($db): void
    {
        if (! $db->tableExists('penanggung_jawab_standar')) {
            return;
        }

        $fields = $db->getFieldNames('penanggung_jawab_standar');
        if (! in_array('user_id', $fields) || ! in_array('proses', $fields)) {
            return;
        }

        $rows = $db->table('penanggung_jawab_standar')
            ->select('user_id, proses')
            ->where('user_id IS NOT NULL', null, false)
            ->where("TRIM(proses) <> ''", null, false)
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $this->insertProsesIfNotExists($db, (int) $row['user_id'], (string) $row['proses']);
        }
    }

    private function insertProsesIfNotExists($db, int $userId, string $proses): void
    {
        $proses = trim($proses);
        if ($userId <= 0 || $proses === '') {
            return;
        }

        $exists = $db->table('user_penanggung_jawab_proses')
            ->where('user_id', $userId)
            ->where('proses', $proses)
            ->countAllResults();

        if ($exists > 0) {
            return;
        }

        $db->table('user_penanggung_jawab_proses')->insert([
            'user_id'    => $userId,
            'proses'     => $proses,
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
