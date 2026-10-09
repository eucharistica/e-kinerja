<?php
$aksi = "module/kesling/aksi-del-kesling.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;

    case "list-data-kesling1":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST DATA HASIL LABORATORIUM</h3>
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
                                <th><center>Tgl. Penilaian</center></th>
                                <th><center>Kegiatan</center></th>
                                <th><center>Jenis</center></th>
                                <th><center>Hasil</center></th>
                                <th><center>Standar</center></th>
                                <th><center>Kesimpulan</center></th>
                                <th><center>Rekomendasi Kesling</center></th>
                                <th><center>Rekomendasi PPI</center></th>
                                <th><center>Tindak Lanjut Unit</center></th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                                    <?php
                                    $no = 0;

                                        $kesling = bukaquery("SELECT * FROM tt_kesling,tm_pegawai,tm_unit,kegiatan WHERE tt_kesling.id_user = tm_pegawai.id_user and tm_pegawai.id_unit=tm_unit.id_unit and tt_kesling.id_kegiatan=kegiatan.id_kegiatan");
                                        $no = 1;
                                        while ($b = mysqli_fetch_array($kesling)){
                                    ?>
                                    <!-- Tambah modal ppi -->
                            <div class="modal fade" id="modal-ubah-<?php echo $b['id_kesling']; ?>"  role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=view&act=update-kesling&id=' . $b['id_kesling'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Tambah Rekomendasi PPI</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label>Rekomendasi PPI</label>
                                                        <input type="text" class="form-control" name="r_ppi" value="<?php echo $b['r_ppi']; ?>" required>
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
                            <!-- Tambah modal ppi -->

                                    <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-hapus-<?php echo $b['id_kesling']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksidel . paramEncrypt('module=kesling&act=delete-kesling&id=' . $b['id_kesling'] . ''); ?>" method="POST" role="form">
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
                                        <td><span data-toggle="modal" data-target="#modal-ubah-<?php echo $b['id_kesling']; ?>" title="Tambah Rekomendasi PPI" class="btn btn-info fa fa-plus"></span>
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


