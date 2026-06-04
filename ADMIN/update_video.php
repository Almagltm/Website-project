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

// Mulai susun query dasar untuk mengupdate teks judul dan deskripsi
$sql = "UPDATE tentang_video SET judul='$judul', deskripsi='$deskripsi'";

// Cek apakah admin mengunggah file video baru
if (!empty($_FILES['video']['name'])) {
    
    // 1. MANAJEMEN ASSET: AMBIL FILE VIDEO LAMA UNTUK DIHAPUS DARI SERVER
    $query_lama = mysqli_query($conn, "SELECT file_video FROM tentang_video LIMIT 1");
    if ($query_lama && mysqli_num_rows($query_lama) > 0) {
        $data_lama = mysqli_fetch_assoc($query_lama);
        $video_lama = $data_lama['file_video'];
        
        // Hapus file video lama secara fisik jika file tersebut ada di folder ASSETS
        if (!empty($video_lama) && file_exists("../ASSETS/" . $video_lama)) {
            unlink("../ASSETS/" . $video_lama);
        }
    }

    // 2. PROSES UPLOAD VIDEO BARU
    $ekstensi_diperbolehkan = ['mp4', 'mov', 'avi', 'mkv', 'webm'];
    $nama_asal = $_FILES['video']['name'];
    $x = explode('.', $nama_asal);
    $ekstensi = strtolower(end($x));
    $file_tmp = $_FILES['video']['tmp_name'];

    // Validasi ekstensi file video demi keamanan server
    if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
        // Beri nama unik menggunakan timestamp agar tidak ada duplikasi nama file
        $namaVideoBaru = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $nama_asal);

        if (move_uploaded_file($file_tmp, "../ASSETS/" . $namaVideoBaru)) {
            // Sambungkan kolom file_video ke query update jika proses pindah file berhasil
            $sql .= ", file_video='$namaVideoBaru'";
        }
    }
}

// Batasi eksekusi baris data (menggunakan LIMIT 1 agar aman)
$sql .= " LIMIT 1";

mysqli_query($conn, $sql);

// Kembalikan halaman admin ke tentang_admin.php
header("Location: tentang_admin.php");
exit();
?>