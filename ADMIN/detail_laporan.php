<?php
/**
 * Admin/detail_laporan.php
 * Halaman detail & kelola laporan untuk admin
 * Tanpa navbar links & tombol Kembali di navbar
 */
require_once '../db.php';

if (!isset($_SESSION['admin_id'])) { header("Location: Login.php"); exit; }

$id_admin   = (int)$_SESSION['admin_id'];
$admin_name = htmlspecialchars($_SESSION['nama_admin'] ?? 'Admin');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: Kelola_Laporan.php"); exit; }

$stmt = $conn->prepare("SELECT l.*, k.nama_kategori, u.nama_lengkap FROM laporan l
    LEFT JOIN kategori k ON k.id_kategori = l.id_kategori
    LEFT JOIN users u ON u.id_user = l.id_user
    WHERE l.id_laporan = ?");
$stmt->bind_param('i', $id); $stmt->execute();
$lap = $stmt->get_result()->fetch_assoc(); $stmt->close();

if (!$lap) { echo "<p style='padding:40px;font-family:Poppins;color:red'>Laporan tidak ditemukan.</p>"; exit; }

$sl = $lap['status'];

// Auto-update ke diproses jika tab=proses dan masih pending
$tab_req = $_GET['tab'] ?? '';
if ($tab_req === 'proses' && $sl === 'pending') {
    $conn->query("UPDATE laporan SET status='diproses', tanggal_pengerjaan=NOW(), updated_at=NOW() WHERE id_laporan=$id");
    logActivity('admin', $id_admin, $admin_name, 'Ubah Status ke diproses', 'ID: '.$id.', Judul: '.$lap['judul']);
    $adm_r = $conn->prepare("SELECT id_admin FROM admins WHERE id_admin = ?"); $adm_r->bind_param('i',$id_admin); $adm_r->execute(); $adm_r->close();
    logStatusHistory($id, $id_admin, 'pending', 'diproses', 'Laporan mulai diproses oleh admin.');
    $sl = 'diproses'; $lap['status'] = 'diproses'; $lap['tanggal_pengerjaan'] = date('Y-m-d H:i:s');
}

$def = $sl === 'selesai' ? 'selesai' : ($sl === 'diproses' ? 'proses' : 'detail');
$tab = in_array($tab_req, ['detail','proses','selesai']) ? $tab_req : $def;

$foto       = (!empty($lap['foto_awal'])  && file_exists('../'.$lap['foto_awal']))  ? htmlspecialchars('../'.$lap['foto_awal'])  : '../ASSETS/rusak 1.jpeg';
$foto_bukti = (!empty($lap['foto_bukti']) && file_exists('../'.$lap['foto_bukti'])) ? htmlspecialchars('../'.$lap['foto_bukti']) : '../ASSETS/verifikasicepat.jpg';
$slabel = ['pending'=>'Menunggu','diproses'=>'Sedang Diproses','selesai'=>'Selesai','ditolak'=>'Ditolak'];
$t2 = $tab==='detail' ? 'active' : ($sl!=='pending' ? 'done' : '');
$t3 = $tab==='proses'  ? 'active' : ($sl==='selesai' ? 'done' : '');
$t4 = $tab==='selesai' ? 'active' : ($sl==='selesai' ? 'done' : '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title><?= htmlspecialchars($lap['judul']) ?> – Admin Aksi Kita</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;background:#f4f7ff;min-height:100vh;}

/* ── NAVBAR ── */
.navbar{background:#1e3d8f;color:#fff;display:flex;align-items:center;padding:13px 50px;position:fixed;top:0;left:0;right:0;z-index:1000;gap:10px;box-shadow:0 4px 16px rgba(0,0,0,.2);}
.logo img{height:50px;}
.navbar .navlinks { display: none !important; }
.nav-auth { display: none !important; }
.nav-user{display:flex;align-items:center;gap:10px;cursor:pointer;}
.nav-user span{color:#fff;font-size:15px;font-weight:500;}
.nav-user img{height:36px;width:36px;border-radius:50%;border:2px solid rgba(255,255,255,.4);}

/* ── HERO MINI ── */
.hero-mini{background:linear-gradient(135deg,#1e3d8f,#2b5fc4 60%,#3d74db);margin-top:76px;padding:55px 50px 45px;text-align:center;position:relative;overflow:hidden;}
.hero-mini::before{content:'';position:absolute;inset:0;background:url('../ASSETS/Main Background.jpg') center/cover no-repeat;opacity:.12;}
.hero-mini h1{position:relative;color:#fff;font-size:30px;font-weight:700;margin-bottom:10px;}
.hero-mini .badge{display:inline-block;padding:6px 18px;border-radius:20px;font-size:13px;font-weight:700;margin-top:8px;}

/* BADGES */
.badge{display:inline-block;padding:5px 12px;border-radius:12px;font-size:12px;font-weight:700;text-transform:uppercase;}
.badge-pending{background:#fef3c7;color:#92400e;}
.badge-diproses{background:#dbeafe;color:#1e40af;}
.badge-selesai{background:#d1fae5;color:#166534;}
.badge-ditolak{background:#fee2e2;color:#991b1b;}

.breadcrumb{position:relative;margin-top:14px;display:flex;justify-content:center;gap:8px;font-size:13px;color:rgba(255,255,255,.75);}
.breadcrumb a{color:rgba(255,255,255,.9);text-decoration:none;}
.breadcrumb a:hover{color:#fff;}
.breadcrumb span{color:rgba(255,255,255,.5);}

.wrap{max-width:960px;margin:0 auto;padding:40px 22px 80px;}

/* ── STEPS ── */
.steps-wrap{margin-bottom:32px;}
.steps{display:flex;background:#fff;border-radius:50px;box-shadow:0 4px 18px rgba(30,61,143,.1);overflow:hidden;border:1px solid #e8edf5;}
.step{flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:14px 10px;font-size:13.5px;font-weight:600;color:#9ca3af;position:relative;text-decoration:none;}
.step:not(:last-child)::after{content:'';position:absolute;right:0;top:20%;height:60%;width:1px;background:#e5e7eb;}
.step .num{width:24px;height:24px;border-radius:50%;background:#f3f4f6;color:#9ca3af;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;}
.step.done{color:#10b981;}.step.done .num{background:#d1fae5;color:#10b981;}
.step.active{color:#b91c1c;font-weight:700;}.step.active .num{background:#fee2e2;color:#b91c1c;}
.step.disabled{opacity:.5;}

/* ── BANNERS ── */
.thanks-banner {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 18px 24px;
  border-radius: 14px;
  margin-bottom: 24px;
  border: 1px solid #6ee7b7;
  background: #f0fdf4;
}
.thanks-banner .tbi {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  color: #fff;
  flex-shrink: 0;
  background: linear-gradient(135deg,#059669,#10b981);
}
.thanks-banner h3 { color: #166534; font-size: 16px; margin-bottom: 3px; }
.thanks-banner p { color: #166534; font-size: 13px; }

/* ── MAIN CARD ── */
.main-card{background:#fff;border-radius:18px;box-shadow:0 8px 28px rgba(30,61,143,.1);overflow:hidden;display:flex;margin-bottom:24px;}
.foto-side{width:320px;flex-shrink:0;position:relative;overflow:hidden;background:#cbd5e1;}
.foto-side img{width:100%;height:100%;object-fit:cover;display:block;}
.foto-overlay{position:absolute;bottom:12px;left:12px;}
.detail-side{flex:1;padding:28px 30px;}
.d-row{display:flex;gap:14px;margin-bottom:16px;}
.d-ico{width:36px;height:36px;background:#eef2ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.d-ico i{color:#1e3d8f;font-size:14px;}
.d-txt label{display:block;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;margin-bottom:3px;}
.d-txt span{font-size:14px;color:#1e293b;line-height:1.5;}
hr.divider{border:none;border-top:1px solid #f1f5f9;margin:8px 0 16px;}

/* ── PROSES CARD ── */
.proses-card{background:#fff;border-radius:18px;box-shadow:0 8px 28px rgba(30,61,143,.1);overflow:hidden;display:flex;margin-bottom:24px;}
.proses-img{width:320px;height:240px;object-fit:cover;display:block;flex-shrink:0;background:#cbd5e1;}
.proses-info{flex:1;padding:28px 30px;display:flex;flex-direction:column;justify-content:center;}
.proses-info h3{font-size:18px;color:#1e293b;margin-bottom:12px;}
.proses-info .meta{display:flex;align-items:center;gap:8px;font-size:13px;color:#64748b;margin-bottom:8px;}
.proses-info .meta i{color:#1e3d8f;width:14px;}
.tgl-proses{display:none;align-items:center;gap:8px;font-size:13px;color:#10b981;background:#d1fae5;padding:8px 14px;border-radius:8px;margin-top:10px;width:max-content;font-weight:600;}
.tgl-proses.show{display:flex;}
.btn-group{display:flex;gap:12px;margin-top:18px;}

/* ── SELESAI CARD ── */
.selesai-card{background:#fff;border-radius:18px;box-shadow:0 8px 28px rgba(30,61,143,.1);overflow:hidden;margin-bottom:24px;}
.selesai-head{background:linear-gradient(135deg,#059669,#10b981);padding:22px 28px;display:flex;align-items:center;gap:16px;}
.selesai-head .ico{width:48px;height:48px;background:rgba(255,255,255,.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;}
.selesai-head h2{color:#fff;font-size:18px;}.selesai-head p{color:rgba(255,255,255,.8);font-size:13px;}
.selesai-body{display:flex;gap:24px;padding:28px;}
.bukti-foto{width:240px;flex-shrink:0;border-radius:12px;overflow:hidden;background:#cbd5e1;}
.bukti-foto img{width:100%;height:180px;object-fit:cover;display:block;}
.bukti-info{flex:1;}
.bukti-info h4{font-size:15px;color:#1e3d8f;margin-bottom:10px;display:flex;align-items:center;gap:8px;}
.bukti-info p{font-size:13.5px;color:#444;line-height:1.6;margin-bottom:14px;}
.bukti-meta .bm{display:flex;align-items:center;gap:8px;font-size:13px;color:#555;margin-bottom:6px;}
.bukti-meta .bm i{color:#1e3d8f;font-size:12px;}

/* ── BUTTONS & ACTION BAR ── */
.action-bar{display:flex;gap:14px;margin-top:8px;}
.btn{display:inline-flex;align-items:center;gap:8px;padding:12px 22px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;transition:.2s;cursor:pointer;border:none;font-family:'Poppins',sans-serif;}
.btn-primary{background:linear-gradient(135deg,#1e3d8f,#2b5fc4);color:#fff;box-shadow:0 4px 14px rgba(30,61,143,.3);}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(30,61,143,.4);}
.btn-warning{background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;box-shadow:0 4px 14px rgba(245,158,11,.3);}.btn-warning:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(245,158,11,.4);}
.btn-success{background:linear-gradient(135deg,#059669,#10b981);color:#fff;box-shadow:0 4px 14px rgba(16,185,129,.3);}.btn-success:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(16,185,129,.4);}
.btn-ghost{background:#f1f5f9;color:#475569;border:1.5px solid #e2e8f0;}.btn-ghost:hover{background:#e2e8f0;}

/* ── POPUPS ── */
.overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9998;display:none;align-items:center;justify-content:center;}
.overlay.open{display:flex;}
.pop-card{background:#fff;border-radius:20px;padding:32px 28px;max-width:420px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.3);font-family:'Poppins',sans-serif;text-align:left;}
.pop-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;border-bottom:1px solid #f1f5f9;padding-bottom:12px;}
.pop-head h3{font-size:16px;color:#1e3d8f;}
.pop-close{background:none;border:none;font-size:18px;color:#94a3b8;cursor:pointer;}
.pop-body .fg{margin-bottom:16px;}
.pop-body label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
.pop-body input[type=file]{width:100%;padding:10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;}
.pop-body textarea{width:100%;height:100px;padding:12px;border:1.5px solid #e2e8f0;border-radius:8px;resize:none;font-family:'Poppins',sans-serif;font-size:13.5px;}
.pop-body textarea:focus{outline:none;border-color:#1e3d8f;}
.pop-foot{display:flex;gap:12px;margin-top:20px;}

/* ── FOOTER ── */
.main-footer{background:linear-gradient(165deg,#080e18,#102647 70%,#9c7719);color:#fff;padding:50px 70px 30px;margin-top:60px;}
.footer-top{display:flex;align-items:center;gap:12px;margin-bottom:28px;}
.footer-logo{height:48px;}
.footer-content{display:flex;justify-content:space-between;flex-wrap:wrap;gap:20px;margin-bottom:24px;}
.footer-col {
  flex: 1;
  min-width: 200px;
}

.footer-col p {
  margin: 6px 0;
  color: #ccc;
  font-size: 15px;
}

.footer-col a {
  display: block;
  margin: 6px 0;
  color: #eee;
  text-decoration: none;
  font-size: 15px;
  transition: 0.2s;
}

.footer-col a:hover {
  color: #0077ff;
}

.footer-social {
  display: flex;
  gap: 15px;
  margin-bottom: 35px;
}

.footer-social a {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #000;
  background: #fff;
  text-decoration: none;
  font-size: 18px;
  transition: 0.3s;
}

.footer-social a:hover {
  transform: translateY(-5px);
}
.footer-bottom {
  text-align: center;
  font-size: 14px;
  color: #ccc;
}

@media(max-width:800px){
  .navbar{padding:12px 16px;}
  .main-card, .proses-card{flex-direction:column;}
  .foto-side, .proses-img{width:100%;height:220px;}
  .selesai-body{flex-direction:column;}
  .bukti-foto{width:100%;}
  .hero-mini{padding:50px 20px 36px;}
}

/* ── LIGHTBOX ── */
.lightbox-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.9);
  backdrop-filter: blur(8px);
  z-index: 99999;
  display: none;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}
.lightbox-overlay.open {
  display: flex;
  opacity: 1;
}
.lightbox-content {
  max-width: 90%;
  max-height: 85%;
  object-fit: contain;
  border-radius: 12px;
  box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
  transform: scale(0.95);
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.lightbox-overlay.open .lightbox-content {
  transform: scale(1);
}
.lightbox-close {
  position: absolute;
  top: 24px;
  right: 24px;
  width: 44px;
  height: 44px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 20px;
  cursor: pointer;
  transition: all 0.2s;
}
.lightbox-close:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: scale(1.05);
}
</style>
</head><body>

<!-- NAVBAR: Logo + Admin Avatar (tanpa navlinks & tombol Kembali) -->
<header class="navbar">
  <div class="logo"><img src="../ASSETS/LOGO.png" alt="Aksi Kita"></div>
  <!-- Notification Bell -->
  <div class="nav-noti-wrap" style="position:relative;margin-left:auto;display:flex;align-items:center;">
    <div id="notiBellBtn" style="cursor:pointer;position:relative;width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;transition:.2s;">
      <i class="fa-solid fa-bell" style="color:#fff;font-size:16px;"></i>
      <?php if ($noti_count > 0): ?>
        <span style="position:absolute;top:-3px;right:-3px;background:#e63946;color:#fff;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;border:2px solid #1e3d8f;"><?= $noti_count ?></span>
      <?php endif; ?>
    </div>
    <div id="notiDropdown" style="position:absolute;top:50px;right:0;background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(15,23,42,.15);width:300px;border:1px solid #e2e8f0;display:none;z-index:9999;overflow:hidden;font-family:'Poppins',sans-serif;">
      <div style="padding:12px 16px;background:#f8fafc;border-bottom:1px solid #e2e8f0;font-weight:700;font-size:13.5px;color:#1e3d8f;display:flex;justify-content:space-between;align-items:center;">
        <span>Notifikasi</span>
        <?php if ($noti_count > 0): ?><span style="background:#dce8ff;color:#1e3d8f;font-size:10.5px;padding:2px 6px;border-radius:10px;font-weight:600;"><?= $noti_count ?> Baru</span><?php endif; ?>
      </div>
      <div style="max-height:280px;overflow-y:auto;">
        <?php if (empty($noti_items)): ?>
          <div style="padding:24px;text-align:center;color:#64748b;font-size:13px;"><i class="fa-solid fa-bell-slash" style="font-size:24px;color:#cbd5e1;margin-bottom:8px;display:block;"></i>Belum ada notifikasi.</div>
        <?php else: foreach ($noti_items as $item): ?>
          <a href="<?= $item['link'] ?>" style="display:block;padding:12px 16px;border-bottom:1px solid #f1f5f9;text-decoration:none;color:inherit;transition:.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
            <span style="font-size:12.5px;font-weight:700;color:#1e293b;display:block;margin-bottom:2px;"><?= $item['title'] ?></span>
            <span style="font-size:11.5px;color:#64748b;display:block;"><?= $item['desc'] ?></span>
            <span style="font-size:10px;color:#94a3b8;display:block;"><i class="fa-regular fa-clock" style="margin-right:4px;"></i><?= $item['time'] ?></span>
          </a>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>
  <!-- Admin name -->
  <div class="nav-user" id="navAdminUser" style="margin-left:15px;cursor:pointer;display:flex;align-items:center;gap:10px;">
    <span><?= $admin_name ?></span>
    <img src="../ASSETS/USER.png" alt="Admin" style="height:36px;width:36px;border-radius:50%;border:2px solid rgba(255,255,255,.4);"/>
  </div>
</header>

<!-- HERO MINI -->
<section class="hero-mini">
  <h1><i class="fa-solid fa-file-lines"></i> <?= htmlspecialchars($lap['judul']) ?></h1>
  <p><span class="badge badge-<?= $sl ?>"><?= $slabel[$sl] ?? $sl ?></span></p>
  <div class="breadcrumb">
    <a href="Kelola_Laporan.php">Kelola Laporan</a><span>/</span><span>Detail</span>
  </div>
</section>

<?php if (isset($_GET['baru'])): ?>
<div class="overlay open" id="ovBaru">
  <div class="pop-card" style="max-width:360px;text-align:center;padding:40px 30px;border-radius:50px 0 50px 0">
    <div style="width:68px;height:68px;background:linear-gradient(135deg,#1e3d8f,#2b5fc4);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;color:#fff;margin:0 auto 16px"><i class="fa-solid fa-check"></i></div>
    <h2 style="color:#1e3d8f;font-size:19px;margin-bottom:8px">Laporan Diterima!</h2>
    <p style="color:#555;font-size:14px;margin-bottom:20px">Laporan ini sedang menunggu tindak lanjut.</p>
    <button class="btn btn-primary" onclick="document.getElementById('ovBaru').classList.remove('open')"><i class="fa-solid fa-eye"></i> Lihat Detail</button>
  </div>
</div>
<?php endif; ?>

<main class="wrap">
  <!-- STEPS -->
  <div class="steps-wrap">
    <div class="steps">
      <a href="detail_laporan.php?id=<?= $id ?>&tab=detail" class="step <?= $t2 ?>">
        <div class="num"><?= $t2==='done' ? '<i class="fa-solid fa-check" style="font-size:10px"></i>' : '1' ?></div> Detail Laporan
      </a>
      <a href="detail_laporan.php?id=<?= $id ?>&tab=proses" class="step <?= $t3 ?>">
        <div class="num"><?= $t3==='done' ? '<i class="fa-solid fa-check" style="font-size:10px"></i>' : '2' ?></div> Proses
      </a>
      <a href="detail_laporan.php?id=<?= $id ?>&tab=selesai" class="step <?= $t4 ?>">
        <div class="num"><?= ($t4==='active' || $t4==='done') ? '<i class="fa-solid fa-check" style="font-size:10px"></i>' : '3' ?></div> Selesai
      </a>
    </div>
  </div>

<?php if ($tab === 'detail'): ?>
  <div class="main-card">
    <div class="foto-side">
      <img src="<?= $foto ?>" alt="<?= htmlspecialchars($lap['judul']) ?>" style="cursor: pointer;" onclick="openLightbox(this.src)"/>
      <div class="foto-overlay"><span class="badge badge-<?= $sl ?>"><?= $slabel[$sl] ?? $sl ?></span></div>
    </div>
    <div class="detail-side">
      <div class="d-row"><div class="d-ico"><i class="fa-solid fa-tag"></i></div><div class="d-txt"><label>Kategori</label><span><?= htmlspecialchars($lap['nama_kategori'] ?? '-') ?><?= (!empty($lap['kategori_lainnya'] ?? '')) ? ' – '.htmlspecialchars($lap['kategori_lainnya']) : '' ?></span></div></div>
      <div class="d-row"><div class="d-ico"><i class="fa-solid fa-location-dot"></i></div><div class="d-txt"><label>Lokasi</label><span><?= htmlspecialchars($lap['lokasi'] ?? '-') ?></span></div></div>
      <div class="d-row"><div class="d-ico"><i class="fa-solid fa-calendar-days"></i></div><div class="d-txt"><label>Tanggal Kejadian</label><span><?= $lap['tanggal_kejadian'] ? date('d F Y', strtotime($lap['tanggal_kejadian'])) : '-' ?></span></div></div>
      <div class="d-row"><div class="d-ico"><i class="fa-solid fa-building"></i></div><div class="d-txt"><label>Instansi Tujuan</label><span><?= htmlspecialchars($lap['instansi_tujuan'] ?: '-') ?></span></div></div>
      <div class="d-row"><div class="d-ico"><i class="fa-solid fa-user"></i></div><div class="d-txt"><label>Dilaporkan Oleh</label><span><?= htmlspecialchars($lap['nama_lengkap'] ?? 'Pengguna') ?></span></div></div>
      <div class="d-row"><div class="d-ico"><i class="fa-solid fa-clock"></i></div><div class="d-txt"><label>Waktu Laporan</label><span><?= date('d F Y, H:i', strtotime($lap['created_at'])) ?> WIB</span></div></div>
      <hr class="divider"/>
      <div class="d-row"><div class="d-ico"><i class="fa-solid fa-align-left"></i></div><div class="d-txt"><label>Deskripsi</label><span><?= nl2br(htmlspecialchars($lap['isi_laporan'] ?: '-')) ?></span></div></div>
    </div>
  </div>
  <div class="action-bar">
    <a href="detail_laporan.php?id=<?= $id ?>&tab=proses" class="btn btn-warning"><i class="fa-solid fa-play"></i> Lanjut ke Proses</a>
    <div class="sep"></div>
    <a href="Kelola_Laporan.php" class="btn btn-ghost"><i class="fa-solid fa-list"></i> Daftar Laporan</a>
  </div>

<?php elseif ($tab === 'proses'): ?>
  <div class="proses-card">
    <div class="foto-side">
      <img src="<?= $foto ?>" alt="<?= htmlspecialchars($lap['judul']) ?>" style="cursor: pointer;" onclick="openLightbox(this.src)"/>
      <div class="foto-overlay"><span class="badge badge-<?= $sl ?>"><?= $slabel[$sl] ?? $sl ?></span></div>
    </div>
    <div class="proses-info">
      <h3><?= htmlspecialchars($lap['judul']) ?></h3>
      <div class="meta"><i class="fa-solid fa-location-dot"></i><?= htmlspecialchars($lap['lokasi'] ?? '-') ?></div>
      <div class="meta"><i class="fa-solid fa-tag"></i><?= htmlspecialchars($lap['nama_kategori'] ?? '-') ?></div>
      <div class="meta"><i class="fa-solid fa-calendar-days"></i><?= $lap['tanggal_kejadian'] ? date('d F Y', strtotime($lap['tanggal_kejadian'])) : '-' ?></div>
      <?php if ($lap['tanggal_pengerjaan']): ?>
        <div class="tgl-proses show"><i class="fa-solid fa-circle-check"></i>Mulai dikerjakan: <?= date('d F Y, H:i', strtotime($lap['tanggal_pengerjaan'])) ?> WIB</div>
      <?php else: ?>
        <div class="tgl-proses" id="tglProses"><i class="fa-solid fa-circle-check"></i><span id="tglTxt"></span></div>
      <?php endif; ?>
      <div class="btn-group">
        <?php if ($sl === 'pending'): ?>
          <button class="btn btn-warning" id="btnProses" onclick="handleProses()"><i class="fa-solid fa-play"></i> Proses Laporan</button>
        <?php endif; ?>
        <?php if ($sl === 'diproses' || $sl === 'selesai'): ?>
          <button class="btn btn-primary" onclick="bukaUpload()"><i class="fa-solid fa-upload"></i> Upload Bukti</button>
        <?php endif; ?>
        <a href="detail_laporan.php?id=<?= $id ?>&tab=selesai" class="btn btn-success"><i class="fa-solid fa-flag-checkered"></i> Tandai Selesai</a>
      </div>
    </div>
  </div>

<?php else: // tab selesai ?>
  <?php if ($sl === 'selesai'): ?>
  <div class="thanks-banner">
    <div class="tbi"><i class="fa-solid fa-heart"></i></div>
    <div><h3>Laporan Selesai! 🎉</h3><p>Laporan ini telah berhasil diselesaikan dan ditindaklanjuti.</p></div>
  </div>
  <div class="selesai-card">
    <div class="selesai-head"><div class="ico"><i class="fa-solid fa-flag-checkered"></i></div><div><h2>Riwayat Bukti Perbaikan</h2><p>Selesai pada: <?= $lap['tanggal_pengerjaan'] ? date('d F Y, H:i', strtotime($lap['tanggal_pengerjaan'])).' WIB' : '-' ?></p></div></div>
    <div class="selesai-body">
      <div class="bukti-foto"><img src="<?= $foto_bukti ?>" alt="Bukti Perbaikan" style="cursor: pointer;" onclick="openLightbox(this.src)"/></div>
      <div class="bukti-info">
        <h4><i class="fa-solid fa-clipboard-check"></i> Keterangan Perbaikan</h4>
        <p><?= nl2br(htmlspecialchars($lap['bukti_deskripsi'] ?: 'Tidak ada deskripsi.')) ?></p>
        <div class="bukti-meta">
          <div class="bm"><i class="fa-solid fa-check-circle"></i>Status: <strong>Selesai</strong></div>
          <div class="bm"><i class="fa-solid fa-user-check"></i>Pelapor: <?= htmlspecialchars($lap['nama_lengkap'] ?? 'Pengguna') ?></div>
          <div class="bm"><i class="fa-solid fa-calendar-check"></i>Laporan dibuat: <?= date('d F Y', strtotime($lap['created_at'])) ?></div>
        </div>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="selesai-card">
    <div class="selesai-head" style="background:linear-gradient(135deg,#f59e0b,#d97706)"><div class="ico"><i class="fa-solid fa-hourglass-half"></i></div><div><h2>Laporan Belum Selesai</h2><p>Upload bukti perbaikan untuk menandai laporan selesai</p></div></div>
    <div style="padding:32px;text-align:center"><p style="color:#555;font-size:14px;margin-bottom:20px">Klik tombol di bawah untuk mengupload bukti perbaikan.</p>
      <button class="btn btn-success" onclick="bukaUpload()"><i class="fa-solid fa-upload"></i> Upload Bukti Perbaikan</button>
    </div>
  </div>
  <?php endif; ?>
<?php endif; ?>
</main>

<!-- UPLOAD MODAL -->
<div class="overlay" id="ovUpload">
  <div class="pop-card">
    <div class="pop-head"><h3><i class="fa-solid fa-upload"></i> Upload Bukti Laporan</h3><button class="pop-close" onclick="tutupUpload()">✕</button></div>
    <form id="formBukti" enctype="multipart/form-data">
      <input type="hidden" name="id_laporan" value="<?= $id ?>"/>
      <div class="pop-body">
        <div class="fg"><label><i class="fa-solid fa-image"></i> Foto Bukti Penyelesaian *</label><input type="file" name="foto_bukti" accept="image/*" required/></div>
        <div class="fg"><label><i class="fa-solid fa-pen-to-square"></i> Deskripsi Kondisi Setelah Perbaikan *</label><textarea name="bukti_deskripsi" placeholder="Jelaskan kondisi setelah perbaikan..." required></textarea></div>
      </div>
      <div class="pop-foot">
        <button type="button" class="btn btn-ghost" onclick="tutupUpload()"><i class="fa-solid fa-xmark"></i> Batal</button>
        <button type="submit" class="btn btn-success" style="flex:1;justify-content:center"><i class="fa-solid fa-flag-checkered"></i> Tandai Selesai</button>
      </div>
    </form>
  </div>
</div>

<footer class="main-footer">
  <div class="footer-top"><img src="../ASSETS/LOGO.png" class="footer-logo" alt="Aksi Kita"><h3>Aksi Kita</h3></div>
  <div class="footer-content">
    <div class="footer-col"><p>Jl. Bachireng No. 12, Indonesia</p><p>0821 6888 9060</p><p>info@aksikita.id</p></div>
    <div class="footer-col">
      <a href="#">Unit Layanan Terpadu</a>
      <a href="#">Cara Kerja</a>
      <a href="#">FAQ</a>
      <a href="#">Aturan Penggunaan</a>
    </div>

    <div class="footer-col">
      <a href="#">Kelola Laporan</a>
      <a href="#">Statistika</a>
      <a href="#">Info Penting</a>
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
  <div class="footer-bottom">© 2026 AksiKita. Semua Hak Dilindungi.</div>
</footer>

<!-- LIGHTBOX MODAL -->
<div id="imageLightbox" class="lightbox-overlay" onclick="closeLightbox(event)">
  <button class="lightbox-close" onclick="closeLightbox(event)">✕</button>
  <img class="lightbox-content" id="lightboxImage" alt="Zoomed Image" onclick="event.stopPropagation()">
</div>

<?php include 'profile_modal_admin.php'; ?>

<script>
const ID = <?= $id ?>;
// Notification
const nb = document.getElementById('notiBellBtn'), nd = document.getElementById('notiDropdown');
if(nb&&nd){nb.addEventListener('click',e=>{e.stopPropagation();nd.style.display=nd.style.display==='block'?'none':'block';});document.addEventListener('click',e=>{if(!nd.contains(e.target)&&!nb.contains(e.target))nd.style.display='none';});}
// Upload modal
function bukaUpload(){document.getElementById('ovUpload').classList.add('open');}
function tutupUpload(){document.getElementById('ovUpload').classList.remove('open');}
document.querySelectorAll('.overlay').forEach(el=>el.addEventListener('click',function(e){if(e.target===this)this.classList.remove('open');}));
// Proses laporan
function handleProses(){
  const btn=document.getElementById('btnProses');
  if(btn){btn.innerHTML='<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';btn.disabled=true;}
  fetch('update_status.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+ID+'&status=diproses'})
  .then(r=>r.json()).then(d=>{
    if(d.success){window.location.href='detail_laporan.php?id='+ID+'&tab=proses';}
    else{if(btn){btn.innerHTML='<i class="fa-solid fa-play"></i> Proses Laporan';btn.disabled=false;}alert('Gagal: '+(d.error||'Coba lagi'));}
  }).catch(()=>{if(btn){btn.innerHTML='<i class="fa-solid fa-play"></i> Proses Laporan';btn.disabled=false;}alert('Kesalahan jaringan.');});
}
// Upload bukti
document.getElementById('formBukti').addEventListener('submit',function(e){
  e.preventDefault();
  const btn=this.querySelector('button[type=submit]');
  btn.innerHTML='<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';btn.disabled=true;
  fetch('upload_bukti.php',{method:'POST',body:new FormData(this)})
  .then(r=>r.json()).then(d=>{
    if(d.success){window.location.href='detail_laporan.php?id='+ID+'&tab=selesai';}
    else{btn.innerHTML='<i class="fa-solid fa-flag-checkered"></i> Tandai Selesai';btn.disabled=false;alert('Gagal: '+(d.error||'Coba lagi'));}
  }).catch(()=>{btn.innerHTML='<i class="fa-solid fa-flag-checkered"></i> Tandai Selesai';btn.disabled=false;alert('Kesalahan jaringan.');});
});

// Lightbox functions
function openLightbox(src){
  const lb=document.getElementById('imageLightbox');
  const img=document.getElementById('lightboxImage');
  img.src=src;
  lb.style.display='flex';
  setTimeout(()=>lb.classList.add('open'),10);
}
function closeLightbox(e){
  const lb=document.getElementById('imageLightbox');
  lb.classList.remove('open');
  setTimeout(()=>lb.style.display='none',300);
}
</script>
</body></html>
