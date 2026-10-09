<?php
$aksi = "module/form-tugas/aksi-form-tugas.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;

    case "list-data-form-tugas":
        ?>
        <script type="text/javascript">
            function atasnama(isEnabled, an, confirm) {
                document.getElementById(an).disabled = !isEnabled;
                document.getElementById(confirm).disabled = !isEnabled;
            }
        </script>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST DATA SURAT TUGAS PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">                
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-form-tugas">
                    Buat Surat Tugas
                </button>
                <br>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-form-tugas">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Form Surat Tugas</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=form-tugas&act=add-surat-tugas'); ?>" method="post"> 
                                <div class="modal-body">
                                    <div class = "form-group">
                                        <label class="control-label">No Surat Tugas</label>
                                        <input type="text" name="no_surat" class="form-control" placeholder=".../...." required>
                                        <label class="control-label">Tanggal Surat Tugas</label>
                                        <input type="text" name="tgl_surat" class="form-control" placeholder="mm/dd/yyyy" id="datepicker" required>   
                                        <label class="control-label">Tentang Kegiatan/Acara</label>
                                        <textarea class="form-control" rows="4" name="kegiatan" placeholder="Tentang..." required></textarea>
                                        <label class="control-label">Tanggal Kegiatan/Acara</label>
                                        <input type="text" name="tgl_kegiatan" class="form-control" placeholder="mm/dd/yyyy" id="datepicker1" required>
                                        <label class="control-label">Waktu Kegiatan/Acara</label>
                                        <input type="text" name="waktu" class="form-control" data-inputmask='"mask": "99:99 - 99:99 WIB","placeholder":"00:00 - 00:00 WIB"' data-mask required>
                                        <label class="control-label">Lokasi Kegiatan/Acara</label>
                                        <textarea class="form-control" rows="4" name="lokasi" placeholder="Lokasi..." required></textarea>
                                        <div class = "form-group">
                                            <input type="checkbox" name="checkbox1" height="20" width="10" id="checkboxOne"  onclick="atasnama(this.checked, 'an')" /> <label>TTD Atas Nama</label><br>                                     
                                            <select class="form-control select2" name="an" id="an"  data-placeholder="-Pilih Nama Pegawai-" style="width: 100%;" disabled>
                                                <option selected="selected" value="">-Pilih Nama Pegawai-</option>
                                                <?php
                                                $tm_pegawai1 = bukaquery("select tm_pegawai.nip, tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status='AKTIF' and tm_pegawai.status_pegawai='PNS'");
                                                while ($row1 = fetch_array($tm_pegawai1)) {
                                                    echo"<option value=" . $row1['nip'] . ">" . $row1['nama_pegawai'] . "</option>";
                                                }
                                                ?>               
                                            </select>
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
                                <th>Tanggal Surat Tugas</th>
                                <th>No Surat Tugas</th>
                                <th>Tantang / Kegiatan/ Acara</th>
                                <th>Tanggal Kegiatan/ Acara</th>
                                <th>Keterangan</th>
                                <th>Penugasan</th>
                                <th>Cetak</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $tm_surat_tugas = bukaquery("select * from tm_surat_tugas order by id_surat desc ");
                            while ($row = fetch_array($tm_surat_tugas)) {
                                $no++;
                                ?>
                                <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-ubah-<?php echo $row['id_surat']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=form-tugas&act=update-surat-tugas&id=' . $row['id_surat'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Update Surat Tugas Pegawai</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label class="control-label">Tanggal Surat Tugas</label>
                                                        <input type="text" name="tgl_surat" class="form-control" value="<?php echo FormatTgl('d-m-Y', $row['tgl_surat']); ?>"  data-inputmask='"mask": "99-99-9999","placeholder":"dd-mm-yyyy"' data-mask required>
                                                        <label class="control-label">No Surat Tugas</label>
                                                        <input type="text" name="no_surat" class="form-control" value="<?php echo $row['no_surat']; ?>" placeholder="" required>
                                                        <label class="control-label">Tentang Kegiatan/Acara</label>
                                                        <textarea class="form-control" rows="4" name="kegiatan" placeholder="Tentang..." required><?php echo $row['kegiatan']; ?></textarea>
                                                        <label class="control-label">Tanggal Kegiatan/Acara</label>
                                                        <input type="text" name="tgl_kegiatan" class="form-control" value="<?php echo FormatTgl('d-m-Y', $row['tgl_kegiatan']); ?>"  data-inputmask='"mask": "99-99-9999","placeholder":"dd-mm-yyyy"' data-mask required>
                                                        <label class="control-label">Waktu Kegiatan/Acara</label>
                                                        <input type="text" name="waktu" class="form-control" value="<?php echo $row['waktu']; ?>"  data-inputmask='"mask": "99:99 - 99:99 WIB","placeholder":"00:00 - 00:00 WIB"' data-mask required>
                                                        <label class="control-label">Lokasi Kegiatan/Acara</label>
                                                        <textarea class="form-control" rows="4" name="lokasi" placeholder="Lokasi..." required><?php echo $row['lokasi']; ?></textarea>                                                        
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
                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_surat']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=form-tugas&act=delete-surat-tugas&id=' . $row['id_surat'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Delete Surat Peringatan</h4>
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

                            <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-peserta-<?php echo $row['id_surat']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=form-tugas&act=add-penugasan&id=' . $row['id_surat'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Tambah Peserta Penugasan</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label>Penugasan Untuk</label>
                                                        <select class="form-control select2" name="nip"  data-placeholder="-Pilih Nama Pengganti-" style="width: 100%;" required>
                                                            <option selected="selected" value="">-Pilih Nama Pegawai-</option>
                                                            <?php
                                                            $tm_pegawai1 = bukaquery("select tm_pegawai.nip, tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status='AKTIF'");
                                                            while ($row1 = fetch_array($tm_pegawai1)) {
                                                                echo"<option value=" . $row1['nip'] . ">" . $row1['nama_pegawai'] . "</option>";
                                                            }
                                                            ?>               
                                                        </select>
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
                                <td><?php echo FormatTgl('d/m/Y', $row['tgl_surat']); ?></td>
                                <td><?php echo $row['no_surat']; ?></td>
                                <td><?php echo $row['kegiatan']; ?></td>
                                <td><?php echo FormatTgl('d/m/Y', $row['tgl_kegiatan']); ?></td> 
                                <td><?php echo "Mulai Jam : " . $row['waktu'] . " <p>Bertempat di " . $row['lokasi'] . "</p>" ?></td>
                                <td>
                                    <span data-toggle="modal" data-target="#modal-peserta-<?php echo $row['id_surat']; ?>" title="Tambah Peserta" class="btn-xs btn-primary fa fa-plus"></span><br>
                                    <hr><?php
                                    $sql_penugasan = bukaquery("select tm_pegawai.nama_pegawai,tm_add_surtug.id_add,tm_add_surtug.`read` from tm_add_surtug inner join tm_pegawai on tm_add_surtug.nip=tm_pegawai.nip where tm_add_surtug.id_surat='$row[id_surat]'");
                                    while ($add = fetch_array($sql_penugasan)) {
                                        if ($add['read'] != 0) {
                                            $status = '[<b style=color:green>R</b>]';
                                        } else {
                                            $status = '[<b style=color:blue>D</b>]';
                                        }
                                        echo $add['nama_pegawai'] . " $status <a href=$aksi" . paramEncrypt('module=form-tugas&act=delete-penugasan&id=' . $add['id_add'] . '') . ">  <span class='btn-xs btn-danger fa fa-trash'></span> </a><br>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="<?php echo 'cetak-surat-tugas-' . $row['id_surat'] . ''; ?>" target="_blank"><span title="Cetak Surat Tugas" class="btn-xs btn fa fa-file-pdf-o">  </span>                         
                                        </button></a>
                                </td>
                                <td>
                                    <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_surat']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                    <?php
                                    if (getOne("select count(tm_add_surtug.id_add) from tm_add_surtug inner join tm_pegawai on tm_add_surtug.nip=tm_pegawai.nip where tm_add_surtug.id_surat='$row[id_surat]'") > 0) {
                                        
                                    } else {
                                        ?>
                                        <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_surat']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
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
}
?>


