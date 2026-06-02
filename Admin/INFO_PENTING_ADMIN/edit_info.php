
<?php

include 'koneksi.php';

$id = $_GET['id'];

$query = mysqli_query($conn,
"SELECT * FROM info_penting WHERE id_info='$id'");

$data = mysqli_fetch_assoc($query);

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Pengumuman</title>

<!-- FONT -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
      rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    background:#f4f6f9;
    min-height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;

    padding:30px;
}

/* ================= CARD ================= */

.container{

    width:100%;
    max-width:800px;

    background:white;

    padding:40px;

    border-radius:20px;

    box-shadow:0 8px 25px rgba(0,0,0,0.08);

}

/* ================= TITLE ================= */

.title{

    text-align:center;

    color:#1e3d8f;

    margin-bottom:35px;

    font-size:32px;

    font-weight:700;
}

/* ================= FORM ================= */

.form-group{
    margin-bottom:25px;
}

label{

    display:block;

    margin-bottom:10px;

    font-weight:600;

    color:#333;
}

input,
textarea{

    width:100%;

    padding:15px;

    border:1px solid #d0d0d0;

    border-radius:12px;

    font-size:15px;

    outline:none;

    transition:0.3s;

    resize:none;
}

input:focus,
textarea:focus{

    border-color:#1e3d8f;

    box-shadow:0 0 0 4px rgba(30,61,143,0.12);
}

/* ================= TEXTAREA ================= */

textarea{

    min-height:250px;

    line-height:1.7;

    word-break:break-word;
}

/* ================= BUTTONS ================= */

.button-group{

    display:flex;

    justify-content:flex-end;

    gap:15px;

    margin-top:35px;
}

.back-btn,
.update-btn{

    padding:13px 25px;

    border:none;

    border-radius:12px;

    font-weight:600;

    cursor:pointer;

    transition:0.3s;

    text-decoration:none;

    font-size:15px;
}

/* BACK */

.back-btn{

    background:#e5e7eb;

    color:#333;
}

.back-btn:hover{

    background:#d1d5db;
}

/* UPDATE */

.update-btn{

    background:#1e3d8f;

    color:white;
}

.update-btn:hover{

    background:#163170;

    transform:translateY(-2px);
}

/* ================= RESPONSIVE ================= */

@media(max-width:700px){

    .container{
        padding:25px;
    }

    .title{
        font-size:25px;
    }

    .button-group{
        flex-direction:column;
    }

    .back-btn,
    .update-btn{
        width:100%;
        text-align:center;
    }

}

</style>
</head>

<body>

<div class="container">

    <h1 class="title">
        Edit Pengumuman
    </h1>

    <form action="proses_edit_info.php" method="POST">

        <!-- ID HIDDEN -->
        <input type="hidden"
               name="id_info"
               value="<?php echo $data['id_info']; ?>">

        <!-- JUDUL -->
        <div class="form-group">

            <label>Judul Pengumuman</label>

            <input type="text"
                   name="judul"
                   value="<?php echo htmlspecialchars($data['judul']); ?>"
                   required>

        </div>

        <!-- ISI -->
        <div class="form-group">

            <label>Isi Pengumuman</label>

            <textarea name="isi"
                      required><?php echo htmlspecialchars($data['isi']); ?></textarea>

        </div>

        <!-- PENULIS -->
        <div class="form-group">

            <label>Penulis</label>

            <input type="text"
                   name="penulis"
                   value="<?php echo htmlspecialchars($data['penulis']); ?>"
                   required>

        </div>

        <!-- BUTTON -->
        <div class="button-group">

            <a href="ADMIN_INFO.php" class="back-btn">
                Kembali
            </a>

            <button type="submit" class="update-btn">
                Update Pengumuman
            </button>

        </div>

    </form>

</div>

</body>
</html>

