<?php
error_reporting(0);
require_once('conf/conf.php');
require_once('libs/aes-encrypt/function.php');
//Set URI
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$alert = isset($url['alert']) ? $url['alert'] : null;
$id = isset($url['id']) ? $url['id'] : null;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Absensi Pegawai</title>
    <link rel="shortcut icon" href="img/icon.png" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="libs/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="libs/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="libs/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="libs/dist/css/AdminLTE.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="libs/iCheck/square/blue.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="libs/dist/css/AdminLTE.min.css">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="libs/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <!-- Date Picker -->
    <link rel="stylesheet" href="libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="libs/bootstrap-daterangepicker/daterangepicker.css">
    <!-- clock picker -->
    <link rel="stylesheet" type="text/css" href="libs/clockpicker/bootstrap-clockpicker.min.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="libs/iCheck/all.css">
    <!-- fullCalendar -->
    <link rel="stylesheet" href="libs/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="libs/fullcalendar/dist/fullcalendar.print.min.css" media="print">

    <meta name="theme-color" content="#6700DF">
    <link rel="manifest" href="manifest.json">
    <!-- iOS Support -->
    <link rel="apple-touch-icon" href="assets/icons/icon-96x96.png">
    <meta name="apple-mobile-web-app-status-bar" content="#FFFFFF">


    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <style>
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background-color: #fff;
        }

        .preloader .loading {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font: 14px arial;
        }
    </style>
</head>

<body background="img/putih.jpg">
    <!-- page loader-->
    <!--                        <div class="preloader">
                                    <div class="loading">
                                        <center>
                                        <img src="img/loading-page.gif" width="120px">
                                        <p><h4 style="font-family: verdana;font-size: 18;">..Harap Tunggu..</h4></p>
                                        </center>
                                    </div>
                                </div>-->
    <!-- page loader-->
    <!-- Main content -->
    <?php
    // url absen masuk npis/absensi.php?9faf749ed776d02801cdf87679f34a80
    // url absen pulang npis/absensi.php?e6e16d68911517133ed39189eea44dfa
    // echo "IN:" . paramEncrypt("page=IN") . " OUT:" . paramEncrypt("page=OUT");
    $status = isset($url['page']) ? $url['page'] : null;
    if ($status == "IN") {
    ?>
        <section class="content">
            <center>
                <h3><b>ABSENSI PEGAWAI</b></i>
                    <p></p>
                    </b>
                </h3>
                <!-- <img src="img/logo2.png" width="180" height="200"> -->
                <h3>
                    <b>
                        <p></p> <?php //echo getOne("select nama_instansi from setup"); 
                                ?>
                    </b></h3>
            </center>
            <div class="col-md-4">
            </div>
            <div class="col-md-4">
                <!-- <div class="align-center"> -->

                <div class="box box-primary box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><span class="fa fa-user-circle"> Form Absensi</span></h3>

                        <div class="box-tools pull-right">
                            <!--                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>-->
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <?php if (isset($_POST['submit'])) {
                            $cek = getOne("select COUNT(*) from log where user='$_POST[token]'");
                            if ($cek > 0) {
                                $autonumber = nokiamat('id', 'log');
                                // $tanggal = FormatTgl('Y-m-d', $_POST['tgl_lahir']);
                                // $tanggal1 = FormatTgl('Y-m-d', $_POST['tgl_str']);
                                // echo "insert into tm_log set id='$autonumber',user='$_POST[user]',tanggal=now(),status='I'";
                                bukainputcek("insert into log set id='$autonumber',user='$_POST[user]',tanggal='$_POST[datetime]',status='$_POST[status]'");
                                // bukainputcek("insert into tm_log set id='$autonumber',user='$id_user',date=now(),status='$status'");
                                // header('location:cetak.php');
                                // echo getOne("select tm_daftar.id_daftar from tm_daftar where id_daftar='$id'");
                                echo "<script>alert('Absen berhasil di Simpan !!!'); window.location = 'cetakabsensi.php?" . paramEncrypt("id='$autonumber'") . "'</script>";
                            } else {
                                echo "<script>alert('Maaf Token tidak sesuai !!!'); window.location = 'javascript:history.go(-1)'</script>";
                            }
                        }
                        ?>
                        <div class="x_panel">
                            <form method="post" role="form" enctype="multipart/form-data" aria-labelledby="myModalLabel">
                                <fieldset>
                                    <label>Nama Pegawai</label>
                                    <select class="form-control select2" name="user" data-placeholder="-Pilih Nama Pegawai-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Nama Pegawai-</option>
                                        <?php
                                        $tm_pegawai = bukaquery("select tm_pegawai.nip,tm_pegawai.log_finger,tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status='AKTIF' and tm_pegawai.status_pegawai = 'NON PNS' order by nama_pegawai asc");
                                        while ($row = fetch_array($tm_pegawai)) {
                                            echo "<option value=" . $row['log_finger'] . ">" . $row['nama_pegawai'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <!-- <label class="control-label">Waktu</label>
                                    <input class="form-control" type="datetime-local" name="datetime" required>
                                    <br> -->
                                    <label class="control-label">Waktu</label>
                                    <input class="form-control" type="text" name="datetime" value="<?php echo date('Y-m-d G:i:s'); ?>" required>
                                    <br>
                                    <label>Status</label>
                                    <select class="form-control select2" name="status" data-placeholder="-Pilih Status-" style="width: 100%;" required>
                                        <option selected="selected" value="I">Masuk</option>
                                        <option value="O">Pulang</option>
                                    </select>
                                    <br>
                                    <label class="control-label">Token</label>
                                    <input class="form-control" type="password" name="token" required>
                                    <br>

                                    <!-- Change this to a button or input when using this as a form -->
                                    <button type="submit" name="submit" class="btn btn-success btn-block fa fa-sign-in"> SIMPAN </button>
                                </fieldset>
                            </form>
                        </div>
                        <br><br><br>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- </div> -->
            </div>
            <div class="col-md-4">
            </div>
        </section>
    <?php } elseif ($status == "OUT") { ?>
        <section class="content">
            <center>
                <h3><b>ABSENSI PEGAWAI</b></i>
                    <p></p>
                    </b>
                </h3>
                <!-- <img src="img/logo2.png" width="180" height="200"> -->
                <h3>
                    <b>
                        <p></p> <?php //echo getOne("select nama_instansi from setup"); 
                                ?>
                    </b></h3>
            </center>
            <div class="col-md-4">
            </div>
            <div class="col-md-4">
                <!-- <div class="align-center"> -->

                <div class="box box-primary box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><span class="fa fa-user-circle"> Form Absensi</span></h3>

                        <div class="box-tools pull-right">
                            <!--                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>-->
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <?php if (isset($_POST['submit'])) {
                            $cek = getOne("select COUNT(*) from log where user='$_POST[token]'");
                            if ($cek > 0) {
                                $autonumber = nokiamat('id', 'log');
                                // $tanggal = FormatTgl('Y-m-d', $_POST['tgl_lahir']);
                                // $tanggal1 = FormatTgl('Y-m-d', $_POST['tgl_str']);
                                // echo "insert into tm_log set id='$autonumber',user='$_POST[user]',tanggal=now(),status='I'";
                                bukainputcek("insert into log set id='$autonumber',user='$_POST[user]',tanggal=now(),status='$_POST[status]'");
                                // bukainputcek("insert into tm_log set id='$autonumber',user='$id_user',date=now(),status='$status'");
                                // header('location:cetak.php');
                                // echo getOne("select tm_daftar.id_daftar from tm_daftar where id_daftar='$id'");
                                echo "<script>alert('Absen berhasil di Simpan !!!'); window.location = 'cetakabsensi.php?" . paramEncrypt("id='$autonumber'") . "'</script>";
                            } else {
                                echo "<script>alert('Maaf Token tidak sesuai !!!'); window.location = 'javascript:history.go(-1)'</script>";
                            }
                        }
                        ?>
                        <div class="x_panel">
                            <form method="post" role="form" enctype="multipart/form-data" aria-labelledby="myModalLabel">
                                <fieldset>
                                    <label>Nama Pegawai</label>
                                    <select class="form-control select2" name="user" data-placeholder="-Pilih Nama Pegawai-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Nama Pegawai-</option>
                                        <?php
                                        $tm_pegawai = bukaquery("select tm_pegawai.nip,tm_pegawai.log_finger,tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status='AKTIF' and tm_pegawai.status_pegawai = 'NON PNS' order by nama_pegawai asc");
                                        while ($row = fetch_array($tm_pegawai)) {
                                            echo "<option value=" . $row['log_finger'] . ">" . $row['nama_pegawai'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <br>
                                    <label>Status</label>
                                    <select class="form-control select2" name="status" data-placeholder="-Pilih Status-" style="width: 100%;" required>
                                        <option selected="selected" value="I">Masuk</option>
                                        <option value="O">Pulang</option>
                                    </select>
                                    <br>
                                    <label class="control-label">Token</label>
                                    <input class="form-control" type="password" name="token" required>
                                    <!-- <label class="control-label">Status Absensi</label>
                                <?php echo enumDropdown("tm_log", "status", "", "-Pilih Status Absensi-"); ?> -->
                                    <br>
                                    <!-- <label>Pendidikan Terakhir</label>
                                <?php echo UpdateEnumDropdown("tm_daftar", "pendidikan", ""); ?>
                                <br> -->
                                    <!-- Change this to a button or input when using this as a form -->
                                    <button type="submit" name="submit" class="btn btn-success btn-block fa fa-sign-in"> SIMPAN </button>
                                    <!--<p><a href="#" class="">Forget Password</a></p>-->
                                </fieldset>
                            </form>
                        </div>
                        <br><br><br>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- </div> -->
            </div>
            <div class="col-md-4">
            </div>
        </section>
    <?php } ?>

    <!-- jQuery 3 -->
    <script src="libs/jquery/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="libs/bootstrap/js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="libs/iCheck/icheck.min.js"></script>
    <!-- datepicker -->
    <script src="libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(function() {
            $('#today').datepicker({
                autoclose: true,
                startDate: "2"

            })
            $('#multidatepicker').datepicker({
                multidate: true,
                selectable: "multiple",
                weekNumber: true,
                showTrigger: '#callmg',
            })
            $('#datepicker').datepicker({
                autoclose: true
            })
            $('#datepicker1').datepicker({
                autoclose: true
            })
            $('#datepicker2').datepicker({
                autoclose: true
            })
            $('#datepicker3').datepicker({
                autoclose: true
            })
            $('#datepicker4').datepicker({
                autoclose: true
            })
            $('#datepicker5').datepicker({
                autoclose: true
            })
            $('#datepicker6').datepicker({
                autoclose: true
            })
            $('#datepicker7').datepicker({
                autoclose: true
            })
            $('#datepicker8').datepicker({
                autoclose: true
            })
            $('#datepicker9').datepicker({
                autoclose: true
            })
            $('#datepicker10').datepicker({
                autoclose: true
            })
            $('#datepicker11').datepicker({
                autoclose: true
            })
            $('#datepicker12').datepicker({
                autoclose: true
            })
            $('#tanggal_lahir').datepicker({
                autoclose: true,
            })
            $('#tanggal_lahir1').datepicker({
                autoclose: true,
            })
            $('#tanggal_lahir2').datepicker({
                autoclose: true,
            })
        })
    </script>

    <script>
        $(function() {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
        $(document).ready(function() {
            $(".preloader").fadeOut();
        });
    </script>
    <script src="main.js"></script>
</body>

</html>