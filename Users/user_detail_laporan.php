<?php
require_once '../db.php';

if (!isset($_SESSION['user_id'])) { header("Location: MASUK.php"); exit; }

$id_user   = (int)$_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Pengguna');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: laporan_saya.php"); exit; }

$stmt = $conn->prepare("SELECT l.*, k.nama_kategori, u.nama_lengkap FROM laporan l
    LEFT JOIN kategori k ON k.id_kategori = l.id_kategori
    LEFT JOIN users u ON u.id_user = l.id_user
    WHERE l.id_laporan = ?");
$stmt->bind_param('i', $id); $stmt->execute();
$lap = $stmt->get_result()->fetch_assoc(); $stmt->close();

if (!$lap) { echo "<p style='padding:40px;font-family:Poppins,sans-serif;color:red'>Laporan tidak ditemukan.</p>"; exit; }

$sl = $lap['status'];
$foto = (!empty($lap['foto_awal']) && file_exists('../' . $lap['foto_awal'])) ? htmlspecialchars('../' . $lap['foto_awal']) : '../ASSETS/rusak 1.jpeg';
$foto_bukti = (!empty($lap['foto_bukti']) && file_exists('../' . $lap['foto_bukti'])) ? htmlspecialchars('../' . $lap['foto_bukti']) : '../ASSETS/verifikasicepat.jpg';
$slabel = ['pending'=>'Menunggu Verifikasi','diproses'=>'Sedang Diproses','selesai'=>'Selesai','ditolak'=>'Ditolak'];
?>
<!DOCTYPE html><html lang="id"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title><?= htmlspecialchars($lap['judul']) ?> – Status Laporan</title>
<meta name="description" content="Status laporan Anda di Aksi Kita."/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;background:#f4f7ff;min-height:100vh;}
.navbar{background:#1e3d8f;color:#fff;display:flex;align-items:center;padding:13px 50px;position:fixed;top:0;left:0;right:0;z-index:1000;gap:10px;box-shadow:0 4px 16px rgba(0,0,0,.2);}
.logo img{height:50px;}
.nav-right{display:flex;align-items:center;gap:14px;margin-left:auto;}
.nav-user{display:flex;align-items:center;gap:10px;cursor:pointer;}
.nav-user span{color:#fff;font-size:15px;font-weight:500;}
.nav-user img{height:36px;width:36px;border-radius:50%;border:2px solid rgba(255,255,255,.4);}
.noti-wrap{position:relative;display:flex;align-items:center;}
.noti-btn{cursor:pointer;width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;transition:.2s;}
.noti-btn:hover{background:rgba(255,255,255,.22);}
.noti-badge{position:absolute;top:-3px;right:-3px;background:#e63946;color:#fff;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;border:2px solid #1e3d8f;}
.noti-dropdown{position:absolute;top:50px;right:0;background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(15,23,42,.15);width:300px;border:1px solid #e2e8f0;display:none;z-index:9999;overflow:hidden;}
.hero-mini{background:linear-gradient(135deg,#1e3d8f,#2b5fc4 60%,#3d74db);margin-top:76px;padding:55px 50px 45px;text-align:center;position:relative;overflow:hidden;}
.hero-mini::before{content:'';position:absolute;inset:0;background:url('../ASSETS/Main Background.jpg') center/cover no-repeat;opacity:.12;}
.hero-mini h1{position:relative;color:#fff;font-size:32px;font-weight:700;margin-bottom:10px;}
.hero-mini .badge{display:inline-block;padding:6px 18px;border-radius:20px;font-size:13px;font-weight:700;margin-top:8px;}
.badge-pending{background:#fef3c7;color:#92400e;}.badge-diproses{background:#dbeafe;color:#1e40af;}.badge-selesai{background:#d1fae5;color:#166534;}.badge-ditolak{background:#fee2e2;color:#991b1b;}
.breadcrumb{position:relative;margin-top:14px;display:flex;justify-content:center;gap:8px;font-size:13px;color:rgba(255,255,255,.7);}
.breadcrumb a{color:rgba(255,255,255,.85);text-decoration:none;}.breadcrumb a:hover{color:#fff;}
.wrap{max-width:860px;margin:0 auto;padding:40px 22px 80px;}
/* STEPS */
.steps-wrap{margin-bottom:32px;}
.steps{display:flex;background:#fff;border-radius:50px;box-shadow:0 4px 18px rgba(30,61,143,.1);overflow:hidden;border:1px solid #e8edf5;}
.step{flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:14px 10px;font-size:13.5px;font-weight:600;color:#9ca3af;position:relative;text-decoration:none;}
.step:not(:last-child)::after{content:'';position:absolute;right:0;top:20%;height:60%;width:1px;background:#e5e7eb;}
.step .num{width:24px;height:24px;border-radius:50%;background:#f3f4f6;color:#9ca3af;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;}
.step.done{color:#10b981;}.step.done .num{background:#d1fae5;color:#10b981;}
.step.active{color:#1e3d8f;font-weight:700;}.step.active .num{background:#dce8ff;color:#1e3d8f;}
.step.disabled{opacity:.5;}
/* BANNERS */
.banner{display:flex;align-items:center;gap:16px;padding:18px 24px;border-radius:14px;margin-bottom:24px;border:1px solid transparent;}
.banner .tbi{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff;flex-shrink:0;}
.banner h3{font-size:16px;margin-bottom:3px;}.banner p{font-size:13px;line-height:1.5;}
.banner-pending{background:#fef9e7;border-color:#fcd34d;}.banner-pending h3{color:#92400e;}.banner-pending p{color:#b45309;}.banner-pending .tbi{background:linear-gradient(135deg,#f59e0b,#fbbf24);}
.banner-diproses{background:#eff6ff;border-color:#93c5fd;}.banner-diproses h3{color:#1e3d8f;}.banner-diproses p{color:#2b5fc4;}.banner-diproses .tbi{background:linear-gradient(135deg,#2b7be5,#3b82f6);}
.banner-selesai{background:#f0fdf4;border-color:#6ee7b7;}.banner-selesai h3{color:#166534;}.banner-selesai p{color:#166534;}.banner-selesai .tbi{background:linear-gradient(135deg,#059669,#10b981);}
.banner-ditolak{background:#fef2f2;border-color:#fca5a5;}.banner-ditolak h3{color:#991b1b;}.banner-ditolak p{color:#b91c1c;}.banner-ditolak .tbi{background:linear-gradient(135deg,#dc2626,#ef4444);}
/* MAIN CARD */
.main-card{background:#fff;border-radius:18px;box-shadow:0 8px 28px rgba(30,61,143,.1);overflow:hidden;display:flex;margin-bottom:24px;}
.foto-side{width:280px;flex-shrink:0;position:relative;overflow:hidden;}
.foto-side img{width:100%;height:100%;object-fit:cover;display:block;}
.foto-overlay{position:absolute;bottom:10px;left:10px;}
.detail-side{flex:1;padding:28px 30px;}
.d-row{display:flex;gap:14px;margin-bottom:16px;}
.d-ico{width:36px;height:36px;background:#eef2ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.d-ico i{color:#1e3d8f;font-size:14px;}
.d-txt label{display:block;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;margin-bottom:3px;}
.d-txt span{font-size:14px;color:#1e293b;line-height:1.5;}
hr.divider{border:none;border-top:1px solid #f1f5f9;margin:8px 0 16px;}
/* SELESAI CARD */
.selesai-card{background:#fff;border-radius:18px;box-shadow:0 8px 28px rgba(30,61,143,.1);overflow:hidden;margin-bottom:24px;}
.selesai-head{background:linear-gradient(135deg,#059669,#10b981);padding:22px 28px;display:flex;align-items:center;gap:16px;}
.selesai-head .ico{width:48px;height:48px;background:rgba(255,255,255,.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;}
.selesai-head h2{color:#fff;font-size:18px;}.selesai-head p{color:rgba(255,255,255,.8);font-size:13px;}
.selesai-body{display:flex;gap:24px;padding:28px;}
.bukti-foto{width:220px;flex-shrink:0;border-radius:12px;overflow:hidden;}
.bukti-foto img{width:100%;height:170px;object-fit:cover;display:block;}
.bukti-info h4{font-size:15px;color:#1e3d8f;margin-bottom:10px;display:flex;align-items:center;gap:8px;}
.bukti-info p{font-size:13.5px;color:#444;line-height:1.6;margin-bottom:14px;}
.bukti-meta .bm{display:flex;align-items:center;gap:8px;font-size:13px;color:#555;margin-bottom:6px;}
.bukti-meta .bm i{color:#1e3d8f;font-size:12px;}
/* ACTION BAR */
.action-bar{display:flex;gap:14px;margin-top:8px;}
.btn{display:inline-flex;align-items:center;gap:8px;padding:12px 22px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;transition:.2s;cursor:pointer;border:none;}
.btn-ghost{background:#f1f5f9;color:#475569;border:1.5px solid #e2e8f0;}.btn-ghost:hover{background:#e2e8f0;}
/* POPUP */
.overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9998;display:none;align-items:center;justify-content:center;}
.overlay.open{display:flex;}
.pop-card{background:#fff;border-radius:20px;padding:40px 30px;max-width:360px;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.3);}
/* FOOTER */
.main-footer{background:linear-gradient(165deg,#080e18,#102647 70%,#9c7719);color:#fff;padding:50px 70px 30px;}
.footer-top{display:flex;align-items:center;gap:12px;margin-bottom:28px;}
.footer-logo{height:48px;}
.footer-content{display:flex;justify-content:space-between;flex-wrap:wrap;gap:20px;margin-bottom:24px;}
.footer-col p,.footer-col a{display:block;margin:5px 0;color:#ccc;font-size:14px;text-decoration:none;}
.footer-social{display:flex;gap:12px;margin-bottom:20px;}
.footer-social a{width:38px;height:38px;border-radius:8px;background:#fff;color:#000;display:inline-flex;align-items:center;justify-content:center;font-size:16px;transition:.3s;}
.footer-social a:hover{transform:translateY(-4px);}
.footer-bottom{text-align:center;font-size:13px;color:#888;border-top:1px solid rgba(255,255,255,.1);padding-top:16px;}
@media(max-width:700px){.navbar{padding:12px 16px;}.main-card{flex-direction:column;}.foto-side{width:100%;height:200px;}.selesai-body{flex-direction:column;}.bukti-foto{width:100%;}.hero-mini{padding:50px 20px 36px;}}

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

<header class="navbar">
  <div class="logo"><img src="../ASSETS/LOGO.png" alt="Aksi Kita"></div>
  <div class="nav-right">
    <div class="noti-wrap">
      <div class="noti-btn" id="notiBellBtn">
        <i class="fa-solid fa-bell" style="color:#fff;font-size:16px;"></i>
        <?php if ($noti_count > 0): ?><span class="noti-badge"><?= $noti_count ?></span><?php endif; ?>
      </div>
      <div class="noti-dropdown" id="notiDropdown">
        <div style="padding:12px 16px;background:#f8fafc;border-bottom:1px solid #e2e8f0;font-weight:700;font-size:13px;color:#1e3d8f;">Notifikasi</div>
        <div style="max-height:280px;overflow-y:auto;">
          <?php if (empty($noti_items)): ?>
            <div style="padding:24px;text-align:center;color:#64748b;font-size:13px;">Belum ada notifikasi.</div>
          <?php else: foreach ($noti_items as $item): ?>
            <a href="<?= $item['link'] ?>" style="display:block;padding:12px 16px;border-bottom:1px solid #f1f5f9;text-decoration:none;color:inherit;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
              <span style="font-size:12.5px;font-weight:700;color:#1e293b;display:block;"><?= $item['title'] ?></span>
              <span style="font-size:11.5px;color:#64748b;display:block;"><?= $item['desc'] ?></span>
            </a>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
    <div class="nav-user" id="navUser"><span><?= $user_name ?></span><img src="../ASSETS/USER.png" alt="User"/></div>
  </div>
</header>

<section class="hero-mini">
  <h1><i class="fa-solid fa-file-invoice"></i> Status Laporan Anda</h1>
  <p><span class="badge badge-<?= $sl ?>"><?= $slabel[$sl] ?? $sl ?></span></p>
  <div class="breadcrumb"><a href="BERANDA2.php">Beranda</a><span>/</span><a href="laporan_saya.php">Laporan Saya</a><span>/</span><span>Detail Status</span></div>
</section>

<?php if (isset($_GET['baru'])): ?>
<div class="overlay open" id="ovBaru">
  <div class="pop-card">
    <div style="width:64px;height:64px;background:linear-gradient(135deg,#1e3d8f,#2b5fc4);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:26px;color:#fff;margin:0 auto 16px"><i class="fa-solid fa-check"></i></div>
    <h2 style="color:#1e3d8f;font-size:19px;margin-bottom:8px">Laporan Terkirim!</h2>
    <p style="color:#555;font-size:14px;margin-bottom:20px">Laporan Anda sedang menunggu verifikasi oleh tim pengelola.</p>
    <button class="btn btn-ghost" style="width:100%;justify-content:center;" onclick="document.getElementById('ovBaru').classList.remove('open')"><i class="fa-solid fa-eye"></i> Lihat Status</button>
  </div>
</div>
<?php endif; ?>

<main class="wrap">
  <!-- STEPS -->
  <div class="steps-wrap">
    <div class="steps">
      <div class="step done"><div class="num"><i class="fa-solid fa-check" style="font-size:9px"></i></div> Isi Laporan</div>
      <div class="step <?= ($sl==='selesai')?'done':(($sl==='pending'||$sl==='diproses'||$sl==='ditolak')?'active':'disabled') ?>">
        <div class="num"><?= ($sl==='selesai')?'<i class="fa-solid fa-check" style="font-size:9px"></i>':'2' ?></div> Detail Laporan
      </div>
      <div class="step <?= ($sl==='selesai')?'active':'disabled' ?>">
        <div class="num"><?= ($sl==='selesai')?'<i class="fa-solid fa-check" style="font-size:9px"></i>':'3' ?></div> Selesai
      </div>
    </div>
  </div>

  <!-- STATUS BANNER -->
  <?php if ($sl === 'selesai'): ?>
    <div class="banner banner-selesai"><div class="tbi"><i class="fa-solid fa-circle-check"></i></div><div><h3>Laporan Selesai! 🎉</h3><p>Laporan Anda telah ditindaklanjuti. Lihat riwayat perbaikan di bawah.</p></div></div>
  <?php elseif ($sl === 'diproses'): ?>
    <div class="banner banner-diproses"><div class="tbi"><i class="fa-solid fa-hourglass-half"></i></div><div><h3>Sedang Diproses</h3><p>Laporan Anda sedang ditindaklanjuti oleh instansi terkait. Terima kasih atas partisipasi Anda!</p></div></div>
  <?php elseif ($sl === 'ditolak'): ?>
    <div class="banner banner-ditolak"><div class="tbi"><i class="fa-solid fa-circle-xmark"></i></div><div><h3>Laporan Ditolak</h3><p>Laporan ini ditolak karena tidak memenuhi kriteria atau data tidak akurat.</p></div></div>
  <?php else: ?>
    <div class="banner banner-pending"><div class="tbi"><i class="fa-solid fa-envelope-open-text"></i></div><div><h3>Menunggu Verifikasi</h3><p>Laporan Anda sudah masuk dan sedang mengantre untuk diverifikasi oleh admin.</p></div></div>
  <?php endif; ?>

  <!-- DETAIL CARD -->
  <div class="main-card" id="detail-card">
    <div class="foto-side"><img src="<?= $foto ?>" alt="<?= htmlspecialchars($lap['judul']) ?>" style="cursor: pointer;" onclick="openLightbox(this.src)"/><div class="foto-overlay"><span class="badge badge-<?= $sl ?>"><?= $slabel[$sl] ?? $sl ?></span></div></div>
    <div class="detail-side">
      <div class="d-row"><div class="d-ico"><i class="fa-solid fa-tag"></i></div><div class="d-txt"><label>Kategori</label><span><?= htmlspecialchars($lap['nama_kategori'] ?? '-') ?><?= (!empty($lap['kategori_lainnya'] ?? '')) ? ' – '.htmlspecialchars($lap['kategori_lainnya']) : '' ?></span></div></div>
      <div class="d-row"><div class="d-ico"><i class="fa-solid fa-location-dot"></i></div><div class="d-txt"><label>Lokasi</label><span><?= htmlspecialchars($lap['lokasi'] ?? '-') ?></span></div></div>
      <div class="d-row"><div class="d-ico"><i class="fa-solid fa-calendar-days"></i></div><div class="d-txt"><label>Tanggal Kejadian</label><span><?= $lap['tanggal_kejadian'] ? date('d F Y', strtotime($lap['tanggal_kejadian'])) : '-' ?></span></div></div>
      <div class="d-row"><div class="d-ico"><i class="fa-solid fa-building"></i></div><div class="d-txt"><label>Instansi Tujuan</label><span><?= htmlspecialchars($lap['instansi_tujuan'] ?: '-') ?></span></div></div>
      <div class="d-row"><div class="d-ico"><i class="fa-solid fa-clock"></i></div><div class="d-txt"><label>Waktu Laporan</label><span><?= date('d F Y, H:i', strtotime($lap['created_at'])) ?> WIB</span></div></div>
      <hr class="divider"/>
      <div class="d-row"><div class="d-ico"><i class="fa-solid fa-align-left"></i></div><div class="d-txt"><label>Deskripsi</label><span><?= nl2br(htmlspecialchars($lap['isi_laporan'] ?: '-')) ?></span></div></div>
    </div>
  </div>

  <?php if ($sl === 'selesai'): ?>
  <div class="selesai-card" id="selesai-card">
    <div class="selesai-head"><div class="ico"><i class="fa-solid fa-flag-checkered"></i></div><div><h2>Riwayat Bukti Perbaikan</h2><p>Selesai pada: <?= $lap['updated_at'] ? date('d F Y, H:i', strtotime($lap['updated_at'])).' WIB' : '-' ?></p></div></div>
    <div class="selesai-body">
      <div class="bukti-foto"><img src="<?= $foto_bukti ?>" alt="Bukti Perbaikan" style="cursor: pointer;" onclick="openLightbox(this.src)"/></div>
      <div class="bukti-info">
        <h4><i class="fa-solid fa-clipboard-check"></i> Keterangan Perbaikan</h4>
        <p><?= nl2br(htmlspecialchars($lap['bukti_deskripsi'] ?: 'Tidak ada keterangan tambahan.')) ?></p>
        <div class="bukti-meta">
          <div class="bm"><i class="fa-solid fa-check-circle"></i>Status: <strong>Selesai</strong></div>
          <div class="bm"><i class="fa-solid fa-user-check"></i>Pelapor: <?= htmlspecialchars($lap['nama_lengkap'] ?? 'Pengguna') ?></div>
          <div class="bm"><i class="fa-solid fa-calendar-check"></i>Dibuat: <?= date('d F Y', strtotime($lap['created_at'])) ?></div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div class="action-bar">
    <a href="laporan_saya.php" class="btn btn-ghost"><i class="fa-solid fa-list"></i> Kembali ke Daftar Laporan</a>
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

<!-- LIGHTBOX MODAL -->
<div id="imageLightbox" class="lightbox-overlay" onclick="closeLightbox(event)">
  <button class="lightbox-close" onclick="closeLightbox(event)">✕</button>
  <img class="lightbox-content" id="lightboxImage" alt="Zoomed Image" onclick="event.stopPropagation()">
</div>

<?php include 'profile_modal.php'; ?>
<script>
const notiBellBtn=document.getElementById('notiBellBtn'),notiDropdown=document.getElementById('notiDropdown');
if(notiBellBtn&&notiDropdown){
  notiBellBtn.addEventListener('click',e=>{e.stopPropagation();notiDropdown.style.display=notiDropdown.style.display==='block'?'none':'block';});
  document.addEventListener('click',e=>{if(!notiDropdown.contains(e.target)&&!notiBellBtn.contains(e.target))notiDropdown.style.display='none';});
}

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
