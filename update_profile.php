<?php
/**
 * update_profile.php (Root) – Bridge file to delegate profile updates
 */
require_once __DIR__ . '/db.php';

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    include __DIR__ . '/ADMIN/update_profile_admin.php';
} else {
    include __DIR__ . '/USER/update_profile.php';
}