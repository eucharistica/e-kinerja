<?php
error_reporting(0);
require_once('conf/conf.php');
require_once('libs/aes-encrypt/function.php');
//Set URI
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$alert = isset($url['alert']) ? $url['alert'] : null;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Form Registrasi Pegawai Baru</title>
    <link rel="shortcut icon" href="img/icon.png" />

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
    <!-- Main content -->
    <section class="content">
        <center>
            <h3><b>SELAMAT DATANG DI FORM REGISTRASI</b></i>
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
        <div class="col-md-4">
            <!-- <div class="align-center"> -->

            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><span class="fa fa-user-circle"> Form Registrasi</span></h3>

                    <div class="box-tools pull-right">
                        <!--                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>-->
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <?php if (isset($_POST['submit'])) {
                        $autonumber = nokiamat('id_daftar', 'tm_daftar');
                        $tanggal = FormatTgl('Y-m-d', $_POST['tgl_lahir']);
                        $tanggal1 = FormatTgl('Y-m-d', $_POST['tgl_str']);
                        $ekstensi_diperbolehkan = array('pdf', 'jpg');

                        //foto
                        $foto = $_FILES['foto']['name'];
                        $x_foto = explode('.', $foto);
                        $nama_foto = $autonumber  . "_1" . round(microtime(true)) . '.' . end($x_foto);
                        $ekstensi_foto = strtolower(end($x_foto));
                        $ukuran_foto = $_FILES['foto']['size'];
                        $tmp_foto = $_FILES['foto']['tmp_name'];
                        $folder_foto = "files/daftar/";

                        //riwayat
                        $riwayat = $_FILES['riwayat']['name'];
                        $x_riwayat = explode('.', $riwayat);
                        $nama_riwayat = $autonumber  . "_2" . round(microtime(true)) . '.' . end($x_riwayat);
                        $ekstensi_riwayat = strtolower(end($x_riwayat));
                        $ukuran_riwayat = $_FILES['riwayat']['size'];
                        $tmp_riwayat = $_FILES['riwayat']['tmp_name'];
                        $folder_riwayat = "files/daftar/";

                        //ktp
                        $ktp = $_FILES['ktp']['name'];
                        $x_ktp = explode('.', $ktp);
                        $nama_ktp = $autonumber  . "_3" . round(microtime(true)) . '.' . end($x_ktp);
                        $ekstensi_ktp = strtolower(end($x_ktp));
                        $ukuran_ktp = $_FILES['ktp']['size'];
                        $tmp_ktp = $_FILES['ktp']['tmp_name'];
                        $folder_ktp = "files/daftar/";

                        //kk
                        $kk = $_FILES['kk']['name'];
                        $x_kk = explode('.', $kk);
                        $nama_kk = $autonumber  . "_4" . round(microtime(true)) . '.' . end($x_kk);
                        $ekstensi_kk = strtolower(end($x_kk));
                        $ukuran_kk = $_FILES['kk']['size'];
                        $tmp_kk = $_FILES['kk']['tmp_name'];
                        $folder_kk = "files/daftar/";

                        //ijazah
                        $ijazah = $_FILES['ijazah']['name'];
                        $x_ijazah = explode('.', $ijazah);
                        $nama_ijazah = $autonumber  . "_5" . round(microtime(true)) . '.' . end($x_ijazah);
                        $ekstensi_ijazah = strtolower(end($x_ijazah));
                        $ukuran_ijazah = $_FILES['ijazah']['size'];
                        $tmp_ijazah = $_FILES['ijazah']['tmp_name'];
                        $folder_ijazah = "files/daftar/";

                        //transkrip
                        $transkrip = $_FILES['transkrip']['name'];
                        $x_transkrip = explode('.', $transkrip);
                        $nama_transkrip = $autonumber  . "_6" . round(microtime(true)) . '.' . end($x_transkrip);
                        $ekstensi_transkrip = strtolower(end($x_transkrip));
                        $ukuran_transkrip = $_FILES['transkrip']['size'];
                        $tmp_transkrip = $_FILES['transkrip']['tmp_name'];
                        $folder_transkrip = "files/daftar/";

                        //sertifikat
                        $sertifikat = $_FILES['sertifikat']['name'];
                        $x_sertifikat = explode('.', $sertifikat);
                        $nama_sertifikat = $autonumber  . "_7" . round(microtime(true)) . '.' . end($x_sertifikat);
                        $ekstensi_sertifikat = strtolower(end($x_sertifikat));
                        $ukuran_sertifikat = $_FILES['sertifikat']['size'];
                        $tmp_sertifikat = $_FILES['sertifikat']['tmp_name'];
                        $folder_sertifikat = "files/daftar/";

                        //skck
                        $skck = $_FILES['skck']['name'];
                        $x_skck = explode('.', $skck);
                        $nama_skck = $autonumber  . "_9" . round(microtime(true)) . '.' . end($x_skck);
                        $ekstensi_skck = strtolower(end($x_skck));
                        $ukuran_skck = $_FILES['skck']['size'];
                        $tmp_skck = $_FILES['skck']['tmp_name'];
                        $folder_skck = "files/daftar/";

                        //surat
                        $surat = $_FILES['surat']['name'];
                        $x_surat = explode('.', $surat);
                        $nama_surat = $autonumber  . "_10" . round(microtime(true)) . '.' . end($x_surat);
                        $ekstensi_surat = strtolower(end($x_surat));
                        $ukuran_surat = $_FILES['surat']['size'];
                        $tmp_surat = $_FILES['surat']['tmp_name'];
                        $folder_surat = "files/daftar/";

                        //lamaran
                        $lamaran = $_FILES['lamaran']['name'];
                        $x_lamaran = explode('.', $lamaran);
                        $nama_lamaran = $autonumber  . "_10" . round(microtime(true)) . '.' . end($x_lamaran);
                        $ekstensi_lamaran = strtolower(end($x_lamaran));
                        $ukuran_lamaran = $_FILES['lamaran']['size'];
                        $tmp_lamaran = $_FILES['lamaran']['tmp_name'];
                        $folder_lamaran = "files/daftar/";

                        //str
                        $str = $_FILES['str']['name'];
                        if ($str == '') {
                            $nama_str = '';
                        } else {
                            $x_str = explode('.', $str);
                            $nama_str = $autonumber  . "_8" . round(microtime(true)) . '.' . end($x_str);
                            $ekstensi_str = strtolower(end($x_str));
                            $ukuran_str = $_FILES['str']['size'];
                            $tmp_str = $_FILES['str']['tmp_name'];
                            $folder_str = "files/daftar/";
                        }

                        //sertifikat profesi
                        $sertifikat_profesi = $_FILES['sertifikat_profesi']['name'];
                        if ($sertifikat_profesi == '') {
                            $nama_sertifikat_profesi = '';
                        } else {
                            $x_sertifikat_profesi = explode('.', $sertifikat_profesi);
                            $nama_sertifikat_profesi = $autonumber  . "_11" . round(microtime(true)) . '.' . end($x_sertifikat_profesi);
                            $ekstensi_sertifikat_profesi = strtolower(end($x_sertifikat_profesi));
                            $ukuran_sertifikat_profesi = $_FILES['sertifikat_profesi']['size'];
                            $tmp_sertifikat_profesi = $_FILES['sertifikat_profesi']['tmp_name'];
                            $folder_sertifikat_profesi = "files/daftar/";
                        }
                        // echo "insert into tm_daftar set id_daftar='$autonumber', nik='$_POST[nik]',nama_pegawai='$_POST[nama_pegawai]',tempat_lahir='$_POST[tempat_lahir]',tgl_lahir='$tanggal',jk='$_POST[jk]',status_nikah='$_POST[status_nikah]',agama='$_POST[agama]',alamat='$_POST[alamat]',no_hp='$_POST[no_hp]',email='$_POST[email]',nilai='$_POST[nilai]',pendidikan='$_POST[pendidikan]',id_formasi='$_POST[id_formasi]', foto='$nama_foto',riwayat='$nama_riwayat',ktp='$nama_ktp',kk='$nama_kk', ijazah='$nama_ijazah',transkrip='$nama_transkrip',sertifikat='$nama_sertifikat',str='$nama_str',status='-',date=now()";


                        $folder = "files/daftar/";
                        if ($foto != '' or $riwayat != '' or $ktp != '' or $kk != '' or $ijazah != '' or $transkrip != '' or $sertifikat != '' or $str != '' or $skck != '' or $surat != '' or $sertifikat_profesi != '' or $lamaran != '') {
                            if (
                                in_array($ekstensi_foto, $ekstensi_diperbolehkan) === true or
                                in_array($ekstensi_riwayat, $ekstensi_diperbolehkan) === true or
                                in_array($ekstensi_ktp, $ekstensi_diperbolehkan) === true or
                                in_array($ekstensi_kk, $ekstensi_diperbolehkan) === true or
                                in_array($ekstensi_ijazah, $ekstensi_diperbolehkan) === true or
                                in_array($ekstensi_transkrip, $ekstensi_diperbolehkan) === true or
                                in_array($ekstensi_sertifikat, $ekstensi_diperbolehkan) === true or
                                in_array($ekstensi_str, $ekstensi_diperbolehkan) === true or
                                in_array($ekstensi_skck, $ekstensi_diperbolehkan) === true or
                                in_array($ekstensi_surat, $ekstensi_diperbolehkan) === true or
                                in_array($ekstensi_sertifikat_profesi, $ekstensi_diperbolehkan) === true or
                                in_array($ekstensi_lamaran, $ekstensi_diperbolehkan) === true
                            ) {
                                if ($ukuran < 5242880) {

                                    bukainputcek("insert into tm_daftar set id_daftar='$autonumber', nik='$_POST[nik]',nama_pegawai='$_POST[nama_pegawai]',tempat_lahir='$_POST[tempat_lahir]',tgl_lahir='$tanggal',jk='$_POST[jk]',status_nikah='$_POST[status_nikah]',agama='$_POST[agama]',alamat='$_POST[alamat]',no_hp='$_POST[no_hp]',email='$_POST[email]',nilai='$_POST[nilai]',pendidikan='$_POST[pendidikan]',id_formasi='$_POST[id_formasi]', foto='$nama_foto',riwayat='$nama_riwayat',ktp='$nama_ktp',kk='$nama_kk', ijazah='$nama_ijazah',transkrip='$nama_transkrip',sertifikat='$nama_sertifikat',skck='$nama_skck',surat='$nama_surat',sertifikat_profesi='$nama_sertifikat_profesi',tgl_str='$tanggal1',str='$nama_str',lamaran='$nama_lamaran',status='-',date=now()");
                                    move_uploaded_file($tmp_foto, $folder_foto . $nama_foto);
                                    move_uploaded_file($tmp_riwayat, $folder_riwayat . $nama_riwayat);
                                    move_uploaded_file($tmp_ktp, $folder_ktp . $nama_ktp);
                                    move_uploaded_file($tmp_kk, $folder_kk . $nama_kk);
                                    move_uploaded_file($tmp_ijazah, $folder_ijazah . $nama_ijazah);
                                    move_uploaded_file($tmp_transkrip, $folder_transkrip . $nama_transkrip);
                                    move_uploaded_file($tmp_sertifikat, $folder_sertifikat . $nama_sertifikat);
                                    move_uploaded_file($tmp_str, $folder_str . $nama_str);
                                    move_uploaded_file($tmp_skck, $folder_skck . $nama_skck);
                                    move_uploaded_file($tmp_surat, $folder_surat . $nama_surat);
                                    move_uploaded_file($tmp_sertifikat_profesi, $folder_sertifikat_profesi . $nama_sertifikat_profesi);
                                    move_uploaded_file($tmp_lamaran, $folder_lamaran . $nama_lamaran);
                                    // header('location:cetak.php');
                                    // echo getOne("select tm_daftar.id_daftar from tm_daftar where id_daftar='$id'");



                                    echo "<script>alert('Data berhasil di Simpan (HARAP SCREENSHOT SETELAH OK) !!!'); window.location = 'cetak.php?" . paramEncrypt("id='$autonumber'") . "'</script>";
                                } else {
                                    echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
                                }
                            } else {
                                echo "<script>alert('File hanya diperbolehkan berformat JPG / PDF !! " . $ekstensi_foto . "'); window.location = 'javascript:history.go(-1)'</script>";
                            }
                        } else {
                            echo "<script>alert('Maaf anda belum upload file !!'); window.location = 'javascript:history.go(-1)'</script>";
                        }
                    }
                    ?>
                    <div class="x_panel">
                        <form method="post" role="form" enctype="multipart/form-data" aria-labelledby="myModalLabel">
                            <fieldset>
                                <label>NIK</label>
                                <div class="form-group">
                                    <input minlength="16" maxlength="16" class="form-control" placeholder="NOMOR INDUK KEPENDUDUKAN" name="nik" type="nik" autofocus>
                                </div>
                                <label>Nama Lengkap</label>
                                <div class="form-group">
                                    <input class="form-control" placeholder="NAMA LENGKAP" name="nama_pegawai" type="nama_pegawai" value="" required>
                                </div>
                                <label>Tempat Lahir</label>
                                <div class="form-group">
                                    <input class="form-control" placeholder="TEMPAT LAHIR" name="tempat_lahir" type="tempat_lahir" value="" required>
                                </div>
                                <label>Tanggal Lahir</label>
                                <input type="text" class="form-control" id="datepicker" placeholder="mm/dd/yyyy" name="tgl_lahir" required readonly>
                                <label>Jenis Kelamin</label>
                                <?php echo UpdateEnumDropdown("tm_daftar", "jk",  ""); ?>
                                <label>Status Menikah</label>
                                <?php echo UpdateEnumDropdown("tm_daftar", "status_nikah", ""); ?>
                                <label>Agama</label>
                                <?php echo UpdateEnumDropdown("tm_daftar", "agama", ""); ?>
                                <label>Alamat Domisili</label>
                                <div class="form-group">
                                    <input class="form-control" placeholder="ALAMAT DOMISILI" name="alamat" type="alamat" value="" required>
                                </div>
                                <label>No. Telepon</label>
                                <div class="form-group">
                                    <input class="form-control" placeholder="NO TELEPON" name="no_hp" type="no_hp" value="" required>
                                </div>
                                <label>Alamat Email</label>
                                <div class="form-group">
                                    <input class="form-control" placeholder="ALAMAT EMAIL" name="email" type="email" value="" required>
                                </div>
                                <label>Nilai IPK / Rata-rata Nilai Ijazah</label>
                                <div class="form-group">
                                    <input class="form-control" placeholder="NILAI" name="nilai" type="nilai" value="" required>
                                </div>
                                <label>Pendidikan Terakhir</label>
                                <?php echo UpdateEnumDropdown("tm_daftar", "pendidikan", ""); ?>
                                <br>
                                <label>Formasi Yang Dilamar</label>
                                <select class="form-control select2" name="id_formasi" data-placeholder="-" style="width: 100%;" required>
                                    <option selected="selected" value="">-</option>
                                    <?php
                                    $tm_shift = bukaquery("select tm_formasi.id_formasi, tm_formasi.formasi, tm_formasi.pendidikan, tm_formasi.status from tm_formasi where tm_formasi.status='AKTIF' ");
                                    while ($row = fetch_array($tm_shift)) {
                                        echo "<option value=" . $row['id_formasi'] . ">" . $row['formasi'] . "&nbsp" . "(" . $row['pendidikan'] . ")" . "</option>";
                                    }
                                    ?>
                                </select>
                                <br>
                                <label>Pas Foto</label> <label style="color:red;">*JPG / PDF Max 5MB</label>
                                <input type="file" class="form-control" name="foto" required>
                                <br>
                                <label>Scan Riwayat Hidup</label> <label style="color:red;">*PDF Max 5MB</label>
                                <input type="file" class="form-control" name="riwayat" required>
                                <br>
                                <label>Scan KTP / Surat Keterangan Domisili</label> <label style="color:red;">*PDF Max 5MB</label>
                                <input type="file" class="form-control" name="ktp" required>
                                <br>
                                <label>Scan Kartu Keluarga</label> <label style="color:red;">*PDF Max 5MB</label>
                                <input type="file" class="form-control" name="kk" required>
                                <br>
                                <label>Scan Ijazah Terakhir</label> <label style="color:red;">*PDF Max 5MB</label>
                                <input type="file" class="form-control" name="ijazah" required>
                                <br>
                                <label>Scan Transkrip Nilai</label> <label style="color:red;">*PDF Max 5MB</label>
                                <input type="file" class="form-control" name="transkrip" required>
                                <br>
                                <label>Scan Sertifikat Kursus / Pelatihan Yang Di Miliki</label> <label style="color:red;">*PDF Max 5MB</label>
                                <input type="file" class="form-control" name="sertifikat" required>
                                <br>
                                <label>Scan SKCK</label> <label style="color:red;">*PDF Max 5MB</label>
                                <input type="file" class="form-control" name="skck" required>
                                <br>
                                <label>Scan Surat Keterangan Pernyataan Bahwa Tidak Ada Keluarga / Ikatan Darah Dengan Pegawai RSUD Cipayung </label> <label style="color:red;">*PDF Max 5MB</label>
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#dropdown" href="#collapse2">
                                            <a href="apk/surat.docx"><span class="fa fa-envelope "> Download Surat Pernyataan</span></a>
                                    </h4>
                                </div>
                                <input type="file" class="form-control" name="surat" required>
                                <br>
                                <label>Scan Sertifikat Kompetensi Profesi</label> <label style="color:red;">(Bagi Tenaga Kesehatan) *PDF Max 5MB</label>
                                <input type="file" class="form-control" name="sertifikat_profesi">
                                <br>
                                <label>Tanggal Berakhir STR</label><label style="color:red;"> *Bagi Tenaga Kesehatan</label>
                                <input type="text" class="form-control" id="datepicker1" placeholder="mm/dd/yyyy" name="tgl_str" required readonly>
                                <br>
                                <label>Scan STR</label> <label style="color:red;"> (Bagi Tenaga Kesehatan) *PDF Max 5MB</label>
                                <input type="file" class="form-control" name="str">
                                <br>
                                <label>Scan Surat Lamaran</label> <label style="color:red;">*PDF Max 5MB</label>
                                <input type="file" class="form-control" name="lamaran" required>
                                <br>
                                <!-- Change this to a button or input when using this as a form -->
                                <button type="submit" name="submit" class="btn btn-success btn-block fa fa-sign-in"> REGISTRASI </button>
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