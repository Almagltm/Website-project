<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit();
}

$admin = $_SESSION['nama_admin'];

/* ==========================================
   LOGIKA NOTIFIKASI YANG DIPANGGIL NAVBAR
   ========================================== */
// 1. Ambil 5 laporan terbaru yang statusnya masih 'pending'
$query_noti = mysqli_query($conn, "
    SELECT id_laporan, judul, created_at 
    FROM laporan 
    WHERE status = 'pending' 
    ORDER BY created_at DESC 
    LIMIT 5
");

$noti_items = [];
if ($query_noti) {
    while ($row = mysqli_fetch_assoc($query_noti)) {
        // Potong judul jika terlalu panjang agar dropdown tetap rapi
        $judul_pendek = strlen($row['judul']) > 40 ? substr($row['judul'], 0, 37) . '...' : $row['judul'];
        
        $noti_items[] = [
            'link'  => 'detail_laporan.php?id=' . $row['id_laporan'],
            'title' => 'Laporan Baru Masuk',
            'desc'  => htmlspecialchars($judul_pendek),
            'time'  => date('d M Y H:i', strtotime($row['created_at']))
        ];
    }
}

// 2. Hitung total seluruh laporan pending untuk angka badge merah
$noti_count = 0;
$count_res = mysqli_query($conn, "SELECT COUNT(*) as c FROM laporan WHERE status = 'pending'");
if ($count_res) {
    $r_count = mysqli_fetch_assoc($count_res);
    $noti_count = (int)$r_count['c'];
}

/* AMBIL DATA TENTANG */
$tentang = mysqli_query($conn, "SELECT * FROM tentang LIMIT 1");
$data_tentang = mysqli_fetch_assoc($tentang);

/* AMBIL VIDEO */
$video = mysqli_query($conn, "SELECT * FROM tentang_video LIMIT 1");
$data_video = mysqli_fetch_assoc($video);

/* AMBIL TIM */
$tim = mysqli_query($conn, "SELECT * FROM tim_pengembang");
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Aksi Kita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
    background-color: #f5f5f5;
    color: #333;
}

/* PERBAIKAN CONTAINER: Digabung & diberi padding-top agar tidak tertutup navbar fixed */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 120px 40px 40px 40px; 
}

.section-title {
    text-align: center;
    margin-bottom: 30px;
    font-size: 2em;
    font-weight: 700;
    color: #333;
}

/* ================= NAVBAR (Disamakan Persis Dengan Beranda Admin) ================= */
.navbar{background:#1e3d8f;color:#fff;display:flex;align-items:center;padding:13px 50px;position:fixed;top:0;left:0;right:0;z-index:1000;gap:10px;box-shadow:0 4px 16px rgba(0,0,0,.2);}
.logo img{height:50px;}

.navlinks{
    display:flex;
    margin-left:60px;
    flex-wrap: wrap;
}

.navlinks a{
    color:white;
    text-decoration:none;
    margin: 0 15px;
    padding: 5px 0;
    font-weight:500;
    transition:0.3s;
    font-size: 16px;
}

.navlinks a:hover{
    color:#ffd54f;
}

.navlinks a.active{
    text-decoration:underline;
}

.nav-right{display:flex;align-items:center;gap:14px;margin-left:auto;}
.nav-admin{display:flex;align-items:center;gap:10px;cursor:pointer;}
.nav-admin span{color:#fff;font-size:15px;font-weight:500;}
.nav-admin img{height:36px;width:36px;border-radius:50%;border:2px solid rgba(255,255,255,.4);}
.noti-wrap{position:relative;display:flex;align-items:center;}
.noti-btn{cursor:pointer;width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;transition:.2s;}
.noti-btn:hover{background:rgba(255,255,255,.22);}
.noti-badge{position:absolute;top:-3px;right:-3px;background:#e63946;color:#fff;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;border:2px solid #1e3d8f;}
.noti-dropdown{position:absolute;top:50px;right:0;background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(15,23,42,.15);width:300px;border:1px solid #e2e8f0;display:none;z-index:9999;overflow:hidden;}

/* STYLE AREA CONTENT ADMIN */
.section {
    background: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,.08);
}

.section h2 {
    color: #1e3d8f;
    margin-bottom: 20px;
    font-size: 1.5em;
    border-bottom: 2px solid #eee;
    padding-bottom: 8px;
}

input[type="text"], textarea {
    width: 100%;
    padding: 12px;
    margin-top: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    outline: none;
    transition: 0.3s;
}

input[type="text"]:focus, textarea:focus {
    border-color: #1e3d8f;
}

textarea {
    height: 120px;
    resize: vertical;
}

label {
    font-weight: 600;
    font-size: 14px;
    color: #444;
}

button {
    padding: 10px 24px;
    background: #1e3d8f;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 6px;
    font-weight: 500;
    transition: 0.3s;
}

button:hover {
    background: #142962;
    transform: translateY(-2px);
}

/* TIM CONTAINER & CARD MANAGEMENT */
.tim-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
    gap: 20px;
}

.team-member-card {
    background-color: white;
    padding: 20px 15px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column; 
    align-items: center; 
    width: calc(25% - 15px);
    min-width: 220px;
    margin-bottom: 10px;
    transition: transform 0.3s, box-shadow 0.3s;
    text-align: center;
    border: 1px solid #eee;
}

.team-member-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

.profile-photo-real {
    width: 100px; 
    height: 100px; 
    border-radius: 50%; 
    object-fit: cover; 
    margin: 0 auto 12px; 
    border: 3px solid #1e3d8f; 
}

.team-member-card h4 {
    font-size: 15px;
    font-weight: 600;
    color: #1e3d8f;
    margin-bottom: 5px;
}

.team-member-card p {
    font-size: 13px;
    color: #666;
    margin-bottom: 10px;
}

.team-member-card small {
    font-size: 12px;
    color: #000000;
    background-color: #ADD8E6; 
    padding: 6px 12px;
    border-radius: 5px;
    display: inline-block;
    margin-bottom: 15px;
    font-weight: 500;
}

.card-actions {
    margin-top: auto;
    font-size: 13px;
    border-top: 1px solid #eee;
    padding-top: 10px;
    width: 100%;
}

.card-actions a {
    text-decoration: none;
    color: #1e3d8f;
    font-weight: 500;
}

.card-actions a.delete-link {
    color: #e63946;
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
  .team-member-card {
    width: 100%;
  }
}
</style>
<body>

    <!-- NAVBAR COPAS DARI BERANDA ADMIN -->
    <header class="navbar">
        <div class="logo">
            <img src="../ASSETS/LOGO.png" alt="Logo">
        </div>

        <nav class="navlinks">
            <a href="Beranda_Admin.php">Beranda</a>
            <a href="peringkat_admin.php">Peringkat</a>
            <a href="tentang_admin.php" class="active">Tentang</a>
        </nav>

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
                <span><?= htmlspecialchars($admin) ?></span>
                <img src="../ASSETS/USER.png" alt="Admin"/>
            </div>
        </div>
    </header>
    
    <div class="container">

        <div class="section">
            <h2>Edit Tentang Aksi Kita</h2>

            <form action="update_tentang.php" method="POST" enctype="multipart/form-data">
                <label>Judul</label>
                <input type="text" name="judul" value="<?= $data_tentang['judul'] ?? '' ?>" placeholder="Judul">

                <label>Deskripsi</label>
                <textarea name="deskripsi" placeholder="Deskripsi..."><?= $data_tentang['deskripsi'] ?? '' ?></textarea>

                <label>Gambar Tentang</label>
                <input type="file" name="gambar" accept="image/*">
                <br><br>
                <?php if(!empty($data_tentang['gambar'])): ?>
                    <img src="../ASSETS/<?= $data_tentang['gambar'] ?>" style="width:200px;border-radius:10px;margin-bottom:15px;display:block;">
                <?php endif; ?>

                <button type="submit">Simpan</button>
            </form>
        </div>

        <div class="section">
            <h2>Edit Video Tentang</h2>

            <form action="update_video.php" method="POST" enctype="multipart/form-data">
                <label>Judul Video</label>
                <input type="text" name="judul" value="<?= $data_video['judul'] ?? '' ?>" placeholder="Judul Video">

                <label>Upload Video</label>
                <input type="file" name="video" accept="video/*">
                <br><br>
                <?php if(!empty($data_video['file_video'])): ?>
                    <video width="300" controls style="border-radius:10px;margin-bottom:15px;display:block;">
                        <source src="../ASSETS/<?= $data_video['file_video'] ?>">
                    </video>
                <?php endif; ?>

                <label>Deskripsi Video</label>
                <textarea name="deskripsi" placeholder="Deskripsi video..."><?= $data_video['deskripsi'] ?? '' ?></textarea>

                <button type="submit">Update Video</button>
            </form>
        </div>

        <div class="section">
            <h2>Kelola Tim Pengembang</h2>

            <a href="tambah_tim.php"><button style="margin-bottom: 20px;">+ Tambah Anggota</button></a>

            <div class="tim-container">
                <?php while($row = mysqli_fetch_assoc($tim)): ?>
                    <div class="team-member-card">
                        <img src="../ASSETS/<?= $row['foto'] ?>" class="profile-photo-real">
                        <h4><?= $row['nama'] ?></h4>
                        <p><?= $row['nim'] ?></p>
                        <small><?= $row['tugas'] ?></small>

                        <div class="card-actions">
                            <a href="edit_tim.php?id=<?= $row['id'] ?>">Edit</a> | 
                            <a href="hapus_tim.php?id=<?= $row['id'] ?>" class="delete-link" onclick="return confirm('Yakin ingin menghapus anggota tim ini?')">Hapus</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

    </div> 
    
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

    <?php include 'profile_modal_admin.php'; ?>

    <!-- SCRIPT COPAS DARI BERANDA ADMIN (DITAMBAH EVENT MODAL PROFIL) -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const nb = document.getElementById('notiBellBtn');
        const nd = document.getElementById('notiDropdown');
        const navAdminUser = document.getElementById("navAdminUser");
        const profileModal = document.getElementById("profileModal");

        // Cek apakah elemen pembentuk notifikasi ada di halaman
        if (nb && nd) {
            nb.addEventListener('click', function(e) {
                e.stopPropagation();
                
                // Cek status display saat ini
                if (nd.style.display === 'block') {
                    nd.style.display = 'none';
                } else {
                    nd.style.display = 'block';
                }
                console.log("Tombol notifikasi berhasil diklik. Status dropdown:", nd.style.display);
            });

            document.addEventListener('click', function(e) {
                if (!nd.contains(e.target) && !nb.contains(e.target)) {
                    nd.style.display = 'none';
                }
            });
        } else {
            console.error("Error: Elemen 'notiBellBtn' atau 'notiDropdown' tidak ditemukan di halaman ini!");
        }

        // Cek dan jalankan modal profil
        if (navAdminUser && profileModal) {
            navAdminUser.addEventListener("click", function(e) {
                e.stopPropagation();
                profileModal.style.display = "flex";
                console.log("Modal profil dibuka.");
            });
        }
    });
    </script>
</body>
</html>