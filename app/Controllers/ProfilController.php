<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ProfilController extends BaseController
{
    public function index()
    {
        $userId = (int) (session()->get('user_id') ?? 0);
        if ($userId <= 0) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = (new UserModel())->find($userId);
        if (! $user) {
            throw PageNotFoundException::forPageNotFound('Data profil pengguna tidak ditemukan.');
        }

        return view('profil/index', [
            'title' => 'Profil Saya',
            'pageTitle' => 'Profil Saya',
            'pageDesc' => 'Kelola data akun pribadi Anda.',
            'userItem' => $user,
            'action' => base_url('/profil/update'),
        ]);
    }

    public function update()
    {
        $userId = (int) (session()->get('user_id') ?? 0);
        if ($userId <= 0) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $model = new UserModel();
        $user = $model->find($userId);
        if (! $user) {
            throw PageNotFoundException::forPageNotFound('Data profil pengguna tidak ditemukan.');
        }

        $rules = [
            'nama' => 'required',
            'email' => 'required|valid_email|is_unique[users.email,id,' . $userId . ']',
            'username' => 'required|is_unique[users.username,id,' . $userId . ']',
            'jabatan' => 'permit_empty|max_length[150]',
            'ttd_digital' => 'permit_empty|max_size[ttd_digital,2048]|is_image[ttd_digital]|mime_in[ttd_digital,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        $password = trim((string) $this->request->getPost('password'));
        if ($password !== '') {
            $rules['password'] = 'min_length[6]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali input profil Anda.');
        }

        $dataUpdate = [
            'nama' => trim((string) $this->request->getPost('nama')),
            'email' => trim((string) $this->request->getPost('email')),
            'username' => trim((string) $this->request->getPost('username')),
            'jabatan' => trim((string) $this->request->getPost('jabatan')) ?: null,
        ];

        if ($password !== '') {
            $dataUpdate['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $file = $this->request->getFile('ttd_digital');
        $namaFileLama = $user['ttd_digital'] ?? null;
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $namaFileBaru = $file->getRandomName();
            $file->move(FCPATH . 'uploads/ttd_standar', $namaFileBaru);
            $dataUpdate['ttd_digital'] = $namaFileBaru;
        }

        $model->update($userId, $dataUpdate);
        if (! empty($model->errors())) {
            if (! empty($dataUpdate['ttd_digital']) && $dataUpdate['ttd_digital'] !== $namaFileLama) {
                $pathBaru = FCPATH . 'uploads/ttd_standar/' . $dataUpdate['ttd_digital'];
                if (file_exists($pathBaru)) {
                    unlink($pathBaru);
                }
            }

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil.');
        }

        if (! empty($dataUpdate['ttd_digital']) && $dataUpdate['ttd_digital'] !== $namaFileLama && ! empty($namaFileLama)) {
            $pathLama = FCPATH . 'uploads/ttd_standar/' . $namaFileLama;
            if (file_exists($pathLama)) {
                unlink($pathLama);
            }
        }

        session()->set([
            'nama' => $dataUpdate['nama'],
            'username' => $dataUpdate['username'],
            'email' => $dataUpdate['email'],
        ]);

        return redirect()->to('/profil')->with('success', 'Profil berhasil diperbarui.');
    }
}
