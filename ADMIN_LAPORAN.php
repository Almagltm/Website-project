<?php
// ====================================================================
// ADMIN LAPORAN PAGE
// Halaman manajemen laporan untuk admin
// ====================================================================

// Simulasi session admin (dalam implementasi nyata, gunakan session yang proper)
// session_start();
// if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin – Manajemen Laporan | Aksi Kita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<style>
/* ====================================================================
   1. GLOBAL RESET & BASE
   ==================================================================== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: linear-gradient(160deg, #f4f7fc 0%, #d2f0ff 100%);
    color: #333;
    min-height: 100vh;
}

/* ====================================================================
   2. NAVBAR ADMIN
   ==================================================================== */
.navbar {
    background: #1e3d8f;
    color: white;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 13px 55px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    gap: 50px;
    box-shadow: 0 2px 15px rgba(30, 61, 143, 0.35);
}

.logo img {
    height: 55px;
}

.navlinks a {
    color: white;
    text-decoration: none;
    margin: 0 15px;
    padding: 5px 0;
    transition: 0.3s ease;
    font-size: 16px;
    opacity: 0.85;
}
.navlinks a:hover { opacity: 1; }
.navlinks a.active {
    text-decoration: underline;
    opacity: 1;
    font-weight: 600;
}

/* Admin badge pill */
.admin-badge {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255,255,255,0.13);
    border: 1.5px solid rgba(255,255,255,0.3);
    padding: 6px 16px 6px 10px;
    border-radius: 30px;
    cursor: pointer;
    transition: 0.25s;
}
.admin-badge:hover { background: rgba(255,255,255,0.22); }

.admin-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffd700, #f4a200);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 700;
    color: #1e3d8f;
}

.admin-name {
    font-size: 15px;
    font-weight: 600;
    color: white;
}
.admin-role {
    font-size: 11px;
    color: rgba(255,255,255,0.65);
    line-height: 1;
}

/* ====================================================================
   3. PAGE WRAPPER
   ==================================================================== */
.page-wrapper {
    max-width: 1200px;
    margin: 100px auto 60px;
    padding: 0 25px;
}

/* ====================================================================
   4. BREADCRUMB HEADER
   ==================================================================== */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #1e3d8f;
    color: white;
    padding: 20px 30px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(30, 61, 143, 0.25);
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 22px;
    font-weight: 700;
    letter-spacing: 0.4px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.page-header-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-back {
    text-decoration: none;
    color: white;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    background: rgba(255,255,255,0.15);
    border-radius: 30px;
    font-size: 14px;
    transition: 0.25s;
    border: none;
    cursor: pointer;
}
.btn-back:hover { background: rgba(255,255,255,0.28); }

/* ====================================================================
   5. STATS SUMMARY CARDS (mini dashboard)
   ==================================================================== */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 14px;
    padding: 22px 20px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.07);
    display: flex;
    align-items: center;
    gap: 16px;
    border-left: 5px solid transparent;
    transition: transform 0.2s, box-shadow 0.2s;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.11); }

.stat-card.total  { border-color: #1e3d8f; }
.stat-card.proses { border-color: #ffa000; }
.stat-card.selesai{ border-color: #388e3c; }
.stat-card.ditolak{ border-color: #e63946; }

.stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.stat-card.total   .stat-icon { background: #eef1ff; color: #1e3d8f; }
.stat-card.proses  .stat-icon { background: #fff8e1; color: #ffa000; }
.stat-card.selesai .stat-icon { background: #e8f5e9; color: #388e3c; }
.stat-card.ditolak .stat-icon { background: #fdecea; color: #e63946; }

.stat-info p {
    font-size: 12px;
    color: #888;
    font-weight: 500;
    margin-bottom: 2px;
}
.stat-info h3 {
    font-size: 26px;
    font-weight: 700;
    color: #222;
}

/* ====================================================================
   6. FILTER TOOLBAR
   ==================================================================== */
.toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.filter-tabs {
    display: flex;
    gap: 8px;
    background: rgb(245, 235, 213);
    padding: 8px;
    border-radius: 40px;
    border: 1.5px solid #1e3d8f;
    box-shadow: 0 3px 8px rgba(0,0,0,0.07);
}

.tab-btn {
    padding: 7px 22px;
    border-radius: 30px;
    border: none;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    background: #ddcba2;
    color: #4a4a4a;
    transition: all 0.22s;
    white-space: nowrap;
}
.tab-btn.active {
    background: #1e3d8f;
    color: white;
    font-weight: 600;
    box-shadow: 0 4px 10px rgba(30,61,143,0.2);
}
.tab-btn:hover:not(.active) { background: #cbb98a; }

.toolbar-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-box {
    display: flex;
    align-items: center;
    background: white;
    border: 1.5px solid #d0d8ee;
    border-radius: 30px;
    padding: 7px 16px;
    gap: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.search-box i { color: #aaa; font-size: 14px; }
.search-box input {
    border: none;
    outline: none;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    width: 190px;
    color: #333;
}

.btn-export {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 9px 20px;
    background: #1e3d8f;
    color: white;
    border: none;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.25s;
    font-family: 'Poppins', sans-serif;
}
.btn-export:hover { background: #16317a; }

/* ====================================================================
   7. LAPORAN GRID
   ==================================================================== */
.laporan-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
}

.laporan-card {
    background: white;
    border-radius: 14px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: transform 0.22s, box-shadow 0.22s;
    display: flex;
    flex-direction: column;
}
.laporan-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.13);
}

.card-img {
    position: relative;
    height: 185px;
    overflow: hidden;
}
.card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.35s;
}
.laporan-card:hover .card-img img { transform: scale(1.04); }

.card-category {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(0,0,0,0.62);
    color: white;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    backdrop-filter: blur(3px);
}

.card-id {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(30,61,143,0.85);
    color: white;
    padding: 3px 9px;
    border-radius: 5px;
    font-size: 11px;
    font-weight: 500;
}

.card-body {
    padding: 14px 16px 10px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.card-location {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    font-size: 13px;
    color: #555;
    line-height: 1.5;
    margin-bottom: 8px;
}
.card-location i { color: #1e3d8f; font-size: 14px; margin-top: 2px; flex-shrink: 0; }

.card-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 12px;
    color: #999;
    margin-bottom: 12px;
}
.card-meta i { font-size: 11px; }

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 12px;
    border-top: 1px solid #f0f0f0;
    margin-top: auto;
}

/* Status badges */
.badge {
    padding: 5px 13px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.badge-proses  { background: #fff8e1; color: #ffa000; }
.badge-selesai { background: #e8f5e9; color: #388e3c; }
.badge-ditolak { background: #fdecea; color: #e63946; }
.badge-baru    { background: #e8eeff; color: #1e3d8f; }

/* Admin action buttons */
.card-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-action {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: 0.2s;
}
.btn-detail  { background: #eef1ff; color: #1e3d8f; }
.btn-approve { background: #e8f5e9; color: #388e3c; }
.btn-reject  { background: #fdecea; color: #e63946; }
.btn-detail:hover  { background: #1e3d8f; color: white; }
.btn-approve:hover { background: #388e3c; color: white; }
.btn-reject:hover  { background: #e63946; color: white; }

/* ====================================================================
   8. EMPTY STATE
   ==================================================================== */
.empty-state {
    text-align: center;
    padding: 70px 20px;
    color: #aaa;
    display: none;
}
.empty-state i { font-size: 52px; margin-bottom: 16px; opacity: 0.4; }
.empty-state p { font-size: 16px; }

/* ====================================================================
   9. PAGINATION
   ==================================================================== */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 35px;
}

.page-btn {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: 1.5px solid #d0d8ee;
    background: white;
    color: #555;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.page-btn.active { background: #1e3d8f; color: white; border-color: #1e3d8f; font-weight: 600; }
.page-btn:hover:not(.active) { background: #eef1ff; border-color: #1e3d8f; color: #1e3d8f; }

/* ====================================================================
   10. MODAL DETAIL LAPORAN
   ==================================================================== */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    z-index: 9000;
    display: none;
    justify-content: center;
    align-items: center;
    padding: 20px;
    backdrop-filter: blur(3px);
    animation: fadeInOverlay 0.2s ease;
}
.modal-overlay.show { display: flex; }

@keyframes fadeInOverlay { from { opacity: 0; } to { opacity: 1; } }

.modal-box {
    background: white;
    border-radius: 18px;
    width: 100%;
    max-width: 640px;
    max-height: 88vh;
    overflow-y: auto;
    box-shadow: 0 25px 60px rgba(0,0,0,0.25);
    animation: slideUp 0.25s ease;
}

@keyframes slideUp {
    from { transform: translateY(30px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}

.modal-header {
    background: #1e3d8f;
    color: white;
    padding: 18px 24px;
    border-radius: 18px 18px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-header h2 { font-size: 18px; font-weight: 700; }

.modal-close {
    background: rgba(255,255,255,0.15);
    border: none;
    color: white;
    width: 34px;
    height: 34px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s;
}
.modal-close:hover { background: rgba(255,255,255,0.3); }

.modal-img {
    width: 100%;
    height: 230px;
    object-fit: cover;
}

.modal-body {
    padding: 22px 24px;
}

.detail-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 14px;
    font-size: 14px;
}
.detail-row i {
    color: #1e3d8f;
    font-size: 16px;
    margin-top: 2px;
    width: 18px;
    flex-shrink: 0;
}
.detail-row .label {
    color: #888;
    font-size: 12px;
    display: block;
    margin-bottom: 1px;
}
.detail-row .value {
    color: #222;
    font-weight: 500;
}

.modal-divider {
    border: none;
    border-top: 1.5px solid #f0f0f0;
    margin: 16px 0;
}

.modal-footer {
    padding: 0 24px 22px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-modal {
    flex: 1;
    min-width: 130px;
    padding: 11px 20px;
    border-radius: 10px;
    border: none;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: 0.22s;
}
.btn-modal-approve {
    background: #388e3c;
    color: white;
}
.btn-modal-approve:hover { background: #2e7d32; }

.btn-modal-reject {
    background: #fdecea;
    color: #e63946;
    border: 1.5px solid #e63946;
}
.btn-modal-reject:hover { background: #e63946; color: white; }

.btn-modal-bukti {
    background: #1e3d8f;
    color: white;
}
.btn-modal-bukti:hover { background: #16317a; }

/* ====================================================================
   11. MODAL BUKTI SELESAI
   ==================================================================== */
.bukti-modal .modal-body {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.upload-area {
    border: 2px dashed #c0ccee;
    border-radius: 12px;
    padding: 40px 20px;
    text-align: center;
    background: #f8f9ff;
    cursor: pointer;
    transition: 0.2s;
}
.upload-area:hover { border-color: #1e3d8f; background: #eef1ff; }
.upload-area i { font-size: 36px; color: #b0bde0; margin-bottom: 10px; display: block; }
.upload-area p { color: #888; font-size: 14px; }
.upload-area span { color: #1e3d8f; font-weight: 600; }

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #444;
}
.form-group textarea,
.form-group input,
.form-group select {
    border: 1.5px solid #d0d8ee;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    color: #333;
    outline: none;
    transition: 0.2s;
    resize: vertical;
}
.form-group textarea:focus,
.form-group input:focus,
.form-group select:focus {
    border-color: #1e3d8f;
    box-shadow: 0 0 0 3px rgba(30,61,143,0.1);
}

/* ====================================================================
   12. MODAL KONFIRMASI
   ==================================================================== */
.confirm-modal .modal-box {
    max-width: 400px;
}
.confirm-body {
    padding: 30px 24px;
    text-align: center;
}
.confirm-icon {
    width: 65px;
    height: 65px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin: 0 auto 18px;
}
.confirm-icon.approve { background: #e8f5e9; color: #388e3c; }
.confirm-icon.reject  { background: #fdecea; color: #e63946; }
.confirm-body h3 { font-size: 18px; margin-bottom: 8px; color: #222; }
.confirm-body p  { font-size: 14px; color: #777; }

/* ====================================================================
   13. TOAST NOTIFICATION
   ==================================================================== */
.toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #1e3d8f;
    color: white;
    padding: 14px 22px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    box-shadow: 0 8px 25px rgba(30,61,143,0.3);
    transform: translateY(80px);
    opacity: 0;
    transition: 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    z-index: 99999;
    display: flex;
    align-items: center;
    gap: 10px;
}
.toast.show { transform: translateY(0); opacity: 1; }
.toast.success { background: #388e3c; }
.toast.error   { background: #e63946; }

/* ====================================================================
   14. FOOTER
   ==================================================================== */
.main-footer {
    background: linear-gradient(165deg, #080e18 0%, #102647 70%, #9c7719 120%);
    color: #fff;
    padding: 60px 70px;
    margin-top: 60px;
}
.footer-top {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 40px;
}
.footer-logo { width: 75px; height: 55px; object-fit: cover; }
.footer-content {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    margin-bottom: 45px;
    gap: 20px;
}
.footer-col { flex: 1; min-width: 200px; }
.footer-col p  { margin: 6px 0; color: #ccc; font-size: 15px; }
.footer-col a  { display: block; margin: 6px 0; color: #eee; text-decoration: none; font-size: 15px; transition: 0.2s; }
.footer-col a:hover { color: #0077ff; }
.footer-social { display: flex; gap: 15px; margin-bottom: 35px; }
.footer-social a {
    width: 40px; height: 40px; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    color: #000; background: #fff; text-decoration: none; font-size: 18px; transition: 0.3s;
}
.footer-social a:hover { transform: translateY(-5px); }
.footer-bottom { text-align: center; font-size: 14px; color: #ccc; }

/* ====================================================================
   15. RESPONSIVE
   ==================================================================== */
@media (max-width: 1050px) {
    .laporan-grid { grid-template-columns: repeat(2, 1fr); }
    .stats-row    { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 700px) {
    .navbar { padding: 13px 20px; gap: 20px; }
    .navlinks a { margin: 0 8px; font-size: 14px; }
    .laporan-grid { grid-template-columns: 1fr; }
    .stats-row    { grid-template-columns: repeat(2, 1fr); }
    .toolbar      { flex-direction: column; align-items: flex-start; }
    .main-footer  { padding: 40px 25px; }
}
</style>

<body>

<!-- ================================================================
     NAVBAR
     ================================================================ -->
<header class="navbar">
    <div class="logo">
        <img src="ASSETS/LOGO.png" alt="Logo Aksi Kita" onerror="this.style.display='none'">
    </div>
    <nav class="navlinks">
        <a href="#">Beranda</a>
        <a href="#" class="active">Laporan</a>
        <a href="#">Peringkat</a>
        <a href="#">Tentang</a>
    </nav>
    <div class="admin-badge" onclick="showToast('Menu admin sedang dikembangkan.', 'info')">
        <div class="admin-avatar">A</div>
        <div>
            <div class="admin-name">Admin</div>
            <div class="admin-role">Super Admin</div>
        </div>
        <i class="fas fa-chevron-down" style="color:rgba(255,255,255,0.6); font-size:12px;"></i>
    </div>
</header>

<!-- ================================================================
     MAIN CONTENT
     ================================================================ -->
<main class="page-wrapper">

    <!-- Breadcrumb / Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-clipboard-list"></i> Manajemen Laporan</h1>
        <div class="page-header-right">
            <button class="btn-back" onclick="showToast('Kembali ke beranda admin.', 'info')">
                <i class="fas fa-arrow-left"></i> Beranda Admin
            </button>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="stats-row">
        <div class="stat-card total">
            <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            <div class="stat-info">
                <p>Total Laporan</p>
                <h3 id="statTotal">12</h3>
            </div>
        </div>
        <div class="stat-card baru" style="border-color:#1e3d8f;">
            <div class="stat-icon" style="background:#e8eeff;color:#1e3d8f;"><i class="fas fa-inbox"></i></div>
            <div class="stat-info">
                <p>Laporan Baru</p>
                <h3 id="statBaru">3</h3>
            </div>
        </div>
        <div class="stat-card proses">
            <div class="stat-icon"><i class="fas fa-spinner"></i></div>
            <div class="stat-info">
                <p>Sedang Diproses</p>
                <h3 id="statProses">5</h3>
            </div>
        </div>
        <div class="stat-card selesai">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <p>Selesai</p>
                <h3 id="statSelesai">4</h3>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="toolbar">
        <div class="filter-tabs">
            <button class="tab-btn active" onclick="filterCards('semua', this)">Semua</button>
            <button class="tab-btn" onclick="filterCards('baru', this)">Baru</button>
            <button class="tab-btn" onclick="filterCards('proses', this)">Diproses</button>
            <button class="tab-btn" onclick="filterCards('selesai', this)">Selesai</button>
            <button class="tab-btn" onclick="filterCards('ditolak', this)">Ditolak</button>
        </div>
        <div class="toolbar-right">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari laporan..." oninput="searchCards(this.value)">
            </div>
            <button class="btn-export" onclick="showToast('Fitur ekspor sedang dikembangkan.', 'info')">
                <i class="fas fa-download"></i> Ekspor
            </button>
        </div>
    </div>

    <!-- Laporan Grid -->
    <div class="laporan-grid" id="laporanGrid">

        <!-- Card 1 -->
        <div class="laporan-card" data-status="baru" data-title="Kerusakan Tiang Kabel Listrik">
            <div class="card-img">
                <img src="ASSETS/kerusakan_tiang.png" alt="Kerusakan Tiang"
                     onerror="this.src='https://placehold.co/400x185/eef1ff/1e3d8f?text=Kerusakan+Tiang'">
                <span class="card-category">Infrastruktur</span>
                <span class="card-id">#LPR-001</span>
            </div>
            <div class="card-body">
                <p class="card-location">
                    <i class="fas fa-map-marker-alt"></i>
                    Jl. K.H. Syahdan No. 9, Kemanggisan, Jakarta Barat
                </p>
                <div class="card-meta">
                    <span><i class="fas fa-user"></i> user2345</span>
                    <span><i class="fas fa-calendar"></i> 10 Mei 2026</span>
                </div>
                <div class="card-footer">
                    <span class="badge badge-baru">BARU</span>
                    <div class="card-actions">
                        <button class="btn-action btn-detail" title="Lihat Detail" onclick="openDetail(0)"><i class="fas fa-eye"></i></button>
                        <button class="btn-action btn-approve" title="Setujui" onclick="openConfirm('approve', 0)"><i class="fas fa-check"></i></button>
                        <button class="btn-action btn-reject" title="Tolak" onclick="openConfirm('reject', 0)"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="laporan-card" data-status="proses" data-title="Kerusakan Jalan Berlubang">
            <div class="card-img">
                <img src="ASSETS/kerusakan_jalan.png" alt="Kerusakan Jalan"
                     onerror="this.src='https://placehold.co/400x185/fff8e1/ffa000?text=Kerusakan+Jalan'">
                <span class="card-category">Jalan</span>
                <span class="card-id">#LPR-002</span>
            </div>
            <div class="card-body">
                <p class="card-location">
                    <i class="fas fa-map-marker-alt"></i>
                    Jl. KH. Zainul Arifin No.7, Madras Hulu, Medan Polonia
                </p>
                <div class="card-meta">
                    <span><i class="fas fa-user"></i> user1800</span>
                    <span><i class="fas fa-calendar"></i> 09 Mei 2026</span>
                </div>
                <div class="card-footer">
                    <span class="badge badge-proses">DIPROSES</span>
                    <div class="card-actions">
                        <button class="btn-action btn-detail" title="Lihat Detail" onclick="openDetail(1)"><i class="fas fa-eye"></i></button>
                        <button class="btn-action btn-approve" title="Tandai Selesai" onclick="openBukti(1)"><i class="fas fa-flag-checkered"></i></button>
                        <button class="btn-action btn-reject" title="Tolak" onclick="openConfirm('reject', 1)"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="laporan-card" data-status="selesai" data-title="Kerusakan Halte Bus">
            <div class="card-img">
                <img src="ASSETS/kerusakan_halte.png" alt="Kerusakan Halte"
                     onerror="this.src='https://placehold.co/400x185/e8f5e9/388e3c?text=Kerusakan+Halte'">
                <span class="card-category">Transportasi</span>
                <span class="card-id">#LPR-003</span>
            </div>
            <div class="card-body">
                <p class="card-location">
                    <i class="fas fa-map-marker-alt"></i>
                    Jl. Jatinegara Timur, Bali Mester, Jatinegara, Jakarta Timur
                </p>
                <div class="card-meta">
                    <span><i class="fas fa-user"></i> user0412</span>
                    <span><i class="fas fa-calendar"></i> 07 Mei 2026</span>
                </div>
                <div class="card-footer">
                    <span class="badge badge-selesai">SELESAI</span>
                    <div class="card-actions">
                        <button class="btn-action btn-detail" title="Lihat Detail" onclick="openDetail(2)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="laporan-card" data-status="proses" data-title="Kerusakan Lampu Merah">
            <div class="card-img">
                <img src="ASSETS/kerusakan_lampu.png" alt="Kerusakan Lampu"
                     onerror="this.src='https://placehold.co/400x185/fff8e1/ffa000?text=Kerusakan+Lampu'">
                <span class="card-category">Penerangan</span>
                <span class="card-id">#LPR-004</span>
            </div>
            <div class="card-body">
                <p class="card-location">
                    <i class="fas fa-map-marker-alt"></i>
                    Jl. Pangeran Antasari, Cilandak, Jakarta Selatan
                </p>
                <div class="card-meta">
                    <span><i class="fas fa-user"></i> user3310</span>
                    <span><i class="fas fa-calendar"></i> 06 Mei 2026</span>
                </div>
                <div class="card-footer">
                    <span class="badge badge-proses">DIPROSES</span>
                    <div class="card-actions">
                        <button class="btn-action btn-detail" title="Lihat Detail" onclick="openDetail(3)"><i class="fas fa-eye"></i></button>
                        <button class="btn-action btn-approve" title="Tandai Selesai" onclick="openBukti(3)"><i class="fas fa-flag-checkered"></i></button>
                        <button class="btn-action btn-reject" title="Tolak" onclick="openConfirm('reject', 3)"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 5 -->
        <div class="laporan-card" data-status="ditolak" data-title="Kerusakan Halte Senayan">
            <div class="card-img">
                <img src="ASSETS/kerusakan_halte2.png" alt="Kerusakan Halte"
                     onerror="this.src='https://placehold.co/400x185/fdecea/e63946?text=Kerusakan+Halte'">
                <span class="card-category">Transportasi</span>
                <span class="card-id">#LPR-005</span>
            </div>
            <div class="card-body">
                <p class="card-location">
                    <i class="fas fa-map-marker-alt"></i>
                    Jl. Jend. Sudirman, Senayan, Jakarta Pusat
                </p>
                <div class="card-meta">
                    <span><i class="fas fa-user"></i> user7823</span>
                    <span><i class="fas fa-calendar"></i> 05 Mei 2026</span>
                </div>
                <div class="card-footer">
                    <span class="badge badge-ditolak">DITOLAK</span>
                    <div class="card-actions">
                        <button class="btn-action btn-detail" title="Lihat Detail" onclick="openDetail(4)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 6 -->
        <div class="laporan-card" data-status="baru" data-title="Kerusakan Lampu Jalan">
            <div class="card-img">
                <img src="ASSETS/kerusakan_lampu2.png" alt="Kerusakan Lampu"
                     onerror="this.src='https://placehold.co/400x185/eef1ff/1e3d8f?text=Kerusakan+Lampu'">
                <span class="card-category">Penerangan</span>
                <span class="card-id">#LPR-006</span>
            </div>
            <div class="card-body">
                <p class="card-location">
                    <i class="fas fa-map-marker-alt"></i>
                    Jl. Ahmad Yani, Bekasi Timur, Jawa Barat
                </p>
                <div class="card-meta">
                    <span><i class="fas fa-user"></i> user9001</span>
                    <span><i class="fas fa-calendar"></i> 11 Mei 2026</span>
                </div>
                <div class="card-footer">
                    <span class="badge badge-baru">BARU</span>
                    <div class="card-actions">
                        <button class="btn-action btn-detail" title="Lihat Detail" onclick="openDetail(5)"><i class="fas fa-eye"></i></button>
                        <button class="btn-action btn-approve" title="Setujui" onclick="openConfirm('approve', 5)"><i class="fas fa-check"></i></button>
                        <button class="btn-action btn-reject" title="Tolak" onclick="openConfirm('reject', 5)"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 7 -->
        <div class="laporan-card" data-status="selesai" data-title="Kerusakan Tiang Kabel Listrik">
            <div class="card-img">
                <img src="ASSETS/rusak3.jpg" alt="Kerusakan"
                     onerror="this.src='https://placehold.co/400x185/e8f5e9/388e3c?text=Infrastruktur'">
                <span class="card-category">Infrastruktur</span>
                <span class="card-id">#LPR-007</span>
            </div>
            <div class="card-body">
                <p class="card-location">
                    <i class="fas fa-map-marker-alt"></i>
                    Jl. Pintu Satu Senayan, Gelora, Tanah Abang, Jakarta Pusat
                </p>
                <div class="card-meta">
                    <span><i class="fas fa-user"></i> user5544</span>
                    <span><i class="fas fa-calendar"></i> 03 Mei 2026</span>
                </div>
                <div class="card-footer">
                    <span class="badge badge-selesai">SELESAI</span>
                    <div class="card-actions">
                        <button class="btn-action btn-detail" title="Lihat Detail" onclick="openDetail(6)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 8 -->
        <div class="laporan-card" data-status="proses" data-title="Kerusakan Jalan Retak">
            <div class="card-img">
                <img src="ASSETS/rusak 1.jpeg" alt="Kerusakan"
                     onerror="this.src='https://placehold.co/400x185/fff8e1/ffa000?text=Jalan+Retak'">
                <span class="card-category">Jalan</span>
                <span class="card-id">#LPR-008</span>
            </div>
            <div class="card-body">
                <p class="card-location">
                    <i class="fas fa-map-marker-alt"></i>
                    Jl. Gatot Subroto, Tebet, Jakarta Selatan
                </p>
                <div class="card-meta">
                    <span><i class="fas fa-user"></i> user2211</span>
                    <span><i class="fas fa-calendar"></i> 04 Mei 2026</span>
                </div>
                <div class="card-footer">
                    <span class="badge badge-proses">DIPROSES</span>
                    <div class="card-actions">
                        <button class="btn-action btn-detail" title="Lihat Detail" onclick="openDetail(7)"><i class="fas fa-eye"></i></button>
                        <button class="btn-action btn-approve" title="Tandai Selesai" onclick="openBukti(7)"><i class="fas fa-flag-checkered"></i></button>
                        <button class="btn-action btn-reject" title="Tolak" onclick="openConfirm('reject', 7)"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 9 -->
        <div class="laporan-card" data-status="baru" data-title="Kerusakan Drainase">
            <div class="card-img">
                <img src="ASSETS/download.jpg" alt="Drainase"
                     onerror="this.src='https://placehold.co/400x185/eef1ff/1e3d8f?text=Drainase'">
                <span class="card-category">Drainase</span>
                <span class="card-id">#LPR-009</span>
            </div>
            <div class="card-body">
                <p class="card-location">
                    <i class="fas fa-map-marker-alt"></i>
                    Jl. Pahlawan, Menteng, Jakarta Pusat
                </p>
                <div class="card-meta">
                    <span><i class="fas fa-user"></i> user6677</span>
                    <span><i class="fas fa-calendar"></i> 12 Mei 2026</span>
                </div>
                <div class="card-footer">
                    <span class="badge badge-baru">BARU</span>
                    <div class="card-actions">
                        <button class="btn-action btn-detail" title="Lihat Detail" onclick="openDetail(8)"><i class="fas fa-eye"></i></button>
                        <button class="btn-action btn-approve" title="Setujui" onclick="openConfirm('approve', 8)"><i class="fas fa-check"></i></button>
                        <button class="btn-action btn-reject" title="Tolak" onclick="openConfirm('reject', 8)"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /laporan-grid -->

    <div class="empty-state" id="emptyState">
        <i class="fas fa-folder-open"></i>
        <p>Tidak ada laporan ditemukan.</p>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
        <button class="page-btn active">1</button>
        <button class="page-btn">2</button>
        <button class="page-btn">3</button>
        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
    </div>

</main>

<!-- ================================================================
     MODAL: DETAIL LAPORAN
     ================================================================ -->
<div class="modal-overlay" id="modalDetail">
    <div class="modal-box">
        <div class="modal-header">
            <h2 id="detailTitle">Detail Laporan</h2>
            <button class="modal-close" onclick="closeModal('modalDetail')"><i class="fas fa-times"></i></button>
        </div>
        <img id="detailImg" class="modal-img" src="" alt="Foto Laporan"
             onerror="this.src='https://placehold.co/640x230/eef1ff/1e3d8f?text=Foto+Laporan'">
        <div class="modal-body">
            <div class="detail-row">
                <i class="fas fa-hashtag"></i>
                <div><span class="label">ID Laporan</span><span class="value" id="detailId"></span></div>
            </div>
            <div class="detail-row">
                <i class="fas fa-user"></i>
                <div><span class="label">Dilaporkan oleh</span><span class="value" id="detailUser"></span></div>
            </div>
            <div class="detail-row">
                <i class="fas fa-map-marker-alt"></i>
                <div><span class="label">Lokasi</span><span class="value" id="detailLocation"></span></div>
            </div>
            <div class="detail-row">
                <i class="fas fa-calendar-alt"></i>
                <div><span class="label">Tanggal Laporan</span><span class="value" id="detailDate"></span></div>
            </div>
            <div class="detail-row">
                <i class="fas fa-tag"></i>
                <div><span class="label">Kategori</span><span class="value" id="detailCategory"></span></div>
            </div>
            <hr class="modal-divider">
            <div class="detail-row">
                <i class="fas fa-align-left"></i>
                <div><span class="label">Deskripsi</span><span class="value" id="detailDesc"></span></div>
            </div>
            <div class="detail-row">
                <i class="fas fa-info-circle"></i>
                <div><span class="label">Status</span><span id="detailStatus"></span></div>
            </div>
        </div>
        <div class="modal-footer" id="detailFooter">
            <!-- buttons injected by JS -->
        </div>
    </div>
</div>

<!-- ================================================================
     MODAL: BUKTI SELESAI PERBAIKAN
     ================================================================ -->
<div class="modal-overlay" id="modalBukti">
    <div class="modal-box bukti-modal">
        <div class="modal-header">
            <h2><i class="fas fa-camera" style="margin-right:8px"></i>Bukti Selesai Perbaikan</h2>
            <button class="modal-close" onclick="closeModal('modalBukti')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size:13px;color:#777;margin-bottom:4px;">Laporan: <b id="buktiLapTitle"></b></p>
            <div class="upload-area" onclick="document.getElementById('fileUpload').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <p><span>Klik untuk unggah</span> atau seret foto bukti perbaikan</p>
                <p style="font-size:12px;margin-top:4px;">JPG, PNG, PDF maks. 10MB</p>
            </div>
            <input type="file" id="fileUpload" accept="image/*,.pdf" style="display:none" onchange="previewFile(this)">
            <div id="filePreview" style="display:none; margin-top:-6px;">
                <img id="previewImg" style="width:100%;border-radius:10px;max-height:180px;object-fit:cover;" src="" alt="">
            </div>
            <div class="form-group">
                <label>Catatan Penyelesaian</label>
                <textarea rows="3" placeholder="Tuliskan keterangan singkat penyelesaian perbaikan..."></textarea>
            </div>
            <div class="form-group">
                <label>Tanggal Selesai</label>
                <input type="date" id="tanggalSelesai">
            </div>
            <div class="form-group">
                <label>Dikerjakan oleh</label>
                <input type="text" placeholder="Nama instansi / petugas yang menangani">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-modal" onclick="closeModal('modalBukti')"
                style="background:#f0f0f0;color:#555;flex:0.5;">Batal</button>
            <button class="btn-modal btn-modal-bukti"
                onclick="submitBukti()"><i class="fas fa-paper-plane"></i> Kirim & Tandai Selesai</button>
        </div>
    </div>
</div>

<!-- ================================================================
     MODAL: KONFIRMASI APPROVE / REJECT
     ================================================================ -->
<div class="modal-overlay confirm-modal" id="modalConfirm">
    <div class="modal-box" style="max-width:400px;">
        <div class="modal-header" id="confirmHeader">
            <h2 id="confirmTitle">Konfirmasi</h2>
            <button class="modal-close" onclick="closeModal('modalConfirm')"><i class="fas fa-times"></i></button>
        </div>
        <div class="confirm-body">
            <div class="confirm-icon" id="confirmIcon">
                <i class="fas fa-check" id="confirmIconI"></i>
            </div>
            <h3 id="confirmHeading"></h3>
            <p id="confirmText"></p>
            <div class="modal-footer" style="padding:20px 0 0; gap:10px;">
                <button class="btn-modal" onclick="closeModal('modalConfirm')"
                    style="background:#f0f0f0;color:#555;flex:0.5;">Batal</button>
                <button class="btn-modal" id="confirmBtn" onclick="executeAction()"></button>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">
    <i class="fas fa-info-circle" id="toastIcon"></i>
    <span id="toastMsg"></span>
</div>

<!-- ================================================================
     FOOTER
     ================================================================ -->
<footer class="main-footer">
    <div class="footer-top">
        <img src="ASSETS/LOGO.png" class="footer-logo" alt="AksiKita"
             onerror="this.style.display='none'">
        <h3>Aksi Kita</h3>
    </div>
    <div class="footer-content">
        <div class="footer-col">
            <p>Jl. Bachireng No. 12, Indonesia</p>
            <p>0821 6888 9060</p>
            <p>info@aksikita.id</p>
        </div>
        <div class="footer-col">
            <a href="#">Unit Layanan Terpadu</a>
            <a href="#">Cara Kerja</a>
            <a href="#">FAQ</a>
            <a href="#">Aturan Penggunaan</a>
        </div>
        <div class="footer-col">
            <a href="#">Lapor</a>
            <a href="#">Statistik</a>
            <a href="#">Peta Situs</a>
            <a href="#">Arsip</a>
        </div>
    </div>
    <div class="footer-social">
        <a href="#"><i class="fab fa-whatsapp"></i></a>
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-youtube"></i></a>
        <a href="#"><i class="fab fa-tiktok"></i></a>
    </div>
    <div class="footer-bottom">© 2026 AksiKita. Semua Hak Dilindungi. — Panel Admin</div>
</footer>

<!-- ================================================================
     JAVASCRIPT
     ================================================================ -->
<script>
// ── Data laporan (simulasi) ────────────────────────────────────────
const reports = [
    { id:'#LPR-001', title:'Kerusakan Tiang Kabel Listrik', user:'user2345', date:'10 Mei 2026',
      location:'Jl. K.H. Syahdan No. 9, Kemanggisan, Jakarta Barat', category:'Infrastruktur',
      status:'baru', desc:'Tiang kabel listrik tampak miring dan hampir roboh, berbahaya bagi pejalan kaki.',
      img:'ASSETS/kerusakan_tiang.png' },
    { id:'#LPR-002', title:'Kerusakan Jalan Berlubang', user:'user1800', date:'09 Mei 2026',
      location:'Jl. KH. Zainul Arifin No.7, Madras Hulu, Medan Polonia', category:'Jalan',
      status:'proses', desc:'Jalan berlubang cukup dalam ± 30 cm berpotensi menyebabkan kecelakaan, terutama saat malam hari.',
      img:'ASSETS/kerusakan_jalan.png' },
    { id:'#LPR-003', title:'Kerusakan Halte Bus', user:'user0412', date:'07 Mei 2026',
      location:'Jl. Jatinegara Timur, Bali Mester, Jatinegara, Jakarta Timur', category:'Transportasi',
      status:'selesai', desc:'Atap halte bus runtuh sebagian, kursi sudah tidak ada, perlu renovasi total.',
      img:'ASSETS/kerusakan_halte.png' },
    { id:'#LPR-004', title:'Kerusakan Lampu Merah', user:'user3310', date:'06 Mei 2026',
      location:'Jl. Pangeran Antasari, Cilandak, Jakarta Selatan', category:'Penerangan',
      status:'proses', desc:'Lampu merah persimpangan mati total sudah 3 hari, menyebabkan kemacetan parah.',
      img:'ASSETS/kerusakan_lampu.png' },
    { id:'#LPR-005', title:'Kerusakan Halte Senayan', user:'user7823', date:'05 Mei 2026',
      location:'Jl. Jend. Sudirman, Senayan, Jakarta Pusat', category:'Transportasi',
      status:'ditolak', desc:'Laporan tidak lengkap — foto bukti tidak jelas dan deskripsi tidak memadai.',
      img:'ASSETS/kerusakan_halte2.png' },
    { id:'#LPR-006', title:'Kerusakan Lampu Jalan', user:'user9001', date:'11 Mei 2026',
      location:'Jl. Ahmad Yani, Bekasi Timur, Jawa Barat', category:'Penerangan',
      status:'baru', desc:'Lampu jalan mati sepanjang 200 meter, area sangat gelap saat malam hari.',
      img:'ASSETS/kerusakan_lampu2.png' },
    { id:'#LPR-007', title:'Kerusakan Tiang Kabel Listrik', user:'user5544', date:'03 Mei 2026',
      location:'Jl. Pintu Satu Senayan, Gelora, Tanah Abang, Jakarta Pusat', category:'Infrastruktur',
      status:'selesai', desc:'Tiang kabel berhasil diganti dan kabel dirapikan oleh petugas PLN.',
      img:'ASSETS/rusak3.jpg' },
    { id:'#LPR-008', title:'Kerusakan Jalan Retak', user:'user2211', date:'04 Mei 2026',
      location:'Jl. Gatot Subroto, Tebet, Jakarta Selatan', category:'Jalan',
      status:'proses', desc:'Aspal retak panjang sepanjang 50m di jalur tengah, berbahaya bagi pengendara sepeda motor.',
      img:'ASSETS/rusak 1.jpeg' },
    { id:'#LPR-009', title:'Kerusakan Drainase', user:'user6677', date:'12 Mei 2026',
      location:'Jl. Pahlawan, Menteng, Jakarta Pusat', category:'Drainase',
      status:'baru', desc:'Saluran drainase tersumbat sampah dan mulai meluap ke jalan.',
      img:'ASSETS/download.jpg' },
];

let currentAction = null;
let currentIndex  = null;

// ── Filter tabs ────────────────────────────────────────────────────
function filterCards(status, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const cards = document.querySelectorAll('#laporanGrid .laporan-card');
    let visible = 0;
    cards.forEach(card => {
        const match = status === 'semua' || card.dataset.status === status;
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('emptyState').style.display = visible === 0 ? 'block' : 'none';
}

// ── Search ─────────────────────────────────────────────────────────
function searchCards(q) {
    const keyword = q.toLowerCase();
    const cards = document.querySelectorAll('#laporanGrid .laporan-card');
    let visible = 0;
    cards.forEach(card => {
        const title = card.dataset.title.toLowerCase();
        const match = title.includes(keyword);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('emptyState').style.display = visible === 0 ? 'block' : 'none';
}

// ── Modal helpers ──────────────────────────────────────────────────
function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => {
        if (e.target === overlay) overlay.classList.remove('show');
    });
});

// ── Detail modal ───────────────────────────────────────────────────
const statusLabel = {
    baru:    '<span class="badge badge-baru">BARU</span>',
    proses:  '<span class="badge badge-proses">SEDANG DIPROSES</span>',
    selesai: '<span class="badge badge-selesai">SELESAI</span>',
    ditolak: '<span class="badge badge-ditolak">DITOLAK</span>',
};

function openDetail(idx) {
    const r = reports[idx];
    document.getElementById('detailTitle').textContent    = r.title;
    document.getElementById('detailId').textContent       = r.id;
    document.getElementById('detailUser').textContent     = r.user;
    document.getElementById('detailLocation').textContent = r.location;
    document.getElementById('detailDate').textContent     = r.date;
    document.getElementById('detailCategory').textContent = r.category;
    document.getElementById('detailDesc').textContent     = r.desc;
    document.getElementById('detailStatus').innerHTML     = statusLabel[r.status];
    document.getElementById('detailImg').src              = r.img;

    // Footer buttons berdasarkan status
    const footer = document.getElementById('detailFooter');
    if (r.status === 'baru') {
        footer.innerHTML = `
            <button class="btn-modal btn-modal-reject" onclick="closeModal('modalDetail'); openConfirm('reject',${idx})">
                <i class="fas fa-times"></i> Tolak
            </button>
            <button class="btn-modal btn-modal-approve" onclick="closeModal('modalDetail'); openConfirm('approve',${idx})">
                <i class="fas fa-check"></i> Setujui & Proses
            </button>`;
    } else if (r.status === 'proses') {
        footer.innerHTML = `
            <button class="btn-modal btn-modal-reject" onclick="closeModal('modalDetail'); openConfirm('reject',${idx})">
                <i class="fas fa-times"></i> Tolak
            </button>
            <button class="btn-modal btn-modal-bukti" onclick="closeModal('modalDetail'); openBukti(${idx})">
                <i class="fas fa-camera"></i> Upload Bukti Selesai
            </button>`;
    } else {
        footer.innerHTML = `
            <button class="btn-modal" style="background:#f0f0f0;color:#555;" onclick="closeModal('modalDetail')">
                Tutup
            </button>`;
    }
    openModal('modalDetail');
}

// ── Bukti selesai ─────────────────────────────────────────────────
function openBukti(idx) {
    currentIndex = idx;
    document.getElementById('buktiLapTitle').textContent = reports[idx].title + ' (' + reports[idx].id + ')';
    document.getElementById('tanggalSelesai').value = new Date().toISOString().split('T')[0];
    document.getElementById('filePreview').style.display = 'none';
    openModal('modalBukti');
}

function previewFile(input) {
    const file = input.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('filePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

function submitBukti() {
    closeModal('modalBukti');
    showToast('Bukti berhasil dikirim. Laporan ditandai Selesai! ✅', 'success');
}

// ── Konfirmasi approve / reject ────────────────────────────────────
function openConfirm(action, idx) {
    currentAction = action;
    currentIndex  = idx;
    const r = reports[idx];

    const isApprove = action === 'approve';
    document.getElementById('confirmIcon').className = 'confirm-icon ' + (isApprove ? 'approve' : 'reject');
    document.getElementById('confirmIconI').className = isApprove ? 'fas fa-check' : 'fas fa-times';
    document.getElementById('confirmHeading').textContent = isApprove ? 'Setujui Laporan?' : 'Tolak Laporan?';
    document.getElementById('confirmText').textContent    =
        isApprove
        ? `Laporan "${r.title}" (${r.id}) akan disetujui dan berstatus Sedang Diproses.`
        : `Laporan "${r.title}" (${r.id}) akan ditolak. Tindakan ini tidak dapat dibatalkan.`;

    const btn = document.getElementById('confirmBtn');
    btn.textContent  = isApprove ? '✔ Ya, Setujui' : '✖ Ya, Tolak';
    btn.className    = 'btn-modal ' + (isApprove ? 'btn-modal-approve' : 'btn-modal-reject');

    openModal('modalConfirm');
}

function executeAction() {
    closeModal('modalConfirm');
    const r = reports[currentIndex];
    if (currentAction === 'approve') {
        showToast(`Laporan ${r.id} disetujui dan masuk ke proses.`, 'success');
    } else {
        showToast(`Laporan ${r.id} telah ditolak.`, 'error');
    }
}

// ── Toast ──────────────────────────────────────────────────────────
function showToast(msg, type = 'info') {
    const toast   = document.getElementById('toast');
    const icon    = document.getElementById('toastIcon');
    const msgEl   = document.getElementById('toastMsg');

    toast.className = 'toast';
    if (type === 'success') { toast.classList.add('success'); icon.className = 'fas fa-check-circle'; }
    else if (type === 'error') { toast.classList.add('error'); icon.className = 'fas fa-times-circle'; }
    else { icon.className = 'fas fa-info-circle'; }

    msgEl.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}

// ── Set today date on load ─────────────────────────────────────────
document.getElementById('tanggalSelesai').value = new Date().toISOString().split('T')[0];
</script>

</body>
</html>
