<?php
/**
 * Admin/profile_modal_admin.php
 * Modal profil admin – TANPA fitur switch role
 */
require_once '../db.php';

$id_admin_modal = (int)($_SESSION['admin_id'] ?? 0);
$profile = ['nama_admin' => 'Admin', 'email' => ''];
if ($id_admin_modal > 0) {
    $ps = $conn->prepare("SELECT nama_admin, email FROM admins WHERE id_admin = ?");
    $ps->bind_param('i', $id_admin_modal); $ps->execute();
    $pr = $ps->get_result()->fetch_assoc(); $ps->close();
    if ($pr) $profile = $pr;
}
$pName  = htmlspecialchars($profile['nama_admin']);
$pEmail = htmlspecialchars($profile['email'] ?? '');
?>
<style>
.pDropdown{position:fixed;top:72px;right:20px;width:260px;background:#fff;border-radius:16px;
  box-shadow:0 10px 40px rgba(15,23,42,.18);border:1px solid #e2e8f0;z-index:9997;display:none;overflow:hidden;font-family:'Poppins',sans-serif;}
.pDropdown.open{display:block;animation:slideDown .25s ease;}
@keyframes slideDown{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}
.pDrop-head{background:linear-gradient(135deg,#7f1d1d,#b91c1c);padding:18px 18px 14px;display:flex;align-items:center;gap:12px;}
.pDrop-avatar{width:46px;height:46px;border-radius:50%;border:2.5px solid rgba(255,255,255,.5);object-fit:cover;}
.pDrop-name{color:#fff;font-size:14px;font-weight:700;line-height:1.3;}
.pDrop-role{color:rgba(255,255,255,.75);font-size:11.5px;}
.pDrop-body{padding:6px 0;}
.pDrop-item{display:flex;align-items:center;gap:12px;padding:11px 18px;color:#374151;font-size:14px;
  text-decoration:none;cursor:pointer;border:none;background:none;width:100%;font-family:'Poppins',sans-serif;transition:background .2s;}
.pDrop-item:hover{background:#f8fafc;}
.pDrop-item i{width:16px;color:#b91c1c;font-size:14px;}
.pDrop-item.danger{color:#e63946;}.pDrop-item.danger i{color:#e63946;}
.pDrop-divider{border:none;border-top:1px solid #f1f5f9;margin:4px 0;}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9998;display:none;align-items:center;justify-content:center;}
.modal-overlay.open{display:flex;animation:fadeIn .2s ease;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal-box{background:#fff;border-radius:20px;width:100%;max-width:480px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);animation:slideUp .3s ease;}
@keyframes slideUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
.modal-head{background:linear-gradient(135deg,#7f1d1d,#b91c1c);padding:22px 28px;display:flex;align-items:center;justify-content:space-between;}
.modal-head h3{color:#fff;font-size:17px;font-weight:700;display:flex;align-items:center;gap:10px;}
.modal-close{background:rgba(255,255,255,.15);border:none;color:#fff;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;transition:.2s;}
.modal-close:hover{background:rgba(255,255,255,.28);}
.modal-body{padding:28px;}
.modal-fg{margin-bottom:18px;}
.modal-fg label{display:block;font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;}
.modal-fg input{width:100%;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;font-family:'Poppins',sans-serif;transition:.2s;}
.modal-fg input:focus{outline:none;border-color:#b91c1c;box-shadow:0 0 0 3px rgba(185,28,28,.1);}
.modal-fg input[readonly]{background:#f8fafc;color:#888;}
.modal-actions{display:flex;gap:12px;padding:0 28px 26px;}
.modal-btn{flex:1;padding:13px;border-radius:10px;border:none;font-size:14px;font-weight:700;cursor:pointer;font-family:'Poppins',sans-serif;transition:.25s;display:flex;align-items:center;justify-content:center;gap:8px;}
.modal-btn.primary{background:linear-gradient(135deg,#7f1d1d,#b91c1c);color:#fff;box-shadow:0 4px 14px rgba(185,28,28,.35);}
.modal-btn.primary:hover{transform:translateY(-2px);}
.modal-btn.ghost{background:#f1f5f9;color:#555;border:1.5px solid #e2e8f0;}
.modal-btn.ghost:hover{background:#e2e8f0;}
.modal-btn.danger-btn{background:#fef2f2;color:#e63946;border:1.5px solid #fca5a5;}
.modal-btn.danger-btn:hover{background:#fee2e2;}
.modal-alert{padding:12px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;display:flex;gap:8px;align-items:center;}
.modal-alert.success{background:#f0fdf4;color:#166534;border:1px solid #86efac;}
.modal-alert.error{background:#fef2f2;color:#b91c1c;border:1px solid #fca5a5;}
.logout-box{text-align:center;padding:36px 28px;}
.logout-icon{width:64px;height:64px;background:linear-gradient(135deg,#e63946,#ff6b6b);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:26px;color:#fff;margin:0 auto 16px;}
</style>

<!-- ADMIN DROPDOWN -->
<div class="pDropdown" id="pDropdown">
  <div class="pDrop-head">
    <img src="../ASSETS/USER.png" class="pDrop-avatar" alt="Admin">
    <div>
      <div class="pDrop-name"><?= $pName ?></div>
      <div class="pDrop-role"><i class="fa-solid fa-user-shield" style="font-size:10px;"></i> Administrator</div>
    </div>
  </div>
  <div class="pDrop-body">
    <button class="pDrop-item" onclick="openAdmModal('modalProfil')"><i class="fa-solid fa-user-pen"></i> Edit Profil</button>
    <button class="pDrop-item" onclick="openAdmModal('modalPassword')"><i class="fa-solid fa-lock"></i> Ubah Password</button>
    <button class="pDrop-item" onclick="location.href='Kelola_Laporan.php'"><i class="fa-solid fa-list-check"></i> Kelola Laporan</button>
    <button class="pDrop-item" onclick="location.href='Beranda_Admin.php'"><i class="fa-solid fa-house"></i> Beranda Admin</button>
    <hr class="pDrop-divider">
    <button class="pDrop-item danger" onclick="openAdmModal('modalLogout')"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
  </div>
</div>

<!-- MODAL: Edit Profil Admin -->
<div class="modal-overlay" id="modalProfil">
  <div class="modal-box">
    <div class="modal-head"><h3><i class="fa-solid fa-user-pen"></i> Edit Profil Admin</h3><button class="modal-close" onclick="closeAdmModal('modalProfil')">✕</button></div>
    <div class="modal-body">
      <div id="alertProfil"></div>
      <div class="modal-fg"><label>Nama Admin</label><input type="text" id="profilNama" value="<?= $pName ?>" placeholder="Nama admin"/></div>
      <div class="modal-fg"><label>Email</label><input type="email" id="profilEmail" value="<?= $pEmail ?>" placeholder="email@domain.com"/></div>
    </div>
    <div class="modal-actions">
      <button class="modal-btn ghost" onclick="closeAdmModal('modalProfil')">Batal</button>
      <button class="modal-btn primary" id="btnSaveProfil" onclick="saveAdmProfil()"><i class="fa-solid fa-save"></i> Simpan</button>
    </div>
  </div>
</div>

<!-- MODAL: Ubah Password Admin -->
<div class="modal-overlay" id="modalPassword">
  <div class="modal-box">
    <div class="modal-head"><h3><i class="fa-solid fa-lock"></i> Ubah Password</h3><button class="modal-close" onclick="closeAdmModal('modalPassword')">✕</button></div>
    <div class="modal-body">
      <div id="alertPassword"></div>
      <div class="modal-fg"><label>Password Lama</label><input type="password" id="pwLama" placeholder="Password lama"/></div>
      <div class="modal-fg"><label>Password Baru</label><input type="password" id="pwBaru" placeholder="Minimal 6 karakter"/></div>
      <div class="modal-fg"><label>Konfirmasi</label><input type="password" id="pwKonfirmasi" placeholder="Ulangi password baru"/></div>
    </div>
    <div class="modal-actions">
      <button class="modal-btn ghost" onclick="closeAdmModal('modalPassword')">Batal</button>
      <button class="modal-btn primary" id="btnSavePw" onclick="saveAdmPassword()"><i class="fa-solid fa-key"></i> Simpan</button>
    </div>
  </div>
</div>

<!-- MODAL: Logout Admin -->
<div class="modal-overlay" id="modalLogout">
  <div class="modal-box">
    <div class="modal-head"><h3><i class="fa-solid fa-right-from-bracket"></i> Keluar</h3><button class="modal-close" onclick="closeAdmModal('modalLogout')">✕</button></div>
    <div class="logout-box">
      <div class="logout-icon"><i class="fa-solid fa-right-from-bracket"></i></div>
      <h3 style="color:#1e293b;font-size:17px;margin-bottom:8px;">Yakin ingin keluar?</h3>
      <p style="color:#64748b;font-size:13.5px;margin-bottom:24px;">Sesi admin Anda akan diakhiri.</p>
    </div>
    <div class="modal-actions" style="padding-top:0;">
      <button class="modal-btn ghost" onclick="closeAdmModal('modalLogout')">Batal</button>
      <a href="../Logout.php" class="modal-btn danger-btn" style="text-decoration:none;"><i class="fa-solid fa-right-from-bracket"></i> Ya, Keluar</a>
    </div>
  </div>
</div>

<script>
const navAdminUser = document.getElementById('navAdminUser');
const pDropdownEl  = document.getElementById('pDropdown');
if (navAdminUser && pDropdownEl) {
  navAdminUser.addEventListener('click', e => { e.stopPropagation(); pDropdownEl.classList.toggle('open'); });
  document.addEventListener('click', e => { if (!pDropdownEl.contains(e.target) && !navAdminUser.contains(e.target)) pDropdownEl.classList.remove('open'); });
}
function openAdmModal(id)  { document.getElementById(id).classList.add('open'); pDropdownEl.classList.remove('open'); }
function closeAdmModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(el => el.addEventListener('click', function(e){ if(e.target===this) this.classList.remove('open'); }));

function showAdmAlert(id, msg, type) {
  const el = document.getElementById(id);
  el.innerHTML = `<div class="modal-alert ${type}"><i class="fa-solid fa-${type==='success'?'check':'exclamation'}-circle"></i>${msg}</div>`;
  setTimeout(() => { if(el) el.innerHTML=''; }, 4000);
}
function setAdmLoading(btnId, loading, txt, icon) {
  const b = document.getElementById(btnId); if(!b) return;
  b.disabled = loading;
  b.innerHTML = loading ? `<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...` : `<i class="fa-solid fa-${icon}"></i> ${txt}`;
}
function saveAdmProfil() {
  const nama = document.getElementById('profilNama').value.trim();
  const email = document.getElementById('profilEmail').value.trim();
  if (!nama) { showAdmAlert('alertProfil','Nama tidak boleh kosong.','error'); return; }
  setAdmLoading('btnSaveProfil', true);
  fetch('update_profile_admin.php', {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`action=update_profile&nama_admin=${encodeURIComponent(nama)}&email=${encodeURIComponent(email)}`
  }).then(r=>r.json()).then(d=>{
    setAdmLoading('btnSaveProfil',false,'Simpan','save');
    if(d.success) showAdmAlert('alertProfil','Profil berhasil diperbarui!','success');
    else showAdmAlert('alertProfil', d.error||'Gagal.','error');
  }).catch(()=>{ setAdmLoading('btnSaveProfil',false,'Simpan','save'); showAdmAlert('alertProfil','Kesalahan jaringan.','error'); });
}
function saveAdmPassword() {
  const lama = document.getElementById('pwLama').value;
  const baru = document.getElementById('pwBaru').value;
  const konfirm = document.getElementById('pwKonfirmasi').value;
  if (baru !== konfirm) { showAdmAlert('alertPassword','Password baru tidak cocok.','error'); return; }
  if (baru.length < 6) { showAdmAlert('alertPassword','Password minimal 6 karakter.','error'); return; }
  setAdmLoading('btnSavePw', true);
  fetch('update_profile_admin.php', {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`action=update_password&password_lama=${encodeURIComponent(lama)}&password_baru=${encodeURIComponent(baru)}`
  }).then(r=>r.json()).then(d=>{
    setAdmLoading('btnSavePw',false,'Simpan','key');
    if(d.success) showAdmAlert('alertPassword','Password berhasil diubah!','success');
    else showAdmAlert('alertPassword', d.error||'Gagal.','error');
  }).catch(()=>{ setAdmLoading('btnSavePw',false,'Simpan','key'); showAdmAlert('alertPassword','Kesalahan jaringan.','error'); });
}
</script>
