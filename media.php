<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Expires: 0");
error_reporting(0);
date_default_timezone_set('Asia/Jakarta');

require_once('conf/conf.php');
require_once('libs/aes-encrypt/function.php');

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// --- sesi konsisten & aman (tidak mengubah nama, tetap PHPSESSID) ---
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');
ini_set('session.cookie_httponly', '1');
if (
  (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
  (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) ||
  (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
  (isset($_SERVER['HTTP_CF_VISITOR']) && stripos($_SERVER['HTTP_CF_VISITOR'], '"scheme":"https"') !== false)
) {
  ini_set('session.cookie_secure', '1');
}
@ini_set('session.cookie_samesite', 'Lax');
session_start();

/* ====== Auth ====== */
$isLoggedIn = isset($_SESSION['superuser']) || (isset($_SESSION['nip']) && isset($_SESSION['id_level']));
if (!$isLoggedIn) {
  header('location:login');
  exit;
}

/* ====== Setup waktu & periode ====== */
$day = date('d');
$setup = mysqli_fetch_array(bukaquery("SELECT setup.tutup_kinerja,setup.validasi_pj,setup.validasi_kasie,setup.validasi_jadwal FROM setup"));

$tgl = TanggalAkhirBulanKemarin();
$bln_sebelumnya = FormatTgl('m', $tgl);
$thn = FormatTgl('Y', $tgl);
$thn_now = date('Y');
$bln_now = date('m');
$dateformat = '%d-%m-%Y';
$awalbulandepan = getOne("SELECT DATE_FORMAT(DATE_SUB(LAST_DAY(DATE_ADD(NOW(), INTERVAL 1 MONTH)), INTERVAL DAY(LAST_DAY(DATE_ADD(NOW(), INTERVAL 1 MONTH)))-1 DAY),'$dateformat') AS firstOfNextMonth");
$akhirbulandepan = getOne("SELECT DATE_FORMAT(LAST_DAY(DATE_ADD(NOW(), INTERVAL 1 MONTH)),'$dateformat') AS lastOfNextMonth");

/* ====== Session vars ====== */
$spv = $_SESSION['superuser'] ?? null;
$nip = $_SESSION['nip'] ?? null;
$idlevel = $_SESSION['id_level'] ?? null;

$status_pegawai = getOne("SELECT status_pegawai FROM tm_pegawai WHERE nip='$nip'");
$id_user = getOne("SELECT tm_pegawai.id_user FROM tm_pegawai WHERE nip='$nip'");
$nama_pegawai = getOne("SELECT tm_pegawai.nama_pegawai FROM tm_pegawai WHERE nip='$nip'");
$agama_pegawai = getOne("SELECT tm_pegawai.agama FROM tm_pegawai WHERE nip='$nip'");
$id_unit = getOne("SELECT tm_pegawai.id_unit FROM tm_pegawai WHERE nip='$nip'");
$kasie = getOne("SELECT tm_pegawai.sub_bagian FROM tm_pegawai WHERE nip='$nip'");
$kasatpel_pegawai = getOne("SELECT tm_pegawai.id_kasatpel FROM tm_pegawai WHERE nip='$nip'");
$kasatpel = getOne("SELECT tm_kasatpel.id_kasatpel FROM tm_kasatpel WHERE id_kasatpel='$kasatpel_pegawai'");
$logfinger = getOne("SELECT tm_pegawai.log_finger FROM tm_pegawai WHERE nip='$nip'");

/* ====== Hak akses ====== */
$superuser = getOne("SELECT setup.kode_skpd FROM setup WHERE nama_instansi='$spv'");
$hak_akses = mysqli_fetch_array(bukaquery("SELECT tm_level.menu_kepegawaian, tm_level.menu_diklat, tm_level.menu_keuangan, tm_level.menu_val_pj, tm_level.menu_val_kasatpel, tm_level.menu_absensi, tm_level.menu_val_kasie, tm_level.menu_laporan, tm_level.menu_surtug, tm_level.upload_perpustakaan, tm_level.menu_helpdesk, tm_level.configurasi, tm_level.menu_k3 FROM tm_level WHERE id_level='$idlevel'"));

/* ====== Notifikasi ====== */
$surat_tugas = getOne("SELECT COUNT(tm_surat_tugas.id_surat) FROM tm_surat_tugas INNER JOIN tm_add_surtug ON tm_surat_tugas.id_surat=tm_add_surtug.id_surat WHERE tm_add_surtug.nip='$nip' AND tm_add_surtug.`read`='0'");

$cek_pengajuan_cuti1 = getOne("SELECT COUNT(tm_cuti.id_cuti) FROM tm_cuti WHERE tm_cuti.nip_pengganti='$nip' AND tm_cuti.acc_pengganti='-'");

if ($hak_akses['menu_val_pj'] == '1') {
  $cek_pengajuan_cuti2 = getOne("SELECT COUNT(tm_cuti.id_cuti) FROM tm_cuti INNER JOIN tm_pegawai ON tm_cuti.id_user=tm_pegawai.id_user WHERE tm_cuti.nip_pj='$nip' AND tm_cuti.acc_pengganti='Y' AND tm_cuti.acc_pj='-'");
} elseif ($hak_akses['menu_val_kasatpel'] == '1') {
  $cek_pengajuan_cuti2 = getOne("SELECT COUNT(tm_cuti.id_cuti) FROM tm_cuti INNER JOIN tm_pegawai ON tm_cuti.id_user=tm_pegawai.id_user WHERE tm_pegawai.id_kasatpel='$kasatpel' AND tm_cuti.acc_pengganti='Y' AND tm_cuti.acc_pj='-'");
} else {
  $cek_pengajuan_cuti2 = 0;
}

$cek_pengajuan_cuti3 = ($hak_akses['menu_kepegawaian'] == '1')
  ? getOne("SELECT COUNT(tm_cuti.id_cuti) FROM tm_cuti WHERE tm_cuti.acc_kepegawaian='-' AND tm_cuti.acc_pj='Y'")
  : 0;

$cek_pengajuan_cuti4 = ($hak_akses['menu_val_kasie'] == '1')
  ? getOne("SELECT COUNT(tm_cuti.id_cuti) FROM tm_cuti INNER JOIN tm_pegawai ON tm_cuti.id_user=tm_pegawai.id_user WHERE tm_pegawai.sub_bagian='$kasie' AND tm_cuti.nip_kasie='$nip' AND tm_cuti.acc_kepegawaian='Y' AND tm_cuti.acc_kasie='-'")
  : 0;

$pengajuan_cuti = $cek_pengajuan_cuti1 + $cek_pengajuan_cuti2 + $cek_pengajuan_cuti3 + $cek_pengajuan_cuti4;

/* ====== Notif suara ====== */
if ($surat_tugas > 0)
  echo "<embed name='sound_file' src='sound/surat_masuk.mp3' loop='true' hidden='true' autostart='true'/>";
if ($pengajuan_cuti > 0)
  echo "<embed name='sound_file' src='sound/surat_masuk.mp3' loop='true' hidden='true' autostart='true'/>";

/* ====== Foto ====== */
$foto1 = mysqli_fetch_array(bukaquery("SELECT tm_pegawai.jk, tm_pegawai.foto FROM tm_pegawai WHERE tm_pegawai.nip='$nip'"));
if ($foto1['foto'] == '-' || $foto1['foto'] == '') {
  $foto = ($foto1['jk'] == 'L') ? 'img/laki.png' : 'img/perempuan.png';
} else {
  $foto = "img/" . $foto1['foto'];
}

/* ====== URI decode ====== */
$URI = $_SERVER['REQUEST_URI'] ?? null;
$url = decode($URI);
$alert = $url['alert'] ?? null;
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="Cache-control" content="no-store">
  <title>SIMPEG | RSUD Matraman</title>
  <link rel="icon" type="image/x-icon" href="img/icon.png">
  <link rel="apple-touch-icon" type="image/x-icon" href="img/icon.png">
  <link rel="shortcut icon" type="image/png" href="img/icon.png" />
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="libs/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="libs/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="libs/Ionicons/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="libs/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="libs/datatables.net-bs/Buttons-1.5.1/css/buttons.dataTables.min.css">
  <!-- bootstrap slider -->
  <link rel="stylesheet" href="libs/bootstrap-slider/slider.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="libs/bootstrap-daterangepicker/daterangepicker.css">
  <!-- clock picker -->
  <link rel="stylesheet" type="text/css" href="libs/clockpicker/bootstrap-clockpicker.min.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="libs/iCheck/all.css">
  <!-- fullCalendar -->
  <!-- <link rel="stylesheet" href="libs/fullcalendar/fc.css"> -->
  <link rel="stylesheet" href="libs/fullcalendar/dist/fullcalendar.min.css">
  <link rel="stylesheet" href="libs/fullcalendar/dist/fullcalendar.print.min.css" media="print">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="libs/dist/css/skins/_all-skins.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="libs/select2/css/select2.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="libs/dist/css/AdminLTE.min.css">
  <style>
    /* Biar isi sidebar (menu) bisa scroll sendiri */
    .main-sidebar .sidebar {
      height: calc(100vh - 50px);
      /* 50px kira-kira tinggi header navbar AdminLTE */
      overflow-y: auto;
      overflow-x: hidden;
    }
  </style>

  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="libs/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  <!-- Google Font -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .example-modal .modal {
      position: relative;
      display: block;
      z-index: 1;
      background: transparent !important;
    }

    .preloader {
      position: fixed;
      inset: 0;
      z-index: 9999;
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      /* Tambahkan ini untuk fade-out yang smooth */
      opacity: 1;
      transition: opacity 0.4s ease;
    }
    
    .preloader.fade-out {
      opacity: 0;
      pointer-events: none;
    }

    .preloader .loading {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      font: 14px arial;
    }

    .upper {
      text-transform: uppercase;
    }

    .lower {
      text-transform: lowercase;
    }

    .cap {
      text-transform: capitalize;
    }

    .small {
      font-variant: small-caps;
    }

    .arial14 {
      font-family: arial;
      font-size: 14px;
    }

    .arial12 {
      font-family: arial;
      font-size: 12px;
    }

    /* Kolom "Nama Pegawai" agar ikut panjang nama */
    #jadwal th.col-nama,
    #jadwal td.col-nama {
      white-space: nowrap;
    }

    /* Compact DataTables */
    table.dataTable tbody th,
    table.dataTable tbody td {
      padding: 4px 6px;
    }

    table.dataTable thead th {
      padding: 6px 6px;
    }

    .dt-nowrap {
      white-space: nowrap;
    }
  </style>
</head>

<body class="hold-transition skin-black sidebar-mini fixed">
   <!--Page loader -->
    <div class="preloader">
      <img src="img/loading.gif" width="800" alt="loading...">
    </div>

  <script>
    // optional: updater jam (kalau ada elemen #date-time di layout)
    const zeroFill = n => ('0' + n).slice(-2);
    setInterval(() => {
      const now = new Date();
      const dateTime = zeroFill(now.getMonth() + 1) + '/' + zeroFill(now.getDate()) + '/' +
        now.getFullYear() + ' ' + zeroFill(now.getHours()) + ':' +
        zeroFill(now.getMinutes()) + ':' + zeroFill(now.getSeconds());
      var el = document.getElementById('date-time');
      if (el) el.innerHTML = dateTime;
    }, 1000);
  </script>

  <div class="wrapper">
    <header class="main-header">
      <a href="?<?php echo paramEncrypt('module=dashboard&act=dashboard'); ?>" class="logo">
        <span class="logo-mini"><b style="color:#359D9e">SIM</b></span>
        <span class="logo-lg"><b style="color:#359D9e">SIMPEG</b></span>
      </a>

      <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button"><span class="sr-only">Toggle
            navigation</span></a>

        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <!-- Surat tugas -->
            <li class="dropdown messages-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-envelope-o"></i>
                <span class="label label-success"><?php echo $surat_tugas; ?></span>
              </a>
              <ul class="dropdown-menu">
                <li class="header"><?php echo $surat_tugas; ?> Surat Tugas Belum Anda Baca</li>
                <li>
                  <ul class="menu">
                    <?php
                    $tampil_surat = bukaquery("
                      SELECT tm_surat_tugas.id_surat, tm_surat_tugas.no_surat, tm_surat_tugas.tgl_surat, tm_surat_tugas.kegiatan, tm_add_surtug.`read`
                      FROM tm_surat_tugas
                      INNER JOIN tm_add_surtug ON tm_surat_tugas.id_surat=tm_add_surtug.id_surat
                      WHERE tm_add_surtug.nip='$nip'
                      ORDER BY tm_surat_tugas.id_surat DESC
                    ");
                    while ($row = fetch_array($tampil_surat)) {
                      $isNew = ($row['read'] == '0');
                      $icon = $isNew ? 'img/surat_in.jpg' : 'img/surat_out.jpg';
                      $status_baca = $isNew ? '<b style=color:red>New</b>' : '<b style=color:green>Read</b>';
                      ?>
                      <li>
                        <a href="<?php echo 'cetak-surat-tugas-' . $row['id_surat']; ?>" target="_blank">
                          <div class="pull-left"><img src="<?php echo $icon; ?>" width="50" height="50" class="img-circle"
                              alt="Surat"></div>
                          <h4>
                            <?php echo $row['no_surat']; ?>
                            <small><i class="fa fa-clock-o"></i>
                              <?php echo "dibuat : " . FormatTgl('d M Y', $row['tgl_surat']) . " / $status_baca"; ?></small>
                          </h4>
                          <p><?php echo $row['kegiatan']; ?></p>
                        </a>
                      </li>
                    <?php } ?>
                  </ul>
                </li>
                <li class="footer"><a href="#">See All Messages</a></li>
              </ul>
            </li>

            <!-- Notifikasi cuti -->
            <li class="dropdown notifications-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-bell-o"></i>
                <span class="label label-warning"><?php echo $pengajuan_cuti; ?></span>
              </a>
              <ul class="dropdown-menu">
                <li class="header">Ada <?php echo $pengajuan_cuti; ?> permintaan ACC Cuti</li>
                <li>
                  <ul class="menu">
                    <li>
                      <a
                        href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-assesment-pengganti-cuti-pegawai'); ?>">
                        <i class="fa fa-exchange text-aqua"></i> <?php echo $cek_pengajuan_cuti1; ?> Menunggu Assesment
                        Pengganti
                      </a>
                    </li>
                    <?php if ($hak_akses['menu_val_pj'] == '1' && $hak_akses['menu_val_kasatpel'] == '0') { ?>
                      <li>
                        <a
                          href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-assesment-pj-cuti-pegawai'); ?>">
                          <i class="fa fa-exchange text-aqua"></i> <?php echo $cek_pengajuan_cuti2; ?> Menunggu Assesment
                          PJ
                        </a>
                      </li>
                    <?php } elseif ($hak_akses['menu_val_kasatpel'] == '1' && $hak_akses['menu_val_pj'] == '0') { ?>
                      <li>
                        <a
                          href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-assesment-satpel-cuti-pegawai'); ?>">
                          <i class="fa fa-exchange text-aqua"></i> <?php echo $cek_pengajuan_cuti2; ?> Menunggu Assesment
                          Kasatpel
                        </a>
                      </li>
                    <?php } ?>
                    <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                      <li>
                        <a
                          href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-assesment-kepegawaian-cuti-pegawai'); ?>">
                          <i class="fa fa-exchange text-aqua"></i> <?php echo $cek_pengajuan_cuti3; ?> Menunggu Assesment
                          Kepegawaian
                        </a>
                      </li>
                    <?php } ?>
                    <?php if ($hak_akses['menu_val_kasie'] == '1') { ?>
                      <li>
                        <a
                          href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-assesment-kasie-cuti-pegawai'); ?>">
                          <i class="fa fa-exchange text-aqua"></i> <?php echo $cek_pengajuan_cuti4; ?> Menunggu Assesment
                          Kasie
                        </a>
                      </li>
                    <?php } ?>
                  </ul>
                </li>
                <li class="footer"><a href="#">View all</a></li>
              </ul>
            </li>

            <!-- User -->
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="<?php echo $foto; ?>" class="user-image" alt="User">
                <span class="hidden-xs"><?php echo paramDecrypt($superuser) . $nama_pegawai; ?></span>
              </a>
              <ul class="dropdown-menu">
                <li class="user-header">
                  <img src="<?php echo $foto; ?>" class="img-circle" alt="User Image">
                  <p><?php echo $nama_pegawai . paramDecrypt($superuser); ?><small><?php echo $nip . $spv; ?></small></p>
                </li>
                <li class="user-footer">
                  <div class="pull-left">
                    <?php if ($superuser == '') { ?>
                      <a href="?<?php echo paramEncrypt('module=simpeg&act=profile-update'); ?>"
                        class="btn btn-default btn-flat">Profile</a>
                    <?php } else { ?>
                      <a href="?<?php echo paramEncrypt('module=simpeg&act=ganti-password'); ?>"
                        class="btn btn-default btn-flat">Ganti Password</a>
                    <?php } ?>
                  </div>
                  <div class="pull-right"><a href="logout" class="btn btn-default btn-flat">Sign out</a></div>
                </li>
              </ul>
            </li>

          </ul>
        </div>
      </nav>
    </header>

    <aside class="main-sidebar">
      <section class="sidebar">
        <div class="user-panel">
          <div class="pull-left image">
            <img src="<?php echo $foto; ?>" class="user-image-circle" alt="User">
          </div>
          <div class="pull-left info">
            <p><?php echo $nama_pegawai; ?></p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          </div>
        </div>
        <?php require_once('menu.php'); ?>
      </section>
    </aside>

    <div class="content-wrapper">
      <!--<section class="content-header">-->
      <!--  <h1>-->
      <!--    <marquee scrolldelay="100"><?php echo getOne("SELECT setup.running_text FROM setup"); ?></marquee>-->
      <!--  </h1>-->
      <!--</section>-->

      <section class="content">
        <div class="row">
          <section class="col-lg-12 connectedSortable">
            <?php require_once('page.php'); ?>
          </section>
        </div>
      </section>
    </div>

    <footer class="main-footer">
      <div class="pull-right hidden-xs"><b>Version</b> 2.0 | Crafted with <i class="fa fa-heart" style="color:pink"></i>
      </div>
      <strong>© IT <?php echo getOne("SELECT setup.nama_instansi FROM setup"); ?></strong>
    </footer>

    <div class="control-sidebar-bg"></div>
  </div>
  <!-- ./wrapper -->

  <!-- ========== JS CORE (urut & tanpa duplikasi) ========== -->
  <!-- jQuery -->
  <!--<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>-->
  <script src="libs/jquery/jquery.min.js"></script>
  <!-- jQuery UI -->
  <script src="libs/jquery-ui/jquery-ui.min.js"></script>
  <!-- Bootstrap -->
  <script src="libs/bootstrap/js/bootstrap.min.js"></script>
  <!-- Select2 -->
  <script src="libs/select2/js/select2.full.min.js"></script>
  <!-- InputMask -->
  <script src="libs/input-mask/jquery.inputmask.js"></script>
  <script src="libs/input-mask/jquery.inputmask.date.extensions.js"></script>
  <script src="libs/input-mask/jquery.inputmask.extensions.js"></script>
  <!-- Moment + Daterange -->
  <script src="libs/moment/min/moment.min.js"></script>
  <script src="libs/bootstrap-daterangepicker/daterangepicker.js"></script>
  <!-- Datepicker -->
  <script src="libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
  <!-- Clockpicker -->
  <script src="libs/clockpicker/bootstrap-clockpicker.min.js"></script>
  <!-- iCheck -->
  <script src="libs/iCheck/icheck.min.js"></script>
  <!-- FastClick -->
  <script src="libs/fastclick/lib/fastclick.js"></script>
  <!--AdminLTE -->
  <!--<script src="libs/dist/js/adminlte.min.js"></script>-->
  <script src="libs/dist/js/adminlte.js"></script>
  <!-- FullCalendar (JS) -->
  <script src="libs/fullcalendar/dist/fullcalendar.min.js"></script>
  <!-- DataTables -->
  <script src="libs/datatables.net-bs/js/jquery.dataTables.min.js"></script>
  <script src="libs/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

  <script type="text/javascript" src="libs/datatables.net-bs/JSZip-2.5.0/jszip.min.js"></script>
  <script type="text/javascript" src="libs/datatables.net-bs/pdfmake-0.1.32/pdfmake.min.js"></script>
  <script type="text/javascript" src="libs/datatables.net-bs/pdfmake-0.1.32/vfs_fonts.js"></script>
  <script type="text/javascript" src="libs/datatables.net-bs/AutoFill-2.2.2/js/dataTables.autoFill.min.js"></script>
  <script type="text/javascript" src="libs/datatables.net-bs/Buttons-1.5.1/js/dataTables.buttons.min.js"></script>
  <script type="text/javascript" src="libs/datatables.net-bs/Buttons-1.5.1/js/buttons.flash.min.js"></script>
  <script type="text/javascript" src="libs/datatables.net-bs/Buttons-1.5.1/js/buttons.html5.min.js"></script>
  <script type="text/javascript" src="libs/datatables.net-bs/Buttons-1.5.1/js/buttons.print.min.js"></script>

  <!-- WYSIHTML5 -->
  <script src="libs/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
  <script>
    function rndInt(min, max) { return Math.floor(Math.random() * (max - min + 1)) + min; }
    function clamp1to10(v) {
      v = parseInt(v, 10);
      if (isNaN(v)) return '';
      return Math.max(1, Math.min(10, v));
    }
    
    // ============================================================
    // PRELOADER — satu tempat, tidak duplikat
    // ============================================================
    (function () {
      var $preloader = $(".preloader");
    
      // 1. Sembunyikan preloader saat halaman selesai FULLY loaded
      //    (window.load menunggu semua gambar, font, dll — bukan cuma DOM)
      $(window).on("load", function () {
        $preloader.fadeOut(400, function () {
          $(this).remove(); // Bersihkan dari DOM setelah fade selesai
        });
      });
    
      // 2. Fallback: paksa hilang setelah 5 detik (buat jaga-jaga)
      setTimeout(function () {
        $preloader.fadeOut(400);
      }, 5000);
    
      // 3. Tampilkan preloader saat user klik link navigasi
      //    (kecuali link yang buka tab baru, anchor, atau link kosong)
      $(document).on("click", "a[href]", function () {
        var href = $(this).attr("href") || "";
        var target = $(this).attr("target") || "";
    
        // Skip: anchor, javascript void, tab baru, atau link kosong
        if (
          href === "#" ||
          href === "" ||
          href.startsWith("javascript") ||
          target === "_blank"
        ) return;
    
        // Tampilkan preloader
        $preloader.stop(true, true).css("opacity", 1).show();
      });
    })();
    
    // daftar field yang diisi preset
    var FIELDS = ['#d_diri', '#d_penampilan', '#d_seragam', '#d_alat', '#d_ruangan', '#d_sarana'];

    function setPreset(minV, maxV) {
      FIELDS.forEach(function (sel) {
        jQuery(sel).val(rndInt(minV, maxV));
      });
    }

    jQuery(function ($) {
      $('#btnPresetBagus').on('click', function () { setPreset(9, 10); });
      $('#btnPresetSedang').on('click', function () { setPreset(7, 8); });
      $('#btnPresetPerlu').on('click', function () { setPreset(5, 6); });
      $('#btnClear').on('click', function () { FIELDS.forEach(function (s) { $(s).val(''); }); });

      // safety clamp 1–10 saat blur/submit
      FIELDS.forEach(function (sel) {
        $(document).on('blur change', sel, function () { $(this).val(clamp1to10($(this).val())); });
      });

      $('#formKedisiplinanBaru').on('submit', function () {
        for (var i = 0; i < FIELDS.length; i++) {
          var $el = $(FIELDS[i]);
          var v = clamp1to10($el.val());
          if (v === '') {
            alert('Semua nilai wajib diisi 1–10.');
            $el.focus();
            return false;
          }
          $el.val(v);
        }
      });
    });

    // Kumpulkan semua input nilai (class "nilai-komp")
    var $ALL = jQuery('.nilai-komp');

    // Preset helpers
    function fillAllConst(val) {
      $ALL.each(function () { jQuery(this).val(val); });
    }
    function fillAllRange(minV, maxV) {
      $ALL.each(function () { jQuery(this).val(rndInt(minV, maxV)); });
    }

    jQuery(function ($) {
      // Tombol preset
      $('#btnPresetExceeds').on('click', function () { fillAllConst(10); });
      $('#btnPresetMeets').on('click', function () { fillAllRange(7, 9); });
      $('#btnPresetDevelop').on('click', function () { fillAllRange(4, 6); });
      $('#btnClearKompetensi').on('click', function () { $ALL.val(''); });

      // Safety clamp 1–10 saat blur/submit
      $ALL.on('blur change', function () { $(this).val(clamp1to10($(this).val())); });

      $('#formKompetensiBaru').on('submit', function () {
        var ok = true, first = null;
        $ALL.each(function () {
          var $el = $(this);
          var v = clamp1to10($el.val());
          if (v === '') { ok = false; first = first || $el; return false; }
          $el.val(v);
        });
        if (!ok) { alert('Semua indikator wajib diisi dengan nilai 1–10.'); first.focus(); return false; }
      });
    });
    
    $(function () {
      // Inputmask basic
      $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
      $('#datemask2').inputmask('mm/yyyy', { 'placeholder': 'mm/yyyy' });
      $('[data-mask]').inputmask();
      $('[data-mask2]').inputmask();
      // Daterange shortcuts
      $('#reservation,#reservation1,#reservation2,#reservation3,#reservation4,#reservation5,#reservation6,#reservation7,#reservation8,#reservation9,#reservation10').daterangepicker();
      $('#reservationtime').daterangepicker({ timePicker: true, timePickerIncrement: 30, locale: { format: 'MM/DD/YYYY h:mm A' } });
      $('#daterange-btn').daterangepicker({
        ranges: {
          // 'Today': [moment(), moment()],
          // 'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          // 'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          '90 Hari': [moment().add(1, 'days'), moment().add(91, 'days')],
          // 'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
          // 'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().add(1, 'days'),
        endDate: moment().add(3, 'month')
      },
        function (start, end) {
          $('#daterange-btn span').html(start.format('MM/DD/YYYY') + ',' + end.format('MM/DD/YYYY'))
        }
      );


      // Datepicker basic
      $('#today').datepicker({ autoclose: true, startDate: "2" });
      $('#multidatepicker').datepicker({ multidate: true, weekNumber: true, startDate: "-14d", endDate: "+60d", clearBtn: true });
      $('#datepicker,#datepicker1,#datepicker2,#datepicker3,#datepicker4,#datepicker5,#datepicker6,#datepicker7,#datepicker8,#datepicker9,#datepicker10,#datepicker12,#datepicker13,#tanggal_lahir,#tanggal_lahir1,#tanggal_lahir2').datepicker({ autoclose: true });

      $('#datepicker11').datepicker({
        startDate: "-30d",
        endDate: "+0d",
        autoclose: true
      });

      // Clockpicker
      $('.clockpicker').clockpicker().find('input').change(function () { console.log(this.value); });
      $('#detail_mulai,#detail_selesai,#jam-mulai-utama,#jam-selesai-utama,#jam1,#jam2,#jam-mulai-tambahan,#jam-selesai-tambahan,#jam-mulai-tambahan1,#jam-selesai-tambahan1').clockpicker({ placement: 'top', align: 'left', autoclose: true, donetext: 'Done' });

      // Select2
      $('.select2').select2();

    });
  </script>
  <script>
    $(function () {
      //Initialize Select2 Elements
      $('.select2').select2()
    })
  </script>
  
  <!-- CK Editor -->
  <script src="libs/ckeditor/ckeditor.js"></script>
    <script>
      $(function () {
        CKEDITOR.replace('new')
        $('.textarea').wysihtml5()
      })
    </script>
    <script>
      $(function () {
        CKEDITOR.replace('new1')
        $('.textarea').wysihtml5()
      })
    </script>
  
  <script>
    $(function () {

      $('#table-laporan-cuti').dataTable({
        "aLengthMenu": [
          [50, 100, 150, -1],
          [50, 100, 150, 'All']
        ],
        "processing": true,
        "serverSide": true,
        'paging': true,
        'ordering': true,
        'info': false,
        'autoWidth': true,
        'responsive': true,
        "ordering": true,
        "lengthChange": true,
        "scrollX": true,
        "scrollY": "550px",
        "scrollCollapse": true,
        "ajax": {
          "url": "module/cuti-pegawai/ss-cuti-pegawai?<?= paramEncrypt("tab=ss-laporan-cuti&pg=" . $nip . "&ta=" . $year . "/" . $src); ?>",
          "type": "POST",
        },
        "fnCreatedRow": function (row, data, index) {
          $('td', row).eq(0).html(index + 1);
        }
      });

      $('#example1').DataTable()
      $('#example_normal').DataTable()
      $('#example').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example2').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example3').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example4').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example5').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example6').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example7').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example8').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example9').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example10').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example11').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example12').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example13').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example14').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example15').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example16').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example17').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example27').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#example28').DataTable({
        'lengthMenu': [
          [50, 100, 150, -1],
          [50, 100, 150, "All"]
        ]

      })
      $('#kinerja').DataTable({
        "aLengthMenu": [
          [50, 100, 150, -1],
          [50, 100, 150, 'All']
        ],
        dom: 'lBfrtip',
        buttons: [{
          extend: 'pdfHtml5',
          orientation: 'landscape',
          pageSize: 'LEGAL',
          text: '<i class="fa fa-file-pdf-o"> Export To PDF</i>',
          titleAttr: 'PDF',

        }

        ]
      })

      $('#imt').DataTable({
        "aLengthMenu": [
          [50, 100, 150, -1],
          [50, 100, 150, 'All']
        ],
        dom: 'lBfrtip',
        buttons: [{
          extend: 'pdfHtml5',
          orientation: 'landscape',
          pageSize: 'LEGAL',
          text: '<i class="fa fa-file-pdf-o"> Export To PDF</i>',
          titleAttr: 'PDF',

        }

        ]
      })

      $('#jadwal').DataTable({
        "aLengthMenu": [
          [50, 100, 150, -1],
          [50, 100, 150, 'All']
        ],
        dom: 'lBfrtip',
        buttons: [{
          extend: 'collection',
          text: 'Export To',
          buttons: [
            'copy',
            'excel'
          ]
        }]
      })

      $('#absensi').DataTable({
        "aLengthMenu": [
          [50, 100, 150, -1],
          [50, 100, 150, 'All']
        ],
        dom: 'lBfrtip',
        buttons: [{
          extend: 'collection',
          text: 'Export To',
          buttons: [
            'copy',
            'excel'
          ]
        }]
      })

      $('#all').DataTable({
        'paging': true,
        'lengthChange': false,
        'searching': false,
        'ordering': false,
        'info': false,
        'autoWidth': true,
        'responsive': true
      })
      $('#laporan').DataTable({
        "aLengthMenu": [
          [50, 100, 150, -1],
          [50, 100, 150, 'All']
        ],
        dom: 'lBfrtip',
        buttons: [{
          extend: 'collection',
          text: 'Export To',
          buttons: [
            'copy',
            'excel'
          ]
        }]
      })
    })
  </script>
  
  <script>
    $('#alert-simpan').click(function () {
      alert('simpandata');
    })
  </script>
  <script>
    $(document).ready(function () {
      // Sembunyikan alert validasi kosong
      $("#kosong").hide();
    });
  </script>
  <script>
    $(function () {
      /* BOOTSTRAP SLIDER */
      $('.slider').slider()
    })
  </script>
  <script>
    $('#myModal').modal('show');
  </script>

  <?php if (isset($tampil_grafik) && $tampil_grafik === 'yes') { ?>
    <script src="libs/hightChart/highcharts.js" type="text/javascript"></script>
    <!-- <script src="libs/hightChart/exporting.js" type="text/javascript"></script> -->
    <!-- <script src="https://code.highcharts.com/highcharts.js"></script> -->
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>


    <script type="text/javascript">
      var chart1; // globally available
      $(document).ready(function () {
        chart1 = new Highcharts.Chart({
          chart: {
            renderTo: 'container',
            type: 'column'
          },
          title: {
            text: 'Grafik Pencapaian Kinerja '
          },
          xAxis: {
            categories: ['Bulan']
          },
          yAxis: {
            title: {
              text: 'Nilai Kinerja'
            }
          },
          plotOptions: {
            series: {
              borderWidth: 0,
              dataLabels: {
                enabled: true,
                format: '{point.y:.1f}%'
              }
            }
          },
          tooltip: {
            headerFormat: '<b>{series.name}</b><br/>',
            pointFormat: ''
          },
          series: [
            <?php
            $sql_pencapaian = bukaquery("SELECT (nskp+nprilaku+(penyerapan*0.2)) as jumlah,tanggal_penilaian  FROM tm_penilaian inner join tm_penyerapan on tm_penilaian.id_penyerapan=tm_penyerapan.id_penyerapan where year(tm_penilaian.tanggal_penilaian)='$thn_now' and id_user='$id_user'");
            while ($get = fetch_array($sql_pencapaian)) {
              ?> {
                name: '<?php echo konversiBulanTahun($get['tanggal_penilaian']); ?>',
                data: [<?php echo $get['jumlah']; ?>]
              },
            <?php } ?>
          ]
        });
      });
    </script>
  <?php } ?>

  <?php if (isset($tampil_cuti) && $tampil_cuti === 'yes') { ?>
  
    <script>
      $(function () {

        /* initialize the external events
         -----------------------------------------------------------------*/
        function init_events(ele) {
          ele.each(function () {

            // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
            // it doesn't need to have a start or end
            var eventObject = {
              title: $.trim($(this).text()) // use the element's text as the event title
            }

            // store the Event Object in the DOM element so we can get to it later
            $(this).data('eventObject', eventObject)

            // make the event draggable using jQuery UI
            $(this).draggable({
              zIndex: 1070,
              revert: true, // will cause the event to go back to its
              revertDuration: 0 //  original position after the drag
            })

          })
        }

        init_events($('#external-events div.external-event'))

        /* initialize the calendar
         -----------------------------------------------------------------*/
        //Date for the calendar events (dummy data)
        var date = new Date()
        var d = date.getDate(),
          m = date.getMonth(),
          y = date.getFullYear()
        $('#calendar').fullCalendar({
          header: {
            left: 'prev,next today',
            center: 'title',
            //                            right: 'month,agendaWeek,agendaDay'
          },
          buttonText: {
            today: 'today',
            month: 'month',
            week: 'week',
            day: 'day'
          },
          //Random default events
          events: [
            <?php
            while ($row = fetch_array($cuti)) {
              ?> {
                title: '<?php echo $row['nama_pegawai']; ?>',
                start: '<?php echo $row['tanggal']; ?>',
                backgroundColor: '#f56954', //red
                borderColor: '#f56954' //red
              },
            <?php } ?>
          ],
        })

        /* ADDING EVENTS */
        var currColor = '#3c8dbc' //Red by default
        //Color chooser button
        var colorChooser = $('#color-chooser-btn')
        $('#color-chooser > li > a').click(function (e) {
          e.preventDefault()
          //Save color
          currColor = $(this).css('color')
          //Add color effect to button
          $('#add-new-event').css({
            'background-color': currColor,
            'border-color': currColor
          })
        })

      })
    </script>
  <?php } ?>

  <?php if (isset($tampil_cuti_full) && $tampil_cuti_full === 'yes') { ?>
    <script>
      /* initialize the calendar
      -----------------------------------------------------------------*/

      var date = new Date();
      var d = date.getDate();
      var m = date.getMonth();
      var y = date.getFullYear();
      var className = Array('fc-primary', 'fc-danger', 'fc-default', 'fc-success', 'fc-info', 'fc-warning', 'fc-danger-solid', 'fc-warning-solid', 'fc-success-solid', 'fc-default-solid', 'fc-success-solid', 'fc-primary-solid');

      $calendar = $('#cuti');
      $calendar.fullCalendar({

        header: {
          right: 'prev,next today',
          center: 'title',
          left: 'month,listWeek'
        },
        selectable: true,
        selectHelper: true,
        select: function (start, end) {

          // on select we show the Sweet Alert modal with an input
          swal({
            title: 'Create an Event',
            html: '',
            content: {
              element: "input",
              attributes: {
                placeholder: "Event Title",
                type: "text",
                id: "input-field",
                className: "form-control"
              },
            },
            buttons: {
              cancel: true,
              confirm: true,
            },
          }).then(
            function () {
              var eventData;
              var classRandom = className[Math.floor(Math.random() * className.length)];
              event_title = $('#input-field').val();

              if (event_title) {
                eventData = {
                  title: event_title,
                  start: start,
                  className: classRandom,
                  end: end
                };
                $calendar.fullCalendar('renderEvent', eventData, true); // stick? = true
              }

              $calendar.fullCalendar('unselect');
            }
          );
        },
        events: [
          <?php
          while ($row = fetch_array($yanghamil)) {
            ?> {
              title: '<?php echo $row['nama_pegawai']; ?>',
              start: '<?php echo $row['tgl_mulai']; ?>',
              end: '<?php echo $row['tgl_selesai']; ?>',
              backgroundColor: '#f56954', //red
              borderColor: '#f56954', //red
              allDay: true
            },
          <?php } ?>
                           <?php
                           while ($row = fetch_array($cuti)) {
                             ?> {
              title: '<?php echo $row['nama_pegawai']; ?>',
              start: '<?php echo $row['tanggal']; ?>',
              allDay: true
            },
          <?php } ?>
        ],


      });



    </script>
  <?php } ?>

  <?php if (isset($tampil_agenda_rapat) && $tampil_agenda_rapat === 'yes') { ?>
    <script>
      $(function () {

        /* initialize the external events
         -----------------------------------------------------------------*/
        function init_events(ele) {
          ele.each(function () {

            // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
            // it doesn't need to have a start or end
            var eventObject = {
              title: $.trim($(this).text()) // use the element's text as the event title
            }

            // store the Event Object in the DOM element so we can get to it later
            $(this).data('eventObject', eventObject)

            // make the event draggable using jQuery UI
            $(this).draggable({
              zIndex: 1070,
              revert: true, // will cause the event to go back to its
              revertDuration: 0 //  original position after the drag
            })

          })
        }

        init_events($('#external-events div.external-event'))

        /* initialize the calendar
         -----------------------------------------------------------------*/
        //Date for the calendar events (dummy data)
        var date = new Date()
        var d = date.getDate(),
          m = date.getMonth(),
          y = date.getFullYear()
        $('#agenda').fullCalendar({
          header: {
            left: 'prev,next today',
            center: 'title',
            //                            right: 'month,agendaWeek,agendaDay'
          },
          buttonText: {
            today: 'today',
            month: 'month',
            week: 'week',
            day: 'day'
          },
          //Random default events
          events: [
            <?php
            function limit_words($string, $word_limit)
            {
              $words = explode(" ", $string);
              return implode(" ", array_splice($words, 0, $word_limit));
            }
            $sql_agenda = bukaquery("SELECT tanggal, kegiatan from tt_agenda");
            while ($ag = fetch_array($sql_agenda)) {
              ?> {
                title: '<?php echo $ag['kegiatan']; ?>',
                start: '<?php echo $ag['tanggal']; ?>',
                backgroundColor: 'bluelight', //red
                borderColor: '#f56954' //red
              },
            <?php } ?>
          ],
        })

        /* ADDING EVENTS */
        var currColor = '#3c8dbc' //Red by default
        //Color chooser button
        var colorChooser = $('#color-chooser-btn')
        $('#color-chooser > li > a').click(function (e) {
          e.preventDefault()
          //Save color
          currColor = $(this).css('color')
          //Add color effect to button
          $('#add-new-event').css({
            'background-color': currColor,
            'border-color': currColor
          })
        })

      })
    </script>
  <?php } ?>

  <?php if (isset($tampil_kalender) && $tampil_kalender === 'yes') { ?>
    <script>
      /* initialize the calendar
      -----------------------------------------------------------------*/

      var date = new Date();
      var d = date.getDate();
      var m = date.getMonth();
      var y = date.getFullYear();
      var className = Array('fc-primary', 'fc-danger', 'fc-default', 'fc-success', 'fc-info', 'fc-warning', 'fc-danger-solid', 'fc-warning-solid', 'fc-success-solid', 'fc-default-solid', 'fc-success-solid', 'fc-primary-solid');

      $calendar = $('#cuti');
      $calendar.fullCalendar({

        header: {
          right: 'prev,next today',
          center: 'title',
          left: 'month,agendaWeek,agendaDay,listWeek'
        },
        selectable: true,
        selectHelper: true,
        select: function (start, end) {

          // on select we show the Sweet Alert modal with an input
          swal({
            title: 'Create an Event',
            html: '',
            content: {
              element: "input",
              attributes: {
                placeholder: "Event Title",
                type: "text",
                id: "input-field",
                className: "form-control"
              },
            },
            buttons: {
              cancel: true,
              confirm: true,
            },
          }).then(
            function () {
              var eventData;
              var classRandom = className[Math.floor(Math.random() * className.length)];
              event_title = $('#input-field').val();

              if (event_title) {
                eventData = {
                  title: event_title,
                  start: start,
                  className: classRandom,
                  end: end
                };
                $calendar.fullCalendar('renderEvent', eventData, true); // stick? = true
              }

              $calendar.fullCalendar('unselect');
            }
          );
        },
        events: [
          <?php
          while ($row = fetch_array($yanghamil)) {
            ?> {
              title: '<?php echo $row['nama_pegawai']; ?>',
              start: '<?php echo $row['tgl_mulai']; ?>',
              end: '<?php echo $row['tgl_selesai']; ?>',
              backgroundColor: '#f56954', //red
              borderColor: '#f56954', //red
              allDay: true
            },
          <?php } ?>
                           <?php
                           while ($row = fetch_array($cuti)) {
                             ?> {
              title: '<?php echo $row['nama_pegawai']; ?>',
              start: '<?php echo $row['tanggal']; ?>',
              allDay: true
            },
          <?php } ?>
        ],


      });



    </script>
  <?php } ?>

  <?php if (isset($kalender_kinerja) && $kalender_kinerja === 'yes') { ?>	
    ?>
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
    <script>
    jQuery(function ($) {
      // ===== Variabel server → JS =====
      var CUR_Y = <?php echo (int) $curY; ?>;
      var CUR_M = <?php echo (int) $curM; ?>;
      var PREV_Y = <?php echo (int) $prevY; ?>;
      var PREV_M = <?php echo (int) $prevM; ?>;
      var TUTUP = <?php echo (int) $tutup_kinerja; ?>;
      var CAN_EDIT_PREV = <?php echo $canEditPrevMonth ? 'true' : 'false'; ?>;
      
      // ===== Export =====
        $('#btnExportExcel').on('click', function(){
            var periodeText = $('#label-periode').text();
            var parts = periodeText.split(' ');
            var monthName = parts[0], year = parts[1];
            var month = new Date(monthName+' 1,'+year).getMonth()+1;
            exportMonthlyDetail(year, month);
        });
        
        function exportMonthlyDetail(y, m){
            $.getJSON('module/kinerja-pegawai/api-kinerja.php', { act:'monthly-detail', y:y, m:m }, function(res){
                var items = res.items || [];
                if(!items.length){
                    alert('Tidak ada aktivitas di bulan ini.');
                    return;
                }
        
                var data = [['Tanggal','Jam Mulai','Jam Selesai','Jenis','SKP','Keterangan','Menit']];
                items.forEach(function(it){
                    data.push([
                        it.tanggal,
                        it.waktu_mulai ? it.waktu_mulai.substring(0,5) : '',
                        it.waktu_akhir ? it.waktu_akhir.substring(0,5) : '',
                        it.status || '',
                        it.skp || '',
                        it.uraian || '',
                        it.menit || 0
                    ]);
                });
        
                var wb = XLSX.utils.book_new();
                var ws = XLSX.utils.aoa_to_sheet(data);
                XLSX.utils.book_append_sheet(wb, ws, y+'-'+m);
                XLSX.writeFile(wb, 'Kinerja-Detail-'+y+'-'+m+'.xlsx');
            });
        }
        
        // contoh tombol klik
        $('#btnExportExcel').on('click', function(){
            var periodeText = $('#label-periode').text();
            var parts = periodeText.split(' ');
            var monthName = parts[0], year = parts[1];
            var month = new Date(monthName+' 1,'+year).getMonth()+1;
            exportMonthlyDetail(year, month);
        });


    
      // ===== Helper umum =====
      function normalizeTime(val) {
        if (!val) return '';
        val = String(val).replace(/[：٫·•ː꞉︓﹕．。]/g, ':')
          .replace(/\s+/g, '')
          .replace(/h/ig, ':')
          .replace(/\.(\d{2})$/, ':$1');
        var m = val.match(/(\d{1,2})\s*:\s*(\d{2})/);
        if (!m) return '';
        var hh = ('0' + Math.min(23, parseInt(m[1], 10))).slice(-2);
        var mm = ('0' + Math.min(59, parseInt(m[2], 10))).slice(-2);
        return hh + ':' + mm;
      }
    
      function putBadge(dateStr, count) {
        var $td = $('#calendar-kinerja').find('td.fc-day[data-date="' + dateStr + '"]');
        if (!$td.length) return;
        $td.find('.fc-mini-badge').remove();
        $td.append('<span class="fc-mini-badge" title="Aktivitas: ' + count + '">' + count + '</span>');
      }
    
      function isFutureBlocked(dateMoment) {
        var todayEnd = moment().endOf('day');
        var sameMonth = (dateMoment.year() === moment().year()) && (dateMoment.month() === moment().month());
        return sameMonth && dateMoment.isAfter(todayEnd);
      }
    
      function canEditDate(dateMoment) {
        var y = +dateMoment.format('YYYY');
        var m = +dateMoment.format('M');
        var now = moment();
        if (y === CUR_Y && m === CUR_M) return !dateMoment.isAfter(now, 'day');
        if (y === PREV_Y && m === PREV_M) return !!CAN_EDIT_PREV;
        return false;
      }
    
      function paintDisabledFutureCells() {
        var todayEnd = moment().endOf('day');
        $('#calendar-kinerja td.fc-day').each(function () {
          var d = moment($(this).data('date'), 'YYYY-MM-DD');
          if (!d.isValid()) return;
          var sameMonth = (d.year() === todayEnd.year()) && (d.month() === todayEnd.month());
          var disable = sameMonth && d.isAfter(todayEnd);
          $(this).toggleClass('fc-disabled-day', disable);
        });
      }
    
      // ===== Select2 =====
      $(document).on('focusin.modal', function (e) {
        if ($(e.target).closest('.select2-container').length) e.stopImmediatePropagation();
      });
    
      function initSelect2Once() {
        if (!$.fn.select2) return;
        ['#detail_skp_utama', '#detail_skp_tambahan'].forEach(function (sel) {
          var $el = $(sel);
          if (!$el.data('select2')) {
            $el.select2({ width: '100%', dropdownParent: $('#modalDetail') });
          }
        });
      }
    
      function setSelect2Val($select, value, textIfMissing) {
        if (!$select) return;
        if (!value) { $select.val(null).trigger('change'); return; }
        var exists = $select.find("option[value='" + value + "']").length > 0;
        if (!exists) {
          var opt = new Option(textIfMissing || value, value, true, true);
          $select.append(opt);
        }
        $select.val(value).trigger('change');
      }
    
      function rebindJenisUI(jenis, isAdd) {
        var isUtama = (jenis || 'Utama') === 'Utama';
        $('#wrap_detail_skp_utama').toggle(isUtama);
        $('#wrap_detail_skp_tambahan').toggle(!isUtama);
        $('input[name="jenisRadio"][value="' + (jenis || 'Utama') + '"]').prop('checked', true);
        $('input[name="jenisRadio"]').prop('disabled', !isAdd);
      }
    
      function setDetailReadOnly(ro) {
        var dis = !!ro;
        $('#detail_mulai,#detail_selesai,#detail_uraian').prop('disabled', dis);
        $('#detail_skp_utama,#detail_skp_tambahan').prop('disabled', dis).trigger('change.select2');
        $('#btnSave').toggle(!dis);
      }
    
      // ===== Load SKP berdasarkan tahun tanggal =====
      function loadSkpByDate(tanggal, callback) {
        if (!tanggal) return;
        var year = moment(tanggal).year();
        $.ajax({
          url: 'module/kinerja-pegawai/api-kinerja.php',
          type: 'GET',
          data: { act: 'skp-by-year', tanggal: tanggal },
          dataType: 'json',
          success: function(res) {
            if (!res || !res.options || res.options === '') {
              alert('Belum ada SKP tahunan untuk tahun ' + year + '. Silakan input SKP tahunan dulu.');
              $('#detail_skp_utama').html('<option value="">(SKP belum tersedia)</option>');
            } else {
              $('#detail_skp_utama').html(res.options);
            }
            if (typeof callback === 'function') callback();
          },
          error: function() {
            $('#detail_skp_utama').html('<option value="">(Gagal memuat)</option>');
          }
        });
      }
    
      // ===== Modal DETAIL =====
      function openDetail(it, editable, isAdd) {
        var tanggal = it.tanggal || '';
        $('#detail_tanggal').val(tanggal);
        loadSkpByDate(tanggal);
    
        $('#detail_mulai').val((it.waktu_mulai || '').substring(0,5));
        $('#detail_selesai').val((it.waktu_akhir || '').substring(0,5));
        $('#detail_uraian').val(it.uraian || '');
        $('#detail_id').val(isAdd ? '' : (it.id_kinerja || ''));
    
        var jenis = it.status || 'Utama';
        rebindJenisUI(jenis, isAdd);
    
        if (jenis === 'Utama') {
          setSelect2Val($('#detail_skp_utama'), it.kd_skp, (it.skp ? it.skp + ' [' + (it.waktu || '') + ' Menit]' : null));
          setSelect2Val($('#detail_skp_tambahan'), null);
        } else {
          setSelect2Val($('#detail_skp_tambahan'), it.kd_skp, (it.skp ? it.skp + ' [' + (it.waktu || '') + ' Menit]' : null));
          setSelect2Val($('#detail_skp_utama'), null);
        }
    
        $('#detail_skp_utama,#detail_skp_tambahan').trigger('change');
    
        setDetailReadOnly(!editable);
        $('#detailActionUrl').val(isAdd ? '<?php echo $urlAdd; ?>' : '<?php echo $urlUpdateNoId; ?>');
    
        // Radio jenis
        $('input[name="jenisRadio"]').off('change').on('change', function() {
          rebindJenisUI(this.value, isAdd);
          if (isAdd) $('#detailActionUrl').val('<?php echo $urlAdd; ?>');
        });
    
        // Submit form
        $('#formDetail').off('submit').on('submit', function() {
          var kd = $('#wrap_detail_skp_utama').is(':visible') ? $('#detail_skp_utama').val() : $('#detail_skp_tambahan').val();
          if (!kd) { alert('Pilih aktivitas (SKP) dulu.'); return false; }
    
          var jm = normalizeTime($('#detail_mulai').val());
          var js = normalizeTime($('#detail_selesai').val());
          if (!jm || !js) { alert('Jam tidak valid.'); return false; }
    
          $('#detail_kd_skp').val(kd);
          $('#detail_mulai').val(jm + ':00');
          $('#detail_selesai').val(js + ':00');
          $(this).attr('action', $('#detailActionUrl').val());
        });
    
        // Init Select2 & Clockpicker
        $('#modalDetail').off('shown.bs.modal').on('shown.bs.modal', function() {
          initSelect2Once();
          if ($.fn.clockpicker) {
            try { $('#detail_mulai,#detail_selesai').clockpicker('remove'); } catch(e){}
            $('#detail_mulai,#detail_selesai').prop('readonly',true).clockpicker({ autoclose:true, container:'body', placement:'bottom', align:'left', donetext:'Simpan' });
          }
        });
    
        $('#modalDetail').modal('show');
      }
    
      function openDetailView(it) {
        $('#view_tanggal').val(it.tanggal || '');
        $('#view_mulai').val((it.waktu_mulai || '').substring(0,5));
        $('#view_selesai').val((it.waktu_akhir || '').substring(0,5));
        $('#view_jenis').val(it.status || '');
        $('#view_skp').val(it.skp ? (it.skp + (it.waktu ? ' [' + it.waktu + ' Menit]' : '')) : '');
        $('#view_uraian').val(it.uraian || '');
        $('#modalDetailView').modal('show');
      }
    
      // ===== FullCalendar =====
      $('#calendar-kinerja').fullCalendar({
        header: { left:'prev,next today', center:'title', right:'month' },
        buttonText: { today:'Hari ini', month:'Bulan' },
        viewRender: function(view) {
          var y = view.intervalStart.format('YYYY');
          var m = view.intervalStart.format('MM');
          $('#label-periode').text(view.title);
          $('#calendar-kinerja .fc-mini-badge').remove();
    
          $.getJSON('module/kinerja-pegawai/api-kinerja.php', { act:'monthly-summary', y:y, m:m }, function(res){
            var days = res.days || {};
            Object.keys(days).forEach(function(d){ putBadge(d, days[d].aktivitas || 0); });
            var s = res.summary || {};
            $('#sum-aktivitas').text(s.total_aktivitas || 0);
            $('#sum-menit').text(s.total_menit || 0);
            $('#sum-belum').text(s.total_belum_validasi || 0);
            $('#sum-setuju').text(s.total_disetujui || 0);
          });
    
          paintDisabledFutureCells();
        },
        dayClick: function(date){
          if (isFutureBlocked(date)) return;
          var tgl = date.format('YYYY-MM-DD');
          var canEdit = canEditDate(date);
    
          $('#modalDayTitle').text('Daftar Aktivitas ' + date.format(', D MMM YYYY') + (canEdit?'':' — (View only)'));
          $('#btnAddFromDay').toggle(canEdit);
    
          $('#tblDay tbody').empty();
          $.getJSON('module/kinerja-pegawai/api-kinerja.php', { act:'day-list', date:tgl }, function(res){
            var items = res.items || [];
            if (!items.length) $('#tblDay tbody').append('<tr><td colspan="7" class="text-center text-muted">Belum ada aktivitas</td></tr>');
            else items.forEach(function(it){
              var jam = (it.waktu_mulai||'') + ' - ' + (it.waktu_akhir||'');
              var btns = '<button class="btn btn-xs '+(canEdit?'btn-primary':'btn-default')+' btnDetil" data-id="'+it.id_kinerja+'">'+(canEdit?'Detil':'Lihat')+'</button>';
              if(canEdit) btns += ' <button class="btn btn-xs btn-danger btnHapus" data-id="'+it.id_kinerja+'">Hapus</button>';
              $('#tblDay tbody').append('<tr><td>'+jam+'</td><td>'+(it.status||'-')+'</td><td>'+it.skp+'</td><td>'+(it.uraian||'')+'</td><td>'+(it.menit||0)+'</td><td>'+(it.volume||0)+'</td><td>'+btns+'</td></tr>');
              $('#tblDay tbody tr:last')[0].__rowdata = it;
            });
    
            // Event
            $('#tblDay').off('click', '.btnDetil').on('click', '.btnDetil', function(){
              var it = $(this).closest('tr')[0].__rowdata;
              $('#modalDay').modal('hide');
              if(canEdit) openDetail(it, true, false);
              else openDetailView(it);
            });
            $('#tblDay').off('click', '.btnHapus').on('click', '.btnHapus', function(){
              var it = $(this).closest('tr')[0].__rowdata;
              if(!confirm('Hapus aktivitas ini?')) return;
              $.post('<?php echo $urlDeleteNoId; ?>',{ ajax:1, id:it.id_kinerja }).done(function(){ location.reload(); }).fail(function(){ alert('Gagal menghapus'); });
            });
    
            // Tambah
            $('#btnAddFromDay').off('click').on('click', function(){
              openDetail({tanggal:tgl, status:'Utama'}, true, true);
              $('#modalDay').modal('hide');
            });
    
            $('#modalDay').modal('show');
          });
        }
      });
    
      // ===== Tanggal di modal berubah =====
      $('#detail_tanggal').on('change', function(){ loadSkpByDate($(this).val()); });
    
      // Init Select2
      initSelect2Once();
    });
    </script>
    
    <script>
    $(document).ready(function () {
        var mainTab = "<?php echo $main_tab; ?>";
        var subTab  = "<?php echo $sub_tab; ?>";
    
        if (mainTab !== "") {
            $('.profile-tab-wrap .nav-tabs a[href="#' + mainTab + '"]').tab('show');
        }
    
        if (subTab !== "") {
            $('.profile-inner-tabs a[href="#' + subTab + '"]').tab('show');
        }
    });
  </script>


  <?php } ?>
</body>

</html>