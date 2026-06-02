<?php
/**
 * laporan_saya.php (Root) – Bridge file to Users/laporan_saya.php or Admin/Kelola_Laporan.php
 */
require_once __DIR__ . '/db.php';

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: Admin/Kelola_Laporan.php" . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
} else {
    header("Location: Users/laporan_saya.php" . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}
