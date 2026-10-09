<?php
$aksi = "module/form-sp/aksi-form-sp.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;

    case "list-data-form-sp":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST DATA SURAT PERINGATAN PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">                
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-form-peringatan">
                    Buat Surat Peringatan
                </button>
                <br>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-form-peringatan">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Form Surat Peringatan</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=form-hukuman&act=add-surat-peringatan'); ?>" method="post"> 
                                <div class="modal-body">
                                    <div class = "form-group">
                                        <label class="control-label">No Surat Peringatan</label>
                                        <input type="text" name="no_hukuman" class="form-control" placeholder=".../RSUD-MATRAMAN/SP-.../...." required>
                                        <label class="control-label">Tanggal Surat</label>
                                        <input type="text" name="tgl_hukuman" class="form-control" placeholder="mm/dd/yyyy" id="datepicker" required>   
                                        <label>Jenis Peringatan</label>
                                        <select class="form-control select2" name="id_sanksi"  data-placeholder="-Pilih Jenis Peringatan-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Jenis Peringatan-</option>
                                            <?php
                                            $tm_sanksi = bukaquery("select tm_sanksi.id_sanksi, tm_sanksi.nama_sanksi from tm_sanksi");
                                            while ($row = fetch_array($tm_sanksi)) {
                                                echo"<option value=" . $row['id_sanksi'] . ">" . $row['nama_sanksi'] . "</option>";
                                            }
                                            ?>               
                                        </select>
                                        <label>Ditujukan Kepegawai</label> 
                                        <select class="form-control select2" name="id_user"  data-placeholder="-Pilih Nama Pegawai-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Nama Pegawai-</option>
                                            <?php
                                            $tm_pegawai = bukaquery("select tm_pegawai.id_user, tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.status='AKTIF' ");
                                            while ($row = fetch_array($tm_pegawai)) {
                                                echo"<option value=" . $row['id_user'] . ">" . $row['nama_pegawai'] . "</option>";
                                            }
                                            ?>               
                                        </select>
                                        <label class="control-label">Aktif Hukuman (Pilih Tanggal 1)</label><br>
                                        <div class="col-xs-4">
                                            <select class="form-control select2" name="bulan"  data-placeholder="-Pilih Bulan-" style="width: 100%;" required>
                                                <?php loadBln('-Pilih Bulan-'); ?>               
                                            </select>  
                                        </div>
                                        <div class="col-xs-8">
                                            <select class="form-control select2" name="tahun"  data-placeholder="-Pilih Tahun-" style="width: 100%;" required>
                                                <?php loadThn('-Pilih Tahun-'); ?>               
                                            </select>  
                                        </div>
                                        <label class="control-label">Alasan Hukuman</label>
                                        <textarea class="form-control" rows="8" name="alasan_hukuman" placeholder="Alasan hukuman" required></textarea>                                        
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
                                <th>Tanggal Surat Peringatan</th>
                                <th>No Surat Peringatan</th>
                                <th>Ditujukan Kepegawai</th>
                                <th>Jenis Peringatan</th>
                                <th>Aktif Potong Tunj Bulan</th>
                                <th>Cetak</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $tm_hukuman = bukaquery("select tt_hukuman.id_hukuman, tt_hukuman.no_hukuman, tt_hukuman.tgl_hukuman, tt_hukuman.aktif_hukuman, tt_hukuman.alasan_hukuman,tt_hukuman.id_user,tt_hukuman.id_sanksi, tm_pegawai.nama_pegawai, tm_sanksi.nama_sanksi, tm_sanksi.masa_aktif from tt_hukuman"
                                    . " inner join tm_sanksi on tt_hukuman.id_sanksi=tm_sanksi.id_sanksi"
                                    . " inner join tm_pegawai on tt_hukuman.id_user=tm_pegawai.id_user ");
                            while ($row = fetch_array($tm_hukuman)) {
                                $no++;
                                ?>
                                <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-ubah-<?php echo $row['id_hukuman']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=form-hukuman&act=update-surat-peringatan&id=' . $row['id_hukuman'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Update Surat Peringatan</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label class="control-label">Tanggal Surat (Tgl/Bln/Thn)</label>
                                                        <input type="text" name="tgl_hukuman" class="form-control" value="<?php echo FormatTgl('d-m-Y', $row['tgl_hukuman']); ?>" data-inputmask='"mask": "99-99-9999","placeholder":"dd-mm-yyyy"' data-mask required>
                                                        <label class="control-label">No Surat Peringatan</label>
                                                        <input type="text" name="no_hukuman" class="form-control" value="<?php echo $row['no_hukuman']; ?>" placeholder=""  required>
                                                        <label class="control-label">Jenis Peringatan</label>
                                                        <select class="form-control select2" name="id_sanksi"  data-placeholder="-Pilih Jenis Peringatan-" style="width: 100%;" required>
                                                            <option selected="selected" value="">-Pilih Jenis Peringatan-</option>
                                                            <?php
                                                            $tm_jenis_peringatan = bukaquery("select tm_sanksi.id_sanksi, tm_sanksi.nama_sanksi from tm_sanksi");
                                                            while ($jp = fetch_array($tm_jenis_peringatan)) {
                                                                if ($row['id_sanksi'] == $jp['id_sanksi']) {
                                                                    echo"<option value=" . $jp['id_sanksi'] . " selected=" . $row['id_sanksi'] . ">" . $jp['nama_sanksi'] . "</option>";
                                                                } else {
                                                                    echo"<option value=" . $jp['id_sanksi'] . ">" . $jp['nama_sanksi'] . "</option>";
                                                                }
                                                            }
                                                            ?>               
                                                        </select>
                                                        <label class="control-label">Ditujukan Kepegawai</label>
                                                        <select class="form-control select2" name="id_user"  data-placeholder="-Pilih Nama Pegawai-" style="width: 100%;" required>
                                                            <option selected="selected" value="">-Pilih Nama Pegawai-</option>
                                                            <?php
                                                            $tm_cuti_pengganti = bukaquery("select tm_pegawai.id_user, tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.status='AKTIF'");
                                                            while ($ct = fetch_array($tm_cuti_pengganti)) {
                                                                if ($row['id_user'] == $ct['id_user']) {
                                                                    echo"<option value=" . $ct['id_user'] . " selected=" . $row['id_user'] . ">" . $ct['nama_pegawai'] . "</option>";
                                                                } else {
                                                                    echo"<option value=" . $ct['id_user'] . ">" . $ct['nama_pegawai'] . "</option>";
                                                                }
                                                            }
                                                            ?>               
                                                        </select>
                                                        <label class="control-label">Aktif Hukuman</label><br>
                                                        <div class="col-xs-4">
                                                            <select class="form-control select2" name="bulan"  data-placeholder="-Pilih Bulan-" style="width: 100%;" required>
                                                                <?php updateloadBln(date('m', strtotime($row['aktif_hukuman']))); ?>               
                                                            </select>  
                                                        </div>
                                                        <div class="col-xs-8">
                                                            <input type="text" class="form-control" name="tahun" placeholder="" value="<?php echo date('Y', strtotime($row['aktif_hukuman'])); ?>" readonly>
                                                        </div>
                                                        <label class="control-label">Alasan Hukuman</label>
                                                        <textarea class="form-control" rows="8" name="alasan_hukuman" placeholder="Alasan.." required><?php echo $row['alasan_hukuman']; ?></textarea>                                                         
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
                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_hukuman']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=form-hukuman&act=delete-surat-peringatan&id=' . $row['id_hukuman'] . ''); ?>" method="POST" role="form">
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
                            <tr>
                                <td><?php echo $no; ?></td>
                                <td><?php echo FormatTgl('d/m/Y', $row['tgl_hukuman']); ?></td>
                                <td><?php echo $row['no_hukuman'] ?></td>
                                <td><?php echo $row['nama_pegawai'] ?></td>
                                <td><?php echo $row['nama_sanksi'] ?></td>    
                                <td><?php echo konversiBulan(FormatTgl('m', $row['aktif_hukuman'])) . " - " . konversiBulan(FormatTgl('m', $row['aktif_hukuman']) + $row['masa_aktif'] - 1); ?></td> 
                                <td></td>
                                <td>
                                    <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_hukuman']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                    <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_hukuman']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
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


