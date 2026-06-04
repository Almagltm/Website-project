<?php
/**
 * profile_modal.php (Root) – Bridge to load appropriate modal
 */
require_once __DIR__ . '/db.php';

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    include __DIR__ . '/ADMIN/profile_modal_admin.php';
} else {
    include __DIR__ . '/USER/profile_modal.php';
}