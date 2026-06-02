<?php
/**
 * detail_laporan.php (Root) – Bridge file to Admin/detail_laporan.php
 */
require_once __DIR__ . '/db.php';

header("Location: Admin/detail_laporan.php" . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit;
