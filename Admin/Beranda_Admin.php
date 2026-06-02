<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit();
}

$admin = $_SESSION['nama_admin'];

/* HITUNG TOTAL LAPORAN */
$query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM laporan");
$data = mysqli_fetch_assoc($query);
$total_laporan = $data['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Admin - Aksi Kita</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;
        }

        body{
            background:#f5f5f5;
            overflow-x:hidden;
        }

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
        }

        .logo img{
            height:55px;
        }

        .navlinks{
            display:flex;
            gap:20px;
            margin-left:60px;
        }

        .navlinks a{
            color:white;
            text-decoration:none;
            font-weight:500;
            transition:0.3s;
        }

        .navlinks a:hover{
            color:#ffd54f;
        }

        .navlinks a.active{
            text-decoration:underline;
        }

        .admin-profile{
            margin-left:auto;
            display:flex;
            align-items:center;
            gap:15px;
        }

        .profile-img{
            width:55px;
            height:55px;
            border-radius:50%;
            overflow:hidden;
            background:white;
        }

        .profile-img img{
            width:100%;
            height:100%;
            object-fit:cover;
        }

        /* ================= HERO ================= */

        .hero{
            margin-top:80px;
        }

        .hero-img{
            width:100%;
            height:55vh;
            object-fit:cover;
            border-radius:0 0 120px 120px;
            box-shadow:0 15px 40px rgba(0,0,0,0.3);
        }

        /* ================= CARD ================= */

        .card-section{
            margin-top:-90px;
            position:relative;
            z-index:10;
            padding:0 40px;
        }

        .card-container{
            display:flex;
            justify-content:center;
            gap:30px;
            flex-wrap:wrap;
        }

        .card-link{
            text-decoration:none;
            color:inherit;
        }

        .about-card{
            width:280px;
            background:white;
            border-radius:15px;
            padding:15px;
            box-shadow:0 5px 15px rgba(0,0,0,0.1);
            transition:0.3s;
            cursor:pointer;
        }

        .about-card:hover{
            transform:translateY(-8px);
            box-shadow:0 15px 25px rgba(0,0,0,0.15);
        }

        .about-card img{
            width:100%;
            height:160px;
            object-fit:cover;
            border-radius:10px;
            margin-bottom:15px;
        }

        .about-card h3{
            text-align:center;
            margin-bottom:10px;
            color:#1e3d8f;
        }

        .about-card p{
            text-align:center;
            color:#666;
            line-height:1.6;
            font-size:14px;
        }

        .laporan-card{
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            min-height:320px;
        }

        .jumlah-laporan{
            font-size:70px;
            font-weight:800;
            color:#1e3d8f;
            line-height:1;
            margin-bottom:15px;
        }

        /* ================== FOOTER ================== */

.main-footer {
  background: linear-gradient(165deg, #080e18 0%, #102647 70%, #9c7719 120%);
  color: #fff;
  padding: 60px 70px;
  font-family: Arial, sans-serif;
  margin-top: 80px;
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
}

.card-link{
  text-decoration:none;
  color:inherit;
}

.card-link:hover{
  color:inherit;
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

        /* ================= RESPONSIVE ================= */

        @media(max-width:768px){

            .navbar{
                flex-direction:column;
                gap:15px;
                padding:20px;
            }

            .navlinks{
                margin-left:0;
            }

            .admin-profile{
                margin-left:0;
            }

            .hero{
                margin-top:170px;
            }

            .hero-img{
                height:40vh;
            }

            .card-section{
                margin-top:-50px;
            }
        }

        .laporan-header{
    height:90px;
    display:flex;
    justify-content:center;
    align-items:center;
}

.laporan-header i{
    font-size:70px;
    color:#1e3d8f;
}

.jumlah-laporan{
    font-size:55px;
    font-weight:800;
    color:#1e3d8f;
    margin-bottom:10px;
}
.laporan-card h3,
.laporan-card p{
    position: relative;
    top: 10px;
}

    </style>
</head>

<body>





    <!-- NAVBAR -->

    <header class="navbar">

        <div class="logo">
            <img src="../ASSETS/LOGO.png" alt="Logo">
        </div>

        <nav class="navlinks">
            <a href="Beranda_Admin.php" class="active">Beranda</a>
            <a href="Peringkat.php">Peringkat</a>
            <a href="Tentang.php">Tentang</a>
        </nav>

        <div class="admin-profile" id="navAdminUser" style="cursor:pointer;">

            <span>
                <?php echo htmlspecialchars($admin); ?>
            </span>

            <div class="profile-img">
                <img src="../ASSETS/USER.png" alt="">
            </div>

        </div>

    </header>

    <!-- HERO -->

    <section class="hero">
        <img src="../ASSETS/Main Background.jpg" class="hero-img" alt="">
    </section>

    <!-- CARD -->

    <section class="card-section">

        <div class="card-container">

            <!-- LAPORAN -->

            

          <a href="Kelola_Laporan.php" class="card-link">

              <div class="about-card laporan-card">

              

            <div class="laporan-header">
    <i class="fa-solid fa-file-lines"></i>
</div>

<div class="jumlah-laporan">
    <?php echo $total_laporan; ?>
</div>

        <h3>LAPORAN</h3>

        <p>
            Total seluruh laporan masyarakat yang telah masuk ke sistem Aksi Kita.
        </p>

    </div>

</a>

            <!-- STATISTIK -->

            <a href="Statistika.php" class="card-link">

                <div class="about-card">

                    <img src="../ASSETS/Statistika Laporan.jpg" alt="">

                    <h3>STATISTIKA LAPORAN</h3>

                    <p>
                        Menampilkan hasil analisis dan perkembangan laporan masyarakat.
                    </p>

                </div>

            </a>

            <!-- INFO -->

            <a href="INFO_PENTING_ADMIN/ADMIN_INFO.php" class="card-link">

                <div class="about-card">

                    <img src="../ASSETS/Info Penting.png" alt="">

                    <h3>INFO PENTING</h3>

                    <p>
                        Informasi dan pengumuman penting yang dikelola langsung oleh admin.
                    </p>

                </div>

            </a>

        </div>

    </section>

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

  <div class="footer-bottom">
    © 2026 AksiKita. Semua Hak Dilindungi.
  </div>

</footer>

<?php include 'profile_modal_admin.php'; ?>
</body>
</html>
