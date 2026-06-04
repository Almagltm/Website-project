<?php
/**
 * Users/login_user.php
 * Halaman login untuk pengguna (user)
 */
require_once '../db.php';

// Jika sudah login sebagai user, redirect ke beranda user
if (isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
    header('Location: BERANDA2.php');
    exit;
}
// Jika sudah login sebagai admin, redirect ke admin
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: ../Admin/login_admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan kata sandi wajib diisi.';
    } else {
        $stmt = $conn->prepare("SELECT id_user, nama_lengkap, email, password, kecamatan FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            // Login berhasil
            $_SESSION['role']         = 'user';
            $_SESSION['id_user']      = $user['id_user'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['email']        = $user['email'];
            $_SESSION['kecamatan']    = $user['kecamatan']; // TAMBAHAN

            logActivity('user', $user['id_user'], $user['nama_lengkap'], 'Login User', 'Login berhasil dari halaman user');
            header('Location: BERANDA2.php');
            exit;
        } else {
            $error = 'Email atau kata sandi salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Masuk Pengguna – Aksi Kita</title>
  <meta name="description" content="Masuk sebagai pengguna Aksi Kita untuk melaporkan kerusakan fasilitas umum."/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{
      font-family:'Poppins',sans-serif;
      min-height:100vh;
      background:linear-gradient(rgba(0,0,0,0.5),rgba(0,0,0,0.5)),
                 url('../ASSETS/Main Background.jpg') center/cover no-repeat;
      display:flex;align-items:center;justify-content:center;
      padding:20px;
    }
    .top-logo{
      position:fixed;top:22px;left:30px;
      display:flex;align-items:center;gap:10px;text-decoration:none;z-index:10;
    }
    .top-logo img{height:44px;}
    .top-logo span{color:#fff;font-weight:700;font-size:20px;}

    .glass{
      background:rgba(255,255,255,.14);
      backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);
      border:1px solid rgba(255,255,255,.28);
      border-radius:24px;
      padding:40px 36px;
      width:100%;max-width:420px;
      color:#fff;
      box-shadow:0 20px 60px rgba(0,0,0,.4);
      animation:fadeUp .4s ease;
    }
    @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

    .role-badge{
      display:inline-flex;align-items:center;gap:8px;
      background:rgba(30,61,143,.6);
      border:1px solid rgba(43,95,196,.5);
      padding:6px 16px;border-radius:30px;
      font-size:12px;font-weight:600;
      margin-bottom:22px;
    }

    h2{font-size:26px;font-weight:700;margin-bottom:6px;}
    .subtitle{font-size:13.5px;color:rgba(255,255,255,.72);margin-bottom:28px;}
    .subtitle a{color:#ffd54f;text-decoration:none;font-weight:500;}
    .subtitle a:hover{text-decoration:underline;}

    .alert-err{
      background:rgba(220,38,38,.25);border:1px solid rgba(220,38,38,.5);
      color:#fecaca;border-radius:10px;padding:12px 16px;
      font-size:13px;margin-bottom:20px;
      display:flex;gap:8px;align-items:center;
    }

    .fg{margin-bottom:22px;}
    .fg label{display:block;font-size:12.5px;font-weight:600;margin-bottom:8px;
      text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.8);}
    .inp-wrap{position:relative;}
    .inp-wrap i{position:absolute;left:14px;top:50%;transform:translateY(-50%);
      color:rgba(255,255,255,.6);font-size:14px;}
    .fg input{
      width:100%;padding:13px 14px 13px 42px;
      background:rgba(255,255,255,.1);
      border:1.5px solid rgba(255,255,255,.25);
      border-radius:12px;color:#fff;font-size:14px;
      font-family:'Poppins',sans-serif;
      transition:border-color .2s,background .2s;
    }
    .fg input::placeholder{color:rgba(255,255,255,.5);}
    .fg input:focus{outline:none;border-color:rgba(255,255,255,.7);background:rgba(255,255,255,.18);}

    .pw-toggle{position:absolute;right:14px;top:50%;transform:translateY(-50%);
      cursor:pointer;color:rgba(255,255,255,.6);font-size:14px;border:none;background:none;}
    .pw-toggle:hover{color:#fff;}

    .btn-submit{
      width:100%;padding:14px;border:none;border-radius:12px;
      background:linear-gradient(135deg,#1e3d8f,#2b5fc4);
      color:#fff;font-size:15px;font-weight:700;
      font-family:'Poppins',sans-serif;cursor:pointer;
      transition:transform .2s,box-shadow .2s;
      display:flex;align-items:center;justify-content:center;gap:8px;
      box-shadow:0 4px 20px rgba(30,61,143,.5);
      margin-top:8px;
    }
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(30,61,143,.6);}
    .btn-submit:active{transform:translateY(0);}

    .divider{display:flex;align-items:center;gap:12px;margin:22px 0;color:rgba(255,255,255,.5);font-size:12px;}
    .divider::before,.divider::after{content:'';flex:1;height:1px;background:rgba(255,255,255,.2);}

    .back-links{text-align:center;margin-top:20px;font-size:13px;color:rgba(255,255,255,.65);}
    .back-links a{color:#ffd54f;text-decoration:none;font-weight:500;}
    .back-links a:hover{text-decoration:underline;}
  </style>
</head>
<body>

  <a href="../login.php" class="top-logo">
    <img src="../ASSETS/LOGO.png" alt="Aksi Kita">
    <span>Aksi Kita</span>
  </a>

  <div class="glass">
    <div class="role-badge"><i class="fa-solid fa-user"></i> Pengguna / User</div>
    <h2>Selamat Datang!</h2>
    <p class="subtitle">Belum punya akun? <a href="DAFTAR.php">Daftar sekarang</a></p>

    <?php if ($error): ?>
    <div class="alert-err"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" id="loginForm">

      <div class="fg">
        <label>Alamat Email</label>
        <div class="inp-wrap">
          <i class="fa-solid fa-envelope"></i>
          <input type="email" name="email" id="email" placeholder="contoh@email.com" required
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
        </div>
      </div>

      <div class="fg">
        <label>Kata Sandi</label>
        <div class="inp-wrap">
          <i class="fa-solid fa-lock"></i>
          <input type="password" name="password" id="password" placeholder="Masukkan kata sandi" required/>
          <button type="button" class="pw-toggle" id="pwToggle" onclick="togglePw()">
            <i class="fa-solid fa-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-submit" id="btnLogin">
        <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk
      </button>
    </form>

    <div class="divider">atau</div>

    <div class="back-links">
      <a href="../login.php"><i class="fa-solid fa-chevron-left"></i> Kembali ke Pilih Peran</a>
    </div>
  </div>

<script>
function togglePw() {
  const inp = document.getElementById('password');
  const ico = document.getElementById('eyeIcon');
  if (inp.type === 'password') {
    inp.type = 'text';
    ico.className = 'fa-solid fa-eye-slash';
  } else {
    inp.type = 'password';
    ico.className = 'fa-solid fa-eye';
  }
}
document.getElementById('loginForm').addEventListener('submit', function() {
  const btn = document.getElementById('btnLogin');
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
  btn.disabled = true;
});
</script>
</body>
</html>
