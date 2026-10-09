<?php
$aksi = "module/kesling/aksi-del-kesling.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;

    case "list-data-kebersihan":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST DATA INPUTAN KEBERSIHAN RUANGAN</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>


                <!-- /.box-header -->                
                <div class="box-body table-responsive">
                    <table id="imt" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><center>No</center></th>
                                <th><center>Ruang / Unit</center></th>
                                <th><center>Detail Lokasi</center></th>
                                <th><center>Lantai</center></th>
                                <th><center>Tanggal</center></th>
                                <th><center>Nama Petugas</center></th>
                                <th><center>Shift</center></th>
                                <th><center>Status</center></th>
                                <th><center>Keterangan</center></th>
                                <th><center>Validasi Unit</center></th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                                    <?php
                                    $no = 0;

                                        $kesling = bukaquery("SELECT * FROM kebersihan,tm_pegawai,tm_unit WHERE kebersihan.id_user = tm_pegawai.id_user and tm_pegawai.id_unit=tm_unit.id_unit ORDER BY kebersihan.id_kebersihan DESC");
                                        $no = 1;
                                        while ($b = mysqli_fetch_array($kesling)){
                                    ?>

                            <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-hapus-<?php echo $b['id_kebersihan']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=kebersihan&act=delete-kebersihan&id=' . $b['id_kebersihan'] . ''); ?>" method="POST" role="form">
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
                                        <td><?php echo $b['nama_unit'] ?></td>
                                        <td><?php echo $b['ruangan'] ?></td>
                                        <td><?php echo $b['lantai'] ?></td>
                                        <td><?php echo $b['tanggal'] ?></td>
                                        <td><?php echo $b['nama_petugas'] ?></td>
                                        <td><?php echo $b['shift'] ?></td>
                                        <td><?php echo $b['status1'] ?></td>
                                        <td><?php echo $b['keterangan'] ?></td>
                                        <td><?php echo $b['valid'] ?></td>
                                        <td><spain data-toggle="modal" data-target="#modal-hapus-<?php echo $b['id_kebersihan']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"/></spain>
                                        </td>
                                    </tr>
                                    <?php $no++; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
        break;

}
?>


