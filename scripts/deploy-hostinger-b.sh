#!/usr/bin/env bash

set -Eeuo pipefail

DOMAIN_NAME="${DOMAIN_NAME:-sipena.unisap.ac.id}"
DOMAIN_ROOT="${DOMAIN_ROOT:-/home/u541589701/domains/${DOMAIN_NAME}}"
APP_ROOT="${APP_ROOT:-${DOMAIN_ROOT}/_sipena_app}"
PUBLIC_ROOT="${PUBLIC_ROOT:-${DOMAIN_ROOT}/public_html}"
SHARED_ROOT="${SHARED_ROOT:-${DOMAIN_ROOT}/shared}"
SHARED_ENV="${SHARED_ENV:-${SHARED_ROOT}/.env}"
REPO_URL="${REPO_URL:-https://github.com/kodingsil-lab/sipena.git}"
BRANCH="${BRANCH:-main}"
PHP_BIN="${PHP_BIN:-php}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-1}"
RUN_SEEDERS="${RUN_SEEDERS:-0}"
SEEDER_CLASS="${SEEDER_CLASS:-ProductionAdminSeeder}"

log() {
    printf '[deploy-hostinger-b] %s\n' "$1"
}

fail() {
    printf '[deploy-hostinger-b][error] %s\n' "$1" >&2
    exit 1
}

has_cmd() {
    command -v "$1" >/dev/null 2>&1
}

need_cmd() {
    has_cmd "$1" || fail "Perintah '$1' tidak tersedia di server."
}

php_function_disabled() {
    local function_name="$1"
    local disabled_functions

    disabled_functions="$("${PHP_BIN}" -r 'echo (string) ini_get("disable_functions");' 2>/dev/null || true)"
    disabled_functions=",${disabled_functions// /},"

    [[ "${disabled_functions}" == *",${function_name},"* ]]
}

run_composer() {
    if has_cmd composer; then
        composer "$@"
        return
    fi

    if [ -f "${APP_ROOT}/composer.phar" ]; then
        "${PHP_BIN}" "${APP_ROOT}/composer.phar" "$@"
        return
    fi

    log "Composer tidak ditemukan, mencoba install composer.phar lokal."
    (
        cd "${APP_ROOT}"
        "${PHP_BIN}" -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        "${PHP_BIN}" composer-setup.php --filename=composer.phar
        "${PHP_BIN}" -r "unlink('composer-setup.php');"
    )

    [ -f "${APP_ROOT}/composer.phar" ] || fail "Gagal install composer.phar. Upload manual composer.phar ke ${APP_ROOT}."
    "${PHP_BIN}" "${APP_ROOT}/composer.phar" "$@"
}

sync_public() {
    mkdir -p "${PUBLIC_ROOT}"

    if has_cmd rsync; then
        rsync -av --delete --exclude='uploads/' "${APP_ROOT}/public/" "${PUBLIC_ROOT}/"
        return
    fi

    log "rsync tidak tersedia, memakai fallback tar untuk public_html."
    find "${PUBLIC_ROOT}" -mindepth 1 -maxdepth 1 ! -name uploads -exec rm -rf {} +
    (cd "${APP_ROOT}/public" && tar -cf - --exclude='uploads' .) | (cd "${PUBLIC_ROOT}" && tar -xf -)
}

run_spark() {
    "${PHP_BIN}" spark "$@"
}

need_cmd git
need_cmd "${PHP_BIN}"

log "Menyiapkan folder Hostinger B"
mkdir -p "${DOMAIN_ROOT}" "${PUBLIC_ROOT}" "${SHARED_ROOT}"

if [ ! -d "${APP_ROOT}/.git" ]; then
    if [ -e "${APP_ROOT}" ] && [ "$(find "${APP_ROOT}" -mindepth 1 -maxdepth 1 2>/dev/null | head -n 1)" ]; then
        fail "${APP_ROOT} sudah ada tetapi bukan repository Git. Backup/hapus manual dulu agar aman."
    fi

    log "Clone repository ${REPO_URL} ke ${APP_ROOT}"
    git clone --branch "${BRANCH}" "${REPO_URL}" "${APP_ROOT}"
fi

cd "${APP_ROOT}"

log "Mengambil update branch ${BRANCH}"
git fetch origin "${BRANCH}"
git checkout "${BRANCH}"
git pull --ff-only origin "${BRANCH}"

if [ ! -f "${SHARED_ENV}" ]; then
    if [ -f "${APP_ROOT}/.env.production.example" ]; then
        cp "${APP_ROOT}/.env.production.example" "${SHARED_ENV}"
        log "Template .env dibuat di ${SHARED_ENV}. Isi kredensial database sebelum membuka website."
    else
        fail "Template .env.production.example tidak ditemukan."
    fi
fi

cp "${SHARED_ENV}" "${APP_ROOT}/.env"
chmod 640 "${APP_ROOT}/.env" || true

log "Menyiapkan folder writable dan uploads"
mkdir -p \
    "${APP_ROOT}/writable/cache" \
    "${APP_ROOT}/writable/debugbar" \
    "${APP_ROOT}/writable/logs" \
    "${APP_ROOT}/writable/session" \
    "${APP_ROOT}/writable/uploads" \
    "${PUBLIC_ROOT}/uploads"

log "Install dependency production"
run_composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

log "Sinkronisasi folder public ke public_html"
sync_public

PUBLIC_INDEX_FILE="${PUBLIC_ROOT}/index.php"
[ -f "${PUBLIC_INDEX_FILE}" ] || fail "index.php publik tidak ditemukan di ${PUBLIC_INDEX_FILE}."

log "Menyesuaikan public_html/index.php ke app root"
if grep -q "require FCPATH \. '../app/Config/Paths.php';" "${PUBLIC_INDEX_FILE}"; then
    sed -i "s#require FCPATH \. '../app/Config/Paths.php';#require '${APP_ROOT}/app/Config/Paths.php';#" "${PUBLIC_INDEX_FILE}"
elif grep -q "require '.*app/Config/Paths.php';" "${PUBLIC_INDEX_FILE}"; then
    sed -E -i "s#require '.*app/Config/Paths.php';#require '${APP_ROOT}/app/Config/Paths.php';#" "${PUBLIC_INDEX_FILE}"
else
    fail "Tidak menemukan baris require Paths.php pada ${PUBLIC_INDEX_FILE}."
fi

if [ "${RUN_MIGRATIONS}" = "1" ]; then
    log "Menjalankan migration"
    run_spark migrate --all --no-interaction
else
    log "Migration dilewati"
fi

if [ "${RUN_SEEDERS}" = "1" ]; then
    log "Menjalankan seeder ${SEEDER_CLASS}"
    run_spark db:seed "${SEEDER_CLASS}" --no-interaction
else
    log "Seeder dilewati"
fi

log "Membersihkan cache"
run_spark cache:clear || true
if php_function_disabled "passthru"; then
    log "Melewati optimize karena passthru() diblokir hosting."
else
    run_spark optimize || true
fi

chmod -R 775 "${APP_ROOT}/writable" "${PUBLIC_ROOT}/uploads" || true

log "Deploy selesai"
log "Domain     : https://${DOMAIN_NAME}/"
log "App root   : ${APP_ROOT}"
log "Public root: ${PUBLIC_ROOT}"
log "Env file   : ${SHARED_ENV}"
