<?php
$aksi = "module/mcu-pegawai/aksi-mcu-pegawai?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;


    case "list-data-mcu-pegawai":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST DATA MCU PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">  
                <!-- /.box-header -->                
                <div class="box-body table-responsive">
                    <table id="kinerja" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal MCU Dilakukan</th>
                                <th>Hasil Keseluruhan MCU</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            
                                $tahun_setup = $thn_now;
                            $tt_olahraga = bukaquery("SELECT b.nama_pegawai, a.tgl_mcu, a.hasil, a.file_mcu
                                                    FROM tm_mcu a
                                                    INNER JOIN tm_pegawai b on b.id_user=a.id_user
                                                    where  a.id_user=" . $id_user . " order by a.tgl_mcu Desc");
                            while ($row = fetch_array($tt_olahraga)) {
                                $no++;
                                // $durasi = floor(((is_string($row['waktu_mulai']) ? strtotime($row['waktu_akhir']) : $row['waktu_akhir']) - (is_string($row['waktu_mulai']) ? strtotime($row['waktu_mulai']) : $row['waktu_mulai'])) / 60);
                                ?>

                            <tr>
                                <td><?php echo $no; ?></td>
                                <td><?php echo $row['nama_pegawai']; ?></td>
                                <td><?php echo FormatTgl('d-m-Y', $row['tgl_mcu']); ?></td>
                                <td><?php echo $row['hasil']; ?></td>
                                <td><a href="files/mcu/<?php echo $row['file_mcu']; ?>" target="_blank"><?php echo $row['file_mcu']; ?></a></td>
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


