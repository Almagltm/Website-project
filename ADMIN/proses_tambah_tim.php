<?php
include '../koneksi.php';

$nama = $_POST['nama'];
$nim = $_POST['nim'];
$tugas = $_POST['tugas'];

$foto = '';

if(!empty($_FILES['foto']['name'])){

    $foto =
        time().'_'.$_FILES['foto']['name'];

    move_uploaded_file(
        $_FILES['foto']['tmp_name'],
        "../ASSETS/".$foto
    );
}

mysqli_query($conn,"
INSERT INTO tim_pengembang
(nama,nim,tugas,foto)
VALUES
('$nama','$nim','$tugas','$foto')
");

header("Location: tentang_admin.php");
exit();
?>