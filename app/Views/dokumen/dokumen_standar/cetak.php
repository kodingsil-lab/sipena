<?php $modePdf = $mode_pdf ?? false; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= esc($dokumen['kode_dokumen'] ?? 'Dokumen Standar'); ?></title>
<style>
@page {
    size: A4 portrait;
    margin-top: 20mm;
    margin-right: 20mm;
    margin-bottom: 20mm;
    margin-left: 20mm;
}

@page cover {
    size: A4 portrait;
    margin: 0;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Times New Roman", Times, serif;
    font-size: 11pt;
    color: #000;
    background: #fff;
    line-height: 1.35;
}

.page-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
    padding: 12px 0;
}

.page-actions .btn {
    border: 1px solid #0d6efd;
    background: #0d6efd;
    color: #fff;
    font-size: 14px;
    padding: 7px 16px;
    border-radius: 4px;
    cursor: pointer;
}

.page-actions .btn.btn-secondary {
    border-color: #6c757d;
    background: #6c757d;
}

/* ===================== COVER ===================== */
.cover-page {
    page: cover;
    width: 210mm;
    height: 297mm;
    margin: 0 auto;
    page-break-after: always;
    overflow: hidden;
}

.cover-inner {
    height: 297mm;
    padding: 28mm 20mm 22mm 20mm;
    text-align: center;
}

.cover-logo {
    max-width: 105px;
    max-height: 105px;
    display: block;
    margin: 0 auto 10pt auto;
}

.cover-spmi {
    font-size: 15pt;
    font-weight: bold;
    line-height: 1.5;
    text-transform: uppercase;
    margin-bottom: 40mm;
}

.cover-doc-main {
    font-size: 28pt;
    font-weight: bold;
    text-transform: uppercase;
    line-height: 1.15;
    margin-bottom: 10pt;
}

.cover-doc-sub {
    font-size: 18pt;
    font-weight: bold;
    text-transform: uppercase;
    line-height: 1.35;
    margin-bottom: 78mm;
}

.cover-bottom {
    font-size: 15pt;
    font-weight: bold;
    text-transform: uppercase;
    line-height: 1.45;
    text-align: center;
}

.cover-bottom-year {
    font-size: 15pt;
    display: block;
    margin-top: 2pt;
}

/* ===================== HALAMAN UMUM ===================== */
.doc-page {
    page-break-before: auto;
}

.identity-page {
    page-break-after: always;
}

.page-inner {
    width: 100%;
    max-width: 170mm;
    margin: 0 auto;
}

/* ===================== HEADER ULANG HALAMAN ISI ===================== */
.repeat-header-table {
    margin: 0 0 5mm 0;
}

.content-head-table {
    margin: 0 0 0 0;
}

.identity-table.content-head-table {
    margin-bottom: 5mm;
}

.repeat-header-table td {
    border: 1.1px solid #000;
    font-size: 9pt;
    font-weight: bold;
    text-transform: uppercase;
    padding: 3px 5px;
    vertical-align: middle;
    line-height: 1.2;
}

.repeat-header-inst {
    width: 44%;
    text-align: left;
}

.repeat-header-meta-label {
    width: 10%;
    text-align: left;
}

.repeat-header-meta-value {
    width: 12%;
    text-align: left;
}

/* ===================== IDENTITAS PAGE ===================== */
.identity-wrap {
    margin-top: 0;
    padding-top: 8mm;
}

.identity-table {
    border: 1.4px solid #000;
    margin-bottom: 18mm;
}

.identity-table td {
    border: 1.4px solid #000;
    vertical-align: middle;
    padding: 6px 7px;
}

.identity-logo-cell {
    width: 18%;
    text-align: center;
}

.identity-logo-cell img {
    max-width: 60px;
    max-height: 78px;
    display: block;
    margin: 0 auto;
}

.identity-title-cell {
    width: 48%;
    text-align: center;
    text-transform: uppercase;
    font-weight: bold;
    line-height: 1.45;
    font-size: 11.5pt;
}

.identity-title-cell .line-main {
    display: block;
    font-size: 12pt;
}

.identity-meta-label {
    width: 14%;
    font-size: 10pt;
    font-weight: bold;
    text-transform: none;
    text-align: left;
}

.identity-meta-sep {
    width: 3%;
    font-size: 10pt;
    font-weight: bold;
    text-align: center;
}

.identity-meta-value {
    width: 17%;
    font-size: 10pt;
    text-align: left;
}

/* ===================== JUDUL HALAMAN KEDUA ===================== */
.identity-doc-title {
    text-align: center;
    text-transform: uppercase;
    font-weight: bold;
    font-size: 13.5pt;
    line-height: 1.45;
    margin: 16mm 0 12mm 0;
}

/* ===================== PENGESAHAN TABLE ===================== */
.pengesahan-table {
    border: 1.4px solid #000;
}

.identity-table,
.pengesahan-table,
.repeat-header-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.pengesahan-table th,
.pengesahan-table td {
    border: 1.4px solid #000;
    padding: 7px 6px;
    font-size: 10.5pt;
    vertical-align: middle;
    line-height: 1.3;
}

.pengesahan-table th {
    font-weight: bold;
    text-transform: uppercase;
    text-align: center;
}

.pengesahan-table .subhead {
    font-size: 10pt;
}

.pengesahan-proses  { width: 18%; text-align: center; }
.pengesahan-nama    { width: 31%; text-align: left; }
.pengesahan-jabatan { width: 15%; text-align: center; }
.pengesahan-ttd     { width: 19%; text-align: center; }
.pengesahan-tanggal { width: 17%; text-align: center; }

.pengesahan-ttd img {
    max-height: 52px;
    max-width: 100px;
    display: inline-block;
}

.pengesahan-row-tall td {
    height: 58px;
}

/* ===================== CONTENT PAGE ===================== */
.content-wrap {
    margin-top: 0;
    padding-top: 4mm;
    padding-bottom: 10mm;
}


.section-title {
    font-weight: bold;
    text-transform: uppercase;
    font-size: 12pt;
    margin: 0 0 10px 0;
    text-align: left;
}

.content-box {
    font-size: 12pt;
    line-height: 1.5;
    text-align: justify;
}

.content-box ol {
    margin: 0 0 0 18px;
    padding: 0;
}

.content-box > ol > li {
    margin-bottom: 8px;
}

.content-item-title {
    font-weight: bold;
    text-transform: uppercase;
    font-size: 12pt;
    margin: 0 0 4px 0;
    page-break-after: avoid;
}

.content-item-body {
    margin-left: 0.35cm;
    margin-bottom: 10px;
}

.content-item {
    margin-bottom: 10px;
    page-break-inside: auto;
    break-inside: auto;
}

.content-item-body p {
    margin: 0 0 6px 0;
    text-indent: 0;
}

.content-item-body p:last-child {
    margin-bottom: 0;
}

.content-item-body ul,
.content-item-body ol,
.hanging-value ul,
.hanging-value ol {
    margin-top: 4px;
    margin-bottom: 6px;
    margin-left: 0;
    padding-left: 20px;
    list-style-position: outside;
}

.content-item-body li {
    margin-bottom: 4px;
    line-height: 1.4;
    padding-left: 4px;
    text-indent: 0;
}

.content-item-body ol ol,
.hanging-value ol ol {
    list-style-type: lower-alpha;
    margin-top: 3px;
    margin-bottom: 3px;
    padding-left: 20px;
}

.content-item-body ol ol ol,
.hanging-value ol ol ol {
    list-style-type: lower-roman;
    padding-left: 18px;
}

.hanging-value p {
    margin: 0 0 6px 0;
    text-indent: 0;
}

.hanging-value li {
    margin-bottom: 4px;
    line-height: 1.4;
    padding-left: 4px;
    text-indent: 0;
}

.hanging-value p:last-child {
    margin-bottom: 0;
}

.hanging-list {
    list-style: decimal;
    margin: 4px 0 6px 0;
    padding-left: 20px;
}

.hanging-list > li {
    position: static;
    padding-left: 4px;
    margin-bottom: 8px;
}

.hanging-list > li::before {
    content: none;
}

.hanging-label {
    font-weight: bold;
    margin-bottom: 2px;
}

.hanging-value {
    margin-top: 2px;
}

.page-break {
    page-break-before: always;
}

.keep-together {
    page-break-inside: avoid;
}

table,
tr,
td,
th {
    page-break-inside: avoid;
}

@media print {
    .page-actions {
        display: none !important;
    }
}


</style>
</head>
<body>

<?php if (! $modePdf): ?>
<div class="page-actions">
    <button type="button" class="btn" onclick="window.print()">Cetak</button>
    <button type="button" class="btn btn-secondary" onclick="window.history.back()">Kembali</button>
</div>
<?php endif; ?>

<?php
$namaInstitusi  = trim((string) ($profil['nama_institusi'] ?? ''));
$namaInstitusi = $namaInstitusi !== '' ? $namaInstitusi : 'Nama Institusi';
$namaStandar    = trim((string) ($standar['nama_standar'] ?? ''));
$namaStandar = $namaStandar !== '' ? $namaStandar : 'Nama Standar';
$kodeDokumen    = trim((string) ($dokumen['kode_dokumen'] ?? ''));
$tanggalDokumen = trim((string) ($dokumen['tanggal_dokumen'] ?? ''));
$revisi         = trim((string) ($dokumen['revisi'] ?? ''));
$halaman        = '';
$visiInstitusi   = ($profil['visi'] ?? '') !== '' ? (string) $profil['visi'] : '<p>-</p>';
$misiInstitusi   = ($profil['misi'] ?? '') !== '' ? (string) $profil['misi'] : '<p>-</p>';
$tujuanInstitusi = ($profil['tujuan'] ?? '') !== '' ? (string) $profil['tujuan'] : '<p>-</p>';
$sasaranInstitusi = ($profil['sasaran'] ?? '') !== '' ? (string) $profil['sasaran'] : '<p>-</p>';

$profilInstitusiBody = '
<ol class="hanging-list">
    <li>
        <div class="hanging-label">Visi</div>
        <div class="hanging-value">' . $visiInstitusi . '</div>
    </li>
    <li>
        <div class="hanging-label">Misi</div>
        <div class="hanging-value">' . $misiInstitusi . '</div>
    </li>
    <li>
        <div class="hanging-label">Tujuan</div>
        <div class="hanging-value">' . $tujuanInstitusi . '</div>
    </li>
    <li>
        <div class="hanging-label">Sasaran</div>
        <div class="hanging-value">' . $sasaranInstitusi . '</div>
    </li>
</ol>';

$toSingleHanging = static function (?string $html): string {
    return trim((string) $html) !== '' ? (string) $html : '-';
};

// Resolve logo
$logoPath = null;
$logoCandidates = [];
if (! empty($profil['logo'])) {
    $logoCandidates[] = basename((string) $profil['logo']);
}
$logoCandidates[] = 'logo-pt.png';
$logoCandidates   = array_values(array_unique($logoCandidates));
foreach ($logoCandidates as $logoFilename) {
    $logoFilePath = FCPATH . 'uploads/logo_institusi/' . $logoFilename;
    if (! is_file($logoFilePath)) {
        continue;
    }
    if ($modePdf) {
        $ext  = strtolower(pathinfo($logoFilename, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/png',
        };
        $logoContent = file_get_contents($logoFilePath);
        if ($logoContent !== false) {
            $logoPath = 'data:' . $mime . ';base64,' . base64_encode($logoContent);
            break;
        }
    } else {
        $logoPath = base_url('uploads/logo_institusi/' . rawurlencode((string) $logoFilename));
        break;
    }
}

function resolveTtdSrc(string $filename, bool $modePdf): string
{
    $path = FCPATH . 'uploads/ttd_standar/' . $filename;
    if ($modePdf && is_file($path)) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'image/png',
        };
        $data = file_get_contents($path);
        if ($data !== false) {
            return 'data:' . $mime . ';base64,' . base64_encode($data);
        }
    }
    return base_url('uploads/ttd_standar/' . rawurlencode($filename));
}

$contentSections = [
    ['title' => 'A. Visi, Misi, Tujuan, dan Sasaran ' . $namaInstitusi, 'body' => $profilInstitusiBody],
    ['title' => 'B. Rasional Standar', 'body' => $toSingleHanging($dokumen['rasional'] ?? '-')],
    ['title' => 'C. Subjek/Pihak yang Bertanggung Jawab', 'body' => $toSingleHanging($dokumen['subjek_bertanggung_jawab'] ?? '-')],
    ['title' => 'D. Definisi Istilah', 'body' => $toSingleHanging($dokumen['definisi_istilah'] ?? '-')],
    ['title' => 'E. Pernyataan Isi Standar', 'body' => $toSingleHanging($dokumen['pernyataan_isi_standar'] ?? '-')],
    ['title' => 'F. Indikator Ketercapaian Standar', 'body' => $toSingleHanging($dokumen['indikator_ketercapaian'] ?? '-')],
    ['title' => 'G. Strategi Pencapaian Standar', 'body' => $toSingleHanging($dokumen['strategi_pencapaian'] ?? '-')],
    ['title' => 'H. Dokumen Terkait Pelaksanaan Standar', 'body' => $toSingleHanging($dokumen['dokumen_terkait'] ?? '-')],
    ['title' => 'I. Referensi', 'body' => $toSingleHanging($dokumen['referensi'] ?? '-')],
];
?>

<!-- COVER (PAGE 1) -->
<div class="cover-page">
    <div class="cover-inner">
        <?php if ($logoPath): ?>
            <img src="<?= esc($logoPath); ?>" alt="Logo" class="cover-logo">
        <?php endif; ?>
        <div class="cover-spmi">
            Sistem Penjaminan Mutu Internal<br>
            <?= esc($namaInstitusi); ?>
        </div>
        <div class="cover-doc-main">Dokumen</div>
        <div class="cover-doc-sub">
            <?= esc($namaStandar); ?><br>
            <?= strtoupper(esc($namaInstitusi)); ?>
        </div>
        <div class="cover-bottom">
            Lembaga Penjaminan Mutu<br>
            <?= esc($namaInstitusi); ?>
            <span class="cover-bottom-year"><?= esc(date('Y')); ?></span>
        </div>
    </div>
</div>

<!-- IDENTITAS + PENGESAHAN (PAGE 2) -->
<section class="doc-page identity-wrap identity-page">
    <div class="page-inner">
        <table class="identity-table">
            <tr>
                <td rowspan="4" class="identity-logo-cell">
                    <?php if ($logoPath): ?>
                        <img src="<?= esc($logoPath); ?>" alt="Logo">
                    <?php endif; ?>
                </td>
                <td rowspan="4" class="identity-title-cell">
                    <span class="line-main"><?= strtoupper(esc($namaInstitusi)); ?></span>
                    <span class="line-main">DOKUMEN</span>
                    <span class="line-main"><?= esc($namaStandar); ?></span>
                </td>
                <td class="identity-meta-label">Kode/No.</td>
                <td class="identity-meta-sep">:</td>
                <td class="identity-meta-value"><?= esc($kodeDokumen !== '' ? $kodeDokumen : '-'); ?></td>
            </tr>
            <tr>
                <td class="identity-meta-label">Tanggal</td>
                <td class="identity-meta-sep">:</td>
                <td class="identity-meta-value"><?= esc($tanggalDokumen !== '' ? $tanggalDokumen : '-'); ?></td>
            </tr>
            <tr>
                <td class="identity-meta-label">Revisi</td>
                <td class="identity-meta-sep">:</td>
                <td class="identity-meta-value"><?= esc($revisi !== '' ? $revisi : '-'); ?></td>
            </tr>
            <tr>
                <td class="identity-meta-label">Halaman</td>
                <td class="identity-meta-sep">:</td>
                <td class="identity-meta-value"><?= esc($halaman !== '' ? $halaman : '-'); ?></td>
            </tr>
        </table>

        <div class="identity-doc-title">
            DOKUMEN<br>
            <?= strtoupper(esc($namaStandar)); ?><br>
            <?= strtoupper(esc($namaInstitusi)); ?>
        </div>

        <table class="pengesahan-table">
            <thead>
                <tr>
                    <th class="pengesahan-proses" rowspan="2">Proses</th>
                    <th colspan="3">Penanggung Jawab</th>
                    <th class="pengesahan-tanggal" rowspan="2">Tanggal</th>
                </tr>
                <tr>
                    <th class="pengesahan-nama subhead">Nama</th>
                    <th class="pengesahan-jabatan subhead">Jabatan</th>
                    <th class="pengesahan-ttd subhead">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $urutanProses = ['Perumusan', 'Pemeriksaan', 'Persetujuan', 'Pengesahan', 'Pengendalian'];
                foreach ($urutanProses as $proses):
                    $user = $penandatanganProses[$proses] ?? null;
                ?>
                <tr class="pengesahan-row-tall">
                    <td class="pengesahan-proses"><?= esc($proses); ?></td>
                    <td class="pengesahan-nama"><?= esc($user['nama'] ?? '-'); ?></td>
                    <td class="pengesahan-jabatan"><?= esc($user['jabatan'] ?? '-'); ?></td>
                    <td class="pengesahan-ttd">
                        <?php if (! empty($user['ttd_digital'])): ?>
                            <img src="<?= esc(resolveTtdSrc($user['ttd_digital'], $modePdf)); ?>" alt="TTD">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="pengesahan-tanggal"><?= esc($user['tanggal_ttd'] ?? '-'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- ISI DOKUMEN (PAGE 3+) -->
<section class="doc-page content-wrap">
    <div class="page-inner">
        <div class="content-box">
            <?php foreach ($contentSections as $section): ?>
                <div class="content-item">
                    <div class="content-item-title"><?= esc($section['title']); ?></div>
                    <div class="content-item-body"><?= $section['body'] !== '' ? $section['body'] : '-'; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

</body>
</html>
