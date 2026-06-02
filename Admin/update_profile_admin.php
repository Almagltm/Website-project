<?php
/**
 * Admin/update_profile_admin.php
 * AJAX endpoint: update profil atau password admin
 */
require_once '../db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Akses ditolak.']); exit;
}
$id_admin = (int)$_SESSION['admin_id'];
$action   = $_POST['action'] ?? '';

if ($action === 'update_profile') {
    $nama  = trim($_POST['nama_admin'] ?? '');
    $email = trim($_POST['email'] ?? '');
    if (!$nama) { echo json_encode(['success' => false, 'error' => 'Nama tidak boleh kosong.']); exit; }

    $stmt = $conn->prepare("UPDATE admins SET nama_admin = ?, email = ? WHERE id_admin = ?");
    $stmt->bind_param('ssi', $nama, $email, $id_admin);
    if ($stmt->execute()) {
        $_SESSION['nama_admin'] = $nama;
        logActivity('admin', $id_admin, $nama, 'Mengubah Profil Admin', 'Nama: '.$nama);
        echo json_encode(['success' => true, 'message' => 'Profil berhasil diperbarui.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Gagal memperbarui profil.']);
    }
    $stmt->close(); exit;
}

if ($action === 'update_password') {
    $pw_lama = $_POST['password_lama'] ?? '';
    $pw_baru = $_POST['password_baru'] ?? '';
    if (strlen($pw_baru) < 6) { echo json_encode(['success' => false, 'error' => 'Password minimal 6 karakter.']); exit; }

    $stmt = $conn->prepare("SELECT password FROM admins WHERE id_admin = ?");
    $stmt->bind_param('i', $id_admin); $stmt->execute();
    $res  = $stmt->get_result()->fetch_assoc(); $stmt->close();
    if (!$res) { echo json_encode(['success' => false, 'error' => 'Admin tidak ditemukan.']); exit; }

    $db_pass = $res['password'];
    $valid = password_verify($pw_lama, $db_pass) || (strlen($db_pass) === 32 && md5($pw_lama) === $db_pass);
    if (!$valid) { echo json_encode(['success' => false, 'error' => 'Password lama tidak sesuai.']); exit; }

    $new_hash = password_hash($pw_baru, PASSWORD_DEFAULT);
    $upd = $conn->prepare("UPDATE admins SET password = ? WHERE id_admin = ?");
    $upd->bind_param('si', $new_hash, $id_admin);
    if ($upd->execute()) {
        $nama = $_SESSION['nama_admin'] ?? 'Admin';
        logActivity('admin', $id_admin, $nama, 'Mengubah Password Admin', '');
        echo json_encode(['success' => true, 'message' => 'Password berhasil diubah.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Gagal mengubah password.']);
    }
    $upd->close(); exit;
}

echo json_encode(['success' => false, 'error' => 'Aksi tidak dikenal.']);
