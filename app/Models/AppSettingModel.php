<?php

namespace App\Models;

use CodeIgniter\Model;

class AppSettingModel extends Model
{
    protected $table            = 'app_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'setting_key',
        'setting_value',
        'updated_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getAllAsMap(): array
    {
        $rows = $this->select('setting_key, setting_value')->findAll();
        $map = [];

        foreach ($rows as $row) {
            $key = trim((string) ($row['setting_key'] ?? ''));
            if ($key === '') {
                continue;
            }

            $map[$key] = (string) ($row['setting_value'] ?? '');
        }

        return $map;
    }

    public function setValue(string $key, ?string $value, ?int $updatedBy = null): void
    {
        $existing = $this->where('setting_key', $key)->first();
        $payload = [
            'setting_key' => $key,
            'setting_value' => $value,
            'updated_by' => $updatedBy,
        ];

        if ($existing) {
            $this->update((int) $existing['id'], $payload);
            return;
        }

        $this->insert($payload);
    }
}
