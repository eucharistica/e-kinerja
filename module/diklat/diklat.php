<?php
$aksi = "module/form-sp/aksi-form-sp.php?";
$aksi2 = "module/diklat/aksi-diklat.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;

    case "list-diklat-pegawai":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST DATA DIKLAT PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">   
                <!-- /.box-header -->
                <div class="table-responsive">
                    <form method="post" action="<?php echo $aksi2 . paramEncrypt('module=diklat&act=bulk-validasi'); ?>">
                        <div style="margin-bottom:10px;">
                            <button type="submit" class="btn btn-success"
                                    onclick="return confirm('Validasi semua data yang dicentang?');">
                                <i class="fa fa-check"></i> Validasi Terpilih
                            </button>
                        </div>
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                          <tr>
                              <th style="width:30px; text-align:center;">
                                  <input type="checkbox" id="checkAll">
                              </th>
                              <th>No</th>
                              <th>Nanam Pegawai</th>
                              <th>Nama Pelatihan</th>
                              <th>Lokasi Pelatihan</th>
                              <th>Periode</th>
                              <th>Jenis Diklat</th>
                              <th>No Sertifikat</th>
                              <th>File Sertifikat</th>
                              <th>Jumlah JP</th>
                              <th>Jumlah SKP</th>
                              <th>Validasi</th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $sql_riwayat_diklat = bukaquery("SELECT *
                                    from tm_riwayat_diklat inner join tm_pegawai on tm_pegawai.id_user=tm_riwayat_diklat.id_user");
                            while ($row = fetch_array($sql_riwayat_diklat)) {
                                $no++;
                                ?>                               
                                <tr>
                                    <td style="text-align:center;">
                                      <?php if ($row['validasi'] == 'T') { ?>
                                          <input type="checkbox" class="row-check" name="ids[]"
                                                 value="<?php echo $row['id_riwayat_diklat'].'-'.$row['id_user']; ?>">
                                      <?php } else { ?>
                                          <!-- kalau sudah valid/ditolak, tidak bisa dicentang -->
                                          <input type="checkbox" disabled style="opacity:.5;">
                                      <?php } ?>
                                    </td>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <td><?php echo $row['nama_pelatihan'] . " <p>(" . $row['instansi_pelatihan'] . ")</p>"; ?></td>
                                    <td><?php echo $row['lokasi'] . " <p>(Alamat : " . $row['alamat_pelatihan'] . ")</p>"; ?></td>
                                    <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))) . " (" . $row['total_jam'] . " Jam)"; ?></td>
                                    <td><?php echo $row['jenis_diklat']; ?></td>
                                    <td><?php echo $row['no_sertifikat'] . " <p>Status Terakreditasi (" . $row['status_akreditasi'] . ")</p>"; ?></td>
                                    <td><a href="simpeg/diklat/<?php echo $row['file_diklat']; ?>" target="_blank"><?php echo $row['file_diklat']; ?></a></a></td>
                                    <td><?php echo $row['total_jam'] . " Jam" ?></td>
                                    <td><?php echo $row['jumlah_skp'] . " <p>(" . $row['status_peserta'] . ")</p>"; ?></td>
                                    <td>
                                        <?php if ($row['validasi'] == 'T') { ?>
                                        
                                            <a href="<?php echo $aksi2 . paramEncrypt('module=diklat&act=validasi&id=' . $row['id_riwayat_diklat'] . '-' . $row['id_user']); ?>">
                                                <i class="btn btn-danger"> Validasi</i>
                                            </a>
                                        
                                            <button type="button" class="btn btn-default" data-toggle="modal"
                                                    data-target="#modal-tolak-diklat-<?php echo $row['id_riwayat_diklat']; ?>">
                                                <i class="fa fa-ban"></i> Tolak
                                            </button>
                                        
                                            <!-- MODAL INPUT ALASAN TOLAK -->
                                            <div class="modal fade" id="modal-tolak-diklat-<?php echo $row['id_riwayat_diklat']; ?>" tabindex="-1" role="dialog">
                                              <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                        
                                                  <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                    <h4 class="modal-title"><i class="fa fa-ban"></i> Tolak Diklat</h4>
                                                  </div>
                                        
                                                  <form method="post"
                                                        action="<?php echo $aksi2 . paramEncrypt('module=diklat&act=tolak&id=' . $row['id_riwayat_diklat'] . '-' . $row['id_user']); ?>">
                                                    <div class="modal-body">
                                                      <div class="form-group">
                                                        <label>Keterangan Tolak</label>
                                                        <textarea name="keterangan" class="form-control" rows="4" required></textarea>
                                                      </div>
                                                    </div>
                                        
                                                    <div class="modal-footer">
                                                      <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                                      <button type="submit" class="btn btn-danger">Simpan Tolak</button>
                                                    </div>
                                                  </form>
                                        
                                                </div>
                                              </div>
                                            </div>
                                        
                                        <?php } elseif ($row['validasi'] == 'Y') { ?>
                                        
                                            <span class="btn btn-success"> Sudah Tervalidasi</span>
                                        
                                            <a href="<?php echo $aksi2 . paramEncrypt('module=diklat&act=batal-validasi&id=' . $row['id_riwayat_diklat'] . '-' . $row['id_user']); ?>">
                                                <i class="btn btn-warning"> Batalkan Validasi</i>
                                            </a>
                                        
                                        <?php } elseif ($row['validasi'] == 'N') { ?>
                                        
                                            <span class="btn btn-default"><i class="fa fa-ban"></i> Ditolak</span>
                                        
                                            <button type="button" class="btn btn-info" data-toggle="modal"
                                                    data-target="#modal-alasan-tolak-<?php echo $row['id_riwayat_diklat']; ?>">
                                                <i class="fa fa-eye"></i> Lihat Alasan
                                            </button>
                                        
                                            <!-- MODAL LIHAT ALASAN (READ ONLY) -->
                                            <div class="modal fade" id="modal-alasan-tolak-<?php echo $row['id_riwayat_diklat']; ?>" tabindex="-1" role="dialog">
                                              <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                        
                                                  <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                    <h4 class="modal-title"><i class="fa fa-info-circle"></i> Alasan Ditolak</h4>
                                                  </div>
                                        
                                                  <div class="modal-body">
                                                    <div class="form-group">
                                                      <label>Keterangan</label>
                                                      <textarea class="form-control" rows="5" readonly><?php
                                                        echo htmlspecialchars($row['keterangan'] ?? '-', ENT_QUOTES);
                                                      ?></textarea>
                                                    </div>
                                                  </div>
                                        
                                                  <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                                                  </div>
                                        
                                                </div>
                                              </div>
                                            </div>
                                        
                                        <?php } else { ?>
                                        
                                            <span class="btn btn-default">Status tidak dikenal</span>
                                        
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>    
                        </tbody>
                    </table>
                    </form>
                </div> 
            </div>
        </div>
        <script>
        document.addEventListener("DOMContentLoaded", function () {
          var checkAll = document.getElementById("checkAll");
          if (!checkAll) return;
        
          checkAll.addEventListener("change", function () {
            var checks = document.querySelectorAll(".row-check");
            checks.forEach(function (c) { c.checked = checkAll.checked; });
          });
        });
        </script>
        <?php
        break;

   
}
?>


