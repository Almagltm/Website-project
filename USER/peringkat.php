<?php
session_start();
// Menggunakan jalur file koneksi yang sama
include '../koneksi.php';

// Proteksi halaman user biasa (sesuaikan key session user milikmu, misal: user_id / id_user)
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

// Mengambil nama user untuk ditampilkan di navbar (Menggunakan $_SESSION['nama_lengkap'] sesuai Beranda2)
$user_nama = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : 'Pengguna';

/* ==========================================
   LOGIKA NOTIFIKASI VERSI USER 
   ========================================== */
$id_user_session = $_SESSION['user_id'];
$query_noti = mysqli_query($conn, "
    SELECT id_laporan, judul, status, created_at 
    FROM laporan 
    WHERE id_user = '$id_user_session'
    ORDER BY created_at DESC 
    LIMIT 5
");

$noti_items = [];
$noti_count = 0;

if ($query_noti) {
    while ($row = mysqli_fetch_assoc($query_noti)) {
        $judul_pendek = strlen($row['judul']) > 40 ? substr($row['judul'], 0, 37) . '...' : $row['judul'];
        $noti_items[] = [
            'link'  => 'user_detail_laporan.php?id=' . $row['id_laporan'],
            'title' => 'Update Status: ' . ucfirst($row['status']),
            'desc'  => htmlspecialchars($judul_pendek),
            'time'  => date('d M Y H:i', strtotime($row['created_at']))
        ];
    }
    $noti_count = count($noti_items);
}

/* ==========================================
   LOGIKA QUERY PERINGKAT KECAMATAN (Berdasarkan Rating Tertinggi)
   ========================================== */
$query_kec = mysqli_query($conn, "SELECT * FROM kecamatan ORDER BY rating_bintang DESC, nama_kecamatan ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringkat Respon Kecamatan - Aksi Kita</title>

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
            background:#f0f0f0; 
            background-image: url(../ASSETS/bgg.jpg); 
            background-size: cover;
            background-attachment: fixed;
            overflow-x:hidden;
        }

        /* ================= NAVBAR (SINKRON DENGAN BERANDA2 USER) ================= */
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

        

        .user-img{
            width:48px;
            height:48px;
            border-radius:50%;
            object-fit:cover;
            border:2px solid rgba(255,255,255,.4);
        }

        .nav-right{
    display:flex;
    align-items:center;
    margin-left:auto;
    gap:15px;
}

.nav-user{
    display:flex;
    align-items:center;
    gap:10px;
    cursor:pointer;
}

.nav-user span{
    color:#fff;
    font-size:16px;
}

        /* Notification Bell */
.noti-wrap{position:relative;display:flex;align-items:center;}
.noti-btn{cursor:pointer;position:relative;width:38px;height:38px;border-radius:50%;
  background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;transition:.2s;}
.noti-btn:hover{background:rgba(255,255,255,.22);}
.noti-badge{position:absolute;top:-3px;right:-3px;background:#e63946;color:#fff;border-radius:50%;
  width:18px;height:18px;display:flex;align-items:center;justify-content:center;
  font-size:10px;font-weight:700;border:2px solid #1e3d8f;}
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

        /* ================= AREA UTAMA USER ================= */
        .workspace {
            margin-top: 110px; 
            padding: 0 50px;
            min-height: 70vh;
        }

        .header-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 24px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
            color: #475569;
        }

        /* DATA WORKSPACE TABLE */
        .table-wrapper {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
            color: #1e3d8f;
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

        /* BADGE PERINGKAT */
        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-weight: 700;
            font-size: 14px;
        }
        .rank-1 { background: #fef3c7; color: #d97706; border: 2px solid #fcd34d; }
        .rank-2 { background: #e2e8f0; color: #475569; border: 2px solid #cbd5e1; }
        .rank-3 { background: #ffedd5; color: #ea580c; border: 2px solid #fed7aa; }
        .rank-other { background: #f1f5f9; color: #64748b; }

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

        /* BUTTON DETAIL VERSI USER */
        .btn-view-detail {
            background: #1e3d8f;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }
        .btn-view-detail:hover { background: #142b66; transform: translateY(-1px); }

        /* MODAL POPUP LIHAT DETAIL FOR USER */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            padding: 20px;
            backdrop-filter: blur(3px);
        }

        .modal-box {
            background: white;
            width: 100%;
            max-width: 600px;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.15);
            overflow: hidden;
            animation: modalFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-header {
            background: #1e3d8f;
            color: white;
            padding: 18px 24px;
            font-size: 18px;
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

        .modal-body { padding: 24px; max-height: 75vh; overflow-y: auto; }
        
        /* DETAIL LAYOUT STRUCTURE */
        .info-section {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-logo {
            width: 90px;
            height: 90px;
            object-fit: contain;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 6px;
        }
        .info-meta { flex: 1; }
        .info-meta h3 { color: #1e3d8f; font-size: 20px; margin-bottom: 6px; }
        .info-row { display: flex; font-size: 14px; margin-bottom: 4px; }
        .info-label { width: 110px; font-weight: 600; color: #64748b; }
        .info-value { color: #1e293b; }

        .detail-block { margin-bottom: 18px; }
        .detail-block h4 { font-size: 14px; font-weight: 600; color: #1e3d8f; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;}
        .detail-block p { font-size: 14px; color: #475569; line-height: 1.6; background: #f8fafc; padding: 12px; border-radius: 8px; border-left: 4px solid #1e3d8f; }

        .condition-img {
            width: 100%;
            max-height: 250px;
            object-fit: cover;
            border-radius: 10px;
            margin-top: 5px;
            border: 1px solid #e2e8f0;
        }

        .modal-footer {
            padding: 16px 24px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
        }
        .btn-close-footer { background: #cbd5e1; color: #334155; border: none; padding: 10px 24px; border-radius: 8px; cursor: pointer; font-weight: 500;}
        .btn-close-footer:hover { background: #94a3b8; }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95) translateY(-10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* ================== FOOTER ================== */
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
    .navbar{
        flex-direction:column;
        gap:15px;
        padding:20px;
    }

    .navbar nav{
        margin-left:0;
    }

    .nav-right{
        margin-left:0;
    }
}
    </style>
</head>

<body>

    <!-- NAVBAR SINKRON DENGAN BERANDA2.PHP -->
    <header class="navbar">
        <div class="logo">
            <img src="../ASSETS/LOGO.png" class="logo-img">
        </div>

        <nav>
            <a href="Beranda2.php">Beranda</a>
            <a href="laporan_saya.php">Laporan</a>
            <a href="peringkat.php" class="active">Peringkat</a>
            <a href="TENTANG.php">Tentang</a>
        </nav>

        <div class="nav-right">

    <div class="noti-wrap">
        <div class="noti-btn" id="notiBellBtn">
            <i class="fa-solid fa-bell"></i>

            <?php if ($noti_count > 0): ?>
                <span class="noti-badge"><?= $noti_count ?></span>
            <?php endif; ?>
        </div>

        <div class="noti-dropdown" id="notiDropdown">
            <div class="noti-header">Notifikasi</div>

            <?php if (empty($noti_items)): ?>
                <div style="padding:20px;text-align:center;color:#64748b;">
                    Belum ada notifikasi
                </div>
            <?php else: ?>
                <?php foreach ($noti_items as $item): ?>
                    <a href="<?= $item['link'] ?>" class="noti-item">
                        <span class="noti-title"><?= $item['title'] ?></span>
                        <span class="noti-desc"><?= $item['desc'] ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="nav-user" id="navUser">
        <span><?= htmlspecialchars($user_nama) ?></span>
        <img src="../ASSETS/USER.png" class="user-img" alt="User">
    </div>

</div>
    </header>

    <div class="workspace">
        <div class="header-box">
            <div>
                <h1>Peringkat Efektivitas Respon Kecamatan</h1>
                <p>Daftar urutan performa instansi kecamatan se-Kota Medan dalam menangani laporan masyarakat secara transparan dan berkala.</p>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 70px; text-align:center;">Posisi</th>
                        <th style="width: 80px; text-align:center;">Logo</th>
                        <th>Nama Wilayah</th>
                        <th>Nama Camat</th>
                        <th>Rating Performa</th>
                        <th style="width: 180px; text-align: center;">Profil Lengkap</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query_kec) > 0): ?>
                        <?php 
                        $no = 1;
                        while($row = mysqli_fetch_assoc($query_kec)): 
                            $val_deskripsi  = isset($row['deskripsi']) ? $row['deskripsi'] : 'Belum ada deskripsi singkat.';
                            $val_penjelasan = isset($row['penjelasan']) ? $row['penjelasan'] : 'Belum ada penjelasan pelayanan.';
                            $val_logo       = (!empty($row['logo_kecamatan'])) ? $row['logo_kecamatan'] : '../ASSETS/LOGO.png';
                            $val_kondisi    = isset($row['foto_kondisi']) ? $row['foto_kondisi'] : '';
                            $val_sekcam     = isset($row['nama_sekcam']) ? $row['nama_sekcam'] : '-';

                            $rank_class = "rank-other";
                            if($no == 1) $rank_class = "rank-1";
                            elseif($no == 2) $rank_class = "rank-2";
                            elseif($no == 3) $rank_class = "rank-3";
                        ?>
                            <tr>
                                <td style="text-align: center;">
                                    <span class="rank-badge <?= $rank_class ?>"><?= $no ?></span>
                                </td>
                                <td style="text-align: center;">
                                    <img src="<?php echo htmlspecialchars($val_logo); ?>" class="cell-logo" alt="Logo">
                                </td>
                                <td><b>Kecamatan <?php echo htmlspecialchars($row['nama_kecamatan']); ?></b></td>
                                <td><?php echo htmlspecialchars($row['nama_camat']); ?></td>
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
                                <td style="text-align: center;">
                                    <button class="btn-view-detail" onclick="openUserDetailModal(
                                        '<?php echo addslashes($row['nama_kecamatan']); ?>',
                                        '<?php echo addslashes($row['nama_camat']); ?>',
                                        '<?php echo addslashes($val_sekcam); ?>',
                                        '<?php echo $row['rating_bintang']; ?>',
                                        '<?php echo addslashes($val_logo); ?>',
                                        '<?php echo addslashes($val_kondisi); ?>',
                                        '<?php echo addslashes($val_deskripsi); ?>',
                                        '<?php echo addslashes($val_penjelasan); ?>'
                                    )">
                                        <i class="fa-solid fa-eye"></i> Lihat Detail
                                    </button>
                                </td>
                            </tr>
                        <?php 
                            $no++;
                            endwhile; 
                        ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 35px; color: #64748b;">
                                Belum ada data peringkat instansi saat ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="userDetailModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <span id="modal_title">Profil Instansi Kecamatan</span>
                <button class="close-modal" onclick="closeUserDetailModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="info-section">
                    <img id="modal_logo" src="../ASSETS/LOGO.png" class="info-logo" alt="Logo">
                    <div class="info-meta">
                        <h3 id="modal_kec_name">Kecamatan</h3>
                        <div class="info-row">
                            <span class="info-label">Camat:</span>
                            <span class="info-value" id="modal_camat">-</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Sekcam:</span>
                            <span class="info-value" id="modal_sekcam">-</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Performa:</span>
                            <span class="info-value stars-display" id="modal_stars">★★★★★</span>
                        </div>
                    </div>
                </div>

                <div class="detail-block">
                    <h4>Deskripsi Wilayah Kerja</h4>
                    <p id="modal_deskripsi">Motto atau deskripsi instansi belum ditambahkan.</p>
                </div>

                <div class="detail-block">
                    <h4>Penjelasan Detail Pelayanan & Histori Laporan</h4>
                    <p id="modal_penjelasan">Detail operasional penanganan pengaduan belum diisi oleh pusat.</p>
                </div>

                <div class="detail-block" id="block_foto_kondisi" style="display:none;">
                    <h4>Foto Kondisi Operasional Pelayanan</h4>
                    <img id="modal_foto_kondisi" src="" class="condition-img" alt="Foto Kondisi Lapangan">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-close-footer" onclick="closeUserDetailModal()">Tutup</button>
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
            &copy; 2026 AksiKita. Hak Cipta Dilindungi Undang-Undang.
        </div>
    </footer>
<?php include 'profile_modal.php'; ?>
    <script>
        // Logika Interaktivitas Dropdown Notifikasi Navbar (Sesuai Beranda2)
        const bell = document.getElementById("notiBellBtn");
        const dropdown = document.getElementById("notiDropdown");

        if(bell && dropdown){
            bell.addEventListener("click", function(e){
                e.stopPropagation();
                dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
            });

            document.addEventListener("click", function(e){
                if(!dropdown.contains(e.target) && !bell.contains(e.target)){
                    dropdown.style.display = "none";
                }
            });
        }

        // Fungsi Membuka Modal Detail Profil Kecamatan bagi User
        function openUserDetailModal(nama_kec, camat, sekcam, rating, logo, foto, deskripsi, penjelasan) {
            document.getElementById('modal_kec_name').innerText = "Kecamatan " + nama_kec;
            document.getElementById('modal_camat').innerText = camat ? camat : '-';
            document.getElementById('modal_sekcam').innerText = sekcam ? sekcam : '-';
            document.getElementById('modal_logo').src = logo ? logo : '../ASSETS/LOGO.png';
            
            let starsStr = "";
            for(let i=1; i<=5; i++){
                starsStr += (i <= rating) ? "★" : "☆";
            }
            document.getElementById('modal_stars').innerText = starsStr + " (" + rating + "/5)";

            document.getElementById('modal_deskripsi').innerText = deskripsi ? deskripsi : 'Belum tersedia deskripsi ringkas wilayah.';
            document.getElementById('modal_penjelasan').innerText = penjelasan ? penjelasan : 'Belum tersedia riwayat penjelasan penanganan laporan.';

            const fotoBlock = document.getElementById('block_foto_kondisi');
            const fotoImg = document.getElementById('modal_foto_kondisi');
            if(foto && foto.trim() !== '') {
                fotoImg.src = foto;
                fotoBlock.style.display = 'block';
            } else {
                fotoBlock.style.display = 'none';
                fotoImg.src = '';
            }

            document.getElementById('userDetailModal').style.display = 'flex';
        }

        function closeUserDetailModal() {
            document.getElementById('userDetailModal').style.display = 'none';
        }
    </script>
</body>
</html>