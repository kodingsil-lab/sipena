<?php

namespace App\Controllers;

use App\Models\JenisStandarModel;
use App\Models\KategoriStandarModel;
use App\Models\ProfilInstitusiModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class MasterStandar extends BaseController
{
    public function jenisStandar()
    {
        $model = new JenisStandarModel();
        $perPage = 15;

        return view('master_standar/jenis_standar', [
            'title'      => 'Master Jenis Standar',
            'pageTitle'  => 'Master Jenis Standar',
            'pageDesc'   => 'Kelola daftar jenis standar.',
            'jenis'      => $model->orderBy('nama_jenis', 'ASC')->paginate($perPage, 'jenis'),
            'pager'      => $model->pager,
            'perPage'    => $perPage,
            'action'     => base_url('/master-data/jenis-standar/simpan'),
            'formMode'   => 'create',
        ]);
    }

    public function simpanJenisStandar()
    {
        $rules = [
            'nama_jenis' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama jenis wajib diisi.');
        }

        $model = new JenisStandarModel();
        $model->save([
            'nama_jenis' => trim((string) $this->request->getPost('nama_jenis')),
            'deskripsi'  => trim((string) $this->request->getPost('deskripsi')),
            'is_aktif'   => $this->request->getPost('is_aktif') ? 1 : 0,
        ]);

        return redirect()->to('/master-data/jenis-standar')->with('success', 'Jenis standar berhasil disimpan.');
    }

    public function editJenisStandar($id)
    {
        $model = new JenisStandarModel();
        $editJenis = $model->find($id);

        if (! $editJenis) {
            throw PageNotFoundException::forPageNotFound('Data jenis standar tidak ditemukan.');
        }

        $perPage = 15;
        return view('master_standar/jenis_standar', [
            'title'      => 'Master Jenis Standar',
            'pageTitle'  => 'Master Jenis Standar',
            'pageDesc'   => 'Kelola daftar jenis standar.',
            'jenis'      => $model->orderBy('nama_jenis', 'ASC')->paginate($perPage, 'jenis'),
            'pager'      => $model->pager,
            'perPage'    => $perPage,
            'action'     => base_url('/master-data/jenis-standar/update/' . $id),
            'formMode'   => 'edit',
            'editJenis'  => $editJenis,
        ]);
    }

    public function updateJenisStandar($id)
    {
        $model = new JenisStandarModel();
        $jenis = $model->find($id);

        if (! $jenis) {
            throw PageNotFoundException::forPageNotFound('Data jenis standar tidak ditemukan.');
        }

        $rules = [
            'nama_jenis' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama jenis wajib diisi.');
        }

        $model->update($id, [
            'nama_jenis' => trim((string) $this->request->getPost('nama_jenis')),
            'deskripsi'  => trim((string) $this->request->getPost('deskripsi')),
            'is_aktif'   => $this->request->getPost('is_aktif') ? 1 : 0,
        ]);

        return redirect()->to('/master-data/jenis-standar')->with('success', 'Jenis standar berhasil diperbarui.');
    }

    public function hapusJenisStandar($id)
    {
        $model = new JenisStandarModel();
        $jenis = $model->find($id);

        if (! $jenis) {
            throw PageNotFoundException::forPageNotFound('Data jenis standar tidak ditemukan.');
        }

        $model->delete($id);

        return redirect()->to('/master-data/jenis-standar')->with('success', 'Jenis standar berhasil dihapus.');
    }

    public function kategoriStandar()
    {
        $model = new KategoriStandarModel();
        $perPage = 15;

        return view('master_standar/kategori_standar', [
            'title'      => 'Master Kategori Standar',
            'pageTitle'  => 'Master Kategori Standar',
            'pageDesc'   => 'Kelola daftar kategori standar.',
            'kategori'   => $model->orderBy('nama_kategori', 'ASC')->paginate($perPage, 'kategori'),
            'pager'      => $model->pager,
            'perPage'    => $perPage,
            'action'     => base_url('/master-data/kategori-standar/simpan'),
            'formMode'   => 'create',
        ]);
    }

    public function simpanKategoriStandar()
    {
        $rules = [
            'nama_kategori' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama kategori wajib diisi.');
        }

        $model = new KategoriStandarModel();
        $model->save([
            'nama_kategori' => trim((string) $this->request->getPost('nama_kategori')),
            'deskripsi'     => trim((string) $this->request->getPost('deskripsi')),
            'is_aktif'      => $this->request->getPost('is_aktif') ? 1 : 0,
        ]);

        return redirect()->to('/master-data/kategori-standar')->with('success', 'Kategori standar berhasil disimpan.');
    }

    public function editKategoriStandar($id)
    {
        $model = new KategoriStandarModel();
        $editKategori = $model->find($id);

        if (! $editKategori) {
            throw PageNotFoundException::forPageNotFound('Data kategori standar tidak ditemukan.');
        }

        $perPage = 15;
        return view('master_standar/kategori_standar', [
            'title'        => 'Master Kategori Standar',
            'pageTitle'    => 'Master Kategori Standar',
            'pageDesc'     => 'Kelola daftar kategori standar.',
            'kategori'     => $model->orderBy('nama_kategori', 'ASC')->paginate($perPage, 'kategori'),
            'pager'        => $model->pager,
            'perPage'      => $perPage,
            'action'       => base_url('/master-data/kategori-standar/update/' . $id),
            'formMode'     => 'edit',
            'editKategori' => $editKategori,
        ]);
    }

    public function updateKategoriStandar($id)
    {
        $model = new KategoriStandarModel();
        $kategori = $model->find($id);

        if (! $kategori) {
            throw PageNotFoundException::forPageNotFound('Data kategori standar tidak ditemukan.');
        }

        $rules = [
            'nama_kategori' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama kategori wajib diisi.');
        }

        $model->update($id, [
            'nama_kategori' => trim((string) $this->request->getPost('nama_kategori')),
            'deskripsi'     => trim((string) $this->request->getPost('deskripsi')),
            'is_aktif'      => $this->request->getPost('is_aktif') ? 1 : 0,
        ]);

        return redirect()->to('/master-data/kategori-standar')->with('success', 'Kategori standar berhasil diperbarui.');
    }

    public function hapusKategoriStandar($id)
    {
        $model = new KategoriStandarModel();
        $kategori = $model->find($id);

        if (! $kategori) {
            throw PageNotFoundException::forPageNotFound('Data kategori standar tidak ditemukan.');
        }

        $model->delete($id);

        return redirect()->to('/master-data/kategori-standar')->with('success', 'Kategori standar berhasil dihapus.');
    }

    public function profilInstitusi()
    {
        $model = new ProfilInstitusiModel();
        $profil = $model->first();

        return view('master_standar/profil_institusi_form', [
            'title'     => 'Master Profil Institusi',
            'pageTitle' => 'Master Profil Institusi',
            'pageDesc'  => 'Kelola data profil institusi untuk sinkronisasi dokumen standar.',
            'profil'    => $profil,
            'action'    => base_url('/pengaturan/profil-institusi/simpan'),
        ]);
    }

    public function simpanProfilInstitusi()
    {
        $rules = [
            'nama_institusi' => 'required',
            'logo' => 'permit_empty|max_size[logo,2048]|is_image[logo]|mime_in[logo,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama institusi wajib diisi. File logo harus berupa gambar.');
        }

        $model  = new ProfilInstitusiModel();
        $profil = $model->first();

        $logoFile = $this->request->getFile('logo');
        $namaLogo = $profil['logo'] ?? null;

        if ($logoFile && $logoFile->isValid() && ! $logoFile->hasMoved()) {
            $namaLogoBaru = $logoFile->getRandomName();
            $logoFile->move(FCPATH . 'uploads/logo_institusi', $namaLogoBaru);

            if (! empty($profil['logo']) && file_exists(FCPATH . 'uploads/logo_institusi/' . $profil['logo'])) {
                unlink(FCPATH . 'uploads/logo_institusi/' . $profil['logo']);
            }

            $namaLogo = $namaLogoBaru;
        }

        $data = [
            'nama_institusi'      => $this->request->getPost('nama_institusi'),
            'singkatan_institusi' => $this->request->getPost('singkatan_institusi'),
            'visi'                => $this->sanitizeProfilHtml($this->request->getPost('visi')),
            'misi'                => $this->sanitizeProfilHtml($this->request->getPost('misi')),
            'tujuan'              => $this->sanitizeProfilHtml($this->request->getPost('tujuan')),
            'sasaran'             => $this->sanitizeProfilHtml($this->request->getPost('sasaran')),
            'alamat'              => $this->request->getPost('alamat'),
            'logo'                => $namaLogo,
        ];

        if ($profil) {
            $model->update($profil['id'], $data);
        } else {
            $model->insert($data);
        }

        return redirect()->to('/pengaturan/profil-institusi')->with('success', 'Profil institusi berhasil disimpan.');
    }

    public function aplikasi()
    {
        return view('pengaturan/aplikasi', [
            'title' => 'Pengaturan Aplikasi',
            'pageTitle' => 'Pengaturan Aplikasi',
            'pageDesc' => 'Halaman konfigurasi aplikasi.',
        ]);
    }

    private function sanitizeProfilHtml($input): string
    {
        $clean = strip_tags((string) $input, '<p><br><ol><ul><li><strong><em><b><i>');

        return preg_replace('/<(\/?)(p|br|ol|ul|li|strong|em|b|i)(?:\s+[^>]*)?>/i', '<$1$2>', $clean) ?? '';
    }

}
