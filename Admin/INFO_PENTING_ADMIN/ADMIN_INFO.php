
<?php
include 'koneksi.php';

$query = mysqli_query($conn, "SELECT * FROM info_penting ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Info Penting - Aksi Kita</title>

    <!-- FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- ICON -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

/* ================= RESET ================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    background:#f4f6f9;
    color:#333;
    line-height:1.6;
}

/* ================= WRAPPER ================= */

.wrapper{
    max-width:1200px;
    margin:0 auto;
    padding:30px 20px;
    min-height:80vh;
}

/* ================= HEADER ================= */

.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;

    background:#1e3d8f;
    color:white;

    padding:20px 30px;

    border-radius:15px;

    box-shadow:0 4px 15px rgba(0,0,0,0.1);

    margin-bottom:25px;
}

.page-header h2{
    font-size:22px;
    font-weight:700;
}

.back-btn{
    text-decoration:none;
    color:white;

    font-weight:600;

    display:flex;
    align-items:center;
    gap:8px;

    padding:8px 16px;

    background:rgba(255,255,255,0.15);

    border-radius:30px;

    transition:0.3s;
}

.back-btn:hover{
    background:rgba(255,255,255,0.3);
}

/* ================= ADMIN TOOLS ================= */

.admin-tools{
    display:flex;
    justify-content:flex-end;

    margin-bottom:20px;
}

.add-btn{
    background:#1e3d8f;
    color:white;

    border:none;

    padding:12px 22px;

    border-radius:10px;

    cursor:pointer;

    font-weight:600;

    transition:0.3s;
}

.add-btn:hover{
    background:#163170;
    transform:translateY(-2px);
}

/* ================= ANNOUNCEMENT ================= */

.announcement-section{
    width:100%;

    background:white;

    border:1px solid #e0e0e0;

    box-shadow:0 6px 15px rgba(0,0,0,0.05);

    border-radius:15px;

    padding:50px 0;

    margin-bottom:30px;
}

/* ================= ACTIONS ================= */

.announcement-actions{
    display:flex;
    justify-content:flex-end;
    gap:10px;

    margin-bottom:30px;

    padding:0 40px;
}

.edit-btn,
.delete-btn{
    border:none;

    padding:10px 16px;

    border-radius:8px;

    cursor:pointer;

    color:white;

    font-weight:600;

    transition:0.3s;
}

.edit-btn{
    background:#f0ad4e;
}

.edit-btn:hover{
    background:#d9922e;
}

.delete-btn{
    background:#dc3545;
}

.delete-btn:hover{
    background:#b52a37;
}

/* ================= TITLE ================= */

.announcement-title{
    font-size:1.8em;

    font-weight:700;

    color:#1e3d8f;

    text-align:center;

    margin-bottom:50px;

    line-height:1.3;

    padding-bottom:15px;

    border-bottom:3px double #d0d0d0;

    max-width:700px;

    margin-left:auto;
    margin-right:auto;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
}

/* ================= CONTENT ================= */

.announcement-content{
    max-width:850px;

    margin:0 auto;

    padding:0 40px;

    line-height:1.8;

    font-size:1em;

    color:#444;
}

.announcement-content p{
    text-align:justify;

    margin-bottom:25px;

    white-space:pre-line;

    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
}

.announcement-section{
    overflow:hidden;
}

/* ================= SIGNATURE ================= */

.signature{
    margin-top:50px;

    display:flex;
    flex-direction:column;
    align-items:flex-end;

    text-align:right;
}

.signature p:last-child{
    font-weight:bold;
}

/* ================= EMPTY ================= */

.empty-box{
    background:white;

    border-radius:15px;

    padding:60px 20px;

    text-align:center;

    color:#777;

    box-shadow:0 4px 12px rgba(0,0,0,0.05);
}

/* ================= FOOTER ================= */

.main-footer{
    background:linear-gradient(165deg,#080e18 0%,#102647 70%,#9c7719 120%);

    color:#fff;

    padding:60px 70px;
}

.footer-content{
    display:flex;

    justify-content:space-between;

    flex-wrap:wrap;

    margin-bottom:45px;

    gap:20px;
}

.footer-col{
    flex:1;
    min-width:200px;
}

.footer-col p{
    margin:6px 0;
    color:#ccc;
}

.footer-col a{
    display:block;

    margin:6px 0;

    color:#eee;

    text-decoration:none;
}

.footer-social{
    display:flex;
    gap:15px;

    margin-bottom:35px;
}

.footer-social a{
    width:40px;
    height:40px;

    border-radius:8px;

    display:flex;
    align-items:center;
    justify-content:center;

    background:white;

    color:black;

    text-decoration:none;

    transition:0.3s;
}

.footer-social a:hover{
    transform:translateY(-5px);
}

.footer-bottom{
    text-align:center;
    color:#ccc;
}

/* ================= RESPONSIVE ================= */

@media(max-width:800px){

    .page-header{
        flex-direction:column;
        gap:15px;
        text-align:center;
    }

    .announcement-actions{
        justify-content:center;
    }

    .footer-content{
        flex-direction:column;
    }

}

</style>
</head>

<body>

<div class="wrapper">

    <!-- HEADER -->
    <header class="page-header">

        <a href="BERANDA_ADMIN.php" class="back-btn">
            <i class="fa-solid fa-chevron-left"></i>
            Kembali
        </a>

        <h2>Info Penting (Admin)</h2>

        <div style="width:80px;"></div>

    </header>

    <!-- ADMIN TOOLS -->
    <div class="admin-tools">

        <a href="tambah_info.php">

            <button class="add-btn">

                <i class="fa-solid fa-plus"></i>

                Tambah Pengumuman

            </button>

        </a>

    </div>

    <!-- MAIN -->
    <main class="container">

<?php
if(mysqli_num_rows($query) > 0){

    while($data = mysqli_fetch_assoc($query)){
?>

        <section class="announcement-section">

            <!-- ACTION BUTTON -->
            <div class="announcement-actions">

                <a href="edit_info.php?id=<?php echo $data['id_info']; ?>">

                    <button class="edit-btn">

                        <i class="fa-solid fa-pen"></i>

                        Edit

                    </button>

                </a>

                <a href="hapus_info.php?id=<?php echo $data['id_info']; ?>"
                   onclick="return confirm('Yakin ingin menghapus pengumuman ini?')">

                    <button class="delete-btn">

                        <i class="fa-solid fa-trash"></i>

                        Hapus

                    </button>

                </a>

            </div>

            <!-- TITLE -->
            <h1 class="announcement-title">

                <?php echo nl2br(htmlspecialchars($data['judul'])); ?>

            </h1>

            <!-- CONTENT -->
            <div class="announcement-content">

                <p>

                    <?php echo nl2br(htmlspecialchars($data['isi'])); ?>

                </p>

                <!-- SIGNATURE -->
                <div class="signature">

                    <p>Hormat kami,</p>

                    <p>

                        <?php echo htmlspecialchars($data['penulis']); ?>

                    </p>

                </div>

            </div>

        </section>

<?php
    }

}else{
?>

        <div class="empty-box">

            <h2>Belum Ada Pengumuman</h2>

            <p>
                Silakan tambahkan pengumuman baru.
            </p>

        </div>

<?php
}
?>

    </main>

</div>

<!-- FOOTER -->
<footer class="main-footer">

    <div class="footer-content">

        <div class="footer-col">

            <img src="./ASSETS/LOGO.png"
                 style="width:80px; margin-bottom:10px;"
                 alt="Logo">

            <h3>Aksi Kita</h3>

            <p>Jl. Bachireng No. 12, Indonesia</p>

            <p>info@aksikita.id</p>

        </div>

        <div class="footer-col">

            <h4>Menu</h4>

            <a href="#">Cara Kerja</a>
            <a href="#">FAQ</a>
            <a href="#">Aturan</a>

        </div>

        <div class="footer-col">

            <h4>Tautan</h4>

            <a href="#">Lapor</a>
            <a href="#">Survei</a>
            <a href="#">Arsip</a>

        </div>

    </div>

    <!-- SOCIAL -->
    <div class="footer-social">

        <a href="#"><i class="fab fa-whatsapp"></i></a>

        <a href="#"><i class="fab fa-facebook"></i></a>

        <a href="#"><i class="fab fa-instagram"></i></a>

        <a href="#"><i class="fab fa-youtube"></i></a>

        <a href="#"><i class="fab fa-tiktok"></i></a>

    </div>

    <!-- COPYRIGHT -->
    <div class="footer-bottom">

        © 2025 AksiKita. Semua Hak Dilindungi.

    </div>

</footer>

</body>
</html>

