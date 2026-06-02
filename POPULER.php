<?php

require_once 'koneksi.php';

$sql = "
SELECT
    l.id_laporan,
    l.judul,
    l.isi_laporan,
    l.lokasi,
    l.status,
    l.created_at,
    u.nama_lengkap,
    k.nama_kategori,
    (
        SELECT path_foto
        FROM foto_laporan f
        WHERE f.id_laporan = l.id_laporan
        LIMIT 1
    ) AS foto
FROM laporan l
LEFT JOIN users u ON l.id_user = u.id_user
LEFT JOIN kategori k ON l.id_kategori = k.id_kategori
ORDER BY l.created_at DESC
";

$result = $conn->query($sql);

$laporan = [];

while ($row = $result->fetch_assoc()) {

    $laporan[] = [
        'id' => $row['id_laporan'],
        'title' => $row['judul'],
        'desc' => $row['isi_laporan'],
        'img' => !empty($row['foto'])
                    ? $row['foto']
                    : 'ASSETS/download.jpg',
        'province' => $row['lokasi'],
        'province_key' => strtolower($row['lokasi']),
        'user' => '@' . preg_replace('/\s+/', '', strtolower($row['nama_lengkap'])),
        'date' => $row['created_at'],
        'views' => 0,
        'likes' => 0,
        'status' => $row['status']
    ];
}

$provinces = [
    "aceh" => "Aceh", "sumut" => "Sumatera Utara", "sumbar" => "Sumatera Barat",
    "riau" => "Riau", "kepri" => "Kepulauan Riau", "jambi" => "Jambi",
    "sumsel" => "Sumatera Selatan", "babel" => "Kep. Bangka Belitung",
    "bengkulu" => "Bengkulu", "lampung" => "Lampung", "banten" => "Banten",
    "jabar" => "Jawa Barat", "jateng" => "Jawa Tengah", "jatim" => "Jawa Timur",
    "jakarta" => "DKI Jakarta", "yogyakarta" => "DI Yogyakarta", "bali" => "Bali",
    "ntb" => "NTB", "ntt" => "NTT", "kalbar" => "Kalimantan Barat",
    "kalteng" => "Kalimantan Tengah", "kalsel" => "Kalimantan Selatan",
    "kaltim" => "Kalimantan Timur", "kaltara" => "Kalimantan Utara",
    "sulut" => "Sulawesi Utara", "sulteng" => "Sulawesi Tengah",
    "sulsel" => "Sulawesi Selatan", "sultra" => "Sulawesi Tenggara",
    "gorontalo" => "Gorontalo", "sulbar" => "Sulawesi Barat",
    "maluku" => "Maluku", "malut" => "Maluku Utara",
    "papua" => "Papua", "papua-barat" => "Papua Barat",
];

$status_config = [
    "selesai" => [
        "label" => "Selesai",
        "color" => "#10b981",
        "bg" => "#ecfdf5",
        "icon" => "fa-circle-check"
    ],
    "diproses" => [
        "label" => "Diproses",
        "color" => "#f59e0b",
        "bg" => "#fffbeb",
        "icon" => "fa-clock"
    ],
    "pending" => [
        "label" => "Menunggu",
        "color" => "#94a3b8",
        "bg" => "#f1f5f9",
        "icon" => "fa-hourglass"
    ],
    "ditolak" => [
        "label" => "Ditolak",
        "color" => "#ef4444",
        "bg" => "#fef2f2",
        "icon" => "fa-circle-xmark"
    ]
];
?>
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: MASUK.php");
    exit();
}

$nama = $_SESSION['nama_lengkap'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Terpopuler — AksiKita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --navy: #0d1b3e;
            --navy-mid: #162952;
            --blue: #2563eb;
            --blue-bright: #3b82f6;
            --gold: #f59e0b;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-800: #1e293b;
            --font-display: 'Sora', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.07);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.09);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.14);
            --shadow-blue: 0 8px 28px rgba(37,99,235,0.2);
            --radius: 16px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-body);
            background: var(--gray-50);
            color: var(--gray-800);
        }

      
      .navbar {
    background: #1e3d8f;
    color: white;
    display: flex;
    align-items: center;
    padding: 13px 50px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    flex-wrap: wrap;
}

.navbar nav {
    display: flex;
    gap: 15px;
    margin-left: 60px;
    flex-wrap: wrap;
}

.navbar nav a {
    color: white;
    margin: 0 15px;
    padding: 5px 0;
    text-decoration: none;
    transition: .3s;
    font-size: 16px;
}

.navbar nav a:hover,
.navbar nav a.active {
    text-decoration: underline;
}

.logo img {
    height: 55px;
}

.user {
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: auto;
}

.username {
    color: white;
    font-size: 16px;
}

.user-img {
    width: 40px;
    height: 40px;
    object-fit: contain;
}
      
        .page-wrapper { padding-top: 70px; }

      
        .page-hero {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 55%, #1a3a6e 100%);
            padding: 46px 40px 80px;
            position: relative;
            overflow: hidden;
        }

        .page-hero::before {
            content: '';
            position: absolute; top: -80px; right: -50px;
            width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(59,130,246,0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-label {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(245,158,11,0.15);
            border: 1px solid rgba(245,158,11,0.3);
            color: var(--gold);
            padding: 5px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
            letter-spacing: 0.5px; text-transform: uppercase;
            margin-bottom: 14px;
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: 34px; font-weight: 700;
            color: white; line-height: 1.2; margin-bottom: 8px;
        }

        .hero-sub { color: rgba(255,255,255,0.55); font-size: 15px; }

        .hero-stats {
            display: flex; gap: 18px; margin-top: 28px; flex-wrap: wrap;
        }

        .stat-pill {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px; padding: 11px 18px;
            display: flex; flex-direction: column; gap: 2px;
        }

        .stat-pill .val {
            font-family: var(--font-display);
            font-size: 20px; font-weight: 700; color: white;
        }

        .stat-pill .lbl {
            font-size: 11px; color: rgba(255,255,255,0.45);
            text-transform: uppercase; letter-spacing: 0.5px;
        }

      
        .content-area {
            max-width: 1160px;
            margin: -36px auto 0;
            padding: 0 24px 70px;
            position: relative; z-index: 10;
        }

      
        .toolbar {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            padding: 16px 20px;
            display: flex; align-items: center;
            gap: 14px; margin-bottom: 22px;
            flex-wrap: wrap;
        }

        .search-wrap { position: relative; flex: 1; min-width: 200px; }

        .search-wrap i {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400); font-size: 14px;
        }

        .search-wrap input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border: 1.5px solid var(--gray-200);
            border-radius: 10px; font-size: 14px;
            font-family: var(--font-body);
            color: var(--gray-800); outline: none;
            transition: border 0.2s; background: var(--gray-50);
        }

        .search-wrap input:focus { border-color: var(--blue); background: white; }

        .filter-wrap { position: relative; }

        .filter-wrap i {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--blue); font-size: 13px; pointer-events: none;
        }

        .filter-wrap select {
            padding: 10px 14px 10px 38px;
            border: 1.5px solid var(--gray-200);
            border-radius: 10px; font-size: 13px;
            font-family: var(--font-body);
            color: var(--gray-800); outline: none;
            background: var(--gray-50); cursor: pointer;
            transition: border 0.2s; appearance: none;
            min-width: 180px;
        }

        .filter-wrap select:focus { border-color: var(--blue); background: white; }

        .view-toggle {
            display: flex; gap: 4px;
            background: var(--gray-100);
            padding: 4px; border-radius: 9px;
        }

        .view-btn {
            width: 34px; height: 34px;
            border: none; border-radius: 7px;
            cursor: pointer; background: transparent;
            color: var(--gray-400); font-size: 14px;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }

        .view-btn.active { background: white; color: var(--blue); box-shadow: var(--shadow-sm); }

        .section-head {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .section-head h2 {
            font-family: var(--font-display);
            font-size: 18px; font-weight: 700;
            color: var(--navy);
            display: flex; align-items: center; gap: 8px;
        }

        .count-badge {
            background: var(--blue); color: white;
            font-size: 11px; font-weight: 700;
            padding: 2px 8px; border-radius: 20px;
        }

        .section-head a {
            color: var(--blue); font-size: 13px;
            font-weight: 600; text-decoration: none;
        }

        .laporan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
            gap: 20px;
        }

        .laporan-card {
            background: white;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1.5px solid var(--gray-200);
            transition: all 0.25s;
            display: flex; flex-direction: column;
            text-decoration: none; color: inherit;
            animation: fadeUp 0.4s ease both;
        }

        .laporan-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-blue);
            border-color: #bfdbfe;
        }

        .card-img {
            position: relative;
            height: 190px; overflow: hidden;
        }

        .card-img img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .laporan-card:hover .card-img img { transform: scale(1.05); }

        .province-badge {
            position: absolute; top: 12px; left: 12px;
            background: rgba(13,27,62,0.82);
            backdrop-filter: blur(6px);
            color: white;
            padding: 4px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
            border: 1px solid rgba(255,255,255,0.15);
        }

        .status-badge {
            position: absolute; top: 12px; right: 12px;
            padding: 4px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
            display: flex; align-items: center; gap: 4px;
            backdrop-filter: blur(6px);
        }

        .pop-bar {
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 3px; background: rgba(255,255,255,0.2);
        }

        .pop-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--blue), #60a5fa);
        }
        .card-body { padding: 18px 20px; flex: 1; display: flex; flex-direction: column; }

        .card-body h3 {
            font-family: var(--font-display);
            font-size: 16px; font-weight: 700;
            color: var(--navy); margin-bottom: 7px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .card-body .desc {
            font-size: 13px; color: var(--gray-500);
            line-height: 1.6; margin-bottom: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }
        .card-stats {
            display: flex; gap: 14px;
            padding: 12px 0;
            border-top: 1px solid var(--gray-100);
            border-bottom: 1px solid var(--gray-100);
            margin-bottom: 12px;
        }

        .cstat {
            display: flex; align-items: center; gap: 5px;
            font-size: 12px; color: var(--gray-500); font-weight: 600;
        }

        .cstat i { font-size: 12px; color: var(--blue-bright); }

        .card-meta {
            display: flex; justify-content: space-between;
            align-items: center; font-size: 12px; color: var(--gray-400);
        }

        .card-meta .user { display: flex; align-items: center; gap: 5px; font-weight: 600; color: var(--gray-600); }
        .card-meta .user i { color: var(--blue-bright); font-size: 11px; }
        .card-meta .date { display: flex; align-items: center; gap: 5px; }
        .card-meta .date i { color: var(--gray-300); font-size: 11px; }

        .card-link {
            display: none;
            margin-top: 12px;
            background: var(--blue);
            color: white; border: none;
            padding: 9px 0; border-radius: 9px;
            font-size: 13px; font-weight: 600;
            font-family: var(--font-body);
            cursor: pointer; text-align: center;
            text-decoration: none;
            transition: background 0.2s;
        }

        .laporan-card:hover .card-link { display: block; }
        .card-link:hover { background: #1d4ed8; }

        <?php foreach ($laporan as $i => $_): ?>
        .laporan-card:nth-child(<?= $i+1 ?>) { animation-delay: <?= $i * 0.06 ?>s; }
        <?php endforeach; ?>

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /*  FOOTER  */
        .main-footer {
            background: linear-gradient(160deg, #080e18 0%, #0d1b3e 60%, #1a2a5e 100%);
            color: white; padding: 60px 40px 30px;
        }

        .footer-inner { max-width: 1160px; margin: 0 auto; }

        .footer-top-row {
            display: flex; align-items: center; gap: 12px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 40px;
        }

        .footer-top-row img { height: 44px; border-radius: 8px; }

        .footer-top-row .brand {
            font-family: var(--font-display);
            font-size: 20px; font-weight: 700; color: white;
        }

        .footer-top-row .brand em { color: var(--gold); font-style: normal; }

        .footer-cols {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px; margin-bottom: 40px;
        }

        .footer-col p { color: rgba(255,255,255,0.5); font-size: 14px; margin-bottom: 6px; line-height: 1.7; }
        .footer-col h4 { font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.35); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 14px; }
        .footer-col a { display: block; color: rgba(255,255,255,0.6); text-decoration: none; font-size: 14px; margin-bottom: 8px; transition: color 0.2s; }
        .footer-col a:hover { color: white; }

        .footer-social { display: flex; gap: 10px; margin-top: 16px; }

        .footer-social a {
            width: 38px; height: 38px; border-radius: 10px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 16px; text-decoration: none;
            transition: all 0.2s;
        }

        .footer-social a:hover { background: var(--blue); border-color: var(--blue); transform: translateY(-3px); }

        .footer-bottom-row {
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 24px;
            display: flex; justify-content: space-between; align-items: center;
        }

        .footer-bottom-row p { color: rgba(255,255,255,0.35); font-size: 13px; }

        /*  RESPONSIVE  */
        @media (max-width: 700px) {
            .navbar { padding: 0 16px; }
            .navlinks { display: none; }
            .page-hero { padding: 36px 18px 70px; }
            .hero-title { font-size: 25px; }
            .content-area { padding: 0 14px 50px; }
            .laporan-grid { grid-template-columns: 1fr; }
            .toolbar { flex-direction: column; align-items: stretch; }
            .footer-cols { grid-template-columns: 1fr; gap: 24px; }
            .footer-bottom-row { flex-direction: column; gap: 8px; text-align: center; }
        }

        /* No results */
        .no-results {
            grid-column: 1/-1;
            text-align: center;
            padding: 60px 20px;
            color: var(--gray-400);
        }

        .no-results i { font-size: 40px; margin-bottom: 14px; display: block; }
        .no-results p { font-size: 15px; }
    </style>
</head>
<body>

<header class="navbar">
    <div class="logo">
      <img src="./ASSETS/LOGO.png" class="logo-img">
    </div>

    <nav>
      <a href="#" class="active">Beranda</a>
      <a href="laporan_saya.php">Laporan</a>
      <a href="PERINGKAT.php">Peringkat</a>
      <a href="./Users/TENTANG.php">Tentang</a>
    </nav>

    <label for="toggleProfile" class="user">
      <span class="username"><?php echo htmlspecialchars($nama); ?></span>
      <img src="./ASSETS/USER.png" class="user-img" />
    </label>
  </header>
<div class="page-wrapper">

    <!-- Hero -->
    <div class="page-hero">
        <div class="hero-label"><i class="fa-solid fa-fire"></i> Trending Sekarang</div>
        <h1 class="hero-title">Laporan Terpopuler</h1>
        <p class="hero-sub">Laporan masyarakat yang paling banyak mendapat perhatian se-Indonesia</p>
        <div class="hero-stats">
            <div class="stat-pill">
                <span class="val"><?= count($laporan) ?></span>
                <span class="lbl">Laporan</span>
            </div>
            <div class="stat-pill">
                <span class="val"><?= number_format(array_sum(array_column($laporan, 'views'))) ?></span>
                <span class="lbl">Total Dilihat</span>
            </div>
            <div class="stat-pill">
                <span class="val"><?= count(array_filter($laporan, fn($l) => $l['status'] === 'selesai')) ?></span>
                <span class="lbl">Diselesaikan</span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="content-area">

        <!-- Toolbar -->
        <div class="toolbar">
            <a href="./Users/BERANDA2.php" style="display:flex;align-items:center;gap:7px;color:var(--gray-600);text-decoration:none;font-size:13px;font-weight:600;padding:8px 14px;border-radius:9px;border:1.5px solid var(--gray-200);background:var(--gray-50);white-space:nowrap;transition:all 0.2s;" onmouseover="this.style.borderColor='#bfdbfe';this.style.color='var(--blue)'" onmouseout="this.style.borderColor='var(--gray-200)';this.style.color='var(--gray-600)'">
                <i class="fa-solid fa-chevron-left"></i> Kembali
            </a>

            <div class="search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="Cari laporan..." oninput="filterCards()">
            </div>

            <div class="filter-wrap">
                <i class="fa-solid fa-map-marker-alt"></i>
                <select id="provinceFilter" onchange="filterCards()">
                    <option value="">Semua Provinsi</option>
                    <?php foreach ($provinces as $key => $label): ?>
                    <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="view-toggle">
                <button class="view-btn active" id="gridBtn" onclick="setView('grid')" title="Grid">
                    <i class="fa-solid fa-grip"></i>
                </button>
                <button class="view-btn" id="listBtn" onclick="setView('list')" title="List">
                    <i class="fa-solid fa-list"></i>
                </button>
            </div>
        </div>

        <!-- Section head -->
        <div class="section-head">
            <h2>
                <i class="fa-solid fa-fire" style="color:#ef4444"></i>
                Laporan Viral
                <span class="count-badge" id="countBadge"><?= count($laporan) ?></span>
            </h2>
            <a href="#">Lihat semua &rarr;</a>
        </div>

        <!-- Grid -->
        <div class="laporan-grid" id="laporanGrid">
            <?php
            $max_views = max(array_column($laporan, 'views'));

if ($max_views <= 0) {
    $max_views = 1;
}
            foreach ($laporan as $i => $item):
                $st = $status_config[$item['status']] ?? $status_config['pending'];
                $pop_pct = round(($item['views'] / $max_views) * 100);
                $tanggal = date('d M Y', strtotime($item['date']));
            ?>
            <a class="laporan-card"
               href="DETAIL_LAPORAN.php?id=<?= $item['id'] ?>"
               data-title="<?= htmlspecialchars(strtolower($item['title'])) ?>"
               data-province="<?= htmlspecialchars($item['province_key']) ?>">

                <div class="card-img">
                    <img src="<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                    <span class="province-badge"><?= htmlspecialchars($item['province']) ?></span>
                    <span class="status-badge"
                          style="background:<?= $st['bg'] ?>;color:<?= $st['color'] ?>;border:1px solid <?= $st['color'] ?>33">
                        <i class="fa-solid <?= $st['icon'] ?>"></i> <?= $st['label'] ?>
                    </span>
                    <div class="pop-bar">
                        <div class="pop-bar-fill" style="width:<?= $pop_pct ?>%"></div>
                    </div>
                </div>

                <div class="card-body">
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p class="desc"><?= htmlspecialchars($item['desc']) ?></p>

                    <div class="card-stats">
                        <div class="cstat">
                            <i class="fa-solid fa-eye"></i>
                            <?= number_format($item['views']) ?>
                        </div>
                        <div class="cstat">
                            <i class="fa-solid fa-heart"></i>
                            <?= number_format($item['likes']) ?>
                        </div>
                    </div>

                    <div class="card-meta">
                        <span class="user"><i class="fa-solid fa-circle-user"></i><?= htmlspecialchars($item['user']) ?></span>
                        <span class="date"><i class="fa-regular fa-calendar"></i><?= $tanggal ?></span>
                    </div>

                    <span class="card-link">Lihat Detail <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
            <?php endforeach; ?>

            <div class="no-results" id="noResults" style="display:none">
                <i class="fa-solid fa-search"></i>
                <p>Tidak ada laporan yang cocok.</p>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-inner">
            <div class="footer-top-row">
                <img src="ASSETS/LOGO.png" alt="AksiKita">
                <span class="brand">Aksi<em>Kita</em></span>
            </div>
            <div class="footer-cols">
                <div class="footer-col">
                    <p>Platform pelaporan dan pengawasan kinerja pemerintah daerah berbasis masyarakat.</p>
                    <p>Jl. Bachireng No. 12, Indonesia</p>
                    <p>0821 6888 9060 &bull; info@aksikita.id</p>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Layanan</h4>
                    <a href="#">Unit Layanan Terpadu</a>
                    <a href="#">Cara Kerja</a>
                    <a href="#">FAQ</a>
                    <a href="#">Aturan Penggunaan</a>
                </div>
                <div class="footer-col">
                    <h4>Navigasi</h4>
                    <a href="#">Lapor</a>
                    <a href="#">Survei</a>
                    <a href="#">Peta Situs</a>
                    <a href="#">Arsip</a>
                </div>
            </div>
            <div class="footer-bottom-row">
                <p>© 2025 AksiKita. Semua Hak Dilindungi.</p>
                <p>Dibangun dengan ❤️ untuk Indonesia</p>
            </div>
        </div>
    </footer>

</div>

<script>
    function filterCards() {
        const q = document.getElementById('searchInput').value.toLowerCase().trim();
        const prov = document.getElementById('provinceFilter').value;
        const cards = document.querySelectorAll('#laporanGrid .laporan-card');
        let visible = 0;

        cards.forEach(card => {
            const title = card.dataset.title || '';
            const province = card.dataset.province || '';
            const matchQ = !q || title.includes(q);
            const matchP = !prov || province === prov;
            const show = matchQ && matchP;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        document.getElementById('countBadge').textContent = visible;
        document.getElementById('noResults').style.display = visible === 0 ? 'block' : 'none';
    }

    function setView(mode) {
        const grid = document.getElementById('laporanGrid');
        const gBtn = document.getElementById('gridBtn');
        const lBtn = document.getElementById('listBtn');

        if (mode === 'list') {
            grid.style.gridTemplateColumns = '1fr';
            lBtn.classList.add('active');
            gBtn.classList.remove('active');
        } else {
            grid.style.gridTemplateColumns = '';
            gBtn.classList.add('active');
            lBtn.classList.remove('active');
        }
    }


    document.addEventListener('click', e => {
        const m = document.getElementById('userMenu');
        if (m && !m.contains(e.target)) m.classList.remove('open');
    });
</script>

</body>
</html>
