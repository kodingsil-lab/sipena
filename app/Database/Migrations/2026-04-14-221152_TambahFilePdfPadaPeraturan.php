<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahFilePdfPadaPeraturan extends Migration
{
    public function up()
    {
        $fields = [
            'file_pdf' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'deskripsi',
            ],
        ];

        $this->forge->addColumn('peraturan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('peraturan', 'file_pdf');
    }
}