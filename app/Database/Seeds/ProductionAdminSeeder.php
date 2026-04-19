<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductionAdminSeeder extends Seeder
{
    public function run()
    {
        $table = $this->db->table('users');
        $now = date('Y-m-d H:i:s');

        $username = 'admin-sipadukar';
        $email = 'admin-sipadukar@sipena.local';
        $plainPassword = 'admin-sipadukar';

        $payload = [
            'nama'       => 'Admin SIPENA',
            'email'      => $email,
            'username'   => $username,
            'password'   => password_hash($plainPassword, PASSWORD_DEFAULT),
            'role'       => 'admin',
            'is_active'  => 1,
            'updated_at' => $now,
        ];

        $existing = $table
            ->groupStart()
            ->where('username', $username)
            ->orWhere('email', $email)
            ->groupEnd()
            ->get()
            ->getRowArray();

        if ($existing) {
            $table->where('id', (int) $existing['id'])->update($payload);
            return;
        }

        $payload['created_at'] = $now;
        $table->insert($payload);
    }
}
