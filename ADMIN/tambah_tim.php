<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit();
}

// Proses form ketika tombol submit ditekan
if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nim = mysqli_real_escape_string($conn, $_POST['nim']);
    $tugas = mysqli_real_escape_string($conn, $_POST['tugas']);
    
    // Logika upload file foto yang benar
    $nama_foto = $_FILES['foto']['name'];
    $tmp_foto = $_FILES['foto']['tmp_name'];
    
    if (!empty($nama_foto)) {
        // Pindahkan file ke folder ASSETS
        move_uploaded_file($tmp_foto, "../ASSETS/" . $nama_foto);
        $foto_final = $nama_foto;
    } else {
        $foto_final = "USER.png"; // Default jika tidak upload foto
    }

    mysqli_query($conn, "
        INSERT INTO tim_pengembang (nama, nim, foto, tugas)
        VALUES ('$nama', '$nim', '$foto_final', '$tugas')
    ");

    header("Location: tentang_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tim - Aksi Kita</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .box {
            background: white;
            width: 100%;
            max-width: 550px;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
        }

        .box h2 {
            color: #1e3d8f;
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 8px;
            text-align: center;
        }

        .box p {
            color: #64748b;
            font-size: 14px;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: #334155;
            margin-bottom: 8px;
        }

        /* Input Styling */
        input[type="text"], textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            color: #334155;
            outline: none;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        input[type="text"]:focus, textarea:focus {
            border-color: #1e3d8f;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(30, 61, 143, 0.15);
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        /* Custom File Input Decoration */
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
            cursor: pointer;
            font-size: 13px;
        }

        input[type="file"]:hover {
            border-color: #1e3d8f;
        }

        /* Button Grid Layout */
        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        button, .btn-back {
            flex: 1;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        button {
            background: #1e3d8f;
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(30, 61, 143, 0.25);
        }

        button:hover {
            background: #142962;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(30, 61, 143, 0.35);
        }

        .btn-back {
            background: #fff;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }

        .btn-back:hover {
            background: #f1f5f9;
            color: #334155;
            border-color: #94a3b8;
        }

        /* Preview Image Area */
        #preview-container {
            display: none;
            margin-top: 10px;
            text-align: center;
        }

        #preview-img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #1e3d8f;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Tambah Anggota Tim</h2>
    <p>Masukkan data detail developer atau pengembang sistem baru.</p>

    <form action="" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label><i class="fa-solid fa-image" style="margin-right: 6px; color:#1e3d8f;"></i> Foto Profil Anggota</label>
            <input type="file" name="foto" id="fotoInput" accept="image/*" required>
            <div id="preview-container">
                <img id="preview-img" src="#" alt="Preview">
            </div>
        </div>

        <div class="form-group">
            <label><i class="fa-solid fa-user" style="margin-right: 6px; color:#1e3d8f;"></i> Nama Lengkap</label>
            <input type="text" name="nama" placeholder="Contoh: Meguru Bachira" required autocomplete="off">
        </div>

        <div class="form-group">
            <label><i class="fa-solid fa-id-card" style="margin-right: 6px; color:#1e3d8f;"></i> NIM (Nomor Induk Mahasiswa)</label>
            <input type="text" name="nim" placeholder="Contoh: 240101xxx" required autocomplete="off">
        </div>

        <div class="form-group">
            <label><i class="fa-solid fa-briefcase" style="margin-right: 6px; color:#1e3d8f;"></i> Tugas / Role Job</label>
            <textarea name="tugas" placeholder="Contoh: Backend Developer - Manajemen Database & API" required></textarea>
        </div>

        <div class="btn-group">
            <a href="tentang_admin.php" class="btn-back">Batal</a>
            <button type="submit" name="simpan">Tambah Anggota</button>
        </div>

    </form>
</div>

<script>
    const fotoInput = document.getElementById('fotoInput');
    const previewContainer = document.getElementById('preview-container');
    const previewImg = document.getElementById('preview-img');

    fotoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            previewContainer.style.display = "block";
            reader.addEventListener('load', function() {
                previewImg.setAttribute('src', this.result);
            });
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = "none";
        }
    });
</script>

</body>
</html>