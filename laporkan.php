<?php
/**
 * laporkan.php (Root) – Bridge file to Users/laporkan.php
 */
require_once __DIR__ . '/db.php';

header("Location: USER/laporkan.php" . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit;