<?php
session_start();
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Beranda2 - Aksi Kita</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f9f9f9;
      margin: 0;
      color: #222;
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

.navbar-right{
    margin-left:auto;
    display:flex;
    align-items:center;
    gap:15px;
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

    .user{
    display:flex;
    align-items:center;
    gap:10px;
    margin-left:auto;
    cursor:pointer;
}

    .user-img{
    width:45px;
    height:45px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid rgba(255,255,255,.4);
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

    
    /* Notification Bell */
.noti-wrap{
    position:relative;
    display:flex;
    align-items:center;
   
}
.noti-btn{cursor:pointer;position:relative;width:38px;height:38px;border-radius:50%;
  background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;transition:.2s;}
.noti-btn:hover{background:rgba(255,255,255,.22);}
.noti-badge{position:absolute;top:-3px;right:-3px;background:#e63946;color:#fff;border-radius:50%;
  width:18px;height:18px;display:flex;align-items:center;justify-content:center;
  font-size:10px;font-weight:700;border:1px solid #1e3d8f;}
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


    .hero { position: relative; 
      text-align: center; 
      overflow: hidden; 
    }
    .hero-img { 
      width: 100%; 
      border-radius: 0 0 130px 130px; 
      min-height: 400px;
      height: 85vh; 
      object-fit: cover; 
      display: block; 
    }
    .hero-text { 
      position: absolute; 
      top: 50%; 
      left: 50%; 
      transform: translate(-50%, -50%); 
      color: white; 
    }
    .hero-text h1 { 
      font-size: clamp(30px, 5vw, 50px);
  width: 90%; 
      margin: auto; 
    }
    .laporkan-btn { 
      margin-top: 40px; 
      padding: 20px 50px; 
      background: #1e3d8f; 
      border: none; 
      color: white; 
      border-radius: 12px; 
      cursor: pointer; 
      font-size: 22px; 
      
    }

    .laporkan-btn:hover {
      background: white;
      color: #244a9a;
    }
    .cards { 
      display: flex; 
      justify-content: center; 
      gap: 30px; 
      margin-top: -100px; 
      position: relative; 
      z-index: 10; }
    .card { 
      background: white; 
      border-radius: 12px; 
      width: 250px; 
      text-align: center; 
      color: #000;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
      padding: 15px; 
      transition: .3s; 
      text-decoration: none;
    }
    .card:hover { 
      transform: translateY(-5px); 
    }
    .card img { 
      width: 250px; 
      height: 150px; 
      object-fit: cover; 
      border-radius: 8px; 
    }

    .footer {
    background: rgba(0, 0, 0, 0.674);
    color: white;
    text-align: center;
    padding: 20px 0;
    margin-top: 80px;
    font-size: 14px;
    }

    .footer-container p {
    margin: 0;
    opacity: 0.9;
    }

    /* ================== ABOUT SECTION ================== */
.about-section {
  padding: 120px 7%;
  background: #ffffff;
  margin-top: 0;
  margin-bottom: 0;
  
}

.about-container {
  max-width: 1500px;
  margin: auto;
  text-align: center;
}

.about-section h2 {
  font-size: 36px;
  font-weight: 700;
  color: #1e3d8f;
  margin-bottom: 20px;
  
}

.about-section p {
  font-size: 20px;
  color: #444;
  max-width: 800px;
  margin: auto;
  line-height: 1.6;
}

/* GRID */
.about-grid {
  margin-top: 60px;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 35px;
}

/* CARD */
.about-card {
  background: white;
  border-radius: 18px;
  padding: 25px;
  box-shadow: 0 12px 35px rgba(0,0,0,0.1);
  transition: 0.3s;
}

.about-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 40px rgba(0,0,0,0.18);
}

.about-card img {
  width: 100%;
  height: 170px;
  object-fit: cover;
  border-radius: 15px;
  margin-bottom: 18px;
}

.about-card h3 {
  margin-top: 8px;
  font-size: 20px;
  color: #1e3d8f;
}

.about-card p {
  font-size: 15px;
  color: #555;
  line-height: 1.4;
}

/* RESPONSIVE */
@media (max-width: 900px) {
  .about-grid {
    grid-template-columns: 1fr;
  }
}


/* ================== ANOTHER DESCRIPTION ================== */
/* SHOWCASE SECTION STYLE */
.showcase {
padding: 80px 7%;
background: #ffffff;
margin-bottom: 100px;
}

.showcase-wrapper {
display: flex;
justify-content: space-between;
align-items: center;
gap: 60px;
max-width: 1200px;
margin: auto;
margin-top: -50px;
}

/* TEXT */
.showcase-text {
flex: 1;
}

.showcase-text h2 {
font-size: 40px;
font-weight: 700;
color: #1e3d8f;
margin-bottom: 20px;
}

.showcase-text p {
font-size: 17px;
line-height: 1.7;
color: #444;
margin-bottom: 20px;
}

/* BUTTON */
.showcase-btn {
padding: 15px 40px;
border-radius: 10px;
border: none;
background: #1e3d8f;
color: white;
font-size: 16px;
font-weight: 600;
cursor: pointer;
transition: 0.3s;
}

.showcase-btn:hover {
background: #163680;
}

/* IMAGE */
.showcase-image {
flex: 1;
display: flex;
justify-content: center;
}

.showcase-image img {
width: 100%;
max-width: 500px;
border-radius: 20px;
box-shadow: 0 25px 80px rgba(0, 0, 0, 0);
}

/* RESPONSIVE */
@media (max-width: 900px) {
.showcase-wrapper {
flex-direction: column;
text-align: center;
}

.showcase-image img {
max-width: 90%;
}
}

/* ================== FOOTER ================== */
.main-footer {
  background: linear-gradient(165deg, #080e18 0%, #102647 70%, #9c7719 120%);
  color: #fff;
  padding: 60px 70px;
  font-family: Arial, sans-serif;
}

/* Header logo + nama */
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

/* Konten kolom */
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

/* Ikon sosial */
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

/* Copyright */
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

.footer {
    background: rgba(0, 0, 0, 0.674);
    color: white;
    text-align: center;
    padding: 20px 0;
    margin-top: 80px;
    font-size: 14px;
    }

    .footer-container p {
    margin: 0;
    opacity: 0.9;
    }

  

/* =============== ZIGZAG MODERN SECTION =============== */
.zigzag-container h2 {
  font-size: 36px;
  color: #1e3d8f;
  font-weight: 700;
  margin-bottom: 30px;
}
.zigzag-section {
    max-width: 100%;
    margin: auto auto;
    max-height: 100%;
    margin-bottom: -150px;
    padding: 80px 7%;
    display: flex;
    flex-direction: column;
    gap: 30px;
    background:radial-gradient(circle at 20% 20%, rgba(0,0,0,0.04), transparent 60%),
        radial-gradient(circle at 80% 80%, rgba(0,0,0,0.04), transparent 60%),
        linear-gradient(135deg, #eef2ff 0%, #f6f7fb 50%, #ffffff 100%);
    box-shadow: inset 0 0 40px rgba(0,0,0,0.05);
    transform: translate(0%, -10%);
  }

/* ITEM WRAPPER */
.zigzag-item {
    display: flex;
    align-items: center;
    gap: 60px;
}
.zigzag-item {
    padding: 30px;
    border-radius: 20px;
    transition: 0.3s ease;
}

.zigzag-item:hover {
    background: rgba(255, 255, 255, 0.6);
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    transform: translateY(-6px);
}

.zigzag-item:hover .zigzag-img i {
    transform: translateY(-6px);
    background-color: #a0b1d8;
}



/* REVERSE (gambar kanan – teks kiri) */
.zigzag-item.reverse {
    flex-direction: row-reverse;
}

/* ICON / IMAGE */
.zigzag-img i {
    font-size: 85px;
    color: #1e3d8f;
    background: #eef2ff;
    padding: 35px;
    border-radius: 25px;
    box-shadow: 0px 10px 25px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.zigzag-img i:hover {
  transform: translateY(-6px);
  background-color: #a0b1d8;
    
}

/* TEXT */
.zigzag-text {
    max-width: 500px;
    
}

.zigzag-text h3 {
    font-size: 28px;
    color: #1e3d8f;
    margin-bottom: 12px;
}

.zigzag-text p {
    font-size: 16px;
    line-height: 1.6;
    color: #444;
}

/* RESPONSIVE */
@media (max-width: 900px) {
    .zigzag-item,
    .zigzag-item.reverse {
        flex-direction: column !important;
        text-align: center;
    }

    .zigzag-img i {
        margin-bottom: 20px;
    }
}

.showcase-btn {
  text-decoration: none;
}

  </style>
</head>

<body>

  <header class="navbar">
    <div class="logo">
      <img src="../ASSETS/LOGO.png" class="logo-img">
    </div>

    <nav>
      <a href="#" class="active">Beranda</a>
      <a href="laporan_saya.php">Laporan</a>
      <a href="peringkat.php">Peringkat</a>
      <a href="TENTANG.php">Tentang</a>
    </nav>

    <div class="user" id="navUser">
      <div class="noti-wrap">

  <div class="noti-btn" id="notiBellBtn">
    <i class="fa-solid fa-bell"></i>

    <?php if($noti_count > 0): ?>
      <span class="noti-badge">
        <?= $noti_count ?>
      </span>
    <?php endif; ?>

  </div>

  <div class="noti-dropdown" id="notiDropdown">

    <div class="noti-header">
      Notifikasi
    </div>

    <?php if(empty($noti_items)): ?>

      <div style="padding:20px;text-align:center;color:#64748b;">
        Belum ada notifikasi
      </div>

    <?php else: ?>

      <?php foreach($noti_items as $item): ?>

        <a href="<?= $item['link'] ?>" class="noti-item">
          <span class="noti-title">
            <?= $item['title'] ?>
          </span>

          <span class="noti-desc">
            <?= $item['desc'] ?>
          </span>
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

  <section class="hero">
    <img src="../ASSETS/Main Background.jpg" class="hero-img" />
    <div class="hero-text">
      <h1>MARI BERKONTRIBUSI DEMI KEBAIKAN INDONESIA</h1>

      <form action="laporkan.php">
        <button class="laporkan-btn">LAPORKAN</button>
      </form>

    </div>
  </section>

  <section class="cards">
    <a href="laporan_terbaru.php" class="card">
      <img src="../ASSETS/Laporan Terpopuler.avif">
      <h3>LAPORAN TERBARU</h3>
      <p>Berdasarkan dari laporan yang terbaru di Kota Medan</p>
    </a>
    
    <a href="STATISTIK.php" class="card">
      <img src="../ASSETS/Statistika Laporan.jpg">
      <h3>STATISTIKA LAPORAN</h3>
      <p>Hasil analisis laporan-laporan yang ada di Kota Medan</p>
    </a>

    <a href="INFO_PENTING.php" class="card">
      <img src="../ASSETS/Info Penting.png">
      <h3>INFO PENTING</h3>
      <p>Pengumuman resmi mengenai pemerintahan Kota Medan</p>
    </a>
  </section>


    <!-- ABOUT SECTION -->
<section class="about-section">
  <div class="about-container">
    <h2>Apa Itu Aksi Kita?</h2>
    <p>
      Aksi Kita adalah platform pelaporan publik yang membantu masyarakat 
      menyampaikan aspirasi, kritik, dan laporan masalah sosial secara cepat 
      dan transparan. Dengan sistem terintegrasi, setiap laporan dapat dipantau 
      prosesnya hingga tuntas.
    </p>

    <div class="about-grid">
      <div class="about-card">
        <img src="../ASSETS/laporanpublik.webp" alt="Laporan Publik">
        <h3>Laporan Publik</h3>
        <p>
          Laporkan masalah yang terjadi di lingkunganmu kapan saja, di mana saja.
        </p>
      </div>

      <div class="about-card">
        <img src="../ASSETS/verifikasicepat.jpg" alt="Verifikasi Cepat">
        <h3>Verifikasi Cepat</h3>
        <p>
          Tim terkait akan memverifikasi informasi secara otomatis dan cepat.
        </p>
      </div>

      <div class="about-card">
        <img src="../ASSETS/transparansidata.jpg" alt="Transparansi Data">
        <h3>Transparansi Data</h3>
        <p>
          Semua status laporan dapat dipantau secara real-time oleh masyarakat.
        </p>
      </div>
    </div>
  </div>
</section>


  <!-- ANOTHER DESCRIPTION -->


  <section class="showcase">
  <div class="showcase-wrapper">

<div class="showcase-text">
  <h2>Aksi Kita – Suara Anda Didengar</h2>
  <p>
    AksiKita adalah platform pelaporan publik modern yang memberikan ruang 
    bagi masyarakat untuk menyampaikan laporan masalah sosial di lingkungan 
    sekitar secara cepat, transparan, dan terukur.
  </p>

  <p>
    Setiap laporan diproses dengan sistem terintegrasi agar dapat ditindaklanjuti 
    oleh pihak yang berwenang, serta dapat dipantau oleh publik secara terbuka.
  </p>

  <a href="TENTANG.php" class="showcase-btn">Pelajari Lebih Lanjut</a>
</div>

<div class="showcase-image">
  <img src="../ASSETS/kolase.png" alt="Mockup AksiKita">
</div>

  </div>
</section>

<section class="zigzag-section">
   <div class="zigzag-container">
    <h2>Mengapa Harus Memilih AksiKita?</h2>
    <!-- ITEM 1 -->
    <div class="zigzag-item">
        <div class="zigzag-img">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div class="zigzag-text">
            <h3>Aman & Terverifikasi</h3>
            <p>Laporan diproses menggunakan sistem keamanan berlapis yang memastikan data tetap terlindungi.
                Setiap laporan yang masuk diverifikasi otomatis untuk mencegah informasi palsu dan memastikan
                laporan yang diterima adalah valid serta dapat ditindaklanjuti oleh pihak berwenang.
            </p>
        </div>
    </div>

    <!-- ITEM 2 -->
    <div class="zigzag-item reverse">
        <div class="zigzag-img">
            <i class="fa-solid fa-bolt"></i>
        </div>
        <div class="zigzag-text">
            <h3>Proses Cepat</h3>
            <p>Sistem memproses laporan secara instan dan langsung mengirimkannya ke instansi terkait.
                Pengguna tidak perlu menunggu lama, karena setiap laporan otomatis dianalisis tanpa menunggu
                antrean manual. Ini memastikan penanganan lebih efisien dan tepat waktu.</p>
        </div>
    </div>

    <!-- ITEM 3 -->
    <div class="zigzag-item">
        <div class="zigzag-img">
            <i class="fa-solid fa-chart-line"></i>
        </div>
        <div class="zigzag-text">
            <h3>Transparansi</h3>
            <p>Perkembangan setiap laporan dapat dipantau secara real-time melalui dashboard publik.
                Masyarakat bisa melihat tahap demi tahap proses penanganan, mulai dari laporan diterima,
                diverifikasi, sedang diproses, hingga selesai. Semua dibuat terbuka agar masyarakat
                mendapatkan kepercayaan penuh.</p>
        </div>
    </div>

    <!-- ITEM 4 -->
    <div class="zigzag-item reverse">
        <div class="zigzag-img">
            <i class="fa-solid fa-people-group"></i>
        </div>
        <div class="zigzag-text">
            <h3>Kolaboratif</h3>
            <p>Masyarakat dapat berpartisipasi secara langsung dengan menambahkan bukti tambahan seperti
                foto, video, maupun informasi lokasi. Kontribusi kolektif ini membantu meningkatkan akurasi
                laporan sehingga proses penyelesaian kasus menjadi lebih cepat dan efektif.</p>
        </div>
    </div>

</section>

  
  <footer class="main-footer">
  <div class="footer-top">
    <img src="../ASSETS/LOGO.png" class="footer-logo" alt="AksiKita">
    <h3>Aksi Kita</h3>
  </div>

  <div class="footer-content">

    <!-- Kiri -->
    <div class="footer-col">
      <p>Jl. Bachireng No. 12, Indonesia</p>
      <p>0821 6888 9060</p>
      <p>info@aksikita.id</p>
    </div>

    <!-- Tengah -->
    <div class="footer-col">
      <a href="#">Unit Layanan Terpadu</a>
      <a href="#">Cara Kerja</a>
      <a href="#">FAQ</a>
      <a href="#">Aturan Penggunaan</a>
    </div>

    <!-- Kanan -->
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

    dropdown.style.display =
      dropdown.style.display === "block"
      ? "none"
      : "block";
  });

  document.addEventListener("click", function(e){

    if(
      !dropdown.contains(e.target) &&
      !bell.contains(e.target)
    ){
      dropdown.style.display = "none";
    }

  });

}
</script>
</body>
</html>
