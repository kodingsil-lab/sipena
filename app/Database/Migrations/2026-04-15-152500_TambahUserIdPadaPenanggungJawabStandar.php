<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahUserIdPadaPenanggungJawabStandar extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if (! in_array('user_id', $db->getFieldNames('penanggung_jawab_standar'))) {
            $this->forge->addColumn('penanggung_jawab_standar', [
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'ttd_digital',
                ],
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if (in_array('user_id', $db->getFieldNames('penanggung_jawab_standar'))) {
            $this->forge->dropColumn('penanggung_jawab_standar', 'user_id');
        }
    }
}
