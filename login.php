<?php
// Inisialisasi session
if (session_status() === PHP_SESSION_NONE) session_start();

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: Admin/Beranda_Admin.php');
    } else {
        header('Location: Users/BERANDA2.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Masuk – Aksi Kita</title>
  <meta name="description" content="Pilih peran Anda untuk masuk ke Aksi Kita."/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{
      font-family:'Poppins',sans-serif;
      min-height:100vh;
      background:linear-gradient(rgba(0,0,0,0.55),rgba(0,0,0,0.55)),
                 url('ASSETS/Main Background.jpg') center/cover no-repeat;
      display:flex;flex-direction:column;align-items:center;justify-content:center;
      padding:20px;
    }

    /* Logo */
    .top-logo{
      position:fixed;top:22px;left:30px;
      display:flex;align-items:center;gap:10px;text-decoration:none;z-index:10;
    }
    .top-logo img{height:44px;}
    .top-logo span{color:#fff;font-weight:700;font-size:20px;letter-spacing:.5px;}

    /* Title */
    .page-title{text-align:center;color:#fff;margin-bottom:40px;}
    .page-title h1{font-size:32px;font-weight:700;margin-bottom:8px;}
    .page-title p{font-size:15px;color:rgba(255,255,255,.75);}

    /* Role Cards */
    .role-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;width:100%;max-width:580px;}
    @media(max-width:500px){.role-grid{grid-template-columns:1fr;}}

    .role-card{
      background:rgba(255,255,255,.12);
      backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);
      border:1px solid rgba(255,255,255,.25);
      border-radius:20px;
      padding:36px 24px;
      text-align:center;
      text-decoration:none;
      color:#fff;
      transition:transform .3s, background .3s, box-shadow .3s;
      cursor:pointer;
      display:flex;flex-direction:column;align-items:center;gap:14px;
    }
    .role-card:hover{
      transform:translateY(-6px);
      background:rgba(255,255,255,.22);
      box-shadow:0 20px 50px rgba(0,0,0,.4);
    }
    .role-card .icon{
      width:72px;height:72px;border-radius:50%;
      display:flex;align-items:center;justify-content:center;
      font-size:28px;color:#fff;
      transition:transform .3s;
    }
    .role-card:hover .icon{transform:scale(1.1);}
    .role-card.user .icon{background:linear-gradient(135deg,#2b5fc4,#1e3d8f);}
    .role-card.admin .icon{background:linear-gradient(135deg,#e63946,#c1121f);}
    .role-card h2{font-size:20px;font-weight:700;}
    .role-card p{font-size:13px;color:rgba(255,255,255,.75);line-height:1.5;}
    .role-card .arrow{
      display:inline-flex;align-items:center;gap:6px;
      padding:8px 20px;border-radius:30px;font-size:13px;font-weight:600;
      margin-top:4px;transition:background .3s;
    }
    .role-card.user .arrow{background:#1e3d8f;}
    .role-card.admin .arrow{background:#c1121f;}
    .role-card:hover .arrow{filter:brightness(1.2);}

    .back-link{
      margin-top:28px;
      color:rgba(255,255,255,.65);font-size:13px;text-align:center;
    }
    .back-link a{color:#ffd54f;text-decoration:none;font-weight:500;}
    .back-link a:hover{text-decoration:underline;}
  </style>
</head>
<body>

  <a href="Users/BERANDA2.php" class="top-logo">
    <img src="ASSETS/LOGO.png" alt="Aksi Kita">
    <span>Aksi Kita</span>
  </a>

  <div class="page-title">
    <h1>Selamat Datang!</h1>
    <p>Pilih peran Anda untuk melanjutkan</p>
  </div>

  <div class="role-grid">
    <!-- USER -->
    <a href="Users/MASUK.php" class="role-card user">
      <div class="icon"><i class="fa-solid fa-user"></i></div>
      <h2>Pengguna</h2>
      <p>Login sebagai warga untuk melaporkan kerusakan fasilitas umum</p>
      <span class="arrow"><i class="fa-solid fa-arrow-right"></i> Masuk</span>
    </a>

    <!-- ADMIN -->
    <a href="Admin/Login.php" class="role-card admin">
      <div class="icon"><i class="fa-solid fa-user-shield"></i></div>
      <h2>Administrator</h2>
      <p>Login sebagai admin untuk mengelola dan memverifikasi laporan</p>
      <span class="arrow"><i class="fa-solid fa-arrow-right"></i> Masuk</span>
    </a>
  </div>

  <p class="back-link">
    Belum punya akun? <a href="Users/DAFTAR.php">Daftar Sekarang</a>
  </p>

</body>
</html>
