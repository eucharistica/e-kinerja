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
                <h3 class="box-title fa fa-list">  LIST DATA KESEHATAN PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">                
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-k3">
                    Tambah Aktifitas
                </button>

                <!--Modal Add IMT -->
                <div class="modal fade" id="modal-add-k3">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Tambah Data IMT </h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=k3&act=add-imt&id=' . $id_user . ''); ?>" method="post"> 
                                <div class="modal-body">
                                    <div class="form-group">
                                        <input class="hidden" name="id_user" value="<?php echo $id_user ?>" required/>
                                        <label>Tanggal Pelaporan</label>
                                        <input type="date" class="form-control" placeholder="mm/dd/yyyy" name="tgl_kegiatan" required>
                                        <br>
                                        <label>Aktifitas</label>
                                        <select class="form-control select2" name="id_kegiatan"  data-placeholder="-Pilih Aktifitas-" style="width: 100%;" required>
                                            <option selected="selected" value="1">MCU Pegawai</option>               
                                        </select>
                                        <br>
                                        <label>Berat Badan</label>
                                        <input class="form-control" name="bb" placeholder="Masukan Berat Badan (kg)      --   Contoh  ' 65 '  ( 65kg )   --" rows="5" required/>
                                        <br>
                                        <label>Tinggi Badan</label>
                                        <input class="form-control" name="tb" placeholder="Masukan Tinggi Badan (m)      --   Contoh  ' 1.65 '  ( 165cm )   --" rows="5" required/>
                                        
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
                    <table id="imt" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
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

                                        $imt = bukaquery("SELECT * FROM tt_imt,tm_pegawai WHERE tt_imt.id_user = tm_pegawai.id_user  AND tt_imt.id_user =" . $id_user ." ");
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
        </div>
        <?php
        break;

}
?>


