<?php
$host     = "localhost";
$user     = "root";
$password = "";
$database = "aksi_kita"; // Pastikan namanya sama dengan database di phpMyAdmin

// Membuat koneksi ke MySQL
$koneksi = mysqli_connect($host, $user, $password, $database);

// Memeriksa apakah koneksi berhasil atau gagal
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>