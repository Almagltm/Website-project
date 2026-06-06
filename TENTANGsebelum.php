<?php
// Hubungkan ke file koneksi database
// Sesuaikan path jika lokasi file tentangsebelum.php berada di dalam folder tertentu (misal jika sejajar dengan index/beranda, gunakan 'koneksi.php')
include 'koneksi.php';

/* 1. AMBIL DATA TENTANG */
$tentang = mysqli_query($conn, "SELECT * FROM tentang LIMIT 1");
$data_tentang = mysqli_fetch_assoc($tentang);

/* 2. AMBIL VIDEO */
$video = mysqli_query($conn, "SELECT * FROM tentang_video LIMIT 1");
$data_video = mysqli_fetch_assoc($video);

/* 3. AMBIL TIM PENGEMBANG */
$tim = mysqli_query($conn, "SELECT * FROM tim_pengembang");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data_tentang['judul'] ?? 'Tentang Aksi Kita') ?></title>
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
    background-color: #f8f8f8; /* Warna latar belakang umum */
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

.logo img {
    height: 55px;
}

.navlinks {
  display: flex;
  gap: 15px;
  margin-left: 60px;
  flex-wrap: wrap;
  
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

.nav-auth {
  display: flex;
  margin-left: auto;
  gap: 10px;
}

.btn-outline {
  padding: 6px 30px;
  border: 2px solid white;
  border-radius: 30px;
  background: transparent;
  color: white;
  font-weight: 550;
  cursor: pointer;
  transition: 0.3s;
}

.btn-outline:hover {
  background: white;
  color: #244a9a;
}

/* ================================== */
/* STYLING TENTANG AKSI KITA */
/* ================================== */
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

/* ================================== */
/* STYLING TENTANG KAMI (TIM) - FINAL FIXED */
/* ================================== */
.about-us .section-title {
    color: #000080;
    margin-bottom: 40px;
    font-size: 1.8em;
}

.team-cards-container {
    display: flex;
    justify-content: center; /* Ubah ke center agar seimbang jika jumlah ganjil */
    flex-wrap: wrap; 
    gap: 20px;
}

.team-member-card {
    background: linear-gradient(145deg, #ffffff, #f9fafb);
    padding: 30px 20px;
    border-radius: 20px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05), 0 2px 6px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column; 
    align-items: center; 
    width: 18%; 
    min-width: 200px; 
    margin-bottom: 20px;
    transition: all 0.3s ease;
    border: 1px solid rgba(226, 232, 240, 0.8);
}

.team-member-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.05);
    border-color: #1e3d8f;
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
</style>
<body>
    <header class="navbar">
        <div class="logo">
            <img src="ASSETS/LOGO.png" alt="Logo Aksi Kita">
        </div>

        <nav class="navlinks">
            <a href="BERANDA1.php">Beranda</a>
            <a href="LAPORANsebelum.php">Laporan</a>
            <a href="PERINGKATsebelum.php">Peringkat</a>
            <a href="TENTANGsebelum.php" class="active">Tentang</a>
        </nav>
        <div class="nav-auth">
          <button onclick="window.location.href='./USER/DAFTAR.php'" class="btn-outline">Daftar</button>
          <button onclick="window.location.href='./login.php'" class="btn-outline">Masuk</button>
        </div>
      
    </header>
    
    <main class="container">
        <section class="about-aksi-kita" style="padding-top: 100px;"> 
            <div class="content-left">
                <?php if(!empty($data_tentang['gambar'])): ?>
                    <img src="ASSETS/<?= htmlspecialchars($data_tentang['gambar']) ?>" alt="Gambar Kolaborasi Aksi Kita">
                <?php else: ?>
                    <img src="ASSETS/kolase.png" alt="Gambar Kolaborasi Aksi Kita">
                <?php endif; ?>
            </div>
            <div class="content-right">
                <h1 class="section-title" style="font-family: 'poppins'; text-align: left;">
                    <?= htmlspecialchars($data_tentang['judul'] ?? 'Tentang Aksi Kita') ?>
                </h1>
                <p><?= nl2br(htmlspecialchars($data_tentang['deskripsi'] ?? 'Aksi Kita adalah platform pelaporan digital yang memberdayakan masyarakat untuk berpartisipasi aktif dalam menjaga kualitas fasilitas umum di seluruh Indonesia.')) ?></p>
            </div>
        </section>

        <section class="intro-video" style="margin-top:80px; margin-bottom:60px;">
            <h2 class="intro-title"><?= htmlspecialchars($data_video['judul'] ?? 'Satu Aplikasi untuk Semua Laporan Warga') ?></h2>

            <div class="intro-video-frame">
                <video controls>
                    <?php if(!empty($data_video['file_video'])): ?>
                        <source src="ASSETS/<?= htmlspecialchars($data_video['file_video']) ?>" type="video/mp4">
                    <?php else: ?>
                        <source src="ASSETS/Video.mp4" type="video/mp4">
                    <?php endif; ?>
                    Browser Anda tidak mendukung video.
                </video>
            </div>

            <p class="intro-desc">
                “<?= htmlspecialchars($data_video['deskripsi'] ?? 'Melalui Aksi Kita, setiap laporan warga bisa ditangani lebih cepat dan tepat. Video ini menunjukkan bagaimana aplikasi membantu meningkatkan kualitas lingkungan kita.') ?>”
            </p>
        </section>

        <section class="about-us">
            <h2 class="section-title">Tim Kreator Aksi Kita</h2>
            <div class="team-cards-container">

                <?php 
                if (mysqli_num_rows($tim) > 0):
                    while($row = mysqli_fetch_assoc($tim)): 
                        $nama_tampil = htmlspecialchars($row['nama']);
                        if ($nama_tampil === 'Alma Murael Gultom') {
                            $nama_tampil = 'Alma Murael<br>Gultom';
                        }
                ?>
                    <div class="team-member-card">
                        <img src="ASSETS/<?= htmlspecialchars($row['foto']) ?>" alt="Foto <?= htmlspecialchars($row['nama']) ?>" class="profile-photo-real">
                        <h3><?= $nama_tampil ?></h3>
                        <p class="nim">NIM: <?= htmlspecialchars($row['nim']) ?></p>
                    </div>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <div class="team-member-card">
                        <img src="ASSETS/vaidon.jpeg" alt="Foto Vaidon" class="profile-photo-real">
                        <h3>Vaidon Shello Sinambela</h3>
                        <p class="nim">NIM: 241712052</p>
                    </div>
                <?php endif; ?>
                
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="footer-top">
            <img src="ASSETS/LOGO.png" class="footer-logo" alt="AksiKita">
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
</body>
</html>