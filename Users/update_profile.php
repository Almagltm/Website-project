<?php
/**
 * Users/update_profile.php
 * AJAX endpoint: update profil, email, atau password user
 */
require_once '../db.php';
header('Content-Type: application/json');

// Guard
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Sesi tidak valid. Silakan login ulang.']);
    exit;
}

$id_user = (int)$_SESSION['user_id'];
$action  = $_POST['action'] ?? '';

if (!$action) {
    echo json_encode(['success' => false, 'error' => 'Aksi tidak valid.']); exit;
}

// ── UPDATE PROFIL (nama + no_telp) ──────────────────────────────────────────
if ($action === 'update_profile') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $telp = trim($_POST['no_telp'] ?? '');

    if (!$nama) { echo json_encode(['success' => false, 'error' => 'Nama lengkap wajib diisi.']); exit; }

    $stmt = $conn->prepare("UPDATE users SET nama_lengkap = ?, no_telp = ? WHERE id_user = ?");
    $stmt->bind_param('ssi', $nama, $telp, $id_user);
    if ($stmt->execute()) {
        $_SESSION['nama_lengkap'] = $nama;
        logActivity('user', $id_user, $nama, 'Mengubah Profil', 'Nama: '.$nama.', Telp: '.$telp);
        echo json_encode(['success' => true, 'message' => 'Profil berhasil diperbarui.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Gagal memperbarui profil.']);
    }
    $stmt->close(); exit;
}

// ── UPDATE EMAIL ─────────────────────────────────────────────────────────────
if ($action === 'update_email') {
    $email = trim($_POST['email'] ?? '');
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Email tidak valid.']); exit;
    }
    // Cek duplikat
    $check = $conn->prepare("SELECT id_user FROM users WHERE email = ? AND id_user != ?");
    $check->bind_param('si', $email, $id_user); $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Email sudah digunakan pengguna lain.']); $check->close(); exit;
    }
    $check->close();

    $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id_user = ?");
    $stmt->bind_param('si', $email, $id_user);
    if ($stmt->execute()) {
        $_SESSION['email'] = $email;
        $nama = $_SESSION['nama_lengkap'] ?? 'User';
        logActivity('user', $id_user, $nama, 'Mengubah Email', 'Email baru: '.$email);
        echo json_encode(['success' => true, 'message' => 'Email berhasil diperbarui.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Gagal memperbarui email.']);
    }
    $stmt->close(); exit;
}

// ── UPDATE PASSWORD ──────────────────────────────────────────────────────────
if ($action === 'update_password') {
    $pw_lama = $_POST['password_lama'] ?? '';
    $pw_baru = $_POST['password_baru'] ?? '';
    if (strlen($pw_baru) < 6) { echo json_encode(['success' => false, 'error' => 'Password baru minimal 6 karakter.']); exit; }

    $stmt = $conn->prepare("SELECT password FROM users WHERE id_user = ?");
    $stmt->bind_param('i', $id_user); $stmt->execute();
    $res  = $stmt->get_result()->fetch_assoc(); $stmt->close();

    if (!$res) { echo json_encode(['success' => false, 'error' => 'Pengguna tidak ditemukan.']); exit; }

    $db_pass = $res['password'];
    // Cek password lama (support bcrypt atau MD5 lama)
    $valid = password_verify($pw_lama, $db_pass) || (strlen($db_pass) === 32 && md5($pw_lama) === $db_pass);
    if (!$valid) { echo json_encode(['success' => false, 'error' => 'Password lama tidak sesuai.']); exit; }

    // Simpan dengan bcrypt
    $new_hash = password_hash($pw_baru, PASSWORD_DEFAULT);
    $upd = $conn->prepare("UPDATE users SET password = ? WHERE id_user = ?");
    $upd->bind_param('si', $new_hash, $id_user);
    if ($upd->execute()) {
        $nama = $_SESSION['nama_lengkap'] ?? 'User';
        logActivity('user', $id_user, $nama, 'Mengubah Password', 'Password berhasil diganti.');
        echo json_encode(['success' => true, 'message' => 'Password berhasil diubah.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Gagal mengubah password.']);
    }
    $upd->close(); exit;
}

echo json_encode(['success' => false, 'error' => 'Aksi tidak dikenal.']);
