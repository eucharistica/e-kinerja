<?php
// ===== SESSION — versi paling kompatibel (PHPSESSID default) =====
// Deteksi HTTPS (termasuk di balik Cloudflare/Zero Trust)
$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
    (isset($_SERVER['HTTP_CF_VISITOR']) && stripos($_SERVER['HTTP_CF_VISITOR'], '"scheme":"https"') !== false)
);

// Hanya cookie (tanpa SID di URL)
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');
// Amankan cookie
ini_set('session.cookie_httponly', '1');
if ($isHttps) {
    // kirim cookie hanya lewat HTTPS kalau memang HTTPS
    ini_set('session.cookie_secure', '1');
}
// SameSite (jika PHP mendukung; kalau tidak, diabaikan)
@ini_set('session.cookie_samesite', 'Lax');

// ===== Anti-cache untuk endpoint auth =====
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

require_once 'conf/conf.php';
require_once 'libs/aes-encrypt/function.php';

// Mulai sesi
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Ambil input
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$passEnc  = paramEncrypt($password);
$userEnc  = paramEncrypt($username);

// Auth
$spv = getOne("SELECT nama_instansi FROM setup 
               WHERE kode_skpd='$userEnc' AND password='$passEnc'");

$login = getOne("SELECT tm_user.id_level
                 FROM tm_pegawai
                 INNER JOIN tm_user ON tm_pegawai.id_user = tm_user.id_user
                 WHERE tm_pegawai.status='AKTIF' AND tm_pegawai.nip='$username' AND tm_user.password='$passEnc'");

$nip = getOne("SELECT nip FROM tm_pegawai 
               WHERE nip='$username' AND status='AKTIF'");
               
               
// ❌ LOGIN GAGAL
if ($login === '' && $spv === '') {
    session_regenerate_id(true);
    $_SESSION['login_error'] = 'Username atau Password salah!';
    header("Location: login");
    exit;
}

// ✅ LOGIN SUKSES
$_SESSION = []; // pastikan bersih
session_regenerate_id(true);

if ($login !== '') {
    $_SESSION['nip']      = $nip;
    $_SESSION['id_level'] = $login;
}

if ($spv !== '') {
    $_SESSION['superuser'] = $spv;
}

// Routing
if ($login !== '') {
    $status = getOne("SELECT status_pegawai FROM tm_pegawai WHERE nip='$nip'");
    $dest = ($status === 'PNS')
        ? 'module=dashboard&act=home'
        : 'module=dashboard&act=dashboard';

    header('Location: page-view?' . paramEncrypt($dest));
    exit;
}

if ($spv !== '') {
    header('Location: page-view?' . paramEncrypt('module=dashboard&act=home'));
    exit;
}

$_SESSION['login_error'] = 'Terjadi kesalahan sistem.';
header("Location: login");
exit;

?>