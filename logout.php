<?php
// ==== Logout robust untuk E-Kinerja (PHPSESSID default) ====

// Matikan cache supaya halaman ini tidak di-cache
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

// Mulai session bila belum aktif
if (session_status() !== PHP_SESSION_ACTIVE) {
    // Hardening ringan seperti di halaman lain
    @ini_set('session.use_only_cookies', '1');
    @ini_set('session.use_trans_sid', '0');
    @ini_set('session.cookie_httponly', '1');

    // Deteksi HTTPS (juga di balik Cloudflare)
    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
        (isset($_SERVER['HTTP_CF_VISITOR']) && stripos($_SERVER['HTTP_CF_VISITOR'], '"scheme":"https"') !== false)
    );
    if ($isHttps) @ini_set('session.cookie_secure', '1');
    @ini_set('session.cookie_samesite', 'Lax');

    @session_start();
}

// Hapus semua data sesi di server
$_SESSION = [];

// Hapus cookie PHPSESSID di browser (pakai parameter yang sama)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    // Sesuaikan domain: jika host adalah IP, jangan set domain
    $rawHost  = $_SERVER['HTTP_HOST'] ?? '';
    $hostOnly = preg_replace('/:\d+$/', '', $rawHost);
    $isIp     = $hostOnly && filter_var($hostOnly, FILTER_VALIDATE_IP);

    $cookieDomain = $isIp ? '' : $params['domain'];
    if ($cookieDomain === '' && !$isIp) {
        // fallback: pakai host tanpa port (aman untuk domain)
        $cookieDomain = $hostOnly;
    }

    // Kirim cookie sesi kedaluwarsa
    setcookie(
        session_name(),        // biasanya PHPSESSID
        '',
        time() - 42000,
        $params['path'] ?: '/',
        $cookieDomain ?: '',
        $params['secure'],
        $params['httponly']
    );
}

// Hancurkan sesi
@session_destroy();

// (Opsional) ganti ID sesi baru kosong agar tab “clean” jika user back()
@session_start();
@session_regenerate_id(true);
@session_write_close();

// ==== Redirect ke /login ====
// Note: .htaccess kamu sudah rewrite `login` -> index.php
$target = 'login';

// Coba kirim header redirect dulu
$headersSent = headers_sent();
if (!$headersSent) {
    header('Location: ' . $target, true, 302);
    exit;
}

// Fallback kalau header sudah terkirim (ada output di atas)
?>
<!doctype html>
<html>
<head>
  <meta http-equiv="refresh" content="0;url=<?php echo htmlspecialchars($target, ENT_QUOTES, 'UTF-8'); ?>">
  <meta charset="utf-8">
  <title>Keluar...</title>
  <noscript>
    <meta http-equiv="refresh" content="0;url=<?php echo htmlspecialchars($target, ENT_QUOTES, 'UTF-8'); ?>">
  </noscript>
</head>
<body>
<script>
  // Jika JavaScript aktif, paksa pindah
  location.replace('<?php echo addslashes($target); ?>');
  // Jika ingin ada notifikasi, buka komentar:
  // alert('Anda telah keluar dari aplikasi');
</script>
<p>Mengalihkan ke halaman login… Jika tidak otomatis, <a href="<?php echo htmlspecialchars($target, ENT_QUOTES, 'UTF-8'); ?>">klik di sini</a>.</p>
</body>
</html>
