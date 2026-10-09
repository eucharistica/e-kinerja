<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

require_once 'conf/conf.php';
require_once 'libs/aes-encrypt/function.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    @session_start();
}

$nip      = $_SESSION['nip'] ?? null;
$id_level = $_SESSION['id_level'] ?? null;

if (!empty($nip) && !empty($id_level)) {
    $cek = getOne("SELECT status_pegawai FROM tm_pegawai WHERE nip='$nip'");
    $dest = ($cek === 'PNS')
        ? 'module=dashboard&act=home'
        : 'module=dashboard&act=dashboard';

    header('Location: page-view?' . paramEncrypt($dest));
    exit;
}

$errorMsg = '';
if (!empty($_SESSION['login_error'])) {
    $errorMsg = htmlspecialchars($_SESSION['login_error'], ENT_QUOTES, 'UTF-8');
    unset($_SESSION['login_error']);
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login | E-Kinerja</title>
    <link rel="shortcut icon" href="img/icon.png" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="libs/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="libs/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="libs/Ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="libs/dist/css/AdminLTE.css">
    <link rel="stylesheet" href="libs/dist/css/js-custom.css">
    <link rel="stylesheet" href="libs/iCheck/square/blue.css">
    <link rel="stylesheet" href="libs/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="libs/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="assets/icons/icon-96x96.png">
    <meta name="apple-mobile-web-app-status-bar" content="#FFFFFF">
    <meta name="keywords" content="HTML, CSS, JavaScript">
    <meta name="author" content="RSUD MATRAMAN">
    <meta name="description" content="E-Kinerja merupakan aplikasi penilaian kinerja untuk pegawai RSUD Matraman">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <style>
        .preloader{position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;background:#fff}
        .preloader .loading{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);font:14px arial}
        .alert-login{max-width:420px;margin:12px auto 0}
    </style>
</head>
<body>

<div id="js-sign-container">
  <div class="js-sign-bg js-gradient-1 js-transition">
    <div class="js-sign-bg-img js-transition js-sikm-float-animation" style="background:url(img/Monas-Vector.jpg)"></div>
    <a href="https://www.vecteezy.com/free-vector/jakarta" hidden>Jakarta Vectors by Vecteezy</a>
  </div>

  <div class="preloader">
    <div class="loading">
      <center><img src="img/loading.gif" alt="loading"></center>
    </div>
  </div>

  <div class="js-sign-form-container js-transition" style="background:linear-gradient(to bottom,#A7C7E7 0%,#FFFFFF 70%);">
    <div class="js-sign-form">
      <center>
        <img src="img/logo.png" width="180" height="130" alt="logo">
        <h2 class="title" style="font-family:Calibri;color:#214D73;text-shadow:2px 2px 4px #BBB;font-size:30px"><b>E-KINERJA<p></p> RSUD MATRAMAN</b></h2>
      </center>
      <br>

      <?php if ($errorMsg): ?>
        <div class="alert alert-danger alert-login" role="alert">
          <i class="fa fa-exclamation-circle"></i> <?php echo $errorMsg; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="cek-login" class="margin-bottom-20">
        <div class="js-sign-input-wrapper js-shadow-1 margin-bottom-20">
          <div>
            <input id="username" type="text" class="js-sign-input form-control" name="username" placeholder="Masukkan NIP/NIPK" required autofocus>
          </div>
          <div class="js-sign-form-divider"></div>
          <div class="js-relative js-password">
            <input id="password" type="password" class="js-sign-input js-sign-input-password form-control" name="password" placeholder="Masukkan password" required>
            <img src="img/icon-eye-hide.png" class="js-sign-hide-password js-shadow-1" alt="hide password">
            <img src="img/icon-eye.png" class="js-sign-show-password js-shadow-1" alt="show password">
          </div>
            <div id="login-error" class="alert alert-danger alert-login" style="display:none">
              <i class="fa fa-exclamation-circle"></i>
              <span id="login-error-text"></span>
            </div>

        </div>
        <br>
        <div class="form-group">
          <button type="submit" class="js-sign-btn js-gradient-1 js-shadow-1 js-transition btn form-control">Masuk</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- JS  -->
<script src="libs/jquery/jquery.min.js"></script>
<script src="libs/jquery/js-custom-login.js.download"></script>
<script src="libs/bootstrap/js/bootstrap.min.js"></script>
<script src="libs/iCheck/icheck.min.js"></script>
<script>
  $(function(){
    $('input').iCheck({checkboxClass:'icheckbox_square-blue',radioClass:'iradio_square-blue',increaseArea:'20%'});
    $(".preloader").fadeOut();
  });
  document.oncontextmenu=function(){return false;};
</script>
<script src="main.js"></script>
<script>
(function () {
    const params = new URLSearchParams(window.location.search);
    const error  = params.get('login_error');

    if (error) {
        let message = 'Login gagal. Username atau password salah.';

        if (error === 'invalid') {
            message = 'Username atau password tidak valid!';
        }

        const box = document.getElementById('login-error');
        const txt = document.getElementById('login-error-text');

        if (box && txt) {
            txt.textContent = message;
            box.style.display = 'block';

            // scroll ke form (UX)
            box.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // HAPUS query string biar ga loop
        window.history.replaceState({}, document.title, window.location.pathname);
    }
})();
</script>
</body>
</html>
