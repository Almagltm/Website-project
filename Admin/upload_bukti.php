<?php
/**
 * Admin/upload_bukti.php
 * AJAX endpoint: upload foto bukti penyelesaian laporan
 */
require_once '../db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Akses ditolak. Hanya admin.']); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method tidak valid.']); exit;
}

$id_laporan      = intval($_POST['id_laporan']    ?? 0);
$bukti_deskripsi = trim($_POST['bukti_deskripsi'] ?? '');

if ($id_laporan <= 0) { echo json_encode(['success' => false, 'error' => 'ID laporan tidak valid.']); exit; }
if (empty($bukti_deskripsi)) { echo json_encode(['success' => false, 'error' => 'Deskripsi bukti wajib diisi.']); exit; }

// Validasi & simpan foto
if (empty($_FILES['foto_bukti']['name']) || $_FILES['foto_bukti']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Foto bukti wajib diunggah.']); exit;
}
$ext = strtolower(pathinfo($_FILES['foto_bukti']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
    echo json_encode(['success' => false, 'error' => 'Format file tidak didukung.']); exit;
}

$dir = '../uploads/bukti/';
if (!is_dir($dir)) mkdir($dir, 0755, true);
$fname = 'bukti_' . $id_laporan . '_' . time() . '.' . $ext;
if (!move_uploaded_file($_FILES['foto_bukti']['tmp_name'], $dir . $fname)) {
    echo json_encode(['success' => false, 'error' => 'Gagal menyimpan file.']); exit;
}
$path_foto = 'uploads/bukti/' . $fname;

// Insert ke foto_laporan
$s1 = $conn->prepare("INSERT INTO foto_laporan (id_laporan, path_foto) VALUES (?, ?)");
$s1->bind_param('is', $id_laporan, $path_foto); $s1->execute(); $s1->close();

// Ambil status lama
$old_res = $conn->query("SELECT status, judul FROM laporan WHERE id_laporan = $id_laporan");
$old_row = $old_res ? $old_res->fetch_assoc() : null;
$old_status = $old_row['status'] ?? 'diproses';
$judul_laporan = $old_row['judul'] ?? '';

// Update laporan → selesai
$s2 = $conn->prepare("UPDATE laporan SET bukti_deskripsi=?, foto_bukti=?, status='selesai', updated_at=NOW() WHERE id_laporan=?");
$s2->bind_param('ssi', $bukti_deskripsi, $path_foto, $id_laporan);

if ($s2->execute()) {
    $id_admin = (int)$_SESSION['admin_id'];
    $adm = $conn->prepare("SELECT nama_admin FROM admins WHERE id_admin = ?");
    $adm->bind_param('i', $id_admin); $adm->execute();
    $adm_row = $adm->get_result()->fetch_assoc(); $adm->close();
    $admin_name = $adm_row['nama_admin'] ?? 'Admin';

    logActivity('admin', $id_admin, $admin_name, 'Upload Bukti Penyelesaian', 'ID: '.$id_laporan.', Judul: '.$judul_laporan);
    logStatusHistory($id_laporan, $id_admin, $old_status, 'selesai', 'Bukti diunggah: '.$bukti_deskripsi);

    echo json_encode(['success' => true, 'message' => 'Bukti berhasil diunggah, laporan diselesaikan.']);
} else {
    echo json_encode(['success' => false, 'error' => 'Gagal update DB: '.$s2->error]);
}
$s2->close();
