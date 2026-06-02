<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: MASUK.php");
    exit();
}

$nama = $_SESSION['nama_lengkap'];



$provinces = [
    [
        "rank" => 1,
        "name" => "Jawa Barat",
        "governor" => "Dedi Mulyadi",
        "deputy" => "Dr. Drs. Herman Suryatman",
        "logo" => "ASSETS/jabar.png",
        "stars" => 5,
        "response_time" => "1.2 jam",
        "total_reports" => 4821,
        "resolved" => 4750,
        "highlight" => true,
    ],
    [
        "rank" => 2,
        "name" => "Bali",
        "governor" => "Wayan Koster",
        "deputy" => "I Nyoman Giri Prasta",
        "logo" => "ASSETS/bali.png",
        "stars" => 4,
        "response_time" => "2.4 jam",
        "total_reports" => 3102,
        "resolved" => 2980,
        "highlight" => false,
    ],
    [
        "rank" => 3,
        "name" => "Kalimantan Timur",
        "governor" => "Rudy Mas'ud",
        "deputy" => "Seno Aji",
        "logo" => "ASSETS/kaltim.png",
        "stars" => 4,
        "response_time" => "3.1 jam",
        "total_reports" => 2750,
        "resolved" => 2600,
        "highlight" => false,
    ],
    [
        "rank" => 4,
        "name" => "D.I. Yogyakarta",
        "governor" => "Sri Sultan HB X",
        "deputy" => "KGPAA Paku Alam X",
        "logo" => "ASSETS/diy.png",
        "stars" => 4,
        "response_time" => "3.5 jam",
        "total_reports" => 2100,
        "resolved" => 1980,
        "highlight" => false,
    ],
    [
        "rank" => 5,
        "name" => "Jawa Timur",
        "governor" => "Khofifah Indar Parawansa",
        "deputy" => "Emil Elestianto Dardak",
        "logo" => "ASSETS/jatim.jpg",
        "stars" => 4,
        "response_time" => "3.9 jam",
        "total_reports" => 5200,
        "resolved" => 4800,
        "highlight" => false,
    ],
];

$rank_badges = ["🥇", "🥈", "🥉", "4", "5"];
$rank_colors = ["#FFD700", "#C0C0C0", "#CD7F32", "#6b7aad", "#6b7aad"];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringkat Pemerintah — AksiKita</title>
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
            --gold: #f59e0b;
            --gold-light: #fde68a;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-400: #94a3b8;
            --gray-600: #475569;
            --gray-800: #1e293b;
            --success: #10b981;
            --font-display: 'Sora', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.15);
            --shadow-blue: 0 8px 30px rgba(37,99,235,0.25);
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

        .page-wrapper {
            padding-top: 70px;
            min-height: 100vh;
        }

        .page-hero {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 60%, #1a3a6e 100%);
            padding: 50px 40px 70px;
            position: relative;
            overflow: hidden;
        }

        .page-hero::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(59,130,246,0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .page-hero::after {
            content: '';
            position: absolute;
            bottom: -80px; left: 30%;
            width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(245,158,11,0.12) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(245,158,11,0.15);
            border: 1px solid rgba(245,158,11,0.3);
            color: var(--gold);
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
            color: rgba(255,255,255,0.6);
            font-size: 15px;
            max-width: 480px;
        }

   
        .hero-stats {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }

        .stat-pill {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
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
            color: rgba(255,255,255,0.5);
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
            left: 14px;
            top: 50%;
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

        .search-wrap input:focus {
            border-color: var(--blue);
            background: white;
        }

        .filter-tabs {
            display: flex;
            gap: 6px;
        }

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

        .filter-tabs .tab-active {
            background: var(--navy);
            color: white;
            border-color: var(--navy);
            box-shadow: 0 4px 12px rgba(13,27,62,0.3);
        }

        .filter-tabs .tab-idle {
            background: var(--gray-100);
            color: var(--gray-600);
            border-color: var(--gray-200);
        }

        .filter-tabs .tab-idle:hover {
            background: var(--gray-200);
            color: var(--gray-800);
        }

    
        .info-banner {
            background: linear-gradient(90deg, #eff6ff, #f0f9ff);
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            font-size: 13.5px;
            color: #1e40af;
        }

        .info-banner i {
            font-size: 16px;
            color: var(--blue);
            flex-shrink: 0;
        }

        /* ===== SECTION TITLE ===== */
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
            background: var(--blue);
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .section-head a {
            color: var(--blue);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }


        .cards-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

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
            text-decoration: none;
            color: inherit;
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
            border-color: #bfdbfe;
            box-shadow: var(--shadow-blue);
            transform: translateY(-2px);
        }

        .province-card:hover::before { background: var(--blue); }

        .province-card.top1 {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border-color: #fde68a;
        }

        .province-card.top1::before { background: var(--gold); }

        .province-card.top1:hover {
            box-shadow: 0 8px 30px rgba(245,158,11,0.25);
        }

  
        .rank-badge {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-family: var(--font-display);
            font-weight: 800;
            flex-shrink: 0;
        }

        .rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; font-size: 22px; }
        .rank-2 { background: linear-gradient(135deg, #d1d5db, #9ca3af); color: white; font-size: 22px; }
        .rank-3 { background: linear-gradient(135deg, #d97706, #b45309); color: white; font-size: 22px; }
        .rank-other { background: var(--gray-100); color: var(--gray-400); font-size: 15px; font-weight: 700; }

  
        .province-logo {
            width: 54px;
            height: 54px;
            object-fit: contain;
            border-radius: 10px;
            flex-shrink: 0;
            background: var(--gray-50);
            padding: 4px;
            border: 1px solid var(--gray-200);
        }

     
        .card-info {
            flex: 1;
            min-width: 0;
        }

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

        .card-info .officials {
            font-size: 12px;
            color: var(--gray-600);
            line-height: 1.6;
        }

        .card-info .officials span {
            font-weight: 600;
            color: var(--gray-800);
        }

 
        .card-stats {
            display: flex;
            gap: 14px;
            margin-top: 10px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            color: var(--gray-600);
        }

        .stat-item i {
            font-size: 11px;
            color: var(--blue-bright);
        }

        .stat-item .stat-val {
            font-weight: 700;
            color: var(--gray-800);
        }

       
        .card-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }

        .stars-row {
            display: flex;
            gap: 2px;
        }

        .stars-row i {
            font-size: 13px;
            color: var(--gray-200);
        }

        .stars-row i.filled { color: #f59e0b; }

        .response-chip {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .province-card.top1 .response-chip {
            background: #fffbeb;
            color: #d97706;
            border-color: #fde68a;
        }

        .btn-detail {
            background: var(--blue);
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

        .btn-detail:hover {
            background: #1d4ed8;
            transform: scale(1.03);
        }

        .province-card.top1 .btn-detail {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 4px 12px rgba(245,158,11,0.3);
        }

        .province-card.top1 .btn-detail:hover {
            background: linear-gradient(135deg, #d97706, #b45309);
        }

    
        .crown-badge {
            position: absolute;
            top: 14px;
            right: 14px;
            background: rgba(245,158,11,0.15);
            border: 1px solid rgba(245,158,11,0.4);
            color: #d97706;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 4px;
            letter-spacing: 0.3px;
        }

        /* Progress bar */
        .resolve-bar-wrap {
            margin-top: 8px;
        }

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
            background: linear-gradient(90deg, var(--blue), var(--blue-bright));
            border-radius: 4px;
            transition: width 0.6s ease;
        }

        .top1 .resolve-bar-fill {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }

    
        .main-footer {
            background: linear-gradient(160deg, #080e18 0%, #0d1b3e 60%, #1a2a5e 100%);
            color: white;
            padding: 60px 40px 30px;
        }

        .footer-inner {
            max-width: 1100px;
            margin: 0 auto;
        }

        .footer-top-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 40px;
        }

        .footer-top-row img {
            height: 44px;
            border-radius: 8px;
        }

        .footer-top-row .brand {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
            color: white;
        }

        .footer-top-row .brand em {
            color: var(--gold);
            font-style: normal;
        }

        .footer-cols {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-col p {
            color: rgba(255,255,255,0.5);
            font-size: 14px;
            margin-bottom: 6px;
            line-height: 1.6;
        }

        .footer-col h4 {
            font-size: 13px;
            font-weight: 700;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 14px;
        }

        .footer-col a {
            display: block;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 8px;
            transition: color 0.2s;
        }

        .footer-col a:hover { color: white; }

        .footer-social {
            display: flex;
            gap: 10px;
            margin-top: 16px;
        }

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

        .footer-social a:hover {
            background: var(--blue);
            border-color: var(--blue);
            transform: translateY(-3px);
        }

        .footer-bottom-row {
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-bottom-row p {
            color: rgba(255,255,255,0.35);
            font-size: 13px;
        }

        /*  RESPONSIVE  */
        @media (max-width: 640px) {
            .navbar { padding: 0 16px; }
            .navlinks { display: none; }
            .page-hero { padding: 40px 20px 60px; }
            .hero-title { font-size: 26px; }
            .hero-stats { flex-wrap: wrap; gap: 10px; }
            .content-area { padding: 0 12px 40px; }
            .toolbar { gap: 10px; }
            .province-card { gap: 12px; padding: 16px; }
            .card-stats { display: none; }
            .province-logo { width: 42px; height: 42px; }
            .footer-cols { grid-template-columns: 1fr; gap: 24px; }
            .footer-bottom-row { flex-direction: column; gap: 8px; text-align: center; }
        }

        /*  ANIMATISI */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .province-card {
            animation: fadeUp 0.4s ease both;
        }

        <?php foreach ($provinces as $i => $p): ?>
        .province-card:nth-child(<?= $i + 1 ?>) { animation-delay: <?= $i * 0.07 ?>s; }
        <?php endforeach; ?>
    </style>
</head>
<body>

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


<div class="page-wrapper">

    <div class="page-hero">
        <div class="hero-label">
            <i class="fa-solid fa-trophy"></i> Peringkat Nasional 2025
        </div>
        <h1 class="hero-title">Peringkat Pemerintah<br>Terbaik Indonesia</h1>
        <p class="hero-sub">Berdasarkan kecepatan respon laporan masyarakat di seluruh nusantara</p>

        <div class="hero-stats">
            <div class="stat-pill">
                <span class="val">34</span>
                <span class="lbl">Provinsi</span>
            </div>
            <div class="stat-pill">
                <span class="val">18.2K</span>
                <span class="lbl">Laporan</span>
            </div>
            <div class="stat-pill">
                <span class="val">94%</span>
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
                <a href="PERINGKAT.php" class="tab-active">
                    <i class="fa-solid fa-arrow-up"></i> Tertinggi
                </a>
                <a href="TERENDAH.php" class="tab-idle">
                    <i class="fa-solid fa-arrow-down"></i> Terendah
                </a>
            </div>
        </div>

        <!-- Info -->
        <div class="info-banner">
            <i class="fa-solid fa-circle-info"></i>
            <span>Peringkat diperbarui secara otomatis berdasarkan rata-rata waktu respon laporan yang masuk.</span>
        </div>

        <!-- Section -->
        <div class="section-head">
            <h2>
                <i class="fa-solid fa-medal" style="color: var(--gold)"></i>
                Top 5 Provinsi Tercepat
                <span class="badge-count"><?= count($provinces) ?></span>
            </h2>
            <a href="#">Lihat semua &rarr;</a>
        </div>

        <!-- Cards -->
        <div class="cards-list" id="cardsList">
            <?php foreach ($provinces as $i => $prov):
                $rank = $prov['rank'];
                $isTop1 = $rank === 1;
                $resolveRate = round(($prov['resolved'] / $prov['total_reports']) * 100);
                $rankClass = $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : 'rank-other'));
                $rankLabel = $rank <= 3 ? ['🥇','🥈','🥉'][$rank-1] : $rank;
            ?>
            <div class="province-card <?= $isTop1 ? 'top1' : '' ?>" data-name="<?= htmlspecialchars(strtolower($prov['name'])) ?>">
                <?php if ($isTop1): ?>
                <div class="crown-badge"><i class="fa-solid fa-crown"></i> Terbaik Nasional</div>
                <?php endif; ?>

                <!-- Rank -->
                <div class="rank-badge <?= $rankClass ?>">
                    <?= $rankLabel ?>
                </div>

                <!-- Logo -->
                <img class="province-logo" src="<?= htmlspecialchars($prov['logo']) ?>" alt="Logo <?= htmlspecialchars($prov['name']) ?>">

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
                            <i class="fa-solid fa-check-circle"></i>
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
                        <i class="fa-solid fa-bolt"></i>
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
        if (menu && !menu.contains(e.target)) {
            menu.classList.remove('open');
        }
    });
</script>

</body>
</html>
