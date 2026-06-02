<?php

include 'koneksi.php';

$id = $_POST['id_info'];
$judul = $_POST['judul'];
$isi = $_POST['isi'];
$penulis = $_POST['penulis'];

$query = mysqli_query($conn, "UPDATE info_penting SET
judul='$judul',
isi='$isi',
penulis='$penulis'
WHERE id_info='$id'
");

if($query){
    header("Location: ADMIN_INFO.php");
}else{
    echo "Gagal update";
}

?>
