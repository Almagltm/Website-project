<?php
session_start();
include '../koneksi.php';

// Pastikan yang mengakses file ini adalah admin yang sah
if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit();
}

// Ambil dan amankan data teks dari input form
$judul = mysqli_real_escape_string($conn, $_POST['judul']);
$deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

// Mulai susun query utama untuk update teks
$sql = "UPDATE tentang SET judul='$judul', deskripsi='$deskripsi'";

// Cek apakah admin mengunggah file gambar baru
if (!empty($_FILES['gambar']['name'])) {
    
    // 1. AMBIL NAMA GAMBAR LAMA UNTUK DIHAPUS DARI FOLDER
    $query_lama = mysqli_query($conn, "SELECT gambar FROM tentang LIMIT 1");
    if ($query_lama && mysqli_num_rows($query_lama) > 0) {
        $data_lama = mysqli_fetch_assoc($query_lama);
        $gambar_lama = $data_lama['gambar'];
        
        // Hapus file fisik gambar lama jika file-nya memang ada di folder ASSETS
        if (!empty($gambar_lama) && file_exists("../ASSETS/" . $gambar_lama)) {
            unlink("../ASSETS/" . $gambar_lama);
        }
    }

    // 2. PROSES UPLOAD GAMBAR BARU
    $ekstensi_diperbolehkan = ['png', 'jpg', 'jpeg', 'webp'];
    $nama_asal = $_FILES['gambar']['name'];
    $x = explode('.', $nama_asal);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['gambar']['size'];
    $file_tmp = $_FILES['gambar']['tmp_name'];

    // Validasi ekstensi file agar tidak ada yang iseng upload file .php / .sh
    if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
        // Beri nama unik menggunakan timestamp agar tidak bentrok
        $namaFileBaru = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $nama_asal);

        if (move_uploaded_file($file_tmp, "../ASSETS/" . $namaFileBaru)) {
            // Gabungkan field gambar ke query jika upload berhasil
            $sql .= ", gambar='$namaFileBaru'";
        }
    }
}

// Jalankan query update data (menggunakan WHERE atau LIMIT 1 agar aman)
// Catatan: Jika tabel 'tentang' kamu menggunakan ID (misal id=1), disarankan mengubahnya menjadi WHERE id=1
$sql .= " LIMIT 1";

mysqli_query($conn, $sql);

// Kembalikan halaman ke tentang_admin.php dengan lancar
header("Location: tentang_admin.php");
exit();
?>