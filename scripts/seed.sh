#!/usr/bin/env bash

set -Eeuo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PHP_BIN="${PHP_BIN:-php}"
SPARK_FILE="${ROOT_DIR}/spark"
MODE="${1:-full}"

run_seed() {
    local seeder="$1"
    echo "[seed] Menjalankan ${seeder}..."
    "${PHP_BIN}" "${SPARK_FILE}" db:seed "${seeder}"
}

if [[ ! -f "${SPARK_FILE}" ]]; then
    echo "[seed][error] File spark tidak ditemukan di: ${SPARK_FILE}" >&2
    exit 1
fi

case "${MODE}" in
    clean)
        run_seed "CleanDummySeeder"
        ;;
    full)
        run_seed "FullDummySeeder"
        ;;
    reset)
        run_seed "CleanDummySeeder"
        run_seed "FullDummySeeder"
        ;;
    *)
        cat <<EOF
Penggunaan:
  bash scripts/seed.sh clean   # Hapus data dummy
  bash scripts/seed.sh full    # Isi full data (otomatis clean dulu)
  bash scripts/seed.sh reset   # Clean lalu full
EOF
        exit 1
        ;;
esac

echo "[seed] Selesai (${MODE})."
