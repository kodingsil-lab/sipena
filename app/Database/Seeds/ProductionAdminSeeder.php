<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductionAdminSeeder extends Seeder
{
    public function run()
    {
        $table = $this->db->table('users');
        $now = date('Y-m-d H:i:s');

        $username = trim((string) env('PRODUCTION_ADMIN_USERNAME', ''));
        if ($username === '') {
            $username = 'admin';
        }

        $email = trim((string) env('PRODUCTION_ADMIN_EMAIL', ''));
        if ($email === '') {
            $email = 'admin@localhost';
        }

        $plainPassword = trim((string) env('PRODUCTION_ADMIN_PASSWORD', ''));

        $payload = [
            'nama'       => 'Admin SIPENA',
            'email'      => $email,
            'username'   => $username,
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

        if ($plainPassword !== '') {
            $payload['password'] = password_hash($plainPassword, PASSWORD_DEFAULT);
        }

        if ($existing) {
            $table->where('id', (int) $existing['id'])->update($payload);
            return;
        }

        if ($plainPassword === '') {
            throw new \RuntimeException(
                'PRODUCTION_ADMIN_PASSWORD wajib diisi saat membuat admin produksi pertama kali.'
            );
        }

        $payload['created_at'] = $now;
        $table->insert($payload);
    }
}
