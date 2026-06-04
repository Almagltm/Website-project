
<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Info</title>

<style>

body{
    font-family:Poppins,sans-serif;
    background:#f4f6f9;
    padding:40px;
}

.container{
    background:white;
    padding:30px;
    border-radius:15px;
    max-width:700px;
    margin:auto;
}

input, textarea{
    width:100%;
    padding:12px;
    margin-top:10px;
    margin-bottom:20px;
    border:1px solid #ccc;
    border-radius:8px;
}

button{
    background:#1e3d8f;
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:8px;
    cursor:pointer;
}

</style>

</head>
<body>

<div class="container">

    <h2>Tambah Pengumuman</h2>

    <form action="proses_tambah_info.php" method="POST">

        <label>Judul</label>
        <input type="text" name="judul" required>

        <label>Isi Pengumuman</label>
        <textarea name="isi" rows="10" required></textarea>

        <label>Penulis</label>
        <input type="text" name="penulis" required>

        <button type="submit">Simpan</button>

    </form>

</div>

</body>
</html>

