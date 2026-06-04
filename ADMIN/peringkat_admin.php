<?php
session_start();
// Menggunakan jalur file koneksi yang sama dengan Beranda Admin
include '../koneksi.php';

// Proteksi halaman admin
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


/* ==========================================
   LOGIKA PENGELOLAAN DATA PERINGKAT KECAMATAN
   ========================================== */
$message = "";

// A. PROSES UPDATE DATA UTAMA (Camat, Sekcam, Rating)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_kecamatan'])) {
    $id_kecamatan  = intval($_POST['id_kecamatan']);
    $nama_camat    = mysqli_real_escape_string($conn, $_POST['nama_camat']);
    $nama_sekcam   = mysqli_real_escape_string($conn, $_POST['nama_sekcam']);
    $rating_bintang = intval($_POST['rating_bintang']);

    if ($rating_bintang < 1) $rating_bintang = 1;
    if ($rating_bintang > 5) $rating_bintang = 5;

    $sql_update = "UPDATE kecamatan 
                   SET nama_camat = '$nama_camat', nama_sekcam = '$nama_sekcam', rating_bintang = $rating_bintang 
                   WHERE id_kecamatan = $id_kecamatan";

    if (mysqli_query($conn, $sql_update)) {
        $message = "<div class='alert success'><i class='fa-solid fa-circle-check'></i> Data utama kecamatan berhasil diperbarui!</div>";
    } else {
        $message = "<div class='alert error'><i class='fa-solid fa-circle-exclamation'></i> Gagal memperbarui data utama: " . mysqli_error($conn) . "</div>";
    }
}

// B. PROSES UPDATE DATA SELENGKAPNYA (Deskripsi, Penjelasan, Logo, & Foto Kondisi)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_selengkapnya'])) {
    $id_kecamatan  = intval($_POST['id_kecamatan']);
    $deskripsi     = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $penjelasan    = mysqli_real_escape_string($conn, $_POST['penjelasan']);
    
    // Ambil data lama untuk mempertahankan berkas lama jika tidak diganti oleh admin
    $q_lama = mysqli_query($conn, "SELECT logo_kecamatan, foto_kondisi FROM kecamatan WHERE id_kecamatan = $id_kecamatan");
    $d_lama = mysqli_fetch_assoc($q_lama);
    
    $path_logo = isset($d_lama['logo_kecamatan']) ? $d_lama['logo_kecamatan'] : '../ASSETS/LOGO.png';
    $path_kondisi = isset($d_lama['foto_kondisi']) ? $d_lama['foto_kondisi'] : '';

    $target_dir = "../ASSETS/";
    $valid_extensions = array("jpg", "jpeg", "png", "gif");

    // Logika upload File 1: Logo Kecamatan
    if (isset($_FILES['logo_kecamatan']) && $_FILES['logo_kecamatan']['error'] == 0) {
        $file_name_logo = time() . "_logo_" . basename($_FILES["logo_kecamatan"]["name"]);
        $target_file_logo = $target_dir . $file_name_logo;
        $imageFileTypeLogo = strtolower(pathinfo($target_file_logo, PATHINFO_EXTENSION));

        if (in_array($imageFileTypeLogo, $valid_extensions)) {
            if (move_uploaded_file($_FILES["logo_kecamatan"]["tmp_name"], $target_file_logo)) {
                $path_logo = "../ASSETS/" . $file_name_logo; 
            }
        }
    }

    // Logika upload File 2: Foto Kondisi / Kerusakan Kantor Kecamatan
    if (isset($_FILES['foto_kondisi']) && $_FILES['foto_kondisi']['error'] == 0) {
        $file_name_kondisi = time() . "_kondisi_" . basename($_FILES["foto_kondisi"]["name"]);
        $target_file_kondisi = $target_dir . $file_name_kondisi;
        $imageFileTypeKondisi = strtolower(pathinfo($target_file_kondisi, PATHINFO_EXTENSION));

        if (in_array($imageFileTypeKondisi, $valid_extensions)) {
            if (move_uploaded_file($_FILES["foto_kondisi"]["tmp_name"], $target_file_kondisi)) {
                $path_kondisi = "../ASSETS/" . $file_name_kondisi; 
            }
        }
    }

    // Query update data detil lengkap ke sistem database
    $sql_update_detail = "UPDATE kecamatan 
                          SET deskripsi = '$deskripsi', penjelasan = '$penjelasan', logo_kecamatan = '$path_logo', foto_kondisi = '$path_kondisi' 
                          WHERE id_kecamatan = $id_kecamatan";

    if (mysqli_query($conn, $sql_update_detail)) {
        $message = "<div class='alert success'><i class='fa-solid fa-circle-check'></i> Deskripsi, logo, dan foto kondisi lapangan berhasil disimpan!</div>";
    } else {
        $message = "<div class='alert error'><i class='fa-solid fa-circle-exclamation'></i> Gagal menyimpan detail: " . mysqli_error($conn) . "</div>";
    }
}

// Ambil data seluruh kecamatan se-Medan untuk diisi ke dalam tabel utama
$query_kec = mysqli_query($conn, "SELECT * FROM kecamatan ORDER BY nama_kecamatan ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peringkat - Aksi Kita</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

        /* ================= NAVBAR (Sama persis dengan Beranda Admin) ================= */
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

        /* ================= MANAGEMENT WORKSPACE CONTENT ================= */
        .workspace {
            margin-top: 110px; 
            padding: 0 50px;
            min-height: 70vh;
        }

        .header-box {
            background: white;
            padding: 24px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header-box h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1e3d8f;
        }

        .header-box p {
            margin-top: 4px;
            font-size: 14px;
            color: #666;
        }

        /* NOTIFIKASI PANELS */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }
        .alert.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* DATA WORKSPACE TABLE */
        .table-wrapper {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 18px;
            border-bottom: 2px solid #e2e8f0;
            font-size: 15px;
        }

        td {
            padding: 16px 18px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .cell-logo {
            width: 48px;
            height: 48px;
            object-fit: contain;
            background: #f1f5f9;
            border-radius: 8px;
            padding: 4px;
        }

        .stars-display {
            color: #ffc107;
            font-size: 17px;
        }

        /* ACTION BUTTON CONTROLS */
        .actions-group {
            display: flex;
            gap: 8px;
            justify-content: flex-start;
        }

        .btn-edit {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }
        .btn-edit:hover { background: #2563eb; transform: translateY(-1px); }

        .btn-more {
            background: #f59e0b;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }
        .btn-more:hover { background: #d97706; transform: translateY(-1px); }

        /* MODAL POPUP STYLE CONFIGURATIONS */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            padding: 20px;
            backdrop-filter: blur(2px);
        }

        .modal-box {
            background: white;
            width: 100%;
            max-width: 520px;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.15);
            overflow: hidden;
            animation: modalFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-header {
            background: #1e3d8f;
            color: white;
            padding: 18px 24px;
            font-size: 17px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header .close-modal {
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.7);
            font-size: 24px;
            cursor: pointer;
            line-height: 1;
        }
        .modal-header .close-modal:hover { color: white; }

        .modal-body { padding: 24px; max-height: 70vh; overflow-y: auto; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13.5px; font-weight: 500; margin-bottom: 8px; color: #334155; }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group textarea { resize: vertical; min-height: 80px; font-family: inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { 
            outline: none; border-color: #1e3d8f; box-shadow: 0 0 0 3px rgba(30,61,143,0.15); 
        }

        .img-preview-container {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f8fafc;
            padding: 12px;
            border-radius: 8px;
            margin-top: 5px;
            border: 1px dashed #cbd5e1;
        }
        .img-preview-container img { width: 60px; height: 60px; object-fit: contain; background: #fff; border-radius: 6px; border: 1px solid #e2e8f0; }

        .modal-footer {
            padding: 16px 24px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        .btn-submit { background: #10b981; color: white; border: none; padding: 10px 22px; border-radius: 8px; cursor: pointer; font-weight: 500; }
        .btn-submit:hover { background: #059669; }
        .btn-cancel { background: #e2e8f0; color: #475569; border: none; padding: 10px 22px; border-radius: 8px; cursor: pointer; }
        .btn-cancel:hover { background: #cbd5e1; }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95) translateY(-10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* ================== FOOTER (Sama persis dengan Beranda Admin) ================== */
        .main-footer {
          background: linear-gradient(165deg, #080e18 0%, #102647 70%, #9c7719 120%);
          color: #fff;
          padding: 60px 70px;
          margin-top: 80px;
        }

        .footer-top { display: flex; align-items: center; gap: 15px; margin-bottom: 40px; }
        .footer-logo { width: 75px; height: 55px; object-fit: cover; }
        .footer-content { display: flex; justify-content: space-between; flex-wrap: wrap; margin-bottom: 45px; gap: 20px; }
        .footer-col { flex: 1; min-width: 200px; }
        .footer-col p { margin: 6px 0; color: #ccc; font-size: 15px; }
        .footer-col a { display: block; margin: 6px 0; color: #eee; text-decoration: none; font-size: 15px; transition: 0.2s; }
        .footer-col a:hover { color: #0077ff; }
        .footer-social { display: flex; gap: 15px; margin-bottom: 35px; }
        
        .footer-social a {
          width: 40px; height: 40px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;
          color: #000; background: #fff; text-decoration: none; font-size: 18px; transition: 0.3s;
        }
        .footer-social a:hover { transform: translateY(-5px); }
        .footer-bottom { text-align: center; font-size: 14px; color: #ccc; }

        @media(max-width:768px){
            .navbar{ flex-direction:column; gap:15px; padding:20px; }
            .navlinks{ margin-left:0; }
            .nav-right{ margin-left:0; }
            .workspace { margin-top: 190px; padding: 0 20px; }
            .actions-group { flex-direction: column; gap: 5px; }
        }
    </style>
</head>

<body>

    <!-- NAVBAR UTAMA -->
    <header class="navbar">
        <div class="logo">
            <img src="../ASSETS/LOGO.png" alt="Logo">
        </div>

        <nav class="navlinks">
            <a href="Beranda_Admin.php">Beranda</a>
            <a href="peringkat_admin.php" class="active">Peringkat</a>
            <a href="tentang_admin.php">Tentang</a>
        </nav>

        <div class="nav-right">
            <!-- Lonceng Notifikasi Dropdown -->
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
            
            <!-- Identitas Admin -->
            <div class="nav-admin" id="navAdminUser">
                <span><?= htmlspecialchars($admin) ?></span>
                <img src="../ASSETS/USER.png" alt="Admin"/>
            </div>
        </div>
    </header>

    <!-- AREA WORKSPACE UTAMA -->
    <div class="workspace">
        
        <div class="header-box">
            <div>
                <h1>Kelola Peringkat Respon Kecamatan</h1>
                <p>Ubah penanggung jawab aparatur, performa rating bintang, serta penjelasan profil lengkap instansi kecamatan Kota Medan.</p>
            </div>
        </div>

        <!-- Alert Notifikasi Status Aksi -->
        <?php echo $message; ?>

        <!-- Tabel Data Utama Manajemen -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 90px; text-align:center;">Logo</th>
                        <th>Nama Wilayah</th>
                        <th>Nama Camat</th>
                        <th>Nama Sekcam</th>
                        <th>Rating Performa</th>
                        <th style="width: 280px; text-align: left;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query_kec) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($query_kec)): 
                            // Proteksi 'Undefined index' jika kolom belum dibuat di phpmyadmin
                            $val_deskripsi  = isset($row['deskripsi']) ? $row['deskripsi'] : '';
                            $val_penjelasan = isset($row['penjelasan']) ? $row['penjelasan'] : '';
                            $val_logo       = isset($row['logo_kecamatan']) ? $row['logo_kecamatan'] : '../ASSETS/LOGO.png';
                            $val_kondisi    = isset($row['foto_kondisi']) ? $row['foto_kondisi'] : '';
                        ?>
                            <tr>
                                <td style="text-align: center;">
                                    <img src="<?php echo htmlspecialchars($val_logo); ?>" class="cell-logo" alt="Logo">
                                </td>
                                <td><b>Kecamatan <?php echo htmlspecialchars($row['nama_kecamatan']); ?></b></td>
                                <td><?php echo htmlspecialchars($row['nama_camat']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_sekcam']); ?></td>
                                <td>
                                    <span class="stars-display">
                                        <?php 
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo ($i <= $row['rating_bintang']) ? "★" : "☆";
                                        }
                                        ?>
                                    </span>
                                    <span style="color: #64748b; font-size: 12px; margin-left: 5px;">(<?php echo $row['rating_bintang']; ?>/5)</span>
                                </td>
                                <td>
                                    <div class="actions-group">
                                        <!-- Tombol Edit Data Utama -->
                                        <button class="btn-edit" onclick="openEditModal(
                                            '<?php echo $row['id_kecamatan']; ?>',
                                            '<?php echo addslashes($row['nama_kecamatan']); ?>',
                                            '<?php echo addslashes($row['nama_camat']); ?>',
                                            '<?php echo addslashes($row['nama_sekcam']); ?>',
                                            '<?php echo $row['rating_bintang']; ?>'
                                        )">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit Utama
                                        </button>
                                        
                                        <!-- Tombol Edit Selengkapnya (Logo, Kondisi, Deskripsi, Penjelasan) -->
                                        <button class="btn-more" onclick="openMoreModal(
                                            '<?php echo $row['id_kecamatan']; ?>',
                                            '<?php echo addslashes($row['nama_kecamatan']); ?>',
                                            '<?php echo addslashes($val_logo); ?>',
                                            '<?php echo addslashes($val_kondisi); ?>',
                                            '<?php echo addslashes($val_deskripsi); ?>',
                                            '<?php echo addslashes($val_penjelasan); ?>'
                                        )">
                                            <i class="fa-solid fa-circle-info"></i> Selengkapnya
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 35px; color: #64748b;">
                                Belum ada data kecamatan di dalam database.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL POPUP DIALOG FOR EDIT DATA UTAMA -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <span>Ubah Data Performa Instansi</span>
                <button class="close-modal" onclick="closeEditModal()">&times;</button>
            </div>
            <form action="peringkat_admin.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_kecamatan" id="edit_id">

                    <div class="form-group">
                        <label>Nama Wilayah Kerja</label>
                        <input type="text" id="edit_nama_display" disabled style="background: #e2e8f0; color: #475569; font-weight: 600;">
                    </div>

                    <div class="form-group">
                        <label for="edit_camat">Nama Lengkap Camat</label>
                        <input type="text" name="nama_camat" id="edit_camat" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_sekcam">Nama Lengkap Sekcam</label>
                        <input type="text" name="nama_sekcam" id="edit_sekcam" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_rating">Rating Efektivitas Respon (Skala Bintang)</label>
                        <select name="rating_bintang" id="edit_rating" required>
                            <option value="5">★★★★★ (5) - Sangat Cepat</option>
                            <option value="4">★★★★☆ (4) - Cepat</option>
                            <option value="3">★★★☆☆ (3) - Cukup Baik</option>
                            <option value="2">★★☆☆☆ (2) - Lambat</option>
                            <option value="1">★☆☆☆☆ (1) - Sangat Lambat</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                    <button type="submit" name="update_kecamatan" class="btn-submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL POPUP DIALOG FOR EDIT SELENGKAPNYA (FOTO, DESKRIPSI, PENJELASAN) -->
    <div id="moreModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header" style="background: #f59e0b;">
                <span>Edit Deskripsi & Foto Instansi</span>
                <button class="close-modal" onclick="closeMoreModal()">&times;</button>
            </div>
            <form action="peringkat_admin.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id_kecamatan" id="more_id">

                    <div class="form-group">
                        <label>Kecamatan Wilayah</label>
                        <input type="text" id="more_nama_display" disabled style="background: #e2e8f0; color: #475569; font-weight: 600;">
                    </div>

                    <div class="form-group">
                        <label for="more_logo">Ganti Gambar/Logo Kecamatan</label>
                        <input type="file" name="logo_kecamatan" id="more_logo" accept="image/*" onchange="previewLogoImage(event)">
                        <div class="img-preview-container">
                            <img id="logo_current_preview" src="" alt="Preview Logo">
                            <span style="font-size:12px; color:#64748b;">Abaikan jika tidak ingin mengubah foto logo saat ini.</span>
                        </div>
                    </div>

                    <!-- INPUT BARU: UNTUK UPLOAD GAMBAR KERUSAKAN/KONDISI KANTOR KECAMATAN -->
                    <div class="form-group">
                        <label for="more_kondisi">Ganti Foto Kondisi / Kantor Kecamatan</label>
                        <input type="file" name="foto_kondisi" id="more_kondisi" accept="image/*" onchange="previewKondisiImage(event)">
                        <div class="img-preview-container">
                            <img id="kondisi_current_preview" src="" alt="Preview Kondisi" style="width: 60px; height: 60px; object-fit: cover;">
                            <span style="font-size:12px; color:#64748b;">Foto/Bukti kondisi fisik pelayanan operasional kecamatan saat ini.</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="more_deskripsi">Deskripsi Singkat Wilayah</label>
                        <textarea name="deskripsi" id="more_deskripsi" placeholder="Tuliskan deskripsi singkat letak geografis atau motto kecamatan..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="more_penjelasan">Penjelasan Detil Pelayanan & Kondisi</label>
                        <textarea name="penjelasan" id="more_penjelasan" placeholder="Tuliskan penjelasan detail terkait histori penanganan laporan, program kerja unggulan instansi..." style="min-height:120px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeMoreModal()">Batal</button>
                    <button type="submit" name="update_selengkapnya" class="btn-submit" style="background: #f59e0b;">Simpan Detail</button>
                </div>
            </form>
        </div>
    </div>

    <!-- FOOTER RESMI -->
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

    <!-- Pemanggilan Modal Profil Bawaan Sistem Admin -->
    <?php include 'profile_modal_admin.php'; ?>

    <!-- SCRIPT LOGIKA EVENT CONTROLLER -->
    <script>
    // 1. Dropdown Notifikasi Kontroler
    const nb=document.getElementById('notiBellBtn'),nd=document.getElementById('notiDropdown');
    if(nb&&nd){
        nb.addEventListener('click',e=>{
            e.stopPropagation();
            nd.style.display=nd.style.display==='block'?'none':'block';
        });
        document.addEventListener('click',e=>{
            if(!nd.contains(e.target)&&!nb.contains(e.target))nd.style.display='none';
        });
    }

    // 2. Kontroler Modal Popup Edit Data Kecamatan UTAMA
    const modal = document.getElementById('editModal');

    function openEditModal(id, nama, camat, sekcam, rating) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nama_display').value = "Kecamatan " + nama;
        document.getElementById('edit_camat').value = camat;
        document.getElementById('edit_sekcam').value = sekcam;
        document.getElementById('edit_rating').value = rating;
        
        modal.style.display = 'flex';
    }

    function closeEditModal() {
        modal.style.display = 'none';
    }

    // 3. Kontroler Modal Popup Edit SELENGKAPNYA (Logo, Kondisi, Deskripsi, Penjelasan)
    const moreModal = document.getElementById('moreModal');

    function openMoreModal(id, nama, logo, kondisi, deskripsi, penjelasan) {
        document.getElementById('more_id').value = id;
        document.getElementById('more_nama_display').value = "Kecamatan " + nama;
        document.getElementById('logo_current_preview').src = logo ? logo : '../ASSETS/LOGO.png';
        document.getElementById('kondisi_current_preview').src = kondisi ? kondisi : '../ASSETS/LOGO.png'; 
        document.getElementById('more_deskripsi').value = deskripsi;
        document.getElementById('more_penjelasan').value = penjelasan;
        
        moreModal.style.display = 'flex';
    }

    function closeMoreModal() {
        moreModal.style.display = 'none';
    }

    // Fungsi live preview file Logo Instansi
    function previewLogoImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('logo_current_preview');
            output.src = reader.result;
        }
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // Fungsi live preview file Kondisi Instansi / Kerusakan
    function previewKondisiImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('kondisi_current_preview');
            output.src = reader.result;
        }
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // Menutup modal jika admin klik di luar area kotak dialog putih
    window.onclick = function(event) {
        if (event.target == modal) {
            closeEditModal();
        }
        if (event.target == moreModal) {
            closeMoreModal();
        }
    }
    </script>
</body>
</html>