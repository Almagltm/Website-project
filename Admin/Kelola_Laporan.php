<?php
/**
 * Admin/Kelola_Laporan.php
 * Daftar semua laporan untuk dikelola admin
 */
require_once '../db.php';

if (!isset($_SESSION['admin_id'])) { header("Location: Login.php"); exit; }

$id_admin   = (int)$_SESSION['admin_id'];
$admin_name = htmlspecialchars($_SESSION['nama_admin'] ?? 'Admin');

// Filter & search
$filter = $_GET['filter'] ?? 'semua';
$search = trim($_GET['q'] ?? '');
$valid_filters = ['semua', 'pending', 'diproses', 'selesai', 'ditolak'];
if (!in_array($filter, $valid_filters)) $filter = 'semua';

// Build query
$where  = [];
$params = [];
$types  = '';
if ($filter !== 'semua') { $where[] = "l.status = ?"; $params[] = $filter; $types .= 's'; }
if ($search !== '')      { $where[] = "(l.judul LIKE ? OR l.lokasi LIKE ? OR u.nama_lengkap LIKE ?)"; $s = "%$search%"; $params[] = $s; $params[] = $s; $params[] = $s; $types .= 'sss'; }

$sql = "SELECT l.id_laporan, l.judul, l.lokasi, l.status, l.created_at, l.updated_at,
               k.nama_kategori, u.nama_lengkap
        FROM laporan l
        LEFT JOIN kategori k ON k.id_kategori = l.id_kategori
        LEFT JOIN users u ON u.id_user = l.id_user"
       . (!empty($where) ? ' WHERE '.implode(' AND ', $where) : '')
       . " ORDER BY l.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$laporan = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Hitung per status
$counts = ['semua'=>0,'pending'=>0,'diproses'=>0,'selesai'=>0,'ditolak'=>0];
$cnt_res = $conn->query("SELECT status, COUNT(*) as c FROM laporan GROUP BY status");
if ($cnt_res) while ($r = $cnt_res->fetch_assoc()) { $counts[$r['status']] = (int)$r['c']; $counts['semua'] += (int)$r['c']; }

$slabel = ['pending'=>'Menunggu','diproses'=>'Diproses','selesai'=>'Selesai','ditolak'=>'Ditolak'];
$sclass = ['pending'=>'badge-pending','diproses'=>'badge-diproses','selesai'=>'badge-selesai','ditolak'=>'badge-ditolak'];
?>
<!DOCTYPE html><html lang="id"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Kelola Laporan – Admin Aksi Kita</title>
<meta name="description" content="Kelola semua laporan masyarakat di Aksi Kita."/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;background:#f4f7ff;min-height:100vh;}
/* NAVBAR */
.navbar{background:#1e3d8f;color:#fff;display:flex;align-items:center;padding:13px 50px;position:fixed;top:0;left:0;right:0;z-index:1000;gap:10px;box-shadow:0 4px 16px rgba(0,0,0,.2);}
.logo img{height:50px;}
.nav-right{display:flex;align-items:center;gap:14px;margin-left:auto;}
.nav-admin{display:flex;align-items:center;gap:10px;cursor:pointer;}
.nav-admin span{color:#fff;font-size:15px;font-weight:500;}
.nav-admin img{height:36px;width:36px;border-radius:50%;border:2px solid rgba(255,255,255,.4);}
.noti-wrap{position:relative;display:flex;align-items:center;}
.noti-btn{cursor:pointer;width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;transition:.2s;}
.noti-btn:hover{background:rgba(255,255,255,.22);}
.noti-badge{position:absolute;top:-3px;right:-3px;background:#e63946;color:#fff;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;border:2px solid #1e3d8f;}
.noti-dropdown{position:absolute;top:50px;right:0;background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(15,23,42,.15);width:300px;border:1px solid #e2e8f0;display:none;z-index:9999;overflow:hidden;}
/* PAGE */
.page-wrap{max-width:1100px;margin:100px auto 60px;padding:0 24px;}
.page-title{font-size:26px;color:#1e3d8f;font-weight:700;margin-bottom:6px;display:flex;align-items:center;gap:10px;}
.page-sub{font-size:14px;color:#64748b;margin-bottom:28px;}
/* STAT CARDS */
.stat-row{display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:28px;}
.stat-card{background:#fff;border-radius:14px;padding:18px 20px;box-shadow:0 4px 16px rgba(30,61,143,.08);border:1.5px solid transparent;text-decoration:none;transition:.25s;display:block;}
.stat-card:hover,.stat-card.active{transform:translateY(-3px);border-color:#1e3d8f;box-shadow:0 8px 24px rgba(30,61,143,.15);}
.stat-card .num{font-size:28px;font-weight:800;color:#1e3d8f;line-height:1;}
.stat-card .lbl{font-size:12px;color:#64748b;margin-top:4px;font-weight:500;}
.stat-card.active .num{color:#1e3d8f;}
/* TOOLBAR */
.toolbar{display:flex;gap:14px;margin-bottom:20px;flex-wrap:wrap;align-items:center;}
.search-box{display:flex;flex:1;min-width:220px;border:1.5px solid #e2e8f0;border-radius:10px;overflow:hidden;background:#fff;transition:.2s;}
.search-box:focus-within{border-color:#1e3d8f;box-shadow:0 0 0 3px rgba(30,61,143,.1);}
.search-box input{flex:1;padding:11px 14px;border:none;font-size:14px;font-family:'Poppins',sans-serif;background:transparent;}
.search-box input:focus{outline:none;}
.search-box button{padding:0 16px;background:#1e3d8f;border:none;color:#fff;cursor:pointer;font-size:14px;transition:.2s;}
.search-box button:hover{background:#2b5fc4;}
/* TABLE */
.table-wrap{background:#fff;border-radius:16px;box-shadow:0 6px 24px rgba(30,61,143,.09);overflow:hidden;}
table{width:100%;border-collapse:collapse;}
thead tr{background:#1e3d8f;color:#fff;}
th{padding:14px 16px;text-align:left;font-size:13px;font-weight:600;white-space:nowrap;}
tbody tr{border-bottom:1px solid #f1f5f9;transition:background .15s;}
tbody tr:hover{background:#f8faff;}
td{padding:13px 16px;font-size:13.5px;color:#374151;vertical-align:middle;}
.td-judul{font-weight:600;color:#1e293b;max-width:220px;}
.td-judul small{display:block;font-size:11.5px;color:#94a3b8;font-weight:400;margin-top:2px;}
.badge{display:inline-block;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;white-space:nowrap;}
.badge-pending{background:#fef3c7;color:#92400e;}
.badge-diproses{background:#dbeafe;color:#1e40af;}
.badge-selesai{background:#d1fae5;color:#166534;}
.badge-ditolak{background:#fee2e2;color:#991b1b;}
.btn-detail{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;background:#eef2ff;color:#1e3d8f;border:1.5px solid #c7d7f5;font-size:12.5px;font-weight:600;text-decoration:none;transition:.2s;white-space:nowrap;}
.btn-detail:hover{background:#1e3d8f;color:#fff;border-color:#1e3d8f;}
/* EMPTY */
.empty-row td{text-align:center;padding:60px 20px;color:#9ca3af;font-size:14px;}
.empty-row .ico{font-size:40px;color:#c0cde8;margin-bottom:12px;}
/* FOOTER */
.main-footer{background:linear-gradient(165deg,#080e18,#102647 70%,#9c7719);color:#fff;padding:50px 70px 30px;margin-top:60px;}
.footer-top{display:flex;align-items:center;gap:12px;margin-bottom:28px;}
.footer-logo{height:48px;}
.footer-content{display:flex;justify-content:space-between;flex-wrap:wrap;gap:20px;margin-bottom:24px;}
.footer-col p,.footer-col a{display:block;margin:5px 0;color:#ccc;font-size:14px;text-decoration:none;}
.footer-social{display:flex;gap:12px;margin-bottom:20px;}
.footer-social a{width:38px;height:38px;border-radius:8px;background:#fff;color:#000;display:inline-flex;align-items:center;justify-content:center;font-size:16px;transition:.3s;}
.footer-social a:hover{transform:translateY(-4px);}
.footer-bottom{text-align:center;font-size:13px;color:#888;border-top:1px solid rgba(255,255,255,.1);padding-top:16px;}
@media(max-width:900px){.stat-row{grid-template-columns:repeat(3,1fr);}table{font-size:12px;}th,td{padding:10px 12px;}}
@media(max-width:600px){.stat-row{grid-template-columns:repeat(2,1fr);}.navbar{padding:12px 16px;}.main-footer{padding:36px 20px 20px;}}
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
        <div style="padding:12px 16px;background:#f8fafc;border-bottom:1px solid #e2e8f0;font-weight:700;font-size:13px;color:#1e3d8f;display:flex;justify-content:space-between;align-items:center;">
          <span>Notifikasi</span>
          <?php if ($noti_count > 0): ?><span style="background:#dce8ff;color:#1e3d8f;font-size:10px;padding:2px 6px;border-radius:10px;font-weight:600;"><?= $noti_count ?> Baru</span><?php endif; ?>
        </div>
        <div style="max-height:280px;overflow-y:auto;">
          <?php if (empty($noti_items)): ?>
            <div style="padding:24px;text-align:center;color:#64748b;font-size:13px;">Belum ada notifikasi.</div>
          <?php else: foreach ($noti_items as $item): ?>
            <a href="<?= $item['link'] ?>" style="display:block;padding:12px 16px;border-bottom:1px solid #f1f5f9;text-decoration:none;color:inherit;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
              <span style="font-size:12.5px;font-weight:700;color:#1e293b;display:block;"><?= $item['title'] ?></span>
              <span style="font-size:11.5px;color:#64748b;display:block;"><?= $item['desc'] ?></span>
              <span style="font-size:10px;color:#94a3b8;"><i class="fa-regular fa-clock" style="margin-right:4px;"></i><?= $item['time'] ?></span>
            </a>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
    <div class="nav-admin" id="navAdminUser">
      <span><?= $admin_name ?></span>
      <img src="../ASSETS/USER.png" alt="Admin"/>
    </div>
  </div>
</header>

<main class="page-wrap">
  <h1 class="page-title"><i class="fa-solid fa-list-check"></i> Kelola Laporan</h1>
  <p class="page-sub">Verifikasi dan tindak lanjuti semua laporan masyarakat</p>

  <!-- STAT CARDS -->
  <div class="stat-row">
    <a href="Kelola_Laporan.php?filter=semua" class="stat-card <?= $filter==='semua'?'active':'' ?>">
      <div class="num"><?= $counts['semua'] ?></div><div class="lbl">Semua Laporan</div>
    </a>
    <a href="Kelola_Laporan.php?filter=pending" class="stat-card <?= $filter==='pending'?'active':'' ?>">
      <div class="num" style="color:#92400e"><?= $counts['pending'] ?></div><div class="lbl">Menunggu</div>
    </a>
    <a href="Kelola_Laporan.php?filter=diproses" class="stat-card <?= $filter==='diproses'?'active':'' ?>">
      <div class="num" style="color:#1e40af"><?= $counts['diproses'] ?></div><div class="lbl">Diproses</div>
    </a>
    <a href="Kelola_Laporan.php?filter=selesai" class="stat-card <?= $filter==='selesai'?'active':'' ?>">
      <div class="num" style="color:#166534"><?= $counts['selesai'] ?></div><div class="lbl">Selesai</div>
    </a>
    <a href="Kelola_Laporan.php?filter=ditolak" class="stat-card <?= $filter==='ditolak'?'active':'' ?>">
      <div class="num" style="color:#991b1b"><?= $counts['ditolak'] ?></div><div class="lbl">Ditolak</div>
    </a>
  </div>

  <!-- TOOLBAR -->
  <div class="toolbar">
    <form class="search-box" method="GET">
      <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>"/>
      <input type="text" name="q" placeholder="Cari judul, lokasi, pelapor..." value="<?= htmlspecialchars($search) ?>"/>
      <button type="submit"><i class="fa-solid fa-search"></i></button>
    </form>
    <?php if ($search): ?>
      <a href="Kelola_Laporan.php?filter=<?= $filter ?>" style="padding:11px 16px;background:#f1f5f9;border-radius:10px;color:#555;text-decoration:none;font-size:13px;border:1.5px solid #e2e8f0;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-xmark"></i> Reset</a>
    <?php endif; ?>
  </div>

  <!-- TABLE -->
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Laporan</th>
          <th>Kategori</th>
          <th>Pelapor</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($laporan)): ?>
        <tr class="empty-row">
          <td colspan="7">
            <div class="ico"><i class="fa-solid fa-folder-open"></i></div>
            <div>Tidak ada laporan<?= $filter!=='semua' ? ' dengan status <strong>'.$slabel[$filter].'</strong>' : '' ?><?= $search ? ' untuk pencarian "<strong>'.htmlspecialchars($search).'</strong>"' : '' ?>.</div>
          </td>
        </tr>
        <?php else: foreach ($laporan as $i => $r): ?>
        <tr>
          <td style="color:#9ca3af;font-size:12px;"><?= $i+1 ?></td>
          <td class="td-judul">
            <?= htmlspecialchars($r['judul']) ?>
            <small><i class="fa-solid fa-location-dot" style="color:#e63946;font-size:10px;"></i> <?= htmlspecialchars($r['lokasi'] ?? '-') ?></small>
          </td>
          <td style="font-size:12.5px;"><?= htmlspecialchars($r['nama_kategori'] ?? '-') ?></td>
          <td style="font-size:12.5px;"><?= htmlspecialchars($r['nama_lengkap'] ?? 'Pengguna') ?></td>
          <td><span class="badge <?= $sclass[$r['status']] ?? '' ?>"><?= $slabel[$r['status']] ?? $r['status'] ?></span></td>
          <td style="font-size:12px;color:#64748b;white-space:nowrap;"><?= date('d M Y', strtotime($r['created_at'])) ?></td>
          <td><a href="detail_laporan.php?id=<?= $r['id_laporan'] ?>" class="btn-detail"><i class="fa-solid fa-eye"></i> Detail</a></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>

<footer class="main-footer">
  <div class="footer-top"><img src="../ASSETS/LOGO.png" class="footer-logo" alt="Aksi Kita"><h3>Aksi Kita</h3></div>
  <div class="footer-content">
    <div class="footer-col"><p>Jl. Bachireng No. 12, Indonesia</p><p>0821 6888 9060</p><p>info@aksikita.id</p></div>
    <div class="footer-col"><a href="#">Unit Layanan Terpadu</a><a href="#">Cara Kerja</a><a href="#">FAQ</a></div>
    <div class="footer-col"><a href="Kelola_Laporan.php">Kelola Laporan</a><a href="Beranda_Admin.php">Beranda Admin</a></div>
  </div>
  <div class="footer-social"><a href="#"><i class="fab fa-whatsapp"></i></a><a href="#"><i class="fab fa-facebook"></i></a><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fab fa-youtube"></i></a></div>
  <div class="footer-bottom">© 2025 AksiKita. Semua Hak Dilindungi.</div>
</footer>

<?php include 'profile_modal_admin.php'; ?>
<script>
const nb=document.getElementById('notiBellBtn'),nd=document.getElementById('notiDropdown');
if(nb&&nd){nb.addEventListener('click',e=>{e.stopPropagation();nd.style.display=nd.style.display==='block'?'none':'block';});document.addEventListener('click',e=>{if(!nd.contains(e.target)&&!nb.contains(e.target))nd.style.display='none';});}
</script>
</body></html>
