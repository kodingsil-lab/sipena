param(
    [ValidateSet('clean', 'full', 'reset')]
    [string]$Mode = 'full',
    [string]$PhpBin = 'php'
)

$ErrorActionPreference = 'Stop'

$RootDir = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$SparkFile = Join-Path $RootDir 'spark'

function Run-Seed([string]$Seeder) {
    Write-Host "[seed] Menjalankan $Seeder..."
    & $PhpBin $SparkFile db:seed $Seeder
    if ($LASTEXITCODE -ne 0) {
        throw "Gagal menjalankan seeder: $Seeder"
    }
}

if (-not (Test-Path $SparkFile)) {
    throw "File spark tidak ditemukan di: $SparkFile"
}

switch ($Mode) {
    'clean' {
        Run-Seed 'CleanDummySeeder'
    }
    'full' {
        Run-Seed 'FullDummySeeder'
    }
    'reset' {
        Run-Seed 'CleanDummySeeder'
        Run-Seed 'FullDummySeeder'
    }
}

Write-Host "[seed] Selesai ($Mode)."
