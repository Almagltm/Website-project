<?php
// ============================================================
// SESSION CHECK & KONEKSI DATABASE
// ============================================================
require_once __DIR__ . '/../db.php';

// Session sudah di-start oleh db.php
// Cek apakah user sudah login (user atau admin)
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: MASUK.php");
    exit();
}

// ============================================================
// PERIODE FILTER (bulan & tahun)
// ============================================================
$bulan_filter = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('m');
$tahun_filter = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');

// Validasi range
if ($bulan_filter < 1 || $bulan_filter > 12) $bulan_filter = (int)date('m');
if ($tahun_filter < 2020 || $tahun_filter > 2099) $tahun_filter = (int)date('Y');

$nama_bulan = [
    1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
    5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
    9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
];
$label_periode = $nama_bulan[$bulan_filter] . ' ' . $tahun_filter;

// ============================================================
// QUERY HELPER — aman dari SQL injection
// ============================================================
function q($conn, $sql) {
    // Support both object dan procedural style
    if (is_object($conn)) {
        $r = $conn->query($sql);
    } else {
        $r = mysqli_query($conn, $sql);
    }
    
    if (!$r) return [];
    $rows = [];
    
    if (is_object($r)) {
        while ($row = $r->fetch_assoc()) $rows[] = $row;
    } else {
        while ($row = mysqli_fetch_assoc($r)) $rows[] = $row;
    }
    return $rows;
}
function q1($conn, $sql) {
    $rows = q($conn, $sql);
    return $rows ? $rows[0] : [];
}

// ============================================================
// 1. LAPORAN PER KATEGORI (bulan ini)
// ============================================================
$sql_kategori = "
    SELECT 
        COALESCE(k.nama_kategori, l.kategori_lainnya, 'Lainnya') AS nama_kategori,
        l.id_kategori,
        COUNT(*) AS jumlah
    FROM laporan l
    LEFT JOIN kategori k ON k.id_kategori = l.id_kategori
    WHERE MONTH(l.created_at) = $bulan_filter
      AND YEAR(l.created_at)  = $tahun_filter
    GROUP BY l.id_kategori, COALESCE(k.nama_kategori, l.kategori_lainnya, 'Lainnya')
    ORDER BY jumlah DESC
";
$data_kategori = q($conn, $sql_kategori);

// Mapping kategori (jika perlu custom nama)
$map_kategori = [];


// ============================================================
// 2. RINGKASAN BULAN INI
// ============================================================
$sql_total = "
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status IN ('selesai','diproses') THEN 1 ELSE 0 END) AS ditangani
    FROM laporan
    WHERE MONTH(created_at) = $bulan_filter
      AND YEAR(created_at)  = $tahun_filter
";
$ringkasan = q1($conn, $sql_total);
$total     = (int)($ringkasan['total'] ?? 0);
$ditangani = (int)($ringkasan['ditangani'] ?? 0);
$pending   = $total - $ditangani;
$pct_selesai = $total > 0 ? round($ditangani / $total * 100) : 0;

// Kolom waktu_tanggap_jam tidak ada di database, tampilkan placeholder
$label_waktu = '—';

// ============================================================
// 3. TREN 6 BULAN TERAKHIR (untuk chart garis kecil)
// ============================================================
$sql_tren = "
    SELECT 
        MONTH(created_at) AS bln,
        YEAR(created_at)  AS thn,
        COUNT(*)          AS total
    FROM laporan
    WHERE created_at >= DATE_FORMAT(DATE_SUB(
        STR_TO_DATE(CONCAT($tahun_filter,'-',$bulan_filter,'-01'),'%Y-%m-%d'),
        INTERVAL 5 MONTH), '%Y-%m-01')
      AND created_at < DATE_FORMAT(
        DATE_ADD(STR_TO_DATE(CONCAT($tahun_filter,'-',$bulan_filter,'-01'),'%Y-%m-%d'),
        INTERVAL 1 MONTH), '%Y-%m-01')
    GROUP BY thn, bln
    ORDER BY thn ASC, bln ASC
";
$data_tren = q($conn, $sql_tren);

// ============================================================
// 4. DISTRIBUSI STATUS (pie / donut data)
// ============================================================
$sql_status = "
    SELECT status, COUNT(*) AS jumlah
    FROM laporan
    WHERE MONTH(created_at) = $bulan_filter
      AND YEAR(created_at)  = $tahun_filter
      AND status IS NOT NULL
    GROUP BY status
    ORDER BY jumlah DESC
";
$data_status = q($conn, $sql_status);

// ============================================================
// 5. LAPORAN TERBARU (tabel 10 terakhir)
// ============================================================
$sql_terbaru = "
    SELECT l.id_laporan as id, l.judul, COALESCE(k.nama_kategori, l.kategori_lainnya, 'Lainnya') AS nama_kategori, 
           l.status, l.created_at, l.lokasi
    FROM laporan l
    LEFT JOIN kategori k ON k.id_kategori = l.id_kategori
    WHERE MONTH(l.created_at) = $bulan_filter
      AND YEAR(l.created_at)  = $tahun_filter
    ORDER BY l.created_at DESC
    LIMIT 10
";
$data_terbaru = q($conn, $sql_terbaru);

// Siapkan array JS
$js_labels  = array_map(fn($r) => $r['nama_kategori'], $data_kategori);
$js_values  = array_map(fn($r) => (int)$r['jumlah'], $data_kategori);
$js_tren_labels = array_map(fn($r) => ($nama_bulan[(int)$r['bln']] ?? '') . ' ' . $r['thn'], $data_tren);
$js_tren_values = array_map(fn($r) => (int)$r['total'], $data_tren);

$status_labels = array_map(fn($r) => ucfirst($r['status']), $data_status);
$status_values = array_map(fn($r) => (int)$r['jumlah'], $data_status);

// Warna palette bar chart
$bar_colors = ['#e9b96e','#f5d080','#c9851a','#8a5c10','#1e3d8f','#4a7fd4','#6dbf87','#e07070'];

// Format badge status
function status_badge($s) {
    $map = [
        'selesai'   => ['label'=>'Selesai',         'bg'=>'#d1fae5','color'=>'#065f46'],
        'diproses'  => ['label'=>'Sedang Diproses', 'bg'=>'#fef3c7','color'=>'#92400e'],
        'pending'   => ['label'=>'Menunggu',        'bg'=>'#fee2e2','color'=>'#991b1b'],
        'ditolak'   => ['label'=>'Ditolak',         'bg'=>'#dbeafe','color'=>'#1e40af'],
    ];
    $d = $map[strtolower($s)] ?? ['label'=>ucfirst($s),'bg'=>'#f3f4f6','color'=>'#374151'];
    return "<span style=\"display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:{$d['bg']};color:{$d['color']}\">{$d['label']}</span>";
}

// Tentukan path ASSETS (untuk Users folder selalu ../ASSETS/)
$asset_path = '../ASSETS/';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Statistik Laporan – Aksi Kita</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
/* ===================== RESET & BASE ===================== */
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
:root {
    --blue-dark:  #1e3d8f;
    --blue-mid:   #2b4faa;
    --gold:       #c9851a;
    --gold-light: #e9b96e;
    --gold-pale:  #fdf3e3;
    --bg:         #f0f4fb;
    --white:      #ffffff;
    --text:       #1a2540;
    --muted:      #64748b;
    --border:     #e2e8f0;
    --radius-lg:  16px;
    --radius-md:  10px;
    --shadow-sm:  0 2px 8px rgba(30,61,143,.07);
    --shadow-md:  0 6px 24px rgba(30,61,143,.10);
    --shadow-lg:  0 12px 40px rgba(30,61,143,.13);
}
body {
    background: var(--bg);
    color: var(--text);
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    min-height: 100vh;
}
a { text-decoration: none; color: inherit; }

/* ===================== NAVBAR / HEADER ===================== */
.topbar {
    background: var(--blue-dark);
    padding: 0 40px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 12px rgba(0,0,0,.18);
}
.topbar-left { display:flex; align-items:center; gap:16px; }
.back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px; height: 36px;
    background: rgba(255,255,255,.15);
    border-radius: 50%;
    color: white;
    font-size: 14px;
    transition: background .2s;
}
.back-btn:hover { background: rgba(255,255,255,.3); }
.topbar h1 {
    color: white;
    font-size: 18px;
    font-weight: 700;
    letter-spacing: .3px;
}
.topbar-badge {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    color: rgba(255,255,255,.85);
    border-radius: 20px;
    padding: 4px 14px;
    font-size: 12px;
    font-weight: 500;
}

/* ===================== PERIOD FILTER BAR ===================== */
.filter-bar {
    background: white;
    border-bottom: 1px solid var(--border);
    padding: 14px 40px;
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}
.filter-bar label {
    font-size: 13px;
    font-weight: 600;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 6px;
}
.filter-bar select {
    border: 1.5px solid var(--border);
    border-radius: 8px;
    padding: 7px 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    color: var(--text);
    background: #f8faff;
    cursor: pointer;
    outline: none;
    transition: border-color .2s;
}
.filter-bar select:focus { border-color: var(--blue-mid); }
.btn-filter {
    background: var(--blue-dark);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 8px 20px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: background .2s, transform .1s;
}
.btn-filter:hover { background: var(--blue-mid); transform: translateY(-1px); }

/* ===================== MAIN LAYOUT ===================== */
.page-wrap {
    max-width: 1280px;
    margin: 0 auto;
    padding: 30px 40px 60px;
}

/* ===================== SECTION TITLE ===================== */
.section-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--blue-dark);
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.section-title::before {
    content: '';
    display: inline-block;
    width: 4px; height: 18px;
    background: var(--gold);
    border-radius: 4px;
}

/* ===================== KPI CARDS ===================== */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.kpi-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 26px 24px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}
.kpi-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
.kpi-card::after {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 4px;
}
.kpi-card.blue::after  { background: linear-gradient(90deg, #1e3d8f, #4a7fd4); }
.kpi-card.gold::after  { background: linear-gradient(90deg, #c9851a, #e9b96e); }
.kpi-card.green::after { background: linear-gradient(90deg, #059669, #6dbf87); }
.kpi-card.red::after   { background: linear-gradient(90deg, #dc2626, #f87171); }

.kpi-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    margin-bottom: 14px;
}
.kpi-card.blue  .kpi-icon { background: #eff3ff; color: #1e3d8f; }
.kpi-card.gold  .kpi-icon { background: #fef3c7; color: #c9851a; }
.kpi-card.green .kpi-icon { background: #d1fae5; color: #059669; }
.kpi-card.red   .kpi-icon { background: #fee2e2; color: #dc2626; }

.kpi-value {
    font-size: 32px;
    font-weight: 800;
    color: var(--text);
    line-height: 1;
    margin-bottom: 6px;
}
.kpi-label {
    font-size: 12px;
    color: var(--muted);
    font-weight: 500;
    line-height: 1.4;
}
.kpi-progress {
    margin-top: 12px;
    height: 5px;
    background: #f1f5f9;
    border-radius: 3px;
    overflow: hidden;
}
.kpi-progress-fill {
    height: 100%;
    border-radius: 3px;
    background: linear-gradient(90deg, #1e3d8f, #4a7fd4);
}

/* ===================== CHART GRID ===================== */
.chart-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}
@media (max-width: 900px) { .chart-grid { grid-template-columns: 1fr; } }

.card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 28px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
}
.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
}
.card-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
}
.card-subtitle {
    font-size: 11px;
    color: var(--muted);
    margin-top: 2px;
}
.chart-wrap { position: relative; }

/* ===================== TREN CHART ROW ===================== */
.tren-row {
    margin-bottom: 30px;
}

/* ===================== TABLE ===================== */
.table-card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    overflow: hidden;
    margin-bottom: 30px;
}
.table-card .card-header {
    padding: 22px 28px 0;
}
.data-table {
    width: 100%;
    border-collapse: collapse;
}
.data-table thead th {
    background: #f8faff;
    padding: 12px 18px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--muted);
    text-align: left;
    border-bottom: 1px solid var(--border);
}
.data-table tbody td {
    padding: 13px 18px;
    font-size: 13px;
    border-bottom: 1px solid #f1f5f9;
    color: var(--text);
    vertical-align: middle;
}
.data-table tbody tr:last-child td { border-bottom: none; }
.data-table tbody tr:hover td { background: #f8faff; }
.id-chip {
    background: #eff3ff;
    color: #1e3d8f;
    border-radius: 6px;
    padding: 2px 8px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}
.empty-row td {
    text-align: center;
    padding: 40px;
    color: var(--muted);
    font-size: 13px;
}

/* ===================== GOLDEN SUMMARY BAR ===================== */
.summary-bar {
    background: linear-gradient(100deg, #1a3070 0%, #2b4faa 45%, #b8760e 100%);
    border-radius: var(--radius-lg);
    padding: 30px 36px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 24px;
    margin-bottom: 30px;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
}
.summary-bar::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
.summary-stat {
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
    z-index: 1;
}
.summary-stat + .summary-stat {
    padding-left: 24px;
    border-left: 1px solid rgba(255,255,255,.18);
}
@media (max-width: 700px) {
    .summary-stat + .summary-stat { border-left: none; padding-left: 0; border-top: 1px solid rgba(255,255,255,.18); padding-top: 20px; }
}
.summary-icon {
    width: 46px; height: 46px;
    border-radius: 12px;
    border: 1.5px solid rgba(255,255,255,.35);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    color: white;
    flex-shrink: 0;
    background: rgba(255,255,255,.08);
}
.summary-info h4 {
    font-size: 12px;
    font-weight: 500;
    color: rgba(255,255,255,.75);
    line-height: 1.4;
    margin-bottom: 4px;
}
.summary-info .val {
    font-size: 26px;
    font-weight: 800;
    color: white;
    line-height: 1;
}
.summary-info .val.sm {
    font-size: 20px;
}

/* ===================== KATEGORI LIST ===================== */
.kategori-list { list-style: none; }
.kategori-list li {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
}
.kategori-list li:last-child { border-bottom: none; }
.kat-dot {
    width: 12px; height: 12px;
    border-radius: 3px;
    flex-shrink: 0;
}
.kat-name { flex: 1; color: var(--text); }
.kat-bar-wrap {
    width: 90px;
    height: 6px;
    background: #f1f5f9;
    border-radius: 3px;
    overflow: hidden;
}
.kat-bar-fill { height: 100%; border-radius: 3px; }
.kat-count { font-weight: 700; color: var(--text); min-width: 28px; text-align: right; }

/* ===================== RESPONSIVE ===================== */
@media (max-width: 768px) {
    .page-wrap { padding: 20px 16px 50px; }
    .filter-bar { padding: 12px 16px; }
    .topbar { padding: 0 16px; }
    .kpi-value { font-size: 26px; }
    .data-table { display: block; overflow-x: auto; }
}

/* ===================== FOOTER ===================== */
.main-footer {
    background: linear-gradient(165deg, #080e18 0%, #102647 70%, #9c7719 120%);
    color: #fff;
    padding: 60px 70px;
    font-family: 'Poppins', sans-serif;
}
.footer-top {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 40px;
}
.footer-logo {
    width: 75px; height: 55px;
    object-fit: cover;
}
.footer-top h3 {
    font-size: 20px;
    font-weight: 700;
}
.footer-content {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    margin-bottom: 45px;
    gap: 20px;
}
.footer-col { flex: 1; min-width: 200px; }
.footer-col p  { margin: 6px 0; color: #ccc; font-size: 15px; }
.footer-col a  { display: block; margin: 6px 0; color: #eee; text-decoration: none; font-size: 15px; transition: .2s; }
.footer-col a:hover { color: #4a7fd4; }
.footer-social { display: flex; gap: 15px; margin-bottom: 35px; }
.footer-social a {
    width: 40px; height: 40px;
    border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    color: #000;
    background: #fff;
    text-decoration: none;
    font-size: 18px;
    transition: .3s;
}
.footer-social a:hover { transform: translateY(-5px); }
.footer-bottom { text-align: center; font-size: 14px; color: #ccc; margin-top: 10px; }
@media (max-width: 800px) {
    .footer-content { flex-direction: column; }
    .footer-top { flex-direction: column; text-align: center; }
    .main-footer { padding: 40px 20px; }
}
</style>
</head>
<body>

<!-- ==================== TOPBAR ==================== -->
<header class="topbar">
    <div class="topbar-left">
        <a href="<?= isset($_SESSION['admin_id']) ? '../Admin/Beranda_Admin.php' : 'BERANDA2.php' ?>" class="back-btn"><i class="fa-solid fa-chevron-left"></i></a>
        <h1>Statistik Laporan</h1>
    </div>
    <span class="topbar-badge"><i class="fa-regular fa-calendar-days" style="margin-right:5px"></i><?= htmlspecialchars($label_periode) ?></span>
</header>

<!-- ==================== FILTER BAR ==================== -->
<div class="filter-bar">
    <form method="GET" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
        <label><i class="fa-solid fa-filter"></i> Filter Periode:</label>
        <select name="bulan">
            <?php foreach ($nama_bulan as $num => $nama): ?>
            <option value="<?= $num ?>" <?= $num == $bulan_filter ? 'selected' : '' ?>>
                <?= $nama ?>
            </option>
            <?php endforeach; ?>
        </select>
        <select name="tahun">
            <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
            <option value="<?= $y ?>" <?= $y == $tahun_filter ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit" class="btn-filter">
            <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
        </button>
    </form>
</div>

<!-- ==================== MAIN ==================== -->
<div class="page-wrap">

    <!-- KPI CARDS -->
    <div class="section-title">Ringkasan <?= htmlspecialchars($label_periode) ?></div>
    <div class="kpi-grid">

        <div class="kpi-card blue">
            <div class="kpi-icon"><i class="fa-solid fa-file-lines"></i></div>
            <div class="kpi-value"><?= number_format($total) ?></div>
            <div class="kpi-label">Total Laporan Masuk</div>
        </div>

        <div class="kpi-card green">
            <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
            <div class="kpi-value"><?= number_format($ditangani) ?></div>
            <div class="kpi-label">Ditangani / Selesai</div>
            <div class="kpi-progress">
                <div class="kpi-progress-fill" style="width:<?= $pct_selesai ?>%;background:linear-gradient(90deg,#059669,#6dbf87)"></div>
            </div>
        </div>

        <div class="kpi-card red">
            <div class="kpi-icon"><i class="fa-solid fa-hourglass-half"></i></div>
            <div class="kpi-value"><?= number_format($pending) ?></div>
            <div class="kpi-label">Menunggu / Pending</div>
        </div>

        <div class="kpi-card gold">
            <div class="kpi-icon"><i class="fa-solid fa-clock"></i></div>
            <div class="kpi-value" style="font-size:20px;padding-top:6px"><?= htmlspecialchars($label_waktu) ?></div>
            <div class="kpi-label">Rata-rata Waktu Tanggap</div>
        </div>

    </div>

    <!-- GOLDEN SUMMARY BAR -->
    <div class="summary-bar">
        <div class="summary-stat">
            <div class="summary-icon"><i class="fa-solid fa-chart-bar"></i></div>
            <div class="summary-info">
                <h4>Total Laporan<br>Bulan Ini</h4>
                <div class="val"><?= number_format($total) ?></div>
            </div>
        </div>
        <div class="summary-stat">
            <div class="summary-icon"><i class="fa-solid fa-gear"></i></div>
            <div class="summary-info">
                <h4>Ditangani</h4>
                <div class="val"><?= number_format($ditangani) ?></div>
            </div>
        </div>
        <div class="summary-stat">
            <div class="summary-icon"><i class="fa-solid fa-percent"></i></div>
            <div class="summary-info">
                <h4>Tingkat Penyelesaian</h4>
                <div class="val"><?= $pct_selesai ?>%</div>
            </div>
        </div>
        <div class="summary-stat">
            <div class="summary-icon"><i class="fa-solid fa-stopwatch"></i></div>
            <div class="summary-info">
                <h4>Rata-rata Waktu Tanggap</h4>
                <div class="val sm"><?= htmlspecialchars($label_waktu) ?></div>
            </div>
        </div>
    </div>

    <!-- CHART GRID: Bar + Donut -->
    <div class="chart-grid">

        <!-- BAR CHART: Laporan per Kategori -->
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Laporan per Kategori</div>
                    <div class="card-subtitle"><?= htmlspecialchars($label_periode) ?></div>
                </div>
            </div>
            <div class="chart-wrap" style="height:280px">
                <?php if (empty($data_kategori)): ?>
                <div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--muted);font-size:13px;">
                    <i class="fa-solid fa-circle-info" style="margin-right:8px"></i> Belum ada data untuk periode ini.
                </div>
                <?php else: ?>
                <canvas id="barChart"></canvas>
                <?php endif; ?>
            </div>
        </div>

        <!-- DONUT: Distribusi Status -->
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Distribusi Status</div>
                    <div class="card-subtitle"><?= htmlspecialchars($label_periode) ?></div>
                </div>
            </div>
            <div class="chart-wrap" style="height:280px">
                <?php if (empty($data_status)): ?>
                <div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--muted);font-size:13px;">
                    <i class="fa-solid fa-circle-info" style="margin-right:8px"></i> Belum ada data.
                </div>
                <?php else: ?>
                <canvas id="donutChart"></canvas>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- TREN 6 BULAN -->
    <div class="tren-row">
        <div class="section-title">Tren Laporan 6 Bulan Terakhir</div>
        <div class="card">
            <div class="chart-wrap" style="height:200px">
                <?php if (empty($data_tren)): ?>
                <div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--muted);font-size:13px;">
                    <i class="fa-solid fa-circle-info" style="margin-right:8px"></i> Belum ada data tren.
                </div>
                <?php else: ?>
                <canvas id="trenChart"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- KATEGORI DETAIL LIST -->
    <?php if (!empty($data_kategori)): ?>
    <div style="margin-bottom:30px;">
        <div class="section-title">Detail per Kategori</div>
        <div class="card">
            <?php
            $max_kat = max(array_column($data_kategori, 'jumlah'));
            $colors_kat = ['#1e3d8f','#c9851a','#059669','#dc2626','#7c3aed','#0ea5e9','#f59e0b','#10b981'];
            ?>
            <ul class="kategori-list">
                <?php foreach ($data_kategori as $i => $row):
                    $nama_kat = $row['nama_kategori'];
                    $pct = $max_kat > 0 ? round($row['jumlah'] / $max_kat * 100) : 0;
                    $col = $colors_kat[$i % count($colors_kat)];
                ?>
                <li>
                    <span class="kat-dot" style="background:<?= $col ?>"></span>
                    <span class="kat-name"><?= htmlspecialchars($nama_kat) ?></span>
                    <div class="kat-bar-wrap">
                        <div class="kat-bar-fill" style="width:<?= $pct ?>%;background:<?= $col ?>"></div>
                    </div>
                    <span class="kat-count"><?= $row['jumlah'] ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <!-- TABEL LAPORAN TERBARU -->
    <div class="section-title">Laporan Terbaru</div>
    <div class="table-card">
        <div class="card-header" style="margin-bottom:0;padding-bottom:16px;">
            <div>
                <div class="card-title">10 Laporan Terakhir</div>
                <div class="card-subtitle"><?= htmlspecialchars($label_periode) ?></div>
            </div>
            <a href="<?= isset($_SESSION['admin_id']) ? 'Kelola_Laporan.php' : 'laporan_saya.php' ?>" style="font-size:12px;color:var(--blue-mid);font-weight:600;display:flex;align-items:center;gap:4px;">
                Lihat semua <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
            </a>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data_terbaru)): ?>
                <tr class="empty-row">
                    <td colspan="6">
                        <i class="fa-solid fa-inbox" style="font-size:28px;color:#cbd5e1;display:block;margin-bottom:8px"></i>
                        Belum ada laporan pada periode ini.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($data_terbaru as $row): ?>
                <tr>
                    <td><span class="id-chip">#<?= $row['id'] ?></span></td>
                    <td><?= htmlspecialchars($row['judul'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['nama_kategori'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['lokasi'] ?? '—') ?></td>
                    <td><?= $row['created_at'] ? date('d M Y', strtotime($row['created_at'])) : '—' ?></td>
                    <td><?= status_badge($row['status']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div><!-- end page-wrap -->

<!-- ==================== FOOTER ==================== -->
<footer class="main-footer">
    <div class="footer-top">
        <img src="<?= $asset_path ?>LOGO.png" class="footer-logo" alt="Aksi Kita">
        <h3>Aksi Kita</h3>
    </div>
    <div class="footer-content">
        <div class="footer-col">
            <p>Jl. Bachireng No. 12, Indonesia</p>
            <p>0821 6888 9060</p>
            <p>info@aksikita.id</p>
        </div>
        <div class="footer-col">
            <a href="#">Unit Layanan Terpadu</a>
            <a href="#">Cara Kerja</a>
            <a href="#">FAQ</a>
            <a href="#">Aturan Penggunaan</a>
        </div>
        <div class="footer-col">
            <a href="#">Lapor</a>
            <a href="#">Survei</a>
            <a href="#">Peta Situs</a>
            <a href="#">Arsip</a>
        </div>
    </div>
    <div class="footer-social">
        <a href="#"><i class="fab fa-whatsapp"></i></a>
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-youtube"></i></a>
        <a href="#"><i class="fab fa-tiktok"></i></a>
    </div>
    <div class="footer-bottom">© <?= date('Y') ?> AksiKita. Semua Hak Dilindungi.</div>
</footer>

<!-- ==================== CHART.JS SCRIPTS ==================== -->
<script>
Chart.defaults.font.family = "'Poppins', sans-serif";
Chart.defaults.color = '#64748b';

// ---- BAR CHART ----
<?php if (!empty($data_kategori)): ?>
(function(){
    const labels = <?= json_encode($js_labels, JSON_UNESCAPED_UNICODE) ?>;
    const values = <?= json_encode($js_values) ?>;
    const palette = ['#1e3d8f','#c9851a','#059669','#dc2626','#7c3aed','#0ea5e9','#f59e0b','#10b981'];
    const colors  = labels.map((_,i) => palette[i % palette.length]);

    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: colors.map(c => c + 'cc'),
                borderColor: colors,
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed.y + ' laporan'
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { stepSize: 10, font: { size: 11 } }
                }
            }
        }
    });
})();
<?php endif; ?>

// ---- DONUT CHART ----
<?php if (!empty($data_status)): ?>
(function(){
    const labels = <?= json_encode($status_labels, JSON_UNESCAPED_UNICODE) ?>;
    const values = <?= json_encode($status_values) ?>;
    const palette = ['#059669','#1e3d8f','#f59e0b','#dc2626','#7c3aed','#0ea5e9'];

    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: palette.slice(0, labels.length),
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 16, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.label + ': ' + ctx.parsed + ' laporan'
                    }
                }
            }
        }
    });
})();
<?php endif; ?>

// ---- TREN LINE CHART ----
<?php if (!empty($data_tren)): ?>
(function(){
    const labels = <?= json_encode($js_tren_labels, JSON_UNESCAPED_UNICODE) ?>;
    const values = <?= json_encode($js_tren_values) ?>;

    new Chart(document.getElementById('trenChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Jumlah Laporan',
                data: values,
                fill: true,
                borderColor: '#1e3d8f',
                backgroundColor: 'rgba(30,61,143,.08)',
                tension: 0.4,
                pointBackgroundColor: '#1e3d8f',
                pointRadius: 5,
                pointHoverRadius: 7,
                borderWidth: 2.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: { label: ctx => ' ' + ctx.parsed.y + ' laporan' }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
})();
<?php endif; ?>
</script>

</body>
</html>