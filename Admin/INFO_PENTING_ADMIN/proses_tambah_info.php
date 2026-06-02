
<?php

include 'koneksi.php';

$judul = $_POST['judul'];
$isi = $_POST['isi'];
$penulis = $_POST['penulis'];

$query = mysqli_query($conn, "INSERT INTO info_penting
(judul, isi, penulis)
VALUES
('$judul', '$isi', '$penulis')
");

if($query){
    header("Location: ADMIN_INFO.php");
}else{
    echo "Gagal menambahkan pengumuman";
}

?>

