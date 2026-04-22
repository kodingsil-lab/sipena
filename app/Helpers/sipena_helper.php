<?php

if (! function_exists('app_settings_map')) {
    function app_settings_map(): array
    {
        static $cache = null;
        if (is_array($cache)) {
            return $cache;
        }

        $cache = [];

        try {
            $db = db_connect();
            if (! $db->tableExists('app_settings')) {
                return $cache;
            }

            $rows = $db->table('app_settings')
                ->select('setting_key, setting_value')
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $key = trim((string) ($row['setting_key'] ?? ''));
                if ($key === '') {
                    continue;
                }
                $cache[$key] = (string) ($row['setting_value'] ?? '');
            }
        } catch (\Throwable $e) {
            return [];
        }

        return $cache;
    }
}

if (! function_exists('app_setting')) {
    function app_setting(string $key, ?string $default = null): ?string
    {
        $map = app_settings_map();
        if (array_key_exists($key, $map)) {
            return $map[$key];
        }

        return $default;
    }
}

if (! function_exists('app_asset_url')) {
    function app_asset_url(?string $path): string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return '';
        }

        return base_url(ltrim($path, '/'));
    }
}

if (! function_exists('app_logo_header_url')) {
    function app_logo_header_url(): string
    {
        $logo = app_asset_url(app_setting('logo_header_path', ''));
        if ($logo !== '') {
            return $logo;
        }

        try {
            $db = db_connect();
            if (! $db->tableExists('profil_institusi')) {
                return '';
            }

            $profil = $db->table('profil_institusi')->select('logo')->limit(1)->get()->getRowArray();
            $filename = basename(trim((string) ($profil['logo'] ?? '')));
            if ($filename === '') {
                return '';
            }

            return base_url('uploads/logo_institusi/' . $filename);
        } catch (\Throwable $e) {
            return '';
        }
    }
}

if (! function_exists('app_favicon_url')) {
    function app_favicon_url(): string
    {
        return app_asset_url(app_setting('favicon_path', ''));
    }
}

if (! function_exists('app_institution_name')) {
    function app_institution_name(string $default = 'Universitas San Pedro'): string
    {
        try {
            $db = db_connect();
            if (! $db->tableExists('profil_institusi')) {
                return $default;
            }

            $profil = $db->table('profil_institusi')
                ->select('nama_institusi')
                ->limit(1)
                ->get()
                ->getRowArray();

            $name = trim((string) ($profil['nama_institusi'] ?? ''));
            return $name !== '' ? $name : $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }
}

if (! function_exists('app_theme_color')) {
    function app_theme_color(): string
    {
        $color = trim((string) app_setting('warna_tema', '#3468cb'));
        if (! preg_match('/^#([A-Fa-f0-9]{6})$/', $color)) {
            return '#3468cb';
        }

        return strtoupper($color);
    }
}

if (! function_exists('app_public_theme_color')) {
    function app_public_theme_color(): string
    {
        $fallback = app_theme_color();
        $color = trim((string) app_setting('warna_tema_public', $fallback));
        if (! preg_match('/^#([A-Fa-f0-9]{6})$/', $color)) {
            return $fallback;
        }

        return strtoupper($color);
    }
}

if (! function_exists('app_color_shade')) {
    function app_color_shade(string $hexColor, int $percent): string
    {
        $hex = ltrim(trim($hexColor), '#');
        if (! preg_match('/^[A-Fa-f0-9]{6}$/', $hex)) {
            return '#3468cb';
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $adjust = static function (int $channel, int $pct): int {
            $next = $pct >= 0
                ? $channel + ((255 - $channel) * $pct / 100)
                : $channel + ($channel * $pct / 100);

            return max(0, min(255, (int) round($next)));
        };

        $r = $adjust($r, $percent);
        $g = $adjust($g, $percent);
        $b = $adjust($b, $percent);

        return sprintf('#%02X%02X%02X', $r, $g, $b);
    }
}

if (! function_exists('sanitize_allowed_html')) {
    function sanitize_allowed_html(?string $input, string $context = 'dokumen'): string
    {
        $raw = (string) $input;
        if (trim($raw) === '') {
            return '';
        }

        $allowedTags = match ($context) {
            'profil' => '<p><br><ol><ul><li><strong><em><b><i>',
            default => '<p><br><ol><ul><li><strong><b><em><i><u><blockquote><table><thead><tbody><tr><th><td><h1><h2><h3><h4><h5><h6>',
        };

        $clean = trim((string) strip_tags($raw, $allowedTags));

        $allowedTagNames = $context === 'profil'
            ? '(p|br|ol|ul|li|strong|em|b|i)'
            : '(p|br|ol|ul|li|strong|b|em|i|u|blockquote|table|thead|tbody|tr|th|td|h[1-6])';

        return preg_replace(
            '/<(\/?)' . $allowedTagNames . '(?:\s+[^>]*)?>/i',
            '<$1$2>',
            $clean
        ) ?? '';
    }
}
