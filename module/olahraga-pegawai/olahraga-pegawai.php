<?php
$aksi = "module/olahraga-pegawai/aksi-olahraga-pegawai.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;


    case "list-data-olahraga-pegawai":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST DATA KEGIATAN OLAHRAGA</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">                
                <button type="button" class="btn btn-success fa fa-plus" data-toggle="modal" data-target="#modal-add-olahraga-pegawai">
                    Tambah Kegiatan Kebugaran
                </button>
                <a href="?<?php echo paramEncrypt('module=olahraga-pegawai&act=rekap-data-olahraga-pegawai'); ?>">
                    <button type="button" class="btn btn-instagram fa fa-search" title="cek rekapitulasi olahraga">                        
                    </button>
                </a>

               
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-olahraga-pegawai">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Tambah Kegiatan</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=olahraga-pegawai&act=add-olahraga-pegawai&id=' . $id_user . ''); ?>" method="post"> 
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <input type="text" class="form-control" id="datepicker2" placeholder="mm/dd/yyyy" name="tanggal_kinerja" required readonly>
                                        <label>Aktifitas</label>
                                        <select class="form-control select2" name="kd_skp"  data-placeholder="-Pilih Aktifitas-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Aktifitas-</option>
                                            <?php
                                            $tm_skp = bukaquery("select * from tm_skpk3");
                                            while ($row = fetch_array($tm_skp)) {
                                                echo"<option value=" . $row['kd_skp'] . ">" . $row['skp'] . " </option>";
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
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Aktifitas</th>
                                <th>Uraian</th>
                                <th>Waktu Mulai</th>
                                <th>Waktu Selesai</th>
                                <th>Tgl. Buat</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            
                                $tahun_setup = $thn_now;
                            $tt_olahraga = bukaquery("SELECT tm_pegawai.nip, tm_skpk3.skp, tt_olahraga.id_kinerja, tt_olahraga.uraian, tt_olahraga.waktu_mulai, tt_olahraga.waktu_akhir, tt_olahraga.date, tm_skpk3.waktu, tt_olahraga.kd_skp, tt_olahraga.tanggal_kinerja, tt_olahraga.`status`
                                                    FROM tt_olahraga
                                                    INNER JOIN tm_skpk3 ON tt_olahraga.kd_skp = tm_skpk3.kd_skp
                                                    INNER JOIN tm_pegawai ON tt_olahraga.id_user = tm_pegawai.id_user
                                                    where year(tt_olahraga.tanggal_kinerja)='$tahun_setup' and tt_olahraga.id_user=" . $id_user . " order by tt_olahraga.tanggal_kinerja, tt_olahraga.waktu_mulai Desc");
                            while ($row = fetch_array($tt_olahraga)) {
                                $no++;
                                $durasi = floor(((is_string($row['waktu_mulai']) ? strtotime($row['waktu_akhir']) : $row['waktu_akhir']) - (is_string($row['waktu_mulai']) ? strtotime($row['waktu_mulai']) : $row['waktu_mulai'])) / 60);
                                ?>

                                <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_kinerja']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=olahraga-pegawai&act=delete-olahraga&id=' . $row['id_kinerja'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Delete Data Kegiatan</h4>
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
                                <td><?php echo $row['skp']; ?></td>
                                <td><?php echo $row['uraian'];?></td>
                                <td><?php echo $row['waktu_mulai']; ?></td>
                                <td><?php echo $row['waktu_akhir']; ?></td>
                                <td><?php echo FormatTgl('d/m/y H:i:s', $row['date']); ?></td>
                                <td>
                                    <a href="?<?php echo paramEncrypt('module=olahraga-pegawai&act=update-olahraga-pegawai&id=' . $row['id_kinerja'] . '') ?>"><button type="submit" class="btn-xs btn-warning fa fa-edit"></button></a>
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

    case "rekap-data-olahraga-pegawai":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  REKAPITULASI DATA KEGIATAN OLAHRAGA</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">
                <a href="?<?php echo paramEncrypt('module=olahraga-pegawai&act=list-data-olahraga-pegawai'); ?>">
                    <button type="button" class="btn btn-warning fa fa-undo" title="Kembali"> Kembali                       
                    </button>
                </a>
                <br><br>
                <form action="" method="post">
                    <div class="form-group">
                        <table align="">
                            <tr> 
                                <td>
                                    <label>Bulan</label>              
                                </td>
                                <td>
                                    <label>Tahun</label> 
                                </td>
                                <td></td>
                            </tr>
                            <tr> 
                                <td>
                                    <select class="form-control select2" name="bulan"  data-placeholder="-Pilih Bulan-"  required>
                                        <?php loadBln('-Pilih Bulan-'); ?>               
                                    </select></td>
                                <td>
                                    <select class="form-control select2" name="tahun"  data-placeholder="-Pilih Tahun-"  required>
                                        <?php loadThn('-Pilih Tahun-'); ?>               
                                    </select>  
                                </td>
                                <td>
                                    <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
                <?php
                $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : null;
                $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : null;
                $tmtcek = FormatTgl("Y-m-d", $tahun_post . "-" . $bulan_post . "-01");
                if ($bulan_post == 0 OR $tahun_post == 0) {
                    ?>
                    <div class="box-header with-border">  
                        <h3 class="box-title fa fa-list"> <label>Bulan dan Tahun Tidak Sesuai</label></h3>
                        <div class="box-tools pull-right">
                        </div>                
                    </div>
                <?php } else {
                    ?>
                    <div class="box-header with-border">  
                        <h3 class="box-title fa fa-list center-block"> 
                            <label align="center">Rekapitulasi Olaharaga <?php echo konversiBulanTahun($tmtcek); ?></label>
                        </h3>
                        <div class="box-tools pull-right">
                        </div>                         
                    </div>
                <?php }
                ?>
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
                                <th>Tgl. Buat</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $tt_kinerja = bukaquery("SELECT tm_pegawai.nip, tm_skpk3.skp, tt_olahraga.id_kinerja, tt_olahraga.uraian, tt_olahraga.waktu_mulai, tt_olahraga.waktu_akhir, tt_olahraga.date, tm_skpk3.waktu, tt_olahraga.kd_skp, tt_olahraga.tanggal_kinerja, tt_olahraga.`status`, tt_olahraga.validasi 
                                                    FROM tt_olahraga
                                                    INNER JOIN tm_skpk3 ON tt_olahraga.kd_skp = tm_skpk3.kd_skp
                                                    INNER JOIN tm_pegawai ON tt_olahraga.id_user = tm_pegawai.id_user
                                                    where month(tt_olahraga.tanggal_kinerja)=" . FormatTgl('m', $tmtcek) . " and year(tt_olahraga.tanggal_kinerja)=" . FormatTgl('Y', $tmtcek) . " and tt_olahraga.id_user=" . $id_user . " order by tt_olahraga.tanggal_kinerja, tt_olahraga.waktu_mulai Desc");
                            while ($row = fetch_array($tt_kinerja)) {
                                $no++;
                                ?>
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo FormatTgl('d-m-Y', $row['tanggal_kinerja']); ?></td>
                                    <td><?php echo $row['skp'] ; ?></td>
                                    <td><?php echo $row['uraian']; ?></td>
                                    <td><?php echo $row['waktu_mulai']; ?></td>
                                    <td><?php echo $row['waktu_akhir']; ?></td>
                                    <td><?php echo FormatTgl('d/m/y H:i:s', $row['date']); ?></td>
                                    <td>
                                        <?php if ($row['validasi'] == 'T') { ?>                                            
                                            <i title="Belum/Tidak di Validasi" class="fa fa-thumbs-o-down"></i>
                                        <?php } else { ?>
                                            <i title="Tervalidasi" class="fa fa-thumbs-o-up"></i>
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

    case"update-olahraga-pegawai":
        $id = isset($url['id']) ? $url['id'] : null;
        $tt_olahraga = fetch_array(bukaquery("SELECT tm_pegawai.nip, tm_skpk3.skp, tt_olahraga.id_kinerja, tt_olahraga.uraian, tt_olahraga.waktu_mulai, tt_olahraga.waktu_akhir, tt_olahraga.date, tm_skpk3.waktu, tt_olahraga.kd_skp, tt_olahraga.tanggal_kinerja, tt_olahraga.`status`
                                                    FROM tt_olahraga
                                                    INNER JOIN tm_skpk3 ON tt_olahraga.kd_skp = tm_skpk3.kd_skp
                                                    INNER JOIN tm_pegawai ON tt_olahraga.id_user = tm_pegawai.id_user
                                                    where tt_olahraga.id_kinerja='$id'"));
        $tanggal = date('m/d/Y', strtotime($tt_olahraga['tanggal_kontrak']));
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-edit">  UPDATE DATA KEGIATAN OLAHRAGA</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">  
                <form action="<?php echo $aksi . paramEncrypt('module=olahraga-pegawai&act=update-olahraga-pegawai&id=' . $tt_olahraga['id_kinerja'] . ''); ?>" method="POST" role="form">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="text" class="form-control" value="<?php echo FormatTgl('m/d/Y', $tt_olahraga['tanggal_kinerja']); ?>" name="tanggal_kinerja" id="datepicker2" required="">
                        <label>Aktifitas</label>
                        <select class="form-control select2" name="kd_skp"  data-placeholder="-Pilih Aktifitas-" style="width: 100%;" required>
                            <option selected="selected" value="">-Pilih Aktifitas-</option>
                            <?php
                            if ($tt_olahraga['status'] == 'Utama') {
                                $tm_skptahunan = bukaquery("SELECT tt_skptahunan.kd_skp, tm_skpk3.skp, tm_skpk3.waktu
                                                                                        FROM tt_skptahunan INNER JOIN tm_skpk3 ON tt_skptahunan.kd_skp = tm_skpk3.kd_skp where tt_skptahunan.id_user=" . $id_user . " ");
                                while ($skp = fetch_array($tm_skptahunan)) {
                                    if ($tt_olahraga['kd_skp'] == $skp['kd_skp']) {
                                        echo"<option value=" . $skp['kd_skp'] . " selected=" . $tt_olahraga['kd_skp'] . ">" . $skp['skp'] . " [" . $skp['waktu'] . " Menit]</option>";
                                    } else {
                                        echo"<option value=" . $skp['kd_skp'] . ">" . $skp['skp'] . " [" . $skp['waktu'] . " Menit]</option>";
                                    }
                                }
                            } else {
                                $tm_skp = bukaquery("select * from tm_skpk3 ");
                                while ($skp = fetch_array($tm_skp)) {
                                    if ($tt_olahraga['kd_skp'] == $skp['kd_skp']) {
                                        echo"<option value=" . $skp['kd_skp'] . " selected=" . $tt_olahraga['kd_skp'] . ">" . $skp['skp'] . " </option>";
                                    } else {
                                        echo"<option value=" . $skp['kd_skp'] . ">" . $skp['skp'] . "</option>";
                                    }
                                }
                            }
                            ?>               
                        </select>
                        <label>Uraian Aktifitas</label>
                        <textarea class="form-control" name="uraian" rows="5"><?php echo $tt_olahraga['uraian']; ?></textarea>
                        <label>Waktu Mulai</label>
                        <div class="input-group">
                            <input type="text"  class="form-control" name="waktu_mulai" value="<?php echo $tt_olahraga['waktu_mulai'] ?>" data-inputmask='"mask": "99:99:99"' data-mask required autofocus>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                            </span>
                        </div>
                        <label>Jam Selesai </label>
                        <div class="input-group">
                            <input type="text"  class="form-control"  name="waktu_akhir" value="<?php echo $tt_olahraga['waktu_akhir'] ?>" data-inputmask='"mask": "99:99:99"' data-mask required>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                            </span>
                        </div>
                    </div>
                    <div class="box-footer">
                        <a href="?<?php echo paramEncrypt('module=olahraga-pegawai&act=list-data-olahraga-pegawai') ?>"><button type="button" class="btn btn-warning fa fa-undo" > Kembali</button></a>
                        <button type="submit" class="btn btn-primary fa fa-save"> Update</button>
                    </div>
                </form>
            </div>
        </div>
        <?php
        break;

  case "list-data-maping-olahraga":
        $tampil_olahraga = 'yes';
        $cuti = bukaquery("SELECT skp, tt_olahraga.tanggal_kinerja, tm_pegawai.nama_pegawai
                                        FROM tt_olahraga
                                        INNER JOIN tm_pegawai ON tm_pegawai.id_user=tt_olahraga.id_user
                                                                                INNER JOIN tm_skpk3 on tm_skpk3.kd_skp = tt_olahraga.kd_skp");
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA OLAHRAGA PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div id="calendar"></div>
            </div>
        </div>

    <?php
        break;

    case "list-data-maping-olahraga-pegawai-laporan":
        $year = isset($_POST['tahun']) ? $_POST['tahun'] : $thn_now;
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LAPORAN OLAHRAGA PEGAWAI PERTAHUN </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-2">
                    <form action="" method="post" id="search">
                        <div class="input-group ">
                            <select class="form-control select2" name="tahun" id="tahun" data-placeholder="-Pilih Tahun-" required>
                                <?php loadThnnow($year); ?>
                            </select>
                            <span class="input-group-btn">
                                <button type="submit" name="search" id="search-btn" class="btn btn-info btn-flat"><i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                    <!--</div>-->
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box-body table-responsive">
                            <table id="laporan" class="table table-bordered table-striped" width='100%'>
                                <thead>
                                    <tr>
                                        <td>No</td>
                                        <td>Nama Pegawai</td>
                                        <td>Unit</td>
                                        <td>Uji Kebugaran Karyawan</td>
                                        <td>Medical Check Up</td>
                                        <td>Senam Kebugaran</td>
                                        <td>Sepak Bola</td>
                                        <td>Tenis Meja</td>
                                        <td>Bulu Tangkis</td>
                                        <td>Badminton</td>
                                        <td>Jogging/Jalan Sehat</td>
                                    </tr>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $thnini = date('Y');
                                    $thnkmrn = date('Y') -1;
                                    $sql = bukaquery("SELECT
                                        tm_pegawai.nama_pegawai,
                                        tm_unit.nama_unit,
                                                                                COUNT(CASE WHEN  YEAR(tt_olahraga.tanggal_kinerja)=' $year' AND kd_skp = '20230001' THEN kd_skp END) as a,
                                                                                COUNT(CASE WHEN  YEAR(tt_olahraga.tanggal_kinerja)=' $year' AND kd_skp = '20230002' THEN kd_skp END) as b,
                                                                                COUNT(CASE WHEN  YEAR(tt_olahraga.tanggal_kinerja)=' $year' AND kd_skp = '20230003' THEN kd_skp END) as c,
                                                                                COUNT(CASE WHEN  YEAR(tt_olahraga.tanggal_kinerja)=' $year' AND kd_skp = '20230004' THEN kd_skp END) as d,
                                                                                COUNT(CASE WHEN  YEAR(tt_olahraga.tanggal_kinerja)=' $year' AND kd_skp = '20230005' THEN kd_skp END) as e,
                                                                                COUNT(CASE WHEN  YEAR(tt_olahraga.tanggal_kinerja)=' $year' AND kd_skp = '20230006' THEN kd_skp END) as f,
                                                                                COUNT(CASE WHEN  YEAR(tt_olahraga.tanggal_kinerja)=' $year' AND kd_skp = '20230007' THEN kd_skp END) as g,
                                                                                COUNT(CASE WHEN  YEAR(tt_olahraga.tanggal_kinerja)=' $year' AND kd_skp = '20230008' THEN kd_skp END) as h
                                        FROM
                                        tm_pegawai
                                        INNER JOIN tm_unit ON tm_unit.id_unit=tm_pegawai.id_unit
                                                                                LEFT JOIN tt_olahraga on tm_pegawai.id_user=tt_olahraga.id_user
                                        WHERE tm_pegawai.`status`='AKTIF'
                                        GROUP BY tm_pegawai.id_user ORDER BY tm_unit.nama_unit, tm_pegawai.nama_pegawai ASC ");
                                    while ($row = fetch_array($sql)) {
                        
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['nama_pegawai']; ?></td>
                                            <td><?= $row['nama_unit']; ?></td>
                                            <td><?= $row['a']; ?></td>
                                            <td><?= $row['b']; ?></td>
                                            <td><?= $row['c']; ?></td>
                                            <td><?= $row['d']; ?></td>
                                            <td><?= $row['e']; ?></td>
                                            <td><?= $row['f']; ?></td>
                                            <td><?= $row['g']; ?></td>
                                            <td><?= $row['h']; ?></td>
                                        </tr>

                                    <?php } ?>
                                </tbody>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
        break;
}
?>


