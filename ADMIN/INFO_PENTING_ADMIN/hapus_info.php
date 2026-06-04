<?php

include 'koneksi.php';

$id = $_GET['id'];

$query = mysqli_query($conn,
"DELETE FROM info_penting WHERE id_info='$id'");

if($query){
    header("Location: ADMIN_INFO.php");
}else{
    echo "Gagal menghapus";
}

?>