<?php
$aksi = "module/k3/aksi-input-k3-pegawai.php?";
$aksidel = "module/k3/aksi-del-k3-pegawai.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;

    case "list-data-k3":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST DATA KEGIATAN PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>               
                <div class="box-body table-responsive">
                    <table id="imt" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pegawai</th>
                                <th>Unit</th>
                                <th>Kegiatan</th>
                                <th>Berat Badan (kg)</th>
                                <th>Tinggi Badan (m2)</th>
                                <th>IMT (kg ÷ m2)</th>
                                <th>Hasil IMT</th>
                                <th>Tgl. Pelaporan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                                    <?php
                                    $no = 0;

                                        $imt = bukaquery("SELECT * FROM tt_imt,tm_pegawai,kegiatan WHERE tt_imt.id_user = tm_pegawai.id_user and tt_imt.id_kegiatan = kegiatan.kode_kegiatan");
                                        $no = 1;
                                        while ($b = mysqli_fetch_array($imt)){
                                    ?>

                                    <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-hapus-<?php echo $b['id_imt']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksidel . paramEncrypt('module=k3&act=delete-imt&id=' . $b['id_imt'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Delete Hasil</h4>
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
                                        <td><?php echo $b['nama_pegawai'] ?></td>
                                        <td><?php echo $b['id_unit'] ?></td>
                                        <td><?php echo $b['nama_kegiatan'] ?></td>
                                        <td><?php echo $b['bb'] ?></td>
                                        <td><?php echo $b['tb'] ?></td>
                                        <td><?php echo $b['imt'] ?></td>
                                        <td><?php echo $b['h_imt'] ?></td>
                                        <td><?php echo $b['tgl_kegiatan'] ?></td>
                                        <td><spain data-toggle="modal" data-target="#modal-hapus-<?php echo $b['id_imt']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"/>
                                        </td>
                                    </tr>
                                    <?php $no++; } ?>
                                 </tbody>
                                </table>
                            </div>
                        </div>


        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  JUMLAH STATUS</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>               
                <div class="box-body table-responsive">
                    <div class="modal-header">
                    <?php
                    /*-------- menghitung total imt -----------*/
                    $imt1 = bukaquery("SELECT *, count(tt_imt.id_imt) as jml_imtk from tt_imt where h_imt = 'Kurus'");
                    $r = mysqli_fetch_array($imt1);
                    $jmlimtk = $r['jml_imtk'];

                    /*-------- menghitung total imt -----------*/
                    $imt2 = bukaquery("SELECT *, count(tt_imt.id_imt) as jml_imtsk from tt_imt where h_imt = 'Sangat Kurus'");
                    $r = mysqli_fetch_array($imt2);
                    $jmlimtsk = $r['jml_imtsk'];

                    /*-------- menghitung total imt -----------*/
                    $imt3 = bukaquery("SELECT *, count(tt_imt.id_imt) as jml_imtn from tt_imt where h_imt = 'Normal'");
                    $r = mysqli_fetch_array($imt3);
                    $jmlimtn = $r['jml_imtn'];

                    /*-------- menghitung total imt -----------*/
                    $imt4 = bukaquery("SELECT *, count(tt_imt.id_imt) as jml_imtg from tt_imt where h_imt = 'Gemuk'");
                    $r = mysqli_fetch_array($imt4);
                    $jmlimtg = $r['jml_imtg'];

                    /*-------- menghitung total imt -----------*/
                    $imt5 = bukaquery("SELECT *, count(tt_imt.id_imt) as jml_imtob1 from tt_imt where h_imt = 'Obesitas Level 1'");
                    $r = mysqli_fetch_array($imt5);
                    $jmlimtob1 = $r['jml_imtob1'];

                    /*-------- menghitung total imt -----------*/
                    $imt6 = bukaquery("SELECT *, count(tt_imt.id_imt) as jml_imtob2 from tt_imt where h_imt = 'Obesitas Level 2'");
                    $r = mysqli_fetch_array($imt6);
                    $jmlimtob2 = $r['jml_imtob2'];

                    /*-------- menghitung total imt -----------*/
                    $imt7 = bukaquery("SELECT *, count(tt_imt.id_imt) as jml_imtob3 from tt_imt where h_imt = 'Obesitas Level 3'");
                    $r = mysqli_fetch_array($imt7);
                    $jmlimtob3 = $r['jml_imtob3']; 

                    ?> 
                    <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-yellow">
                     <div class="inner">
                        <h3><?php echo $jmlimtsk; ?></h3>
                        <p>Jumlah Pegawai Sangat Kurus</p>
                    </div>
                    <div class="icon">
                     <i class="ion ion-stats-bars"></i>
                    </div>
                    </div>
                    </div><!-- ./col -->  
                    <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                     <div class="inner">
                        <h3><?php echo $jmlimtk; ?></h3>
                        <p>Jumlah Pegawai Kurus</p>
                    </div>
                    <div class="icon">
                     <i class="ion ion-stats-bars"></i>
                    </div>
                    </div>
                    </div><!-- ./col --> 
                    <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-green">
                     <div class="inner">
                        <h3><?php echo $jmlimtn; ?></h3>
                        <p>Jumlah Pegawai Normal</p>
                    </div>
                    <div class="icon">
                     <i class="ion ion-stats-bars"></i>
                    </div>
                    </div>
                    </div><!-- ./col -->
                    <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-orange">
                     <div class="inner">
                        <h3><?php echo $jmlimtg; ?></h3>
                        <p>Jumlah Pegawai Gemuk</p>
                    </div>
                    <div class="icon">
                     <i class="ion ion-stats-bars"></i>
                    </div>
                    </div>
                    </div><!-- ./col -->
                    <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-red">
                     <div class="inner">
                        <h3><?php echo $jmlimtob1; ?></h3>
                        <p>Jumlah Pegawai Obesitas Level 1</p>
                    </div>
                    <div class="icon">
                     <i class="ion ion-stats-bars"></i>
                    </div>
                    </div>
                    </div><!-- ./col -->
                    <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-red">
                     <div class="inner">
                        <h3><?php echo $jmlimtob2; ?></h3>
                        <p>Jumlah Pegawai Obesitas Level 2</p>
                    </div>
                    <div class="icon">
                     <i class="ion ion-stats-bars"></i>
                    </div>
                    </div>
                    </div><!-- ./col -->
                    <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-red">
                     <div class="inner">
                        <h3><?php echo $jmlimtob3; ?></h3>
                        <p>Jumlah Pegawai Obesitas Level 3</p>
                    </div>
                    <div class="icon">
                     <i class="ion ion-stats-bars"></i>
                    </div>
                    </div>
                    </div><!-- ./col -->                       
                    </div>
                </div>
        </div>
        <?php
        break;
}
?>


