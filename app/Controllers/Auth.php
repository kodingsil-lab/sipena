<?php

namespace App\Controllers;

use App\Models\UserModel;
use Config\Services;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login', [
            'title' => 'Login',
        ]);
    }

    public function attemptLogin()
    {
        $ipAddress = (string) $this->request->getIPAddress();
        $usernameInput = strtolower(trim((string) $this->request->getPost('username')));
        $usernameKey = $usernameInput !== '' ? $usernameInput : 'unknown';
        $throttler = Services::throttler();
        $ipUserKey = 'login-ip-user:' . sha1($ipAddress . '|' . $usernameKey);
        $ipKey = 'login-ip:' . sha1($ipAddress);

        // 5 attempts per 15 minutes per IP+username and 30 attempts per 15 minutes per IP.
        if (
            ! $throttler->check($ipUserKey, 5, MINUTE * 15)
            || ! $throttler->check($ipKey, 30, MINUTE * 15)
        ) {
            return redirect()->back()->withInput()->with(
                'error',
                'Terlalu banyak percobaan login. Silakan coba lagi dalam 15 menit.'
            );
        }

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Username dan password wajib diisi.');
        }

        $username = trim($this->request->getPost('username'));
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if (! $user) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        if ((int) $user['is_active'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'Akun tidak aktif.');
        }

        if (! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        $sessionData = [
            'user_id'      => $user['id'],
            'nama'         => $user['nama'],
            'username'     => $user['username'],
            'email'        => $user['email'],
            'role'         => $user['role'],
            'is_logged_in' => true,
        ];

        session()->set($sessionData);
        session()->regenerate(true);

        return redirect()->to('/dashboard')->with('success', 'Berhasil login.');
    }

    public function logout()
    {
        if (! $this->request->is('post')) {
            return redirect()->to('/dashboard')->with('error', 'Metode logout tidak valid.');
        }

        session()->destroy();
        return redirect()->to('/login')->with('success', 'Berhasil logout.');
    }
}
