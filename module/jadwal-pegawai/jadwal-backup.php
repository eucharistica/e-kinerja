<?php
$aksi = "module/jadwal-pegawai/aksi-jadwal.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default:
        echo "default";
        header("location:error404");
        break;


    case "add-jadwal-pegawai":
?>
        <!--Modal Add waktu -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-clock-o">TAMBAH JADWAL PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-8">
                    <?php
                    if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1' or $hak_akses['menu_val_kasatpel'] =='1') {
                        $link = $aksi . paramEncrypt('module=jadwal-pegawai&act="add-jadwal-pegawai&act=add-jadwal-pegawai&id=' . $id_user . '&link=kepegawaian');
                    } else {
                        $link = $aksi . paramEncrypt('module=jadwal-pegawai&act="add-jadwal-pegawai&id=' . $id . '');
                    }
                    $pegawai = fetch_array(bukaquery("select tm_pegawai.id_user,tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto, tm_unit.nama_unit from tm_pegawai INNER JOIN tm_unit ON tm_unit.id_unit=tm_pegawai.id_unit where tm_pegawai.id_user='$dpa' "));
                    $id_jadwal = getOne("select id_jadwal from tm_jadwal where tm_jadwal.nip_pegawai='$pegawai[nip]' and  tanggal_jadwal='$id'");
                    if ($id_jadwal == '') {
                    ?>
                        <form role="form" action="<?php echo $link; ?>" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Tanggal Jadwal</label>
                                    <input type="text" class="form-control" value="<?= $id ?>" name="tanggal_jadwal" required readonly>
                                    <label>Nama Unit</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_unit'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['id_unit'] ?>" name="id_unit" required readonly>
                                    <label>Nama Pegawai</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_pegawai'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['nip'] ?>" name="nip" required readonly>
                                    <label>Pilih Shift</label>
                                    <select class="form-control select2" name="id_absensi" data-placeholder="-Pilih Shift-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Shift-</option>
                                        <?php
                                        $tm_shift = bukaquery("select tm_shift.id_absensi, tm_shift.nama_shift, tm_shift.jam_masuk, tm_shift.jam_pulang from tm_shift inner join set_shift on set_shift.id_absensi = tm_shift.id_absensi where set_shift.id_unit LIKE '%$id_unit%' and tm_shift.status = '1' group by tm_shift.id_absensi order by nama_shift desc");
                                        while ($row = fetch_array($tm_shift)) {
                                            echo "<option value=" . $row['id_absensi'] . ">" . $row['nama_shift'] . "&nbsp" . "(" . $row['jam_masuk'] . "&nbsp" . "-" . $row['jam_pulang'] . ")" . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <?php if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1') { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } else { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } ?>
                                <input type="submit" class="btn btn-primary" value="Simpan">
                            </div>
                        </form>
                    <?php
                    } else {
                        $tm_jadwal = fetch_array(bukaquery("select * from tm_jadwal where nip_pegawai=" . $pegawai['nip'] . " and id_jadwal='$id_jadwal'"));
                        if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1') {
                            $link = $aksi . paramEncrypt('module=jadwal-pegawai&act=update-jadwal&id=' . $id_jadwal . '&link=kepegawaian');
                        } else {
                            $link = $aksi . paramEncrypt('module=jadwal-pegawai&act=update-jadwal&id=' . $id_jadwal . '&link=kepegawaian');
                        }
                    ?>
                        <form role="form" action="<?php echo $link; ?>" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Tanggal Jadwal</label>
                                    <input type="text" class="form-control" value="<?= $id ?>" name="tanggal_jadwal" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $id_jadwal ?>" name="id_jadwal" required readonly>
                                    <label>Nama Unit</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_unit'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['id_unit'] ?>" name="id_unit" required readonly>
                                    <label>Nama Pegawai</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_pegawai'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['nip'] ?>" name="nip" required readonly>
                                    <label>Pilih Shift</label>
                                    <select class="form-control select2" name="id_absensi" data-placeholder="-Pilih Shift-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Shift-</option>
                                        <?php
                                        $tm_shift = bukaquery("select tm_shift.id_absensi, tm_shift.nama_shift, tm_shift.jam_masuk, tm_shift.jam_pulang from tm_shift inner join set_shift on set_shift.id_absensi = tm_shift.id_absensi where set_shift.id_unit LIKE '%$id_unit%' and tm_shift.status = '1'  group by tm_shift.id_absensi order by nama_shift desc");
                                        while ($shift = fetch_array($tm_shift)) {
                                            if ($tm_jadwal['id_absensi'] == $shift['id_absensi']) {
                                                echo "<option value=" . $shift['id_absensi'] . " selected=" . $tm_jadwal['id_absensi'] . ">" . $shift['nama_shift'] . "&nbsp" . "(" . $shift['jam_masuk'] . "&nbsp" . "-" . $shift['jam_pulang'] . ")" . "</option>";
                                            } else {
                                                echo "<option value=" . $shift['id_absensi'] . ">" . $shift['nama_shift'] . "&nbsp" . "(" . $shift['jam_masuk'] . "&nbsp" . "-" . $shift['jam_pulang'] . ")" . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <?php if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1') { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } else { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } ?>
                                <input type="submit" class="btn btn-warning" value="Update">
                            </div>
                        </form>
                    <?php } ?>
                </div>
                <div class="col-md-4">
                    <center>
                        <?php
                        if ($pegawai['foto'] == '-' or $pegawai['foto'] == '') {
                            if ($pegawai['jk'] == 'L') {
                                $foto = 'img/laki.png';
                            } else {
                                $foto = 'img/perempuan.png';
                            }
                        } else {
                            $foto = "img/" . $pegawai['foto'];
                        }
                        ?>
                        <br><br>
                        <img src="<?php echo $foto; ?>" width="300px" height="300px" class="img-squere">
                    </center>
                </div>
            </div>
        </div>
        <!-- Tutup Modal waktu -->
    <?php
        break;

        case "add-validasi-jadwal": //menu validasi jadwal kepegawaian
?>
        <!--Modal Add waktu -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-clock-o">VALIDASI JADWAL PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-8">
                    <?php
                    if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1' or $hak_akses['menu_val_kasatpel'] =='1') {
                        $link = $aksi . paramEncrypt('module=jadwal-pegawai&act="add-validasi-jadwal&act=add-validasi-jadwal&id=' . $id_user . '&link=kepegawaian');
                    } else {
                        $link = $aksi . paramEncrypt('module=jadwal-pegawai&act="add-validasi-jadwal&id=' . $id . '');
                    }
                    $pegawai = fetch_array(bukaquery("select tm_pegawai.id_user,tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto, tm_unit.nama_unit from tm_pegawai INNER JOIN tm_unit ON tm_unit.id_unit=tm_pegawai.id_unit where tm_pegawai.id_user='$dpa' "));
                    $id_jadwal = getOne("select id_jadwal from tm_jadwal where tm_jadwal.nip_pegawai='$pegawai[nip]' and  tanggal_jadwal='$id'");
                    if ($id_jadwal == '') {
                    ?>
                        <form role="form" action="<?php echo $link; ?>" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Tanggal Jadwal</label>
                                    <input type="text" class="form-control" value="<?= $id ?>" name="tanggal_jadwal" required readonly>
                                    <label>Nama Unit</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_unit'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['id_unit'] ?>" name="id_unit" required readonly>
                                    <label>Nama Pegawai</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_pegawai'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['nip'] ?>" name="nip" required readonly>
                                    <label>Pilih Shift</label>
                                    <select class="form-control select2" name="id_absensi" data-placeholder="-Pilih Shift-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Shift-</option>
                                        <?php
                                        $tm_shift = bukaquery("select tm_shift.id_absensi, tm_shift.nama_shift, tm_shift.jam_masuk, tm_shift.jam_pulang from tm_shift where tm_shift.status ='1'");
                                        while ($row = fetch_array($tm_shift)) {
                                            echo "<option value=" . $row['id_absensi'] . ">" . $row['nama_shift'] . "&nbsp" . "(" . $row['jam_masuk'] . "&nbsp" . "-" . $row['jam_pulang'] . ")" . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <?php if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1') { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=validasi-data-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } else { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=validasi-data-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } ?>
                                <input type="submit" class="btn btn-primary" value="Simpan">
                            </div>
                        </form>
                    <?php
                    } else {
                        $tm_jadwal = fetch_array(bukaquery("select * from tm_jadwal where nip_pegawai=" . $pegawai['nip'] . " and id_jadwal='$id_jadwal'"));
                        if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1') {
                            $link = $aksi . paramEncrypt('module=jadwal-pegawai&act=update-validasi-jadwal&id=' . $id_jadwal . '&link=kepegawaian');
                        } else {
                            $link = $aksi . paramEncrypt('module=jadwal-pegawai&act=update-validasi-jadwal&id=' . $id_jadwal . '&link=kepegawaian');
                        }
                    ?>
                        <form role="form" action="<?php echo $link; ?>" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Tanggal Jadwal</label>
                                    <input type="text" class="form-control" value="<?= $id ?>" name="tanggal_jadwal" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $id_jadwal ?>" name="id_jadwal" required readonly>
                                    <label>Nama Unit</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_unit'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['id_unit'] ?>" name="id_unit" required readonly>
                                    <label>Nama Pegawai</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_pegawai'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['nip'] ?>" name="nip" required readonly>
                                    <label>Pilih Shift</label>
                                    <select class="form-control select2" name="id_absensi" data-placeholder="-Pilih Shift-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Shift-</option>
                                        <?php
                                        $tm_shift = bukaquery("select tm_shift.id_absensi, tm_shift.nama_shift, tm_shift.jam_masuk, tm_shift.jam_pulang from tm_shift where tm_shift.status ='1' ");
                                        while ($shift = fetch_array($tm_shift)) {
                                            if ($tm_jadwal['id_absensi'] == $shift['id_absensi']) {
                                                echo "<option value=" . $shift['id_absensi'] . " selected=" . $tm_jadwal['id_absensi'] . ">" . $shift['nama_shift'] . "&nbsp" . "(" . $shift['jam_masuk'] . "&nbsp" . "-" . $shift['jam_pulang'] . ")" . "</option>";
                                            } else {
                                                echo "<option value=" . $shift['id_absensi'] . ">" . $shift['nama_shift'] . "&nbsp" . "(" . $shift['jam_masuk'] . "&nbsp" . "-" . $shift['jam_pulang'] . ")" . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <?php if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1') { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=validasi-data-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } else { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=validasi-data-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } ?>
                                <input type="submit" class="btn btn-warning" value="Update">
                            </div>
                        </form>
                    <?php } ?>
                </div>
                <div class="col-md-4">
                    <center>
                        <?php
                        if ($pegawai['foto'] == '-' or $pegawai['foto'] == '') {
                            if ($pegawai['jk'] == 'L') {
                                $foto = 'img/laki.png';
                            } else {
                                $foto = 'img/perempuan.png';
                            }
                        } else {
                            $foto = "img/" . $pegawai['foto'];
                        }
                        ?>
                        <br><br>
                        <img src="<?php echo $foto; ?>" width="300px" height="300px" class="img-squere">
                    </center>
                </div>
            </div>
        </div>
        <!-- Tutup Modal waktu -->
    <?php
        break;

        case "add-validasi-pegawai": //menu validasi jadwal pegawai
?>
        <!--Modal Add waktu -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-clock-o">VALIDASI JADWAL</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-8">
                    <?php
                    if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1' or $hak_akses['menu_val_kasatpel'] =='1') {
                        $link = $aksi . paramEncrypt('module=jadwal-pegawai&act="add-validasi-pegawai&act=add-validasi-pegawai&id=' . $id_user . '&link=kepegawaian');
                    } else {
                        $link = $aksi . paramEncrypt('module=jadwal-pegawai&act="add-validasi-pegawai&id=' . $id . '');
                    }
                    $pegawai = fetch_array(bukaquery("select tm_pegawai.id_user,tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto, tm_unit.nama_unit from tm_pegawai INNER JOIN tm_unit ON tm_unit.id_unit=tm_pegawai.id_unit where tm_pegawai.id_user='$dpa' "));
                    $id_jadwal = getOne("select id_jadwal from tm_jadwal where tm_jadwal.nip_pegawai='$pegawai[nip]' and  tanggal_jadwal='$id'");
                    if ($id_jadwal == '') {
                    ?>
                        <form role="form" action="<?php echo $link; ?>" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Tanggal Jadwal</label>
                                    <input type="text" class="form-control" value="<?= $id ?>" name="tanggal_jadwal" required readonly>
                                    <label>Nama Unit</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_unit'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['id_unit'] ?>" name="id_unit" required readonly>
                                    <label>Nama Pegawai</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_pegawai'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['nip'] ?>" name="nip" required readonly>
                                    <label>Pilih Shift</label>
                                    <select class="form-control select2" name="id_absensi" data-placeholder="-Pilih Shift-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Shift-</option>
                                        <?php
                                        $tm_shift = bukaquery("select tm_shift.id_absensi, tm_shift.nama_shift, tm_shift.jam_masuk, tm_shift.jam_pulang from tm_shift inner join set_shift on set_shift.id_absensi = tm_shift.id_absensi where set_shift.id_unit LIKE '%$id_unit%' and tm_shift.status = '1'  group by tm_shift.id_absensi order by nama_shift desc");
                                        while ($row = fetch_array($tm_shift)) {
                                            echo "<option value=" . $row['id_absensi'] . ">" . $row['nama_shift'] . "&nbsp" . "(" . $row['jam_masuk'] . "&nbsp" . "-" . $row['jam_pulang'] . ")" . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <?php if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1') { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=validasi-list-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } else { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=validasi-list-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } ?>
                                <input type="submit" class="btn btn-primary" value="Simpan">
                            </div>
                        </form>
                    <?php
                    } else {
                        $tm_jadwal = fetch_array(bukaquery("select * from tm_jadwal where nip_pegawai=" . $pegawai['nip'] . " and id_jadwal='$id_jadwal'"));
                        if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1') {
                            $link = $aksi . paramEncrypt('module=jadwal-pegawai&act=update-validasi-pegawai&id=' . $id_jadwal . '&link=kepegawaian');
                        } else {
                            $link = $aksi . paramEncrypt('module=jadwal-pegawai&act=update-validasi-pegawai&id=' . $id_jadwal . '&link=kepegawaian');
                        }
                    ?>
                        <form role="form" action="<?php echo $link; ?>" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Tanggal Jadwal</label>
                                    <input type="text" class="form-control" value="<?= $id ?>" name="tanggal_jadwal" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $id_jadwal ?>" name="id_jadwal" required readonly>
                                    <label>Nama Unit</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_unit'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['id_unit'] ?>" name="id_unit" required readonly>
                                    <label>Nama Pegawai</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_pegawai'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['nip'] ?>" name="nip" required readonly>
                                    <label>Pilih Shift</label>
                                    <select class="form-control select2" name="id_absensi" data-placeholder="-Pilih Shift-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Shift-</option>
                                        <?php
                                        $tm_shift = bukaquery("select tm_shift.id_absensi, tm_shift.nama_shift, tm_shift.jam_masuk, tm_shift.jam_pulang from tm_shift inner join set_shift on set_shift.id_absensi = tm_shift.id_absensi where set_shift.id_unit LIKE '%$id_unit%' and tm_shift.status = '1'  group by tm_shift.id_absensi order by nama_shift desc");
                                        while ($shift = fetch_array($tm_shift)) {
                                            if ($tm_jadwal['id_absensi'] == $shift['id_absensi']) {
                                                echo "<option value=" . $shift['id_absensi'] . " selected=" . $tm_jadwal['id_absensi'] . ">" . $shift['nama_shift'] . "&nbsp" . "(" . $shift['jam_masuk'] . "&nbsp" . "-" . $shift['jam_pulang'] . ")" . "</option>";
                                            } else {
                                                echo "<option value=" . $shift['id_absensi'] . ">" . $shift['nama_shift'] . "&nbsp" . "(" . $shift['jam_masuk'] . "&nbsp" . "-" . $shift['jam_pulang'] . ")" . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <?php if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1') { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=validasi-list-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } else { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=validasi-list-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } ?>
                                <input type="submit" class="btn btn-warning" value="Update">
                            </div>
                        </form>
                    <?php } ?>
                </div>
                <div class="col-md-4">
                    <center>
                        <?php
                        if ($pegawai['foto'] == '-' or $pegawai['foto'] == '') {
                            if ($pegawai['jk'] == 'L') {
                                $foto = 'img/laki.png';
                            } else {
                                $foto = 'img/perempuan.png';
                            }
                        } else {
                            $foto = "img/" . $pegawai['foto'];
                        }
                        ?>
                        <br><br>
                        <img src="<?php echo $foto; ?>" width="300px" height="300px" class="img-squere">
                    </center>
                </div>
            </div>
        </div>
        <!-- Tutup Modal waktu -->
    <?php
        break;


    case "add-rekap-jadwal-pegawai":
    ?>
        <!--Modal Add waktu -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-clock-o">JADWAL PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-8">
                    <?php
                    if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1' or $hak_akses['menu_val_kasatpel'] =='1') {
                        $link = $aksi . paramEncrypt('module=jadwal-pegawai&act="add-rekap-jadwal-pegawai&act=add-rekap-jadwal-pegawai&id=' . $id_user . '&link=kepegawaian');
                    } else {
                        $link = $aksi . paramEncrypt('module=jadwal-pegawai&act="add-rekap-jadwal-pegawai&id=' . $id . '');
                    }
                    $pegawai = fetch_array(bukaquery("select tm_pegawai.id_user,tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto, tm_unit.nama_unit from tm_pegawai INNER JOIN tm_unit ON tm_unit.id_unit=tm_pegawai.id_unit where tm_pegawai.id_user='$dpa' "));
                    $id_jadwal = getOne("select id_jadwal from tm_jadwal where tm_jadwal.nip_pegawai='$pegawai[nip]' and  tanggal_jadwal='$id'");
                    if ($id_jadwal == '') {
                    ?>
                        <form role="form" action="<?php echo $link; ?>" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Tanggal Jadwal</label>
                                    <input type="text" class="form-control" value="<?= $id ?>" name="tanggal_jadwal" required readonly>
                                    <label>Nama Unit</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_unit'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['id_unit'] ?>" name="id_unit" required readonly>
                                    <label>Nama Pegawai</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_pegawai'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['nip'] ?>" name="nip" required readonly>
                                    <label>Pilih Shift</label>
                                    <select class="form-control select2" name="id_absensi" data-placeholder="-Pilih Shift-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Shift-</option>
                                        <?php
                                        $tm_shift = bukaquery("select tm_shift.id_absensi, tm_shift.nama_shift, tm_shift.jam_masuk, tm_shift.jam_pulang from tm_shift  where tm_shift.status ='1'");
                                        while ($row = fetch_array($tm_shift)) {
                                            echo "<option value=" . $row['id_absensi'] . ">" . $row['nama_shift'] . "&nbsp" . "(" . $row['jam_masuk'] . "&nbsp" . "-" . $row['jam_pulang'] . ")" . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <?php if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1') { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=rekap-data-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } else { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=rekap-data-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } ?>
                                <input type="submit" class="btn btn-primary" value="Simpan">
                            </div>
                        </form>
                    <?php
                    } else {
                        $tm_jadwal = fetch_array(bukaquery("select * from tm_jadwal where nip_pegawai=" . $pegawai['nip'] . " and id_jadwal='$id_jadwal'"));
                        if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1') {
                            $link = $aksi . paramEncrypt('module=jadwal-pegawai&act=update-rekap-jadwal&id=' . $id_jadwal . '&link=kepegawaian');
                        } else {
                            $link = $aksi . paramEncrypt('module=jadwal-pegawai&act=update-rekap-jadwal&id=' . $id_jadwal . '&link=kepegawaian');
                        }
                    ?>
                        <form role="form" action="<?php echo $link; ?>" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Tanggal Jadwal</label>
                                    <input type="text" class="form-control" value="<?= $id ?>" name="tanggal_jadwal" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $id_jadwal ?>" name="id_jadwal" required readonly>
                                    <label>Nama Unit</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_unit'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['id_unit'] ?>" name="id_unit" required readonly>
                                    <label>Nama Pegawai</label>
                                    <input type="text" class="form-control" value="<?= $pegawai['nama_pegawai'] ?>" required readonly>
                                    <input type="hidden" class="form-control" value="<?= $pegawai['nip'] ?>" name="nip" required readonly>
                                    <label>Pilih Shift</label>
                                    <select class="form-control select2" name="id_absensi" data-placeholder="-Pilih Shift-" style="width: 100%;" required>
                                        <option selected="selected" value="">-Pilih Shift-</option>
                                        <?php
                                        $tm_shift = bukaquery("select tm_shift.id_absensi, tm_shift.nama_shift, tm_shift.jam_masuk, tm_shift.jam_pulang from tm_shift  where tm_shift.status ='1' ");
                                        while ($shift = fetch_array($tm_shift)) {
                                            if ($tm_jadwal['id_absensi'] == $shift['id_absensi']) {
                                                echo "<option value=" . $shift['id_absensi'] . " selected=" . $tm_jadwal['id_absensi'] . ">" . $shift['nama_shift'] . "&nbsp" . "(" . $shift['jam_masuk'] . "&nbsp" . "-" . $shift['jam_pulang'] . ")" . "</option>";
                                            } else {
                                                echo "<option value=" . $shift['id_absensi'] . ">" . $shift['nama_shift'] . "&nbsp" . "(" . $shift['jam_masuk'] . "&nbsp" . "-" . $shift['jam_pulang'] . ")" . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <?php if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_pj'] == '1') { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=rekap-data-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } else { ?>
                                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=rekap-data-jadwal-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                <?php } ?>
                                <input type="submit" class="btn btn-warning" value="Update">
                            </div>
                        </form>
                    <?php } ?>
                </div>
                <div class="col-md-4">
                    <center>
                        <?php
                        if ($pegawai['foto'] == '-' or $pegawai['foto'] == '') {
                            if ($pegawai['jk'] == 'L') {
                                $foto = 'img/laki.png';
                            } else {
                                $foto = 'img/perempuan.png';
                            }
                        } else {
                            $foto = "img/" . $pegawai['foto'];
                        }
                        ?>
                        <br><br>
                        <img src="<?php echo $foto; ?>" width="300px" height="300px" class="img-squere">
                    </center>
                </div>
            </div>
        </div>
        <!-- Tutup Modal waktu -->
    <?php
        break;


    case "list-data-jadwal-pegawai":
        $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
        $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : date('Y')
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> INPUT JADWAL PEGAWAI</h3>
                <!-- <p><?php echo FormatTgl('Y-m-d', $awalbulandepan); ?></p> -->
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=rekap-data-jadwal-pegawai'); ?>">
                    <button type="button" class="btn btn-instagram fa fa-search" title="cek rekapitulasi jadwal">
                        Rekapitulasi Jadwal
                    </button>
                </a>
                <?Php
                if (date('d') > 20 )  { 
                    $from = FormatTgl('Y-m-00', $awalbulandepan);
                    $to = $akhir = FormatTgl('Y-m-d', $akhirbulandepan);
                } else {
                    $from = TanggalAwalBulan();
                    $to = date('Y-m-t');
                }

                ?>
                <tr>
                    <br>
                    <br>
                    <td>
                        <label>Note : <?php echo ''; ?></label>
                    </td>
                    <br>
                    <td>
                        <label>OFF = Libur</label>
                        <br>

                        <label>Izin/Sakit/Cuti/Isoman mohon di input ke jadwal, 
                            <br>perubahan jadwal setelah tanggal 20 melalui menu validasi jadwal</label>
                    </td>
                </tr>

            
                
                <!-- /.box-headerx -->
                <div class="box-body table-responsive">
                    <table id="jadwal" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Unit</th>
                                <th rowspan="2">Nama Pegawai</th>
                                <?php
                                $nomor = 0;
                                while (strtotime($from) < strtotime($to)) {
                                    $nilai[] = $nomor++;
                                    $from = mktime(0, 0, 0, date("m", strtotime($from)), date("d", strtotime($from)) + 1, date("Y", strtotime($from)));
                                    $from = date("Y-m-d ", $from);
                                    $tanggalan = konversiTanggalDoang($from);
                                    $libur = fetch_array(bukaquery("select tm_hari_libur.jenis, tm_hari_libur.keterangan from tm_hari_libur where tanggal='$from'"));
                                    if ($libur['jenis'] != '') {
                                        echo "<th rowspan='2' style='background-color: red' title='" . $libur['jenis']  . " (" . $libur['keterangan']  . ") '>" . $tanggalan .  "(" . hariindo1($from) . ")</th>";
                                    } else {
                                        echo "<th rowspan='2'>" .  $tanggalan .  "(" . hariindo1($from) . ")</th>";
                                    }
                                }
                                ?>
                                <th title='Hari Kerja Pagi' style="text-align:center; background-color:lightblue;">HKP</th>
                                <th title='Hari Kerja Sore' style="text-align:center; background-color:lightblue;">HKS</th>
                                <th title='Hari Kerja Malam' style="text-align:center; background-color:lightblue;">HKM</th>
                                
                                <th title='Hari Libur Pagi' style="text-align:center; background-color:yellow;">HLP</th>
                                <th title='Hari Libur Sore' style="text-align:center; background-color:yellow;">HLS</th>
                                <th title='Hari Libur Malam' style="text-align:center; background-color:yellow;">HLM</th>

                                <th title='Hari Raya Pagi' style="text-align:center; background-color:pink;">HRP</th>
                                <th title='Hari Raya Sore' style="text-align:center; background-color:pink;">HRS</th>
                                <th title='Hari Raya Malam' style="text-align:center; background-color:pink;">HRM</th>

                                <th style="text-align:center; background-color:deeppink;">Total Hari</th>
                                <th style="text-align:center; background-color:deeppink;">Total Off</th>
                                <th style="text-align:center; background-color:deepskyblue;">Total Menit</th>
                                <th style="text-align:center; background-color:deepskyblue;">Total Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $no = 0;
                            if (date('d') > 20) { 
                                $awal = FormatTgl('Y-m-d', $awalbulandepan);
                                $akhir = FormatTgl('Y-m-d', $akhirbulandepan);
                                $tmtcek = TanggalAkhirBulanKemarin();
                                $bd = BulanDepanYM();
                            } else {
                                $awal = FormatTgl('Y-m-d', TanggalAwalBulan_());
                                $akhir = FormatTgl('Y-m-d', TanggalAkhirBulan());
                                $tmtcek = TanggalAkhirBulan();
                                $bd = date('m');
                            }
                            $sql_pegawai = bukaquery("SELECT
                                tm_pegawai.id_user,
                                tm_pegawai.nip,
                                tm_pegawai.nama_pegawai,
                                tm_unit.id_unit,
                                tm_unit.nama_unit,
                                sum(case when kode_shift='OFF' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as OFF,
                                sum(case when kode_shift='NS' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as NS,
                                sum(case when kode_shift='P' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as P,
                                sum(case when kode_shift='S' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as S,
                                sum(case when kode_shift='M' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as M,
                                sum(case when kode_shift='PS' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as PS,
                                sum(case when kode_shift='SM' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as SM,
                                sum(case when kode_shift='C' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as Cuti,
                                sum(case when kode_shift='I' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as Izin,
                                sum(case when kode_shift='Skt' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as Sakit,
                                sum(case when kode_shift='DL' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'  then 1 else 0 end ) as DL,
                                sum(case when kode_shift not in ('OFF','C','I','Skt') AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as hari_kerja
                            FROM
                                tm_pegawai
                                INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai = tm_pegawai.nip 
                                LEFT JOIN tm_shift ON tm_shift.id_absensi = tm_jadwal.id_absensi
                            WHERE
                                tm_pegawai.STATUS = 'AKTIF' 
                                AND tm_pegawai.id_unit LIKE '%$id_unit%'
                                -- AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
                            GROUP BY
                                tm_pegawai.id_user 
                            ORDER BY
                                tm_pegawai.nama_pegawai ASC");
                            while ($row = fetch_array($sql_pegawai)) {
                            
                            $no++;
                            $nip = $row['nip'];

                            // $menitkerja = fetch_array(bukaquery("SELECT
                            //     SUM(jumlah_menit) as menit_kerja
                            // FROM
                            //     tm_pegawai
                            //     INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                            //     LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai = tm_pegawai.nip 
                            //     LEFT JOIN tm_shift ON tm_shift.id_absensi = tm_jadwal.id_absensi
                            // WHERE
                            //     tm_pegawai.STATUS = 'AKTIF' 
                            //     AND tm_pegawai.nip=$nip
                            //     AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
                            // ORDER BY
                            //     tm_pegawai.nama_pegawai ASC"));

                            $harikerja = $row['hari_kerja'];  
                            $kerjashift = fetch_array(bukaquery("SELECT id_user, nip_pegawai,
                            IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS kerjapagi,
                            IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS kerjasore,
                            IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS kerjamalam,
                            sum(jumlah_menit) as kerjamenit
                            FROM tm_jadwal 
                            INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                            WHERE nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));

                            $liburshift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                            IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS liburpagi, 
                            IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS libursore, 
                            IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS liburmalam 
                            FROM tm_jadwal 
                            INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                            INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal                         
                            where tm_hari_libur.jenis IN('Libur Kerja','Libur Nasional','Cuti Bersama')and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));

                            $rayashift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                            IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS rayapagi, 
                            IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS rayasore, 
                            IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS rayamalam 
                            FROM tm_jadwal 
                            INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                            INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal 
                            where tm_hari_libur.jenis ='Libur Hari Raya' and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));    
                            
                            $jum_menit = $kerjashift ['kerjamenit'];
                         
                            ?>
                                <tr>
                                    <td><?php echo $no ?></td>
                                    <td><?php echo $row['nama_unit']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <?php

                                    for ($a = 1; $a <= FormatTgl('d', $akhir); $a++) {
                                        if (date('m') == 12 && date('d') > 20 ){
                                            $tmtcek_ =  date('Y')+1 . "-" . $bd . "-" . $a;
                                        }
                                        else {
                                            $tmtcek_ =  date('Y') . "-" . $bd . "-" . $a;
                                        }                                       
                                        $mapping = fetch_array(bukaquery("SELECT
                                                tm_shift.nama_shift,
                                                tm_shift.color,
                                                tm_shift.kode_shift,
                                                case 
                                                when tm_shift.color='A' then 'red-gradient'
                                                when tm_shift.color='B' then 'aqua-gradient'
                                                when tm_shift.color='C' then 'blue'
                                                when tm_shift.color='D' then 'red'
                                                when tm_shift.color='E' then 'green' 
                                                when tm_shift.color='F' then 'pink' 
                                                when tm_shift.color='G' then 'purple' 
                                                when tm_shift.color='H' then 'orange'
                                                when tm_shift.color='I' then 'Navy'
                                                when tm_shift.color='J' then 'teal'
                                                when tm_shift.color='K' then 'gray'
                                                when tm_shift.color='L' then 'purple-gradient'
                                                when tm_shift.color='M' then 'blue-gradient'
                                                when tm_shift.color='N' then 'black'
                                                else 'white' end as color 
                                                from tm_jadwal
                                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                                where tm_jadwal.nip_pegawai='$row[nip]' and tanggal_jadwal='$tmtcek_'"));
                                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                                        echo "<td title='" . $mapping['nama_shift']  . "'>" . ("") . "<a href='?" . paramEncrypt('module=jadwal-pegawai&act=add-jadwal-pegawai&id=' . $tmtcek_ . '&dpa=' . $row['id_user'] . '') . "'>
                                        <button type='submit' class='btn-xs bg-" . $mapping['color'] . "' >" . $mapping['kode_shift'] . " </button></a></td>";

                                          
                                    }
                                    ?>
                                    <!-- Pagix -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjapagi'] - $liburshift['liburpagi'] - $rayashift['rayapagi']; ?></td>
                                    <!-- Siang -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjasore'] - $liburshift['libursore'] - $rayashift['rayasore']; ?></td>
                                     <!-- Malam -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjamalam'] - $liburshift['liburmalam'] - $rayashift['rayamalam']; ?></td>
                                    
                                    <!-- Pagi(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburpagi']; ?></td>
                                    <!-- Siang(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['libursore']; ?></td>
                                    <!-- Malam(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburmalam']; ?></td>
                                    <!-- Pagi(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayapagi']; ?></td>
                                    <!-- Siang(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayasore']; ?></td>
                                    <!-- Malam(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayamalam']; ?></td>
                                    <!-- Vol. Hari Kerja -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $harikerja; ?></td>
                                    <!-- Off(L) -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $row['OFF'] + $row['Cuti'] + $row['Izin'] + $row['Sakit']; ?></td>
                                    <!-- Vol. Menit -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo $jum_menit; ?></td>
                                    <!-- Vol. Jam -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo $jum_menit / 60; ?></td>
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

    case "validasi-list-jadwal-pegawai":
        $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
        $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : date('Y')
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> VALIDASI JADWAL PEGAWAI.</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">

                <tr>
                    <td>
                        <label>Note :</label>
                    </td>
                    <br>
                    <td>
                        <label>OFF = Libur</label>
                        <br>

                        <label>Izin/Sakit/Cuti/Isoman mohon di input ke jadwal</label>
                    </td>
                    
                </tr>

                <?Php
                if (date('d') > 20)  { #dibuka tanggal brp
                    $from = TanggalAwalBulan(); #tampil daftar hari
                    $to = TanggalAkhirBulan(); 
                } else {
                    $from = TanggalAwalBulan();
                    $to = date('Y-m-t');
                }

                ?>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="jadwal" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Unit</th>
                                <th rowspan="2">Nama Pegawai</th>
                                <?php
                                $nomor = 0;
                                while (strtotime($from) < strtotime($to)) {
                                    $nilai[] = $nomor++;
                                    $from = mktime(0, 0, 0, date("m", strtotime($from)), date("d", strtotime($from)) + 1, date("Y", strtotime($from)));
                                    $from = date("Y-m-d ", $from);
                                    $tanggalan = konversiTanggalDoang($from);
                                    $libur = fetch_array(bukaquery("select tm_hari_libur.jenis, tm_hari_libur.keterangan from tm_hari_libur where tanggal='$from'"));
                                    if ($libur['jenis'] != '') {
                                        echo "<th rowspan='2' style='background-color: red' title='" . $libur['jenis']  . " (" . $libur['keterangan']  . ") '>" . $tanggalan .  "(" . hariindo1($from) . ")</th>";
                                    } else {
                                        echo "<th rowspan='2'>" .  $tanggalan .  "(" . hariindo1($from) . ")</th>";
                                    }
                                }
                                ?>
                                <th title='Hari Kerja Pagi' style="text-align:center; background-color:lightblue;">HKP</th>
                                <th title='Hari Kerja Sore' style="text-align:center; background-color:lightblue;">HKS</th>
                                <th title='Hari Kerja Malam' style="text-align:center; background-color:lightblue;">HKM</th>
                                
                                <th title='Hari Libur Pagi' style="text-align:center; background-color:yellow;">HLP</th>
                                <th title='Hari Libur Sore' style="text-align:center; background-color:yellow;">HLS</th>
                                <th title='Hari Libur Malam' style="text-align:center; background-color:yellow;">HLM</th>

                                <th title='Hari Raya Pagi' style="text-align:center; background-color:pink;">HRP</th>
                                <th title='Hari Raya Sore' style="text-align:center; background-color:pink;">HRS</th>
                                <th title='Hari Raya Malam' style="text-align:center; background-color:pink;">HRM</th>

                                <th style="text-align:center; background-color:deeppink;">Total Hari</th>
                                <th style="text-align:center; background-color:deeppink;">Total Off</th>
                                <th style="text-align:center; background-color:deepskyblue;">Total Menit</th>
                                <th style="text-align:center; background-color:deepskyblue;">Total Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php
                                $no = 0;
                                if (date('d') > 20) { #dibuka tanggal brp
                                    $awal = FormatTgl('Y-m-d', TanggalAwalBulan_()); #tampil jadwal 
                                    $akhir = FormatTgl('Y-m-d', TanggalAkhirBulan()); #tampil jadwal 
                                    $tmtcek = TanggalAkhirBulan();
                                    $bd = BulanSekarangYM(); #setup input bulan jadwal
                                } else {
                                    $awal = FormatTgl('Y-m-d', TanggalAwalBulan_());
                                    $akhir = FormatTgl('Y-m-d', TanggalAkhirBulan());
                                    $tmtcek = TanggalAkhirBulan();
                                    $bd = date('m');
                                }
                                $sql_pegawai = bukaquery("SELECT
                                    tm_pegawai.id_user,
                                    tm_pegawai.nip,
                                    tm_pegawai.nama_pegawai,
                                    tm_unit.id_unit,
                                    tm_unit.nama_unit,
                                    sum(case when kode_shift='OFF' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as OFF,
                                    sum(case when kode_shift='NS' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as NS,
                                    sum(case when kode_shift='P' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as P,
                                    sum(case when kode_shift='S' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as S,
                                    sum(case when kode_shift='M' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as M,
                                    sum(case when kode_shift='PS' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as PS,
                                    sum(case when kode_shift='SM' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as SM,
                                    sum(case when kode_shift='C' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as Cuti,
                                    sum(case when kode_shift='I' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as Izin,
                                    sum(case when kode_shift='Skt' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as Sakit,
                                    sum(case when kode_shift='DL' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'  then 1 else 0 end ) as DL,
                                    sum(case when kode_shift not in ('OFF','C','I','Skt') AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as hari_kerja
                                FROM
                                    tm_pegawai
                                    INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                    LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai = tm_pegawai.nip 
                                    LEFT JOIN tm_shift ON tm_shift.id_absensi = tm_jadwal.id_absensi
                                WHERE
                                    tm_pegawai.STATUS = 'AKTIF' 
                                    AND tm_pegawai.id_unit LIKE '%$id_unit%'
                                    -- AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
                                GROUP BY
                                    tm_pegawai.id_user 
                                ORDER BY
                                    tm_pegawai.nama_pegawai ASC");
                                while ($row = fetch_array($sql_pegawai)) {
                                
                                $no++;
                                $nip = $row['nip'];
    
                                // $menitkerja = fetch_array(bukaquery("SELECT
                                //     SUM(jumlah_menit) as menit_kerja
                                // FROM
                                //     tm_pegawai
                                //     INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                //     LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai = tm_pegawai.nip 
                                //     LEFT JOIN tm_shift ON tm_shift.id_absensi = tm_jadwal.id_absensi
                                // WHERE
                                //     tm_pegawai.STATUS = 'AKTIF' 
                                //     AND tm_pegawai.nip=$nip
                                //     AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
                                // ORDER BY
                                //     tm_pegawai.nama_pegawai ASC"));
    
                                $harikerja = $row['hari_kerja'];  
                                $kerjashift = fetch_array(bukaquery("SELECT id_user, nip_pegawai,
                                IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS kerjapagi,
                                IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS kerjasore,
                                IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS kerjamalam,
                                sum(jumlah_menit) as kerjamenit
                                FROM tm_jadwal 
                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                WHERE nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));
    
                                $liburshift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                                IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS liburpagi, 
                                IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS libursore, 
                                IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS liburmalam
                                FROM tm_jadwal 
                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal                         
                                where tm_hari_libur.jenis IN('Libur Kerja','Libur Nasional','Cuti Bersama')and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));
    
                                $rayashift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                                IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS rayapagi, 
                                IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS rayasore, 
                                IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS rayamalam
                                FROM tm_jadwal 
                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal 
                                where tm_hari_libur.jenis ='Libur Hari Raya' and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));   
                                
                                $jum_menit = $kerjashift ['kerjamenit'];
                             
                                ?>
                                <tr>
                                    <td><?php echo $no ?></td>
                                    <td><?php echo $row['nama_unit']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <?php

                                    for ($a = 1; $a <= FormatTgl('d', $akhir); $a++) {
                                        $tmtcek_ =  date('Y') . "-" . $bd . "-" . $a;
                                        $mapping = fetch_array(bukaquery("SELECT
                                                tm_shift.nama_shift,
                                                tm_shift.color,
                                                case 
                                                when tm_shift.color='A' then 'red-gradient'
                                                when tm_shift.color='B' then 'aqua-gradient'
                                                when tm_shift.color='C' then 'blue'
                                                when tm_shift.color='D' then 'red'
                                                when tm_shift.color='E' then 'green' 
                                                when tm_shift.color='F' then 'pink' 
                                                when tm_shift.color='G' then 'purple' 
                                                when tm_shift.color='H' then 'orange'
                                                when tm_shift.color='I' then 'Navy'
                                                when tm_shift.color='J' then 'teal'
                                                when tm_shift.color='K' then 'gray'
                                                when tm_shift.color='L' then 'purple-gradient'
                                                when tm_shift.color='M' then 'blue-gradient'
                                                when tm_shift.color='N' then 'black'
                                                else 'white' end as color 
                                                from tm_jadwal
                                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                                where tm_jadwal.nip_pegawai='$row[nip]' and tanggal_jadwal='$tmtcek_'"));
                                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                                        echo "<td>" . ("") . "<a href='?" . paramEncrypt('module=jadwal-pegawai&act=add-validasi-pegawai&id=' . $tmtcek_ . '&dpa=' . $row['id_user'] . '') . "'><button type='submit' class='btn-xs bg-" . $mapping['color'] . "' >"
                                            . $mapping['nama_shift'] . " </button></a></td>";
                                    }
                                    ?>
                                    <!-- Pagix -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjapagi'] - $liburshift['liburpagi'] - $rayashift['rayapagi']; ?></td>
                                    <!-- Siang -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjasore'] - $liburshift['libursore'] - $rayashift['rayasore']; ?></td>
                                     <!-- Malam -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjamalam'] - $liburshift['liburmalam'] - $rayashift['rayamalam']; ?></td>
                                    
                                    <!-- Pagi(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburpagi']; ?></td>
                                    <!-- Siang(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['libursore']; ?></td>
                                    <!-- Malam(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburmalam']; ?></td>
                                    <!-- Pagi(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayapagi']; ?></td>
                                    <!-- Siang(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayasore']; ?></td>
                                    <!-- Malam(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayamalam']; ?></td>
                                    <!-- Vol. Hari Kerja -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $harikerja; ?></td>
                                    <!-- Off(L) -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $row['OFF'] + $row['Cuti'] + $row['Izin'] + $row['Sakit']; ?></td>
                                    <!-- Vol. Menit -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo $jum_menit; ?></td>
                                    <!-- Vol. Jam -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo $jum_menit / 60; ?></td>
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
        
    case "validasi-list-jadwal-pegawai-bulankemarin":
        $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
        $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : date('Y')
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> VALIDASI JADWAL PEGAWAI.</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">

                <tr>
                    <td>
                        <label>Note :</label>
                    </td>
                    <br>
                    <td>
                        <label>OFF = Libur</label>
                        <br>

                        <label>Izin/Sakit/Cuti/Isoman mohon di input ke jadwal</label>
                    </td>
                    
                </tr>

                <?Php
                if (date('d') <= $setup['validasi_jadwal'])  { #dibuka tanggal brp
                    $from = TanggalAwalBulanKemarin_(); #tampil daftar hari
                    $to = TanggalAkhirBulanKemarin(); 
                } else {
                    $from = TanggalAwalBulanKemarin_();
                    $to = date('Y-m-t');
                }

                ?>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="jadwal" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Unit</th>
                                <th rowspan="2">Nama Pegawai</th>
                                <?php
                                $nomor = 0;
                                while (strtotime($from) < strtotime($to)) {
                                    $nilai[] = $nomor++;
                                    $from = mktime(0, 0, 0, date("m", strtotime($from)), date("d", strtotime($from)) + 1, date("Y", strtotime($from)));
                                    $from = date("Y-m-d ", $from);
                                    $tanggalan = konversiTanggalDoang($from);
                                    $libur = fetch_array(bukaquery("select tm_hari_libur.jenis, tm_hari_libur.keterangan from tm_hari_libur where tanggal='$from'"));
                                    if ($libur['jenis'] != '') {
                                        echo "<th rowspan='2' style='background-color: red' title='" . $libur['jenis']  . " (" . $libur['keterangan']  . ") '>" . $tanggalan .  "(" . hariindo1($from) . ")</th>";
                                    } else {
                                        echo "<th rowspan='2'>" .  $tanggalan .  "(" . hariindo1($from) . ")</th>";
                                    }
                                }
                                ?>
                                <th title='Hari Kerja Pagi' style="text-align:center; background-color:lightblue;">HKP</th>
                                <th title='Hari Kerja Sore' style="text-align:center; background-color:lightblue;">HKS</th>
                                <th title='Hari Kerja Malam' style="text-align:center; background-color:lightblue;">HKM</th>
                                
                                <th title='Hari Libur Pagi' style="text-align:center; background-color:yellow;">HLP</th>
                                <th title='Hari Libur Sore' style="text-align:center; background-color:yellow;">HLS</th>
                                <th title='Hari Libur Malam' style="text-align:center; background-color:yellow;">HLM</th>

                                <th title='Hari Raya Pagi' style="text-align:center; background-color:pink;">HRP</th>
                                <th title='Hari Raya Sore' style="text-align:center; background-color:pink;">HRS</th>
                                <th title='Hari Raya Malam' style="text-align:center; background-color:pink;">HRM</th>

                                <th style="text-align:center; background-color:deeppink;">Total Hari</th>
                                <th style="text-align:center; background-color:deeppink;">Total Off</th>
                                <th style="text-align:center; background-color:deepskyblue;">Total Menit</th>
                                <th style="text-align:center; background-color:deepskyblue;">Total Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php
                                $no = 0;
                                    if (date('d') <= $setup['validasi_jadwal']) { #dibuka tanggal brp
                                    $awal = FormatTgl('Y-m-d', TanggalAwalBulanKemarin__()); #tampil jadwal 
                                    $akhir = FormatTgl('Y-m-d', TanggalAkhirBulanKemarin()); #tampil jadwal 
                                    $tmtcek = TanggalAkhirBulanKemarin();
                                    $bd = BulanKemarinYM(); #setup input bulan jadwal
                                } else {
                                    $awal = FormatTgl('Y-m-d', TanggalAwalBulanKemarin__());
                                    $akhir = FormatTgl('Y-m-d', TanggalAkhirBulanKemarin());
                                    $tmtcek = TanggalAkhirBulanKemarin();
                                    $bd = date('m');
                                }
                                $sql_pegawai = bukaquery("SELECT
                                    tm_pegawai.id_user,
                                    tm_pegawai.nip,
                                    tm_pegawai.nama_pegawai,
                                    tm_unit.id_unit,
                                    tm_unit.nama_unit,
                                    sum(case when kode_shift='OFF' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as OFF,
                                    sum(case when kode_shift='NS' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as NS,
                                    sum(case when kode_shift='P' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as P,
                                    sum(case when kode_shift='S' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as S,
                                    sum(case when kode_shift='M' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as M,
                                    sum(case when kode_shift='PS' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as PS,
                                    sum(case when kode_shift='SM' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as SM,
                                    sum(case when kode_shift='C' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as Cuti,
                                    sum(case when kode_shift='I' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as Izin,
                                    sum(case when kode_shift='Skt' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as Sakit,
                                    sum(case when kode_shift='DL' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'  then 1 else 0 end ) as DL,
                                    sum(case when kode_shift not in ('OFF','C','I','Skt') AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as hari_kerja
                                FROM
                                    tm_pegawai
                                    INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                    LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai = tm_pegawai.nip 
                                    LEFT JOIN tm_shift ON tm_shift.id_absensi = tm_jadwal.id_absensi
                                WHERE
                                    tm_pegawai.STATUS = 'AKTIF' 
                                    AND tm_pegawai.id_unit LIKE '%$id_unit%'
                                    -- AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
                                GROUP BY
                                    tm_pegawai.id_user 
                                ORDER BY
                                    tm_pegawai.nama_pegawai ASC");
                                while ($row = fetch_array($sql_pegawai)) {
                                
                                $no++;
                                $nip = $row['nip'];
    
                                // $menitkerja = fetch_array(bukaquery("SELECT
                                //     SUM(jumlah_menit) as menit_kerja
                                // FROM
                                //     tm_pegawai
                                //     INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                //     LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai = tm_pegawai.nip 
                                //     LEFT JOIN tm_shift ON tm_shift.id_absensi = tm_jadwal.id_absensi
                                // WHERE
                                //     tm_pegawai.STATUS = 'AKTIF' 
                                //     AND tm_pegawai.nip=$nip
                                //     AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
                                // ORDER BY
                                //     tm_pegawai.nama_pegawai ASC"));
    
                                $harikerja = $row['hari_kerja'];  
                                $kerjashift = fetch_array(bukaquery("SELECT id_user, nip_pegawai,
                                IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS kerjapagi,
                                IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS kerjasore,
                                IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS kerjamalam,
                                sum(jumlah_menit) as kerjamenit
                                FROM tm_jadwal 
                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                WHERE nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));
    
                                $liburshift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                                IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS liburpagi, 
                                IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS libursore, 
                                IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS liburmalam
                                FROM tm_jadwal 
                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal                         
                                where tm_hari_libur.jenis IN('Libur Kerja','Libur Nasional','Cuti Bersama')and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));
    
                                $rayashift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                                IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS rayapagi, 
                                IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS rayasore, 
                                IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS rayamalam
                                FROM tm_jadwal 
                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal 
                                where tm_hari_libur.jenis ='Libur Hari Raya' and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));   
                                
                                $jum_menit = $kerjashift ['kerjamenit'];
                             
                                ?>
                                <tr>
                                    <td><?php echo $no ?></td>
                                    <td><?php echo $row['nama_unit']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <?php

                                    for ($a = 1; $a <= FormatTgl('d', $akhir); $a++) {
                                        $tmtcek_ =  date('Y') . "-" . $bd . "-" . $a;
                                        $mapping = fetch_array(bukaquery("SELECT
                                                tm_shift.nama_shift,
                                                tm_shift.color,
                                                case 
                                                when tm_shift.color='A' then 'red-gradient'
                                                when tm_shift.color='B' then 'aqua-gradient'
                                                when tm_shift.color='C' then 'blue'
                                                when tm_shift.color='D' then 'red'
                                                when tm_shift.color='E' then 'green' 
                                                when tm_shift.color='F' then 'pink' 
                                                when tm_shift.color='G' then 'purple' 
                                                when tm_shift.color='H' then 'orange'
                                                when tm_shift.color='I' then 'Navy'
                                                when tm_shift.color='J' then 'teal'
                                                when tm_shift.color='K' then 'gray'
                                                when tm_shift.color='L' then 'purple-gradient'
                                                when tm_shift.color='M' then 'blue-gradient'
                                                when tm_shift.color='N' then 'black'
                                                else 'white' end as color 
                                                from tm_jadwal
                                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                                where tm_jadwal.nip_pegawai='$row[nip]' and tanggal_jadwal='$tmtcek_'"));
                                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                                        echo "<td>" . ("") . "<a href='?" . paramEncrypt('module=jadwal-pegawai&act=add-validasi-pegawai&id=' . $tmtcek_ . '&dpa=' . $row['id_user'] . '') . "'><button type='submit' class='btn-xs bg-" . $mapping['color'] . "' >"
                                            . $mapping['nama_shift'] . " </button></a></td>";
                                    }
                                    ?>
                                    <!-- Pagix -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjapagi'] - $liburshift['liburpagi'] - $rayashift['rayapagi']; ?></td>
                                    <!-- Siang -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjasore'] - $liburshift['libursore'] - $rayashift['rayasore']; ?></td>
                                     <!-- Malam -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjamalam'] - $liburshift['liburmalam'] - $rayashift['rayamalam']; ?></td>
                                    
                                    <!-- Pagi(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburpagi']; ?></td>
                                    <!-- Siang(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['libursore']; ?></td>
                                    <!-- Malam(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburmalam']; ?></td>
                                    <!-- Pagi(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayapagi']; ?></td>
                                    <!-- Siang(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayasore']; ?></td>
                                    <!-- Malam(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayamalam']; ?></td>
                                    <!-- Vol. Hari Kerja -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $harikerja; ?></td>
                                    <!-- Off(L) -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $row['OFF'] + $row['Cuti'] + $row['Izin'] + $row['Sakit']; ?></td>
                                    <!-- Vol. Menit -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo $jum_menit; ?></td>
                                    <!-- Vol. Jam -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo $jum_menit / 60; ?></td>
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

    case "jadwal-pegawai-bulankemarin":
        $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
        $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : date('Y')
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> VALIDASI JADWAL PEGAWAI BULAN KEMARIN</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">

                <tr>
                    <td>
                        <label>Note :</label>
                    </td>
                    <br>
                    <td>
                        <label>OFF = Libur</label>
                        <br>

                        <label>Izin/Sakit/Cuti/Isoman mohon di input ke jadwal</label>
                    </td>
                    
                </tr>

                <?Php
                if (date('d') < 31)  { #dibuka tanggal brp
                    $from = TanggalAwalBulanKemarin_(); #tampil daftar hari
                    $to = TanggalAkhirBulanKemarin(); 
                } else {
                    $from = TanggalAwalBulanKemarin_();
                    $to = date('Y-m-t');
                }

                ?>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="jadwal" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Unit</th>
                                <th rowspan="2">Nama Pegawai</th>
                                <?php
                                $nomor = 0;
                                while (strtotime($from) < strtotime($to)) {
                                    $nilai[] = $nomor++;
                                    $from = mktime(0, 0, 0, date("m", strtotime($from)), date("d", strtotime($from)) + 1, date("Y", strtotime($from)));
                                    $from = date("Y-m-d ", $from);
                                    $tanggalan = konversitanggal($from);
                                    $libur = fetch_array(bukaquery("select tm_hari_libur.jenis, tm_hari_libur.keterangan from tm_hari_libur where tanggal='$from'"));
                                    if ($libur['jenis'] != '') {
                                        echo "<th rowspan='2' style='background-color: red' title='" . $libur['jenis']  . " (" . $libur['keterangan']  . ") '>" . $tanggalan .  "(" . hariindo($from) . ")</th>";
                                    } else {
                                        echo "<th rowspan='2'>" .  $tanggalan .  "(" . hariindo($from) . ")</th>";
                                    }
                                }
                                ?>
                                <th colspan="3" style="text-align:center; background-color:lightblue;">Hari Kerja</th>
                                <th colspan="3" style="text-align:center; background-color:yellow;">Hari Minggu/Libur Nasional</th>
                                <th colspan="3" style="text-align:center; background-color:pink;">Hari Libur Raya</th>
                                <th colspan="2" style="text-align:center; background-color:deeppink;">Vol. Total</th>
                                <th rowspan="2" style="text-align:center; background-color:deepskyblue;">Total Menit Kerja</th>
                                <th rowspan="2" style="text-align:center; background-color:deepskyblue;">Total Jam Kerja</th>
                            </tr>
                            <tr>
                                <th style="text-align:center; background-color:lightblue;">P</th>
                                <th style="text-align:center; background-color:lightblue;">S</th>
                                <th style="text-align:center; background-color:lightblue;">M</th>
                                
                                <th style="text-align:center; background-color:yellow;">P</th>
                                <th style="text-align:center; background-color:yellow;">S</th>
                                <th style="text-align:center; background-color:yellow;">M</th>

                                <th style="text-align:center; background-color:pink;">P</th>
                                <th style="text-align:center; background-color:pink;">S</th>
                                <th style="text-align:center; background-color:pink;">M</th>

                                <th style="text-align:center; background-color:deeppink;">Hari kerja</th>
                                <th style="text-align:center; background-color:deeppink;">Off</th>
                                <!-- <th style="text-align:center; background-color:deeppink;">I/S</th> -->

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            if (date('d') < 31) { #dibuka tanggal brp
                                $awal = FormatTgl('Y-m-d', TanggalAwalBulanKemarin__()); #tampil jadwal 
                                $akhir = FormatTgl('Y-m-d', TanggalAkhirBulanKemarin()); #tampil jadwal 
                                $tmtcek = TanggalAkhirBulanKemarin();
                                $bd = BulanKemarinYM(); #setup input bulan jadwal
                            } else {
                                $awal = FormatTgl('Y-m-d', TanggalAwalBulanKemarin__());
                                $akhir = FormatTgl('Y-m-d', TanggalAkhirBulanKemarin());
                                $tmtcek = TanggalAkhirBulanKemarin();
                                $bd = date('m');
                            }
                            $sql_pegawai = bukaquery("SELECT
                           tm_pegawai.id_user,
                           tm_pegawai.nip,
                           tm_pegawai.nama_pegawai,
                           tm_unit.id_unit,
                           tm_unit.nama_unit,
                           sum(case when tm_jadwal.id_absensi='ABS-000001' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as satu,
                           sum(case when tm_jadwal.id_absensi='ABS-000002' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as dua,
                           sum(case when tm_jadwal.id_absensi='ABS-000003' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000004' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empat,
                           sum(case when tm_jadwal.id_absensi='ABS-000005' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as lima,
                           sum(case when tm_jadwal.id_absensi='ABS-000006' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as enam,
                           sum(case when tm_jadwal.id_absensi='ABS-000007' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000008' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as delapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000009' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000010' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sepuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000011' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sebelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000012' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000013' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000014' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000015' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000016' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as enambelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000017' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tujuhbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000018' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as delapanbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000019' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sembilanbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000020' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000021' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000022' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000023' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000024' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000025' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as dualima,
                           sum(case when tm_jadwal.id_absensi='ABS-000026' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duaenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000027' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duatujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000028' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duadelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000029' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duasembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000030' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000031' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000032' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000033' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000034' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000035' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigalima,
                           sum(case when tm_jadwal.id_absensi='ABS-000036' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigaenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000037' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigatujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000038' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigadelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000039' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigasembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000040' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatpuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000041' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000042' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdua,
                           sum(case when tm_jadwal.id_absensi='ABS-000043' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000044' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000045' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatlima,
                           sum(case when tm_jadwal.id_absensi='ABS-000041' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000042' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdua,
                           sum(case when tm_jadwal.id_absensi='ABS-000043' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000044' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000045' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatlima,
                           sum(case when tm_jadwal.id_absensi='ABS-000046' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000047' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000048' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000049' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000050' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000051' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000052' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000053' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000054' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000055' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limalima,
                           sum(case when tm_jadwal.id_absensi='ABS-000000' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as nol
                           FROM tm_pegawai
                           INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                           LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai=tm_pegawai.nip
                           where tm_pegawai.status='AKTIF' and tm_pegawai.id_unit LIKE '%$id_unit%' 
                           GROUP BY tm_pegawai.id_user
                           order by tm_unit.nama_unit desc");
                            while ($row = fetch_array($sql_pegawai)) {
                                $no++;
                                $nip = $row['nip'];

                                $menitkerja = (($row['satu']*450) + ($row['dua']*450) + ($row['tiga']*450) + ($row['empat']*480) + ($row['lima']*360) + ($row['enam']*360) +($row['tujuh']*390) + ($row['delapan']*600) + ($row['sembilan']*390) + ($row['sepuluh']*420) + ($row['sebelas']*720) + ($row['duabelas']*720) + ($row['tigabelas']*720) + ($row['empatbelas']*630) + ($row['limabelas']*840) + ($row['enambelas']*390) + ($row['tujuhbelas']*360) + ($row['delapanbelas']*360) + ($row['sembilanbelas']*420) + ($row['duapuluh']*390) + ($row['duasatu']*420) + ($row['duadua']*1020) + ($row['duatiga']*1020) + ($row['duaempat']*660) + ($row['dualima']*660) + ($row['duaenam']*660) + ($row['duadelapan']*270) + ($row['duasembilan']*0) + ($row['tigatiga']*450) + ($row['tigaempat']*300) + ($row['tigalima']*420) + $row['tigaenam']*420 + $row['tigatujuh']*450 + $row['tigadelapan']*420 + $row['tigasembilan']*420 + $row['empatpuluh']*420 + $row['empatsatu']*420 + $row['empatdua']*630 + $row['empattiga']*390 + $row['empatempat']*420 + $row['empatlima']*450 + $row['empatenam']*420+ $row['empattujuh']*420+ $row['empatdelapan']*420+ $row['empatsembilan']*330+ $row['limapuluh']*800+ $row['limasatu']*800+ $row['limadua']*420+ $row['limatiga']*420+ $row['limaempat']*420+ $row['limalima']*350);  

                                 $harikerja = $row['satu'] + $row['dua'] + $row['tiga'] + $row['empat'] + $row['lima'] + $row['enam'] + $row['tujuh'] + ($row['delapan']*2) + $row['sembilan'] + $row['sepuluh'] + ($row['sebelas']*2) + ($row['duabelas']*2) + ($row['tigabelas']*2) + ($row['empatbelas']*2) + ($row['limabelas']*2) + ($row['enambelas']*2) + $row['tujuhbelas'] + $row['delapanbelas'] + $row['sembilanbelas'] + $row['duapuluh'] + $row['duasatu'] + ($row['duadua']*2) + ($row['duatiga']*2) + ($row['duaempat']) + ($row['dualima']) + ($row['duaenam']) + $row['duadelapan'] + $row['duasembilan'] + $row['tigatiga'] + $row['tigaempat'] + $row['tigalima'] + $row['tigaenam'] + $row['tigatujuh'] + $row['tigadelapan'] + $row['tigasembilan'] + $row['empatpuluh'] + $row['empatsatu'] + ($row['empatdua']*2) + $row['empattiga'] + $row['empatempat'] + $row['empatlima'] + $row['empatenam'] + $row['empattujuh'] + $row['empatdelapan'] + $row['empatsembilan'] + ($row['limapuluh']*2)+ ($row['limasatu']*2) + $row['limadua']+ $row['limatiga']+ $row['limaempat']+ $row['limalima'];
        
                                $kerjashift = fetch_array(bukaquery("SELECT id_user, nip_pegawai,
                        IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS kerjapagi,
                        IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS kerjasore,
                        IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS kerjamalam 
                        FROM tm_jadwal 
                        INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                        WHERE nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));
        
                                $liburshift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                        IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS liburpagi, 
                        IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS libursore, 
                        IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS liburmalam 
                        FROM tm_jadwal 
                        INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                        INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal                         
                        where tm_hari_libur.jenis IN('Libur Kerja','Libur Nasional','Cuti Bersama')and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));
        
                                $rayashift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                        IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS rayapagi, 
                        IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS rayasore, 
                        IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS rayamalam 
                        FROM tm_jadwal 
                        INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                        INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal 
                        where tm_hari_libur.jenis ='Libur Hari Raya' and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));      
                            ?>
                                <tr>
                                    <td><?php echo $no ?></td>
                                    <td><?php echo $row['nama_unit']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <?php

                                    for ($a = 1; $a <= FormatTgl('d', $akhir); $a++) {
                                        // $tmtcek_ =  date('Y') . "-" . $bd . "-" . $a;
                                        if (date('m') == 1 ){
                                            $tmtcek_ =  date('Y')-1 . "-" . $bd . "-" . $a;
                                        }
                                        else {
                                            $tmtcek_ =  date('Y') . "-" . $bd . "-" . $a;
                                        }
                                        $mapping = fetch_array(bukaquery("SELECT
                                                tm_shift.nama_shift,
                                                tm_shift.color,
                                                case 
                                                when tm_shift.color='A' then 'red-gradient'
                                                when tm_shift.color='B' then 'aqua-gradient'
                                                when tm_shift.color='C' then 'blue'
                                                when tm_shift.color='D' then 'red'
                                                when tm_shift.color='E' then 'green' 
                                                when tm_shift.color='F' then 'pink' 
                                                when tm_shift.color='G' then 'purple' 
                                                when tm_shift.color='H' then 'orange'
                                                when tm_shift.color='I' then 'Navy'
                                                when tm_shift.color='J' then 'teal'
                                                when tm_shift.color='K' then 'gray'
                                                when tm_shift.color='L' then 'purple-gradient'
                                                when tm_shift.color='M' then 'blue-gradient'
                                                when tm_shift.color='N' then 'black'
                                                else 'white' end as color 
                                                from tm_jadwal
                                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                                where tm_jadwal.nip_pegawai='$row[nip]' and tanggal_jadwal='$tmtcek_'"));
                                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                                        echo "<td>" . ("") . "<a href='?" . paramEncrypt('module=jadwal-pegawai&act=add-validasi-pegawai&id=' . $tmtcek_ . '&dpa=' . $row['id_user'] . '') . "'><button type='submit' class='btn-xs bg-" . $mapping['color'] . "' >"
                                            . $mapping['nama_shift'] . " </button></a></td>";
                                    }
                                    ?>
                                    <!-- Pagi -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjapagi'] - $liburshift['liburpagi'] - $rayashift['rayapagi']; ?></td>
                                    <!-- Siang -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjasore'] - $liburshift['libursore'] - $rayashift['rayasore']; ?></td>
                                     <!-- Malam -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjamalam'] - $liburshift['liburmalam'] - $rayashift['rayamalam']; ?></td>
                                    
                                    <!-- Pagi(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburpagi']; ?></td>
                                    <!-- Siang(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['libursore']; ?></td>
                                    <!-- Malam(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburmalam']; ?></td>
                                    <!-- Pagi(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayapagi']; ?></td>
                                    <!-- Siang(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayasore']; ?></td>
                                    <!-- Malam(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayamalam']; ?></td>
                                    <!-- Vol. Hari Kerja -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $harikerja; ?></td>
                                    <!-- Off(L) -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $row['nol'] + $row['tigapuluh'] + $row['tigasatu'] + $row['tigadua']; ?></td>
                                    <!-- Vol. Menit -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo $menitkerja; ?></td>
                                    <!-- Vol. Jam -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo $menitkerja / 60; ?></td>
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

    case "list-data-maping-jadwal-pegawai":
        $tampil_cuti = 'yes';
        $cuti = bukaquery("SELECT tm_hari_cuti.id_cuti, tm_hari_cuti.tanggal, tm_pegawai.nama_pegawai
                                        FROM tm_hari_cuti
                                        INNER JOIN tm_cuti ON tm_cuti.id_cuti=tm_hari_cuti.id_cuti
                                        INNER JOIN tm_pegawai ON tm_pegawai.id_user=tm_cuti.id_user");
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST MAPING DATA JADWAL PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div id="calendar"></div>
            </div>
        </div>

    <?php
        break;

    case "rekap-data-jadwal-pegawai":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> REKAPITULASI JADWAL PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'); ?>">
                    <button type="button" class="btn btn-warning fa fa-undo" title="Kembali"> Kembali
                    </button>
                </a>
                <br><br>
                <form action="" method="post">
                    <div class="form-group">
                        <table align="">
                            <tr>
                                <td>
                                    <label>Bulan</label>
                                </td>
                                <td>
                                    <label>Tahun</label>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" required>
                                        <?php loadBln('-Pilih Bulan-'); ?>
                                    </select></td>
                                <td>
                                    <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                                        <?php loadThn('-Pilih Tahun-'); ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                </td>
                                
                                
                            </tr>
                        </table>
                    </div>
                </form>
                <?php

                $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
                $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : date('Y');
                $tmtcek = FormatTgl("Y-m-d", $tahun_post . "-" . $bulan_post . "-01");
                $from = FormatTgl('00-m-Y', $tmtcek);
                $to = FormatTgl('t-m-Y', $tmtcek);

                if ($bulan_post == 0 or $tahun_post == 0) {
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> <label>Bulan dan Tahun Tidak Sesuai</label></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                <?php } else {
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list center-block">
                            <label align="center">Validasi Jadwal <?php echo konversiBulanTahun($tmtcek); ?></label>
                        </h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                <?php }
                ?>
                <div class="box-body table-responsive">
                    <table id="jadwal" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2">No <?php echo $bulan_setup; ?></th>
                                <th rowspan="2">Unit</th>
                                <th rowspan="2">Nama Pegawai</th>
                                <?php while (strtotime($from) < strtotime($to)) {
                                    $from = mktime(0, 0, 0, date("m", strtotime($from)), date("d", strtotime($from)) + 1, date("Y", strtotime($from)));
                                    $from = date("Y-m-d ", $from);
                                    $tanggalan = konversitanggal($from);
                                    $libur = fetch_array(bukaquery("select tm_hari_libur.jenis, tm_hari_libur.keterangan from tm_hari_libur where tanggal='$from'"));
                                    if ($libur['jenis'] != '') {
                                        echo "<th rowspan='2' style='background-color: red' title='" . $libur['jenis']  . " (" . $libur['keterangan']  . ") '>" . $tanggalan .  "(" . hariindo($from) . ")</th>";
                                    } else {
                                        echo "<th rowspan='2'>" .  $tanggalan .  "(" . hariindo($from) . ")</th>";
                                    }
                                }
                                ?>
                                <th colspan="3" style="text-align:center; background-color:lightblue;">Hari Kerja</th>
                                <th colspan="3" style="text-align:center; background-color:yellow;">Hari Minggu/Libur Nasional</th>
                                <th colspan="3" style="text-align:center; background-color:pink;">Hari Libur Raya</th>
                                <th colspan="2" style="text-align:center; background-color:deeppink;">Vol. Total</th>
                                <th rowspan="2" style="text-align:center; background-color:deepskyblue;">Total Menit Kerja</th>
                                <th rowspan="2" style="text-align:center; background-color:deepskyblue;">Total Jam Kerja</th>
                            </tr>
                            <tr>
                                <th style="text-align:center; background-color:lightblue;">P</th>
                                <th style="text-align:center; background-color:lightblue;">S</th>
                                <th style="text-align:center; background-color:lightblue;">M</th>
                                
                                <th style="text-align:center; background-color:yellow;">P</th>
                                <th style="text-align:center; background-color:yellow;">S</th>
                                <th style="text-align:center; background-color:yellow;">M</th>

                                <th style="text-align:center; background-color:pink;">P</th>
                                <th style="text-align:center; background-color:pink;">S</th>
                                <th style="text-align:center; background-color:pink;">M</th>

                                <th style="text-align:center; background-color:deeppink;">Hari kerja</th>
                                <th style="text-align:center; background-color:deeppink;">Off</th>
                                <!-- <th style="text-align:center; background-color:deeppink;">I/S</th> -->

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $awal = FormatTgl("Y-m-d", $tahun_post . "-" . $bulan_post . "-01");
                            $akhir = FormatTgl("Y-m-t", $tahun_post . "-" . $bulan_post . "-01");
                            $tmtcek = TanggalAkhirBulanKemarin();
                            $sql_pegawai = bukaquery("SELECT
                            tm_pegawai.id_user,
                            tm_pegawai.nip,
                            tm_pegawai.nama_pegawai,
                            tm_unit.id_unit,
                            tm_unit.nama_unit,
                            sum(case when tm_jadwal.id_absensi='ABS-000001' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as satu,
                           sum(case when tm_jadwal.id_absensi='ABS-000002' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as dua,
                           sum(case when tm_jadwal.id_absensi='ABS-000003' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000004' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empat,
                           sum(case when tm_jadwal.id_absensi='ABS-000005' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as lima,
                           sum(case when tm_jadwal.id_absensi='ABS-000006' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as enam,
                           sum(case when tm_jadwal.id_absensi='ABS-000007' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000008' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as delapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000009' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000010' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sepuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000011' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sebelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000012' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000013' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000014' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000015' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000016' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as enambelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000017' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tujuhbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000018' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as delapanbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000019' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sembilanbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000020' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000021' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000022' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000023' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000024' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000025' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as dualima,
                           sum(case when tm_jadwal.id_absensi='ABS-000026' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duaenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000027' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duatujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000028' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duadelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000029' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duasembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000030' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000031' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000032' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000033' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000034' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000035' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigalima,
                           sum(case when tm_jadwal.id_absensi='ABS-000036' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigaenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000037' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigatujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000038' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigadelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000039' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigasembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000040' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatpuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000041' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000042' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdua,
                           sum(case when tm_jadwal.id_absensi='ABS-000043' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000044' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000045' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatlima,
                           sum(case when tm_jadwal.id_absensi='ABS-000041' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000042' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdua,
                           sum(case when tm_jadwal.id_absensi='ABS-000043' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000044' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000045' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatlima,
                           sum(case when tm_jadwal.id_absensi='ABS-000046' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000047' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000048' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000049' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000050' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000051' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000052' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000053' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000054' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000055' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limalima,
                           sum(case when tm_jadwal.id_absensi='ABS-000000' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as nol
                            FROM tm_pegawai
                            INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                            LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai=tm_pegawai.nip
                            where tm_pegawai.status='AKTIF' and tm_pegawai.tgl_masuk <= '$tmtcek' and tm_pegawai.id_unit LIKE '%$id_unit%' GROUP BY tm_pegawai.id_user order by tm_unit.nama_unit desc ");
                            while ($row = fetch_array($sql_pegawai)) {
                                $no++;
                                $nip = $row['nip'];

                                $menitkerja = (($row['satu']*450) + ($row['dua']*450) + ($row['tiga']*450) + ($row['empat']*480) + ($row['lima']*360) + ($row['enam']*360) +($row['tujuh']*390) + ($row['delapan']*600) + ($row['sembilan']*390) + ($row['sepuluh']*420) + ($row['sebelas']*720) + ($row['duabelas']*720) + ($row['tigabelas']*720) + ($row['empatbelas']*630) + ($row['limabelas']*840) + ($row['enambelas']*390) + ($row['tujuhbelas']*360) + ($row['delapanbelas']*360) + ($row['sembilanbelas']*420) + ($row['duapuluh']*390) + ($row['duasatu']*420) + ($row['duadua']*1020) + ($row['duatiga']*1020) + ($row['duaempat']*660) + ($row['dualima']*660) + ($row['duaenam']*660) + ($row['duadelapan']*270) + ($row['duasembilan']*0) + ($row['tigatiga']*450) + ($row['tigaempat']*300) + ($row['tigalima']*420) + $row['tigaenam']*420 + $row['tigatujuh']*450 + $row['tigadelapan']*420 + $row['tigasembilan']*420 + $row['empatpuluh']*420 + $row['empatsatu']*420 + $row['empatdua']*630 + $row['empattiga']*390 + $row['empatempat']*420 + $row['empatlima']*450 + $row['empatenam']*420+ $row['empattujuh']*420+ $row['empatdelapan']*420+ $row['empatsembilan']*330+ $row['limapuluh']*800+ $row['limasatu']*800+ $row['limadua']*420+ $row['limatiga']*420+ $row['limaempat']*420+ $row['limalima']*350);  

                                 $harikerja = $row['satu'] + $row['dua'] + $row['tiga'] + $row['empat'] + $row['lima'] + $row['enam'] + $row['tujuh'] + ($row['delapan']*2) + $row['sembilan'] + $row['sepuluh'] + ($row['sebelas']*2) + ($row['duabelas']*2) + ($row['tigabelas']*2) + ($row['empatbelas']*2) + ($row['limabelas']*2) + ($row['enambelas']*2) + $row['tujuhbelas'] + $row['delapanbelas'] + $row['sembilanbelas'] + $row['duapuluh'] + $row['duasatu'] + ($row['duadua']*2) + ($row['duatiga']*2) + ($row['duaempat']) + ($row['dualima']) + ($row['duaenam']) + $row['duadelapan'] + $row['duasembilan'] + $row['tigatiga'] + $row['tigaempat'] + $row['tigalima'] + $row['tigaenam'] + $row['tigatujuh'] + $row['tigadelapan'] + $row['tigasembilan'] + $row['empatpuluh'] + $row['empatsatu'] + ($row['empatdua']*2) + $row['empattiga'] + $row['empatempat'] + $row['empatlima'] + $row['empatenam'] + $row['empattujuh'] + $row['empatdelapan'] + $row['empatsembilan'] + ($row['limapuluh']*2)+ ($row['limasatu']*2) + $row['limadua']+ $row['limatiga']+ $row['limaempat']+ $row['limalima'];
        
                                $kerjashift = fetch_array(bukaquery("SELECT id_user, nip_pegawai,
                        IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS kerjapagi,
                        IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS kerjasore,
                        IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS kerjamalam 
                        FROM tm_jadwal 
                        INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                        WHERE nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));
        
                                $liburshift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                        IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS liburpagi, 
                        IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS libursore, 
                        IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS liburmalam 
                        FROM tm_jadwal 
                        INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                        INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal                         
                        where tm_hari_libur.jenis IN('Libur Kerja','Libur Nasional','Cuti Bersama')and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));
        
                                $rayashift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                        IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS rayapagi, 
                        IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS rayasore, 
                        IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS rayamalam 
                        FROM tm_jadwal 
                        INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                        INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal 
                        where tm_hari_libur.jenis ='Libur Hari Raya' and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));      
                            ?>
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $row['nama_unit']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <?php
                                    for ($a = 1; $a <= FormatTgl('d', $from); $a++) {
                                        $tmtcek_ = $tahun_post . "-" . $bulan_post . "-" . $a;
                                        $mapping = fetch_array(bukaquery("SELECT
                                                tm_shift.nama_shift,
                                                tm_shift.color,
                                                case 
                                                when tm_shift.color='A' then 'red-gradient'
                                                when tm_shift.color='B' then 'aqua-gradient'
                                                when tm_shift.color='C' then 'blue'
                                                when tm_shift.color='D' then 'red'
                                                when tm_shift.color='E' then 'green' 
                                                when tm_shift.color='F' then 'pink' 
                                                when tm_shift.color='G' then 'purple' 
                                                when tm_shift.color='H' then 'orange'
                                                when tm_shift.color='I' then 'Navy'
                                                when tm_shift.color='J' then 'teal'
                                                when tm_shift.color='K' then 'gray'
                                                when tm_shift.color='L' then 'purple-gradient'
                                                when tm_shift.color='M' then 'blue-gradient'
                                                when tm_shift.color='N' then 'black'
                                                else 'white' end as color
                                                from tm_jadwal
                                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                                where tm_jadwal.nip_pegawai='$row[nip]' and tanggal_jadwal='$tmtcek_'"));
                                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                                        if (date('d') <= 5) {
                                            echo "<td>" . ("") . "<a href='?" . paramEncrypt('module=jadwal-pegawai&act=add-rekap-jadwal-pegawai&id=' . $tmtcek_ . '&dpa=' . $row['id_user'] . '') . "'><button type='submit' class='btn-xs bg-" . $mapping['color'] . "' >"
                                                . $mapping['nama_shift'] . "</button></a></td>";
                                        } else {
                                            echo "<td>" . ("") . "<a href='#'><button type='submit' class='btn-xs bg-" . $mapping['color'] . "' >"
                                                . $mapping['nama_shift'] . "</button></a></td>";
                                        }
                                    }
                                    ?>
                                    <!-- Pagi -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjapagi'] - $liburshift['liburpagi'] - $rayashift['rayapagi']; ?></td>
                                    <!-- Siang -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjasore'] - $liburshift['libursore'] - $rayashift['rayasore']; ?></td>
                                     <!-- Malam -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjamalam'] - $liburshift['liburmalam'] - $rayashift['rayamalam']; ?></td>
                                    
                                    <!-- Pagi(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburpagi']; ?></td>
                                    <!-- Siang(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['libursore']; ?></td>
                                    <!-- Malam(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburmalam']; ?></td>
                                    <!-- Pagi(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayapagi']; ?></td>
                                    <!-- Siang(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayasore']; ?></td>
                                    <!-- Malam(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayamalam']; ?></td>
                                    <!-- Vol. Hari Kerja -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $harikerja; ?></td>
                                    <!-- Off(L) -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $row['nol'] + $row['tigapuluh'] + $row['tigasatu'] + $row['tigadua']; ?></td>
                                    <!-- Vol. Menit -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo $menitkerja; ?></td>
                                    <!-- Vol. Jam -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo $menitkerja / 60; ?></td>
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

    case "validasi-data-jadwal-pegawai":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> VALIDASI JADWAL KEPEGAWAIAN</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <br><br>
                <form action="" method="post">
                    <div class="form-group">
                        <table align="">
                            <tr>
                                <td>
                                    <label>Unit</label>
                                </td>
                                <td>
                                    <label>Bulan</label>
                                </td>
                                <td>
                                    <label>Tahun</label>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control select2" name="id_unit" data-placeholder="-Pilih Unit-" style="width: 200%;" required>
                                        <option selected="selected" value="">-  Pilih Unit  -</option>
                                        <?php
                                        $tm_shift = bukaquery("select tm_unit.id_unit, tm_unit.nama_unit from tm_unit ");
                                        while ($row = fetch_array($tm_shift)) {
                                            echo "<option value=" . $row['id_unit'] . ">" . $row['nama_unit'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" required>
                                        <?php loadBln('-Pilih Bulan-'); ?>
                                    </select></td>
                                <td>
                                    <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                                        <?php loadThn('-Pilih Tahun-'); ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                </td>
                                <br>
                    <br>
                    <td>
                        <label>Note :</label>
                    </td>
                    <br>
                    <td>
                        <label>OFF = Libur</label>
                        <br>

                        <label>Izin/Sakit/Cuti/Isoman mohon di input ke jadwal</label>
                    </td>
                                
                            </tr>
                        </table>
                    </div>
                </form>
                <?php
                $id_unit_post = isset($_POST['id_unit']) ? $_POST['id_unit'] : null;
                $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : null;
                $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : null;
                $awal = FormatTgl("Y-m-d", $tahun_post . "-" . $bulan_post . "-01");
                $akhir = FormatTgl("Y-m-t", $tahun_post . "-" . $bulan_post . "-01");
                $tmtcek = FormatTgl("Y-m-d", $tahun_post . "-" . $bulan_post . "-01");
                $from = FormatTgl('00-m-Y', $tmtcek);
                $to = FormatTgl('t-m-Y', $tmtcek);

                if ($id_unit_post == '' or $bulan_post == 0 or $tahun_post == 0) {
                    $query = "SELECT
                    tm_pegawai.id_user,
                    tm_pegawai.nip,
                    tm_pegawai.nama_pegawai,
                    tm_unit.id_unit,
                    tm_unit.nama_unit,
                    sum(case when tm_jadwal.id_absensi='ABS-000001' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as satu,
                           sum(case when tm_jadwal.id_absensi='ABS-000002' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as dua,
                           sum(case when tm_jadwal.id_absensi='ABS-000003' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000004' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empat,
                           sum(case when tm_jadwal.id_absensi='ABS-000005' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as lima,
                           sum(case when tm_jadwal.id_absensi='ABS-000006' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as enam,
                           sum(case when tm_jadwal.id_absensi='ABS-000007' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000008' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as delapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000009' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000010' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sepuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000011' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sebelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000012' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000013' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000014' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000015' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000016' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as enambelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000017' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tujuhbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000018' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as delapanbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000019' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sembilanbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000020' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000021' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000022' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000023' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000024' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000025' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as dualima,
                           sum(case when tm_jadwal.id_absensi='ABS-000026' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duaenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000027' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duatujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000028' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duadelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000029' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duasembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000030' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000031' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000032' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000033' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000034' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000035' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigalima,
                           sum(case when tm_jadwal.id_absensi='ABS-000036' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigaenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000037' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigatujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000038' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigadelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000039' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigasembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000040' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatpuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000041' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000042' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdua,
                           sum(case when tm_jadwal.id_absensi='ABS-000043' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000044' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000045' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatlima,
                           sum(case when tm_jadwal.id_absensi='ABS-000041' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000042' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdua,
                           sum(case when tm_jadwal.id_absensi='ABS-000043' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000044' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000045' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatlima,
                           sum(case when tm_jadwal.id_absensi='ABS-000046' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000047' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000048' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000049' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000050' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000051' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000052' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000053' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000054' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000055' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limalima,
                           sum(case when tm_jadwal.id_absensi='ABS-000000' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as nol
                    FROM tm_pegawai
                    INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                    LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai=tm_pegawai.nip
                    where tm_pegawai.status='AKTIF' and tm_pegawai.tgl_masuk <= '$tmtcek' and tm_pegawai.id_unit LIKE '%$id_unit%' GROUP BY tm_pegawai.id_user order by tm_unit.nama_unit desc ";
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> <label>Unit, Bulan dan Tahun Tidak Sesuai</label></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                <?php } else {
                    $query = "SELECT
                    tm_pegawai.id_user,
                    tm_pegawai.nip,
                    tm_pegawai.nama_pegawai,
                    tm_unit.id_unit,
                    tm_unit.nama_unit,
                    sum(case when tm_jadwal.id_absensi='ABS-000001' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as satu,
                           sum(case when tm_jadwal.id_absensi='ABS-000002' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as dua,
                           sum(case when tm_jadwal.id_absensi='ABS-000003' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000004' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empat,
                           sum(case when tm_jadwal.id_absensi='ABS-000005' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as lima,
                           sum(case when tm_jadwal.id_absensi='ABS-000006' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as enam,
                           sum(case when tm_jadwal.id_absensi='ABS-000007' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000008' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as delapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000009' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000010' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sepuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000011' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sebelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000012' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000013' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000014' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000015' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000016' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as enambelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000017' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tujuhbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000018' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as delapanbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000019' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sembilanbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000020' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000021' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000022' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000023' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000024' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000025' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as dualima,
                           sum(case when tm_jadwal.id_absensi='ABS-000026' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duaenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000027' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duatujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000028' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duadelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000029' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duasembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000030' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000031' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000032' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000033' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000034' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000035' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigalima,
                           sum(case when tm_jadwal.id_absensi='ABS-000036' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigaenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000037' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigatujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000038' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigadelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000039' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigasembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000040' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatpuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000041' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000042' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdua,
                           sum(case when tm_jadwal.id_absensi='ABS-000043' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000044' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000045' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatlima,
                           sum(case when tm_jadwal.id_absensi='ABS-000041' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000042' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdua,
                           sum(case when tm_jadwal.id_absensi='ABS-000043' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000044' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000045' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatlima,
                           sum(case when tm_jadwal.id_absensi='ABS-000046' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000047' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000048' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000049' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000050' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000051' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000052' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000053' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000054' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000055' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limalima,
                           sum(case when tm_jadwal.id_absensi='ABS-000000' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as nol
                    FROM tm_pegawai
                    INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                    LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai=tm_pegawai.nip
                    where tm_pegawai.status='AKTIF' and tm_pegawai.tgl_masuk <= '$tmtcek' and tm_pegawai.id_unit LIKE '%$id_unit_post%' GROUP BY tm_pegawai.id_user order by tm_unit.nama_unit desc ";
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list center-block">
                            <label align="center">Rekapitulasi Jadwal <?php echo konversiBulanTahun($tmtcek); ?></label>
                        </h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                <?php }
                ?>
                <div class="box-body table-responsive">
                    <table id="jadwal" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2">No <?php echo $bulan_setup; ?></th>
                                <th rowspan="2">Unit</th>
                                <th rowspan="2">Nama Pegawai</th>
                                <?php while (strtotime($from) < strtotime($to)) {
                                    $from = mktime(0, 0, 0, date("m", strtotime($from)), date("d", strtotime($from)) + 1, date("Y", strtotime($from)));
                                    $from = date("Y-m-d ", $from);
                                    $tanggalan = konversitanggal($from);
                                    $libur = fetch_array(bukaquery("select tm_hari_libur.jenis, tm_hari_libur.keterangan from tm_hari_libur where tanggal='$from'"));
                                    if ($libur['jenis'] != '') {
                                        echo "<th rowspan='2' style='background-color: red' title='" . $libur['jenis']  . " (" . $libur['keterangan']  . ") '>" . $tanggalan .  "(" . hariindo($from) . ")</th>";
                                    } else {
                                        echo "<th rowspan='2'>" .  $tanggalan .  "(" . hariindo($from) . ")</th>";
                                    }
                                }
                                ?>
                                <th colspan="3" style="text-align:center; background-color:lightblue;">Hari Kerja</th>
                                <th colspan="3" style="text-align:center; background-color:yellow;">Hari Minggu/Libur Nasional</th>
                                <th colspan="3" style="text-align:center; background-color:pink;">Hari Libur Raya</th>
                                <th colspan="2" style="text-align:center; background-color:deeppink;">Vol. Total</th>
                                <th rowspan="2" style="text-align:center; background-color:deepskyblue;">Total Menit Kerja</th>
                                <th rowspan="2" style="text-align:center; background-color:deepskyblue;">Total Jam Kerja</th>
                            </tr>
                            <tr>
                                <th style="text-align:center; background-color:lightblue;">P</th>
                                <th style="text-align:center; background-color:lightblue;">S</th>
                                <th style="text-align:center; background-color:lightblue;">M</th>
                                
                                <th style="text-align:center; background-color:yellow;">P</th>
                                <th style="text-align:center; background-color:yellow;">S</th>
                                <th style="text-align:center; background-color:yellow;">M</th>

                                <th style="text-align:center; background-color:pink;">P</th>
                                <th style="text-align:center; background-color:pink;">S</th>
                                <th style="text-align:center; background-color:pink;">M</th>

                                <th style="text-align:center; background-color:deeppink;">Hari kerja</th>
                                <th style="text-align:center; background-color:deeppink;">Off</th>
                                <!-- <th style="text-align:center; background-color:deeppink;">I/S</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $id_unit_post = isset($_POST['id_unit']) ? $_POST['id_unit'] : null;
                            $awal = FormatTgl("Y-m-d", $tahun_post . "-" . $bulan_post . "-01");
                            $akhir = FormatTgl("Y-m-t", $tahun_post . "-" . $bulan_post . "-01");
                            $tmtcek = TanggalAkhirBulanKemarin();
                            $sql_pegawai = bukaquery("$query");
                            while ($row = fetch_array($sql_pegawai)) {
                                $no++;
                                $nip = $row['nip'];

                                $menitkerja = (($row['satu']*450) + ($row['dua']*450) + ($row['tiga']*450) + ($row['empat']*480) + ($row['lima']*360) + ($row['enam']*360) +($row['tujuh']*390) + ($row['delapan']*600) + ($row['sembilan']*390) + ($row['sepuluh']*420) + ($row['sebelas']*720) + ($row['duabelas']*720) + ($row['tigabelas']*720) + ($row['empatbelas']*630) + ($row['limabelas']*840) + ($row['enambelas']*390) + ($row['tujuhbelas']*360) + ($row['delapanbelas']*360) + ($row['sembilanbelas']*420) + ($row['duapuluh']*390) + ($row['duasatu']*420) + ($row['duadua']*1020) + ($row['duatiga']*1020) + ($row['duaempat']*660) + ($row['dualima']*660) + ($row['duaenam']*660) + ($row['duadelapan']*270) + ($row['duasembilan']*0) + ($row['tigatiga']*450) + ($row['tigaempat']*300) + ($row['tigalima']*420) + $row['tigaenam']*420 + $row['tigatujuh']*450 + $row['tigadelapan']*420 + $row['tigasembilan']*420 + $row['empatpuluh']*420 + $row['empatsatu']*420 + $row['empatdua']*630 + $row['empattiga']*390 + $row['empatempat']*420 + $row['empatlima']*450 + $row['empatenam']*420+ $row['empattujuh']*420+ $row['empatdelapan']*420+ $row['empatsembilan']*330+ $row['limapuluh']*800+ $row['limasatu']*800+ $row['limadua']*420+ $row['limatiga']*420+ $row['limaempat']*420+ $row['limalima']*350);  

                                 $harikerja = $row['satu'] + $row['dua'] + $row['tiga'] + $row['empat'] + $row['lima'] + $row['enam'] + $row['tujuh'] + ($row['delapan']*2) + $row['sembilan'] + $row['sepuluh'] + ($row['sebelas']*2) + ($row['duabelas']*2) + ($row['tigabelas']*2) + ($row['empatbelas']*2) + ($row['limabelas']*2) + ($row['enambelas']*2) + $row['tujuhbelas'] + $row['delapanbelas'] + $row['sembilanbelas'] + $row['duapuluh'] + $row['duasatu'] + ($row['duadua']*2) + ($row['duatiga']*2) + ($row['duaempat']) + ($row['dualima']) + ($row['duaenam']) + $row['duadelapan'] + $row['duasembilan'] + $row['tigatiga'] + $row['tigaempat'] + $row['tigalima'] + $row['tigaenam'] + $row['tigatujuh'] + $row['tigadelapan'] + $row['tigasembilan'] + $row['empatpuluh'] + $row['empatsatu'] + ($row['empatdua']*2) + $row['empattiga'] + $row['empatempat'] + $row['empatlima'] + $row['empatenam'] + $row['empattujuh'] + $row['empatdelapan'] + $row['empatsembilan'] + ($row['limapuluh']*2)+ ($row['limasatu']*2) + $row['limadua']+ $row['limatiga']+ $row['limaempat']+ $row['limalima'];
        
                                $kerjashift = fetch_array(bukaquery("SELECT id_user, nip_pegawai,
                        IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS kerjapagi,
                        IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS kerjasore,
                        IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS kerjamalam 
                        FROM tm_jadwal 
                        INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                        WHERE nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));
        
                                $liburshift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                        IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS liburpagi, 
                        IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS libursore, 
                        IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS liburmalam 
                        FROM tm_jadwal 
                        INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                        INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal                         
                        where tm_hari_libur.jenis IN('Libur Kerja','Libur Nasional','Cuti Bersama')and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));
        
                                $rayashift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                        IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS rayapagi, 
                        IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS rayasore, 
                        IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS rayamalam 
                        FROM tm_jadwal 
                        INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                        INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal 
                        where tm_hari_libur.jenis ='Libur Hari Raya' and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));         
                            ?>
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $row['nama_unit']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <?php
                                    for ($a = 1; $a <= FormatTgl('d', $from); $a++) {
                                        $tmtcek_ = $tahun_post . "-" . $bulan_post . "-" . $a;
                                        $mapping = fetch_array(bukaquery("SELECT
                                                tm_shift.nama_shift,
                                                tm_shift.color,
                                                case 
                                                when tm_shift.color='A' then 'red-gradient'
                                                when tm_shift.color='B' then 'aqua-gradient'
                                                when tm_shift.color='C' then 'blue'
                                                when tm_shift.color='D' then 'red'
                                                when tm_shift.color='E' then 'green' 
                                                when tm_shift.color='F' then 'pink' 
                                                when tm_shift.color='G' then 'purple' 
                                                when tm_shift.color='H' then 'orange'
                                                when tm_shift.color='I' then 'Navy'
                                                when tm_shift.color='J' then 'teal'
                                                when tm_shift.color='K' then 'gray'
                                                when tm_shift.color='L' then 'purple-gradient'
                                                when tm_shift.color='M' then 'blue-gradient'
                                                when tm_shift.color='N' then 'black'
                                                else 'white' end as color 
                                                from tm_jadwal
                                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                                where tm_jadwal.nip_pegawai='$row[nip]' and tanggal_jadwal='$tmtcek_'"));
                                         
                                        echo "<td>" . ("") . "<a href='?" . paramEncrypt('module=jadwal-pegawai&act=add-validasi-jadwal&id=' . $tmtcek_ . '&dpa=' . $row['id_user'] . '') . "'><button type='submit' class='btn-xs bg-" . $mapping['color'] . "' >"
                                            . $mapping['nama_shift'] . " </button></a></td>";
                                        
                                        // echo "<td>".$mapping['nama_shift'].$mapping['color']."</td>"; 
                                    }
                                    ?>
                                    <!-- Pagi -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjapagi'] - $liburshift['liburpagi'] - $rayashift['rayapagi']; ?></td>
                                    <!-- Siang -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjasore'] - $liburshift['libursore'] - $rayashift['rayasore']; ?></td>
                                     <!-- Malam -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjamalam'] - $liburshift['liburmalam'] - $rayashift['rayamalam']; ?></td>
                                    
                                    <!-- Pagi(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburpagi']; ?></td>
                                    <!-- Siang(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['libursore']; ?></td>
                                    <!-- Malam(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburmalam']; ?></td>
                                    <!-- Pagi(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayapagi']; ?></td>
                                    <!-- Siang(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayasore']; ?></td>
                                    <!-- Malam(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayamalam']; ?></td>
                                    <!-- Vol. Hari Kerja -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $harikerja; ?></td>
                                    <!-- Off(L) -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $row['nol'] + $row['tigapuluh'] + $row['tigasatu'] + $row['tigadua']; ?></td>
                                    <!-- Vol. Menit -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo $menitkerja; ?></td>
                                    <!-- Vol. Jam -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo ($menitkerja) / 60; ?></td>
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

    case "update-jadwal-pegawai":
        $id = isset($url['id']) ? $url['id'] : null;
        $tm_jadwal = fetch_array(bukaquery("SELECT
        tm_jadwal.id_jadwal,
        tm_jadwal.tanggal_jadwal,
        tm_jadwal.id_user,
        tm_jadwal.id_unit,
        tm_jadwal.id_absensi,
        tm_jadwal.nip_pegawai,
        tm_jadwal.acc_pj,
        tm_jadwal.acc_kepegawaian,
        tm_jadwal.acc_kasie,
        tm_jadwal.date,
        tm_pegawai.nama_pegawai,
        tm_unit.nama_unit,
        tm_shift.nama_shift
        FROM
        tm_jadwal
        INNER JOIN tm_pegawai ON tm_jadwal.id_user = tm_pegawai.id_user
        INNER JOIN tm_unit ON tm_unit.id_unit=tm_jadwal.id_unit
        INNER JOIN tm_shift ON tm_shift.id_absensi=tm_jadwal.id_absensi
        where tm_jadwal.id_jadwal='$id'"));
        $tanggal = date('m/d/Y', strtotime($tm_jadwal['tanggal_jadwal']));
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-edit"> UPDATE DATA JADWAL PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <form action="<?php echo $aksi . paramEncrypt('module=jadwal-pegawai&act=update-kinerja-utama&id=' . $tm_jadwal['id_jadwal'] . ''); ?>" method="POST" role="form">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="text" class="form-control" value="<?php echo FormatTgl('m/d/Y', $tm_jadwal['tanggal_jadwal']); ?>" name="tanggal_jadwal" readonly="" required="">
                        <label>Nama Pegawai</label>
                        <input type="text" class="form-control" value="<?php echo ($tm_jadwal['nip_pegawai']); ?>" name="nip_pegawai" readonly="" required="">
                        <label>Pilih Shift</label>
                        <select class="form-control select2" name="id_absensi" data-placeholder="-Pilih Shift-" style="width: 100%;" required>
                            <option selected="selected" value="">-Pilih Shift-</option>
                            <?php
                            $tm_shift = bukaquery("select tm_shift.id_absensi, tm_shift.nama_shift from tm_shift  where tm_shift.status ='1'");
                            while ($row = fetch_array($tm_shift)) {
                                echo "<option value=" . $row['id_absensi'] . ">" . $row['nama_shift'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="box-footer">
                        <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai') ?>"><button type="button" class="btn btn-warning fa fa-undo"> Kembali</button></a>
                        <button type="submit" class="btn btn-primary fa fa-save"> Update</button>
                    </div>
                </form>
            </div>
        </div>
<?php
        break;

case "jadwal-unit":
        $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
        $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : date('Y')
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST JADWAL UNIT - BULAN <?php echo strtoupper(konversiBulan($bulan_post)) ?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=rekap-data-jadwal-pegawai'); ?>">
                    <button type="button" class="btn btn-instagram fa fa-search" title="cek rekapitulasi jadwal">
                        Rekapitulasi Jadwal
                    </button>
                </a>
                <tr>
                    <br>
                    <br>
                    <td>
                        <label>Note :</label>
                    </td>
                    <br>
                    <td>
                        <label>OFF = Libur</label>
                        <br>

                        <label>Izin/Sakit/Cuti/Isoman mohon di input ke jadwal</label>
                    </td>
                </tr>

            
                <?Php
                if (date('d') > 31) { 
                    $from = TanggalAwalBulanDepan_(); #bulandepan
                    $to = TanggalAkhirBulanDepan(); #bulandepan
                } else {
                    $from = TanggalAwalBulan();
                    $to = date('Y-m-t');
                }

                ?>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="jadwal" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <!-- <th rowspan="2">Unit</th> -->
                                <th rowspan="2">Nama</th>
                                <?php
                                $nomor = 0;
                                while (strtotime($from) < strtotime($to)) {
                                    $nilai[] = $nomor++;
                                    $from = mktime(0, 0, 0, date("m", strtotime($from)), date("d", strtotime($from)) + 1, date("Y", strtotime($from)));
                                    $from = date("Y-m-d ", $from);
                                    $tanggalan = konversiTanggalDoang($from);
                                    $libur = fetch_array(bukaquery("select tm_hari_libur.jenis, tm_hari_libur.keterangan from tm_hari_libur where tanggal='$from'"));
                                    if ($libur['jenis'] != '') {
                                        echo "<th rowspan='2' style='background-color: red' title='" . $libur['jenis']  . " (" . $libur['keterangan']  . ") '>" . $tanggalan .  "(" . hariindo1($from) . ")</th>";
                                    } else {
                                        echo "<th rowspan='2'>" .  $tanggalan .  "(" . hariindo1($from) . ")</th>";
                                    }
                                }
                                ?>

                                <th style="text-align:center; background-color:lightblue;">HKP</th>
                                <th style="text-align:center; background-color:lightblue;">HKS</th>
                                <th style="text-align:center; background-color:lightblue;">HKM</th>
                                
                                <th style="text-align:center; background-color:yellow;">HLP</th>
                                <th style="text-align:center; background-color:yellow;">HLS</th>
                                <th style="text-align:center; background-color:yellow;">HLM</th>

                                <th style="text-align:center; background-color:pink;">HRP</th>
                                <th style="text-align:center; background-color:pink;">HRS</th>
                                <th style="text-align:center; background-color:pink;">HRM</th>

                                <th style="text-align:center; background-color:deeppink;">Total Hari</th>
                                <th style="text-align:center; background-color:deeppink;">Total Off</th>
                                <th style="text-align:center; background-color:deepskyblue;">Total Menit</th>
                                <th style="text-align:center; background-color:deepskyblue;">Total Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $no = 0;
                            if (date('d') > 31) { 
                                $awal = FormatTgl('Y-m-d', TanggalAwalBulanDepan_());
                                $akhir = FormatTgl('Y-m-d', TanggalAkhirBulanDepan());
                                $tmtcek = TanggalAkhirBulanKemarin();
                                $bd = BulanDepanYM();
                            } else {
                                $awal = FormatTgl('Y-m-d', TanggalAwalBulan_());
                                $akhir = FormatTgl('Y-m-d', TanggalAkhirBulan());
                                $tmtcek = TanggalAkhirBulan();
                                $bd = date('m');
                            }
                            $sql_pegawai = bukaquery("SELECT
                                tm_pegawai.id_user,
                                tm_pegawai.nip,
                                tm_pegawai.nama_pegawai,
                                tm_unit.id_unit,
                                tm_unit.nama_unit,
                                sum(case when kode_shift='OFF' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as OFF,
                                sum(case when kode_shift='NS' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as NS,
                                sum(case when kode_shift='P' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as P,
                                sum(case when kode_shift='S' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as S,
                                sum(case when kode_shift='M' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as M,
                                sum(case when kode_shift='PS' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as PS,
                                sum(case when kode_shift='SM' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as SM,
                                sum(case when kode_shift='C' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as Cuti,
                                sum(case when kode_shift='I' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as Izin,
                                sum(case when kode_shift='Skt' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as Sakit,
                                sum(case when kode_shift='DL' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'  then 1 else 0 end ) as DL,
                                sum(case when kode_shift not in ('OFF','C','I','Skt') AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' then 1 else 0 end ) as hari_kerja
                            FROM
                                tm_pegawai
                                INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai = tm_pegawai.nip 
                                LEFT JOIN tm_shift ON tm_shift.id_absensi = tm_jadwal.id_absensi
                            WHERE
                                tm_pegawai.STATUS = 'AKTIF' 
                                AND tm_pegawai.id_unit LIKE '%$id_unit%'
                                -- AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
                            GROUP BY
                                tm_pegawai.id_user 
                            ORDER BY
                                tm_pegawai.nama_pegawai ASC");
                            while ($row = fetch_array($sql_pegawai)) {
                                $no++;
                                $nip = $row['nip'];
                                $menitkerja = fetch_array(bukaquery("SELECT
                                SUM(jumlah_menit) as menit_kerja
                            FROM
                                tm_pegawai
                                INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai = tm_pegawai.nip 
                                LEFT JOIN tm_shift ON tm_shift.id_absensi = tm_jadwal.id_absensi
                            WHERE
                                tm_pegawai.STATUS = 'AKTIF' 
                                AND tm_pegawai.nip=$nip
                                AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
                            ORDER BY
                                tm_pegawai.nama_pegawai ASC"));
                                $harikerja = $row['hari_kerja'];  
                                $kerjashift = fetch_array(bukaquery("SELECT id_user, nip_pegawai,
                                IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS kerjapagi,
                                IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS kerjasore,
                                IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS kerjamalam 
                                FROM tm_jadwal 
                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                WHERE nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));
                
                                $liburshift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                                IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS liburpagi, 
                                IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS libursore, 
                                IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS liburmalam 
                                FROM tm_jadwal 
                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal                         
                                where tm_hari_libur.jenis IN('Libur Kerja','Libur Nasional','Cuti Bersama')and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));
                
                                $rayashift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                                IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS rayapagi, 
                                IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS rayasore, 
                                IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS rayamalam 
                                FROM tm_jadwal 
                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal 
                                where tm_hari_libur.jenis ='Libur Hari Raya' and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));         
                            ?>
                                <tr>
                                    <td><?php echo $no ?></td>
                                    <!-- <td><?php echo $row['nama_unit']; ?></td> -->
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <?php
                                    for ($a = 1; $a <= FormatTgl('d', $akhir); $a++) {
                                        $tmtcek_ =  date('Y') . "-" . $bd . "-" . $a;
                                        $mapping = fetch_array(bukaquery("SELECT
                                                tm_shift.nama_shift,
                                                tm_shift.color,
                                                tm_shift.kode_shift,
                                                case 
                                                when tm_shift.color='A' then 'red-gradient'
                                                when tm_shift.color='B' then 'aqua-gradient'
                                                when tm_shift.color='C' then 'blue'
                                                when tm_shift.color='D' then 'red'
                                                when tm_shift.color='E' then 'green' 
                                                when tm_shift.color='F' then 'pink' 
                                                when tm_shift.color='G' then 'purple' 
                                                when tm_shift.color='H' then 'orange'
                                                when tm_shift.color='I' then 'Navy'
                                                when tm_shift.color='J' then 'teal'
                                                when tm_shift.color='K' then 'gray'
                                                when tm_shift.color='L' then 'purple-gradient'
                                                when tm_shift.color='M' then 'blue-gradient'
                                                when tm_shift.color='N' then 'black'
                                                else 'white' end as color 
                                                from tm_jadwal
                                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                                where tm_jadwal.nip_pegawai='$row[nip]' and tanggal_jadwal='$tmtcek_'"));
                                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                                        echo "<td title='" . $mapping['nama_shift']  . "'>" . ("") . "<button class='btn-xs bg-" . $mapping['color'] . "' >"
                                            . $mapping['kode_shift'] . " </button></td>";
                                    }
                                    ?> 
                                    <!-- Pagi -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjapagi'] - $liburshift['liburpagi'] - $rayashift['rayapagi']; ?></td>
                                    <!-- Siang -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjasore'] - $liburshift['libursore'] - $rayashift['rayasore']; ?></td>
                                     <!-- Malam -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjamalam'] - $liburshift['liburmalam'] - $rayashift['rayamalam']; ?></td>
                                    
                                    <!-- Pagi(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburpagi']; ?></td>
                                    <!-- Siang(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['libursore']; ?></td>
                                    <!-- Malam(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburmalam']; ?></td>
                                    <!-- Pagi(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayapagi']; ?></td>
                                    <!-- Siang(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayasore']; ?></td>
                                    <!-- Malam(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayamalam']; ?></td>
                                    <!-- Vol. Hari Kerja -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $harikerja; ?></td>
                                    <!-- Off(L) -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $row['OFF'] + $row['Cuti'] + $row['Izin'] + $row['Sakit']; ?></td>
                                    <!-- Vol. Menit -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo $menitkerja['menit_kerja']; ?></td>
                                    <!-- Vol. Jam -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo $menitkerja['menit_kerja'] / 60; ?></td>
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

    //Bulan -2
        case "jadwal-pegawai-bulankemarin2":
        $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
        $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : date('Y')
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> VALIDASI JADWAL PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">

                <tr>
                    <td>
                        <label>Note :</label>
                    </td>
                    <br>
                    <td>
                        <label>OFF = Libur</label>
                        <br>

                        <label>Izin/Sakit/Cuti/Isoman mohon di input ke jadwal</label>
                    </td>
                    
                </tr>

                <?Php
                if (date('d') > 1)  { #dibuka tanggal brp
                    $from = TanggalAwalBulanKemarin_(); #tampil daftar hari
                    $to = TanggalAkhirBulanKemarin(); 
                } else {
                    $from = TanggalAwalBulanKemarin_();
                    $to = date('Y-m-t');
                }

                ?>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="jadwal" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Unit</th>
                                <th rowspan="2">Nama Pegawai</th>
                                <?php
                                $nomor = 0;
                                while (strtotime($from) < strtotime($to)) {
                                    $nilai[] = $nomor++;
                                    $from = mktime(0, 0, 0, date("m", strtotime($from)), date("d", strtotime($from)) + 1, date("Y", strtotime($from)));
                                    $from = date("Y-m-d ", $from);
                                    $tanggalan = konversitanggal($from);
                                    $libur = fetch_array(bukaquery("select tm_hari_libur.jenis, tm_hari_libur.keterangan from tm_hari_libur where tanggal='$from'"));
                                    if ($libur['jenis'] != '') {
                                        echo "<th rowspan='2' style='background-color: red' title='" . $libur['jenis']  . " (" . $libur['keterangan']  . ") '>" . $tanggalan .  "(" . hariindo($from) . ")</th>";
                                    } else {
                                        echo "<th rowspan='2'>" .  $tanggalan .  "(" . hariindo($from) . ")</th>";
                                    }
                                }
                                ?>
                                <th colspan="3" style="text-align:center; background-color:lightblue;">Hari Kerja</th>
                                <th colspan="3" style="text-align:center; background-color:yellow;">Hari Minggu/Libur Nasional</th>
                                <th colspan="3" style="text-align:center; background-color:pink;">Hari Libur Raya</th>
                                <th colspan="2" style="text-align:center; background-color:deeppink;">Vol. Total</th>
                                <th rowspan="2" style="text-align:center; background-color:deepskyblue;">Total Menit Kerja</th>
                                <th rowspan="2" style="text-align:center; background-color:deepskyblue;">Total Jam Kerja</th>
                            </tr>
                            <tr>
                                <th style="text-align:center; background-color:lightblue;">P</th>
                                <th style="text-align:center; background-color:lightblue;">S</th>
                                <th style="text-align:center; background-color:lightblue;">M</th>
                                
                                <th style="text-align:center; background-color:yellow;">P</th>
                                <th style="text-align:center; background-color:yellow;">S</th>
                                <th style="text-align:center; background-color:yellow;">M</th>

                                <th style="text-align:center; background-color:pink;">P</th>
                                <th style="text-align:center; background-color:pink;">S</th>
                                <th style="text-align:center; background-color:pink;">M</th>

                                <th style="text-align:center; background-color:deeppink;">Hari kerja</th>
                                <th style="text-align:center; background-color:deeppink;">Off</th>
                                <!-- <th style="text-align:center; background-color:deeppink;">I/S</th> -->

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            if (date('d') > 1) { #dibuka tanggal brp
                                $awal = FormatTgl('Y-m-d', TanggalAwalBulanKemarin__()); #tampil jadwal 
                                $akhir = FormatTgl('Y-m-d', TanggalAkhirBulanKemarin()); #tampil jadwal 
                                $tmtcek = TanggalAkhirBulanKemarin2();
                                $bd = BulanKemarinYM2(); #setup input bulan jadwal
                            } else {
                                $awal = FormatTgl('Y-m-d', TanggalAwalBulanKemarin__());
                                $akhir = FormatTgl('Y-m-d', TanggalAkhirBulanKemarin());
                                $tmtcek = TanggalAkhirBulanKemarin();
                                $bd = date('m');
                            }
                            $sql_pegawai = bukaquery("SELECT
                           tm_pegawai.id_user,
                           tm_pegawai.nip,
                           tm_pegawai.nama_pegawai,
                           tm_unit.id_unit,
                           tm_unit.nama_unit,
                           sum(case when tm_jadwal.id_absensi='ABS-000001' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as satu,
                           sum(case when tm_jadwal.id_absensi='ABS-000002' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as dua,
                           sum(case when tm_jadwal.id_absensi='ABS-000003' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000004' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empat,
                           sum(case when tm_jadwal.id_absensi='ABS-000005' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as lima,
                           sum(case when tm_jadwal.id_absensi='ABS-000006' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as enam,
                           sum(case when tm_jadwal.id_absensi='ABS-000007' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000008' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as delapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000009' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000010' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sepuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000011' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sebelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000012' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000013' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000014' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000015' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limabelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000016' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as enambelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000017' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tujuhbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000018' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as delapanbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000019' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as sembilanbelas,
                           sum(case when tm_jadwal.id_absensi='ABS-000020' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000021' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000022' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000023' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000024' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000025' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as dualima,
                           sum(case when tm_jadwal.id_absensi='ABS-000026' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duaenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000027' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duatujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000028' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duadelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000029' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as duasembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000030' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000031' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000032' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000033' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000034' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000035' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigalima,
                           sum(case when tm_jadwal.id_absensi='ABS-000036' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigaenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000037' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigatujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000038' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigadelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000039' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as tigasembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000040' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatpuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000041' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000042' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdua,
                           sum(case when tm_jadwal.id_absensi='ABS-000043' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000044' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000045' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatlima,
                           sum(case when tm_jadwal.id_absensi='ABS-000041' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000042' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdua,
                           sum(case when tm_jadwal.id_absensi='ABS-000043' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000044' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000045' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatlima,
                           sum(case when tm_jadwal.id_absensi='ABS-000046' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatenam,
                           sum(case when tm_jadwal.id_absensi='ABS-000047' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empattujuh,
                           sum(case when tm_jadwal.id_absensi='ABS-000048' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatdelapan,
                           sum(case when tm_jadwal.id_absensi='ABS-000049' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as empatsembilan,
                           sum(case when tm_jadwal.id_absensi='ABS-000050' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limapuluh,
                           sum(case when tm_jadwal.id_absensi='ABS-000051' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limasatu,
                           sum(case when tm_jadwal.id_absensi='ABS-000052' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limadua,
                           sum(case when tm_jadwal.id_absensi='ABS-000053' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limatiga,
                           sum(case when tm_jadwal.id_absensi='ABS-000054' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limaempat,
                           sum(case when tm_jadwal.id_absensi='ABS-000055' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as limalima,
                           sum(case when tm_jadwal.id_absensi='ABS-000000' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) as nol
                           FROM tm_pegawai
                           INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                           LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai=tm_pegawai.nip
                           where tm_pegawai.status='AKTIF' and tm_pegawai.id_unit LIKE '%$id_unit%' 
                           GROUP BY tm_pegawai.id_user
                           order by tm_unit.nama_unit desc");
                            while ($row = fetch_array($sql_pegawai)) {
                                $no++;
                                $nip = $row['nip'];

                                $menitkerja = (($row['satu']*450) + ($row['dua']*450) + ($row['tiga']*450) + ($row['empat']*480) + ($row['lima']*360) + ($row['enam']*360) +($row['tujuh']*390) + ($row['delapan']*600) + ($row['sembilan']*390) + ($row['sepuluh']*420) + ($row['sebelas']*720) + ($row['duabelas']*720) + ($row['tigabelas']*720) + ($row['empatbelas']*630) + ($row['limabelas']*840) + ($row['enambelas']*390) + ($row['tujuhbelas']*360) + ($row['delapanbelas']*360) + ($row['sembilanbelas']*420) + ($row['duapuluh']*390) + ($row['duasatu']*420) + ($row['duadua']*1020) + ($row['duatiga']*1020) + ($row['duaempat']*660) + ($row['dualima']*660) + ($row['duaenam']*660) + ($row['duadelapan']*270) + ($row['duasembilan']*0) + ($row['tigatiga']*450) + ($row['tigaempat']*300) + ($row['tigalima']*420) + $row['tigaenam']*420 + $row['tigatujuh']*450 + $row['tigadelapan']*420 + $row['tigasembilan']*420 + $row['empatpuluh']*420 + $row['empatsatu']*420 + $row['empatdua']*630 + $row['empattiga']*390 + $row['empatempat']*420 + $row['empatlima']*450 + $row['empatenam']*420+ $row['empattujuh']*420+ $row['empatdelapan']*420+ $row['empatsembilan']*330+ $row['limapuluh']*800+ $row['limasatu']*800+ $row['limadua']*420+ $row['limatiga']*420+ $row['limaempat']*420+ $row['limalima']*350);  

                                 $harikerja = $row['satu'] + $row['dua'] + $row['tiga'] + $row['empat'] + $row['lima'] + $row['enam'] + $row['tujuh'] + ($row['delapan']*2) + $row['sembilan'] + $row['sepuluh'] + ($row['sebelas']*2) + ($row['duabelas']*2) + ($row['tigabelas']*2) + ($row['empatbelas']*2) + ($row['limabelas']*2) + ($row['enambelas']*2) + $row['tujuhbelas'] + $row['delapanbelas'] + $row['sembilanbelas'] + $row['duapuluh'] + $row['duasatu'] + ($row['duadua']*2) + ($row['duatiga']*2) + ($row['duaempat']) + ($row['dualima']) + ($row['duaenam']) + $row['duadelapan'] + $row['duasembilan'] + $row['tigatiga'] + $row['tigaempat'] + $row['tigalima'] + $row['tigaenam'] + $row['tigatujuh'] + $row['tigadelapan'] + $row['tigasembilan'] + $row['empatpuluh'] + $row['empatsatu'] + ($row['empatdua']*2) + $row['empattiga'] + $row['empatempat'] + $row['empatlima'] + $row['empatenam'] + $row['empattujuh'] + $row['empatdelapan'] + $row['empatsembilan'] + ($row['limapuluh']*2)+ ($row['limasatu']*2) + $row['limadua']+ $row['limatiga']+ $row['limaempat']+ $row['limalima'];
        
                                $kerjashift = fetch_array(bukaquery("SELECT id_user, nip_pegawai,
                                    IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS kerjapagi,
                                    IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS kerjasore,
                                    IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS kerjamalam 
                                    FROM tm_jadwal 
                                    INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                    WHERE nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));
        
                                $liburshift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                                    IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS liburpagi, 
                                    IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS libursore, 
                                    IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS liburmalam 
                                    FROM tm_jadwal 
                                    INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                    INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal                         
                                    where tm_hari_libur.jenis IN('Libur Kerja','Libur Nasional','Cuti Bersama') 
                                    and nip_pegawai='$nip' and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));
        
                                $rayashift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                                    IFNULL(sum( kode_shift IN ('P','PS','NS','DL')),0 ) AS rayapagi, 
                                    IFNULL(sum( kode_shift IN ('S','PS','SM')),0 ) AS rayasore, 
                                    IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS rayamalam 
                                    FROM tm_jadwal 
                                    INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                    INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal 
                                    where tm_hari_libur.jenis ='Libur Hari Raya' and nip_pegawai='$nip' 
                                    and tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'"));    
                            ?>
                                <tr>
                                    <td><?php echo $no ?></td>
                                    <td><?php echo $row['nama_unit']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <?php

                                    for ($a = 1; $a <= FormatTgl('d', $akhir); $a++) {
                                        $tmtcek_ =  date('Y') -1 . "-" . $bd . "-" . $a;
                                        $mapping = fetch_array(bukaquery("SELECT
                                                tm_shift.nama_shift,
                                                tm_shift.color,
                                                case 
                                                when tm_shift.color='A' then 'red-gradient'
                                                when tm_shift.color='B' then 'aqua-gradient'
                                                when tm_shift.color='C' then 'blue'
                                                when tm_shift.color='D' then 'red'
                                                when tm_shift.color='E' then 'green' 
                                                when tm_shift.color='F' then 'pink' 
                                                when tm_shift.color='G' then 'purple' 
                                                when tm_shift.color='H' then 'orange'
                                                when tm_shift.color='I' then 'Navy'
                                                when tm_shift.color='J' then 'teal'
                                                when tm_shift.color='K' then 'gray'
                                                when tm_shift.color='L' then 'purple-gradient'
                                                when tm_shift.color='M' then 'blue-gradient'
                                                when tm_shift.color='N' then 'black'
                                                else 'white' end as color 
                                                from tm_jadwal
                                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                                where tm_jadwal.nip_pegawai='$row[nip]' and tanggal_jadwal='$tmtcek_'"));
                                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                                        echo "<td>" . ("") . "<a href='?" . paramEncrypt('module=jadwal-pegawai&act=add-validasi-pegawai&id=' . $tmtcek_ . '&dpa=' . $row['id_user'] . '') . "'><button type='submit' class='btn-xs bg-" . $mapping['color'] . "' >"
                                            . $mapping['nama_shift'] . " </button></a></td>";
                                    }
                                    ?>
                                    <!-- Pagi -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjapagi'] - $liburshift['liburpagi'] - $rayashift['rayapagi']; ?></td>
                                    <!-- Siang -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjasore'] - $liburshift['libursore'] - $rayashift['rayasore']; ?></td>
                                     <!-- Malam -->
                                    <td style="text-align:center; background-color:lightblue;"><?php echo $kerjashift ['kerjamalam'] - $liburshift['liburmalam'] - $rayashift['rayamalam']; ?></td>
                                    
                                    <!-- Pagi(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburpagi']; ?></td>
                                    <!-- Siang(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['libursore']; ?></td>
                                    <!-- Malam(L) -->
                                    <td style="text-align:center; background-color:yellow;"><?php echo $liburshift['liburmalam']; ?></td>
                                    <!-- Pagi(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayapagi']; ?></td>
                                    <!-- Siang(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayasore']; ?></td>
                                    <!-- Malam(R) -->
                                    <td style="text-align:center; background-color:pink;"><?php echo $rayashift['rayamalam']; ?></td>
                                    <!-- Vol. Hari Kerja -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $harikerja; ?></td>
                                    <!-- Off(L) -->
                                    <td style="text-align:center; background-color:deeppink;"><?php echo $row['nol'] + $row['tigapuluh'] + $row['tigasatu'] + $row['tigadua']; ?></td>
                                    <!-- Vol. Menit -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo $menitkerja; ?></td>
                                    <!-- Vol. Jam -->
                                    <td style="text-align:center; background-color:deepskyblue;"><?php echo ($menitkerja) / 60; ?></td>
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

    case "validasi-data-absensi":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> VALIDASI JADWAL</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <form action="" method="post">
                    <div class="form-group">
                        <table align="">
                            <tr>
                                <td>
                                    <label>Bulan</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" required>
                                        <?php loadBln('-Pilih Bulan-'); ?>
                                    </select></td>
                                <td>
                                    <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                </td>    
                            </tr>
                        </table>
                    </div>
                </form>
                <?php
                        $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
                        if ($bulan_post == '' or $id_unit_post = '') {
                            $bulan_post = $blnsekarang;
                        ?>
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> <label>Data absensi bulan <?php echo konversiBulan($blnsekarang); ?></label></h3>
                                <div class="box-tools pull-right">
                                </div>
                            </div>
                        <?php
                        } else {
                            $tgl_absensi = $cari;
                        ?>
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> <label align="center">Data absensi bulan <?php echo konversiBulan($bulan_post); ?></label></h3>
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
                                        <th>Nama Pegawai</th>
                                        <th>Nama Unit</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
                                    $sql_pegawai = bukaquery("SELECT tm_pegawai.id_user,
                                            tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_unit.id_unit , tm_unit.nama_unit                                
                                            FROM tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                                            where tm_pegawai.status_pegawai != 'PJLP' and tm_pegawai.status='AKTIF' order by tm_pegawai.nama_pegawai asc ");
                                    while ($row = fetch_array($sql_pegawai)) {
                                        $no++;
                                    ?>
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['nama_pegawai']; ?></td>
                                            <td><?php echo $row['nama_unit']; ?></td>
                                            <td>
                                                <a href="?<?php echo paramEncrypt("module=absensi-pegawai&act=cek-absensi-pegawai&id=" . $row['nip'] . "&dpa=" . $row['id_unit'] . ""); ?>"><span title="Absensi" class="btn-xs btn-info fa fa-eye">Absensi</span></a>
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

    case "validasi-data-absensi-spesialis":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> DATA ABSENSI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <form action="" method="post">
                    <div class="form-group">
                        <table align="">
                            <tr>
                                <td>
                                    <label>Bulan</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" required>
                                        <?php loadBln('-Pilih Bulan-'); ?>
                                    </select></td>
                                <td>
                                    <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                </td>    
                            </tr>
                        </table>
                    </div>
                </form>
                <?php
                        $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
                        if ($bulan_post == '' or $id_unit_post = '') {
                            $bulan_post = $blnsekarang;
                        ?>
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> <label>Data absensi bulan <?php echo konversiBulan($blnsekarang); ?></label></h3>
                                <div class="box-tools pull-right">
                                </div>
                            </div>
                        <?php
                        } else {
                            $tgl_absensi = $cari;
                        ?>
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> <label align="center">Data absensi bulan <?php echo konversiBulan($bulan_post); ?></label></h3>
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
                                        <th>Nama Pegawai</th>
                                        <th>Nama Unit</th>
                                        <th>Alpha/Hari</th>
                                        <th>Sakit 1-2/Hari</th>
                                        <th>Sakit >2/Hari</th>
                                        <th>Izin/Hari</th>
                                        <th>Cuti</th>
                                        <th>Telat/Menit</th>
                                        <th>Pulang Cepat/Menit</th>
                                        <th>Hari Kerja</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
                                    $sql_pegawai = bukaquery("SELECT tm_pegawai.id_user,
                                            tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_unit.id_unit , tm_unit.nama_unit                                
                                            FROM tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                                            where tm_pegawai.rumpun LIKE '%Dokter%' and tm_pegawai.status='AKTIF' order by tm_unit.nama_unit desc ");
                                    while ($row = fetch_array($sql_pegawai)) {
                                        $no++;
                                        $cv_waktu_pengurangan = getOne("select count(id_waktu_k) from tm_waktu_k where month(tm_waktu_k.date_k)='$bulan_post' and year(tm_waktu_k.date_k)='$thn' and tm_waktu_k.id_user='$row[id_user]'");
                                        $cv_waktu_penambahan = getOne("select count(id_waktu_t) from tm_waktu_t where month(tm_waktu_t.date_t)='$bulan_post' and tm_waktu_t.id_user='$row[id_user]'");
                                        $cv_waktu_shift = getOne("select count(id_waktu_s) from tm_waktu_s where month(tm_waktu_s.date_s)='$bulan_post' and tm_waktu_s.id_user='$row[id_user]'");
                                        $cv_potongan_pegawai = getOne("select count(id_potongan_p) from tm_potongan_p where month(tm_potongan_p.date_p)='$bulan_post' and tm_potongan_p.id_user='$row[id_user]'");
                                        $cv_tkhusus_pegawai = getOne("select count(id_uang_t) from tm_uang_t where month(tm_uang_t.date_p)='$bulan_post'  and tm_uang_t.id_user='$row[id_user]'");
                                        $penambahan = fetch_array(bukaquery("SELECT * FROM tm_waktu_t where month(tm_waktu_t.date_t)='$bulan_post' and tm_waktu_t.id_user='$row[id_user]'"));
                                        $pengurangan = fetch_array(bukaquery("SELECT * FROM tm_waktu_k where month(tm_waktu_k.date_k)='$bulan_post' and year(tm_waktu_k.date_k)='$thn' and tm_waktu_k.id_user='$row[id_user]'"));
                                        $cuti = fetch_array(bukaquery("SELECT sum(ct_alasan_k+ct_sakit_k+ct_persalinan_k) as cuti FROM tm_waktu_k where month(tm_waktu_k.date_k)='$bulan_post' and tm_waktu_k.id_user='$row[id_user]'"));
                                        //$shift = fetch_array(bukaquery("SELECT * FROM tm_waktu_s where tm_waktu_s.date_s='$tmtcek' and tm_waktu_s.id_user='$row[id_user]'"));
                                        $jhk = fetch_array(bukaquery("SELECT (tm_waktu_s.j_hkm+tm_waktu_s.j_hks+tm_waktu_s.j_hlm+tm_waktu_s.j_hlp+tm_waktu_s.j_hls+tm_waktu_s.j_hrm+tm_waktu_s.j_hrp+tm_waktu_s.j_hrs+tm_waktu_s.j_ns) as jumlah from tm_waktu_s where month(tm_waktu_s.date_s)='$bulan_post' and year(tm_waktu_s.date_s)='$thn'  and tm_waktu_s.id_user='$row[id_user]'"))
                                    ?>
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['nama_pegawai']; ?></td>
                                            <td><?php echo $row['nama_unit']; ?></td>
                                            <td><?php echo $pengurangan['alpha']; ?></td>
                                            <td><?php echo $pengurangan['sakit1']; ?></td>
                                            <td><?php echo $pengurangan['sakit2']; ?></td>
                                            <td><?php echo $pengurangan['izin']; ?></td>
                                            <td><?php echo $pengurangan['ct_alasan_k']+$pengurangan['ct_sakit_k']+$pengurangan['ct_persalinan_k']; ?></td>
                                            <td><?php echo $pengurangan['telat']; ?></td>
                                            <td><?php echo $pengurangan['pulang_cepat']; ?></td>
                                            <td><?php
                                                if ($jhk['jumlah'] != '') {
                                                    echo $jhk['jumlah'] . " Hari";
                                                }
                                                ?></td>
                                            <td>
                                                                                        
                                                <a href="?<?php echo paramEncrypt("module=absensi-pegawai&act=cek-absensi-pegawai2&id=" . $row['nip'] . "&dpa=" . $row['id_unit'] . ""); ?>"><span title="Absensi" class="btn-xs btn-info fa fa-eye">Absensi</span></a>
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