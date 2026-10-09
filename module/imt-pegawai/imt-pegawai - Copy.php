<?php
$aksi = "module/imt-pegawai/aksi-imt-pegawai?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;


    case "list-data-imt-pegawai":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST DATA INDEX MASSA TUBUH (IMT) PEGAWAI</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if (getOne("select count(id_imt) as total from tt_imt where year(tt_imt.tgl_imt)='$thn'") <= 12) { ?>
                            <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-imt">
                                Buat Data IMT
                            </button>
                        <?php
                        } else {
                        }
                        ?>
                        <!--Modal Add SKP -->
                        <div class="modal fade" id="modal-add-imt">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-plus"> Tambah Data IMT</h4>
                                    </div>
                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=imt-pegawai&act=add-imt-pegawai&id=' . $id_user . ''); ?>" method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <div>
                                                    <label>Tanggal</label>
                                                    <input type="text" class="form-control" id="datepicker1" placeholder="mm/dd/yyyy" name="date" required readonly>
                                                    <!-- <select class="form-control" name="date" data-placeholder="-Pilih Bulan-" style="width: 100%;" required>
                                                        <?php loadBln('-Pilih Bulan-'); ?>
                                                    </select> -->
                                                </div>
                                                <!-- <div>
                                                    <p></p>
                                                    <label>Tahun</label>
                                                    <input type="text" class="form-control" name="tahun" placeholder="" value="<?php echo date('Y'); ?>" readonly>
                                                </div> -->
                                                <div>
                                                    <p></p>
                                                    <label>Berat Badan</label>
                                                    <input type="text" maxlength="3" class="form-control" name="bb" placeholder="berat badan dalam kilogram (kg)" required>
                                                </div>
                                                <div>
                                                    <p></p>
                                                    <label>Tinggi Badan</label>
                                                    <input type="text" maxlength="3" class="form-control" name="tb" placeholder="tinggi badan dalam sentimeter (cm)" required>
                                                </div>
                                                <div>
                                                    <p></p>
                                                    <label>Lingkar Perut</label>
                                                    <input type="text" maxlength="3" class="form-control" name="lp" placeholder="lingkar perut dalam sentimeter (cm)" required>
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
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal IMT</th>
                                        <th>Berat Badan</th>
                                        <th>Tinggi Badan</th>
                                        <th>Lingkar Perut</th>
                                        <th>Nilai IMT</th>
                                        <th>Hasil IMT</th>
                                        <th>Saran</th>
                                        <!-- <th>Konsultasi Gizi</th> -->
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $sql_imt = bukaquery("select * from tt_imt where tt_imt.id_user=" . $id_user . " order by tgl_imt asc");
                                    while ($row = fetch_array($sql_imt)) {
                                        $no++;
                                    ?>
                                        <!-- Edit Modal SKP -->
                                        <div class="modal fade" id="modal-ubah-<?php echo $row['id_imt']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=imt-pegawai&act=update-imt-pegawai&id=' . $row['id_imt'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Update Data IMT</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <div>
                                                                        <label>Tanggal</label>
                                                                        <!-- <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" style="width: 100%;" disabled required>
                                                                            <?php updateloadBln(date('m', strtotime($row['bulan']))); ?>
                                                                        </select> -->
                                                                        <input type="text" class="form-control" value="<?php echo FormatTgl('m/d/Y', $row['tgl_imt']); ?>" name="date" required readonly>
                                                                    </div>
                                                                    <!-- <div>
                                                                        <label>Tahun</label>
                                                                        <input type="text" class="form-control" name="tahun" placeholder="" value="<?php echo date('Y', strtotime($row['bulan'])); ?>" readonly>
                                                                    </div> -->
                                                                    <div>
                                                                        <p></p>
                                                                        <label>Berat Badan</label>
                                                                        <input type="text" maxlength="3" class="form-control" name="bb" value="<?php echo $row['bb']; ?>" placeholder="berat badan dalam kilogram (kg)" required>
                                                                    </div>
                                                                    <div>
                                                                        <p></p>
                                                                        <label>Tinggi Badan</label>
                                                                        <input type="text" maxlength="3" class="form-control" name="tb" value="<?php echo $row['tb']; ?>" placeholder="tinggi badan dalam sentimeter (cm)" required>
                                                                    </div>
                                                                    <div>
                                                                        <p></p>
                                                                        <label>Lingkar Perut</label>
                                                                        <input type="text" maxlength="3" class="form-control" name="lp" value="<?php echo $row['lp']; ?>" placeholder="lingkar perut dalam sentimeter (cm)" required>
                                                                    </div>
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
                                        <div class="modal fade" id="modal-hapus-<?php echo $row['id_imt']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?php echo $aksi . paramEncrypt('module=imt-pegawai&act=delete-imt-pegawai&id=' . $row['id_imt'] . ''); ?>" method="POST" role="form">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Hapus Data IMT</h4>
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
                                            <td><?php echo FormatTgl('d-m-Y', $row['tgl_imt']); ?></td>
                                            <td><?php echo $row['bb']; ?></td>
                                            <td><?php echo $row['tb']; ?></td>
                                            <td><?php echo $row['lp']; ?></td>
                                            <td><?php echo $row['imt']; ?></td>
                                            <td><?php echo $row['h_imt']; ?></td>
                                            <td><?php echo $row['saran']; ?></td>
                                            <!-- <td></td> -->
                                            <td>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_imt']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_imt']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
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

    case "list-data-maping-imt-pegawai-laporan":
        $year = isset($_POST['tahun']) ? $_POST['tahun'] : $thn_now;
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LAPORAN IMT PEGAWAI PERTAHUN </h3>
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
                                        <td>Jan</td>
                                        <td>Feb</td>
                                        <td>Mar</td>
                                        <td>Apr</td>
                                        <td>Mei</td>
                                        <td>Jun</td>
                                        <td>Jul</td>
                                        <td>Aug</td>
                                        <td>Sep</td>
                                        <td>Okt</td>
                                        <td>Nov</td>
                                        <td>Des</td>
                                        <td>Rata-Rata</td>
                                    </tr>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $thnini = date('Y');
                                    $thnkmrn = date('Y') -1;
                                    $sql = bukaquery("SELECT
                                        tm_pegawai.nama_pegawai,
                                        tm_pegawai.id_user,
                                        tm_unit.nama_unit,
                                        ($year) as tahun,
                                        MAX(CASE WHEN MONTH(tt_imt.tgl_imt) BETWEEN 1 AND 1  AND YEAR(tt_imt.tgl_imt)='$year' THEN imt ELSE 0 END) as jan,
                                        MAX(CASE WHEN MONTH(tt_imt.tgl_imt) BETWEEN 2 AND 2  AND YEAR(tt_imt.tgl_imt)='$year' THEN imt ELSE 0 END) as feb,
                                        MAX(CASE WHEN MONTH(tt_imt.tgl_imt) BETWEEN 3 AND 3  AND YEAR(tt_imt.tgl_imt)='$year' THEN imt ELSE 0 END) as mar,
                                        MAX(CASE WHEN MONTH(tt_imt.tgl_imt) BETWEEN 4 AND 4  AND YEAR(tt_imt.tgl_imt)='$year' THEN imt ELSE 0 END) as apr,
                                        MAX(CASE WHEN MONTH(tt_imt.tgl_imt) BETWEEN 5 AND 5  AND YEAR(tt_imt.tgl_imt)='$year' THEN imt ELSE 0 END) as mei,
                                        MAX(CASE WHEN MONTH(tt_imt.tgl_imt) BETWEEN 6 AND 6  AND YEAR(tt_imt.tgl_imt)='$year' THEN imt ELSE 0 END) as jun,
                                        MAX(CASE WHEN MONTH(tt_imt.tgl_imt) BETWEEN 7 AND 7  AND YEAR(tt_imt.tgl_imt)='$year' THEN imt ELSE 0 END) as jul,
                                        MAX(CASE WHEN MONTH(tt_imt.tgl_imt) BETWEEN 8 AND 8  AND YEAR(tt_imt.tgl_imt)='$year' THEN imt ELSE 0 END) as aug,
                                        MAX(CASE WHEN MONTH(tt_imt.tgl_imt) BETWEEN 9 AND 9  AND YEAR(tt_imt.tgl_imt)='$year' THEN imt ELSE 0 END) as sep,
                                        MAX(CASE WHEN MONTH(tt_imt.tgl_imt) BETWEEN 10 AND 10  AND YEAR(tt_imt.tgl_imt)='$year' THEN imt ELSE 0 END) as okt,
                                        MAX(CASE WHEN MONTH(tt_imt.tgl_imt) BETWEEN 11 AND 11  AND YEAR(tt_imt.tgl_imt)='$year' THEN imt ELSE 0 END) as nov,
                                        MAX(CASE WHEN MONTH(tt_imt.tgl_imt) BETWEEN 12 AND 12  AND YEAR(tt_imt.tgl_imt)='$year' THEN imt ELSE 0 END) as des,
                                        AVG(CASE WHEN MONTH(tt_imt.tgl_imt) BETWEEN 1 AND 12  AND YEAR(tt_imt.tgl_imt)='$year' THEN imt ELSE 0 END) as avg
                                        FROM
                                        tm_pegawai
                                        INNER JOIN tm_unit ON tm_unit.id_unit=tm_pegawai.id_unit
                                        INNER JOIN tt_imt on tm_pegawai.id_user=tt_imt.id_user
                                        WHERE tm_pegawai.`status`='AKTIF'
                                        GROUP BY tm_pegawai.id_user ORDER BY tm_unit.nama_unit ASC ");
                                    while ($row = fetch_array($sql)) {
                        
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['nama_pegawai']; ?></td>
                                            <td><?= $row['nama_unit']; ?></td>
                                            <td><?= round($row['jan'],2); ?></td>
                                            <td><?= round($row['feb'],2); ?></td>
                                            <td><?= round($row['mar'],2); ?></td>
                                            <td><?= round($row['apr'],2); ?></td>
                                            <td><?= round($row['mei'],2) ?></td>
                                            <td><?= round($row['jun'],2); ?></td>
                                            <td><?= round($row['jul'],2); ?></td>
                                            <td><?= round($row['aug'],2); ?></td>
                                            <td><?= round($row['sep'],2); ?></td>
                                            <td><?= round($row['okt'],2); ?></td>
                                            <td><?= round($row['nov'],2); ?></td>
                                            <td><?= round($row['des'],2); ?></td>
                                            <td><?= round($row['avg'],2); ?></td>
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


