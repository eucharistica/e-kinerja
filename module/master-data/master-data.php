<?php
$aksi = "module/master-data/aksi-master-data.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default:
        echo "default";
        header('location:error.php');
        break;

    case "list-master-data-bpjs":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST MASTER DATA UMP BPJS</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-bpjs">
                            Buat Master Data UMP BPJS
                        </button>
                        <form action="" method="post">
                            <div class="form-group">
                                <table>
                                    <tr>
                                        <td>
                                            <label style="text-align: center">Tahun</label>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                                                <?php loadThnnow(isset($_POST['tahun']) ? $_POST['tahun'] : null); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                        <!--Modal Add SKP -->
                        <div class="modal fade" id="modal-add-bpjs">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-plus"> Tambah Data UMP</h4>
                                    </div>
                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-data-ump'); ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <!-- <div class="col-md-4"> -->
                                                <label>Tahun</label>
                                                <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" style="width: 100%;" required>
                                                    <?php loadThnKondisi(date('Y') + 1); ?>
                                                </select>
                                            </div>
                                            <label>Jumlah UMP BPJS KS</label>
                                            <input type="number" class="form-control" name="jumlah_ks" placeholder="Rp. " required>
                                            <label>Jumlah UMP BPJS TK</label>
                                            <input type="number" class="form-control" name="jumlah_tk" placeholder="Rp. " required>
                                            <!-- </div> -->
                                        </div>
                                        
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input type="submit" class="btn btn-primary" value="Simpan">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal Skp -->
                        <?php
                        $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : $thn_now;
                        $thn_now = $tahun . "-";
                        if ($tahun_post == 'Tidak Sesuai' or $tahun_post == '') {
                        ?>
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> <label>Tahun Tidak Sesuai</label></h3>
                                <div class="box-tools pull-right">
                                </div>
                            </div>
                        <?php } else {
                        ?>
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> <label align="center"><?= $kategori; ?> UMP BPJS Tahun <?= $tahun_post; ?></label></h3>
                                <div class="box-tools pull-right">
                                </div>
                            </div>
                        <?php }
                        ?>
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jumlah UMP BPJS KS</th>
                                        <th>Jumlah UMP BPJS TK</th>
                                        <th>Tahun</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $sql_bpjs = bukaquery("select
                                        tm_ump_bpjs.id_ump,
                                        tm_ump_bpjs.jumlah_bpjs_ks,
                                        tm_ump_bpjs.jumlah_bpjs_tk,
                                        tm_ump_bpjs.tahun
                                        from tm_ump_bpjs
                                        where tm_ump_bpjs.tahun='$tahun_post'
                                        order by tm_ump_bpjs.tahun asc");
                                    while ($row = fetch_array($sql_bpjs)) {
                                        $no++;
                                        $bpjs_ks = $row['jumlah_bpjs_ks'];
                                        $bpjs_tk = $row['jumlah_bpjs_tk'];
                                    ?>
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-ubah-<?php echo $row['id_ump']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-data-bpjs&id=' . $row['id_ump'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Data UMP BPJS</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <p></p>
                                                                    <label>Jumlah UMP BPJS KS</label>
                                                                    <input type="number" class="form-control" value="<?php echo $bpjs_ks; ?>" name="jumlah_ks" placeholder="Rp." required>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <p></p>
                                                                    <label>Jumlah UMP BPJS TK</label>
                                                                    <input type="number" class="form-control" value="<?php echo $bpjs_tk; ?>" name="jumlah_tk" placeholder="Rp." required>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <p></p>
                                                                    <label>Tahun</label>
                                                                    <input type="number" class="form-control" value="<?php echo $row['tahun']; ?>" name="tahun" placeholder="Masukan tahun" required>
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
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-hapus-<?php echo $row['id_ump']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=delete-master-data-bpjs&id=' . $row['id_ump'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Hapus Master Data UMP BPJS</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <text>Yakin Anda ingin Menghapus Data ini? klik Tombol Ya Untuk Menghapusnya</text>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tutup Edit Modal permintaan -->
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['jumlah_bpjs_ks']; ?></td>
                                            <td><?php echo $row['jumlah_bpjs_tk']; ?></td>
                                            <td><?php echo $row['tahun']; ?></td>
                                            <td>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_ump']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_ump']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            break;

    case "upload-data-pegawai":
?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-upload"> UPLOAD DATA PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <form method="post" action="" enctype="multipart/form-data" style="float:left">
                    <a href="format_upload_pegawai.xlsx" class="btn btn-default" style="float:right">
                        <span class="glyphicon glyphicon-download"></span>
                        Download Format
                    </a>
                    <br>
                    <div class="form-group">
                        <label>Import excel :</label>
                        <input type="file" name="file" class="form-control">
                    </div>
                    <button type="submit" name="preview" class="btn btn-success btn-sm">
                        <span class="glyphicon glyphicon-eye-open"></span> Preview
                    </button>
                </form>
            </div>
            <?php
            // Jika user telah mengklik tombol Preview
            if (isset($_POST['preview'])) {
                //$ip = ; // Ambil IP Address dari User
                $nama_file_baru = 'data.xlsx';

                // Cek apakah terdapat file data.xlsx pada folder tmp
                if (is_file('tmp/' . $nama_file_baru)) // Jika file tersebut ada
                    unlink('tmp/' . $nama_file_baru); // Hapus file tersebut

                $tipe_file = $_FILES['file']['type']; // Ambil tipe file yang akan diupload
                $tmp_file = $_FILES['file']['tmp_name'];

                // Cek apakah file yang diupload adalah file Excel 2007 (.xlsx)
                if ($tipe_file == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
                    // Upload file yang dipilih ke folder tmp
                    // dan rename file tersebut menjadi data{ip_address}.xlsx
                    // {ip_address} diganti jadi ip address user yang ada di variabel $ip
                    // Contoh nama file setelah di rename : data127.0.0.1.xlsx
                    move_uploaded_file($tmp_file, 'tmp/' . $nama_file_baru);

                    // Load librari PHPExcel nya
                    require_once 'libs/PHPExcel/PHPExcel.php';

                    $excelreader = new PHPExcel_Reader_Excel2007();
                    $loadexcel = $excelreader->load('tmp/' . $nama_file_baru); // Load file yang tadi diupload ke folder tmp
                    $sheet = $loadexcel->getActiveSheet()->toArray(null, true, true, true);

                    // Buat sebuah tag form untuk proses import data ke database
                    $a = $aksi . paramEncrypt("module=master-data&act=upload-data-pegawai");
                    echo "<form method='post' action=" . $a . ">";

                    // Buat sebuah div untuk alert validasi kosong
                    echo "<div class='alert alert-danger' id='kosong'>
					Semua data belum diisi, Ada <span id='jumlah_kosong'></span> data yang belum diisi.
			  </div>";

                    echo "<div class='box-body'>
                        <div class='box-body table-responsive'>
                    <table class='table table-bordered table-striped'>
					<tr>
						<th colspan='26' class='text-center'>Preview Data</th>
					</tr>
					<tr>
						<th>Id User</th>
						<th>Nip</th>
						<th>NIK</th>
                                                <th>Nama Pegawai</th>
                                                <th>Tempat lahir</th>
                                                <th>Tgl Lahir</th>
                                                <th>JK</th>
                                                <th>Alamat</th>
                                                <th>NPWP</th>
                                                <th>Pendidikan</th>
                                                <th>TMT</th>
                                                <th>Status Nikah</th>
                                                <th>Rumpun</th>
                                                <th>Pajak</th>
                                                <th>Id Unit</th>
                                                <th>Bpjs ks</th>
                                                <th>Bpjs Jkk</th>
                                                <th>Bpjs Ijht</th>
                                                <th>Bpjs Jp</th>
                                                <th>Foto</th>
                                                <th>Status Kepegawaian</th>
						<th>SubBagian</th>
                                                <th>Log Finger</th>
						<th>Aktif</th>
                                                <th>Id Level</th>
                                                <th>Id Kasatpel</th>
					</tr>";

                    $numrow = 1;
                    $kosong = 0;
                    foreach ($sheet as $row) { // Lakukan perulangan dari data yang ada di excel
                        // Ambil data pada excel sesuai Kolom
                        $id_user = $row['A']; // Ambil data id
                        $nip = $row['B']; // Ambil data nip
                        $nik = $row['C'];
                        $nama = $row['D'];
                        $tempat_lahir = $row['E'];
                        $tgl_lahir = $row['F'];
                        $jk = $row['G'];
                        $alamat = $row['H'];
                        $npwp = $row['I'];
                        $norek = $row['J'];
                        $pendidikan = $row['K'];
                        $tmt = $row['L'];
                        $status_nikah = $row['M'];
                        $rumpun = $row['N'];
                        $pajak = $row['O'];
                        $id_unit = $row['P'];
                        $bpjs_ks = $row['Q'];
                        $bpjs_jkk = $row['R'];
                        $bpjs_ijht = $row['S'];
                        $bpjs_jp = $row['T'];
                        $foto = $row['U'];
                        $status_kepegawaian = $row['V'];
                        $sub_bagian = $row['W'];
                        $log_finger = $row['X'];
                        $aktif = $row['Y'];
                        $id_level = $row['Z'];
                        $id_kasatpel = $row['Z'];
                        // Cek jika semua data tidak diisi
                        if (empty($id_user) && empty($nip) && empty($nik) && empty($nama) && empty($tempat_lahir) && empty($tgl_lahir) && empty($jk) && empty($alamat) && empty($npwp) && empty($norek) && empty($pendidikan) && empty($tmt) && empty($status_nikah) && empty($rumpun) && empty($pajak) && empty($id_unit) && empty($bpjs_ks) && empty($bpjs_jkk) && empty($bpjs_jp) && empty($foto) && empty($status_kepegawaian) && empty($sub_bagian) && empty($log_finger) && empty($aktif) && empty($id_level) && empty($id_kasatpel))
                            continue; // Lewat data pada baris ini (masuk ke looping selanjutnya / baris selanjutnya)                       
                        // Cek $numrow apakah lebih dari 1
                        // Artinya karena baris pertama adalah nama-nama kolom
                        // Jadi dilewat saja, tidak usah diimport
                        if ($numrow > 1) {
                            // Validasi apakah semua data telah diisi
                            $id_user_td = (!empty($id_user)) ? "" : " style='background: #E07171;'"; // Jika kosong, beri warna merah
                            $nip_td = (!empty($nip)) ? "" : " style='background: #E07171;'";
                            $nik_td = (!empty($nik)) ? "" : " style='background: #E07171;'";
                            $nama_td = (!empty($nama)) ? "" : " style='background: #E07171;'";
                            $tempat_lahir_td = (!empty($tempat_lahir)) ? "" : " style='background: #E07171;'";
                            $tgl_lahir_td = (!empty($tgl_lahir)) ? "" : " style='background: #E07171;'";
                            $jk_td = (!empty($jk)) ? "" : " style='background: #E07171;'";
                            $alamat_td = (!empty($alamat)) ? "" : " style='background: #E07171;'";
                            $npwp_td = (!empty($npwp)) ? "" : " style='background: #E07171;'";
                            $norek_td = (!empty($norek)) ? "" : " style='background: #E07171;'";
                            $pendidikan_td = (!empty($pendidikan)) ? "" : " style='background: #E07171;'";
                            $tmt_td = (!empty($tmt)) ? "" : " style='background: #E07171;'";
                            $status_nikah_td = (!empty($status_nikah)) ? "" : " style='background: #E07171;'";
                            $rumpun_td = (!empty($rumpun)) ? "" : " style='background: #E07171;'";
                            $pajak_td = (!empty($pajak)) ? "" : " style='background: #E07171;'";
                            $id_unit_td = (!empty($id_unit)) ? "" : " style='background: #E07171;'";
                            $bpjs_ks_td = (!empty($bpjs_ks)) ? "" : " style='background: #E07171;'";
                            $bpjs_jkk_td = (!empty($bpjs_jkk)) ? "" : " style='background: #E07171;'";
                            $bpjs_ijht_td = (!empty($bpjs_ijht)) ? "" : " style='background: #E07171;'";
                            $bpjs_jp_td = (!empty($bpjs_jp)) ? "" : " style='background: #E07171;'";
                            $foto_td = (!empty($foto)) ? "" : " style='background: #E07171;'";
                            $status_kepegawaian_td = (!empty($status_kepegawaian)) ? "" : " style='background: #E07171;'";
                            $sub_bagian_td = (!empty($sub_bagian)) ? "" : " style='background: #E07171;'";
                            $log_finger_td = (!empty($log_finger)) ? "" : " style='background: #E07171;'";
                            $aktif_td = (!empty($aktif)) ? "" : " style='background: #E07171;'";
                            $id_level_td = (!empty($id_level)) ? "" : " style='background: #E07171;'";
                            $id_kasatpel_td = (!empty($id_kasatpel)) ? "" : " style='background: #E07171;'";
                            // Jika salah satu data ada yang kosong
                            if (empty($id_user) && empty($nip) && empty($nik) && empty($nama) && empty($tempat_lahir) && empty($tgl_lahir) && empty($jk) && empty($alamat) && empty($npwp) && empty($norek) && empty($pendidikan) && empty($tmt) && empty($status_nikah) && empty($rumpun) && empty($pajak) && empty($id_unit) && empty($bpjs_ks) && empty($bpjs_jkk) && empty($bpjs_jp) && empty($foto) && empty($status_kepegawaian) && empty($sub_bagian) && empty($log_finger) && empty($aktif) && empty($id_level) && empty($id_kasatpel)) {
                                $kosong++; // Tambah 1 variabel $kosong
                            }
                            echo "<tr>";
                            echo "<td" . $id_unit_td . ">" . $id_user . "</td>";
                            echo "<td" . $nip_td . ">" . $nip . "</td>";
                            echo "<td" . $nama_td . ">" . $nama . "</td>";
                            echo "<td" . $tempat_lahir_td . ">" . $tempat_lahir . "</td>";
                            echo "<td" . $tgl_lahir_td . ">" . $tgl_lahir . "</td>";
                            echo "<td" . $jk_td . ">" . $jk . "</td>";
                            echo "<td" . $alamat_td . ">" . $alamat . "</td>";
                            echo "<td" . $npwp_td . ">" . $npwp . "</td>";
                            echo "<td" . $norek_td . ">" . $norek . "</td>";
                            echo "<td" . $pendidikan_td . ">" . $pendidikan . "</td>";
                            echo "<td" . $tmt_td . ">" . $tmt . "</td>";
                            echo "<td" . $status_nikah_td . ">" . $status_nikah . "</td>";
                            echo "<td" . $rumpun_td . ">" . $rumpun . "</td>";
                            echo "<td" . $pajak_td . ">" . $pajak . "</td>";
                            echo "<td" . $id_unit_td . ">" . $id_unit . "</td>";
                            echo "<td" . $bpjs_ks_td . ">" . $bpjs_ks . "</td>";
                            echo "<td" . $bpjs_jkk_td . ">" . $bpjs_jkk . "</td>";
                            echo "<td" . $bpjs_ijht_td . ">" . $bpjs_ijht . "</td>";
                            echo "<td" . $bpjs_jp_td . ">" . $bpjs_jp . "</td>";
                            echo "<td" . $foto_td . ">" . $foto . "</td>";
                            echo "<td" . $status_kepegawaian_td . ">" . $status_kepegawaian . "</td>";
                            echo "<td" . $sub_bagian_td . ">" . $sub_bagian . "</td>";
                            echo "<td" . $log_finger_td . ">" . $log_finger . "</td>";
                            echo "<td" . $aktif_td . ">" . $aktif . "</td>";
                            echo "<td" . $id_level_td . ">" . $id_level . "</td>";
                            echo "<td" . $id_kasatpel_td . ">" . $id_kasatpel . "</td>";
                            echo "</tr>";
                        }

                        $numrow++; // Tambah 1 setiap kali looping
                    }

                    echo "</table> "
                        . "</div>";

                    // Cek apakah variabel kosong lebih dari 1
                    // Jika lebih dari 1, berarti ada data yang masih kosong
                    if ($kosong > 1) {
            ?>
                        <script>
                            $(document).ready(function() {
                                // Ubah isi dari tag span dengan id jumlah_kosong dengan isi dari variabel kosong
                                $("#jumlah_kosong").html('<?php echo $kosong; ?>');
                                $("#kosong").show(); // Munculkan alert validasi kosong
                            });
                        </script>
            <?php
                    } else { // Jika semua data sudah diisi
                        echo "<hr>";
                        // Buat sebuah tombol untuk mengimport data ke database
                        echo "<div class='form-group'><button type='submit' name='import' class='btn btn-primary'><span class='glyphicon glyphicon-upload'></span> Import</button></div>";
                    }

                    echo "</form></div>";
                } else { // Jika file yang diupload bukan File Excel 2007 (.xlsx)
                    // Munculkan pesan validasi
                    echo "<div class='box-body'><div class='alert alert-danger'>
					Hanya File Excel 2007 (.xlsx) yang diperbolehkan
					</div></div>";
                }
            }
            ?>

            <div class="box-body table-responsive">
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama Pegawai</th>
                            <th>Tanggal Masuk</th>
                            <th>Unit</th>
                            <th>Pendidikan</th>
                            <th>Status Nikah</th>
                            <th>Rumpun</th>
                            <th>Jabatan</th>
                            <th>Kepegawaian</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 0;
                        $list_pegawai = bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,tm_pegawai.status_pegawai,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level 
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level");
                        while ($row = fetch_array($list_pegawai)) {
                            $no++;
                        ?>
                            <tr>
                                <td><?php echo $no; ?></td>
                                <td><?php echo $row['nip']; ?></td>
                                <td><?php echo $row['nama_pegawai']; ?></td>
                                <td><?php echo konversiTanggal($row['tgl_masuk']); ?></td>
                                <td><?php echo $row['nama_unit']; ?></td>
                                <td><?php echo $row['pendidikan']; ?></td>
                                <td><?php echo ucwords(strtolower($row['status_nikah'])); ?></td>
                                <td><?php echo $row['rumpun']; ?></td>
                                <td><?php echo $row['nama_level']; ?></td>
                                <td><?php echo $row['status_pegawai']; ?></td>
                                <td>
                                    <?php if ($row['status'] == 'AKTIF') { ?>
                                        <span class="fa fa-check" title="Pegawai Aktif"> AKTIF</span>
                                    <?php } else { ?>
                                        <span class="fa fa-close" title="Pegawai NON Aktif"> NON-AKTIF</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php
        break;

    case "list-master-data-pegawai-pns":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PEGAWAI PNS</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-pegawai-pns">
                    Buat Pegawai PNS
                </button>
                <p></p>


                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-pegawai-pns">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Tambah Data Pegawai PNS</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-pegawai-pns'); ?>" method="post">
                                <div class="modal-body">
                                    <div class="modal-body">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">NIP</label>
                                                <input type="number" name="nip" class="form-control" placeholder="Masukan NIP" required>
                                                <label class="control-label">Nama Pegawai</label>
                                                <input type="text" name="nama_pegawai" class="form-control" placeholder="Nama lengkap" required>
                                                <label class="control-label">Tanggak Masuk</label>
                                                <input type="text" class="form-control" id="datepicker" name="tgl_masuk" placeholder="dd/mm/yyyy" required>
                                                <label class="control-label">Unit/Bagian</label>
                                                <select class="form-control select2" name="id_unit" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Unit/Bagian-</option>
                                                    <?php
                                                    $tm_unit = bukaquery("select tm_unit.id_unit,tm_unit.nama_unit from tm_unit");
                                                    while ($row = fetch_array($tm_unit)) {
                                                        echo "<option value=" . $row['id_unit'] . ">" . $row['nama_unit'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label class="control-label">Sub Bagian<font color="red">*</font></label>
                                                <?php echo enumDropdown("tm_pegawai", "sub_bagian", "", "-Pilih Sub Bagian-"); ?>
                                                <label class="control-label">Kasatpel<font color="red">*</font></label>
                                                <select class="form-control select2" name="id_kasatpel" data-placeholder="-Pilih Kasatpel-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih kasatpel-</option>
                                                    <?php
                                                    $tm_kasatpel = bukaquery("select tm_kasatpel.id_kasatpel,tm_kasatpel.nama_kasatpel from tm_kasatpel");
                                                    while ($row = fetch_array($tm_kasatpel)) {
                                                        echo "<option value=" . $row['id_kasatpel'] . ">" . $row['nama_kasatpel'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label class="control-label">Level Petugas<font color="red">*</font></label>
                                                <select class="form-control select2" name="id_level" data-placeholder="-Level Petugas-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Level Petugas-</option>
                                                    <?php
                                                    $tm_unit = bukaquery("select tm_level.id_level,tm_level.nama_level from tm_level");
                                                    while ($row = fetch_array($tm_unit)) {
                                                        echo "<option value=" . $row['id_level'] . ">" . $row['nama_level'] . "</option>";
                                                    }
                                                    ?>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="control-label">Status Nikah</label>
                                                <?php echo enumDropdown("tm_pegawai", "status_nikah", "", "-Pilih Status Nikah-"); ?>
                                                <label class="control-label">PTKP</label>
                                                <?php echo enumDropdown("tm_pegawai", "pajak", "", "-Pilih Pajak-"); ?>
                                                <label class="control-label">Pendidikan</label>
                                                <?php echo enumDropdown("tm_pegawai", "pendidikan", "", "-Pilih Pendidikan-"); ?>
                                                <label class="control-label">Rumpun</label>
                                                <?php echo enumDropdown("tm_pegawai", "rumpun", "", "-Pilih Rumpun-"); ?>
                                                <label class="control-label">Acc Log Finger</label>
                                                <input type="text" name="log_finger" class="form-control" placeholder="Masukan Log finger Absensi" required>
                                                <label class="control-label">BPJS Kesehatan</label><br>
                                                <input type="checkbox" name="bpjs_ks" Value="0.01" class="flat"> BPJS Kesehatan <br>
                                                <label class="control-label">BPJS Ketenagakerjaan</label><br>
                                                <input type="checkbox" name="bpjs_jkk" Value="0.0054" class="flat"> JKK & JKM &nbsp;
                                                <input type="checkbox" name="bpjs_ijht" Value="0.057" class="flat"> IJHT &nbsp;
                                                <input type="checkbox" name="bpjs_jp" Value="0.03" class="flat"> JP &nbsp;<br>
                                                <label class="control-label">Status Karyawan</label><br>
                                                <input type="checkbox" name="status" value="AKTIF" class="js-switch" checked /> AKTIF
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Simpan">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Tutup Modal Skp -->
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Tanggal Masuk</th>
                                <th>Unit</th>
                                <th>Pendidikan</th>
                                <th>Status Nikah</th>
                                <th>Rumpun</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $list_pegawai = bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel  
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.status_pegawai='PNS' and status = 'AKTIF' order by tm_pegawai.nama_pegawai asc");
                            while ($row = fetch_array($list_pegawai)) {
                                $no++;
                            ?>
                                <!-- Edit Modal SKP -->
                                <div class="modal fade" id="modal-reset-<?php echo $row['id_user']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=reset-master-pegawai-pns&id=' . $row['id_user'] . ''); ?>" method="POST" role="form">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Reset Password</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label>Yakin Anda ingin Mereset Password Atas nama <b><?php echo $row['nama_pegawai']; ?></b>,klik Tombol Ya? Untuk Mereset Password</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                    <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Tutup Edit Modal permintaan -->
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $row['nip']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <td><?php echo konversiTanggal($row['tgl_masuk']); ?></td>
                                    <td><?php echo $row['nama_unit']; ?></td>
                                    <td><?php echo $row['pendidikan']; ?></td>
                                    <td><?php echo ucwords(strtolower($row['status_nikah'])); ?></td>
                                    <td><?php echo $row['rumpun']; ?></td>
                                    <td><?php echo $row['nama_level']; ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'AKTIF') { echo '<i class="btn-xs btn-success fa fa-check"> Aktif</i>';
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-times"> Non-Aktif</i>';
                                                                } ?>
                                    </td>
                                    <td>
                                        <a href="?<?php echo paramEncrypt('module=master-data&act=update-data-pegawai-pns&id=' . $row['id_user']); ?>">
                                            <i title="update Pegawai" class="btn-xs btn-warning fa fa-edit"></i>
                                        </a>
                                        <?php if ($superuser != '') { ?>
                                            <span data-toggle="modal" data-target="#modal-reset-<?php echo $row['id_user']; ?>" title="Reset Pass" class="btn-xs btn-danger fa fa-refresh"> Reset Pass</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        break;

    case "list-master-data-pegawai-non-pns":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PEGAWAI NON PNS</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-pegawai-non-pns">
                    Buat Pegawai Non PNS
                </button>
                <p></p>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-pegawai-non-pns">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Tambah Data Pegawai Non PNS</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-pegawai-non-pns'); ?>" method="post">
                                <div class="modal-body">
                                    <div class="modal-body">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">NIP</label>
                                                <input type="number" name="nip" class="form-control" placeholder="Masukan NIP" required>
                                                <label class="control-label">Nama Pegawai</label>
                                                <input type="text" name="nama_pegawai" class="form-control" placeholder="Nama Lengkap" required>
                                                <label class="control-label">Tanggak Masuk</label>
                                                <input type="text" class="form-control" id="datepicker" name="tgl_masuk" placeholder="dd/mm/yyyy" required>
                                                <label class="control-label">Unit/Bagian</label>
                                                <select class="form-control select2" name="id_unit" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Unit/Bagian-</option>
                                                    <?php
                                                    $tm_unit = bukaquery("select tm_unit.id_unit,tm_unit.nama_unit from tm_unit");
                                                    while ($row = fetch_array($tm_unit)) {
                                                        echo "<option value=" . $row['id_unit'] . ">" . $row['nama_unit'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label class="control-label">Sub Bagian<font color="red">*</font></label>
                                                <?php echo enumDropdown("tm_pegawai", "sub_bagian", "", "-Pilih Sub Bagian-"); ?>
                                                <label class="control-label">Kasatpel<font color="red">*</font></label>
                                                <select class="form-control select2" name="id_kasatpel" data-placeholder="-Pilih Kasatpel-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Kasatpel-</option>
                                                    <?php
                                                    $tm_kasatpel = bukaquery("select tm_kasatpel.id_kasatpel,tm_kasatpel.nama_kasatpel from tm_kasatpel");
                                                    while ($row = fetch_array($tm_kasatpel)) {
                                                        echo "<option value=" . $row['id_kasatpel'] . ">" . $row['nama_kasatpel'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label class="control-label">Level Petugas<font color="red">*</font></label>
                                                <select class="form-control select2" name="id_level" data-placeholder="-Level Petugas-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Level Petugas-</option>
                                                    <?php
                                                    $tm_unit = bukaquery("select tm_level.id_level,tm_level.nama_level from tm_level");
                                                    while ($row = fetch_array($tm_unit)) {
                                                        echo "<option value=" . $row['id_level'] . ">" . $row['nama_level'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="control-label">Status Nikah</label>
                                                <?php echo enumDropdown("tm_pegawai", "status_nikah", "", "-Pilih Status Nikah-"); ?>
                                                <label class="control-label">PTKP</label>
                                                <?php echo enumDropdown("tm_pegawai", "pajak", "", "-Pilih Pajak-"); ?>
                                                <label class="control-label">Pendidikan</label>
                                                <?php echo enumDropdown("tm_pegawai", "pendidikan", "", "-Pilih Pendidikan-"); ?>
                                                <label class="control-label">Rumpun</label>
                                                <?php echo enumDropdown("tm_pegawai", "rumpun", "", "-Pilih Rumpun-"); ?>
                                                <label class="control-label">Acc Log Finger</label>
                                                <input type="text" name="log_finger" class="form-control" placeholder="Masukan Log finger Absensi" required>
                                                <label class="control-label">BPJS Kesehatan</label><br>
                                                <input type="checkbox" name="bpjs_ks" Value="0.01" class="flat"> BPJS Kesehatan <br>
                                                <label class="control-label">BPJS Ketenagakerjaan</label><br>
                                                <input type="checkbox" name="bpjs_jkk" Value="0.0054" class="flat"> JKK & JKM &nbsp;
                                                <input type="checkbox" name="bpjs_ijht" Value="0.057" class="flat"> IJHT &nbsp;
                                                <input type="checkbox" name="bpjs_jp" Value="0.03" class="flat"> JP &nbsp;<br>
                                                <label class="control-label">Status Karyawan</label><br>
                                                <input type="checkbox" name="status" value="AKTIF" class="js-switch" checked /> AKTIF
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Simpan">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Tutup Modal Skp -->
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Tanggal Masuk</th>
                                <th>Unit</th>
                                <th>Pendidikan</th>
                                <th>Status Nikah</th>
                                <th>Rumpun</th>
                                <th>Jabatan</th>
                                <th>Agama</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $list_pegawai = bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel, tm_pegawai.agama
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.status_pegawai='NON PNS'  and status = 'AKTIF' order by tm_pegawai.nama_pegawai asc");
                            while ($row = fetch_array($list_pegawai)) {
                                $no++;
                            ?>
                                <!-- Edit Modal SKP -->
                                <div class="modal fade" id="modal-reset-<?php echo $row['id_user']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=reset-master-pegawai-non-pns&id=' . $row['id_user'] . ''); ?>" method="POST" role="form">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Reset Password</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label>Yakin Anda ingin Mereset Password Atas nama <b><?php echo $row['nama_pegawai']; ?></b>,klik Tombol Ya? Untuk Mereset Password</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                    <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Tutup Edit Modal permintaan -->
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $row['nip']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <td><?php echo konversiTanggal($row['tgl_masuk']); ?></td>
                                    <td><?php echo $row['nama_unit']; ?></td>
                                    <td><?php echo $row['pendidikan']; ?></td>
                                    <td><?php echo ucwords(strtolower($row['status_nikah'])); ?></td>
                                    <td><?php echo $row['rumpun']; ?></td>
                                    <td><?php echo $row['nama_level']; ?></td>
                                    <td><?php echo $row['agama']; ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'AKTIF') { echo '<i class="btn-xs btn-success fa fa-check"> Aktif</i>';
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-times"> Non-Aktif</i>';
                                                                } ?>
                                    </td>
                                    <td>
                                        <a href="?<?php echo paramEncrypt('module=master-data&act=update-data-pegawai-non-pns&id=' . $row['id_user']); ?>">
                                            <i title="update Pegawai" class="btn-xs btn-warning fa fa-edit"></i>
                                        </a>
                                        <a href="?<?php echo paramEncrypt('module=simpeg&act=profile-pegawai&id=' . $row['id_user'] . '') ?>"><span title="Profil" class="btn-xs btn-info fa fa-user"></span></a>
                                        <?php if ($superuser != '') { ?>
                                            <span data-toggle="modal" data-target="#modal-reset-<?php echo $row['id_user']; ?>" title="Reset Pass" class="btn-xs btn-danger fa fa-refresh"> Reset Pass</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        break;

    case "list-master-data-pegawai-pjlp":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PEGAWAI PJLP</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-pegawai-pjlp">
                    Buat Pegawai PJLP
                </button>
                <p></p>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-pegawai-pjlp">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Tambah Data Pegawai PJLP</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-pegawai-pjlp'); ?>" method="post">
                                <div class="modal-body">
                                    <div class="modal-body">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">NIP</label>
                                                <input type="number" name="nip" class="form-control" placeholder="Masukan NIP" required>
                                                <label class="control-label">Nama Pegawai</label>
                                                <input type="text" name="nama_pegawai" class="form-control" placeholder="Nama Lengkap" required>
                                                <label class="control-label">Tanggak Masuk</label>
                                                <input type="text" class="form-control" id="datepicker" name="tgl_masuk" placeholder="dd/mm/yyyy" required>
                                                <label class="control-label">Unit/Bagian</label>
                                                <select class="form-control select2" name="id_unit" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Unit/Bagian-</option>
                                                    <?php
                                                    $tm_unit = bukaquery("select tm_unit.id_unit,tm_unit.nama_unit from tm_unit");
                                                    while ($row = fetch_array($tm_unit)) {
                                                        echo "<option value=" . $row['id_unit'] . ">" . $row['nama_unit'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label class="control-label">Sub Bagian<font color="red">*</font></label>
                                                <?php echo enumDropdown("tm_pegawai", "sub_bagian", "", "-Pilih Sub Bagian-"); ?>
                                                <label class="control-label">Kasatpel<font color="red">*</font></label>
                                                <select class="form-control select2" name="id_kasatpel" data-placeholder="-Pilih Kasatpel-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Kasatpel-</option>
                                                    <?php
                                                    $tm_kasatpel = bukaquery("select tm_kasatpel.id_kasatpel,tm_kasatpel.nama_kasatpel from tm_kasatpel");
                                                    while ($row = fetch_array($tm_kasatpel)) {
                                                        echo "<option value=" . $row['id_kasatpel'] . ">" . $row['nama_kasatpel'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label class="control-label">Level Petugas<font color="red">*</font></label>
                                                <select class="form-control select2" name="id_level" data-placeholder="-Level Petugas-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Level Petugas-</option>
                                                    <?php
                                                    $tm_unit = bukaquery("select tm_level.id_level,tm_level.nama_level from tm_level");
                                                    while ($row = fetch_array($tm_unit)) {
                                                        echo "<option value=" . $row['id_level'] . ">" . $row['nama_level'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="control-label">Status Nikah</label>
                                                <?php echo enumDropdown("tm_pegawai", "status_nikah", "", "-Pilih Status Nikah-"); ?>
                                                <label class="control-label">PTKP</label>
                                                <?php echo enumDropdown("tm_pegawai", "pajak", "", "-Pilih Pajak-"); ?>
                                                <label class="control-label">Pendidikan</label>
                                                <?php echo enumDropdown("tm_pegawai", "pendidikan", "", "-Pilih Pendidikan-"); ?>
                                                <label class="control-label">Rumpun</label>
                                                <?php echo enumDropdown("tm_pegawai", "rumpun", "", "-Pilih Rumpun-"); ?>
                                                <label class="control-label">Acc Log Finger</label>
                                                <input type="text" name="log_finger" class="form-control" placeholder="Masukan Log finger Absensi" required>
                                                <label class="control-label">BPJS Kesehatan</label><br>
                                                <input type="checkbox" name="bpjs_ks" Value="0.01" class="flat"> BPJS Kesehatan <br>
                                                <label class="control-label">BPJS Ketenagakerjaan</label><br>
                                                <input type="checkbox" name="bpjs_jkk" Value="0" class="flat"> JKK & JKM &nbsp;
                                                <input type="checkbox" name="bpjs_ijht" Value="0" class="flat"> IJHT &nbsp;
                                                <input type="checkbox" name="bpjs_jp" Value="0" class="flat"> JP &nbsp;<br>
                                                <label class="control-label">Status Karyawan</label><br>
                                                <input type="checkbox" name="status" value="AKTIF" class="js-switch" checked /> AKTIF
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Simpan">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Tutup Modal Skp -->
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Tanggal Masuk</th>
                                <th>Unit</th>
                                <th>Pendidikan</th>
                                <th>Status Nikah</th>
                                <th>Rumpun</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $list_pegawai = bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
                                                    tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                                    tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel
                                                    FROM
                                                    tm_pegawai
                                                    INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                                    INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                                    INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                                    where tm_pegawai.status_pegawai='PJLP' and status = 'AKTIF' order by tm_pegawai.nama_pegawai asc");
                            while ($row = fetch_array($list_pegawai)) {
                                $no++;
                            ?>
                                <!-- Edit Modal SKP -->
                                <div class="modal fade" id="modal-reset-<?php echo $row['id_user']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=reset-master-pegawai-non-pjlp&id=' . $row['id_user'] . ''); ?>" method="POST" role="form">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Reset Password</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label>Yakin Anda ingin Mereset Password Atas nama <b><?php echo $row['nama_pegawai']; ?></b>,klik Tombol Ya? Untuk Mereset Password</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                    <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Tutup Edit Modal permintaan -->
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $row['nip']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <td><?php echo konversiTanggal($row['tgl_masuk']); ?></td>
                                    <td><?php echo $row['nama_unit']; ?></td>
                                    <td><?php echo $row['pendidikan']; ?></td>
                                    <td><?php echo ucwords(strtolower($row['status_nikah'])); ?></td>
                                    <td><?php echo $row['rumpun']; ?></td>
                                    <td><?php echo $row['nama_level']; ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'AKTIF') { echo '<i class="btn-xs btn-success fa fa-check"> Aktif</i>';
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-times"> Non-Aktif</i>';
                                                                } ?>
                                    </td>
                                    <td>
                                        <a href="?<?php echo paramEncrypt('module=master-data&act=update-data-pegawai-pjlp&id=' . $row['id_user']); ?>">
                                            <i title="update Pegawai" class="btn-xs btn-warning fa fa-edit"></i>
                                        </a>
                                        <a href="?<?php echo paramEncrypt('module=simpeg&act=profile-pegawai&id=' . $row['id_user'] . '') ?>"><span title="Profil" class="btn-xs btn-info fa fa-user"></span></a>
                                        <?php if ($superuser != '') { ?>
                                            <span data-toggle="modal" data-target="#modal-reset-<?php echo $row['id_user']; ?>" title="Reset Pass" class="btn-xs btn-danger fa fa-refresh"> Reset Pass</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        break;


case "list-master-data-pegawai-spesialis":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PEGAWAI SPESIALIS</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-pegawai-spesialis">
                    Buat Pegawai Spesialis
                </button>
                <p></p>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-pegawai-spesialis">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Tambah Data Pegawai SPESIALIS</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-pegawai-spesialis'); ?>" method="post">
                                <div class="modal-body">
                                    <div class="modal-body">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">NIP</label>
                                                <input type="number" name="nip" class="form-control" placeholder="Masukan NIP" required>
                                                <label class="control-label">Nama Pegawai</label>
                                                <input type="text" name="nama_pegawai" class="form-control" placeholder="Nama Lengkap" required>
                                                <label class="control-label">Tanggak Masuk</label>
                                                <input type="text" class="form-control" id="datepicker" name="tgl_masuk" placeholder="dd/mm/yyyy" required>
                                                <label class="control-label">Unit/Bagian</label>
                                                <select class="form-control select2" name="id_unit" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Unit/Bagian-</option>
                                                    <?php
                                                    $tm_unit = bukaquery("select tm_unit.id_unit,tm_unit.nama_unit from tm_unit");
                                                    while ($row = fetch_array($tm_unit)) {
                                                        echo "<option value=" . $row['id_unit'] . ">" . $row['nama_unit'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label class="control-label">Sub Bagian<font color="red">*</font></label>
                                                <?php echo enumDropdown("tm_pegawai", "sub_bagian", "", "-Pilih Sub Bagian-"); ?>
                                                <label class="control-label">Kasatpel<font color="red">*</font></label>
                                                <select class="form-control select2" name="id_kasatpel" data-placeholder="-Pilih Kasatpel-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Kasatpel-</option>
                                                    <?php
                                                    $tm_kasatpel = bukaquery("select tm_kasatpel.id_kasatpel,tm_kasatpel.nama_kasatpel from tm_kasatpel");
                                                    while ($row = fetch_array($tm_kasatpel)) {
                                                        echo "<option value=" . $row['id_kasatpel'] . ">" . $row['nama_kasatpel'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label class="control-label">Level Petugas<font color="red">*</font></label>
                                                <select class="form-control select2" name="id_level" data-placeholder="-Level Petugas-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Level Petugas-</option>
                                                    <?php
                                                    $tm_unit = bukaquery("select tm_level.id_level,tm_level.nama_level from tm_level");
                                                    while ($row = fetch_array($tm_unit)) {
                                                        echo "<option value=" . $row['id_level'] . ">" . $row['nama_level'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="control-label">Status Nikah</label>
                                                <?php echo enumDropdown("tm_pegawai", "status_nikah", "", "-Pilih Status Nikah-"); ?>
                                                <label class="control-label">PTKP</label>
                                                <?php echo enumDropdown("tm_pegawai", "pajak", "", "-Pilih Pajak-"); ?>
                                                <label class="control-label">Pendidikan</label>
                                                <?php echo enumDropdown("tm_pegawai", "pendidikan", "", "-Pilih Pendidikan-"); ?>
                                                <label class="control-label">Rumpun</label>
                                                <?php echo enumDropdown("tm_pegawai", "rumpun", "", "-Pilih Rumpun-"); ?>
                                                <label class="control-label">Acc Log Finger</label>
                                                <input type="text" name="log_finger" class="form-control" placeholder="Masukan Log finger Absensi" required>
                                                <label class="control-label">BPJS Kesehatan</label><br>
                                                <input type="checkbox" name="bpjs_ks" Value="0.01" class="flat"> BPJS Kesehatan <br>
                                                <label class="control-label">BPJS Ketenagakerjaan</label><br>
                                                <input type="checkbox" name="bpjs_jkk" Value="0.0054" class="flat"> JKK & JKM &nbsp;
                                                <input type="checkbox" name="bpjs_ijht" Value="0.057" class="flat"> IJHT &nbsp;
                                                <input type="checkbox" name="bpjs_jp" Value="0.03" class="flat"> JP &nbsp;<br>
                                                <label class="control-label">Status Karyawan</label><br>
                                                <input type="checkbox" name="status" value="AKTIF" class="js-switch" checked /> AKTIF
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Simpan">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Tutup Modal Skp -->
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Tanggal Masuk</th>
                                <th>Unit</th>
                                <th>Pendidikan</th>
                                <th>Status Nikah</th>
                                <th>Rumpun</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $list_pegawai = bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.status_pegawai='SPESIALIS'  and status = 'AKTIF' order by tm_pegawai.nama_pegawai asc");
                            while ($row = fetch_array($list_pegawai)) {
                                $no++;
                            ?>
                                <!-- Edit Modal SKP -->
                                <div class="modal fade" id="modal-reset-<?php echo $row['id_user']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=reset-master-pegawai-spesialis&id=' . $row['id_user'] . ''); ?>" method="POST" role="form">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Reset Password</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label>Yakin Anda ingin Mereset Password Atas nama <b><?php echo $row['nama_pegawai']; ?></b>,klik Tombol Ya? Untuk Mereset Password</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                    <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Tutup Edit Modal permintaan -->
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $row['nip']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <td><?php echo konversiTanggal($row['tgl_masuk']); ?></td>
                                    <td><?php echo $row['nama_unit']; ?></td>
                                    <td><?php echo $row['pendidikan']; ?></td>
                                    <td><?php echo ucwords(strtolower($row['status_nikah'])); ?></td>
                                    <td><?php echo $row['rumpun']; ?></td>
                                    <td><?php echo $row['nama_level']; ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'AKTIF') { echo '<i class="btn-xs btn-success fa fa-check"> Aktif</i>';
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-times"> Non-Aktif</i>';
                                                                } ?>
                                    </td>
                                    <td>
                                        <a href="?<?php echo paramEncrypt('module=master-data&act=update-data-pegawai-non-pns&id=' . $row['id_user']); ?>">
                                            <i title="update Pegawai" class="btn-xs btn-warning fa fa-edit"></i>
                                        </a>
                                        <a href="?<?php echo paramEncrypt('module=simpeg&act=profile-pegawai&id=' . $row['id_user'] . '') ?>"><span title="Profil" class="btn-xs btn-info fa fa-user"></span></a>
                                        <?php if ($superuser != '') { ?>
                                            <span data-toggle="modal" data-target="#modal-reset-<?php echo $row['id_user']; ?>" title="Reset Pass" class="btn-xs btn-danger fa fa-refresh"> Reset Pass</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        break;
        
    case "list-master-data-pegawai-isip":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PEGAWAI INTERNSHIP</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-pegawai-isip">
                    Buat Pegawai Internship
                </button>
                <p></p>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-pegawai-isip">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Tambah Data Pegawai Internship</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-pegawai-isip'); ?>" method="post">
                                <div class="modal-body">
                                    <div class="modal-body">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">NIP</label>
                                                <input type="number" name="nip" class="form-control" placeholder="Masukan NIP" required>
                                                <label class="control-label">Nama Pegawai</label>
                                                <input type="text" name="nama_pegawai" class="form-control" placeholder="Nama Lengkap" required>
                                                <label class="control-label">Tanggak Masuk</label>
                                                <input type="text" class="form-control" id="datepicker" name="tgl_masuk" placeholder="dd/mm/yyyy" required>
                                                <label class="control-label">Unit/Bagian</label>
                                                <select class="form-control select2" name="id_unit" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Unit/Bagian-</option>
                                                    <?php
                                                    $tm_unit = bukaquery("select tm_unit.id_unit,tm_unit.nama_unit from tm_unit");
                                                    while ($row = fetch_array($tm_unit)) {
                                                        echo "<option value=" . $row['id_unit'] . ">" . $row['nama_unit'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label class="control-label">Sub Bagian<font color="red">*</font></label>
                                                <?php echo enumDropdown("tm_pegawai", "sub_bagian", "", "-Pilih Sub Bagian-"); ?>
                                                <label class="control-label">Kasatpel<font color="red">*</font></label>
                                                <select class="form-control select2" name="id_kasatpel" data-placeholder="-Pilih Kasatpel-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Kasatpel-</option>
                                                    <?php
                                                    $tm_kasatpel = bukaquery("select tm_kasatpel.id_kasatpel,tm_kasatpel.nama_kasatpel from tm_kasatpel");
                                                    while ($row = fetch_array($tm_kasatpel)) {
                                                        echo "<option value=" . $row['id_kasatpel'] . ">" . $row['nama_kasatpel'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label class="control-label">Level Petugas<font color="red">*</font></label>
                                                <select class="form-control select2" name="id_level" data-placeholder="-Level Petugas-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Level Petugas-</option>
                                                    <?php
                                                    $tm_unit = bukaquery("select tm_level.id_level,tm_level.nama_level from tm_level");
                                                    while ($row = fetch_array($tm_unit)) {
                                                        echo "<option value=" . $row['id_level'] . ">" . $row['nama_level'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="control-label">Status Nikah</label>
                                                <?php echo enumDropdown("tm_pegawai", "status_nikah", "", "-Pilih Status Nikah-"); ?>
                                                <label class="control-label">PTKP</label>
                                                <?php echo enumDropdown("tm_pegawai", "pajak", "", "-Pilih Pajak-"); ?>
                                                <label class="control-label">Pendidikan</label>
                                                <?php echo enumDropdown("tm_pegawai", "pendidikan", "", "-Pilih Pendidikan-"); ?>
                                                <label class="control-label">Rumpun</label>
                                                <?php echo enumDropdown("tm_pegawai", "rumpun", "", "-Pilih Rumpun-"); ?>
                                                <label class="control-label">Acc Log Finger</label>
                                                <input type="text" name="log_finger" class="form-control" placeholder="Masukan Log finger Absensi" required>
                                                <label class="control-label">BPJS Kesehatan</label><br>
                                                <input type="checkbox" name="bpjs_ks" Value="0.01" class="flat"> BPJS Kesehatan <br>
                                                <label class="control-label">BPJS Ketenagakerjaan</label><br>
                                                <input type="checkbox" name="bpjs_jkk" Value="0.0054" class="flat"> JKK & JKM &nbsp;
                                                <input type="checkbox" name="bpjs_ijht" Value="0.057" class="flat"> IJHT &nbsp;
                                                <input type="checkbox" name="bpjs_jp" Value="0.03" class="flat"> JP &nbsp;<br>
                                                <label class="control-label">Status Karyawan</label><br>
                                                <input type="checkbox" name="status" value="AKTIF" class="js-switch" checked /> AKTIF
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Simpan">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Tutup Modal Skp -->
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Tanggal Masuk</th>
                                <th>Unit</th>
                                <th>Pendidikan</th>
                                <th>Status Nikah</th>
                                <th>Rumpun</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $list_pegawai = bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.status_pegawai='INTERNSHIP'  and status = 'AKTIF' order by tm_pegawai.nama_pegawai asc");
                            while ($row = fetch_array($list_pegawai)) {
                                $no++;
                            ?>
                                <!-- Edit Modal SKP -->
                                <div class="modal fade" id="modal-reset-<?php echo $row['id_user']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=reset-master-pegawai-isip&id=' . $row['id_user'] . ''); ?>" method="POST" role="form">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Reset Password</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label>Yakin Anda ingin Mereset Password Atas nama <b><?php echo $row['nama_pegawai']; ?></b>,klik Tombol Ya? Untuk Mereset Password</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                    <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Tutup Edit Modal permintaan -->
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $row['nip']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <td><?php echo konversiTanggal($row['tgl_masuk']); ?></td>
                                    <td><?php echo $row['nama_unit']; ?></td>
                                    <td><?php echo $row['pendidikan']; ?></td>
                                    <td><?php echo ucwords(strtolower($row['status_nikah'])); ?></td>
                                    <td><?php echo $row['rumpun']; ?></td>
                                    <td><?php echo $row['nama_level']; ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'AKTIF') { echo '<i class="btn-xs btn-success fa fa-check"> Aktif</i>';
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-times"> Non-Aktif</i>';
                                                                } ?>
                                    </td>
                                    <td>
                                        <a href="?<?php echo paramEncrypt('module=master-data&act=update-data-pegawai-non-pns&id=' . $row['id_user']); ?>">
                                            <i title="update Pegawai" class="btn-xs btn-warning fa fa-edit"></i>
                                        </a>
                                        <a href="?<?php echo paramEncrypt('module=simpeg&act=profile-pegawai&id=' . $row['id_user'] . '') ?>"><span title="Profil" class="btn-xs btn-info fa fa-user"></span></a>
                                        <?php if ($superuser != '') { ?>
                                            <span data-toggle="modal" data-target="#modal-reset-<?php echo $row['id_user']; ?>" title="Reset Pass" class="btn-xs btn-danger fa fa-refresh"> Reset Pass</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        break;    

    case "list-master-data-pegawai-resign":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PEGAWAI RESIGN</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                
                <!-- Tutup Modal Skp -->
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Tanggal Masuk</th>
                                <th>TMT Resign</th>
                                <th>Unit</th>
                                <th>Pendidikan</th>
                                <th>Status Nikah</th>
                                <th>Rumpun</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $list_pegawai = bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel, tm_pegawai.tmt_resign
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where status = 'NON-AKTIF' order by tm_pegawai.nama_pegawai asc");
                            while ($row = fetch_array($list_pegawai)) {
                                $no++;
                            ?>
                                <!-- Edit Modal SKP -->
                                <div class="modal fade" id="modal-reset-<?php echo $row['id_user']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=reset-master-pegawai-resign&id=' . $row['id_user'] . ''); ?>" method="POST" role="form">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Reset Password</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label>Yakin Anda ingin Mereset Password Atas nama <b><?php echo $row['nama_pegawai']; ?></b>,klik Tombol Ya? Untuk Mereset Password</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                    <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Tutup Edit Modal permintaan -->
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $row['nip']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <td><?php echo konversiTanggal($row['tgl_masuk']); ?></td>
                                    <td><?php echo konversiTanggal($row['tmt_resign']); ?></td>
                                    <td><?php echo $row['nama_unit']; ?></td>
                                    <td><?php echo $row['pendidikan']; ?></td>
                                    <td><?php echo ucwords(strtolower($row['status_nikah'])); ?></td>
                                    <td><?php echo $row['rumpun']; ?></td>
                                    <td><?php echo $row['nama_level']; ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'AKTIF') { echo '<i class="btn-xs btn-success fa fa-check"> Aktif</i>';
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-times"> Non-Aktif</i>';
                                                                } ?>
                                    </td>
                                    <td>
                                        <a href="?<?php echo paramEncrypt('module=master-data&act=update-data-pegawai-resign&id=' . $row['id_user']); ?>">
                                            <i title="update Pegawai" class="btn-xs btn-warning fa fa-edit"></i>
                                        </a>
                                        <a href="?<?php echo paramEncrypt('module=simpeg&act=profile-pegawai&id=' . $row['id_user'] . '') ?>"><span title="Profil" class="btn-xs btn-info fa fa-user"></span></a>
                                        <?php if ($superuser != '') { ?>
                                            <span data-toggle="modal" data-target="#modal-reset-<?php echo $row['id_user']; ?>" title="Reset Pass" class="btn-xs btn-danger fa fa-refresh"> Reset Pass</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        break;

    case "list-master-data-pegawai-resign-pjlp":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PEGAWAI RESIGN</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                
                <!-- Tutup Modal Skp -->
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Tanggal Masuk</th>
                                <th>TMT Resign</th>
                                <th>Unit</th>
                                <th>Pendidikan</th>
                                <th>Status Nikah</th>
                                <th>Rumpun</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $list_pegawai = bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status,tm_pegawai.status_pegawai, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel, tm_pegawai.tmt_resign
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where status = 'NON-AKTIF' and tm_pegawai.status_pegawai = 'PJLP' order by tm_pegawai.nama_pegawai asc");
                            while ($row = fetch_array($list_pegawai)) {
                                $no++;
                            ?>
                                <!-- Edit Modal SKP -->
                                <div class="modal fade" id="modal-reset-<?php echo $row['id_user']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=reset-master-pegawai-resign&id=' . $row['id_user'] . ''); ?>" method="POST" role="form">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Reset Password</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label>Yakin Anda ingin Mereset Password Atas nama <b><?php echo $row['nama_pegawai']; ?></b>,klik Tombol Ya? Untuk Mereset Password</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                    <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Tutup Edit Modal permintaan -->
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $row['nip']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <td><?php echo konversiTanggal($row['tgl_masuk']); ?></td>
                                    <td><?php echo konversiTanggal($row['tmt_resign']); ?></td>
                                    <td><?php echo $row['nama_unit']; ?></td>
                                    <td><?php echo $row['pendidikan']; ?></td>
                                    <td><?php echo ucwords(strtolower($row['status_nikah'])); ?></td>
                                    <td><?php echo $row['rumpun']; ?></td>
                                    <td><?php echo $row['nama_level']; ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'AKTIF') { echo '<i class="btn-xs btn-success fa fa-check"> Aktif</i>';
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-times"> Non-Aktif</i>';
                                                                } ?>
                                    </td>
                                    <td>
                                        <a href="?<?php echo paramEncrypt('module=master-data&act=update-data-pegawai-resign&id=' . $row['id_user']); ?>">
                                            <i title="update Pegawai" class="btn-xs btn-warning fa fa-edit"></i>
                                        </a>
                                        <a href="?<?php echo paramEncrypt('module=simpeg&act=profile-pegawai&id=' . $row['id_user'] . '') ?>"><span title="Profil" class="btn-xs btn-info fa fa-user"></span></a>
                                        <?php if ($superuser != '') { ?>
                                            <span data-toggle="modal" data-target="#modal-reset-<?php echo $row['id_user']; ?>" title="Reset Pass" class="btn-xs btn-danger fa fa-refresh"> Reset Pass</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        break;

    case "update-data-pegawai-pns":
    ?>
        <!--Modal Add waktu -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-user"> UPDATE DATA PEGAWAI PNS</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-8">
                    <?php $pegawai = fetch_array(bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.id_user='$id'")); ?>
                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-pegawai-pns&id=' . $id . ''); ?>" method="post">
                        <div class="modal-body">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">NIP</label>
                                    <input type="text" name="nip" value="<?php echo $pegawai['nip']; ?>" class="form-control" placeholder="Nip" required>
                                    <label class="control-label">Nama Pegawai</label>
                                    <input type="text" name="nama_pegawai" value="<?php echo $pegawai['nama_pegawai']; ?>" class="form-control" placeholder="Nama Pegawai" required>
                                    <label class="control-label">TMT (Tgl/Bln/Thn)</label>
                                    <input type="text" name="tgl_masuk" value="<?php echo FormatTgl('d-m-Y', $pegawai['tgl_masuk']); ?>" class="form-control" data-inputmask='"mask": "99-99-9999","placeholder":"dd-mm-yyyy"' data-mask required>
                                    <label class="control-label">Unit/Bagian <font color="red">*</font></label>
                                    <select class="form-control select2" name="id_unit" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Unit/Bagian-</option>
                                        <?php
                                        $tm_unit = bukaquery("select tm_unit.id_unit,tm_unit.nama_unit from tm_unit ");
                                        while ($unt = fetch_array($tm_unit)) {
                                            if ($pegawai['id_unit'] == $unt['id_unit']) {
                                                echo "<option value=" . $unt['id_unit'] . " selected=" . $pegawai['id_unit'] . ">" . $unt['nama_unit'] . "</option>";
                                            } else {
                                                echo "<option value=" . $unt['id_unit'] . ">" . $unt['nama_unit'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label class="control-label">Sub Bagian <font color="red">*</font></label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "sub_bagian", $pegawai['sub_bagian'], ""); ?>
                                    <label class="control-label">Kasatpel <font color="red">*</font></label>
                                    <select class="form-control select2" name="id_kasatpel" data-placeholder="-Pilih Kasatpel-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Kasatpel-</option>
                                        <?php
                                        $sql_kasatpel = bukaquery("select tm_kasatpel.id_kasatpel,tm_kasatpel.nama_kasatpel from tm_kasatpel ");
                                        while ($lvl = fetch_array($sql_kasatpel)) {
                                            if ($pegawai['id_kasatpel'] == $lvl['id_kasatpel']) {
                                                echo "<option value=" . $lvl['id_kasatpel'] . " selected=" . $pegawai['id_kasatpel'] . ">" . $lvl['nama_kasatpel'] . "</option>";
                                            } else {
                                                echo "<option value=" . $lvl['id_kasatpel'] . ">" . $lvl['nama_kasatpel'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label class="control-label">Level Petugas <font color="red">*</font></label>
                                    <select class="form-control select2" name="id_level" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Level Petugas-</option>
                                        <?php
                                        $tm_level = bukaquery("select tm_level.id_level,tm_level.nama_level from tm_level ");
                                        while ($lvl = fetch_array($tm_level)) {
                                            if ($pegawai['id_level'] == $lvl['id_level']) {
                                                echo "<option value=" . $lvl['id_level'] . " selected=" . $pegawai['id_level'] . ">" . $lvl['nama_level'] . "</option>";
                                            } else {
                                                echo "<option value=" . $lvl['id_level'] . ">" . $lvl['nama_level'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Status Nikah</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "status_nikah", $pegawai['status_nikah'], ""); ?>
                                    <label class="control-label">PTKP</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "pajak", $pegawai['pajak'], ""); ?>
                                    <label class="control-label">Pendidikan</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "pendidikan", $pegawai['pendidikan'], ""); ?>
                                    <label class="control-label">Rumpun</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "rumpun", $pegawai['rumpun'], ""); ?>
                                    <label class="control-label">Acc Log Finger</label>
                                    <input type="text" name="log_finger" class="form-control" value="<?php echo $pegawai['log_finger']; ?>" placeholder="Masukan Log finger Absensi" required>
                                    <label class="control-label">BPJS Kesehatan</label><br>
                                    <input type="checkbox" name="bpjs_ks" Value="0.01" <?php
                                                                                        if ($pegawai['bpjs_ks'] == '0.01') {
                                                                                            echo "checked";
                                                                                        }
                                                                                        ?>> BPJS Kesehatan<br>
                                    <label class="control-label">BPJS Ketenagakerjaan</label><br>
                                    <input type="checkbox" name="bpjs_jkk" Value="0.0054" <?php
                                                                                        if ($pegawai['bpjs_jkk'] == '0.0054') {
                                                                                            echo "checked";
                                                                                        }
                                                                                        ?>> JKK & JKM &nbsp;
                                    <input type="checkbox" name="bpjs_ijht" Value="0.057" <?php
                                                                                            if ($pegawai['bpjs_ijht'] == '0.057') {
                                                                                                echo "checked";
                                                                                            }
                                                                                            ?>> IJHT &nbsp;
                                    <input type="checkbox" name="bpjs_jp" Value="0.03" <?php
                                                                                        if ($pegawai['bpjs_jp'] == '0.03') {
                                                                                            echo "checked";
                                                                                        }
                                                                                        ?>> JP &nbsp;<br>
                                    <label class="control-label">Status Karyawan</label><br>
                                    <input type="checkbox" name="status" class="js-switch" Value="AKTIF" <?php
                                                                                                            if ($pegawai['status'] == 'AKTIF') {
                                                                                                                echo "checked";
                                                                                                            }
                                                                                                            ?>>&nbsp; AKTIF
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"></label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" onClick="history.go(-1);">Close</button>
                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                        </div>
                </div </form> </div> </div> <!-- Tutup Modal waktu -->
            <?php
            break;

    case "update-data-pegawai-non-pns":
    ?>
        <!--Modal Add waktu -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-user"> UPDATE DATA PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-8">
                    <?php $pegawai = fetch_array(bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.id_user='$id'")); ?>
                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-pegawai-non-pns&id=' . $id . ''); ?>" method="post">
                        <div class="modal-body">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">NIP</label>
                                    <input type="text" name="nip" value="<?php echo $pegawai['nip']; ?>" class="form-control" placeholder="Nip" required>
                                    <label class="control-label">Nama Pegawai</label>
                                    <input type="text" name="nama_pegawai" value="<?php echo $pegawai['nama_pegawai']; ?>" class="form-control" placeholder="Nama Pegawai" required>
                                    <label class="control-label">TMT (Tgl/Bln/Thn)</label>
                                    <input type="text" name="tgl_masuk" value="<?php echo FormatTgl('d-m-Y', $pegawai['tgl_masuk']); ?>" class="form-control" data-inputmask='"mask": "99-99-9999","placeholder":"dd-mm-yyyy"' data-mask required>
                                    <label class="control-label">Unit/Bagian <font color="red">*</font></label>
                                    <select class="form-control select2" name="id_unit" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Unit/Bagian-</option>
                                        <?php
                                        $tm_unit = bukaquery("select tm_unit.id_unit,tm_unit.nama_unit from tm_unit ");
                                        while ($unt = fetch_array($tm_unit)) {
                                            if ($pegawai['id_unit'] == $unt['id_unit']) {
                                                echo "<option value=" . $unt['id_unit'] . " selected=" . $pegawai['id_unit'] . ">" . $unt['nama_unit'] . "</option>";
                                            } else {
                                                echo "<option value=" . $unt['id_unit'] . ">" . $unt['nama_unit'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label class="control-label">Sub Bagian <font color="red">*</font></label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "sub_bagian", $pegawai['sub_bagian'], ""); ?>
                                    <label class="control-label">Kasatpel <font color="red">*</font></label>
                                    <select class="form-control select2" name="id_kasatpel" data-placeholder="-Pilih Kasatpel-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Kasatpel-</option>
                                        <?php
                                        $sql_kasatpel = bukaquery("select tm_kasatpel.id_kasatpel,tm_kasatpel.nama_kasatpel from tm_kasatpel ");
                                        while ($lvl = fetch_array($sql_kasatpel)) {
                                            if ($pegawai['id_kasatpel'] == $lvl['id_kasatpel']) {
                                                echo "<option value=" . $lvl['id_kasatpel'] . " selected=" . $pegawai['id_kasatpel'] . ">" . $lvl['nama_kasatpel'] . "</option>";
                                            } else {
                                                echo "<option value=" . $lvl['id_kasatpel'] . ">" . $lvl['nama_kasatpel'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label class="control-label">Level Petugas <font color="red">*</font></label>
                                    <select class="form-control select2" name="id_level" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Level Petugas-</option>
                                        <?php
                                        $tm_level = bukaquery("select tm_level.id_level,tm_level.nama_level from tm_level ");
                                        while ($lvl = fetch_array($tm_level)) {
                                            if ($pegawai['id_level'] == $lvl['id_level']) {
                                                echo "<option value=" . $lvl['id_level'] . " selected=" . $pegawai['id_level'] . ">" . $lvl['nama_level'] . "</option>";
                                            } else {
                                                echo "<option value=" . $lvl['id_level'] . ">" . $lvl['nama_level'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Status Nikah</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "status_nikah", $pegawai['status_nikah'], ""); ?>
                                    <label class="control-label">PTKP</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "pajak", $pegawai['pajak'], ""); ?>
                                    <label class="control-label">Pendidikan</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "pendidikan", $pegawai['pendidikan'], ""); ?>
                                    <label class="control-label">Rumpun</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "rumpun", $pegawai['rumpun'], ""); ?>
                                    <label class="control-label">Acc Log Finger</label>
                                    <input type="text" name="log_finger" class="form-control" value="<?php echo $pegawai['log_finger']; ?>" placeholder="Masukan Log finger Absensi" required>
                                    <label class="control-label">BPJS Kesehatan</label><br>
                                    <input type="checkbox" name="bpjs_ks" Value="0.01" <?php
                                                                                        if ($pegawai['bpjs_ks'] == '0.01') {
                                                                                            echo "checked";
                                                                                        }
                                                                                        ?>> BPJS Kesehatan<br>
                                    <label class="control-label">BPJS Ketenagakerjaan</label><br>
                                    <input type="checkbox" name="bpjs_jkk" Value="0.0054" <?php
                                                                                        if ($pegawai['bpjs_jkk'] == '0.0054') {
                                                                                            echo "checked";
                                                                                        }
                                                                                        ?>> JKK & JKM &nbsp;
                                    <input type="checkbox" name="bpjs_ijht" Value="0.057" <?php
                                                                                            if ($pegawai['bpjs_ijht'] == '0.057') {
                                                                                                echo "checked";
                                                                                            }
                                                                                            ?>> IJHT &nbsp;
                                    <input type="checkbox" name="bpjs_jp" Value="0.03" <?php
                                                                                        if ($pegawai['bpjs_jp'] == '0.03') {
                                                                                            echo "checked";
                                                                                        }
                                                                                        ?>> JP &nbsp;<br>
                                    <label class="control-label">Status Karyawan</label><br>
                                    <input type="checkbox" name="status" class="js-switch" Value="AKTIF" <?php
                                                                                                            if ($pegawai['status'] == 'AKTIF') {
                                                                                                                echo "checked";
                                                                                                            }
                                                                                                            ?>>&nbsp; AKTIF
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"></label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" onClick="history.go(-1);">Close</button>
                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                        </div>
                </div </form> </div> </div> <!-- Tutup Modal waktu -->
            <?php
            break;

        case "update-data-pegawai-pjlp":
    ?>
        <!--Modal Add waktu -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-user"> UPDATE DATA PEGAWAI PJLP</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-8">
                    <?php $pegawai = fetch_array(bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.id_user='$id'")); ?>
                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-pegawai-pjlp&id=' . $id . ''); ?>" method="post">
                        <div class="modal-body">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">NIP</label>
                                    <input type="text" name="nip" value="<?php echo $pegawai['nip']; ?>" class="form-control" placeholder="Nip" required>
                                    <label class="control-label">Nama Pegawai</label>
                                    <input type="text" name="nama_pegawai" value="<?php echo $pegawai['nama_pegawai']; ?>" class="form-control" placeholder="Nama Pegawai" required>
                                    <label class="control-label">TMT (Tgl/Bln/Thn)</label>
                                    <input type="text" name="tgl_masuk" value="<?php echo FormatTgl('d-m-Y', $pegawai['tgl_masuk']); ?>" class="form-control" data-inputmask='"mask": "99-99-9999","placeholder":"dd-mm-yyyy"' data-mask required>
                                    <label class="control-label">Unit/Bagian <font color="red">*</font></label>
                                    <select class="form-control select2" name="id_unit" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Unit/Bagian-</option>
                                        <?php
                                        $tm_unit = bukaquery("select tm_unit.id_unit,tm_unit.nama_unit from tm_unit ");
                                        while ($unt = fetch_array($tm_unit)) {
                                            if ($pegawai['id_unit'] == $unt['id_unit']) {
                                                echo "<option value=" . $unt['id_unit'] . " selected=" . $pegawai['id_unit'] . ">" . $unt['nama_unit'] . "</option>";
                                            } else {
                                                echo "<option value=" . $unt['id_unit'] . ">" . $unt['nama_unit'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label class="control-label">Sub Bagian <font color="red">*</font></label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "sub_bagian", $pegawai['sub_bagian'], ""); ?>
                                    <label class="control-label">Kasatpel <font color="red">*</font></label>
                                    <select class="form-control select2" name="id_kasatpel" data-placeholder="-Pilih Kasatpel-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Kasatpel-</option>
                                        <?php
                                        $sql_kasatpel = bukaquery("select tm_kasatpel.id_kasatpel,tm_kasatpel.nama_kasatpel from tm_kasatpel ");
                                        while ($lvl = fetch_array($sql_kasatpel)) {
                                            if ($pegawai['id_kasatpel'] == $lvl['id_kasatpel']) {
                                                echo "<option value=" . $lvl['id_kasatpel'] . " selected=" . $pegawai['id_kasatpel'] . ">" . $lvl['nama_kasatpel'] . "</option>";
                                            } else {
                                                echo "<option value=" . $lvl['id_kasatpel'] . ">" . $lvl['nama_kasatpel'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label class="control-label">Level Petugas <font color="red">*</font></label>
                                    <select class="form-control select2" name="id_level" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Level Petugas-</option>
                                        <?php
                                        $tm_level = bukaquery("select tm_level.id_level,tm_level.nama_level from tm_level ");
                                        while ($lvl = fetch_array($tm_level)) {
                                            if ($pegawai['id_level'] == $lvl['id_level']) {
                                                echo "<option value=" . $lvl['id_level'] . " selected=" . $pegawai['id_level'] . ">" . $lvl['nama_level'] . "</option>";
                                            } else {
                                                echo "<option value=" . $lvl['id_level'] . ">" . $lvl['nama_level'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Status Nikah</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "status_nikah", $pegawai['status_nikah'], ""); ?>
                                    <label class="control-label">PTKP</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "pajak", $pegawai['pajak'], ""); ?>
                                    <label class="control-label">Pendidikan</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "pendidikan", $pegawai['pendidikan'], ""); ?>
                                    <label class="control-label">Rumpun</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "rumpun", $pegawai['rumpun'], ""); ?>
                                    <label class="control-label">Acc Log Finger</label>
                                    <input type="text" name="log_finger" class="form-control" value="<?php echo $pegawai['log_finger']; ?>" placeholder="Masukan Log finger Absensi" required>
                                    <label class="control-label">BPJS Kesehatan</label><br>
                                    <input type="checkbox" name="bpjs_ks" Value="0.01" <?php
                                                                                        if ($pegawai['bpjs_ks'] == '0.01') {
                                                                                            echo "checked";
                                                                                        }
                                                                                        ?>> BPJS Kesehatan<br>
                                    <label class="control-label">BPJS Ketenagakerjaan</label><br>
                                    <input type="checkbox" name="bpjs_jkk" Value="0" <?php
                                                                                        if ($pegawai['bpjs_jkk'] == '0.0054') {
                                                                                            echo "checked";
                                                                                        }
                                                                                        ?>> JKK & JKM &nbsp;
                                    <input type="checkbox" name="bpjs_ijht" Value="0" <?php
                                                                                            if ($pegawai['bpjs_ijht'] == '0.057') {
                                                                                                echo "checked";
                                                                                            }
                                                                                            ?>> IJHT &nbsp;
                                    <input type="checkbox" name="bpjs_jp" Value="0" <?php
                                                                                        if ($pegawai['bpjs_jp'] == '0.03') {
                                                                                            echo "checked";
                                                                                        }
                                                                                        ?>> JP &nbsp;<br>
                                    <label class="control-label">Status Karyawan</label><br>
                                    <input type="checkbox" name="status" class="js-switch" Value="AKTIF" <?php
                                                                                                            if ($pegawai['status'] == 'AKTIF') {
                                                                                                                echo "checked";
                                                                                                            }
                                                                                                            ?>>&nbsp; AKTIF
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"></label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" onClick="history.go(-1);">Close</button>
                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                        </div>
                </div </form> </div> </div> <!-- Tutup Modal waktu -->
            <?php
            break;

        case "update-data-pegawai-resign":
    ?>
        <!--Modal Add waktu -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-user"> UPDATE DATA PEGAWAI RESIGN</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-8">
                    <?php $pegawai = fetch_array(bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel, tm_pegawai.tmt_resign
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.id_user='$id'")); ?>
                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-pegawai-resign&id=' . $id . ''); ?>" method="post">
                        <div class="modal-body">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">NIP</label>
                                    <input type="text" name="nip" value="<?php echo $pegawai['nip']; ?>" class="form-control" placeholder="Nip" required>
                                    <label class="control-label">Nama Pegawai</label>
                                    <input type="text" name="nama_pegawai" value="<?php echo $pegawai['nama_pegawai']; ?>" class="form-control" placeholder="Nama Pegawai" required>
                                    <label class="control-label">TMT MASUK (Tgl/Bln/Thn)</label>
                                    <input type="text" name="tgl_masuk" value="<?php echo FormatTgl('d-m-Y', $pegawai['tgl_masuk']); ?>" class="form-control" data-inputmask='"mask": "99-99-9999","placeholder":"dd-mm-yyyy"' data-mask required>
                                    <label class="control-label">TMT BERHENTI (Tgl/Bln/Thn)</label>
                                    <input type="text" name="tgl_keluar" value="<?php echo FormatTgl('d-m-Y', $pegawai['tmt_resign']); ?>" class="form-control" data-inputmask='"mask": "99-99-9999","placeholder":"dd-mm-yyyy"' data-mask>
                                    <label class="control-label">Unit/Bagian <font color="red">*</font></label>
                                    <select class="form-control select2" name="id_unit" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Unit/Bagian-</option>
                                        <?php
                                        $tm_unit = bukaquery("select tm_unit.id_unit,tm_unit.nama_unit from tm_unit ");
                                        while ($unt = fetch_array($tm_unit)) {
                                            if ($pegawai['id_unit'] == $unt['id_unit']) {
                                                echo "<option value=" . $unt['id_unit'] . " selected=" . $pegawai['id_unit'] . ">" . $unt['nama_unit'] . "</option>";
                                            } else {
                                                echo "<option value=" . $unt['id_unit'] . ">" . $unt['nama_unit'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label class="control-label">Sub Bagian <font color="red">*</font></label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "sub_bagian", $pegawai['sub_bagian'], ""); ?>
                                    <label class="control-label">Kasatpel <font color="red">*</font></label>
                                    <select class="form-control select2" name="id_kasatpel" data-placeholder="-Pilih Kasatpel-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Kasatpel-</option>
                                        <?php
                                        $sql_kasatpel = bukaquery("select tm_kasatpel.id_kasatpel,tm_kasatpel.nama_kasatpel from tm_kasatpel ");
                                        while ($lvl = fetch_array($sql_kasatpel)) {
                                            if ($pegawai['id_kasatpel'] == $lvl['id_kasatpel']) {
                                                echo "<option value=" . $lvl['id_kasatpel'] . " selected=" . $pegawai['id_kasatpel'] . ">" . $lvl['nama_kasatpel'] . "</option>";
                                            } else {
                                                echo "<option value=" . $lvl['id_kasatpel'] . ">" . $lvl['nama_kasatpel'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label class="control-label">Level Petugas <font color="red">*</font></label>
                                    <select class="form-control select2" name="id_level" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Level Petugas-</option>
                                        <?php
                                        $tm_level = bukaquery("select tm_level.id_level,tm_level.nama_level from tm_level ");
                                        while ($lvl = fetch_array($tm_level)) {
                                            if ($pegawai['id_level'] == $lvl['id_level']) {
                                                echo "<option value=" . $lvl['id_level'] . " selected=" . $pegawai['id_level'] . ">" . $lvl['nama_level'] . "</option>";
                                            } else {
                                                echo "<option value=" . $lvl['id_level'] . ">" . $lvl['nama_level'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Status Nikah</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "status_nikah", $pegawai['status_nikah'], ""); ?>
                                    <label class="control-label">PTKP</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "pajak", $pegawai['pajak'], ""); ?>
                                    <label class="control-label">Pendidikan</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "pendidikan", $pegawai['pendidikan'], ""); ?>
                                    <label class="control-label">Rumpun</label>
                                    <?php echo UpdateEnumDropdown("tm_pegawai", "rumpun", $pegawai['rumpun'], ""); ?>
                                    <label class="control-label">Acc Log Finger</label>
                                    <input type="text" name="log_finger" class="form-control" value="<?php echo $pegawai['log_finger']; ?>" placeholder="Masukan Log finger Absensi" required>
                                    <label class="control-label">BPJS Kesehatan</label><br>
                                    <input type="checkbox" name="bpjs_ks" Value="0.01" <?php
                                                                                        if ($pegawai['bpjs_ks'] == '0.01') {
                                                                                            echo "checked";
                                                                                        }
                                                                                        ?>> BPJS Kesehatan<br>
                                    <label class="control-label">BPJS Ketenagakerjaan</label><br>
                                    <input type="checkbox" name="bpjs_jkk" Value="0.0054" <?php
                                                                                        if ($pegawai['bpjs_jkk'] == '0.0054') {
                                                                                            echo "checked";
                                                                                        }
                                                                                        ?>> JKK & JKM &nbsp;
                                    <input type="checkbox" name="bpjs_ijht" Value="0.057" <?php
                                                                                            if ($pegawai['bpjs_ijht'] == '0.057') {
                                                                                                echo "checked";
                                                                                            }
                                                                                            ?>> IJHT &nbsp;
                                    <input type="checkbox" name="bpjs_jp" Value="0.03" <?php
                                                                                        if ($pegawai['bpjs_jp'] == '0.03') {
                                                                                            echo "checked";
                                                                                        }
                                                                                        ?>> JP &nbsp;<br>
                                    <label class="control-label">Status Karyawan</label><br>
                                    <input type="checkbox" name="status" class="js-switch" Value="AKTIF" <?php
                                                                                                            if ($pegawai['status'] == 'AKTIF') {
                                                                                                                echo "checked";
                                                                                                            }
                                                                                                            ?>>&nbsp; AKTIF
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"></label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" onClick="history.go(-1);">Close</button>
                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                        </div>
                </div </form> </div> </div> <!-- Tutup Modal waktu -->
            <?php
            break;



        case "list-master-data-bagian":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST MASTER DATA BAGIAN</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-bagian">
                            Buat Master Data Bagian
                        </button>
                        <!--Modal Add SKP -->
                        <div class="modal fade" id="modal-add-bagian">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-plus"> Tambah Data Bagian</h4>
                                    </div>
                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-data-bagian'); ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama Bagian/Unit</label>
                                                <input type="text" class="form-control" name="nama_unit" placeholder="Nama Unit/Bagian" required>
                                                <label>kategori Petugas</label>
                                                <select class="form-control select2" name="id_petugas" data-placeholder="-Kategori Petugas-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Kategori Petugas-</option>
                                                    <?php
                                                    $tm_honor_shift = bukaquery("select tm_honor_shift.id_petugas,tm_honor_shift.petugas from tm_honor_shift");
                                                    while ($row = fetch_array($tm_honor_shift)) {
                                                        echo "<option value=" . $row['id_petugas'] . ">" . $row['petugas'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input type="submit" class="btn btn-primary" value="Simpan">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal Skp -->
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Bagian/Unit</th>
                                        <th>Kategori</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $sql_unit = bukaquery("SELECT tm_unit.id_unit,tm_unit.nama_unit, tm_unit.id_petugas, tm_honor_shift.petugas FROM tm_unit INNER JOIN tm_honor_shift ON tm_unit.id_petugas = tm_honor_shift.id_petugas where nama_unit!='-'");
                                    while ($row = fetch_array($sql_unit)) {
                                        $no++;
                                    ?>
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-ubah-<?php echo $row['id_unit']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-data-bagian&id=' . $row['id_unit'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Data Master Bagian/Unit</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Nama bagian/Unit</label>
                                                                    <input type="text" class="form-control" name="nama_unit" value="<?php echo $row['nama_unit']; ?>" placeholder="Nama Bagian/Unit" required>
                                                                    <label>Kategori</label>
                                                                    <select class="form-control select2" name="id_petugas" data-placeholder="-Kategori Petugas-" style="width: 100%;" required>
                                                                        <option selected="selected" value="">-Pilih Kategori Petugas-</option>
                                                                        <?php
                                                                        $tm_honor_shift = bukaquery("select tm_honor_shift.id_petugas,tm_honor_shift.petugas from tm_honor_shift ");
                                                                        while ($shift = fetch_array($tm_honor_shift)) {
                                                                            if ($row['id_petugas'] == $shift['id_petugas']) {
                                                                                echo "<option value=" . $shift['id_petugas'] . " selected=" . $row['id_petugas'] . ">" . $shift['petugas'] . "</option>";
                                                                            } else {
                                                                                echo "<option value=" . $shift['id_petugas'] . ">" . $shift['petugas'] . "</option>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
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
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-hapus-<?php echo $row['id_unit']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=delete-master-data-bagian&id=' . $row['id_unit'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Delete Data Bagian/Unit</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Yakin Anda ingin Menghapus Data ini,klik Tombol Ya? Untuk Menghapusnya</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tutup Edit Modal permintaan -->
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['nama_unit']; ?></td>
                                            <td><?php echo $row['petugas']; ?></td>
                                            <td>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_unit']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_unit']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            break;

        case "list-master-data-kasatpel":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST MASTER DATA KASATPEL</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-kasatpel">
                            Buat Master Data Kasatpel
                        </button>
                        <!--Modal Add SKP -->
                        <div class="modal fade" id="modal-add-kasatpel">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-plus"> Tambah Data Kasatpel</h4>
                                    </div>
                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-data-kasatpel'); ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama Kasatpel</label>
                                                <input type="text" class="form-control" name="nama_kasatpel" placeholder="Nama Kasatpel" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input type="submit" class="btn btn-primary" value="Simpan">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal Skp -->
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kasatpel</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $sql_kasatpel = bukaquery("SELECT * FROM tm_kasatpel where id_kasatpel !='-'");
                                    while ($row = fetch_array($sql_kasatpel)) {
                                        $no++;
                                    ?>
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-ubah-<?php echo $row['id_kasatpel']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-data-kasatpel&id=' . $row['id_kasatpel'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Data Master Kasatpel</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Nama Kasatpel</label>
                                                                    <input type="text" class="form-control" name="nama_kasatpel" value="<?php echo $row['nama_kasatpel']; ?>" placeholder="Nama Kasatpel" required>
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
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-hapus-<?php echo $row['id_kasatpel']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=delete-master-data-kasatpel&id=' . $row['id_kasatpel'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Delete Data Bagian/Unit</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Yakin Anda ingin Menghapus Data ini,klik Tombol Ya? Untuk Menghapusnya</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tutup Edit Modal permintaan -->
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['nama_kasatpel']; ?></td>
                                            <td>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_kasatpel']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_kasatpel']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            break;

        case "list-master-data-shifting":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST MASTER DATA HONOR SHIFTING</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-shifting">
                            Buat Master Data Honor Shift
                        </button>
                        <!--Modal Add SKP -->
                        <div class="modal fade" id="modal-add-shifting">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-plus"> Tambah Data Honor Dhift</h4>
                                    </div>
                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-data-honor-shift'); ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Kategori Shift</label>
                                                <input type="text" class="form-control" name="petugas" placeholder="kategori petugas" required>
                                                <label>Hari Kerja Siang</label>
                                                <input type="number" class="form-control" name="hks" placeholder="Rp" required>
                                                <label>Hari Kerja Malam</label>
                                                <input type="number" class="form-control" name="hkm" placeholder="Rp" required>
                                                <label>Hari Libur pagi</label>
                                                <input type="number" class="form-control" name="hlp" placeholder="Rp" required>
                                                <label>Hari Libur Siang</label>
                                                <input type="number" class="form-control" name="hls" placeholder="Rp" required>
                                                <label>Hari Libur Malam</label>
                                                <input type="number" class="form-control" name="hlm" placeholder="Rp" required>
                                                <label>Hari Raya Pagi</label>
                                                <input type="number" class="form-control" name="hrp" placeholder="Rp" required>
                                                <label>Hari Raya Siang</label>
                                                <input type="number" class="form-control" name="hrs" placeholder="Rp" required>
                                                <label>Hari Raya Malam</label>
                                                <input type="number" class="form-control" name="hrm" placeholder="Rp" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input type="submit" class="btn btn-primary" value="Simpan">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal Skp -->
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kategori</th>
                                        <th>HKS</th>
                                        <th>HKM</th>
                                        <th>HLP</th>
                                        <th>HLS</th>
                                        <th>HLM</th>
                                        <th>HRP</th>
                                        <th>HRS</th>
                                        <th>HRM</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $sql_unit = bukaquery("SELECT * from tm_honor_shift where petugas!='-'");
                                    while ($row = fetch_array($sql_unit)) {
                                        $no++;
                                    ?>
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-ubah-<?php echo $row['id_petugas']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-data-honor-shift&id=' . $row['id_petugas'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Data Master Bagian/Unit</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>kategori Petugas</label>
                                                                    <input type="text" class="form-control" name="petugas" value="<?php echo $row['petugas']; ?>" placeholder="Kategori Petugas" required>
                                                                    <label>Hari Kerja Siang</label>
                                                                    <input type="number" class="form-control" name="hks" value="<?php echo $row['hks']; ?>" placeholder='Rp' required>
                                                                    <label>Hari Kerja Malam</label>
                                                                    <input type="number" class="form-control" name="hkm" value="<?php echo $row['hkm']; ?>" placeholder='Rp' required>
                                                                    <label>Hari Libur Pagi</label>
                                                                    <input type="number" class="form-control" name="hlp" value="<?php echo $row['hlp']; ?>" placeholder='Rp' required>
                                                                    <label>Hari Libur Siang</label>
                                                                    <input type="number" class="form-control" name="hls" value="<?php echo $row['hls']; ?>" placeholder='Rp' required>
                                                                    <label>Hari Libur malam</label>
                                                                    <input type="number" class="form-control" name="hlm" value="<?php echo $row['hlm']; ?>" placeholder='Rp' required>
                                                                    <label>Hari Raya Pagi</label>
                                                                    <input type="number" class="form-control" name="hrp" value="<?php echo $row['hrp']; ?>" placeholder='Rp' required>
                                                                    <label>Hari Raya Sore</label>
                                                                    <input type="number" class="form-control" name="hrs" value="<?php echo $row['hrs']; ?>" placeholder='Rp' required>
                                                                    <label>Hari Raya Malam</label>
                                                                    <input type="number" class="form-control" name="hrm" value="<?php echo $row['hrm']; ?>" placeholder='Rp' required>
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
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-hapus-<?php echo $row['id_petugas']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=delete-master-data-honor-shift&id=' . $row['id_petugas'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Data Bagian/Unit</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Yakin Anda ingin Menghapus Data ini,klik Tombol Ya? Untuk Menghapusnya</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tutup Edit Modal permintaan -->
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['petugas']; ?></td>
                                            <td><?php echo formatDuit($row['hks']); ?></td>
                                            <td><?php echo formatDuit($row['hkm']); ?></td>
                                            <td><?php echo formatDuit($row['hlp']); ?></td>
                                            <td><?php echo formatDuit($row['hls']); ?></td>
                                            <td><?php echo formatDuit($row['hlm']); ?></td>
                                            <td><?php echo formatDuit($row['hrp']); ?></td>
                                            <td><?php echo formatDuit($row['hrs']); ?></td>
                                            <td><?php echo formatDuit($row['hrm']); ?></td>
                                            <td>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_petugas']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_petugas']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            break;

        case "list-master-data-hari-kerja":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST MASTER DATA HARI KERJA PEGAWAI</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-hari-kerja">
                            Buat Master Data Hari Kerja
                        </button>
                        <!--Modal Add SKP -->
                        <div class="modal fade" id="modal-add-hari-kerja">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-plus"> Tambah Data Hari kerja</h4>
                                    </div>
                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-data-hari-kerja'); ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <div class="col-md-4">
                                                    <label>Bulan</label>
                                                    <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" style="width: 100%;" required>
                                                        <?php loadBln('-Pilih Bulan-'); ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Tahun</label>
                                                    <input type="text" class="form-control" name="tahun" placeholder="" value="<?php echo date('Y'); ?>" readonly>
                                                </div>
                                                <div class="col-md-8">
                                                    <p></p>
                                                    <label>Jumlah Hari Kerja</label>
                                                    <input type="number" class="form-control" name="hari" placeholder="" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label"><br><br><br><br><br></label>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input type="submit" class="btn btn-primary" value="Simpan">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal Skp -->
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bulan</th>
                                        <th>Jumlah Hari</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $sql_hari_kerja = bukaquery("select * from tm_hari_kerja order by bulan asc");
                                    while ($row = fetch_array($sql_hari_kerja)) {
                                        $no++;
                                    ?>
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-ubah-<?php echo $row['id_hari_kerja']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-data-hari-kerja&id=' . $row['id_hari_kerja'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Data Hari Kerja</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <div class="col-md-4">
                                                                        <label>Bulan</label>
                                                                        <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" style="width: 100%;" required>
                                                                            <?php updateloadBln(date('m', strtotime($row['bulan']))); ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label>Tahun</label>
                                                                        <input type="text" class="form-control" name="tahun" placeholder="" value="<?php echo date('Y', strtotime($row['bulan'])); ?>" readonly>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <p></p>
                                                                        <label>Jumlah Hari Kerja</label>
                                                                        <input type="number" class="form-control" value="<?php echo $row['hari']; ?>" name="hari" placeholder="Masukan hari kerja" required>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label><br><br><br><br><br><br><br></label>
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
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-hapus-<?php echo $row['id_hari_kerja']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=delete-master-data-hari-kerja&id=' . $row['id_hari_kerja'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Hapus Master Data Hari kerja</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <text>Yakin Anda ingin Menghapus Data Bulan <b><?php echo konversiBulan(date('m', strtotime($row['bulan']))); ?> </b> ini,klik Tombol Ya? Untuk Menghapusnya</text>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tutup Edit Modal permintaan -->
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo konversiBulan(date('m', strtotime($row['bulan']))); ?></td>
                                            <td><?php echo $row['hari'] . " hari"; ?></td>
                                            <td>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_hari_kerja']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_hari_kerja']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            break;

        case "list-master-data-penyerapan":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST MASTER DATA NILAI PENDAPATAN</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if (getOne("select count(id_penyerapan) as total from tm_penyerapan where year(tm_penyerapan.bulan)='$thn'") <= 12) { ?>
                            <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-penyerapan">
                                Buat Master Data Pendapatan
                            </button>
                        <?php
                        } else {
                        }
                        ?>
                        <!--Modal Add SKP -->
                        <div class="modal fade" id="modal-add-penyerapan">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-plus"> Tambah Data Pendapatan</h4>
                                    </div>
                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-data-penyerapan'); ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <div class="col-md-4">
                                                    <label>Bulan</label>
                                                    <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" style="width: 100%;" required>
                                                        <?php loadBln('-Pilih Bulan-'); ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Tahun</label>
                                                    <input type="text" class="form-control" name="tahun" placeholder="" value="<?php echo date('Y'); ?>" readonly>
                                                </div>
                                                <div class="col-md-8">
                                                    <p></p>
                                                    <label>Nilai Pendapatan</label>
                                                    <input type="number" class="form-control" name="penyerapan" placeholder="Angka Penyerapan Desimal" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label"><br><br><br><br><br></label>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input type="submit" class="btn btn-primary" value="Simpan">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal Skp -->
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bulan</th>
                                        <th>Nilai Penyerapan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $sql_penyerapan = bukaquery("select * from tm_penyerapan order by bulan asc");
                                    while ($row = fetch_array($sql_penyerapan)) {
                                        $no++;
                                    ?>
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-ubah-<?php echo $row['id_penyerapan']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-data-penyerapan&id=' . $row['id_penyerapan'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Data Penyerapan</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <div class="col-md-4">
                                                                        <label>Bulan</label>
                                                                        <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" style="width: 100%;" disabled required>
                                                                            <?php updateloadBln(date('m', strtotime($row['bulan']))); ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label>Tahun</label>
                                                                        <input type="text" class="form-control" name="tahun" placeholder="" value="<?php echo date('Y', strtotime($row['bulan'])); ?>" readonly>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <p></p>
                                                                        <label>Nilai Penyerapan</label>
                                                                        <input type="decimal" class="form-control" value="<?php echo $row['penyerapan']; ?>" name="penyerapan" placeholder="Masukan hari kerja" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label"><br><br><br><br><br>
                                                                    <p></p>
                                                                </label>
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
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-hapus-<?php echo $row['id_penyerapan']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=delete-master-data-penyerapan&id=' . $row['id_penyerapan'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Hapus Master Data Penyerapan</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <text>Yakin Anda ingin Menghapus Data Bulan <b><?php echo konversiBulan(date('m', strtotime($row['bulan']))); ?> </b> ini,klik Tombol Ya? Untuk Menghapusnya</text>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tutup Edit Modal permintaan -->
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo konversiBulan(date('m', strtotime($row['bulan']))) . " " . FormatTgl('Y', $row['bulan']); ?></td>
                                            <td><?php echo $row['penyerapan'] . " %"; ?></td>
                                            <td>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_penyerapan']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_penyerapan']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            break;

        case "list-master-data-sanksi":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST MASTER DATA SANKSI</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-sanksi">
                            Buat Master Data Sanksi
                        </button>
                        <!--Modal Add SKP -->
                        <div class="modal fade" id="modal-add-sanksi">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-plus"> Tambah Data Sanksi</h4>
                                    </div>
                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-data-sanksi'); ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <div class="col-md-8">
                                                    <label>Nama Sanksi</label>
                                                    <input type="text" class="form-control" name="nama_sanksi" placeholder="Nama Sanksi" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Masa Aktif Bulan</label>
                                                    <input type="number" class="form-control" name="masa_aktif" placeholder="Angka" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Nilai Sanksi</label>
                                                    <input type="number" class="form-control" name="nilai_sanksi" placeholder="Angka" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input type="submit" class="btn btn-primary" value="Simpan">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal Skp -->
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Sanksi</th>
                                        <th>Masa Aktif</th>
                                        <th>Nilai Sanksi</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $sql_sanksi = bukaquery("select * from tm_sanksi order by nama_sanksi asc");
                                    while ($row = fetch_array($sql_sanksi)) {
                                        $no++;
                                    ?>
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-ubah-<?php echo $row['id_sanksi']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-data-sanksi&id=' . $row['id_sanksi'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Data Penyerapan</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <div class="col-md-8">
                                                                        <label>Nama Sanksi</label>
                                                                        <input type="text" class="form-control" value="<?php echo $row['nama_sanksi']; ?>" name="nama_sanksi" placeholder="" required>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label>Masa Aktif Bulan</label>
                                                                        <input type="number" class="form-control" value="<?php echo $row['masa_aktif']; ?>" name="masa_aktif" placeholder="" required>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label>Nilai Sanksi</label>
                                                                        <input type="number" class="form-control" value="<?php echo $row['nilai_sanksi']; ?>" name="nilai_sanksi" placeholder="" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label"><br><br></label>
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
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-hapus-<?php echo $row['id_sanksi']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=delete-master-data-sanksi&id=' . $row['id_sanksi'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Hapus Master Data Master Sanksi</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Yakin Anda ingin Menghapus Data ini,klik Tombol Ya? Untuk Menghapusnya</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tutup Edit Modal permintaan -->
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['nama_sanksi']; ?></td>
                                            <td><?php echo $row['masa_akttif'] . " Bulan"; ?></td>
                                            <td><?php echo $row['nilai_sanksi']; ?></td>
                                            <td>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_sanksi']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_sanksi']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            break;

        case "list-master-data-skp":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST MASTER DATA SKP</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-skp">
                            Buat Master Data SKP
                        </button>
                        <!--Modal Add SKP -->
                        <div class="modal fade" id="modal-add-skp">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-plus"> Tambah Data SKP</h4>
                                    </div>
                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-data-skp'); ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama SKP</label>
                                                <input type="text" class="form-control" name="skp" placeholder="Nama SKP" required>
                                                <label>Waktu Efektif</label>
                                                <input type="number" class="form-control" name="waktu" placeholder="Waktu Efektif" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input type="submit" class="btn btn-primary" value="Simpan">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal Skp -->
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>SKP</th>
                                        <th>Waktu Efektif</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $sql_skp = bukaquery("SELECT * FROM tm_skp");
                                    while ($row = fetch_array($sql_skp)) {
                                        $no++;
                                    ?>
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-ubah-<?php echo $row['kd_skp']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-data-skp&id=' . $row['kd_skp'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Data Master SKP</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Nama SKP</label>
                                                                    <input type="text" class="form-control" name="skp" value="<?php echo $row['skp']; ?>" placeholder="Nama SKP" required>
                                                                    <label>Waktu Efektif</label>
                                                                    <input type="number" class="form-control" name="waktu" value="<?php echo $row['waktu']; ?>" placeholder="Waktu Efektif" required>
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
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-hapus-<?php echo $row['kd_skp']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=delete-master-data-skp&id=' . $row['kd_skp'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Delete Data master SKP</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Yakin Anda ingin Menghapus Data ini,klik Tombol Ya? Untuk Menghapusnya</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tutup Edit Modal permintaan -->
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['skp']; ?></td>
                                            <td><?php echo $row['waktu']; ?></td>
                                            <td>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['kd_skp']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['kd_skp']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            break;

        case "list-master-izin-belajar":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST MASTER DATA IZIN BELAJAR PEGAWAI</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-izin-belajar">
                            Buat Izin Belajar
                        </button>
                        <!--Modal Add SKP -->
                        <div class="modal fade" id="modal-add-izin-belajar">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-plus"> Tambah Data Izin Belajar</h4>
                                    </div>
                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-data-izin-belajar'); ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama Pegawai</label>
                                                <select class="form-control select2" name="id_user" data-placeholder="-Pilih Nama Pegawai-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Nama Pegawai-</option>
                                                    <?php
                                                    $tm_pegawai = bukaquery("select tm_pegawai.id_user,tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status='AKTIF' order by tm_pegawai.nama_pegawai asc");
                                                    while ($row = fetch_array($tm_pegawai)) {
                                                        echo "<option value=" . $row['id_user'] . ">" . $row['nama_pegawai'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label>Jenis Peningkatan</label>
                                                <?php echo enumDropdown("tm_izin_belajar", "jenis_peningkatan", "", "-Pilih Jenis-"); ?>
                                                <label>Akademik/Univ</label>
                                                <input type="text" class="form-control" name="nama_univ" placeholder="Nama Akademik/Univ" required>
                                                <label>Alamat Akademik/Univ</label>
                                                <textarea class="form-control" name="alamat_univ" placeholder="Alamat" required></textarea>
                                                <label>Pendidikan Sebelum</label>
                                                <?php echo enumDropdown("tm_izin_belajar", "pendidikan_sebelum", "", "-Pilih Pendidikan-"); ?>
                                                <label>Pendidikan Lanjutan</label>
                                                <?php echo enumDropdown("tm_izin_belajar", "pendidikan_sesudah", "", "-Pilih Pendidikan-"); ?>
                                                <label>Jurusan</label>
                                                <input type="text" class="form-control" name="jurusan" placeholder="Nama Jurusan" required>
                                                <label>Akreditasi</label>
                                                <?php echo enumDropdown("tm_izin_belajar", "akreditasi", "", "-Pilih Akreditasi-"); ?>
                                                <label>No Izin</label>
                                                <input type="text" class="form-control" name="no_izin" placeholder="No Izin Belajar" required>
                                                <label>Tanggal Penerbitan</label>
                                                <input type="text" class="form-control" name="tanggal_izin_belajar" id="datepicker" placeholder="mm/dd/yyyy" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input type="submit" class="btn btn-primary" value="Simpan">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal Skp -->
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Izin Belajar</th>
                                        <th>Nama Pegawai</th>
                                        <th>Nama Akademik/Univ</th>
                                        <th>Jenis Peningkatan</th>
                                        <th>Peningkatan</th>
                                        <th>Akreditasi</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $sql_izin_belajar = bukaquery("SELECT tm_izin_belajar.*, tm_pegawai.nama_pegawai FROM tm_izin_belajar inner join tm_pegawai on tm_izin_belajar.id_user=tm_pegawai.id_user "
                                        . "order by tm_izin_belajar.tanggal_izin_belajar desc ");
                                    while ($row = fetch_array($sql_izin_belajar)) {
                                        $no++;
                                    ?>
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-ubah-<?php echo $row['id_belajar']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-data-izin-belajar&id=' . $row['id_belajar'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Data Master Izin Belajar</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Nama Pegawai</label>
                                                                    <select class="form-control select2" name="id_user" value="<?php echo $row['nama_pegawai']; ?>" data-placeholder="-Pilih Nama Pegawai-" style="width: 100%;" required>
                                                                        <option selected="selected" value="">-Pilih Unit/Bagian-</option>
                                                                        <?php
                                                                        $tm_izin = bukaquery("select tm_pegawai.id_user,tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status='AKTIF' order by tm_pegawai.nama_pegawai asc ");
                                                                        while ($izin = fetch_array($tm_izin)) {
                                                                            if ($row['id_user'] == $izin['id_user']) {
                                                                                echo "<option value=" . $izin['id_user'] . " selected=" . $row['id_user'] . ">" . $izin['nama_pegawai'] . "</option>";
                                                                            } else {
                                                                                echo "<option value=" . $izin['id_user'] . ">" . $izin['nama_pegawai'] . "</option>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                    <label>Jenis Peningkatan</label>
                                                                    <?php echo UpdateEnumDropdown("tm_izin_belajar", "jenis_peningkatan", $row['jenis_peningkatan'], ""); ?>
                                                                    <label>Akademik/Univ</label>
                                                                    <input type="text" class="form-control" name="nama_univ" value="<?php echo $row['nama_univ']; ?>" placeholder="Nama Akademik/Univ" required>
                                                                    <label>Alamat Akademik/Univ</label>
                                                                    <textarea class="form-control" name="alamat_univ" placeholder="Alamat" required>value="<?php echo $row['alamat_univ']; ?>"</textarea>
                                                                    <label>Pendidikan Sebelum</label>
                                                                    <?php echo UpdateEnumDropdown("tm_izin_belajar", "pendidikan_sebelum", $row['pendidikan_sebelum'], ""); ?>
                                                                    <label>Pendidikan Lanjutan</label>
                                                                    <?php echo UpdateEnumDropdown("tm_izin_belajar", "pendidikan_sesudah", $row['pendidikan_sesudah'], ""); ?>
                                                                    <label>Jurusan</label>
                                                                    <input type="text" class="form-control" name="jurusan" value="<?php echo $row['jurusan']; ?>" placeholder="Nama Jurusan" required>
                                                                    <label>Akreditasi</label>
                                                                    <?php echo UpdateEnumDropdown("tm_izin_belajar", "akreditasi", $row['akreditasi'], ""); ?>
                                                                    <label>No Izin</label>
                                                                    <input type="text" class="form-control" name="no_izin" value="<?php echo $row['no_izin']; ?>" placeholder="No Izin Belajar" required>
                                                                    <label>Tanggal Penerbitan (Tgl/Bln/Thn)</label>
                                                                    <input type="text" class="form-control" name="tanggal_izin_belajar" value="<?php echo FormatTgl('d-m-Y', $row['tanggal_izin_belajar']); ?>" data-inputmask='"mask": "99-99-9999","placeholder":"dd-mm-yyyy"' data-mask required>
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
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-hapus-<?php echo $row['id_belajar']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=delete-master-data-izin-belajar&id=' . $row['id_belajar'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Delete Data master SKP</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Yakin Anda ingin Menghapus Data ini,klik Tombol Ya? Untuk Menghapusnya</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tutup Edit Modal permintaan -->
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['no_izin'] . " , Tanggal Penerbitan : " . FormatTgl('d-m-Y', $row['tanggal_izin_belajar']) . ""; ?></td>
                                            <td><?php echo $row['nama_pegawai']; ?></td>
                                            <td><?php echo $row['nama_univ'] . " <p> Alamat : " . $row['alamat_univ'] . "</p>"; ?></td>
                                            <td><?php echo $row['jenis_peningkatan']; ?></td>
                                            <td><?php echo "Dari " . $row['pendidikan_sebelum'] . " Ke " . $row['pendidikan_sesudah'] . " " . $row['jurusan']; ?></td>
                                            <td><?php echo $row['akreditasi']; ?></td>
                                            <td>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_belajar']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_belajar']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            break;

        case "list-master-absensi":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST MASTER DATA SHIFT/ABSENSI</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-absensi">
                            Buat Absensi/Shift
                        </button>
                        <!--Modal Add SKP -->
                        <div class="modal fade" id="modal-add-absensi">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-plus"> Tambah Data Absensi/Shift</h4>
                                    </div>
                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-data-absensi'); ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama Shift</label>
                                                <input type="text" class="form-control" name="nama_shift" placeholder="Nama Shift" required autofocus>
                                                <label>Jam In</label>
                                                <input type="text" class="form-control" name="jam_masuk" data-inputmask='"mask": "99:99:99","placeholder":"hh:ii:ss"' data-mask required>
                                                <label>Sebelum In</label>
                                                <input type="text" class="form-control" name="bi" data-inputmask='"mask": "99:99:99","placeholder":"hh:ii:ss"' data-mask required>
                                                <label>Sesudah In</label>
                                                <input type="text" class="form-control" name="ai" data-inputmask='"mask": "99:99:99","placeholder":"hh:ii:ss"' data-mask required>
                                                <label>Jam Out</label>
                                                <input type="text" class="form-control" name="jam_pulang" data-inputmask='"mask": "99:99:99","placeholder":"hh:ii:ss"' data-mask required>
                                                <label>Sebelum Out</label>
                                                <input type="text" class="form-control" name="bo" data-inputmask='"mask": "99:99:99","placeholder":"hh:ii:ss"' data-mask required>
                                                <label>Sesudah Out</label>
                                                <input type="text" class="form-control" name="ao" data-inputmask='"mask": "99:99:99","placeholder":"hh:ii:ss"' data-mask required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input type="submit" class="btn btn-primary" value="Simpan">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal Skp -->
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Shift</th>
                                        <th>In</th>
                                        <th>Sebelum In</th>
                                        <th>Sesudah In</th>
                                        <th>OUT</th>
                                        <th>Sebelum Out</th>
                                        <th>Sesudah Out</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $sql_absensi = bukaquery("SELECT * FROM tm_shift where status ='1'");
                                    while ($row = fetch_array($sql_absensi)) {
                                        $no++;
                                    ?>
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-ubah-<?php echo $row['id_absensi']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-data-absensi&id=' . $row['id_absensi'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Data Master Absensi/Shift</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Nama Shift</label>
                                                                    <input type="text" class="form-control" name="nama_shift" value="<?php echo $row['nama_shift']; ?>" placeholder="Nama Shift" required autofocus>
                                                                    <label>Jam In</label>
                                                                    <input type="text" class="form-control" name="jam_masuk" value="<?php echo $row['jam_masuk']; ?>" data-inputmask='"mask": "99:99:99","placeholder":"hh:ii:ss"' data-mask required>
                                                                    <label>Sebelum In</label>
                                                                    <input type="text" class="form-control" name="bi" value="<?php echo $row['bi']; ?>" data-inputmask='"mask": "99:99:99","placeholder":"hh:ii:ss"' data-mask required>
                                                                    <label>Sesudah In</label>
                                                                    <input type="text" class="form-control" name="ai" value="<?php echo $row['ai']; ?>" data-inputmask='"mask": "99:99:99","placeholder":"hh:ii:ss"' data-mask required>
                                                                    <label>Jam Out</label>
                                                                    <input type="text" class="form-control" name="jam_pulang" value="<?php echo $row['jam_pulang']; ?>" data-inputmask='"mask": "99:99:99","placeholder":"hh:ii:ss"' data-mask required>
                                                                    <label>Sebelum Out</label>
                                                                    <input type="text" class="form-control" name="bo" value="<?php echo $row['bo']; ?>" data-inputmask='"mask": "99:99:99","placeholder":"hh:ii:ss"' data-mask required>
                                                                    <label>Sesudah Out</label>
                                                                    <input type="text" class="form-control" name="ao" value="<?php echo $row['ao']; ?>" data-inputmask='"mask": "99:99:99","placeholder":"hh:ii:ss"' data-mask required>
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
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-hapus-<?php echo $row['id_absensi']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=delete-master-data-absensi&id=' . $row['id_absensi'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Delete Data master SKP</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Yakin Anda ingin Menghapus Data ini,klik Tombol Ya? Untuk Menghapusnya</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tutup Edit Modal permintaan -->
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['nama_shift']; ?></td>
                                            <td><?php echo $row['jam_masuk']; ?></td>
                                            <td><?php echo $row['bi']; ?></td>
                                            <td><?php echo $row['ai']; ?></td>
                                            <td><?php echo $row['jam_pulang']; ?></td>
                                            <td><?php echo $row['bo']; ?></td>
                                            <td><?php echo $row['ao']; ?></td>
                                            <td>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_absensi']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_absensi']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            break;

        case "list-master-libur":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST MASTER DATA LIBUR</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-libur">
                            Buat Hari Libur
                        </button>
                        <!--Modal Add SKP -->
                        <div class="modal fade" id="modal-add-libur">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-plus"> Tambah Data Hari Libur</h4>
                                    </div>
                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-data-libur'); ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label class="control-label">Tanggal Libur</label>
                                                <input type="text" name="tanggal" class="form-control" placeholder="mm/dd/yyyy" id="datepicker" required readonly>
                                                <label>Jenis Libur</label>
                                                <?php echo enumDropdown("tm_hari_libur", "jenis", "", "-Pilih Jenis Libur-"); ?>
                                                <label>Keterangan Libur</label>
                                                <input type="text" class="form-control" name="keterangan" placeholder="Keterangan Libur" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input type="submit" class="btn btn-primary" value="Simpan">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal Skp -->
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Libur</th>
                                        <th>Jenis Libur</th>
                                        <th>Keterangan Libur</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $sql_libur = bukaquery("SELECT tm_hari_libur.id_libur, tm_hari_libur.tanggal, tm_hari_libur.jenis, tm_hari_libur.keterangan FROM tm_hari_libur ");
                                    while ($row = fetch_array($sql_libur)) {
                                        $no++;
                                    ?>
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-ubah-<?php echo $row['id_libur']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-data-libur&id=' . $row['id_libur'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Data Hari Libur</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Tanggal Libur</label>
                                                                    <input type="text" class="form-control" placeholder="mm/dd/yyyy" id="datepicker" name="tanggal" value="<?php echo $row['tanggal']; ?>" required autofocus>
                                                                    <label class="control-label">Jenis Libur</label>
                                                                    <?php echo UpdateEnumDropdown("tm_hari_libur", "jenis", ""); ?>
                                                                    <label>Keterangan Libur</label>
                                                                    <input type="text" class="form-control" name="keterangan" value="<?php echo $row['keterangan']; ?>" required autofocus>
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
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-hapus-<?php echo $row['id_libur']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=delete-master-data-libur&id=' . $row['id_libur'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Delete Data Master Libur</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Yakin Anda ingin Menghapus Data ini,klik Tombol Ya? Untuk Menghapusnya</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tutup Edit Modal permintaan -->
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['tanggal']; ?></td>
                                            <td><?php echo $row['jenis']; ?></td>
                                            <td><?php echo $row['keterangan']; ?></td>
                                            <td>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_libur']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_libur']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            break;

        case "list-master-data-formasi":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST MASTER DATA FORMASI</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-formasi">
                            Buat Master Data Formasi
                        </button>
                        <!--Modal Add SKP -->
                        <div class="modal fade" id="modal-add-formasi">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-plus"> Tambah Data Formasi</h4>
                                    </div>
                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-master-data-formasi'); ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama Formasi</label>
                                                <input type="text" class="form-control" name="formasi" placeholder="Nama Formasi" required>
                                                <label class="control-label">Formasi<font color="red">*</font></label>
                                                <?php echo enumDropdown("tm_formasi", "pendidikan", "", "-Pilih Pendidikan-"); ?>
                                                <label class="control-label">Status<font color="red">*</font></label>
                                                <?php echo enumDropdown("tm_formasi", "status", "", "-Pilih Status-"); ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input type="submit" class="btn btn-primary" value="Simpan">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal Skp -->
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Formasi</th>
                                        <th>Pendidikan</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $sql_skp = bukaquery("SELECT * FROM tm_formasi");
                                    while ($row = fetch_array($sql_skp)) {
                                        $no++;
                                    ?>
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-ubah-<?php echo $row['id_formasi']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-master-data-formasi&id=' . $row['id_formasi'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Data Master Formasi</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Nama Formasi</label>
                                                                    <input type="text" class="form-control" name="formasi" value="<?php echo $row['formasi']; ?>" placeholder="Nama Formasi" required>
                                                                    <label class="control-label">Formasi<font color="red">*</font></label>
                                                                    <?php echo enumDropdown("tm_formasi", "pendidikan", "", "-Pilih Pendidikan-"); ?>
                                                                    <label class="control-label">Status<font color="red">*</font></label>
                                                                    <?php echo enumDropdown("tm_formasi", "status", "", "-Pilih Status-"); ?>
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
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-hapus-<?php echo $row['id_formasi']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=delete-master-data-formasi&id=' . $row['id_formasi'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Delete Data master Formasi</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Yakin Anda ingin Menghapus Data ini,klik Tombol Ya? Untuk Menghapusnya</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tutup Edit Modal permintaan -->
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['formasi']; ?></td>
                                            <td><?php echo $row['pendidikan']; ?></td>
                                            <td><?php echo $row['status']; ?></td>
                                            <td>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_formasi']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_formasi']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            break;

        case "set-spj":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> MAPING SPJ GAJI</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if (getOne("select count(set_spj.id_spj) from set_spj") == 0) { ?>
                            <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-set-spj">
                                Buat Maping SPJ
                            </button>
                        <?php } ?>
                        <!--Modal Add SKP -->
                        <div class="modal fade" id="modal-add-set-spj">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-plus"> Setup SPJ</h4>
                                    </div>
                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=master-data&act=add-set-spj'); ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama PPK Keuangan</label>
                                                <select class="form-control select2" name="ppk_keuangan" data-placeholder="-Pilih PPK Keuangan-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Nama Pegawai-</option>
                                                    <?php
                                                    $tm_pegawai = bukaquery("select tm_pegawai.id_user,tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status='AKTIF' order by tm_pegawai.nama_pegawai asc");
                                                    while ($row = fetch_array($tm_pegawai)) {
                                                        echo "<option value=" . $row['id_user'] . ">" . $row['nama_pegawai'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label>Nama Bendahara Pengeluaran</label>
                                                <select class="form-control select2" name="bendahara_pengeluaran" data-placeholder="-Pilih Bendahara-" style="width: 100%;" required>
                                                    <option selected="selected" value="">-Pilih Nama Pegawai-</option>
                                                    <?php
                                                    $tm_pegawai = bukaquery("select tm_pegawai.id_user,tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status='AKTIF' order by tm_pegawai.nama_pegawai asc");
                                                    while ($row = fetch_array($tm_pegawai)) {
                                                        echo "<option value=" . $row['id_user'] . ">" . $row['nama_pegawai'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input type="submit" class="btn btn-primary" value="Simpan">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal Skp -->
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>PPK Keuangan</th>
                                        <th>Bendahara Pengeluaran</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $sql_spj = bukaquery("SELECT * FROM set_spj ");
                                    while ($row = fetch_array($sql_spj)) {
                                        $no++;
                                    ?>
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-ubah-<?php echo $row['id_spj']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-set-spj&id=' . $row['id_spj'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Setup SPJ</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label>Nama PPK Keuangan</label>
                                                                    <select class="form-control select2" name="ppk_keuangan" value="<?php echo getOne("select nama_pegawai from tm_pegawai where id_user='$row[ppk_keuangan]'"); ?>" data-placeholder="-Pilih PPK Keuangan-" style="width: 100%;" required>
                                                                        <option selected="selected" value="">-Pilih PPK Keuangan-</option>
                                                                        <?php
                                                                        $tm_pegawai = bukaquery("select tm_pegawai.id_user,tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status='AKTIF' order by tm_pegawai.nama_pegawai asc ");
                                                                        while ($pegawai = fetch_array($tm_pegawai)) {
                                                                            if ($row['ppk_keuangan'] == $pegawai['id_user']) {
                                                                                echo "<option value=" . $pegawai['id_user'] . " selected=" . $row['ppk_keuangan'] . ">" . $pegawai['nama_pegawai'] . "</option>";
                                                                            } else {
                                                                                echo "<option value=" . $pegawai['id_user'] . ">" . $pegawai['nama_pegawai'] . "</option>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                    <label>Nama Bendahara Pengeluaran</label>
                                                                    <select class="form-control select2" name="bendahara_pengeluaran" value="<?php echo getOne("select nama_pegawai from tm_pegawai where id_user='$row[bendahara_pengeluaan]'"); ?>" data-placeholder="-Pilih Bendahara Pengeluaran-" style="width: 100%;" required>
                                                                        <option selected="selected" value="">-Pilih Bendahara Pengeluaran-</option>
                                                                        <?php
                                                                        $tm_pegawai = bukaquery("select tm_pegawai.id_user,tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status='AKTIF' order by tm_pegawai.nama_pegawai asc ");
                                                                        while ($pegawai = fetch_array($tm_pegawai)) {
                                                                            if ($row['bendahara_pengeluaran'] == $pegawai['id_user']) {
                                                                                echo "<option value=" . $pegawai['id_user'] . " selected=" . $row['bendahara_pengeluaran'] . ">" . $pegawai['nama_pegawai'] . "</option>";
                                                                            } else {
                                                                                echo "<option value=" . $pegawai['id_user'] . ">" . $pegawai['nama_pegawai'] . "</option>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
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
                                        <tr>
                                            <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where id_user='$row[ppk_keuangan]'"); ?></td>
                                            <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where id_user='$row[bendahara_pengeluaran]'"); ?></td>
                                            <td>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_spj']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            break;

        case "list-master-grade":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST MASTER GRADE</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php
                        $sql = bukaquery("select id_level,grade_kinerja,grade_prilaku,nama_level from tm_level");
                        ?>
                        <div class="box-body table-responsive">
                            <table id="example" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Jabatan</th>
                                        <th>Grade Kinerja</th>
                                        <th>Grade Prilaku</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    while ($row = mysql_fetch_array($sql)) {
                                        $no++;
                                    ?>
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['nama_level']; ?></td>
                                            <td><?php echo $row['grade_kinerja']; ?></td>
                                            <td><?php echo $row['grade_prilaku']; ?></td>
                                            <td>
                                                <button type="button" class="btn-xs btn-warning fa fa-edit" data-toggle="modal" title="Update" data-target="#modal-edit-<?php echo $row['id_level']; ?>"> </button>
                                            </td>
                                        </tr>
                                        <!--Modal Edit-->
                                        <div class="modal fade" id="modal-edit-<?php echo $row['id_level']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form action="<?php echo $aksi . paramEncrypt('module=master-data&act=update-grade&id=' . $row['id_level'] . ''); ?>" role="form" method="post">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                            </button>
                                                            <h4 class="modal-title" id="myModalLabel">Form Grade Jabatan</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label class="control-label">Nama Level</label>
                                                                <input type="text" name="nama_level" class="form-control" value="<?php echo $row['nama_level']; ?>" readonly>
                                                                <label class="control-label">Grade Kinerja</label>
                                                                <input type="text" name="grade_kinerja" class="form-control" value="<?php echo $row['grade_kinerja']; ?>" required>
                                                                <label class="control-label">Grade Prilaku</label>
                                                                <input type="text" name="grade_prilaku" class="form-control" value="<?php echo $row['grade_prilaku']; ?>" required>
                                                            </div>
                                                            <br>
                                                            <div class="form-group">
                                                                <label class="control-label"><br></label>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default fa fa-close" data-dismiss="modal"> Close</button>
                                                            <button type="submit" class="btn btn-warning fa fa-save"> Update</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!--Tutup Modal Edit-->
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

        <?php
            break;
    }
        ?>