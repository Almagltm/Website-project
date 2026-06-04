<?php
require_once '../db.php';

// Guard: hanya user yang sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: MASUK.php");
    exit;
}

$id_user   = (int)$_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Pengguna');
$error     = '';

// Ambil data user untuk display
$u_stmt = $conn->prepare("SELECT nama_lengkap FROM users WHERE id_user = ?");
$u_stmt->bind_param('i', $id_user);
$u_stmt->execute();
$u_res = $u_stmt->get_result()->fetch_assoc();
$u_stmt->close();
if ($u_res) $user_name = htmlspecialchars($u_res['nama_lengkap']);

// Proses submit laporan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul            = trim($_POST['judul']            ?? '');
    $isi_laporan      = trim($_POST['isi']              ?? '');
    $tanggal_kejadian = trim($_POST['tanggal']          ?? '');
    $lokasi           = trim($_POST['lokasi']           ?? '');
    $kecamatan        = trim($_POST['kecamatan']        ?? '');
    $instansi_tujuan  = trim($_POST['instansi']         ?? '');
    $id_kategori      = intval($_POST['id_kategori']    ?? 0);
    $kategori_lainnya = trim($_POST['kategori_lainnya'] ?? '');

    // Foto default berdasarkan kategori
    $foto_map = [
        1 => '../ASSETS/kerusakan_jalan.png', 2 => '../ASSETS/kerusakan_tiang.png',
        3 => '../ASSETS/rusak 1.jpeg',         4 => '../ASSETS/kerusakan_lampu.png',
        5 => '../ASSETS/kerusakan_halte.png',  6 => '../ASSETS/download.jpg',
    ];
    $foto_awal = $foto_map[$id_kategori] ?? '../ASSETS/download.jpg';
    // Simpan path relatif dari root untuk DB
    $foto_awal_db = str_replace('../', '', $foto_awal);

    // Upload foto lampiran
    if (!empty($_FILES['lampiran']['name']) && $_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['lampiran']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $dir = '../uploads/laporan/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $fname = 'lap_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (move_uploaded_file($_FILES['lampiran']['tmp_name'], $dir . $fname)) {
                $foto_awal_db = 'uploads/laporan/' . $fname;
            }
        }
    }

    if (empty($judul) || $id_kategori === 0 || empty($lokasi) || empty($kecamatan)) {
        $error = 'Harap lengkapi semua field yang wajib diisi (*).';
    } else {
        // Cek apakah kolom foto_awal dan kategori_lainnya ada di tabel
        $stmt = $conn->prepare("
INSERT INTO laporan (
    id_user,
    id_kategori,
    judul,
    isi_laporan,
    lokasi,
    kecamatan,
    instansi_tujuan,
    tanggal_kejadian,
    status,
    foto_awal,
    kategori_lainnya
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)
");
$stmt->bind_param(
            'iissssssss',
            $id_user,
            $id_kategori,
            $judul,
            $isi_laporan,
            $lokasi,
            $kecamatan,
            $instansi_tujuan,
            $tanggal_kejadian,
            $foto_awal_db,
            $kategori_lainnya
        );

        if ($stmt->execute()) {
            $id_laporan = $conn->insert_id;


            // Log aktivitas
            logActivity('user', $id_user, $user_name, 'Membuat Laporan Baru', 'ID Laporan: ' . $id_laporan . ', Judul: ' . $judul);

            header("Location: user_detail_laporan.php?id=$id_laporan&baru=1");
            exit;
        } else {
            $error = 'Gagal menyimpan laporan: ' . htmlspecialchars($stmt->error);
            $stmt->close();
        }
    }
}

// Ambil daftar kategori dari DB
$kategori_list = [];
$res = $conn->query("SELECT id_kategori, nama_kategori FROM kategori ORDER BY id_kategori");
if ($res) while ($row = $res->fetch_assoc()) $kategori_list[] = $row;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Formulir Pengaduan – Aksi Kita</title>
  <meta name="description" content="Laporkan kerusakan fasilitas umum di sekitar Anda melalui Aksi Kita."/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;background:#f4f4f4;min-height:100vh;}

/* ── NAVBAR (Logo + Notif + User) ── */
.navbar{background:#1e3d8f;color:#fff;display:flex;align-items:center;
  padding:13px 50px;position:fixed;top:0;left:0;right:0;z-index:1000;gap:10px;}
.logo img{height:50px;}
.nav-right{display:flex;align-items:center;gap:14px;margin-left:auto;}
.nav-user{display:flex;align-items:center;gap:10px;cursor:pointer;}
.nav-user span{color:#fff;font-size:15px;font-weight:500;}
.nav-user img{height:36px;width:36px;border-radius:50%;border:2px solid rgba(255,255,255,.5);}
.noti-wrap{position:relative;display:flex;align-items:center;}
.noti-btn{cursor:pointer;position:relative;width:38px;height:38px;border-radius:50%;
  background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;transition:.2s;}
.noti-btn:hover{background:rgba(255,255,255,.22);}
.noti-badge{position:absolute;top:-3px;right:-3px;background:#e63946;color:#fff;border-radius:50%;
  width:18px;height:18px;display:flex;align-items:center;justify-content:center;
  font-size:10px;font-weight:700;border:2px solid #1e3d8f;}
.noti-dropdown{position:absolute;top:50px;right:0;background:#fff;border-radius:12px;
  box-shadow:0 10px 30px rgba(15,23,42,.15);width:300px;border:1px solid #e2e8f0;
  display:none;z-index:9999;overflow:hidden;}

/* ── HERO MINI ── */
.hero-mini{background:linear-gradient(135deg,#1e3d8f 0%,#2b5fc4 60%,#3d74db 100%);
  margin-top:76px;padding:60px 50px 50px;text-align:center;position:relative;overflow:hidden;}
.hero-mini::before{content:'';position:absolute;inset:0;
  background:url('../ASSETS/Main Background.jpg') center/cover no-repeat;opacity:.12;}
.hero-mini h1{position:relative;color:#fff;font-size:36px;font-weight:700;margin-bottom:10px;}
.hero-mini p{position:relative;color:rgba(255,255,255,.82);font-size:16px;}
.breadcrumb{position:relative;margin-top:18px;display:flex;justify-content:center;gap:8px;
  font-size:14px;color:rgba(255,255,255,.7);}
.breadcrumb a{color:rgba(255,255,255,.85);text-decoration:none;}
.breadcrumb a:hover{color:#fff;}
.breadcrumb span{color:rgba(255,255,255,.5);}

/* ── CONTAINER ── */
.page-wrap{max-width:860px;margin:0 auto;padding:50px 22px 100px;}

/* ── STEPS BAR ── */
.steps{display:flex;background:#fff;border-radius:50px;
  box-shadow:0 4px 18px rgba(30,61,143,.12);overflow:hidden;border:1px solid #e8edf5;margin-bottom:40px;}
.step{flex:1;display:flex;align-items:center;justify-content:center;gap:10px;
  padding:16px 12px;text-decoration:none;transition:background .25s,color .25s;position:relative;
  font-size:14px;font-weight:600;color:#9ca3af;background:#fff;border:none;font-family:'Poppins',sans-serif;}
.step:first-child{border-radius:50px 0 0 50px;}.step:last-child{border-radius:0 50px 50px 0;}
.step:not(:last-child)::after{content:'';position:absolute;right:0;top:20%;height:60%;width:1px;background:#e5e7eb;}
.step .num{width:26px;height:26px;border-radius:50%;background:#f3f4f6;color:#9ca3af;
  display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;transition:.25s;}
.step.active{background:linear-gradient(135deg,#1e3d8f,#2b5fc4);color:#fff;border-radius:50px;
  box-shadow:0 4px 14px rgba(30,61,143,.35);z-index:2;}
.step.active .num{background:rgba(255,255,255,.25);color:#fff;}
.step.active::after{display:none;}

/* ── FORM CARD ── */
.form-card{background:#fff;border-radius:20px;box-shadow:0 8px 30px rgba(30,61,143,.1);overflow:hidden;}
.card-header{background:linear-gradient(135deg,#1e3d8f,#2b5fc4);padding:24px 32px;
  display:flex;align-items:center;gap:14px;}
.card-header .ico{width:48px;height:48px;background:rgba(255,255,255,.2);border-radius:12px;
  display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;}
.card-header h2{color:#fff;font-size:20px;font-weight:700;}
.card-header p{color:rgba(255,255,255,.75);font-size:13px;margin-top:2px;}
.card-body{padding:36px 32px 40px;}

/* ── FORM GROUPS ── */
.row-2{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
.fg{margin-bottom:20px;}
.fg label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px;
  display:flex;align-items:center;gap:7px;}
.fg label i{color:#1e3d8f;font-size:12px;}
.fg input,.fg textarea,.fg select{width:100%;padding:13px 16px;
  border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;
  font-family:'Poppins',sans-serif;background:#fafafa;color:#222;
  transition:border-color .2s,box-shadow .2s,background .2s;}
.fg input:focus,.fg textarea:focus,.fg select:focus{
  outline:none;border-color:#1e3d8f;background:#fff;
  box-shadow:0 0 0 3px rgba(30,61,143,.12);}
.fg textarea{min-height:130px;resize:vertical;}
.fg select{cursor:pointer;}

/* FILE UPLOAD */
.file-wrap{border:2px dashed #c7d7f5;border-radius:10px;background:#f0f5ff;
  padding:24px;text-align:center;cursor:pointer;transition:.25s;position:relative;}
.file-wrap:hover{border-color:#1e3d8f;background:#e8f0ff;}
.file-wrap i{font-size:32px;color:#1e3d8f;margin-bottom:8px;display:block;}
.file-wrap p{font-size:13px;color:#666;margin-bottom:8px;}
.file-wrap input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
.file-name{font-size:12px;color:#1e3d8f;font-weight:600;margin-top:6px;display:none;}

#lainnya-wrap{display:none;margin-top:12px;}

/* ── ACTIONS ── */
.actions{display:flex;gap:14px;margin-top:30px;}
.btn-reset{flex:1;padding:14px;border-radius:10px;border:2px solid #e5e7eb;
  background:#fff;color:#555;font-size:15px;font-weight:600;cursor:pointer;
  font-family:'Poppins',sans-serif;transition:.25s;display:flex;align-items:center;gap:8px;justify-content:center;}
.btn-reset:hover{background:#f5f5f5;border-color:#ccc;}
.btn-submit{flex:2;padding:14px;border-radius:10px;border:none;
  background:linear-gradient(135deg,#1e3d8f,#2b5fc4);color:#fff;
  font-size:15px;font-weight:700;cursor:pointer;font-family:'Poppins',sans-serif;
  transition:.25s;display:flex;align-items:center;gap:8px;justify-content:center;
  box-shadow:0 4px 16px rgba(30,61,143,.35);}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(30,61,143,.45);}

/* ALERT */
.alert-err{background:#fef2f2;border:1.5px solid #fca5a5;color:#b91c1c;
  border-radius:10px;padding:14px 18px;margin-bottom:24px;font-size:14px;
  display:flex;gap:10px;align-items:center;}

/* ── TIPS CARD ── */
.tips-card{background:linear-gradient(135deg,#eef2ff,#e8f0ff);border-radius:16px;
  padding:22px 26px;margin-top:28px;border-left:4px solid #1e3d8f;}
.tips-card h4{color:#1e3d8f;font-size:14px;font-weight:700;margin-bottom:10px;
  display:flex;align-items:center;gap:8px;}
.tips-card ul{list-style:none;display:flex;flex-direction:column;gap:6px;}
.tips-card li{font-size:13px;color:#444;display:flex;align-items:flex-start;gap:8px;}
.tips-card li i{color:#1e3d8f;margin-top:2px;font-size:11px;}

/* ── FOOTER ── */
.main-footer{background:linear-gradient(165deg,#080e18 0%,#102647 70%,#9c7719 120%);
  color:#fff;padding:50px 70px 30px;}
.footer-top{display:flex;align-items:center;gap:12px;margin-bottom:30px;}
.footer-logo{height:50px;}
.footer-content{display:flex;justify-content:space-between;flex-wrap:wrap;gap:20px;margin-bottom:30px;}
.footer-col p,.footer-col a{display:block;margin:5px 0;color:#ccc;font-size:14px;text-decoration:none;transition:.2s;}
.footer-col a:hover{color:#4d8ef5;}
.footer-social{display:flex;gap:12px;margin-bottom:24px;}
.footer-social a{width:38px;height:38px;border-radius:8px;background:#fff;color:#000;
  display:inline-flex;align-items:center;justify-content:center;font-size:16px;transition:.3s;}
.footer-social a:hover{transform:translateY(-4px);}
.footer-bottom{text-align:center;font-size:13px;color:#888;border-top:1px solid rgba(255,255,255,.1);padding-top:20px;}

@media(max-width:700px){
  .navbar{padding:12px 20px;}
  .row-2{grid-template-columns:1fr;}
  .card-body{padding:22px 18px;}
  .hero-mini{padding:50px 20px 40px;}
  .hero-mini h1{font-size:26px;}
  .main-footer{padding:40px 20px 24px;}
  .steps{display:none;}
}
</style>
</head>
<body>

<!-- NAVBAR -->
<header class="navbar">
  <div class="logo"><img src="../ASSETS/LOGO.png" alt="Aksi Kita"></div>
  <div class="nav-right">
    <div class="noti-wrap">
      <div class="noti-btn" id="notiBellBtn">
        <i class="fa-solid fa-bell" style="color:#fff;font-size:16px;"></i>
        <?php if ($noti_count > 0): ?><span class="noti-badge"><?= $noti_count ?></span><?php endif; ?>
      </div>
      <div class="noti-dropdown" id="notiDropdown">
        <div style="padding:12px 16px;background:#f8fafc;border-bottom:1px solid #e2e8f0;font-weight:700;font-size:13.5px;color:#1e3d8f;">Notifikasi</div>
        <div style="max-height:280px;overflow-y:auto;">
          <?php if (empty($noti_items)): ?>
            <div style="padding:24px;text-align:center;color:#64748b;font-size:13px;">Belum ada notifikasi.</div>
          <?php else: foreach ($noti_items as $item): ?>
            <a href="<?= $item['link'] ?>" style="display:block;padding:12px 16px;border-bottom:1px solid #f1f5f9;text-decoration:none;color:inherit;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
              <span style="font-size:12.5px;font-weight:700;color:#1e293b;display:block;"><?= $item['title'] ?></span>
              <span style="font-size:11.5px;color:#64748b;display:block;"><?= $item['desc'] ?></span>
            </a>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
    <div class="nav-user" id="navUser">
      <span><?= $user_name ?></span>
      <img src="../ASSETS/USER.png" alt="User"/>
    </div>
  </div>
</header>

<!-- HERO MINI -->
<section class="hero-mini">
  <h1><i class="fa-solid fa-flag"></i> Formulir Pengaduan</h1>
  <p>Sampaikan laporan kerusakan fasilitas umum di sekitar Anda</p>
  <div class="breadcrumb">
    <a href="BERANDA2.php">Beranda</a><span>/</span><span>Formulir Pengaduan</span>
  </div>
</section>

<main class="page-wrap">

  <!-- STEPS BAR -->
  <div class="steps">
    <div class="step active"><div class="num">1</div> Isi Data Laporan</div>
    <div class="step"><div class="num">2</div> Detail Laporan</div>
    <div class="step"><div class="num">3</div> Selesai</div>
  </div>

  <div class="form-card">
    <div class="card-header">
      <div class="ico"><i class="fa-solid fa-pen-to-square"></i></div>
      <div>
        <h2>Sampaikan Laporan Anda!</h2>
        <p>Pastikan data yang diisi akurat agar laporan dapat segera diproses</p>
      </div>
    </div>
    <div class="card-body">

      <?php if ($error): ?>
      <div class="alert-err"><i class="fa-solid fa-circle-exclamation"></i><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="laporkan.php" enctype="multipart/form-data" id="formLaporan">

        <div class="fg">
          <label for="judul"><i class="fa-solid fa-heading"></i> Judul Laporan *</label>
          <input type="text" id="judul" name="judul" placeholder="Contoh: Jalan berlubang di depan SDN 12 Makassar"
                 required value="<?= htmlspecialchars($_POST['judul'] ?? '') ?>"/>
        </div>

        <div class="fg">
          <label for="isi"><i class="fa-solid fa-align-left"></i> Deskripsi Laporan</label>
          <textarea id="isi" name="isi" placeholder="Ceritakan kondisi yang Anda temukan secara detail..."><?= htmlspecialchars($_POST['isi'] ?? '') ?></textarea>
        </div>

        <div class="row-2">
          <div class="fg">
            <label for="tanggal"><i class="fa-solid fa-calendar-days"></i> Tanggal Kejadian *</label>
            <input type="date" id="tanggal" name="tanggal" required value="<?= htmlspecialchars($_POST['tanggal'] ?? date('Y-m-d')) ?>"/>
          </div>
          <div class="fg">
            <label for="instansi"><i class="fa-solid fa-building"></i> Instansi Tujuan</label>
            <input type="text" id="instansi" name="instansi" placeholder="Contoh: Dinas PU Kota" value="<?= htmlspecialchars($_POST['instansi'] ?? '') ?>"/>
          </div>
        </div>

        <div class="row-2">
          <div class="fg">
            <label for="lokasi"><i class="fa-solid fa-location-dot"></i> Lokasi Kejadian *</label>
            <input type="text" id="lokasi" name="lokasi" placeholder="Contoh: Jl. Gatot Subroto No.5" required value="<?= htmlspecialchars($_POST['lokasi'] ?? '') ?>"/>
          </div>
          <div class="fg">
            <label for="kecamatan"><i class="fa-solid fa-map-location-dot"></i> Kecamatan *</label>
            <select id="kecamatan" name="kecamatan" required>
              <option value="" disabled selected>— Pilih Kecamatan —</option>
              <option value="Medan Amplas" <?= (($_POST['kecamatan'] ?? '') == 'Medan Amplas') ? 'selected' : '' ?>>Medan Amplas</option>
              <option value="Medan Area" <?= (($_POST['kecamatan'] ?? '') == 'Medan Area') ? 'selected' : '' ?>>Medan Area</option>
              <option value="Medan Barat" <?= (($_POST['kecamatan'] ?? '') == 'Medan Barat') ? 'selected' : '' ?>>Medan Barat</option>
              <option value="Medan Baru" <?= (($_POST['kecamatan'] ?? '') == 'Medan Baru') ? 'selected' : '' ?>>Medan Baru</option>
              <option value="Medan Belawan" <?= (($_POST['kecamatan'] ?? '') == 'Medan Belawan') ? 'selected' : '' ?>>Medan Belawan</option>
              <option value="Medan Deli" <?= (($_POST['kecamatan'] ?? '') == 'Medan Deli') ? 'selected' : '' ?>>Medan Deli</option>
              <option value="Medan Denai" <?= (($_POST['kecamatan'] ?? '') == 'Medan Denai') ? 'selected' : '' ?>>Medan Denai</option>
              <option value="Medan Helvetia" <?= (($_POST['kecamatan'] ?? '') == 'Medan Helvetia') ? 'selected' : '' ?>>Medan Helvetia</option>
              <option value="Medan Johor" <?= (($_POST['kecamatan'] ?? '') == 'Medan Johor') ? 'selected' : '' ?>>Medan Johor</option>
              <option value="Medan Kota" <?= (($_POST['kecamatan'] ?? '') == 'Medan Kota') ? 'selected' : '' ?>>Medan Kota</option>
              <option value="Medan Labuhan" <?= (($_POST['kecamatan'] ?? '') == 'Medan Labuhan') ? 'selected' : '' ?>>Medan Labuhan</option>
              <option value="Medan Maimun" <?= (($_POST['kecamatan'] ?? '') == 'Medan Maimun') ? 'selected' : '' ?>>Medan Maimun</option>
              <option value="Medan Marelan" <?= (($_POST['kecamatan'] ?? '') == 'Medan Marelan') ? 'selected' : '' ?>>Medan Marelan</option>
              <option value="Medan Perjuangan" <?= (($_POST['kecamatan'] ?? '') == 'Medan Perjuangan') ? 'selected' : '' ?>>Medan Perjuangan</option>
              <option value="Medan Petisah" <?= (($_POST['kecamatan'] ?? '') == 'Medan Petisah') ? 'selected' : '' ?>>Medan Petisah</option>
              <option value="Medan Polonia" <?= (($_POST['kecamatan'] ?? '') == 'Medan Polonia') ? 'selected' : '' ?>>Medan Polonia</option>
              <option value="Medan Selayang" <?= (($_POST['kecamatan'] ?? '') == 'Medan Selayang') ? 'selected' : '' ?>>Medan Selayang</option>
              <option value="Medan Sunggal" <?= (($_POST['kecamatan'] ?? '') == 'Medan Sunggal') ? 'selected' : '' ?>>Medan Sunggal</option>
              <option value="Medan Tembung" <?= (($_POST['kecamatan'] ?? '') == 'Medan Tembung') ? 'selected' : '' ?>>Medan Tembung</option>
              <option value="Medan Timur" <?= (($_POST['kecamatan'] ?? '') == 'Medan Timur') ? 'selected' : '' ?>>Medan Timur</option>
              <option value="Medan Tuntungan" <?= (($_POST['kecamatan'] ?? '') == 'Medan Tuntungan') ? 'selected' : '' ?>>Medan Tuntungan</option>
            </select>
          </div>
        </div>

        <div class="fg">
          <label><i class="fa-solid fa-tag"></i> Kategori Laporan *</label>
          <select id="id_kategori" name="id_kategori" required onchange="cekLainnya(this)">
            <option value="" disabled selected>— Pilih Kategori —</option>
            <?php foreach ($kategori_list as $k): ?>
            <option value="<?= $k['id_kategori'] ?>" <?= (($_POST['id_kategori'] ?? '') == $k['id_kategori']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($k['nama_kategori']) ?>
            </option>
            <?php endforeach; ?>
          </select>
          <div id="lainnya-wrap">
            <input type="text" id="kategori_lainnya" name="kategori_lainnya" placeholder="Tuliskan kategori Anda..." value="<?= htmlspecialchars($_POST['kategori_lainnya'] ?? '') ?>"/>
          </div>
        </div>

        <div class="fg">
          <label><i class="fa-solid fa-image"></i> Unggah Foto Laporan</label>
          <div class="file-wrap" id="fileWrap">
            <i class="fa-solid fa-cloud-arrow-up"></i>
            <p>Klik atau seret foto ke sini</p>
            <small style="color:#999">Format: JPG, PNG, WEBP (maks. 10MB)</small>
            <div class="file-name" id="fileName"></div>
            <input type="file" id="lampiran" name="lampiran" accept="image/*" onchange="showFileName(this)"/>
          </div>
        </div>

        <div class="actions">
          <button type="reset" class="btn-reset" onclick="resetForm()"><i class="fa-solid fa-rotate-left"></i> Batalkan</button>
          <button type="submit" class="btn-submit" id="btnSubmit"><i class="fa-solid fa-paper-plane"></i> Kirim Laporan</button>
        </div>
      </form>

      <div class="tips-card">
        <h4><i class="fa-solid fa-lightbulb"></i> Tips Laporan yang Baik</h4>
        <ul>
          <li><i class="fa-solid fa-check"></i> Gunakan judul yang jelas dan spesifik</li>
          <li><i class="fa-solid fa-check"></i> Sertakan alamat lengkap dengan patokan</li>
          <li><i class="fa-solid fa-check"></i> Foto yang jelas mempercepat proses verifikasi</li>
          <li><i class="fa-solid fa-check"></i> Pilih instansi tujuan yang tepat</li>
        </ul>
      </div>

    </div>
  </div>
</main>

<footer class="main-footer">
  <div class="footer-top"><img src="../ASSETS/LOGO.png" class="footer-logo" alt="Aksi Kita"><h3>Aksi Kita</h3></div>
  <div class="footer-content">
    <div class="footer-col"><p>Jl. Bachireng No. 12, Indonesia</p><p>0821 6888 9060</p><p>info@aksikita.id</p></div>
    <div class="footer-col"><a href="#">Unit Layanan Terpadu</a><a href="#">Cara Kerja</a><a href="#">FAQ</a></div>
    <div class="footer-col"><a href="laporkan.php">Lapor</a><a href="#">Tentang Kami</a></div>
  </div>
  <div class="footer-social">
    <a href="#"><i class="fab fa-whatsapp"></i></a><a href="#"><i class="fab fa-facebook"></i></a>
    <a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fab fa-youtube"></i></a>
  </div>
  <div class="footer-bottom">© 2025 AksiKita. Semua Hak Dilindungi.</div>
</footer>

<?php include 'profile_modal.php'; ?>

<script>
function cekLainnya(sel){
  const wrap=document.getElementById('lainnya-wrap');
  const inp=document.getElementById('kategori_lainnya');
  if(sel.options[sel.selectedIndex]?.textContent.trim()==='Lainnya'){
    wrap.style.display='block'; inp.required=true;
  } else { wrap.style.display='none'; inp.required=false; inp.value=''; }
}
function showFileName(inp){
  const fn=document.getElementById('fileName');
  if(inp.files[0]){fn.textContent='✓ '+inp.files[0].name;fn.style.display='block';}
}
function resetForm(){
  document.getElementById('fileName').style.display='none';
  document.getElementById('lainnya-wrap').style.display='none';
}
document.getElementById('formLaporan').addEventListener('submit',function(){
  const btn=document.getElementById('btnSubmit');
  btn.innerHTML='<i class="fa-solid fa-spinner fa-spin"></i> Mengirim...';
  btn.disabled=true;
});
window.addEventListener('DOMContentLoaded',()=>{
  const s=document.getElementById('id_kategori');
  if(s.value) cekLainnya(s);
});
// Notification dropdown
const notiBellBtn=document.getElementById('notiBellBtn');
const notiDropdown=document.getElementById('notiDropdown');
if(notiBellBtn&&notiDropdown){
  notiBellBtn.addEventListener('click',function(e){e.stopPropagation();notiDropdown.style.display=notiDropdown.style.display==='block'?'none':'block';});
  document.addEventListener('click',function(e){if(!notiDropdown.contains(e.target)&&!notiBellBtn.contains(e.target)){notiDropdown.style.display='none';}});
}
</script>
</body>
</html>
