<?php
/**
 * Admin/update_status.php
 * AJAX endpoint: ubah status laporan (hanya admin)
 */
require_once '../db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Akses ditolak. Hanya admin.']); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method tidak valid.']); exit;
}

$id     = intval($_POST['id']     ?? 0);
$status = trim($_POST['status']   ?? '');
$valid  = ['pending', 'diproses', 'selesai', 'ditolak'];

if ($id <= 0 || !in_array($status, $valid)) {
    echo json_encode(['success' => false, 'error' => 'Parameter tidak valid.']); exit;
}

// Ambil status lama
$old_res = $conn->query("SELECT status, judul FROM laporan WHERE id_laporan = $id");
$old_row = $old_res ? $old_res->fetch_assoc() : null;
$old_status    = $old_row['status'] ?? 'pending';
$judul_laporan = $old_row['judul']  ?? '';

if ($status === 'diproses') {
    $stmt = $conn->prepare("UPDATE laporan SET status=?, tanggal_pengerjaan=NOW(), updated_at=NOW() WHERE id_laporan=?");
} else {
    $stmt = $conn->prepare("UPDATE laporan SET status=?, updated_at=NOW() WHERE id_laporan=?");
}
$stmt->bind_param('si', $status, $id);

if ($stmt->execute()) {
    $id_admin = (int)$_SESSION['admin_id'];
    $adm = $conn->prepare("SELECT nama_admin FROM admins WHERE id_admin = ?");
    $adm->bind_param('i', $id_admin); $adm->execute();
    $adm_row = $adm->get_result()->fetch_assoc(); $adm->close();
    $admin_name = $adm_row['nama_admin'] ?? 'Admin';

    logActivity('admin', $id_admin, $admin_name, 'Ubah Status Laporan ke '.$status, 'ID: '.$id.', Judul: '.$judul_laporan);
    logStatusHistory($id, $id_admin, $old_status, $status, 'Status diubah oleh admin.');

    $r = $conn->query("SELECT tanggal_pengerjaan FROM laporan WHERE id_laporan=$id");
    $row = $r ? $r->fetch_assoc() : [];
    echo json_encode(['success' => true, 'status' => $status, 'tanggal_pengerjaan' => $row['tanggal_pengerjaan'] ?? null]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
$stmt->close();
