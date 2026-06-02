
<?php
include '../koneksi.php';

$query = mysqli_query($conn,
"SELECT * FROM info_penting ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Info Penting - Aksi Kita</title>

    <!-- FONT -->
    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap"
          rel="stylesheet">

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
    background-color:#f4f6f9;
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

    margin-bottom:30px;
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

/* ================= CONTAINER ================= */

.container{
    width:100%;
}

/* ================= CARD ================= */

.announcement-section{

    width:100%;

    background:white;

    border:1px solid #e0e0e0;

    box-shadow:0 6px 15px rgba(0,0,0,0.05);

    border-radius:15px;

    padding:60px 0;

    margin-bottom:35px;

    overflow:hidden;
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

    word-wrap:break-word;
    overflow-wrap:break-word;
    word-break:break-word;
}

/* ================= CONTENT ================= */

.announcement-content{

    max-width:850px;

    margin:0 auto;

    padding:0 40px;

    line-height:1.85;

    font-size:1em;

    color:#444;
}

.announcement-content p{

    text-align:justify;

    margin-bottom:25px;

    white-space:pre-line;

    word-wrap:break-word;
    overflow-wrap:break-word;
    word-break:break-word;
}

/* ================= QUOTE ================= */

.closing-quote{

    font-style:italic;

    font-weight:600;

    text-align:center !important;

    margin:40px 0 !important;

    padding:15px 0;

    border-top:1px solid #1e3d8f;
    border-bottom:1px solid #1e3d8f;

    color:#1e3d8f;
}

/* ================= SIGNATURE ================= */

.signature{

    margin-top:50px;

    display:flex;
    flex-direction:column;

    align-items:flex-end;

    text-align:right;

    line-height:1.6;
}

.signature p:last-child{

    font-weight:bold;

    font-size:1.05em;

    margin-top:5px;
}

/* ================= EMPTY ================= */

.empty-box{

    background:white;

    padding:70px 30px;

    border-radius:15px;

    text-align:center;

    box-shadow:0 4px 12px rgba(0,0,0,0.05);

    color:#777;
}

/* ================= FOOTER ================= */

.main-footer{

    background:linear-gradient(
        165deg,
        #080e18 0%,
        #102647 70%,
        #9c7719 120%
    );

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

    font-size:15px;
}

.footer-col a{

    display:block;

    margin:6px 0;

    color:#eee;

    text-decoration:none;

    font-size:15px;

    transition:0.2s;
}

.footer-col a:hover{
    color:#0077ff;
}

/* ================= SOCIAL ================= */

.footer-social{

    display:flex;

    gap:15px;

    margin-bottom:35px;
}

.footer-social a{

    width:40px;
    height:40px;

    border-radius:8px;

    display:inline-flex;

    align-items:center;
    justify-content:center;

    color:#000;

    background:#fff;

    text-decoration:none;

    font-size:18px;

    transition:0.3s;
}

.footer-social a:hover{
    transform:translateY(-5px);
}

/* ================= COPYRIGHT ================= */

.footer-bottom{

    text-align:center;

    font-size:14px;

    color:#ccc;

    margin-top:10px;
}

/* ================= RESPONSIVE ================= */

@media(max-width:800px){

    .footer-content{
        flex-direction:column;
    }

    .page-header{
        flex-direction:column;
        gap:15px;
        text-align:center;
    }

    .announcement-content{
        padding:0 25px;
    }

}

</style>
</head>

<body>

<div class="wrapper">

    <!-- HEADER -->
    <header class="page-header">

        <a href="BERANDA2.php" class="back-btn">

            <i class="fa-solid fa-chevron-left"></i>

            Kembali

        </a>

        <h2>Info Penting</h2>

        <div style="width:80px;"></div>

    </header>

    <!-- MAIN -->
    <main class="container">

<?php
if(mysqli_num_rows($query) > 0){

    while($data = mysqli_fetch_assoc($query)){
?>

        <!-- CARD -->
        <section class="announcement-section">

            <!-- TITLE -->
            <h1 class="announcement-title">

                <?php
                echo nl2br(
                    htmlspecialchars($data['judul'])
                );
                ?>

            </h1>

            <!-- CONTENT -->
            <div class="announcement-content">

                <p>

                    <?php
                    echo nl2br(
                        htmlspecialchars($data['isi'])
                    );
                    ?>

                </p>

                <!-- SIGNATURE -->
                <div class="signature">

                    <p>Hormat kami,</p>

                    <p>

                        <?php
                        echo htmlspecialchars(
                            $data['penulis']
                        );
                        ?>

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
                Saat ini belum ada info terbaru.
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

            <img src="ASSETS/LOGO.png"
                 style="width:80px; margin-bottom:10px;"
                 alt="Logo Aksi Kita">

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

