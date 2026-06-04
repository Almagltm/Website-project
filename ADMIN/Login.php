<?php
session_start();
include '../koneksi.php';

/* Jika admin sudah login */
if (isset($_SESSION['admin_id'])) {
    header("Location: Beranda_Admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama_admin = trim($_POST['nama_admin']);
    $password   = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE nama_admin = ?");
    $stmt->bind_param("s", $nama_admin);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {

            $_SESSION['admin_id']   = $admin['id_admin'];
            $_SESSION['nama_admin'] = $admin['nama_admin'];

            echo "<script>
                    alert('Login Admin berhasil!');
                    window.location.href='Beranda_Admin.php';
                  </script>";
            exit();

        } else {

            echo "<script>
                    alert('Kata sandi salah!');
                  </script>";
        }

    } else {

        echo "<script>
                alert('Admin tidak ditemukan!');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Aksi Kita</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:"Poppins",sans-serif;
        }

        body{
            background:
            linear-gradient(
                rgba(0,0,0,0.4),
                rgba(0,0,0,0.4)
            ),
            url('../ASSETS/Main Background.jpg')
            no-repeat center center/cover;

            height:100vh;
            width:100vw;

            display:flex;
            justify-content:center;
            align-items:center;

            position:relative;
        }

        .top-logo{
            position:absolute;
            top:20px;
            left:30px;
            width:120px;
            z-index:10;
        }

        .glass-panel{
            background:rgba(255,255,255,0.15);
            backdrop-filter:blur(12px);
            -webkit-backdrop-filter:blur(12px);

            border:1px solid rgba(255,255,255,0.3);
            border-radius:20px;

            padding:30px;
            width:100%;
            max-width:400px;

            box-shadow:0 8px 32px rgba(0,0,0,0.37);

            color:#fff;
            text-align:center;
        }

        .admin-badge{
            width:70px;
            height:70px;

            background:#1e3d8f;

            border-radius:50%;

            display:flex;
            justify-content:center;
            align-items:center;

            margin:0 auto 15px auto;

            font-size:32px;

            box-shadow:0 5px 20px rgba(0,0,0,0.3);
        }

        .glass-panel h2{
            font-size:24px;
            margin-bottom:25px;
        }

        .input-group{
            margin-bottom:20px;
            text-align:left;
        }

        .input-group label{
            display:block;
            margin-bottom:5px;
            font-size:14px;
            font-weight:500;
        }

        .input-group input{
            width:100%;
            padding:10px 0;

            background:transparent;
            border:none;
            border-bottom:2px solid rgba(255,255,255,0.6);

            color:white;
            font-size:16px;
            outline:none;
        }

        .input-group input::placeholder{
            color:rgba(255,255,255,0.7);
        }

        .input-group input:focus{
            border-bottom:2px solid #fff;
        }

        .btn-submit{
            width:100%;
            padding:12px;

            border:none;
            border-radius:8px;

            background:#1e3d8f;
            color:white;

            font-size:16px;
            font-weight:600;

            cursor:pointer;
            transition:.3s;
        }

        .btn-submit:hover{
            background:#244a9a;
        }

        .remember-forgot{
            display:flex;
            justify-content:space-between;
            align-items:center;

            font-size:13px;

            margin-bottom:25px;
            color:#e5e5e5;
        }

        .remember-forgot a{
            color:#ffd54f;
            text-decoration:none;
        }

        .remember-forgot a:hover{
            text-decoration:underline;
        }
    </style>
</head>

<body>

    <a href="../BERANDA1.html">
        <img src="../ASSETS/LOGO.png"
             alt="Aksi Kita Logo"
             class="top-logo">
    </a>

    <div class="glass-panel">

        <div class="admin-badge">
            🛡️
        </div>

        <h2>Masuk Sebagai Admin</h2>

        <form method="POST">

            <div class="input-group">
                <label>Nama Pengguna</label>
                <input
                    type="text"
                    name="nama_admin"
                    placeholder="Masukkan nama pengguna"
                    required>
            </div>

            <div class="input-group">
                <label>Kata Sandi</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan kata sandi"
                    required>
            </div>

            <div class="remember-forgot">
                <label>
                    <input type="checkbox">
                    Ingat Saya
                </label>

                <a href="#">Lupa Kata Sandi?</a>
            </div>

            <button type="submit" class="btn-submit">
                MASUK
            </button>

        </form>

    </div>

</body>
</html>
