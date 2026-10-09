<?php
$aksi = "module/kinerja-pegawai/aksi-kinerja-pegawai.php?";
$aksilama = "module/kinerja-pegawai/aksi-kinerja-lama.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;

    case "list-data-skp-tahunan-pegawai":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST DATA SKP TAHUNAN</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <!-- Combo Box-->
            <div class="box-body"> 
                    <form action="" method="post">
                        <div class="form-group">
                            <table align="Left">
                                <tr>
                                    <td>
                                        <label>Tahun</label> 
                                    </td>
                                    <td></td>
                                </tr>
                                <tr> 
                                    <td>
                                        <select class="form-control select2" name="tahun"  data-placeholder="-Pilih Tahun-"  required>
                                        <?php loadThnnow(isset($_POST['tahun'])? $_POST['tahun'] : null); ?>               
                                        </select>  
                                    </td>
                                    <td>
                                        <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>
            </div>
                <?php
                $tahun = isset($_POST['tahun']) ? $_POST['tahun'] : $thn_now;
                 ?>
            <div class="box-body">                
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-skp-tahunan">
                    Buat Master Data SKP Tahunan
                </button>
                <br>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-skp-tahunan">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Tambah Data SKP Tahunan</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=kinerja-pegawai&act=add-skp-tahunan&id=' . $id_user . ''); ?>" method="post"> 
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Aktifitas</label>
                                        <select class="form-control select2" name="kd_skp"  data-placeholder="-Pilih Aktifitas-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Aktifitas-</option>
                                            <?php
                                            $tm_skp = bukaquery("select * from tm_skp");
                                            while ($row = fetch_array($tm_skp)) {
                                                echo"<option value=" . $row['kd_skp'] . ">" . $row['skp'] . " [" . $row['waktu'] . " Menit]</option>";
                                            }
                                            ?>               
                                        </select>
                                        <label>Kuantitas</label>
                                        <input type="number" class="form-control" name="kuantitas" placeholder="Kuantitas" required>
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
                                <th>Nama Kegiatan (SKP)</th>
                                <th>Unit</th>
                                <th>Kuantitas <br>(Target)</th>
                                <th><p class="text-red">Kuantitas <br>(Pencapaian)</p></th>
                        <th>Kualitas <br> (Target)</th>
                        <th><p class="text-red">Kualitas <br> (Pencapaian)</p></th>
                        <th>Capaian Waktu <br> (Target)</th>
                        <th><p class="text-red">Capaian Waktu <br> (Pencapaian)</p></th>
                        <th>Tgl. Buat</th>
                        <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $realisasi = [];
                            
                            $q_realisasi = bukaquery("
                                SELECT 
                                    tt_kinerja.kd_skp,
                                    SUM(
                                        ROUND(
                                            TIMESTAMPDIFF(
                                                MINUTE,
                                                tt_kinerja.waktu_mulai,
                                                tt_kinerja.waktu_akhir
                                            ) / tm_skp.waktu
                                        )
                                    ) AS volume
                                FROM tt_kinerja
                                INNER JOIN tm_skp ON tt_kinerja.kd_skp = tm_skp.kd_skp
                                WHERE tt_kinerja.id_user = '$id_user'
                                AND YEAR(tt_kinerja.tanggal_kinerja) = '$tahun'
                                GROUP BY tt_kinerja.kd_skp
                            ");
                            
                            while ($r = fetch_array($q_realisasi)) {
                                $realisasi[$r['kd_skp']] = $r['volume'];
                            }
                            
                            $no = 0;
                            $total_realisasi_menit = 0;
                            $tt_skptahunan = bukaquery("SELECT tm_pegawai.nip, tm_unit.nama_unit, tm_skp.skp, tt_skptahunan.id_skp, tt_skptahunan.kd_skp, tt_skptahunan.kuantitas, tt_skptahunan.tgl_buat, tt_skptahunan.tgl_update, tm_skp.waktu
                                                        FROM tt_skptahunan
                                                        INNER JOIN tm_skp ON tt_skptahunan.kd_skp = tm_skp.kd_skp
                                                        INNER JOIN tm_pegawai ON tt_skptahunan.id_user = tm_pegawai.id_user
                                                        INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                                        where tt_skptahunan.id_user='$id_user' and year(tt_skptahunan.tgl_buat) = '$tahun'");
                            while ($row = fetch_array($tt_skptahunan)) {
                                $no++;
                                $volume = getOne("
                                    SELECT SUM(
                                        ROUND(
                                            TIMESTAMPDIFF(MINUTE, tt_kinerja.waktu_mulai, tt_kinerja.waktu_akhir) 
                                            / tm_skp.waktu
                                        )
                                    ) AS volume
                                    FROM tt_kinerja
                                    INNER JOIN tm_skp ON tt_kinerja.kd_skp = tm_skp.kd_skp
                                    INNER JOIN tm_pegawai ON tt_kinerja.id_user = tm_pegawai.id_user
                                    WHERE tt_kinerja.kd_skp = '$row[kd_skp]'
                                    AND tm_pegawai.id_user = '$id_user'
                                    AND YEAR(tanggal_kinerja) = '$tahun'
                                ");
                                
                                $volume = $realisasi[$row['kd_skp']] ?? 0;
                                $menit_realisasi = $volume * $row['waktu'];
                                
                                // tambahkan ke total
                                $total_realisasi_menit += $menit_realisasi;


                                ?>
                                <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-ubah-<?php echo $row['id_skp']; ?>"  role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=kinerja-pegawai&act=update-skp-tahunan&id=' . $row['id_skp'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Update SKP Tahunan</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label>Aktifitas</label>
                                                        <select class="form-control select2" name="kd_skp"  data-placeholder="-Pilih Aktifitas-" style="width: 100%;" required>
                                                            <option selected="selected" value="">-Pilih Aktifitas-</option>
                                                            <?php
                                                            $tm_skp = bukaquery("select * from tm_skp ");
                                                            while ($skp = fetch_array($tm_skp)) {
                                                                if ($row['kd_skp'] == $skp['kd_skp']) {
                                                                    echo"<option value=" . $skp['kd_skp'] . " selected=" . $row['kd_skp'] . ">" . $skp['skp'] . " [" . $skp['waktu'] . " Menit]</option>";
                                                                } else {
                                                                    echo"<option value=" . $skp['kd_skp'] . ">" . $skp['skp'] . " [" . $skp['waktu'] . " Menit]</option>";
                                                                }
                                                            }
                                                            ?>               
                                                        </select>
                                                        <label>Kuantitas</label>
                                                        <input type="number" class="form-control" name="kuantitas" value="<?php echo $row['kuantitas']; ?>" required>
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
                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_skp']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=kinerja-pegawai&act=delete-skp-tahunan&id=' . $row['id_skp'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Delete Data SKP Tahunan</h4>
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
                                <td><?php echo $row['skp'] . " (" . $row['waktu'] . " Menit)"; ?></td>
                                <td><?php echo $row['nama_unit'] ?></td>
                                <td><?php echo $row['kuantitas']; ?></td>
                                <td><?php echo $volume; ?></td>
                                <td>100%</td>
                                <td>
                                <?php
                                echo $row['kuantitas'] > 0
                                    ? number_format(($volume / $row['kuantitas']) * 100, 2) . '%'
                                    : '0%';
                                ?>
                                </td>
                                <td><?php echo $row['kuantitas'] * $row['waktu'] . " Menit"; ?></td>
                                <td><?php echo $menit_realisasi; ?> Menit</td>
                                <td><?php echo FormatTgl('d-m-Y', $row['tgl_buat']); ?></td>
                                <td>
                                <?php 
                                if ($tahun==$thn_now){
                                ?>
                                    <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_skp']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                    <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_skp']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                <?php } ?>
                                </td>
                            </tr>                                  
                            <?php
                        }
                        ?>    
                        </tbody>
                        <thead>
                        <th colspan="7"></th>
                        <th colspan="1"><?php
                            $total_target = getOne("SELECT sum(tt_skptahunan.kuantitas * tm_skp.waktu) from tt_skptahunan INNER JOIN tm_skp ON tt_skptahunan.kd_skp = tm_skp.kd_skp where tt_skptahunan.id_user='$id_user' and year(tt_skptahunan.tgl_buat) = '$tahun'");
                            if ($total_target > 115200) {
                                echo $total_target . " Menit <p class='text-red'>Target anda Tidak Boleh Melebihi 115200 menit";
                            } else {
                                echo $total_target . " Menit";
                            }
                            ?>
                        </th>
                        <th colspan="3">
                            <?php echo $total_realisasi_menit; ?> Menit
                        </th>

                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <?php
    break;
        
    case "kinerja-pegawai":
       ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST DATA KINERJA PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">                
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-kinerja-utama">
                    Buat Kinerja Utama
                </button>
                <button type="button" class="btn btn-warning fa fa-plus" data-toggle="modal" data-target="#modal-add-kinerja-tambahan">
                    Buat Kinerja Tambahan
                </button>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-kinerja-utama">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Tambah Kinerja Utama </h4>
                            </div>
                            <form role="form" action="<?php echo $aksilama . paramEncrypt('module=kinerja-pegawai&act=add-kinerja-utama&id=' . $id_user . ''); ?>" method="post"> 
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <input type="text" class="form-control" id="datepicker1" placeholder="mm/dd/yyyy" name="tanggal_kinerja" required readonly>
                                        <label>Aktifitas</label>
                                        <select class="form-control select2" name="kd_skp"  data-placeholder="-Pilih Aktifitas-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Aktifitas-</option>
                                            <?php
                                            $tt_skptahunan = bukaquery("SELECT tm_skp.skp, tm_skp.waktu, tm_skp.kd_skp FROM tt_skptahunan INNER JOIN tm_skp ON tt_skptahunan.kd_skp = tm_skp.kd_skp where tt_skptahunan.id_user=" . $id_user . " and year(tt_skptahunan.tgl_buat) ='$thn_now'");
                                            while ($skptahuanan = fetch_array($tt_skptahunan)) {
                                                echo"<option value=" . $skptahuanan['kd_skp'] . ">" . $skptahuanan['skp'] . " [" . $skptahuanan['waktu'] . " Menit]</option>";
                                            }
                                            ?>               
                                        </select>
                                        <label>Uraian Aktifitas</label>
                                        <textarea class="form-control" name="uraian" placeholder="Note" rows="5" required></textarea>
                                        <label>Waktu Mulai</label>
                                        <div class="input-group">
                                            <input class="form-control" name="waktu_mulai" id="jam-mulai-utama" value="" placeholder="00:00" readonly required>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                        <label>Waktu Selesai</label>
                                        <div class="input-group">
                                            <input class="form-control" name="waktu_akhir" id="jam-selesai-utama" value="" placeholder="00:00" readonly required>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
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
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-kinerja-tambahan">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Tambah Kinerja Tambahan</h4>
                            </div>
                            <form role="form" action="<?php echo $aksilama . paramEncrypt('module=kinerja-pegawai&act=add-kinerja-tambahan&id=' . $id_user . ''); ?>" method="post"> 
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <input type="text" class="form-control" id="datepicker2" placeholder="mm/dd/yyyy" name="tanggal_kinerja" required readonly>
                                        <label>Aktifitas</label>
                                        <select class="form-control select2" name="kd_skp"  data-placeholder="-Pilih Aktifitas-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Aktifitas-</option>
                                            <?php
                                            $tm_skp = bukaquery("select * from tm_skp");
                                            while ($row = fetch_array($tm_skp)) {
                                                echo"<option value=" . $row['kd_skp'] . ">" . $row['skp'] . " [" . $row['waktu'] . " Menit]</option>";
                                            }
                                            ?>               
                                        </select>
                                        <label>Uraian Aktifitas</label>
                                        <textarea class="form-control" name="uraian" rows="5" placeholder="Note" required></textarea>
                                        <label>Waktu Mulai</label>
                                        <div class="input-group">
                                            <input class="form-control" name="waktu_mulai" id="jam-mulai-tambahan" value="" placeholder="00:00" readonly required>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                        <label>Waktu Selesai</label>
                                        <div class="input-group">
                                            <input class="form-control" name="waktu_akhir" id="jam-selesai-tambahan" value="" placeholder="00:00" readonly required>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
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
                    <table id="kinerja" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No <?php echo $bulan_setup; ?></th>
                                <th>Tanggal</th>
                                <th>Aktifitas</th>
                                <th>Uraian</th>
                                <th>Waktu Mulai</th>
                                <th>Waktu Selesai</th>
                                <th>Waktu Efektif</th>
                                <th>Kategori</th>
                                <th>Volume</th>
                                <th>Tgl. Buat</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            if ($day >= 1 and $day <= $setup['tutup_kinerja']) {
                                $bulan_setup = $bln_sebelumnya;
                                $tahun_setup = $thn;
                            } else {
                                $bulan_setup = $bln_now;
                                $tahun_setup = $thn_now;
                            }
                            $tt_kinerja = bukaquery("SELECT tm_pegawai.nip, tm_skp.skp, tt_kinerja.id_kinerja, tt_kinerja.uraian, tt_kinerja.waktu_mulai, tt_kinerja.waktu_akhir, tt_kinerja.date, tm_skp.waktu, tt_kinerja.kd_skp, tt_kinerja.tanggal_kinerja, tt_kinerja.`status`
                                                    FROM tt_kinerja
                                                    INNER JOIN tm_skp ON tt_kinerja.kd_skp = tm_skp.kd_skp
                                                    INNER JOIN tm_pegawai ON tt_kinerja.id_user = tm_pegawai.id_user
                                                    where month(tt_kinerja.tanggal_kinerja)='$bulan_setup' and year(tt_kinerja.tanggal_kinerja)='$tahun_setup' and tt_kinerja.id_user=" . $id_user . " order by tt_kinerja.tanggal_kinerja, tt_kinerja.waktu_mulai Desc");
                            while ($row = fetch_array($tt_kinerja)) {
                                $no++;
                                $durasi = floor(((is_string($row['waktu_mulai']) ? strtotime($row['waktu_akhir']) : $row['waktu_akhir']) - (is_string($row['waktu_mulai']) ? strtotime($row['waktu_mulai']) : $row['waktu_mulai'])) / 60);
                                ?>

                                <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_kinerja']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksilama . paramEncrypt('module=kinerja-pegawai&act=delete-kinerja&id=' . $row['id_kinerja'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Delete Data SKP Tahunan</h4>
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
                                <td><?php echo FormatTgl('d-m-Y', $row['tanggal_kinerja']); ?></td>
                                <td><?php echo $row['skp'] . " (" . $row['waktu'] . " Menit)"; ?></td>
                                <td><?php echo $row['uraian'] ?></td>
                                <td><?php echo $row['waktu_mulai']; ?></td>
                                <td><?php echo $row['waktu_akhir']; ?></td>
                                <?php if ($durasi < 0) {
                                    echo "<th style='background-color: coral'>" .  $durasi .  " Menit</th>";
                                }
                                else {
                                    echo "<th>" .  $durasi .  " Menit</th>";
                                }

                                 ?>
                                <td><?php echo $row['status']; ?></td>
                                <td><?php echo number_format(floor(((is_string($row['waktu_mulai']) ? strtotime($row['waktu_akhir']) : $row['waktu_akhir']) - (is_string($row['waktu_mulai']) ? strtotime($row['waktu_mulai']) : $row['waktu_mulai'])) / 60) / $row['waktu']); ?></td>
                                <td><?php echo FormatTgl('d/m/y H:i:s', $row['date']); ?></td>
                                <td>
                                    <!--<span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_kinerja']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>--> 
                                    <a href="?<?php echo paramEncrypt('module=kinerja-pegawai&act=update-kinerja-pegawai&id=' . $row['id_kinerja'] . '') ?>"><button type="submit" class="btn-xs btn-warning fa fa-edit"></button></a>
                                    <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_kinerja']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
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
        
    case "add-kinerja-pegawai": //kalender
    
      // ---------- Aturan edit ----------
      $tutup_kinerja = (int)getOne("SELECT setup.tutup_kinerja FROM setup"); // mis. 4
      $today  = new DateTime();                  // hari ini
      $dayNow = (int)$today->format('d');
      $curY   = (int)$today->format('Y');
      $curM   = (int)$today->format('m');
      $prev   = (clone $today)->modify('first day of last month');
      $prevY  = (int)$prev->format('Y');
      $prevM  = (int)$prev->format('m');
    
      // Bulan kemarin bisa diedit hanya jika hari ini ≤ tutup_kinerja
      $canEditPrevMonth = ($dayNow <= $tutup_kinerja);
    
      // Anchor tampilan awal kalender: bulan berjalan
      $allowed      = (clone $today)->modify('first day of this month');
      $allowedYear  = (int)$allowed->format('Y');
      $allowedMonth = (int)$allowed->format('m');
    
      // ---------- Endpoint ----------
      $aksiBase       = "module/kinerja-pegawai/aksi-kinerja-pegawai.php?";
      $urlAdd         = $aksiBase . paramEncrypt('module=kinerja-pegawai&act=add-kinerja&id='.$id_user);
      $urlUpdateNoId  = $aksiBase . paramEncrypt('module=kinerja-pegawai&act=update-kinerja');
      $urlDeleteNoId  = $aksiBase . paramEncrypt('module=kinerja-pegawai&act=delete-kinerja');
    
      // ---------- Opsi SKP ----------
      function buildOptions($sql){
        $out=''; $q=bukaquery($sql);
        while($r=fetch_array($q)){ $out.='<option value="'.$r['kd_skp'].'">'.$r['skp'].' ['.$r['waktu'].' Menit]</option>'; }
        return $out ?: '<option value="">(Tidak tersedia)</option>';
      }
      $optUtama    = buildOptions("
          SELECT tm_skp.kd_skp, tm_skp.skp, tm_skp.waktu
          FROM tt_skptahunan INNER JOIN tm_skp ON tt_skptahunan.kd_skp=tm_skp.kd_skp
          WHERE tt_skptahunan.id_user='$id_user' AND YEAR(tt_skptahunan.tgl_buat)='".date('Y')."'
          ORDER BY tm_skp.skp
      ");
      $optTambahan = buildOptions("SELECT kd_skp, skp, waktu FROM tm_skp ORDER BY skp");
    ?>
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title fa fa-calendar">  Input Kinerja</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
    
      <?php $kalender_kinerja='yes'; ?>
      <div class="box-body">
        <div class="row">
          <div class="col-md-8">
            <div id="calendar-kinerja"></div>
            <p class="help-block">
              • Klik nomor tanggal untuk melihat aktivitas pada tanggal itu.<br>
              • <b>Boleh mengisi:</b> Bulan berjalan (sampai hari ini)<?php echo $canEditPrevMonth ? ' dan bulan kemarin (hingga tanggal '.$tutup_kinerja.' bulan ini)' : ''; ?>.<br>
              • Bulan kemarin menjadi <b>view only</b> setelah tanggal <?php echo $tutup_kinerja; ?> bulan ini.<br>
              • Badge angka di kotak tanggal = jumlah aktivitas yang diinput.
            </p>
          </div>
          <div class="col-md-4">
            <div class="box box-primary">
              <div class="box-header with-border"><h3 class="box-title">Ringkasan Bulan</h3></div>
              <div class="box-body">
                <div><b id="label-periode"></b></div><hr>
                <p>Total aktivitas: <b id="sum-aktivitas">0</b></p>
                <p>Total waktu: <b id="sum-menit">0</b> menit</p>
                <p>Belum divalidasi: <b id="sum-belum">0</b></p>
                <p>Disetujui: <b id="sum-setuju">0</b></p>
                <p>
                  <button id="btnExportExcel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Export Excel</button>
                </p>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal daftar aktivitas per hari -->
    <div class="modal fade" id="modalDay" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-aqua">
            <button class="close" data-dismiss="modal"><span>&times;</span></button>
            <h4 class="modal-title" id="modalDayTitle">Daftar Aktivitas</h4>
          </div>
          <div class="modal-body">
            <div class="text-right" style="margin-bottom:10px">
              <button id="btnAddFromDay" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Tambah</button>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered table-striped" id="tblDay">
                <thead>
                  <tr>
                    <th style="width:120px">Jam</th>
                    <th style="width:90px">Jenis</th>
                    <th>Nama Aktivitas</th>
                    <th>Laporan</th>
                    <th style="width:85px">Waktu</th>
                    <th style="width:85px">Volume</th>
                    <th style="width:160px">Aksi</th>
                  </tr>
                </thead>
                <tbody><!-- via JS --></tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-default" data-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal Detil (EDIT/INPUT) -->
    <div class="modal fade" id="modalDetail" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <form id="formDetail" method="post">
          <input type="hidden" id="detailActionUrl">
          <input type="hidden" id="detail_kd_skp" name="kd_skp">
          <input type="hidden" id="detail_id" name="id">
          <div class="modal-content">
            <div class="modal-header bg-light-blue">
              <button class="close" data-dismiss="modal"><span>&times;</span></button>
              <h4 class="modal-title"><i class="fa fa-list"></i> Detil Aktivitas</h4>
            </div>
            <div class="modal-body">
              <div class="row" style="margin-bottom:10px">
                <div class="col-xs-12">
                  <label>Tanggal</label>
                  <input class="form-control" id="detail_tanggal" name="tanggal_kinerja" readonly>
                </div>
              </div>
    
              <div class="row" style="margin-bottom:10px">
                <div class="col-xs-4">
                  <label>Jam Mulai</label>
                  <input type="text" class="form-control" id="detail_mulai"  name="waktu_mulai" autocomplete="off">
                </div>
                <div class="col-xs-4">
                  <label>Jam Selesai</label>
                  <input type="text" class="form-control" id="detail_selesai" name="waktu_akhir" autocomplete="off">
                </div>
              </div>
    
              <div class="form-group">
                <label>Jenis</label><br>
                <label class="radio-inline"><input type="radio" name="jenisRadio" value="Utama" checked> Utama</label>
                <label class="radio-inline"><input type="radio" name="jenisRadio" value="Tambahan"> Tambahan</label>
              </div>
    
              <div class="form-group" id="wrap_detail_skp_utama">
                <label>Aktivitas (SKP Utama)</label>
                <select class="form-control" id="detail_skp_utama" style="width:100%">
                  <option value="">-Pilih Aktifitas-</option>
                  <?php echo $optUtama; ?>
                </select>
              </div>
    
              <div class="form-group" id="wrap_detail_skp_tambahan" style="display:none">
                <label>Aktivitas (SKP Tambahan)</label>
                <select class="form-control" id="detail_skp_tambahan" style="width:100%">
                  <option value="">-Pilih Aktifitas-</option>
                  <?php echo $optTambahan; ?>
                </select>
              </div>
    
              <div class="form-group">
                <label>Keterangan</label>
                <textarea class="form-control" rows="4" name="uraian" id="detail_uraian" maxlength="4000"></textarea>
              </div>
            </div>
    
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
              <button id="btnSave" type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Modal Detil (VIEW ONLY) -->
    <div class="modal fade" id="modalDetailView" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-gray">
            <button class="close" data-dismiss="modal"><span>&times;</span></button>
            <h4 class="modal-title"><i class="fa fa-eye"></i> Detil Aktivitas (View only)</h4>
          </div>
          <div class="modal-body">
            <div class="row" style="margin-bottom:10px">
              <div class="col-xs-12">
                <label>Tanggal</label>
                <input class="form-control" id="view_tanggal" readonly>
              </div>
            </div>
            <div class="row" style="margin-bottom:10px">
              <div class="col-xs-4">
                <label>Jam Mulai</label>
                <input type="text" class="form-control" id="view_mulai" readonly>
              </div>
              <div class="col-xs-4">
                <label>Jam Selesai</label>
                <input type="text" class="form-control" id="view_selesai" readonly>
              </div>
            </div>
            <div class="form-group">
              <label>Jenis</label>
              <input type="text" class="form-control" id="view_jenis" readonly>
            </div>
            <div class="form-group">
              <label>Aktivitas (SKP)</label>
              <input type="text" class="form-control" id="view_skp" readonly>
            </div>
            <div class="form-group">
              <label>Keterangan</label>
              <textarea class="form-control" rows="4" id="view_uraian" readonly></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-default" data-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
    
    <style>
    /* Badge di tengah kotak tanggal */
    #calendar-kinerja td.fc-day { position: relative; pointer-events:auto; }
    .fc-mini-badge{
      position:absolute; left:50%; top:50%;
      transform: translate(-50%,-50%);
      min-width:24px; height:24px; line-height:24px;
      border-radius:12px; padding:0 8px;
      font-size:12px; text-align:center; color:#fff; background:#3c8dbc;
      pointer-events:none; z-index:1;
    }
    /* Kunci tanggal masa depan (bulan berjalan) saja */
    .fc-disabled-day { background:#f5f5f5 !important; cursor:not-allowed; pointer-events:none; }
    .fc-disabled-day .fc-day-number { opacity:.5; }
    </style>
    <?php
    break;
}
?>


