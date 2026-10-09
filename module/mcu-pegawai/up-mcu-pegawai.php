<?php
$aksi = "module/mcu-pegawai/aksi-input-mcu.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;
    case "list-data-up-mcu-pegawai":
        ?>
        <style>
        .hidden-by-default {
            display: none;
        }
        </style>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list"> LIST DATA MCU PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <!-- Tombol collapse -->
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>                
            </div>
            <div class="box-body">              
                <button type="button" class="btn btn-success fa fa-plus" data-toggle="modal" data-target="#modal-add-up-mcu-pegawai">
                    Tambah Hasil MCU
                </button>

                <div class="clearfix"></div>
                <!-- Modal Add MCU -->
                <div class="modal fade" id="modal-add-up-mcu-pegawai">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Tambah Hasil MCU</h4>
                            </div>
                            
                            <form role="form" 
                              action="<?php echo $aksi . paramEncrypt('module=mcu&act=add-up-mcu-pegawai&id=' . $id_user . ''); ?>" 
                              method="post" 
                              enctype="multipart/form-data">  
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Tanggal MCU</label>
                                        <input type="text" 
                                           class="form-control" 
                                           id="datepicker2" 
                                           placeholder="mm/dd/yyyy" 
                                           name="tgl_mcu" 
                                           required>
                                        <br>
                                        <label>Pegawai</label>
                                        <select class="form-control select2" name="user" data-placeholder="-Pilih Pegawai-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Pegawai-</option>
                                            <?php
                                            $tm_skp = bukaquery("SELECT * FROM tm_pegawai");
                                            while ($row = fetch_array($tm_skp)) {
                                                echo "<option value='".$row['id_user']."'>".$row['nama_pegawai']."</option>";
                                            }
                                            ?>               
                                        </select>
                                        <br>
                                        <label>Hasil Keseluruhan</label>
                                        <textarea class="form-control" name="hasil" rows="5" placeholder="Hasil Kesimpulan" required></textarea>
                                        <br>
                                        <label>Upload Hasil</label>
                                        <input type="file" class="form-control" name="file" required>
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
                
                <div class="box-body table-responsive">
                    <table id="tabelMCU" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal MCU Dilakukan</th>
                                <th>Hasil Keseluruhan MCU</th>
                                <th>File</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "
                                SELECT a.id_mcu, a.id_user, b.nama_pegawai, a.tgl_mcu, a.hasil, a.file_mcu
                                FROM tm_mcu a
                                INNER JOIN tm_pegawai b ON b.id_user = a.id_user
                                ORDER BY a.tgl_mcu DESC, a.id_mcu DESC
                            ";
                            $tt_mcu = bukaquery($sql);
                            
                            $no = 0;
                            $latest_user = []; // simpan id_user yg sudah tampil terbaru
                            while ($row = fetch_array($tt_mcu)) {
                                $no++;
                            
                                // Cek apakah ini record terbaru untuk id_user
                                if (!isset($latest_user[$row['id_user']])) {
                                    $latest_user[$row['id_user']] = $row['id_mcu'];
                                    $class = ''; // tampilkan (default)
                                } else {
                                    $class = 'older hidden-by-default'; // tandai record lama
                                }
                            ?>
                                                            
                            <!-- Modal Edit MCU -->
                            <div class="modal fade" id="modal-edit-<?php echo $row['id_mcu']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title fa fa-edit"> Edit Hasil MCU</h4>
                                        </div>
                            
                                        <form role="form" 
                                              action="<?php echo $aksi . paramEncrypt('module=mcu&act=update-up-mcu-pegawai&id=' . $row['id_mcu']); ?>" 
                                              method="post" 
                                              enctype="multipart/form-data">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Tanggal MCU</label>
                                                    <input type="text" 
                                                        class="form-control" 
                                                        id="datepicker2" 
                                                        name="tgl_mcu" 
                                                        value="<?php echo FormatTgl('m/d/Y', $row['tgl_mcu']); ?>" 
                                                        required readonly>
                                                    <br>
                            
                                                    <label>Pegawai</label>
                                                    <select class="form-control select2" name="user" style="width: 100%;" disabled>
                                                        <option value="<?php echo $row['id_user']; ?>" selected>
                                                            <?php echo $row['nama_pegawai']; ?>
                                                        </option>
                                                    </select>
                                                    <input type="hidden" name="user" value="<?php echo $row['id_user']; ?>">
                                                    <br>
                            
                                                    <label>Hasil Keseluruhan</label>
                                                    <textarea class="form-control" name="hasil" rows="5" required><?php echo $row['hasil']; ?></textarea>
                                                    <br>
                            
                                                    <label>File MCU</label><br>
                                                    <?php if (!empty($row['file_mcu'])) { ?>
                                                        <!-- Preview PDF -->
                                                        <embed src="files/mcu/<?php echo $row['file_mcu']; ?>" 
                                                               type="application/pdf" 
                                                               width="100%" 
                                                               height="600px"
                                                               style="border:1px solid #ccc; border-radius:8px;">
                                                        <br><br>
                                                    <?php } ?>
                                                    
                                                    <input type="file" class="form-control" name="file">
                                                    <small class="text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                                                </div>
                                            </div>
                            
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                <input type="submit" class="btn btn-primary" value="Update">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Hapus MCU -->
                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_mcu']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=mcu&act=delete-up-mcu-pegawai&id=' . $row['id_mcu']); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel">Delete Data MCU</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>Yakin Anda ingin menghapus data MCU milik <b><?php echo $row['nama_pegawai']; ?></b> 
                                                   pada tanggal <b><?php echo FormatTgl('d-m-Y', $row['tgl_mcu']); ?></b>?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <tr class="<?= $class ?>">
                                <td><?php echo $no; ?></td>
                                <td><?= $row['nama_pegawai'] ?></td>
                                <td><?= FormatTgl('d-m-Y', $row['tgl_mcu']) ?></td>
                                <td><?= $row['hasil'] ?></td>
                                <td><a href="files/mcu/<?= $row['file_mcu'] ?>" target="_blank"><?= $row['file_mcu'] ?></a></td>
                                <td>
                                    <span data-toggle="modal" data-target="#modal-edit-<?= $row['id_mcu'] ?>" class="btn-xs btn-warning fa fa-edit"></span>
                                    <span data-toggle="modal" data-target="#modal-hapus-<?= $row['id_mcu'] ?>" class="btn-xs btn-danger fa fa-trash"></span>
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

        <!-- DataTables + Export -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap.min.css">

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap.min.js"></script>

        <!-- Export Excel/PDF -->
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

        <script>
        $(document).ready(function() {
            var table = $('#tabelMCU').DataTable({
                dom: '<"top"lBf>rt<"bottom"ip>',
                buttons: [
                    { extend: 'excelHtml5', title: 'Data MCU Pegawai' },
                    { extend: 'pdfHtml5', title: 'Data MCU Pegawai' }
                ],
                lengthMenu: [[10, 50, 100, 1000, -1], [10, 50, 100, 1000, "All"]],
                pageLength: 50,
                responsive: true,
                ordering: true
            });
        
            // Event search DataTables
            table.on('search.dt', function() {
                var searchVal = table.search();
        
                if (searchVal) {
                    // Jika ada search → tampilkan semua data (termasuk yang lama)
                    $('#tabelMCU tbody tr.hidden-by-default').show();
                } else {
                    // Jika search kosong → sembunyikan data lama
                    $('#tabelMCU tbody tr.hidden-by-default').hide();
                }
            });
        });
        </script>
        <script>
        $(function() {
            // aktifkan datepicker
            $("#datepicker2").datepicker({
                autoclose: true,
                todayHighlight: true,
                endDate: new Date() // 🔒 maksimal hanya hari ini
            });
        
            // set default hari ini
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();
            $("#datepicker2").datepicker("setDate", mm + '/' + dd + '/' + yyyy);
        });
        </script>
        <?php
    break;
}
?>
