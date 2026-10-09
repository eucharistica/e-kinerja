<?php
$aksi = "module/kesling/aksi-input-kesling.php?";
$aksidel = "module/kesling/aksi-del-kesling.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;

    case "list-data-kesling-unit":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST DATA KESLING DI UNIT</h3>
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
                                <th><center>Detail lokasi</center></th>
                                <th><center>Tgl. Penilaian</center></th>
                                <th><center>Kegiatan</center></th>
                                <th><center>Jenis</center></th>
                                <th><center>Hasil</center></th>
                                <th><center>Standar</center></th>
                                <th><center>Kesimpulan</center></th>
                                <th><center>Rekomendasi Kesling</center></th>
                                <th><center>Rekomendasi PPI</center></th>
                                <th><center>Tindak Lanjut Unit</center></th>
                                <th><center>Action</center></th>
                            </tr>
                        </thead>
                        <tbody>
                                    <?php
                                    $no = 0;

                                        $kesling = bukaquery("SELECT * FROM tt_kesling,tm_pegawai,kegiatan WHERE tt_kesling.id_user = tm_pegawai.id_user AND tt_kesling.id_kegiatan=kegiatan.id_kegiatan AND tt_kesling.id_user =" . $id_user ." ");
                                        $no = 1;
                                        while ($b = mysqli_fetch_array($kesling)){
                                    ?>
                            <!-- Tambah modal tindakan unit -->
                            <div class="modal fade" id="modal-ubah-<?php echo $b['id_kesling']; ?>"  role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksidel . paramEncrypt('module=view-kesling-unit&act=unit-kesling&id=' . $b['id_kesling'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Tambah Tindak Lanjut Yang Dilakukan Unit</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label>Tindak Lanjut Unit</label>
                                                        <input type="text" class="form-control" name="t_unit" value="<?php echo $b['t_unit']; ?>" required>
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
                            <!-- Tutup modal tindakan unit -->

                                    <tr>
                                        <td><?php echo $no; ?></td>
                                        <td><?php echo $b['detail'] ?></td>
                                        <td><?php echo $b['tgl_hasil'] ?></td>
                                        <td><?php echo $b['nama_kegiatan'] ?></td>
                                        <td><?php echo $b['jenis'] ?></td>
                                        <td><?php echo $b['hasil'] ?></td>
                                        <td><?php echo $b['standar'] ?></td>
                                        <td><?
                                        if ($b['kesimpulan']=='Memenuhi Syarat') {
                                        echo '<font color="green">'.$b['kesimpulan'].'</p>';
                                        }else{
                                        echo '<font color="red">'.$b['kesimpulan'].'</p>';
                                        }
                                        ?></td>
                                        <td><?php echo $b['r_kesling'] ?></td>
                                        <td><?php echo $b['r_ppi'] ?></td>
                                        <td><?php echo $b['t_unit'] ?></td>
                                        <td><span data-toggle="modal" data-target="#modal-ubah-<?php echo $b['id_kesling']; ?>" title="Tambah Tindakan Unit" class="btn btn-info fa fa-plus"></span>
                                            <a href="https://rsudmatraman.jakarta.go.id/helpdesk/index.php?a=add"  title="Butuh Bantuan Unit Lain" class="btn btn-info fa fa-ticket"></span>
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


