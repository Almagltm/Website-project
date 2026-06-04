<?php
session_start();
// Menggunakan koneksi sesuai dengan yang tertulis di kode atas kamu: require_once '../db.php'
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: MASUK.php");
    exit();
}

$id_user = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT nama_lengkap,email,no_telp
    FROM users
    WHERE id_user = ?
");

$stmt->bind_param("i", $id_user);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$nama = $user['nama_lengkap'];

/* ==========================================================
   KODE OTOMATISASI: AMBIL DATA DARI DATABASE AGAR SINKRON
   ========================================================== */
// 1. Ambil data Tentang Aksi Kita
$query_tentang = mysqli_query($conn, "SELECT * FROM tentang LIMIT 1");
$data_tentang = mysqli_fetch_assoc($query_tentang);

// 2. Ambil data Video
$query_video = mysqli_query($conn, "SELECT * FROM tentang_video LIMIT 1");
$data_video = mysqli_fetch_assoc($query_video);

// 3. Ambil data Tim Pengembang
$query_tim = mysqli_query($conn, "SELECT * FROM tim_pengembang");

// Fallback untuk notifikasi agar tidak error jika belum di-query sebelumnya
$noti_count = $noti_count ?? 0;
$noti_items = $noti_items ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Aksi Kita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<style>
/* RESET DASAR */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background-color: #f8f8f8;
    color: #333;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.section-title {
    text-align: center;
    margin-bottom: 30px;
    font-size: 2em;
    font-weight: 700;
    color: #333;
}
.navbar-right{
    margin-left:auto;
    display:flex;
    align-items:center;
    gap:15px;
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

.navlinks {
  display: flex;
  gap: 15px;
  margin-left: 60px;
  flex-wrap: wrap;
}

.logo img {
    height: 55px;
}

.navlinks a {
    color: white;
    text-decoration: none;
    margin: 0 15px;
    padding: 5px 0;
    transition: 0.3s ease;
    font-size: 16px;
}

.navlinks a.active {
    text-decoration: underline;
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

.noti-header{
  padding:12px 15px;
  background:#f8fafc;
  border-bottom:1px solid #e2e8f0;
  font-weight:700;
  color:#1e3d8f;
}

.noti-item{
  display:block;
  padding:12px 15px;
  text-decoration:none;
  border-bottom:1px solid #f1f5f9;
}

.noti-item:hover{
  background:#f8fafc;
}

.noti-title{
  display:block;
  color:#1e293b;
  font-size:13px;
  font-weight:600;
}

.noti-desc{
  display:block;
  color:#64748b;
  font-size:12px;
  margin-top:2px;
}

/* STYLING TENTANG AKSI KITA */
.about-aksi-kita {
    display: flex;
    align-items: center;
    gap: 40px;
    padding: 50px 0;
    margin-bottom: 40px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.content-left {
    flex: 1;
    display: flex;
    justify-content: center;
    overflow: hidden;
    border-radius: 10px 0 0 10px;
}

.content-left img {
    max-width: 80%;
    height: auto;
    object-fit: cover;
    border-radius: 10px;
}

.content-right {
    flex: 1.5;
    padding-right: 40px;
}

.content-right .section-title {
    text-align: left;
    margin-top: 0;
    font-size: 1.8em;
    color: #000080;
}

.content-right p {
    line-height: 1.6;
    margin-bottom: 15px;
    text-align: justify;
    white-space: pre-line; /* Agar baris baru/enter di textarea admin terbaca rapi */
}

/* FRAME VIDEO */
.intro-video-frame {
    width: 800px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    margin: 20px auto;
}

.intro-video-frame video {
    width: 100%;
    display: block;
    border-radius: 15px;
}

/* TITLE + DESCRIPTION */
.intro-title {
    text-align: left;
    color: #000080;
    font-size: 1.8em;
    margin-bottom: 15px;
}

.intro-desc {
    text-align: left;
    font-size: 15px;
    color: #444;
    line-height: 1.6;
    margin-top: 15px;
}

/* STYLING TENTANG KAMI (TIM) */
.about-us .section-title {
    color: #000080;
    margin-bottom: 40px;
    font-size: 1.8em;
}

.team-cards-container {
    display: flex;
    justify-content: center; 
    flex-wrap: wrap; 
    gap: 20px;
}

.team-member-card {
    background-color: white;
    padding: 20px 15px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column; 
    align-items: center; 
    width: 18%; 
    min-width: 200px; 
    margin-bottom: 20px;
    transition: transform 0.3s;
}

.team-member-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

.profile-photo-real {
    width: 120px; 
    height: 120px; 
    border-radius: 50%; 
    object-fit: cover; 
    margin: 0 auto 15px; 
    border: 3px solid #1e3d8f; 
}

.team-member-card h3 {
    font-size: 1em;
    font-weight: 600;
    color: #000080;
    margin-top: 10px;
    margin-bottom: 5px;
    text-align: center;      
    width: 100%;             
    min-height: 45px;        
    display: flex;           
    align-items: center;     
    justify-content: center;
}

.team-member-card .nim {
    font-size: 0.85em;
    color: #666;
    margin-bottom: 15px;
}

.team-member-card .role {
    font-size: 0.75em;
    color: #000000;
    background-color: #ADD8E6; 
    padding: 10px; 
    border-radius: 5px;
    width: 100%;          
    margin-top: auto;     
    min-height: 60px;     
    display: flex;
    align-items: center;     
    justify-content: center; 
    text-align: center;
}

/* FOOTER */
.main-footer {
    background: linear-gradient(165deg, #080e18 0%, #102647 70%, #9c7719 120%);
    color: #fff;
    padding: 60px 70px;
    font-family: Arial, sans-serif;
}

.footer-top {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 40px;
}

.footer-logo {
  width: 75px;
  height: 55px;
  object-fit: cover;
}

.footer-content {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  margin-bottom: 45px;
  gap: 20px;
}

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
  margin-top: 10px;
}

@media (max-width: 800px) {
  .footer-content {
    flex-direction: column;
  }
  
  .footer-top {
    flex-direction: column;
    text-align: center;
  }
}
</style>
<body>

    <header class="navbar">
        <div class="logo">
            <img src="../ASSETS/LOGO.png" alt="Logo Aksi Kita">
        </div>

        <nav class="navlinks">
            <a href="BERANDA2.php">Beranda</a>
            <a href="laporan_saya.php">Laporan</a>
            <a href="peringkat.php">Peringkat</a>
            <a href="TENTANG.php" class="active">Tentang</a>
        </nav>
        
        <div class="user" id="navUser">
            <div class="noti-wrap">
                <div class="noti-btn" id="notiBellBtn">
                    <i class="fa-solid fa-bell"></i>
                    <?php if($noti_count > 0): ?>
                        <span class="noti-badge"><?= $noti_count ?></span>
                    <?php endif; ?>
                </div>

                <div class="noti-dropdown" id="notiDropdown">
                    <div class="noti-header">Notifikasi</div>
                    <?php if(empty($noti_items)): ?>
                        <div style="padding:20px;text-align:center;color:#64748b;">Belum ada notifikasi</div>
                    <?php else: ?>
                        <?php foreach($noti_items as $item): ?>
                            <a href="<?= $item['link'] ?>" class="noti-item">
                                <span class="noti-title"><?= $item['title'] ?></span>
                                <span class="noti-desc"><?= $item['desc'] ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <span class="username">
                <?php echo htmlspecialchars($nama); ?>
            </span>
            <img src="../ASSETS/USER.png" class="user-img">
        </div>
    </header>
    
    <main class="container">
        
        <section class="about-aksi-kita" style="padding-top: 100px;"> 
            <div class="content-left">
                <img src="../ASSETS/<?= !empty($data_tentang['gambar']) ? $data_tentang['gambar'] : 'kolase.png' ?>" alt="Gambar Kolaborasi Aksi Kita">
            </div>
            <div class="content-right">
                <h1 class="section-title" style="font-family: 'poppins';"><?= htmlspecialchars($data_tentang['judul'] ?? 'Tentang Aksi Kita') ?></h1>
                <p><?= htmlspecialchars($data_tentang['deskripsi'] ?? 'Aksi Kita adalah platform pelaporan digital...') ?></p>
            </div>
        </section>

        <section class="intro-video" style="margin-top:80px; margin-bottom:60px;">
            <h2 class="intro-title"><?= htmlspecialchars($data_video['judul'] ?? 'Satu Aplikasi untuk Semua Laporan Warga') ?></h2>

            <div class="intro-video-frame">
                <video controls>
                    <source src="../ASSETS/<?= !empty($data_video['file_video']) ? $data_video['file_video'] : 'Video.mp4' ?>" type="video/mp4">
                    Browser Anda tidak mendukung video.
                </video>
            </div>

            <p class="intro-desc">
                “<?= htmlspecialchars($data_video['deskripsi'] ?? 'Melalui Aksi Kita, setiap laporan warga bisa ditangani lebih cepat dan tepat.') ?>”
            </p>
        </section>

        <section class="about-us">
            <h2 class="section-title">Tim Kreator Aksi Kita</h2>
            <div class="team-cards-container">
                <?php if ($query_tim && mysqli_num_rows($query_tim) > 0): ?>
                    <?php while($row_tim = mysqli_fetch_assoc($query_tim)): ?>
                        <div class="team-member-card">
                            <img src="../ASSETS/<?= $row_tim['foto'] ?>" alt="Foto <?= htmlspecialchars($row_tim['nama']) ?>" class="profile-photo-real">
                            <h3><?= htmlspecialchars($row_tim['nama']) ?></h3>
                            <p class="nim">NIM: <?= htmlspecialchars($row_tim['nim']) ?></p>
                            <p class="role"><?= htmlspecialchars($row_tim['tugas']) ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align:center; color:#666; width:100%;">Belum ada data tim pengembang.</div>
                <?php endif; ?>
            </div>
        </section>
        
    </main>

    <footer class="main-footer">
        <div class="footer-top">
            <img src="../ASSETS/LOGO.png" class="footer-logo" alt="AksiKita">
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
      
        <div class="footer-bottom">
            © 2026 AksiKita. Semua Hak Dilindungi.
        </div>
    </footer>

<?php include 'profile_modal.php'; ?>

<script>
const bell = document.getElementById("notiBellBtn");
const dropdown = document.getElementById("notiDropdown");

if(bell && dropdown){
  bell.addEventListener("click", function(e){
    e.stopPropagation();
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
  });

  document.addEventListener("click", function(e){
    if(!dropdown.contains(e.target) && !bell.contains(e.target)){
      dropdown.style.display = "none";
    }
  });
}
</script>
</body>
</html>