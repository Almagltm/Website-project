<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit();
}

$id = (int)$_GET['id'];

mysqli_query(
    $conn,
    "DELETE FROM tim_pengembang
    WHERE id='$id'"
);

header("Location: tentang_admin.php");
exit();
?>