<?php
$provinces_low = [
    [
        "rank" => 1,
        "name" => "Gorontalo",
        "governor" => "Rusli Habibie",
        "deputy" => "Idris Rahim",
        "logo" => "ASSETS/gorontalo.png",
        "stars" => 1,
        "response_time" => "48.2 jam",
        "total_reports" => 820,
        "resolved" => 310,
        "worst" => true,
    ],
    [
        "rank" => 2,
        "name" => "Maluku Utara",
        "governor" => "Abdul Gani Kasuba",
        "deputy" => "M. Al Yasin Ali",
        "logo" => "ASSETS/download.jpg",
        "stars" => 2,
        "response_time" => "36.5 jam",
        "total_reports" => 1020,
        "resolved" => 520,
        "worst" => false,
    ],
    [
        "rank" => 3,
        "name" => "Papua Barat",
        "governor" => "Dominggus Mandacan",
        "deputy" => "Mohamad Lakotani",
        "logo" => "ASSETS/papua.png",
        "stars" => 2,
        "response_time" => "30.1 jam",
        "total_reports" => 760,
        "resolved" => 410,
        "worst" => false,
    ],
    [
        "rank" => 4,
        "name" => "Banten",
        "governor" => "Wahidin Halim",
        "deputy" => "Andika Hazrumy",
        "logo" => "ASSETS/banten.png",
        "stars" => 3,
        "response_time" => "24.7 jam",
        "total_reports" => 1850,
        "resolved" => 1100,
        "worst" => false,
    ],
    [
        "rank" => 5,
        "name" => "Riau",
        "governor" => "Syamsuar",
        "deputy" => "Edy Nasution",
        "logo" => "ASSETS/riau.png",
        "stars" => 3,
        "response_time" => "22.3 jam",
        "total_reports" => 1430,
        "resolved" => 900,
        "worst" => false,
    ],
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
    <title>Peringkat Terendah — AksiKita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --navy: #0d1b3e;
            --navy-mid: #162952;
            --navy-light: #1e3d8f;
            --blue: #2563eb;
            --blue-bright: #3b82f6;
            --red: #ea580c;
            --red-mid: #c2410c;
            --red-light: #fb923c;
            --red-pale: #fff7ed;
            --red-border: #fed7aa;
            --orange: #ea580c;
            --gold: #f59e0b;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-400: #94a3b8;
            --gray-600: #475569;
            --gray-800: #1e293b;
            --font-display: 'Sora', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.15);
            --shadow-red: 0 8px 30px rgba(234,88,12,0.2);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-body);
            background: var(--gray-50);
            color: var(--gray-800);
            min-height: 100vh;
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

@media (max-width: 768px) {

  .navbar {
    flex-direction: column;
    gap: 15px;
    padding: 20px;
  }


  .hero-text h1 {
    font-size: 32px;
  }

  .about-grid {
    grid-template-columns: 1fr;
  }

  .showcase-wrapper {
    flex-direction: column;
  }

  .zigzag-item,
  .zigzag-item.reverse {
    flex-direction: column;
    text-align: center;
  }

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
      transition: 0.3s;
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
      display: inline-block;
      background: transparent;
      border: none;
      color: white;
      font-size: 16px;
      padding: 0;
      cursor: pointer;
    }

   

        .page-wrapper { padding-top: 70px; min-height: 100vh; }

        .page-hero {
            background: linear-gradient(135deg, #1a0a0a 0%, #2d0f0f 40%, #3b1414 100%);
            padding: 50px 40px 70px;
            position: relative;
            overflow: hidden;
        }

        .page-hero::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(234,88,12,0.25) 0%, transparent 70%);
            border-radius: 50%;
        }

        .page-hero::after {
            content: '';
            position: absolute;
            bottom: -60px; left: 25%;
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(234,88,12,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-deco {
            position: absolute;
            bottom: 20px; right: 40px;
            opacity: 0.08;
            font-size: 80px;
            color: white;
        }

        .hero-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(234,88,12,0.2);
            border: 1px solid rgba(234,88,12,0.4);
            color: #fb923c;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: 36px;
            font-weight: 700;
            color: white;
            line-height: 1.2;
            margin-bottom: 10px;
        }

        .hero-sub {
            color: rgba(255,255,255,0.5);
            font-size: 15px;
            max-width: 480px;
        }

        .hero-stats {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }

        .stat-pill {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 12px 20px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .stat-pill .val {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 700;
            color: white;
        }

        .stat-pill .lbl {
            font-size: 11px;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-area {
            max-width: 860px;
            margin: -30px auto 0;
            padding: 0 20px 60px;
            position: relative;
            z-index: 10;
        }

        /* ===== TOOLBAR ===== */
        .toolbar {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-wrap {
            position: relative;
            flex: 1;
            min-width: 180px;
        }

        .search-wrap i {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 14px;
        }

        .search-wrap input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border: 1.5px solid var(--gray-200);
            border-radius: 10px;
            font-size: 14px;
            font-family: var(--font-body);
            color: var(--gray-800);
            outline: none;
            transition: border 0.2s;
            background: var(--gray-50);
        }

        .search-wrap input:focus { border-color: var(--red); background: white; }

        .filter-tabs { display: flex; gap: 6px; }

        .filter-tabs a, .filter-tabs button {
            padding: 9px 18px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            font-family: var(--font-body);
            cursor: pointer;
            text-decoration: none;
            border: 1.5px solid transparent;
            transition: all 0.2s;
        }

        .tab-idle {
            background: var(--gray-100);
            color: var(--gray-600);
            border-color: var(--gray-200) !important;
        }

        .tab-idle:hover { background: var(--gray-200); color: var(--gray-800); }

        .tab-active-red {
            background: var(--red);
            color: white !important;
            border-color: var(--red) !important;
            box-shadow: 0 4px 12px rgba(220,38,38,0.3);
        }

        .info-banner {
            background: linear-gradient(90deg, #fff7ed, #fff8f1);
            border: 1px solid #fed7aa;
            border-radius: 12px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            font-size: 13.5px;
            color: #9a3412;
        }

        .info-banner i { font-size: 16px; color: var(--red); flex-shrink: 0; }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .section-head h2 {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 700;
            color: var(--navy);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-head .badge-count {
            background: var(--red);
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .section-head a { color: var(--red); font-size: 13px; font-weight: 600; text-decoration: none; }

        .cards-list { display: flex; flex-direction: column; gap: 14px; }

        .province-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 18px;
            box-shadow: var(--shadow-sm);
            border: 1.5px solid var(--gray-200);
            transition: all 0.25s;
            position: relative;
            overflow: hidden;
        }

        .province-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: var(--gray-200);
            transition: background 0.2s;
        }

        .province-card:hover {
            border-color: #fecaca;
            box-shadow: var(--shadow-red);
            transform: translateY(-2px);
        }

        .province-card:hover::before { background: var(--red); }

        .province-card.worst {
            background: linear-gradient(135deg, #fff1f2 0%, #fff5f5 100%);
            border-color: #fecaca;
        }

        .province-card.worst::before { background: var(--red); }

        .province-card.worst:hover { box-shadow: var(--shadow-red); }

        .rank-badge {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display);
            font-weight: 800;
            flex-shrink: 0;
        }

        .rank-worst { background: linear-gradient(135deg, #fb923c, #ea580c); color: white; font-size: 20px; }
        .rank-2nd   { background: linear-gradient(135deg, #f97316, #ea580c); color: white; font-size: 20px; }
        .rank-3rd   { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; font-size: 20px; }
        .rank-other { background: var(--gray-100); color: var(--gray-400); font-size: 15px; font-weight: 700; }

        .province-logo {
            width: 54px; height: 54px;
            object-fit: contain;
            border-radius: 10px;
            flex-shrink: 0;
            background: var(--gray-50);
            padding: 4px;
            border: 1px solid var(--gray-200);
        }

        .card-info { flex: 1; min-width: 0; }

        .card-info h3 {
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-info .officials { font-size: 12px; color: var(--gray-600); line-height: 1.6; }
        .card-info .officials span { font-weight: 600; color: var(--gray-800); }

        .card-stats { display: flex; gap: 14px; margin-top: 10px; }

        .stat-item { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--gray-600); }
        .stat-item i { font-size: 11px; color: var(--red-light); }
        .stat-item .stat-val { font-weight: 700; color: var(--gray-800); }

        
        .resolve-bar-wrap { margin-top: 8px; }

        .resolve-bar-label {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: var(--gray-400);
            margin-bottom: 3px;
        }

        .resolve-bar {
            height: 4px;
            background: var(--gray-200);
            border-radius: 4px;
            overflow: hidden;
        }

        .resolve-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--red), #fb923c);
            border-radius: 4px;
            transition: width 0.6s ease;
        }

        .card-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }

        .stars-row { display: flex; gap: 2px; }
        .stars-row i { font-size: 13px; color: var(--gray-200); }
        .stars-row i.filled { color: #f59e0b; }

        .response-chip {
            background: #fff1f2;
            color: var(--red);
            border: 1px solid #fecaca;
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .btn-detail {
            background: var(--red);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            font-family: var(--font-body);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            transition: background 0.2s, transform 0.1s;
        }

        .btn-detail:hover { background: var(--red-mid); transform: scale(1.03); }

        .worst-badge {
            position: absolute;
            top: 14px; right: 14px;
            background: rgba(234,88,12,0.1);
            border: 1px solid rgba(234,88,12,0.3);
            color: var(--red);
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 4px;
            letter-spacing: 0.3px;
        }

        /*  FOOTER  */
        .main-footer {
            background: linear-gradient(160deg, #080e18 0%, #0d1b3e 60%, #1a2a5e 100%);
            color: white;
            padding: 60px 40px 30px;
        }

        .footer-inner { max-width: 1100px; margin: 0 auto; }

        .footer-top-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 40px;
        }

        .footer-top-row img { height: 44px; border-radius: 8px; }

        .footer-top-row .brand {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
            color: white;
        }

        .footer-top-row .brand em { color: var(--gold); font-style: normal; }

        .footer-cols {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-col p { color: rgba(255,255,255,0.5); font-size: 14px; margin-bottom: 6px; line-height: 1.6; }
        .footer-col h4 { font-size: 13px; font-weight: 700; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 14px; }
        .footer-col a { display: block; color: rgba(255,255,255,0.6); text-decoration: none; font-size: 14px; margin-bottom: 8px; transition: color 0.2s; }
        .footer-col a:hover { color: white; }

        .footer-social { display: flex; gap: 10px; margin-top: 16px; }

        .footer-social a {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 16px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .footer-social a:hover { background: var(--red); border-color: var(--red); transform: translateY(-3px); }

        .footer-bottom-row {
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-bottom-row p { color: rgba(255,255,255,0.35); font-size: 13px; }

        /*  RESPONSIVE  */
        @media (max-width: 640px) {
            .navbar { padding: 0 16px; }
            .navlinks { display: none; }
            .page-hero { padding: 40px 20px 60px; }
            .hero-title { font-size: 26px; }
            .hero-stats { flex-wrap: wrap; gap: 10px; }
            .content-area { padding: 0 12px 40px; }
            .card-stats { display: none; }
            .province-logo { width: 42px; height: 42px; }
            .footer-cols { grid-template-columns: 1fr; gap: 24px; }
            .footer-bottom-row { flex-direction: column; gap: 8px; text-align: center; }
        }

        /*  ANIMASI  */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .province-card { animation: fadeUp 0.4s ease both; }

        <?php foreach ($provinces_low as $i => $p): ?>
        .province-card:nth-child(<?= $i + 1 ?>) { animation-delay: <?= $i * 0.07 ?>s; }
        <?php endforeach; ?>
    </style>
</head>
<body>

<!-- TOGGLE -->
<input type="checkbox" id="toggleSetting">
<input type="checkbox" id="toggleProfile">
<input type="checkbox" id="toggleLogout">

<!-- NAVBAR -->
<header class="navbar">
    <div class="logo">
        <img src="ASSETS/LOGO.png" class="logo-img" alt="AksiKita">
    </div>

    <nav>
        <a href="./Users/BERANDA2.php">Beranda</a>
        <a href="LAPORAN_SAYA.php">Laporan</a>
        <a href="PERINGKAT.php" class="active">Peringkat</a>
        <a href="./Users/TENTANG.php">Tentang</a>
    </nav>

    <label for="toggleProfile" class="user">
        <span class="username">
            <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Pengguna'); ?>
        </span>
        <img src="ASSETS/USER.png" class="user-img" alt="User">
    </label>
</header>

    </div>
</div>

    


<!--  PAGE WRAPPER  -->
<div class="page-wrapper">

    <div class="page-hero">
        <div class="hero-deco"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="hero-label">
            <i class="fa-solid fa-triangle-exclamation"></i> Perlu Perhatian 2026
            
        </div>
        <h1 class="hero-title">Peringkat Pemerintah<br>Terendah Indonesia</h1>
        <p class="hero-sub">Provinsi dengan waktu respon laporan masyarakat paling lambat se-nusantara</p>

        <div class="hero-stats">
            <div class="stat-pill">
                <span class="val">5</span>
                <span class="lbl">Provinsi</span>
            </div>
            <div class="stat-pill">
                <span class="val">5.9K</span>
                <span class="lbl">Laporan Masuk</span>
            </div>
            <div class="stat-pill">
                <span class="val">52%</span>
                <span class="lbl">Terselesaikan</span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="content-area">

        <!-- Toolbar -->
        <div class="toolbar">
            <div class="search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="Cari provinsi..." oninput="filterCards()">
            </div>
            <div class="filter-tabs">
                <a href="PERINGKAT.php" class="tab-idle">
                    <i class="fa-solid fa-arrow-up"></i> Tertinggi
                </a>
                <a href="TERENDAH.php" class="tab-active-red">
                    <i class="fa-solid fa-arrow-down"></i> Terendah
                </a>
            </div>
        </div>

        <!-- Info -->
        <div class="info-banner">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>Provinsi-provinsi berikut memiliki rata-rata waktu respon laporan paling lambat. Data diperbarui secara berkala.</span>
        </div>

        <!-- Section -->
        <div class="section-head">
            <h2>
                <i class="fa-solid fa-circle-exclamation" style="color: var(--red)"></i>
                Top 5 Provinsi Terlambat
                <span class="badge-count"><?= count($provinces_low) ?></span>
            </h2>
            <a href="#">Lihat semua &rarr;</a>
        </div>

        <!-- Cards -->
        <div class="cards-list" id="cardsList">
            <?php foreach ($provinces_low as $i => $prov):
                $rank = $prov['rank'];
                $isWorst = $prov['worst'];
                $resolveRate = round(($prov['resolved'] / $prov['total_reports']) * 100);
                $rankIcons = ['💀','🔴','🟠','🟡','🟡'];
                $rankLabel = $rankIcons[$i] ?? $rank;
                $rankClass = $rank === 1 ? 'rank-worst' : ($rank === 2 ? 'rank-2nd' : ($rank === 3 ? 'rank-3rd' : 'rank-other'));
                if ($rank >= 4) $rankLabel = $rank;
            ?>
            <div class="province-card <?= $isWorst ? 'worst' : '' ?>"
                 data-name="<?= htmlspecialchars(strtolower($prov['name'])) ?>">

                <?php if ($isWorst): ?>
                <div class="worst-badge">
                    <i class="fa-solid fa-triangle-exclamation"></i> Perlu Perhatian
                </div>
                <?php endif; ?>

                <!-- Rank -->
                <div class="rank-badge <?= $rankClass ?>">
                    <?= $rankLabel ?>
                </div>

                <!-- Logo -->
                <img class="province-logo"
                     src="<?= htmlspecialchars($prov['logo']) ?>"
                     alt="Logo <?= htmlspecialchars($prov['name']) ?>">

                <!-- Info -->
                <div class="card-info">
                    <h3><?= htmlspecialchars($prov['name']) ?></h3>
                    <div class="officials">
                        <span>Gubernur:</span> <?= htmlspecialchars($prov['governor']) ?><br>
                        <span>Wakil:</span> <?= htmlspecialchars($prov['deputy']) ?>
                    </div>
                    <div class="card-stats">
                        <div class="stat-item">
                            <i class="fa-solid fa-file-lines"></i>
                            <span class="stat-val"><?= number_format($prov['total_reports']) ?></span> laporan
                        </div>
                        <div class="stat-item">
                            <i class="fa-solid fa-clock"></i>
                            <span class="stat-val"><?= $resolveRate ?>%</span> selesai
                        </div>
                    </div>
                    <div class="resolve-bar-wrap">
                        <div class="resolve-bar-label">
                            <span>Tingkat penyelesaian</span>
                            <span><?= $resolveRate ?>%</span>
                        </div>
                        <div class="resolve-bar">
                            <div class="resolve-bar-fill" style="width: <?= $resolveRate ?>%"></div>
                        </div>
                    </div>
                </div>

                <!-- Right -->
                <div class="card-right">
                    <div class="response-chip">
                        <i class="fa-solid fa-clock"></i>
                        <?= htmlspecialchars($prov['response_time']) ?>
                    </div>
                    <div class="stars-row">
                        <?php for ($s = 1; $s <= 5; $s++): ?>
                        <i class="fa-solid fa-star <?= $s <= $prov['stars'] ? 'filled' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <a href="DETAIL_PROVINSI.php?id=<?= $rank ?>" class="btn-detail">
                        Detail <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!--  FOOTER  -->
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
        const q = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('#cardsList .province-card').forEach(card => {
            const name = card.dataset.name || '';
            card.style.display = name.includes(q) ? '' : 'none';
        });
    }

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('userMenu');
        if (menu && !menu.contains(e.target)) menu.classList.remove('open');
    });
</script>

</body>
</html>
