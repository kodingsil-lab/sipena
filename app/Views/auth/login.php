<!doctype html>
<html lang="id">
<head>
    <?php
    $appName = trim((string) app_setting('nama_aplikasi', 'SIPENA'));
    if ($appName === '') {
        $appName = 'SIPENA';
    }
    $themePrimary = app_theme_color();
    $themeDark = app_color_shade($themePrimary, -12);
    $themeLight = app_color_shade($themePrimary, 18);
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Login'); ?></title>
    <?php $appFavicon = app_favicon_url(); ?>
    <?php if ($appFavicon !== ''): ?>
        <link rel="icon" type="image/png" href="<?= esc($appFavicon); ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html {
            font-size: 17px;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, <?= esc($themePrimary); ?> 0%, <?= esc($themeLight); ?> 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', 'Segoe UI', Roboto, Arial, sans-serif;
            padding: 12px;
        }

        .login-card {
            width: 100%;
            max-width: 435px;
            background: #f7f8fb;
            border: 0;
            border-radius: 20px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.18);
        }

        .login-inner {
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
        }

        .login-header {
            text-align: center;
            margin-bottom: 1.1rem;
        }

        .login-logo {
            width: 78px;
            height: 78px;
            object-fit: contain;
            object-position: center;
            margin-bottom: 12px;
        }

        .login-campus {
            font-size: 0.97rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }

        .login-title {
            color: <?= esc($themePrimary); ?>;
            font-weight: 700;
            font-size: 2.1rem;
            line-height: 1;
            margin-bottom: 6px;
            letter-spacing: 0.01em;
        }

        .login-subtitle {
            font-size: 0.84rem;
            color: #64748b;
            margin: 0;
        }

        .form-label {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 9px;
        }

        .login-input-group {
            border: 1px solid #ced7e6;
            border-radius: 12px;
            overflow: hidden;
            background: #e8eef8;
        }

        .login-input-group .input-group-text {
            width: 44px;
            border: 0;
            border-right: 1px solid #d7e1ef;
            border-radius: 0;
            background: #ffffff;
            color: #334155;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .login-input-group .form-control {
            border: 0;
            border-radius: 0;
            background: transparent;
            min-height: 48px;
            padding: 12px 14px;
            box-shadow: none;
        }

        .password-toggle-btn {
            width: 46px;
            border: 0;
            border-left: 1px solid #d7e1ef;
            border-radius: 0;
            background: #ffffff;
            color: #334155;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .password-toggle-btn:hover {
            background: #f3f7fd;
            color: <?= esc($themeDark); ?>;
        }

        .password-toggle-btn:focus {
            box-shadow: none;
            outline: none;
        }

        .btn-login {
            background: <?= esc($themePrimary); ?>;
            border-color: <?= esc($themePrimary); ?>;
            border-radius: 12px;
            padding: 11px 14px;
            font-weight: 700;
            font-size: 1.12rem;
        }

        .btn-login:hover {
            background: <?= esc($themeDark); ?>;
            border-color: <?= esc($themeDark); ?>;
        }
    </style>
</head>
<body>
    <div class="card login-card p-3 p-md-4">
        <div class="card-body">
            <div class="login-inner">
                <div class="login-header">
                    <?php $logoPtUrl = app_logo_header_url(); ?>
                    <?php if ($logoPtUrl !== ''): ?>
                        <img src="<?= esc($logoPtUrl); ?>" alt="Logo Perguruan Tinggi" class="login-logo">
                    <?php endif; ?>
                    <div class="login-campus"><?= esc(app_institution_name('Universitas San Pedro')); ?></div>
                    <h3 class="login-title"><?= esc($appName); ?></h3>
                    <p class="login-subtitle">Sistem Informasi Penjaminan Mutu Internal</p>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')); ?></div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
                <?php endif; ?>

                <form action="<?= base_url('/login'); ?>" method="post">
                    <?= csrf_field(); ?>

                    <div class="mb-3">
                        <label for="username" class="form-label">Nama Pengguna</label>
                        <div class="input-group login-input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person"></i>
                            </span>
                            <input
                                type="text"
                                class="form-control"
                                id="username"
                                name="username"
                                value="<?= esc(old('username')); ?>"
                                placeholder="Masukkan Username"
                                required
                            >
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group login-input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="Masukkan Password"
                                required
                            >
                            <button type="button" class="password-toggle-btn" id="togglePassword" aria-label="Tampilkan/Sembunyikan Password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login w-100">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    (() => {
        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('togglePassword');
        if (!passwordInput || !toggleButton) return;

        toggleButton.addEventListener('click', () => {
            const icon = toggleButton.querySelector('i');
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            if (icon) {
                icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
            }
        });
    })();
    </script>
</body>
</html>
