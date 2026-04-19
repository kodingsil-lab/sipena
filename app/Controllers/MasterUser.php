<?php

namespace App\Controllers;

use App\Models\PenugasanPenandatanganStandarModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class MasterUser extends BaseController
{
    private function resolvePerPage(): int
    {
        $allowed = [15, 25, 50];
        $requested = (int) $this->request->getGet('per_page');

        return in_array($requested, $allowed, true) ? $requested : 15;
    }

    public function index()
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $model   = new UserModel();
        $perPage = $this->resolvePerPage();
        $keyword = trim((string) $this->request->getGet('keyword'));

        if ($keyword !== '') {
            $model->groupStart()
                  ->like('nama', $keyword)
                  ->orLike('username', $keyword)
                  ->orLike('email', $keyword)
                  ->groupEnd();
        }

        $users = $model->orderBy('id', 'DESC')->paginate($perPage, 'users');
        foreach ($users as &$user) {
            $user['role_label'] = $this->opsiRole()[$user['role']] ?? $user['role'];
        }
        unset($user);

        return view('master_user/index', [
            'title'        => 'Master Pengguna',
            'pageTitle'    => 'Master Pengguna',
            'pageDesc'     => 'Kelola data pengguna, role sistem, dan TTD digital.',
            'users'        => $users,
            'pager'        => $model->pager,
            'perPage'      => $perPage,
            'perPageAktif' => $perPage,
            'opsiPerPage'  => [15, 25, 50],
            'keywordAktif' => $keyword,
        ]);
    }

    public function tambah()
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        return view('master_user/form', [
            'title'      => 'Tambah Pengguna',
            'pageTitle'  => 'Tambah Pengguna',
            'pageDesc'   => 'Form input data pengguna.',
            'userItem'   => null,
            'action'     => base_url('/pengaturan/pengguna/simpan'),
            'opsiRole'   => $this->opsiRole(),
        ]);
    }

    public function simpan()
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $rules = [
            'nama'        => 'required',
            'email'       => 'required|valid_email|is_unique[users.email]',
            'username'    => 'required|is_unique[users.username]',
            'password'    => 'required|min_length[6]',
            'role'        => 'required',
            'jabatan'     => 'permit_empty|max_length[150]',
            'ttd_digital' => 'permit_empty|max_size[ttd_digital,2048]|is_image[ttd_digital]|mime_in[ttd_digital,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali input. Email/username harus unik, password minimal 6 karakter.');
        }

        $file = $this->request->getFile('ttd_digital');
        $namaFile = null;
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $namaFile = $file->getRandomName();
            $file->move(FCPATH . 'uploads/ttd_standar', $namaFile);
        }

        $model = new UserModel();
        $model->save([
            'nama'        => $this->request->getPost('nama'),
            'email'       => $this->request->getPost('email'),
            'username'    => $this->request->getPost('username'),
            'password'    => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'        => $this->request->getPost('role'),
            'jabatan'     => trim((string) $this->request->getPost('jabatan')) ?: null,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'ttd_digital' => $namaFile,
        ]);

        if (! empty($model->errors())) {
            if (! empty($namaFile) && file_exists(FCPATH . 'uploads/ttd_standar/' . $namaFile)) {
                unlink(FCPATH . 'uploads/ttd_standar/' . $namaFile);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data pengguna.');
        }

        return redirect()->to('/pengaturan/pengguna')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $model    = new UserModel();
        $userItem = $model->find($id);

        if (! $userItem) {
            throw PageNotFoundException::forPageNotFound('User tidak ditemukan.');
        }

        return view('master_user/form', [
            'title'      => 'Edit Pengguna',
            'pageTitle'  => 'Edit Pengguna',
            'pageDesc'   => 'Form edit data pengguna.',
            'userItem'   => $userItem,
            'action'     => base_url('/pengaturan/pengguna/update/' . $id),
            'opsiRole'   => $this->opsiRole(),
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $model    = new UserModel();
        $userItem = $model->find($id);

        if (! $userItem) {
            throw PageNotFoundException::forPageNotFound('User tidak ditemukan.');
        }

        $rules = [
            'nama'        => 'required',
            'email'       => 'required|valid_email|is_unique[users.email,id,' . $id . ']',
            'username'    => 'required|is_unique[users.username,id,' . $id . ']',
            'role'        => 'required',
            'jabatan'     => 'permit_empty|max_length[150]',
            'ttd_digital' => 'permit_empty|max_size[ttd_digital,2048]|is_image[ttd_digital]|mime_in[ttd_digital,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        $password = (string) $this->request->getPost('password');
        if ($password !== '') {
            $rules['password'] = 'min_length[6]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali input user.');
        }

        $dataUpdate = [
            'nama'      => $this->request->getPost('nama'),
            'email'     => $this->request->getPost('email'),
            'username'  => $this->request->getPost('username'),
            'role'      => $this->request->getPost('role'),
            'jabatan'   => trim((string) $this->request->getPost('jabatan')) ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($password !== '') {
            $dataUpdate['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $file = $this->request->getFile('ttd_digital');
        $namaFileLama = $userItem['ttd_digital'] ?? null;
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $namaFileBaru = $file->getRandomName();
            $file->move(FCPATH . 'uploads/ttd_standar', $namaFileBaru);
            $dataUpdate['ttd_digital'] = $namaFileBaru;
        }

        $model->update($id, $dataUpdate);
        if (! empty($model->errors())) {
            if (! empty($dataUpdate['ttd_digital']) && $dataUpdate['ttd_digital'] !== $namaFileLama) {
                $pathBaru = FCPATH . 'uploads/ttd_standar/' . $dataUpdate['ttd_digital'];
                if (file_exists($pathBaru)) {
                    unlink($pathBaru);
                }
            }
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data pengguna.');
        }

        if (! empty($dataUpdate['ttd_digital']) && $dataUpdate['ttd_digital'] !== $namaFileLama && ! empty($namaFileLama)) {
            $pathLama = FCPATH . 'uploads/ttd_standar/' . $namaFileLama;
            if (file_exists($pathLama)) {
                unlink($pathLama);
            }
        }

        return redirect()->to('/pengaturan/pengguna')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function hapus($id)
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $model    = new UserModel();
        $userItem = $model->find($id);

        if (! $userItem) {
            throw PageNotFoundException::forPageNotFound('User tidak ditemukan.');
        }

        $penugasanModel = new PenugasanPenandatanganStandarModel();
        $totalDipakai = $penugasanModel->where('user_id', (int) $id)->countAllResults();
        if ($totalDipakai > 0) {
            return redirect()->to('/pengaturan/pengguna')->with(
                'error',
                'User tidak bisa dihapus karena masih dipakai pada ' . $totalDipakai . ' penugasan penandatangan standar.'
            );
        }

        $model->delete($id);
        if (! empty($model->errors())) {
            return redirect()->to('/pengaturan/pengguna')->with('error', 'Gagal menghapus user.');
        }

        if (! empty($userItem['ttd_digital']) && file_exists(FCPATH . 'uploads/ttd_standar/' . $userItem['ttd_digital'])) {
            unlink(FCPATH . 'uploads/ttd_standar/' . $userItem['ttd_digital']);
        }

        return redirect()->to('/pengaturan/pengguna')->with('success', 'Pengguna berhasil dihapus.');
    }

    private function opsiRole(): array
    {
        return [
            'admin'       => 'Admin',
            'kepala_lpm'  => 'Kepala LPM',
            'dosen'       => 'Dosen',
        ];
    }
}
