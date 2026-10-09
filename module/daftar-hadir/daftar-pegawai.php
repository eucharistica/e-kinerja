<?php
$aksi = "module/daftar-hadir/aksi-daftar-hadir.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default:
        echo "default";
        header("location:error404");
        break;

        case "daftar-pegawai":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> DAFTAR KEHADIRAN PEGAWAI</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <form action="" method="post">
                            <div class="form-group">
                                <table>
                                    <tr>
                                        <td>
                                            <label>Pilih Tanggal</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" id="datepicker" placeholder="mm/dd/yyyy" name="tanggal_absensi" required readonly>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                        <?php
                        $cari = Formattgl("Y-m-d", $_POST['tanggal_absensi']);
                        $tmtcek = isset($_POST['tanggal_absensi']) ? $_POST['tanggal_absensi'] : null;
                        if ($tmtcek == '') {
                            $tgl_absensi = date('Y-m-d');
                        ?>
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> <label>Daftar Hadir <?php echo konversiTanggal($tgl_absensi); ?></label></h3>
                                <div class="box-tools pull-right">
                                </div>
                            </div>
                        <?php
                        } else {
                            $tgl_absensi = $cari;
                        ?>
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> <label align="center">Daftar Hadir <?php echo konversiTanggal($tgl_absensi); ?></label></h3>
                                <div class="box-tools pull-right">
                                </div>
                            </div>
                        <?php }
                        ?>
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="laporan" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Unit</th>
                                        <th>Jadwal</th>
                                        <th>Tanggal</th>
                                        <th>Absen Masuk</th>
                                        <th>Jam Masuk</th>
                                        <th>Absen Pulang</th>
                                        <th>Jam Pulang</th>
                                        <th>Absensi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $no = 0;
                                    $cari = FormatTgl("Y-m-d", $_POST['tanggal_absensi']);
                                    $sql_pegawai = bukaquery("SELECT a.nama_pegawai, nama_unit, a.nip, a.id_unit,
                                            IFNULL(b.nama_shift,'-') as nama_shift,
                                            IFNULL(TIME(c.tanggal),'-') as jam_masuk, 
                                            IFNULL(c.status,'-') as status_masuk, 
                                            IFNULL(TIME(d.tanggal),'-') as jam_pulang,
                                            IFNULL(d.status,'-') as status_pulang
                                            FROM tm_pegawai a
                                            LEFT JOIN log c ON c.user = a.log_finger AND c.status = 'I' AND DATE_FORMAT(c.tanggal,'%Y-%m-%d') = '$tgl_absensi' 
                                            LEFT JOIN log d ON d.user = a.log_finger AND d.status = 'O' AND DATE_FORMAT(d.tanggal,'%Y-%m-%d') = '$tgl_absensi'
                                            LEFT JOIN tm_jadwal e ON e.nip_pegawai = a.nip AND DATE_FORMAT(e.tanggal_jadwal,'%Y-%m-%d') = '$tgl_absensi'
                                            LEFT JOIN tm_shift b ON b.id_absensi = e.id_absensi
                                            INNER JOIN tm_unit on tm_unit.id_unit = a.id_unit
                                            WHERE a.`status`='AKTIF' 
                                            -- and a.status_pegawai != 'PNS' or 'SPESIALIS' 
                                            GROUP BY a.nama_pegawai
                                            ORDER BY a.nama_pegawai");
                                        while ($row = fetch_array($sql_pegawai)) {
                                        $no++;  

                                    ?>
                                        <tr>
                                            
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['nama_pegawai']; ?></td>
                                            <td><?php echo $row['nama_unit']; ?></td>
                                            <td><?php echo $row['nama_shift']; ?></td>
                                            <td><?php echo $tgl_absensi; ?></td>
                                            <td><?php if ($row['status_masuk'] == 'I') { echo '<i class="btn-xs btn-success">CHECK IN</i>';
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-window_minimize"></i>';
                                                                } ?></td>
                                            <td><?php if ($row['jam_masuk'] != '-') { echo $row['jam_masuk'];
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-window_minimize"></i>';
                                                                } ?></td>
                                            <td><?php if ($row['status_pulang'] == 'O') { echo '<i class="btn-xs btn-success">CHECK OUT</i>';
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-window_minimize"></i>';
                                                                } ?></td>
                                            <td><?php if ($row['jam_pulang'] != '-') { echo $row['jam_pulang'];
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-window_minimize"></i>';
                                                                } ?></td>
                                            <td><a target="_blank" href="?<?= paramEncrypt("module=absensi-pegawai&act=cek-absensi-pegawai&id={$row['nip']}&dpa={$row['id_unit']}&bulan=$bulan&tahun=$tahun") ?>">
                                                <span class="btn-xs btn-info fa fa-eye"> Absensi</span>
                                            </a></td>
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

            case "daftar-hadir-unit":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> DAFTAR KEHADIRAN UNIT</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <form action="" method="post">
                            <div class="form-group">
                                <table>
                                    <tr>
                                        <td>
                                            <label>Pilih Tanggal</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" id="datepicker" placeholder="mm/dd/yyyy" name="tanggal_absensi" required readonly>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                        <?php
                        $cari = Formattgl("Y-m-d", $_POST['tanggal_absensi']);
                        $tmtcek = isset($_POST['tanggal_absensi']) ? $_POST['tanggal_absensi'] : null;
                        if ($tmtcek == '') {
                            $tgl_absensi = date('Y-m-d');
                        ?>
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> <label>Daftar Hadir <?php echo konversiTanggal($tgl_absensi); ?></label></h3>
                                <div class="box-tools pull-right">
                                </div>
                            </div>
                        <?php
                        } else {
                            $tgl_absensi = $cari;
                        ?>
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> <label align="center">Daftar Hadir <?php echo konversiTanggal($tgl_absensi); ?></label></h3>
                                <div class="box-tools pull-right">
                                </div>
                            </div>
                        <?php }
                        ?>


                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="laporan" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jadwal</th>
                                        <th>Tanggal</th>
                                        <th>Absen Masuk</th>
                                        <th>Jam Masuk</th>
                                        <th>Absen Pulang</th>
                                        <th>Jam Pulang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $no = 0;
                                    $cari = FormatTgl("Y-m-d", $_POST['tanggal_absensi']);
                                    $sql_pegawai = bukaquery("SELECT a.nama_pegawai, nama_unit,
                                            IFNULL(b.nama_shift,'-') as nama_shift,
                                            IFNULL(TIME(c.tanggal),'-') as jam_masuk, 
                                            IFNULL(c.status,'-') as status_masuk, 
                                            IFNULL(TIME(d.tanggal),'-') as jam_pulang,
                                            IFNULL(d.status,'-') as status_pulang
                                            FROM tm_pegawai a
                                            LEFT JOIN log c ON c.user = a.log_finger AND c.status = 'I' AND DATE_FORMAT(c.tanggal,'%Y-%m-%d') = '$tgl_absensi' 
                                            LEFT JOIN log d ON d.user = a.log_finger AND d.status = 'O' AND DATE_FORMAT(d.tanggal,'%Y-%m-%d') = '$tgl_absensi'
                                            LEFT JOIN tm_jadwal e ON e.nip_pegawai = a.nip AND DATE_FORMAT(e.tanggal_jadwal,'%Y-%m-%d') = '$tgl_absensi'
                                            LEFT JOIN tm_shift b ON b.id_absensi = e.id_absensi
                                            INNER JOIN tm_unit on tm_unit.id_unit = a.id_unit
                                            WHERE a.`status`='AKTIF' and a.id_unit LIKE '%$id_unit%'
                                            GROUP BY a.nama_pegawai
                                            ORDER BY jam_masuk");
                                        while ($row = fetch_array($sql_pegawai)) {
                                        $no++;  

                                    ?>
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['nama_pegawai']; ?></td>
                                            <td><?php echo $row['nama_shift']; ?></td>
                                            <td><?php echo $tgl_absensi; ?></td>
                                            <td><?php if ($row['status_masuk'] == 'I') { echo '<i class="btn-xs btn-success">CHECK IN</i>';
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-window_minimize"></i>';
                                                                } ?></td>
                                            <td><?php if ($row['jam_masuk'] != '-') { echo $row['jam_masuk'];
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-window_minimize"></i>';
                                                                } ?></td>
                                            <td><?php if ($row['status_pulang'] == 'O') { echo '<i class="btn-xs btn-success">CHECK OUT</i>';
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-window_minimize"></i>';
                                                                } ?></td>
                                            <td><?php if ($row['jam_pulang'] != '-') { echo $row['jam_pulang'];
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-window_minimize"></i>';
                                                                } ?></td>
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
            
            case "daftar-hadir-kasatpel":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> DAFTAR KEHADIRAN UNIT</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <form action="" method="post">
                            <div class="form-group">
                                <table>
                                    <tr>
                                        <td>
                                            <label>Pilih Tanggal</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" id="datepicker" placeholder="mm/dd/yyyy" name="tanggal_absensi" required readonly>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                        <?php
                        $cari = Formattgl("Y-m-d", $_POST['tanggal_absensi']);
                        $tmtcek = isset($_POST['tanggal_absensi']) ? $_POST['tanggal_absensi'] : null;
                        if ($tmtcek == '') {
                            $tgl_absensi = date('Y-m-d');
                        ?>
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> <label>Daftar Hadir <?php echo konversiTanggal($tgl_absensi); ?></label></h3>
                                <div class="box-tools pull-right">
                                </div>
                            </div>
                        <?php
                        } else {
                            $tgl_absensi = $cari;
                        ?>
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> <label align="center">Daftar Hadir <?php echo konversiTanggal($tgl_absensi); ?></label></h3>
                                <div class="box-tools pull-right">
                                </div>
                            </div>
                        <?php }
                        ?>


                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="laporan" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jadwal</th>
                                        <th>Tanggal</th>
                                        <th>Absen Masuk</th>
                                        <th>Jam Masuk</th>
                                        <th>Absen Pulang</th>
                                        <th>Jam Pulang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $no = 0;
                                    $cari = FormatTgl("Y-m-d", $_POST['tanggal_absensi']);
                                    $sql_pegawai = bukaquery("SELECT a.nama_pegawai, nama_unit,
                                            IFNULL(b.nama_shift,'-') as nama_shift,
                                            IFNULL(TIME(c.tanggal),'-') as jam_masuk, 
                                            IFNULL(c.status,'-') as status_masuk, 
                                            IFNULL(TIME(d.tanggal),'-') as jam_pulang,
                                            IFNULL(d.status,'-') as status_pulang
                                            FROM tm_pegawai a
                                            LEFT JOIN log c ON c.user = a.log_finger AND c.status = 'I' AND DATE_FORMAT(c.tanggal,'%Y-%m-%d') = '$tgl_absensi' 
                                            LEFT JOIN log d ON d.user = a.log_finger AND d.status = 'O' AND DATE_FORMAT(d.tanggal,'%Y-%m-%d') = '$tgl_absensi'
                                            LEFT JOIN tm_jadwal e ON e.nip_pegawai = a.nip AND DATE_FORMAT(e.tanggal_jadwal,'%Y-%m-%d') = '$tgl_absensi'
                                            LEFT JOIN tm_shift b ON b.id_absensi = e.id_absensi
                                            INNER JOIN tm_unit on tm_unit.id_unit = a.id_unit
                                            WHERE a.`status`='AKTIF' and a.id_kasatpel LIKE '%$kasatpel_pegawai%'
                                            GROUP BY a.nama_pegawai
                                            ORDER BY jam_masuk");
                                        while ($row = fetch_array($sql_pegawai)) {
                                        $no++;  

                                    ?>
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['nama_pegawai']; ?></td>
                                            <td><?php echo $row['nama_shift']; ?></td>
                                            <td><?php echo $tgl_absensi; ?></td>
                                            <td><?php if ($row['status_masuk'] == 'I') { echo '<i class="btn-xs btn-success">CHECK IN</i>';
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-window_minimize"></i>';
                                                                } ?></td>
                                            <td><?php if ($row['jam_masuk'] != '-') { echo $row['jam_masuk'];
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-window_minimize"></i>';
                                                                } ?></td>
                                            <td><?php if ($row['status_pulang'] == 'O') { echo '<i class="btn-xs btn-success">CHECK OUT</i>';
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-window_minimize"></i>';
                                                                } ?></td>
                                            <td><?php if ($row['jam_pulang'] != '-') { echo $row['jam_pulang'];
                                                                } else { echo '<i class="btn-xs btn-danger fa fa-window_minimize"></i>';
                                                                } ?></td>
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

            case "id-password":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> DAFTAR ID PASSWORD</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">

                        <div class="box-body table-responsive">
                            <table id="laporan" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIP</th>
                                        <th>Nama Pegawai</th>
                                        <th>Password</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $no = 0;
                                    $sql_pegawai = bukaquery("SELECT nip,nama_pegawai, tm_user.`password` FROM tm_pegawai
INNER JOIN tm_user on tm_user.id_user = tm_pegawai.id_user
WHERE tm_pegawai.`status` = 'AKTIF'
");
                                        while ($row = fetch_array($sql_pegawai)) {
                                        $no++;  

                                    ?>
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['nip']; ?></td>
                                            <td><?php echo $row['nama_pegawai']; ?></td>
                                            <td><?php echo paramDecrypt($row['password']); ?></td>
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