<?php
require_once '../db.php';

// Guard: hanya user yang sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: MASUK.php");
    exit;
}

$id_user   = (int)$_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Pengguna');


// Filter laporan
$filter = $_GET['filter'] ?? 'saya';

if ($filter === 'saya') {
    // 1. Tampilkan hanya laporan milik user yang sedang login
    $sql  = "SELECT l.*, k.nama_kategori FROM laporan l
             LEFT JOIN kategori k ON k.id_kategori = l.id_kategori
             WHERE l.id_user = ? ORDER BY l.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_user);

} elseif ($filter === 'kecamatan') {
    // 2. Tampilkan laporan yang berada di kecamatan domisili user
    // Ambil data kecamatan user dari session yang tadi kita buat di file login
    $user_kecamatan = $_SESSION['kecamatan'] ?? ''; 
    
    $sql  = "SELECT l.*, k.nama_kategori FROM laporan l
             LEFT JOIN kategori k ON k.id_kategori = l.id_kategori
             WHERE l.kecamatan = ? ORDER BY l.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $user_kecamatan);

} else {
    // 3. Tampilkan seluruh laporan (filter=medan atau default lainnya)
    $sql  = "SELECT l.*, k.nama_kategori FROM laporan l
             LEFT JOIN kategori k ON k.id_kategori = l.id_kategori
             ORDER BY l.created_at DESC";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$laporan = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$slabel = ['pending' => 'Menunggu', 'diproses' => 'Sedang Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'];
$sclass = ['pending' => 'status-pending', 'diproses' => 'status-progress', 'selesai' => 'status-complete', 'ditolak' => 'status-rejected'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Laporan Saya – Aksi Kita</title>
<meta name="description" content="Daftar laporan yang telah Anda buat di Aksi Kita."/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;background:linear-gradient(180deg,#fff,#d2f6ff);min-height:100vh;}

/* ── NAVBAR (Logo + Notif + User) ── */
/* ================= NAVBAR ================= */

.navbar{
    background:#1e3d8f;
    color:white;
    display:flex;
    align-items:center;
    padding:13px 50px;
    position:fixed;
    top:0;
    left:0;
    right:0;
    z-index:1000;
    flex-wrap:wrap;
}

.logo img{
    height:55px;
}

.main-nav{
    display:flex;
    gap:15px;
    margin-left:60px;
    flex-wrap:wrap;
}

.main-nav a{
    color:white;
    margin:0 15px;
    padding:5px 0;
    text-decoration:none;
    transition:.3s;
    font-size:16px;
}

.main-nav a:hover,
.main-nav a.active{
    text-decoration:underline;
}

.nav-right{
    display:flex;
    align-items:center;
    margin-left:auto;
    gap:12px;
}


.nav-user{
    display:flex;
    align-items:center;
    gap:10px;
    cursor:pointer;
}

.nav-user span{
    color:#fff;
    font-size:16px;
    
}



.user-img{
    width:48px;
    height:48px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid rgba(255,255,255,.4);
}
/* Notification Bell */
.noti-wrap{position:relative;display:flex;align-items:center;}
.noti-btn{cursor:pointer;position:relative;width:38px;height:38px;border-radius:50%;
  background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;transition:.2s;}
.noti-btn:hover{background:rgba(255,255,255,.22);}
.noti-badge{position:absolute;top:-3px;right:-3px;background:#e63946;color:#fff;border-radius:50%;
  width:18px;height:18px;display:flex;align-items:center;justify-content:center;
  font-size:10px;font-weight:700;border:2px solid #1e3d8f;}
.noti-dropdown{position:absolute;top:50px;right:0;background:#fff;border-radius:12px;
  box-shadow:0 10px 30px rgba(15,23,42,.15);width:300px;border:1px solid #e2e8f0;
  display:none;z-index:9999;overflow:hidden;font-family:'Poppins',sans-serif;}

/* ── PAGE ── */
.page-wrap{
    max-width:1300px;
    margin:120px auto 60px;
    padding:0 40px;
}
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;}
.page-header h1{font-size:24px;color:#1e3d8f;font-weight:700;}

/* Filter */
.laporan-filter{display:flex;justify-content:center;gap:10px;background:#f5ead5;
  padding:8px;border-radius:40px;border:1.5px solid #1e3d8f;
  box-shadow:0 4px 12px rgba(0,0,0,.08);margin-bottom:32px;flex-wrap:wrap;}
.filter-btn{padding:8px 24px;font-size:14px;border-radius:40px;border:none;cursor:pointer;
  font-weight:500;font-family:'Poppins',sans-serif;transition:.2s;color:#4a4a4a;
  background:#ddc9a0;text-decoration:none;}
.filter-btn.active,.filter-btn:hover{background:#1e3d8f;color:#fff;font-weight:600;
  box-shadow:0 4px 12px rgba(0,0,0,.15);}

/* Grid 3-kolom */
.laporan-list{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
.laporan-card{background:#fff;border-radius:14px;
  box-shadow:0 6px 20px rgba(30,61,143,.12);overflow:hidden;transition:.25s;}
.laporan-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(30,61,143,.18);}
.card-img{position:relative;height:185px;overflow:hidden;}
.card-img img{width:100%;height:100%;object-fit:cover;display:block;transition:.3s;}
.laporan-card:hover .card-img img{transform:scale(1.04);}
.card-img h3{position:absolute;top:10px;left:10px;right:10px;
  background:rgba(0,0,0,.60);color:#fff;padding:5px 10px;border-radius:6px;font-size:13px;margin:0;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.card-body{padding:13px 14px 0;}
.loc{display:flex;align-items:flex-start;gap:6px;font-size:13px;color:#555;line-height:1.5;}
.loc i{color:#e63946;font-size:14px;margin-top:2px;flex-shrink:0;}
.card-foot{display:flex;justify-content:space-between;align-items:center;
  margin-top:12px;padding:11px 14px;border-top:1px solid #f0f0f0;}
.status-badge{padding:6px 12px;border-radius:20px;font-weight:600;font-size:11px;text-transform:uppercase;white-space:nowrap;}
.status-pending{background:#fff3cd;color:#856404;}
.status-progress{background:#ffecb3;color:#e65100;}
.status-complete{background:#d1fae5;color:#166534;}
.status-rejected{background:#fee2e2;color:#991b1b;}
.btn-detail{color:#1e3d8f;text-decoration:none;font-weight:600;font-size:13px;
  padding:6px 14px;border-radius:8px;border:1.5px solid #1e3d8f;transition:.2s;white-space:nowrap;}
.btn-detail:hover{background:#1e3d8f;color:#fff;}

/* Empty State */
.empty-state{text-align:center;padding:60px 20px;grid-column:1/-1;}
.empty-state .ico{font-size:56px;color:#c0cde8;margin-bottom:16px;}
.empty-state h3{color:#1e3d8f;font-size:20px;margin-bottom:8px;}
.empty-state p{color:#888;font-size:14px;margin-bottom:24px;}
.empty-state a{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;
  background:linear-gradient(135deg,#1e3d8f,#2b5fc4);color:#fff;border-radius:10px;
  text-decoration:none;font-weight:600;font-size:14px;}
.empty-state a:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(30,61,143,.4);}

/* FAB */
.fab-btn{position:fixed;bottom:30px;right:30px;
  background:linear-gradient(135deg,#10b981,#059669);color:white;
  padding:14px 24px;border-radius:50px;
  box-shadow:0 8px 24px rgba(16,185,129,.4);
  text-decoration:none;display:flex;align-items:center;gap:8px;
  font-weight:600;font-size:15px;z-index:9999;
  transition:all .3s cubic-bezier(.4,0,.2,1);}
.fab-btn:hover{transform:scale(1.05) translateY(-3px);box-shadow:0 12px 30px rgba(16,185,129,.5);color:#fff;}

/* Footer */
.main-footer{background:linear-gradient(165deg,#080e18,#102647 70%,#9c7719);
  color:#fff;padding:50px 70px 30px;margin-top:60px;}
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
@media(max-width:900px){.laporan-list{grid-template-columns:repeat(2,1fr);}}
@media(max-width:580px){.laporan-list{grid-template-columns:1fr;}}
@media(max-width:700px){.navbar{padding:12px 16px;}.main-footer{padding:36px 20px 20px;}}
</style>
</head>
<body>

<header class="navbar">
  <div class="logo"><img src="../ASSETS/LOGO.png" alt="Aksi Kita"></div>
  <nav class="main-nav">
      <a href="BERANDA2.php">Beranda</a>
      <a href="laporan_saya.php" class="active">Laporan</a>
      <a href="peringkat.php">Peringkat</a>
      <a href="TENTANG.php">Tentang</a>
  </nav>
  <div class="nav-right">
    <div class="noti-wrap">
      <div class="noti-btn" id="notiBellBtn">
        <i class="fa-solid fa-bell" style="color:#fff;font-size:16px;"></i>
        <?php if ($noti_count > 0): ?>
          <span class="noti-badge"><?= $noti_count ?></span>
        <?php endif; ?>
      </div>
      <div class="noti-dropdown" id="notiDropdown">
        <div style="padding:12px 16px;background:#f8fafc;border-bottom:1px solid #e2e8f0;font-weight:700;font-size:13.5px;color:#1e3d8f;display:flex;justify-content:space-between;align-items:center;">
          <span>Notifikasi</span>
          <?php if ($noti_count > 0): ?><span style="background:#dce8ff;color:#1e3d8f;font-size:10.5px;padding:2px 6px;border-radius:10px;font-weight:600;"><?= $noti_count ?> Baru</span><?php endif; ?>
        </div>
        <div style="max-height:280px;overflow-y:auto;">
          <?php if (empty($noti_items)): ?>
            <div style="padding:24px;text-align:center;color:#64748b;font-size:13px;">
              <i class="fa-solid fa-bell-slash" style="font-size:24px;color:#cbd5e1;margin-bottom:8px;display:block;"></i>Belum ada notifikasi.
            </div>
          <?php else: foreach ($noti_items as $item): ?>
            <a href="<?= $item['link'] ?>" style="display:block;padding:12px 16px;border-bottom:1px solid #f1f5f9;text-decoration:none;color:inherit;transition:.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
              <span style="font-size:12.5px;font-weight:700;color:#1e293b;display:block;margin-bottom:2px;"><?= $item['title'] ?></span>
              <span style="font-size:11.5px;color:#64748b;display:block;line-height:1.4;margin-bottom:4px;"><?= $item['desc'] ?></span>
              <span style="font-size:10px;color:#94a3b8;display:block;"><i class="fa-regular fa-clock" style="margin-right:4px;"></i><?= $item['time'] ?></span>
            </a>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
    <div class="nav-user" id="navUser">
      <span><?= $user_name ?></span>
      <img src="../ASSETS/USER.png" class="user-img" alt="User"/>
    </div>
  </div>
</header>

<main class="page-wrap">
  <div class="page-header">
    <h1><i class="fa-solid fa-file-alt"></i> Laporan Saya</h1>
  </div>

  <div class="laporan-filter">
    <a href="laporan_saya.php?filter=saya"      class="filter-btn <?= $filter==='saya' ?'active':'' ?>">Laporan Saya</a>
    <a href="laporan_saya.php?filter=kecamatan" class="filter-btn <?= $filter==='kecamatan'?'active':'' ?>">Kecamatan Saya</a>
    <a href="laporan_saya.php?filter=medan"     class="filter-btn <?= $filter==='medan'?'active':'' ?>">Seluruh Kota Medan</a>
  </div>

  <div class="laporan-list">
    <?php if (empty($laporan)): ?>
    <div class="empty-state">
      <div class="ico"><i class="fa-solid fa-folder-open"></i></div>
      
      <?php if ($filter === 'saya'): ?>
        <h3>Belum Ada Laporan</h3>
        <p>Anda belum membuat laporan. Laporkan masalah fasilitas umum sekarang!</p>
        <a href="laporkan.php"><i class="fa-solid fa-plus"></i> Buat Laporan Baru</a>
        
      <?php elseif ($filter === 'kecamatan'): ?>
        <h3>Kecamatan Aman & Terkendali</h3>
        <p>Belum ada laporan kerusakan fasilitas umum di sekitar <b><?= htmlspecialchars($_SESSION['kecamatan'] ?? 'kecamatan Anda') ?></b>.</p>
        
      <?php else: ?>
        <h3>Belum Ada Laporan</h3>
        <p>Saat ini belum ada data laporan dari seluruh masyarakat Kota Medan.</p>
      <?php endif; ?>
      
    </div>
    <?php else: foreach ($laporan as $r):
      $foto = '../ASSETS/rusak 1.jpeg';

if (!empty($r['foto_awal'])) {
    $path = '../' . $r['foto_awal'];

    if (file_exists($path)) {
        $foto = htmlspecialchars($path);
    }
}
      $st   = $r['status'];
    ?>
    <div class="laporan-card">
      <div class="card-img">
        <img src="<?= $foto ?>" alt="<?= htmlspecialchars($r['judul']) ?>"/>
        <h3><?= htmlspecialchars($r['judul']) ?></h3>
      </div>
      <div class="card-body">
        <p class="loc"><i class="fa-solid fa-location-dot"></i><?= htmlspecialchars($r['lokasi'] ?: '-') ?></p>
        <div class="card-foot">
          <span class="status-badge <?= $sclass[$st] ?? 'status-pending' ?>"><?= $slabel[$st] ?? $st ?></span>
          <a href="user_detail_laporan.php?id=<?= $r['id_laporan'] ?>" class="btn-detail">Selengkapnya</a>
        </div>
      </div>
    </div>
    <?php endforeach; endif; ?>
  </div>
</main>

<a href="laporkan.php" class="fab-btn" title="Buat Laporan Baru">
  <i class="fa-solid fa-plus"></i>
  <span>Buat Laporan</span>
</a>

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

<?php include 'profile_modal.php'; ?>

<script>
// Notification dropdown toggle
const notiBellBtn = document.getElementById('notiBellBtn');
const notiDropdown = document.getElementById('notiDropdown');
if (notiBellBtn && notiDropdown) {
  notiBellBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    notiDropdown.style.display = notiDropdown.style.display === 'block' ? 'none' : 'block';
  });
  document.addEventListener('click', function(e) {
    if (!notiDropdown.contains(e.target) && !notiBellBtn.contains(e.target)) {
      notiDropdown.style.display = 'none';
    }
  });
}
</script>

</body>
</html>