<?php
/**
 * db.php – Koneksi database aksi_kita
 * Mendeteksi sesi dari login teman (user_id / admin_id)
 * dan memetakannya ke variabel role internal
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // sesuaikan password MySQL Anda
define('DB_NAME', 'aksi_kita');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    die('<p style="font-family:sans-serif;color:red;padding:30px">
         ❌ Koneksi database gagal: ' . htmlspecialchars($conn->connect_error) . '<br>
         Pastikan MySQL/MariaDB berjalan dan database <b>aksi_kita</b> sudah diimport.</p>');
}

$conn->set_charset('utf8mb4');

// Pastikan tabel log_aktivitas ada
$conn->query("CREATE TABLE IF NOT EXISTS `log_aktivitas` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `role` enum('user','admin') NOT NULL,
  `id_actor` int(11) NOT NULL,
  `nama_actor` varchar(100) NOT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `detail` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_log`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

// Inisialisasi session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Pemetaan sesi dari login teman ke variabel role internal:
 * - Teman user  meng-set: $_SESSION['user_id'], $_SESSION['nama_lengkap']
 * - Teman admin meng-set: $_SESSION['admin_id'], $_SESSION['nama_admin']
 * Kita set $_SESSION['role'], $_SESSION['id_user'], $_SESSION['id_admin']
 * supaya file-file saya bisa berjalan dengan benar.
 */
if (!isset($_SESSION['role'])) {
    if (isset($_SESSION['admin_id'])) {
        $_SESSION['role']     = 'admin';
        $_SESSION['id_admin'] = (int)$_SESSION['admin_id'];
    } elseif (isset($_SESSION['user_id'])) {
        $_SESSION['role']   = 'user';
        $_SESSION['id_user'] = (int)$_SESSION['user_id'];
    }
} else {
    // Sinkronkan jika sudah ada role tapi belum ada id mapping
    if ($_SESSION['role'] === 'admin' && !isset($_SESSION['id_admin']) && isset($_SESSION['admin_id'])) {
        $_SESSION['id_admin'] = (int)$_SESSION['admin_id'];
    }
    if ($_SESSION['role'] === 'user' && !isset($_SESSION['id_user']) && isset($_SESSION['user_id'])) {
        $_SESSION['id_user'] = (int)$_SESSION['user_id'];
    }
}

/**
 * Helper: catat aktivitas user/admin ke log_aktivitas
 */
function logActivity($role, $id_actor, $nama_actor, $aktivitas, $detail = null) {
    global $conn;
    if (!isset($conn)) return false;
    $stmt = $conn->prepare("INSERT INTO log_aktivitas (role, id_actor, nama_actor, aktivitas, detail) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param('sisss', $role, $id_actor, $nama_actor, $aktivitas, $detail);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

/**
 * Helper: catat riwayat perubahan status laporan
 */
function logStatusHistory($id_laporan, $id_admin, $status_lama, $status_baru, $catatan = null) {
    global $conn;
    if (!isset($conn)) return false;
    $stmt = $conn->prepare("INSERT INTO status_history (id_laporan, id_admin, status_lama, status_baru, catatan) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param('iisss', $id_laporan, $id_admin, $status_lama, $status_baru, $catatan);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

// ── Global Notification Logic ───────────────────────────────────────────────
$noti_count = 0;
$noti_items = [];

if (isset($conn)) {
    // Notifikasi Admin: laporan pending menunggu verifikasi
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $id_admin_noti = $_SESSION['id_admin'] ?? ($_SESSION['admin_id'] ?? 0);
        if ($id_admin_noti) {
            $n_stmt = $conn->prepare("SELECT id_laporan, judul, created_at FROM laporan WHERE status = 'pending' ORDER BY id_laporan DESC LIMIT 5");
            if ($n_stmt) {
                $n_stmt->execute();
                $n_res = $n_stmt->get_result();
                while ($row = $n_res->fetch_assoc()) {
                    $noti_items[] = [
                        'link'  => 'detail_laporan.php?id=' . $row['id_laporan'],
                        'title' => 'Laporan Baru: ' . htmlspecialchars($row['judul']),
                        'desc'  => 'Menunggu verifikasi Anda.',
                        'time'  => date('d M, H:i', strtotime($row['created_at']))
                    ];
                }
                $noti_count = count($noti_items);
                $n_stmt->close();
            }
        }
    }

    // Notifikasi User: update status laporan milik user
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
        $u_id = $_SESSION['id_user'] ?? ($_SESSION['user_id'] ?? 0);
        if ($u_id) {
            $n_stmt = $conn->prepare("SELECT id_laporan, judul, status, updated_at FROM laporan WHERE id_user = ? AND status != 'pending' ORDER BY updated_at DESC LIMIT 5");
            if ($n_stmt) {
                $n_stmt->bind_param("i", $u_id);
                $n_stmt->execute();
                $n_res = $n_stmt->get_result();
                if ($n_res) {
                    while ($row = $n_res->fetch_assoc()) {
                        $status_txt = match($row['status']) {
                            'diproses' => 'sedang diproses',
                            'selesai'  => 'telah selesai',
                            'ditolak'  => 'ditolak',
                            default    => $row['status']
                        };
                        $noti_items[] = [
                            'link'  => 'user_detail_laporan.php?id=' . $row['id_laporan'],
                            'title' => 'Update Laporan: ' . htmlspecialchars($row['judul']),
                            'desc'  => 'Laporan Anda ' . $status_txt . '.',
                            'time'  => date('d M, H:i', strtotime($row['updated_at']))
                        ];
                    }
                }
                $noti_count = count($noti_items);
                $n_stmt->close();
            }
        }
    }
}