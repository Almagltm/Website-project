<?php

session_start();

require_once '../db.php';



$query = mysqli_query($conn, "

SELECT l.*, k.nama_kategori

FROM laporan l

LEFT JOIN kategori k ON k.id_kategori = l.id_kategori

ORDER BY l.created_at DESC

LIMIT 20

");

?>



<!DOCTYPE html>



<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">



<title>Laporan Terpopuler - Aksi Kita</title>



<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>



<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">



<link rel="stylesheet"

href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



<style>



*{

    margin:0;

    padding:0;

    box-sizing:border-box;

    font-family:'Poppins',sans-serif;

}



body{

    background:#f4f6f9;

    color:#333;

}



/* WRAPPER */



.wrapper{

    max-width:1200px;

    margin:auto;

    padding:30px 20px;

}



/* HEADER */



.page-header{

    display:flex;

    justify-content:space-between;

    align-items:center;



    background:#1e3d8f;

    color:white;



    padding:20px 30px;

    border-radius:15px;



    margin-bottom:30px;



    box-shadow:0 4px 15px rgba(0,0,0,.1);

}



.page-header h2{

    font-size:22px;

}



.back-btn{

    text-decoration:none;

    color:white;



    display:flex;

    align-items:center;

    justify-content:center;



    width:42px;

    height:42px;



    border-radius:50%;

    background:rgba(255,255,255,.15);



    transition:.3s;

}



.back-btn:hover{

    background:rgba(255,255,255,.3);

}



/* GRID */



.laporan-grid{

    display:grid;

    grid-template-columns:repeat(auto-fill,minmax(320px,1fr));

    gap:25px;

}



/* CARD */



.card{

    background:white;

    border-radius:15px;

    overflow:hidden;

    box-shadow:0 5px 15px rgba(0,0,0,.08);

    transition:.3s;

}



.card:hover{

    transform:translateY(-6px);

}



.card-img{

    height:220px;

    overflow:hidden;

}



.card-img img{

    width:100%;

    height:100%;

    object-fit:cover;

}



.card-body{

    padding:18px;

}



.card-body h3{

    color:#1e3d8f;

    margin-bottom:10px;

    font-size:18px;

}



.card-body p{

    margin-bottom:8px;

    font-size:14px;

}



.badge{

    display:inline-block;

    padding:6px 12px;

    border-radius:20px;

    font-size:12px;

    font-weight:600;

}



.pending{

    background:#fff3cd;

    color:#856404;

}



.diproses{

    background:#dbeafe;

    color:#1e40af;

}



.selesai{

    background:#d1fae5;

    color:#166534;

}



.ditolak{

    background:#fee2e2;

    color:#991b1b;

}



.btn-detail{

    display:inline-block;

    margin-top:12px;



    background:#1e3d8f;

    color:white;



    text-decoration:none;



    padding:10px 18px;

    border-radius:8px;



    transition:.3s;

}



.btn-detail:hover{

    background:#163171;

}



/* EMPTY */



.empty-box{

    background:white;

    padding:80px 20px;

    text-align:center;

    border-radius:15px;

}



/* FOOTER */



.main-footer{



    background:linear-gradient(

        165deg,

        #080e18 0%,

        #102647 70%,

        #9c7719 120%

    );



    color:#fff;



    padding:60px 70px;



    margin-top:60px;

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



.footer-bottom{



    text-align:center;



    font-size:14px;



    color:#ccc;



    margin-top:10px;

}



@media(max-width:800px){



    .footer-content{

        flex-direction:column;

    }



    .page-header{

        flex-direction:column;

        gap:15px;

    }

}



</style>



</head>

<body>



<div class="wrapper">



<header class="page-header">





<a href="BERANDA2.php" class="back-btn">

    <i class="fa-solid fa-chevron-left"></i>

</a>



<h2>Laporan Terbaru</h2>



<div style="width:42px"></div>





</header>



<?php if(mysqli_num_rows($query)>0): ?>



<div class="laporan-grid">



<?php while($row = mysqli_fetch_assoc($query)):



$foto = "../ASSETS/rusak 1.jpeg";



if(!empty($row['foto_awal'])){

    $cek = "../".$row['foto_awal'];



    if(file_exists($cek)){

        $foto = $cek;

    }

}



?>



<div class="card">



<div class="card-img">

<img src="<?= $foto ?>">

</div>



<div class="card-body">



<h3><?= htmlspecialchars($row['judul']) ?></h3>



<p>

<i class="fa-solid fa-location-dot"></i>

<?= htmlspecialchars($row['lokasi']) ?>

</p>



<p>

<i class="fa-solid fa-layer-group"></i>

<?= htmlspecialchars($row['nama_kategori']) ?>

</p>



<p>

<i class="fa-solid fa-calendar"></i>

<?= date('d M Y',strtotime($row['created_at'])) ?>

</p>



<div class="badge <?= $row['status'] ?>">

<?= strtoupper($row['status']) ?>

</div>



<br>



<a href="user_detail_laporan.php?id=<?= $row['id_laporan'] ?>"

class="btn-detail">

Lihat Detail </a>



</div>



</div>



<?php endwhile; ?>



</div>



<?php else: ?>



<div class="empty-box">

<h2>Belum Ada Laporan</h2>

<p>Tidak ada data laporan saat ini.</p>

</div>



<?php endif; ?>



</div>



<footer class="main-footer">



<div class="footer-content">



<div class="footer-col">



<img src="../ASSETS/LOGO.png"

style="width:80px;margin-bottom:10px;"

alt="Logo">



<h3>Aksi Kita</h3>



<p>Jl. Bachireng No. 12, Indonesia</p>

<p>0821 6888 9060</p>

<p>info@aksikita.id</p>



</div>



<div class="footer-col">



<h4>Menu</h4>



<a href="#">Cara Kerja</a> <a href="#">FAQ</a> <a href="#">Aturan Penggunaan</a>



</div>



<div class="footer-col">



<h4>Tautan</h4>



<a href="#">Kelola Laporan</a> <a href="#">Statistika</a> <a href="#">Info Penting</a>



</div>



</div>



<div class="footer-social">



<a href="#"><i class="fab fa-whatsapp"></i></a> <a href="#"><i class="fab fa-facebook"></i></a> <a href="#"><i class="fab fa-instagram"></i></a> <a href="#"><i class="fab fa-youtube"></i></a> <a href="#"><i class="fab fa-tiktok"></i></a>



</div>



<div class="footer-bottom">

© 2026 AksiKita. Semua Hak Dilindungi.

</div>



</footer>



</body>

</html>