<?php
error_reporting(0);
require_once('conf/conf.php');
require_once('libs/aes-encrypt/function.php');
//Set URI
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$id = isset($url['id']) ? $url['id'] : null;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Hasil Rekrutmen</title>
    <link rel="shortcut icon" href="img/icon.png" />
    <!-- <div class="panel panel-info">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#dropdown" href="#collapse2">
            </h4>
        </div>
    </div> -->
    <!-- Tell the browser to be responsive to screen width -->
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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
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

<body background="img/bg.jpg">

    <section class="content">
        <center>
            <h3><b>HASIL REKRUTMEN</b></i>
                <p></p>
                </b>
            </h3>
            <img src="img/jakarta.png" width="180" height="200">
            <h3>
                <b>
                    <p></p> <?php echo getOne("select nama_instansi from setup"); ?>
                </b></h3>
        </center>
        <div class="col-md-4">
        </div>

        <div class="box box-succses box-solid" style="min-width:1000px">
            <div class="box-header with-border" style="min-width:1000px">
                <center>
                    <!-- <h3 class="box-title"><span class="fa "> HARAP SCREENSHOT UNTUK BUKTI REGISTRASI !!!</span></h3> -->
                </center>

                <div class="box-tools pull-right">
                    <!--                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>-->
                </div>
                <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="min-width:1000px">
                <form action="" method="post">
                    <div class="form-group">
                        <table align="Left">
                            <tr>
                                <td>
                                    <label>FORMASI = &nbsp&nbsp&nbsp&nbsp</label>
                                </td>
                                <td>
                                    <select class="form-control select2" name="id_formasi" data-placeholder="-Pilih Formasi-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Formasi-</option>
                                        <?php
                                        $tm_shift = bukaquery("select tm_formasi.id_formasi, tm_formasi.formasi, tm_formasi.pendidikan, tm_formasi.status from tm_formasi where tm_formasi.status='AKTIF' ");
                                        while ($row = fetch_array($tm_shift)) {
                                            echo "<option value=" . $row['id_formasi'] . ">" . $row['formasi'] . "&nbsp" . "(" . $row['pendidikan'] . ")" . "</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp&nbsp&nbsp&nbsp</td>
                                <td>
                                    <button type="submit" name="search" name="submit" value="submit" class="btn btn-info">Cari Data <i class="fa fa-search"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- <br><br><br><br> -->
                </form>
                <br><br><br><br>
                <hr>
                <?php
                $formasi_post = isset($_POST['id_formasi']) ? $_POST['id_formasi'] : null;
                if ($formasi_post == '') {
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> <label align="center">Silakan Cari Data</label></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                <?php } else {
                ?>
                    <div class="box-header with-border">
                        <!-- <h3 class="box-title fa fa-list"> <label align="center">Rekrutmen = <?php echo konversiTanggal($cari); ?></label></h3>
                                    <h3 class="box-title fa "> <label align="center">Sampai <?php echo konversiTanggal($cari_); ?></label></h3> -->
                        <h3 class="box-title fa "> <label align="center">Formasi Rekrutmen : <?php echo getOne("select Concat(tm_formasi.formasi,' ', tm_formasi.pendidikan,' ') from tm_formasi where tm_formasi.status='AKTIF' and id_formasi='$formasi_post'"); ?></label></h3>
                        <!-- <h3 class="box-title fa "> <label align="center">(Provinsi/KL : <?php echo "$provinsi_post"; ?></label>)</h3> -->
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                <?php }
                ?>
                <div class="box-body table-responsive">
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>ID REGISTRASI</th>
                                <th>NIK</th>
                                <th>NAMA PEGAWAI</th>
                                <th>TEMPAT LAHIR</th>
                                <th>TANGGAL LAHIR</th>
                                <th>JENIS KELAMIN</th>
                                <th>PENDIDIKAN TERAKHIR</th>
                                <th>FORMASI</th>
                                <th>TANGGAL REGISTRASI</th>
                                <th>STATUS</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $status = 'eror';
                            if ($formasi_post == '') {
                                $status = 'eror';
                            } else {
                                $status = 'AKTIF';
                            }
                            $sql_pegawai = bukaquery("SELECT
                                tm_daftar.id_daftar,
                                tm_daftar.nik,
                                tm_daftar.nama_pegawai,
                                tm_daftar.tempat_lahir,
                                tm_daftar.tgl_lahir,
                                tm_daftar.jk,
                                tm_daftar.status_nikah,
                                tm_daftar.agama,
                                tm_daftar.alamat,
                                tm_daftar.no_hp,
                                tm_daftar.email,
                                tm_daftar.nilai,
                                tm_daftar.pendidikan,
                                tm_daftar.id_formasi,
                                tm_daftar.foto,
                                tm_daftar.riwayat,
                                tm_daftar.ktp,
                                tm_daftar.kk,
                                tm_daftar.ijazah,
                                tm_daftar.transkrip,
                                tm_daftar.sertifikat,
                                tm_daftar.tgl_str,
                                tm_daftar.str,
                                tm_daftar.`status`,
                                tm_daftar.date,
                                tm_formasi.id_formasi,
                                tm_formasi.formasi,
                                tm_formasi.pendidikan
                                FROM
                                tm_daftar
                                INNER JOIN tm_formasi ON tm_daftar.id_formasi = tm_formasi.id_formasi
                                        where tm_daftar.id_formasi='$formasi_post' and tm_daftar.status='TERIMA' order by tm_daftar.date ASC");
                            while ($row = fetch_array($sql_pegawai)) {
                                $no++;
                            ?>
                                <!-- Edit Modal SKP -->
                                <div class="modal fade" id="modal-ubah-<?php echo $row['id_daftar']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=update-master-data-daftar&id=' . $row['id_daftar'] . ''); ?>" method="POST" role="form">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Update Data Daftar</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label>Id Registrasi</label>
                                                            <input type="text" class="form-control" name="id_daftar" value="<?php echo $row['id_daftar']; ?>" placeholder="Id Registrasi" readonly>
                                                            <label>Nama Pegawai</label>
                                                            <input type="text" class="form-control" name="nama_pegawai" value="<?php echo $row['nama_pegawai']; ?>" placeholder="Nama Pegawai" readonly>
                                                            <label class="control-label">Status<font color="red">*</font></label>
                                                            <?php echo enumDropdown("tm_daftar", "status", "", "-Pilih-"); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Tutup Edit Modal permintaan -->
                                <!-- Tutup Edit Modal permintaan -->
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><span class="btn-xs btn-warning"> <?php echo $row['id_daftar'] ?></span></td>
                                    <td><?php echo $row['nik']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <td><?php echo $row['tempat_lahir']; ?></td>
                                    <td><?php echo $row['tgl_lahir']; ?></td>
                                    <td><?php echo $row['jk']; ?></td>
                                    <td><?php echo $row['pendidikan'] ?></td>
                                    <td><?php echo $row['formasi'] ?></td>
                                    <td><?php echo $row['date']; ?></td>
                                    <td><span class="btn-xs btn-success"> <?php echo $row['status'] ?></span></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        </div>
        </div>
        </div>
    </section>


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
