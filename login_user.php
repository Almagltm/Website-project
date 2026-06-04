<?php
/**
 * login_user.php (Root) – Bridge file to Users/MASUK.php
 */
require_once __DIR__ . '/db.php';

header("Location: USER/MASUK.php" . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit;
