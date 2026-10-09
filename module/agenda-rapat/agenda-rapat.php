<?php
$aksi = "module/agenda-rapat/aksi-agenda-rapat.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;

    case "list-agenda-rapat":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST AGENDA </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">   
                <!-- /.box-header -->
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-helpdesk">
                    Buat Agenda
                </button>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-helpdesk">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Form Agenda</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=agenda-rapat&act=add-agenda'); ?>" method="post"> 
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <input type="text" name="tanggal" id="datepicker" class="form-control" required="" autocomplete="off">
                                        <label>Ruang</label>
                                        <input type="text" name="ruang" placeholder="Ruang" class="form-control" required="">
                                        <label>Waktu</label>
                                        <input type="text" name="waktu" class="form-control" data-inputmask='"mask": "99:99 - 99:99 WIB","placeholder":"hh:ii - hh:ii WIB"' data-mask  required="">
                                        <label>Kegiatan</label>  
                                        <textarea class="form-control" name="kegiatan" placeholder="Kegiatan" rows="5" required></textarea>
                                        <label>Catatan Tambahan</label>  
                                        <textarea class="form-control" name="note" placeholder="Catatan" rows="5"></textarea>
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
                <br><br>
                <div class="table-responsive">
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Ruang</th>
                                <th>Kegiatan</th>
                                <th>Note</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $sql_riwayat_diklat = bukaquery("SELECT * from tt_agenda");
                            while ($row = fetch_array($sql_riwayat_diklat)) {
                                $no++;
                                ?>     
                                <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-ubah-<?php echo $row['id_agenda']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=agenda-rapat&act=update-agenda&id=' . $row['id_agenda'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Update Laporan</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label>Tanggal</label>
                                                        <input type="text" name="tanggal" value="<?php echo FormatTgl('m/d/Y', $row['tanggal']); ?>" class="form-control" readonly="">
                                                        <label>Ruang</label>
                                                        <input type="text" name="ruang" placeholder="Ruang" value="<?php echo $row['ruang']; ?>" class="form-control" required="">
                                                        <label>Waktu</label>
                                                        <input type="text" name="waktu" class="form-control" data-inputmask='"mask": "99:99 - 99:99 WIB","placeholder":"hh:ii - hh:ii WIB"' value="<?php echo $row['waktu']; ?>" data-mask  required="">
                                                        <label>Kegiatan</label>  
                                                        <textarea class="form-control" name="kegiatan" placeholder="Kegiatan" rows="5" required><?php echo $row['kegiatan']; ?></textarea>
                                                        <label>Catatan Tambahan</label>  
                                                        <textarea class="form-control" name="note" placeholder="Catatan" rows="5"><?php echo $row['note']; ?></textarea>
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
                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_agenda']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=agenda-rapat&act=delete-aganda&id=' . $row['id_agenda'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Delete Agenda</h4>
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
                            <tr>
                                <td><?php echo $no; ?></td>
                                <td><?php echo $row['tanggal']; ?></td>
                                <td><?php echo $row['ruang'] . " <p>(" . $row['waktu'] . ")</p>"; ?></td>
                                <td><?php echo $row['kegiatan']; ?></td>
                                <td><?php echo $row['note']; ?></td>
                                <td>
                                    <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_agenda']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                    <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_agenda']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
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
}
?>


