<?php
$aksi = "module/helpdesk/aksi-helpdesk.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header('location:error.php');
        break;

    case "list-helpdesk":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">HELPDEK E-KINERJA</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">                
                <button type="button" class="btn btn-info fa fa-plus"> <a href="http://rsudmatraman.jakarta.go.id/helpdesk" target="_blank">Helpdesk Pegawai</a>
                </button>
                <!-- <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-helpdesk">
                    Buat Tiket
                </button> -->
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-helpdesk">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Form Laporan</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=helpdesk&act=add-helpdesk&id=' . $nip . ''); ?>" method="post"> 
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Jenis Tiket</label>
                                        <?php echo enumDropdown("tt_helpdesk", "jenis", "", "-Pilih Jenis-"); ?>
                                        <br>
                                        <label>Narasi Tiket</label>  
                                        <textarea class="form-control" name="narasi" placeholder="" rows="5" required></textarea>
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
                                <th>No Tiket</th>
                                <th>Tanggal Tiket</th>
                                <th>Jenis Tiket</th>
                                <th>Narasi</th>
                                <th>Tanggapan</th>
                                <th>Akun</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $sql_helpdesk = bukaquery("SELECT tt_helpdesk.jenis, tt_helpdesk.narasi,tt_helpdesk.respon, tt_helpdesk.`status`, tt_helpdesk.date, tm_pegawai.nama_pegawai, tt_helpdesk.no_tiket, tt_helpdesk.nip 
                                    FROM tt_helpdesk  INNER JOIN tm_pegawai on tm_pegawai.nip=tt_helpdesk.nip where tm_pegawai.nip='$nip'");
                            while ($row = fetch_array($sql_helpdesk)) {
                                $no++;
                                ?>
                                <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-ubah-<?php echo $row['no_tiket']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=helpdesk&act=update-helpdesk&id=' . $row['no_tiket'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Update Laporan</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label>Jenis Tiket</label>
                                                        <?php echo UpdateEnumDropdown("tt_helpdesk", "jenis", $row['jenis'], ""); ?>
                                                        <label>Narasi Tiket</label>  
                                                        <textarea class="form-control" name="narasi" placeholder="" rows="3" required><?php echo $row['narasi']; ?></textarea>
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
                            <div class="modal fade" id="modal-hapus-<?php echo $row['no_tiket']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=helpdesk&act=delete-helpdesk&id=' . $row['no_tiket'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Delete Laporan</h4>
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
                                <td><?php echo $row['no_tiket']; ?></td>
                                <td><?php echo $row['date']; ?></td>
                                <td><?php echo $row['jenis']; ?></td>
                                <td><?php echo $row['narasi']; ?></td>
                                <td><?php echo $row['respon']; ?></td>
                                <td><?php echo $row['nama_pegawai']; ?></td>
                                <td>
                                    <?php
                                    if ($row['nip'] == $nip) {
                                        if ($row['status'] == 'Terkirim') {
                                            ?>
                                            <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['no_tiket']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                            <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['no_tiket']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                        <?php } else { ?>
                                            <i><?php echo $row['status']; ?></i>
                                            <?php
                                        }
                                    }
                                    ?>
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

    case "tiket-helpdesk":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">LIST TIKET HELPDEK</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">                
                <div id="collapse1" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <?php
                                $notif_helpdesk = fetch_array(bukaquery("SELECT (select count(no_tiket) from tt_helpdesk WHERE `status`='Terkirim' OR `status`='Diterima') as tiket, (select count(no_tiket) from tt_helpdesk WHERE `status`='Menunggu') as menunggu,
                                                                (select count(no_tiket) from tt_helpdesk WHERE `status`='On Proses') as proses, (select count(no_tiket) from tt_helpdesk WHERE `status`='Selesai') as selesai
                                                                FROM tt_helpdesk LIMIT 1"));
                                ?>
                                <li class="active"><a href="#tab_1" data-toggle="tab"> TIKET MASUK <small class="label bg-red"> <?php echo $notif_helpdesk['tiket']; ?></small></a> </li>
                                <li><a href="#tab_2" data-toggle="tab">MENUNGGU <small class="label bg-yellow"> <?php echo $notif_helpdesk['menunggu']; ?></small></a></li>
                                <li><a href="#tab_3" data-toggle="tab">ON PROSES <small class="label bg-blue"> <?php echo $notif_helpdesk['proses']; ?></small></a></li>
                                <li><a href="#tab_4" data-toggle="tab">SELESAI <small class="label bg-green"> <?php echo $notif_helpdesk['selesai']; ?></small></a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_1">
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>No Tiket</th>
                                                    <th>Tanggal Laporan</th>
                                                    <th>Jenis Laporan</th>
                                                    <th>Narasi</th>
                                                    <th>Tanggapan</th>
                                                    <th>Akun</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 0;
                                                $sql_helpdesk = bukaquery("SELECT tt_helpdesk.jenis, tt_helpdesk.narasi,tt_helpdesk.respon, tt_helpdesk.`status`, tt_helpdesk.date, tm_pegawai.nama_pegawai, tt_helpdesk.no_tiket, tt_helpdesk.nip 
                                                FROM tt_helpdesk INNER JOIN tm_pegawai on tm_pegawai.nip=tt_helpdesk.nip where tt_helpdesk.status='Terkirim' OR tt_helpdesk.status='Diterima'");
                                                while ($row = fetch_array($sql_helpdesk)) {
                                                    $no++;
                                                    ?>
                                                    <!-- Edit Modal SKP -->
                                                <div class="modal fade" id="modal-ubah-<?php echo $row['no_tiket']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <form action="<?php echo $aksi . paramEncrypt('module=helpdesk&act=update-status&id=' . $row['no_tiket'] . ''); ?>" method="POST" role="form">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                    <h4 class="modal-title" id="myModalLabel">Update Laporan</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <div class="form-group">
                                                                            <label>Jenis Laporan</label>
                                                                            <input type="text" class="form-control" value="<?php echo $row['jenis']; ?>" readonly> 
                                                                            <label>Narasi Laporan</label>  
                                                                            <textarea class="form-control" rows="3" readonly><?php echo $row['narasi']; ?></textarea>
                                                                            <label>Status</label>
                                                                            <?php echo UpdateEnumDropdown("tt_helpdesk", "status", $row['status'], ""); ?>
                                                                            <label>Respon</label>  
                                                                            <textarea class="form-control" name="respon" rows="3"><?php echo $row['respon']; ?></textarea>

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
                                                    <td><?php echo $no; ?></td>
                                                    <td><?php echo $row['no_tiket']; ?></td>
                                                    <td><?php echo $row['date']; ?></td>
                                                    <td><?php echo $row['jenis']; ?></td>
                                                    <td><?php echo $row['narasi']; ?></td>
                                                    <td><?php echo $row['respon']; ?></td>
                                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                                    <td>
                                                        <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['no_tiket']; ?>" title="Ubah" class="btn btn-success fa fa-feed"> <i> Tanggapi</i></span>
                                                    </td>
                                                </tr>                                  
                                                <?php
                                            }
                                            ?>    
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab_2">
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>No Tiket</th>
                                                    <th>Tanggal Laporan</th>
                                                    <th>Jenis Laporan</th>
                                                    <th>Narasi</th>
                                                    <th>Tanggapan</th>
                                                    <th>Akun</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 0;
                                                $sql_helpdesk = bukaquery("SELECT tt_helpdesk.jenis, tt_helpdesk.narasi,tt_helpdesk.respon, tt_helpdesk.`status`, tt_helpdesk.date, tm_pegawai.nama_pegawai, tt_helpdesk.no_tiket, tt_helpdesk.nip 
                                                FROM tt_helpdesk INNER JOIN tm_pegawai on tm_pegawai.nip=tt_helpdesk.nip where tt_helpdesk.status='Menunggu'");
                                                while ($row = fetch_array($sql_helpdesk)) {
                                                    $no++;
                                                    ?>
                                                    <!-- Edit Modal SKP -->
                                                <div class="modal fade" id="modal-ubah-<?php echo $row['no_tiket']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <form action="<?php echo $aksi . paramEncrypt('module=helpdesk&act=update-status&id=' . $row['no_tiket'] . ''); ?>" method="POST" role="form">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                    <h4 class="modal-title" id="myModalLabel">Update Laporan</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <div class="form-group">
                                                                            <label>Jenis Laporan</label>
                                                                            <input type="text" class="form-control" value="<?php echo $row['jenis']; ?>" readonly> 
                                                                            <label>Narasi Laporan</label>  
                                                                            <textarea class="form-control" rows="3" readonly><?php echo $row['narasi']; ?></textarea>
                                                                            <label>Status</label>
                                                                            <?php echo UpdateEnumDropdown("tt_helpdesk", "status", $row['status'], ""); ?>
                                                                            <label>Respon</label>  
                                                                            <textarea class="form-control" name="respon" rows="3"><?php echo $row['respon']; ?></textarea>

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
                                                    <td><?php echo $no; ?></td>
                                                    <td><?php echo $row['no_tiket']; ?></td>
                                                    <td><?php echo $row['date']; ?></td>
                                                    <td><?php echo $row['jenis']; ?></td>
                                                    <td><?php echo $row['narasi']; ?></td>
                                                    <td><?php echo $row['respon']; ?></td>
                                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                                    <td>
                                                        <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['no_tiket']; ?>" title="Ubah" class="btn btn-success fa fa-feed"> <i> Tanggapi</i></span>
                                                    </td>
                                                </tr>                                  
                                                <?php
                                            }
                                            ?>    
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab_3">
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>No Tiket</th>
                                                    <th>Tanggal Laporan</th>
                                                    <th>Jenis Laporan</th>
                                                    <th>Narasi</th>
                                                    <th>Tanggapan</th>
                                                    <th>Akun</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 0;
                                                $sql_helpdesk = bukaquery("SELECT tt_helpdesk.jenis, tt_helpdesk.narasi,tt_helpdesk.respon, tt_helpdesk.`status`, tt_helpdesk.date, tm_pegawai.nama_pegawai, tt_helpdesk.no_tiket, tt_helpdesk.nip 
                                                FROM tt_helpdesk INNER JOIN tm_pegawai on tm_pegawai.nip=tt_helpdesk.nip where tt_helpdesk.status='On Proses'");
                                                while ($row = fetch_array($sql_helpdesk)) {
                                                    $no++;
                                                    ?>
                                                    <!-- Edit Modal SKP -->
                                                <div class="modal fade" id="modal-ubah-<?php echo $row['no_tiket']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <form action="<?php echo $aksi . paramEncrypt('module=helpdesk&act=update-status&id=' . $row['no_tiket'] . ''); ?>" method="POST" role="form">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                    <h4 class="modal-title" id="myModalLabel">Update Laporan</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <div class="form-group">
                                                                            <label>Jenis Laporan</label>
                                                                            <input type="text" class="form-control" value="<?php echo $row['jenis']; ?>" readonly> 
                                                                            <label>Narasi Laporan</label>  
                                                                            <textarea class="form-control" rows="3" readonly><?php echo $row['narasi']; ?></textarea>
                                                                            <label>Status</label>
                                                                            <?php echo UpdateEnumDropdown("tt_helpdesk", "status", $row['status'], ""); ?>
                                                                            <label>Respon</label>  
                                                                            <textarea class="form-control" name="respon" rows="3"><?php echo $row['respon']; ?></textarea>

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
                                                    <td><?php echo $no; ?></td>
                                                    <td><?php echo $row['no_tiket']; ?></td>
                                                    <td><?php echo $row['date']; ?></td>
                                                    <td><?php echo $row['jenis']; ?></td>
                                                    <td><?php echo $row['narasi']; ?></td>
                                                    <td><?php echo $row['respon']; ?></td>
                                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                                    <td>
                                                        <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['no_tiket']; ?>" title="Ubah" class="btn btn-success fa fa-feed"> <i> Tanggapi</i></span>
                                                    </td>
                                                </tr>                                  
                                                <?php
                                            }
                                            ?>    
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab_4">
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>No Tiket</th>
                                                    <th>Tanggal Laporan</th>
                                                    <th>Jenis Laporan</th>
                                                    <th>Narasi</th>
                                                    <th>Tanggapan</th>
                                                    <th>Akun</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 0;
                                                $sql_helpdesk = bukaquery("SELECT tt_helpdesk.jenis, tt_helpdesk.narasi,tt_helpdesk.respon, tt_helpdesk.`status`, tt_helpdesk.date, tm_pegawai.nama_pegawai, tt_helpdesk.no_tiket, tt_helpdesk.nip 
                                                FROM tt_helpdesk INNER JOIN tm_pegawai on tm_pegawai.nip=tt_helpdesk.nip where tt_helpdesk.status='Selesai'");
                                                while ($row = fetch_array($sql_helpdesk)) {
                                                    $no++;
                                                    ?>
                                                    <!-- Edit Modal SKP -->
                                                <div class="modal fade" id="modal-ubah-<?php echo $row['no_tiket']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <form action="<?php echo $aksi . paramEncrypt('module=helpdesk&act=update-status&id=' . $row['no_tiket'] . ''); ?>" method="POST" role="form">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                    <h4 class="modal-title" id="myModalLabel">Update Laporan</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <div class="form-group">
                                                                            <label>Jenis Laporan</label>
                                                                            <input type="text" class="form-control" value="<?php echo $row['jenis']; ?>" readonly> 
                                                                            <label>Narasi Laporan</label>  
                                                                            <textarea class="form-control" rows="3" readonly><?php echo $row['narasi']; ?></textarea>
                                                                            <label>Status</label>
                                                                            <?php echo UpdateEnumDropdown("tt_helpdesk", "status", $row['status'], ""); ?>
                                                                            <label>Respon</label>  
                                                                            <textarea class="form-control" name="respon" rows="3"><?php echo $row['respon']; ?></textarea>

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
                                                    <td><?php echo $no; ?></td>
                                                    <td><?php echo $row['no_tiket']; ?></td>
                                                    <td><?php echo $row['date']; ?></td>
                                                    <td><?php echo $row['jenis']; ?></td>
                                                    <td><?php echo $row['narasi']; ?></td>
                                                    <td><?php echo $row['respon']; ?></td>
                                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                                    <td>
                                                        <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['no_tiket']; ?>" title="Ubah" class="btn btn-success fa fa-feed"> <i> Tanggapi</i></span>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
}
?>


