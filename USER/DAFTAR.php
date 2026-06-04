<?php
session_start();
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$nama_lengkap = $_POST['nama_lengkap'];
$email = $_POST['email'];
$no_telp = $_POST['no_telp'];
$kecamatan = $_POST['kecamatan'];

$password = password_hash(
    $_POST['password'],
    PASSWORD_DEFAULT
);

$stmt = $conn->prepare("
INSERT INTO users
(nama_lengkap, email, password, no_telp, kecamatan)
VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "sssss",
    $nama_lengkap,
    $email,
    $password,
    $no_telp,
    $kecamatan
);

    if ($stmt->execute()) {
        echo "<script>alert('Pendaftaran berhasil! Silakan login.'); window.location.href='MASUK.php';</script>";
    } else {
        echo "<script>alert('Pendaftaran gagal!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - AksiKita</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            /* Background image with a slight dark overlay to make white text pop */
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('../ASSETS/Main Background.jpg') no-repeat center center/cover;
            height: 100vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* Top left logo */
        .top-logo {
            position: absolute;
            top: 20px;
            left: 30px;
            width: 120px;
            z-index: 10;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            color: #fff;
            text-align: center;
        }

        /* Scrollbar Styling for Glass Panel */
        .glass-panel::-webkit-scrollbar {
            width: 6px;
        }
        .glass-panel::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .glass-panel h2 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .glass-panel p {
            font-size: 14px;
            margin-bottom: 25px;
        }

        .glass-panel p a {
            color: #ffd54f; /* Yellow link color */
            text-decoration: none;
            font-weight: 500;
        }

        .glass-panel p a:hover {
            text-decoration: underline;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .input-group input[type="email"],
        .input-group input[type="password"],
        .input-group input[type="text"],
        .input-group select {
            width: 100%;
            padding: 8px 0;
            background: transparent;
            border: none;
            border-bottom: 2px solid rgba(255, 255, 255, 0.6);
            color: #fff;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s;
            cursor: pointer;
        }

        /* Tambahan agar isi dropdown bisa dibaca saat di-klik */
        .input-group select option {
            color: #000;
        }
            padding: 8px 0;
            background: transparent;
            border: none;
            border-bottom: 2px solid rgba(255, 255, 255, 0.6);
            color: #fff;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s;
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .input-group input:focus {
            border-bottom: 2px solid #fff;
        }



        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #1e3d8f; /* Blue from Beranda */
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-bottom: 20px;
        }

        .btn-submit:hover {
            background: #244a9a;
        }

        .separator-box {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }

        .line {
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.4);
        }

        .atau {
            margin: 0 15px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
        }

        .socials {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .social-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .social-icon:hover {
            transform: scale(1.1);
        }

        /* Checkbox hack for show/hide password */
        .pw-wrap {
            position: relative;
        }
        
        #showPw {
            display: none;
        }
        
        #showPw:checked ~ .password {
            -webkit-text-security: none !important;
            text-security: none !important;
        }
        
        .password {
            -webkit-text-security: disc;
            text-security: disc;
        }
    </style>
</head>
<body>

    <!-- Logo Top Left -->
    <a href="MASUK.php"><img src="../ASSETS/LOGO.png" alt="Aksi Kita Logo" class="top-logo"></a>

    <!-- Glassmorphism Form Container -->
    <div class="glass-panel">
        <h2>Daftar</h2>
        <p>Sudah punya akun? <a href="MASUK.php">Masuk</a></p>

        <form action="" method="POST">
            
            <div class="input-group">
                <label>Nama</label>
                <input type="text" name="nama_lengkap" placeholder="Masukkan nama" required>
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Masukkan email" required pattern=".*@gmail\.com">
            </div>

            <div class="input-group">
                <label>No. Telepon</label>
                <input type="text" name="no_telp" placeholder="Masukkan no. telepon" required>
            </div>

            <div class="input-group">
                <label>Kecamatan Domisili</label>
                <select name="kecamatan" required>
                    <option value="" disabled selected>— Pilih Kecamatan —</option>
                    <option value="Medan Amplas">Medan Amplas</option>
                    <option value="Medan Area">Medan Area</option>
                    <option value="Medan Barat">Medan Barat</option>
                    <option value="Medan Baru">Medan Baru</option>
                    <option value="Medan Belawan">Medan Belawan</option>
                    <option value="Medan Deli">Medan Deli</option>
                    <option value="Medan Denai">Medan Denai</option>
                    <option value="Medan Helvetia">Medan Helvetia</option>
                    <option value="Medan Johor">Medan Johor</option>
                    <option value="Medan Kota">Medan Kota</option>
                    <option value="Medan Labuhan">Medan Labuhan</option>
                    <option value="Medan Maimun">Medan Maimun</option>
                    <option value="Medan Marelan">Medan Marelan</option>
                    <option value="Medan Perjuangan">Medan Perjuangan</option>
                    <option value="Medan Petisah">Medan Petisah</option>
                    <option value="Medan Polonia">Medan Polonia</option>
                    <option value="Medan Selayang">Medan Selayang</option>
                    <option value="Medan Sunggal">Medan Sunggal</option>
                    <option value="Medan Tembung">Medan Tembung</option>
                    <option value="Medan Timur">Medan Timur</option>
                    <option value="Medan Tuntungan">Medan Tuntungan</option>
                </select>
            </div>

            <div class="input-group pw-wrap">
                <label>Kata Sandi</label>
                <input id="showPw" type="checkbox">
                <input type="password" name="password" class="password" placeholder="Buat kata sandi" required>
            </div>

            <button type="submit" class="btn-submit">Daftar</button>

            <div class="separator-box">
                <div class="line"></div>
                <span class="atau">atau daftar dengan</span>
                <div class="line"></div>
            </div>

            <div class="socials">
                <a href="https://www.facebook.com" target="_blank" title="Facebook">
                    <svg class="social-icon" viewBox="0 0 24 24" fill="#1877F2" style="background: white;">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>
                <a href="https://accounts.google.com" target="_blank" title="Google">
                    <svg class="social-icon" viewBox="0 0 48 48" style="background: white; border-radius: 50%; padding: 4px;">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.7 17.74 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    </svg>
                </a>
            </div>

        </form>
    </div>

</body>
</html>
