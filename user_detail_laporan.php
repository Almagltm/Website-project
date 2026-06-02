<?php
/**
 * user_detail_laporan.php (Root) – Bridge file to Users/user_detail_laporan.php
 */
require_once __DIR__ . '/db.php';

header("Location: Users/user_detail_laporan.php" . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit;
