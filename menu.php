<!-- sidebar menu: : style can be found in sidebar.less -->
<ul class="sidebar-menu" data-widget="tree">
    <?php if ($superuser == '') { ?>
        <li class="header">
            <div id="date-time"></div>
        </li>
        <li class="header">MAIN NAVIGATION</li>

        <!-- MENU AWAL PNS -->
        <?php if ($status_pegawai == 'PNS') { ?>
            <li>
                <a href="?<?php echo paramEncrypt('module=dashboard&act=home'); ?>">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=absensi-pegawai'); ?>">
                    <i class="fa fa-id-badge"></i> <span>Absensi</span>
                </a>
            </li>

            <!-- MENU AWAL SELAIN PNS -->
        <?php } else { ?>
            <li>
                <a href="?<?php echo paramEncrypt('module=dashboard&act=dashboard'); ?>">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>

            <!-- KINERJA & ABSENSI -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-book"></i> <span>E-Kinerja dan Absensi</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="?<?php echo paramEncrypt('module=kinerja-pegawai&act=list-data-skp-tahunan-pegawai'); ?>"><i
                                class="fa fa-circle-o"></i> SKP Tahunan</a></li>
                    <li><a href="?<?php echo paramEncrypt('module=kinerja-pegawai&act=add-kinerja-pegawai'); ?>"><i
                                class="fa fa-circle-o"></i> Aktifitas</a></li>
                    <li>
                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=absensi-pegawai'); ?>">
                            <i class="fa fa-id-badge"></i> <span>Absensi</span>
                        </a>
                    </li>

                </ul>
            </li>
        <?php } ?>

        <!-- PROFIL -->
        <li>
            <a href="?<?php echo paramEncrypt('module=simpeg&act=profile-update'); ?>">
                <i class="fa fa-id-card-o"></i> <span>Profil</span>
            </a>
        </li>

        <!-- CUTI -->
        <li class="treeview">
            <a href="#">
                <i class="fa fa-book"></i> <span>Cuti</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                <li class="treeview-menu">
                <li><a href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-pengajuan-cuti-pegawai'); ?>"><i
                            class="fa fa-circle-o"></i> Pengajuan Cuti
                    </a>
                </li>
                <li><a
                        href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-assesment-pengganti-cuti-pegawai'); ?>"><i
                            class="fa fa-circle-o"></i> Acc Pengganti Cuti
                        <?php
                        $notif = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti where tm_cuti.nip_pengganti='$nip' and tm_cuti.acc_pengganti='-'");
                        if ($notif > 0) {
                            ?>
                            <span class="pull-right-container">
                                <small class="label pull-right bg-red"><?php echo $notif; ?></small>
                            </span>
                        <?php } ?>
                    </a>
                </li>
            </ul>
        </li>


        <?php
    }
    ?>

    <!-- HELPDESK -->
    <!--<li>-->
    <!--    <a href="https://rsudmatraman.jakarta.go.id/helpdesk" target="_blank">-->
    <!--        <i class="fa fa-ticket"></i> <span>Helpdesk</span>-->
    <!--    </a>-->
    <!--</li>-->


    <!-- ASSESMEN CUTI -->
    <?php if ($hak_akses['menu_val_kasatpel'] == '1' or $hak_akses['menu_val_pj'] == '1' or $hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_kasie'] == '1') {
        ?>
        <li class="treeview">
            <?php
            $notif = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti where tm_cuti.nip_pengganti='$nip' and tm_cuti.acc_pengganti='-'");
            if ($notif > 0) {
                ?>
                <a href="#">
                    <i class="fa fa-check-circle-o"></i> <span>Assesment Cuti</span>
                    <span class="pull-right-container">
                        <small class="label pull-right bg-red"><?php echo '!'; ?></small>
                    </span>
                </a>
            <?php } else { ?>
                <a href="#">
                    <i class="fa fa-check-circle-o"></i> <span>Assesment Cuti</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
            <?php } ?>
            <ul class="treeview-menu">
                <li class="treeview-menu">
                <li><a
                        href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-assesment-pengganti-cuti-pegawai'); ?>"><i
                            class="fa fa-circle-o"></i> Assesment Pengganti
                        <?php
                        $notif = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti where tm_cuti.nip_pengganti='$nip' and tm_cuti.acc_pengganti='-'");
                        if ($notif > 0) {
                            ?>
                            <span class="pull-right-container">
                                <small class="label pull-right bg-red"><?php echo $notif; ?></small>
                            </span>
                        <?php } ?>
                    </a>
                </li>
                <?php if ($hak_akses['menu_val_kasatpel'] == '1') { ?>
                    <li class="treeview-menu">
                    <li><a
                            href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-assesment-satpel-cuti-pegawai'); ?>"><i
                                class="fa fa-circle-o"></i> Assesment Kasatpel
                            <?php
                            if ($hak_akses['menu_val_pj'] == '1' and $hak_akses['menu_val_kasatpel'] == '0') {
                                $notif_pj = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti "
                                    . "inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user where tm_cuti.nip_pj='$nip' and tm_cuti.acc_pengganti='Y' and tm_cuti.acc_pj='-'");
                            } elseif ($hak_akses['menu_val_kasatpel'] == '1' and $hak_akses['menu_val_pj'] == '0') {
                                $notif_pj = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti "
                                    . "inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user where tm_pegawai.id_kasatpel='$kasatpel' and tm_cuti.acc_pengganti='Y' and tm_cuti.acc_pj='-'");
                            } else {
                                $notif_pj = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti "
                                    . "inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user where tm_pegawai.id_kasatpel='$kasatpel' and tm_pegawai.id_unit='$id_unit' and tm_cuti.acc_pengganti='Y' and tm_cuti.acc_pj='-' group by tm_cuti.periode_cuti");
                            }
                            if ($notif_pj > 0) {
                                ?>
                                <span class="pull-right-container">
                                    <small class="label pull-right bg-red"><?php echo $notif_pj; ?></small>
                                </span>
                            <?php } ?>
                        </a>
                    </li>

                <?php } ?>
                <?php if ($hak_akses['menu_val_pj'] == '1') { ?>
                    <li class="treeview-menu">
                    <li><a href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-assesment-pj-cuti-pegawai'); ?>"><i
                                class="fa fa-circle-o"></i> Assesment PJ
                            <?php
                            if ($hak_akses['menu_val_pj'] == '1' and $hak_akses['menu_val_kasatpel'] == '0') {
                                $notif_pj = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti "
                                    . "inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user where tm_cuti.nip_pj='$nip' and tm_cuti.acc_pengganti='Y' and tm_cuti.acc_pj='-'");
                            } elseif ($hak_akses['menu_val_kasatpel'] == '1' and $hak_akses['menu_val_pj'] == '0') {
                                $notif_pj = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti "
                                    . "inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user where tm_pegawai.id_kasatpel='$kasatpel' and tm_cuti.acc_pengganti='Y' and tm_cuti.acc_pj='-'");
                            } else {
                                $notif_pj = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti "
                                    . "inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user where tm_pegawai.id_kasatpel='$kasatpel' and tm_pegawai.id_unit='$id_unit' and tm_cuti.acc_pengganti='Y' and tm_cuti.acc_pj='-' group by tm_cuti.periode_cuti");
                            }
                            if ($notif_pj > 0) {
                                ?>
                                <span class="pull-right-container">
                                    <small class="label pull-right bg-red"><?php echo $notif_pj; ?></small>
                                </span>
                            <?php } ?>
                        </a>
                    </li>

                <?php } ?>
                <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                    <li><a
                            href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-assesment-kepegawaian-cuti-pegawai'); ?>"><i
                                class="fa fa-circle-o"></i> Assesment Kepegawaian
                            <?php
                            $notif_kepegawaian = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti where tm_cuti.acc_kepegawaian='-' and tm_cuti.acc_pj='Y'");
                            if ($notif_kepegawaian > 0) {
                                ?>
                                <span class="pull-right-container">
                                    <small class="label pull-right bg-red"><?php echo $notif_kepegawaian; ?></small>
                                </span>
                            <?php } ?>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($hak_akses['menu_val_kasie'] == '1') { ?>
                    <li><a
                            href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-assesment-kasie-cuti-pegawai'); ?>"><i
                                class="fa fa-circle-o"></i> Assesment Kasie
                            <?php
                            $notif_kasie = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti "
                                . "inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user where tm_pegawai.sub_bagian='$kasie' and tm_cuti.nip_kasie='$nip' and tm_cuti.acc_kepegawaian='Y' and tm_cuti.acc_kasie='-'");
                            if ($notif_kasie > 0) {
                                ?>
                                <span class="pull-right-container">
                                    <small class="label pull-right bg-red"><?php echo $notif_kasie; ?></small>
                                </span>
                            <?php } ?>
                        </a>
                    </li>
                    <li>
                        <a href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-maping-cuti-kepegawaian'); ?>">
                            <i class="fa fa-calendar-check-o"></i> <span>Daftar Cuti Pegawai</span>
                        </a>
                    </li>
                <?php } ?>


            </ul>
            <ul class="treeview-menu">
                <?php if ($hak_akses['menu_val_pj'] == '1') { ?>
                    <li class="treeview-menu">
                    <li><a
                            href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-assesment-satpel-cuti-pegawai'); ?>"><i
                                class="fa fa-circle-o"></i> Assesment PJ
                            <?php
                            if ($hak_akses['menu_val_pj'] == '1' and $hak_akses['menu_val_kasatpel'] == '0') {
                                $notif_pj = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti "
                                    . "inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user where tm_cuti.nip_pj='$nip' and tm_cuti.acc_pengganti='Y' and tm_cuti.acc_pj='-'");
                            } elseif ($hak_akses['menu_val_kasatpel'] == '1' and $hak_akses['menu_val_pj'] == '0') {
                                $notif_pj = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti "
                                    . "inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user where tm_pegawai.id_kasatpel='$kasatpel' and tm_cuti.acc_pengganti='Y' and tm_cuti.acc_pj='-'");
                            } else {
                                $notif_pj = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti "
                                    . "inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user where tm_pegawai.id_kasatpel='$kasatpel' and tm_pegawai.id_unit='$id_unit' and tm_cuti.acc_pengganti='Y' and tm_cuti.acc_pj='-' group by tm_cuti.periode_cuti");
                            }
                            if ($notif_pj > 0) {
                                ?>
                                <span class="pull-right-container">
                                    <small class="label pull-right bg-red"><?php echo $notif_pj; ?></small>
                                </span>
                            <?php } ?>
                        </a>
                    </li>

                <?php } ?>
                <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                    <li><a
                            href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-assesment-kepegawaian-cuti-pegawai'); ?>"><i
                                class="fa fa-circle-o"></i> Assesment Kepegawaian
                            <?php
                            $notif_kepegawaian = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti where tm_cuti.acc_kepegawaian='-' and tm_cuti.acc_pj='Y'");
                            if ($notif_kepegawaian > 0) {
                                ?>
                                <span class="pull-right-container">
                                    <small class="label pull-right bg-red"><?php echo $notif_kepegawaian; ?></small>
                                </span>
                            <?php } ?>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($hak_akses['menu_val_kasie'] == '1') { ?>
                    <li><a
                            href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-assesment-kasie-cuti-pegawai'); ?>"><i
                                class="fa fa-circle-o"></i> Assesment Kasie
                            <?php
                            $notif_kasie = getOne("select count(tm_cuti.id_cuti) as total from tm_cuti "
                                . "inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user where tm_pegawai.sub_bagian='$kasie' and tm_cuti.nip_kasie='$nip' and tm_cuti.acc_kepegawaian='Y' and tm_cuti.acc_kasie='-'");
                            if ($notif_kasie > 0) {
                                ?>
                                <span class="pull-right-container">
                                    <small class="label pull-right bg-red"><?php echo $notif_kasie; ?></small>
                                </span>
                            <?php } ?>
                        </a>
                    </li>
                    <li>
                        <a href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-maping-cuti-kepegawaian'); ?>">
                            <i class="fa fa-calendar-check-o"></i> <span>Daftar Cuti Pegawai</span>
                        </a>
                    </li>
                <?php } ?>


            </ul>
        </li>


    <?php } ?>
    <!-- K3 Kesling  -->
    <li class="treeview">
        <a href="#"><i class="fa fa-calendar-check-o"></i> <span>MCU, K3 dan PPI</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <!-- Input Hasil MCU -->
            <?php
            if ($id_user == '2112280130') {
                ?>
                <li><a href="?<?php echo paramEncrypt('module=mcu&act=list-data-up-mcu-pegawai'); ?>"><i
                            class="fa fa-list"></i> Input Hasil MCU Pegawai</a></li>
                <?php
            }
            ?>
            <!-- And Input Hasil MCU -->
            <li><a href="?<?php echo paramEncrypt('module=mcu-pegawai&act=list-data-mcu-pegawai'); ?>"><i
                        class="fa fa-list"></i> Hasil MCU Pegawai</a></li>
            <li><a href="?<?php echo paramEncrypt('module=olahraga-pegawai&act=list-data-olahraga-pegawai'); ?>"><i
                        class="fa fa-list"></i> Input Aktifitas olahraga</a></li>
            <li><a href="?<?php echo paramEncrypt('module=imt-pegawai&act=list-data-imt-pegawai'); ?>"><i
                        class="fa fa-list"></i> Input IMT</a></li>
            <li>
                <a href="?<?php echo paramEncrypt('module=view-kesling-unit&act=list-data-kesling-unit'); ?>">
                    <i class="fa fa-list"></i> <span> Rekomendasi Kesling dan PPI</span>
                </a>
            </li>

        </ul>
    </li>

    <!-- Validasi Jadwal Admin Yanmep/Yankep -->
    <?php
    if ($idlevel == 'LVL-000046' or $idlevel == 'LVL-000045' or $idlevel == 'LVL-000075' or $idlevel == 'LVL-000116') {
        ?>
        <li class="header">Kepegawaian</li>
        <li>
            <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=validasi-data-jadwal-pegawai'); ?>">
                <i class="fa fa-check-square-o"></i> <span>Validasi Jadwal Kepegawaian</span>
            </a>
        </li>
        <li>
            <a href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-maping-cuti-kepegawaian'); ?>">
                <i class="fa fa-calendar-check-o"></i> <span>Daftar Cuti Pegawai</span>
            </a>
        </li>
        <li><a href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-maping-cuti-pegawai'); ?>">
                <i class="fa fa-circle-o"></i> Kalender Cuti Pegawai</a>
        </li>
        <?php
    }
    ?>

    <?php
    if ($hak_akses['menu_val_pj'] == '1' && $hak_akses['menu_val_kasatpel'] != '1' ) {
        ?>
        <li>
            <a href="?<?php echo paramEncrypt('module=daftar-hadir&act=daftar-hadir-unit'); ?>">
                <i class="fa fa-calendar-check-o"></i> <span>Daftar Kehadiran Unit </span>
            </a>
        </li>
        <li>
            <a href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-maping-cuti-unit'); ?>">
                <i class="fa fa-calendar-check-o"></i> <span>Laporan Cuti Pegawai</span>
            </a>
        </li>
        <?php
    }
    ?>
    
    <?php
    if ($hak_akses['menu_val_pj'] == '1' && $hak_akses['menu_val_kasatpel'] == '1' ) {
        ?>
        <li>
            <a href="?<?php echo paramEncrypt('module=daftar-hadir&act=daftar-hadir-kasatpel'); ?>">
                <i class="fa fa-calendar-check-o"></i> <span>Daftar Kehadiran Unit </span>
            </a>
        </li>
        <li>
            <a href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-maping-cuti-unit'); ?>">
                <i class="fa fa-calendar-check-o"></i> <span>Laporan Cuti Pegawai</span>
            </a>
        </li>
        <?php
    }
    ?>

    <?php
    if ($hak_akses['menu_val_pj'] == '0' && $hak_akses['menu_val_kasie'] != '1') {
        ?>
        <li>
            <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=jadwal-unit'); ?>">
                <i class="fa fa-calendar"></i> <span>Jadwal Unit</span>
            </a>
        </li>

        <?php
    }
    ?>
    
    <?php
    if ($hak_akses['menu_val_kasatpel'] == '1' && $hak_akses['menu_val_pj'] == '0') {
        ?>
        <li>
            <a href="?<?php echo paramEncrypt('module=daftar-hadir&act=daftar-hadir-unit'); ?>">
                <i class="fa fa-calendar"></i> <span>Daftar Kehadiran Unit</span>
            </a>
        </li>

        <?php
    }
    ?>
    
    <!-- MENU KASIE  -->
    <?php
    if ($idlevel == 'LVL-000042' or $idlevel == 'LVL-000043' or $idlevel == 'LVL-000044') {
        ?>
        <li>
            <a href="?<?php echo paramEncrypt('module=daftar-hadir&act=daftar-pegawai'); ?>">
                <i class="fa fa-calendar-check-o"></i> <span>Daftar Kehadiran Pegawai</span>
            </a>
        </li>

        <?php
    }
    ?>
    <!-- VALIDASI BULANAN  -->
    <?php
    if ($day >= 2 and $day <= $setup['validasi_pj'] and $idlevel != 'LVL-000093' and $idlevel != 'LVL-000094') {
        if ($hak_akses['menu_val_kasatpel'] == '1' or $hak_akses['menu_val_pj'] == '1') {
            ?>
            <li class="header">VALIDASI</li>
            <li>
                <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                    <i class="fa fa-check-square-o"></i> <span>Validasi Pegawai</span>
                </a>
            </li>
            <?php
        }
    }

    if ($day >= 2 and $day <= $setup['validasi_pj']) {
        if ($idlevel == 'LVL-000093' or $idlevel == 'LVL-000094' or $idlevel == 'LVL-000110') {
            ?>
            <li class="header">VALIDASI PJLP</li>
            <li>
                <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pjlp'); ?>">
                    <i class="fa fa-check-square-o"></i> <span>Validasi PJLP</span>
                </a>
            </li>
            <?php
        }
    }

    if ($day >= 3 and $day <= $setup['validasi_kasie']) {
        // $setup['validasi_pj']
        if ($hak_akses['menu_val_kasie'] == '1' and $idlevel != 'LVL-000052' and $idlevel != 'LVL-000064' and $idlevel != 'LVL-000003' and $idlevel != 'LVL-000042') {
            ?>
            <li class="header">VALIDASI</li>
            <li>
                <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                    <i class="fa fa-check-square-o"></i> <span>Validasi Kasie</span>
                </a>
            </li>
            <?php
        }
        if ($hak_akses['menu_val_kasie'] == '1' and $idlevel == 'LVL-000042'){
            ?>
            <li class="header">VALIDASI</li>
            <li>
                <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                    <i class="fa fa-check-square-o"></i> <span>Validasi Pegawai</span>
                </a>
            </li> 
            <?php
        }
    }
    ?>

    <!-- VALIDASI JADWAL -->
    <?php
    if ($hak_akses['menu_val_kasatpel'] == '0' && $hak_akses['menu_val_pj'] == '1') {
        ?>
        <li class="header">JADWAL PEGAWAI</li>
        <li>
            <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'); ?>">
                <i class="fa fa-check-square-o"></i> <span>Input Jadwal Pegawai</span>
            </a>
        </li>
        <?php
    }
    ?>
    
    <?php
    if ($hak_akses['menu_val_kasatpel'] == '1' && $hak_akses['menu_val_pj'] == '1') {
        ?>
        <li class="header">JADWAL PEGAWAI</li>
        <li>
            <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'); ?>">
                <i class="fa fa-check-square-o"></i> <span>Input Jadwal Pegawai</span>
            </a>
        </li>
        <?php
    }
    ?>

    <!-- Menu Yanmed -->
    <?php if ($hak_akses['menu_absensi'] == '1') { ?>
        <li class="header">ABSENSI SPESIALIS</li>
        <li>
            <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=validasi-data-absensi-spesialis'); ?>">
                <i class="fa fa-check-square-o"></i> <span>Absensi </span>
            </a>
        </li>
        <?php
    }
    ?>

    <!-- MENU KEPEGAWAIAN -->
    <?php if ($hak_akses['menu_kepegawaian'] == '1' or $superuser != '') { ?>
        <li class="header">KEPEGAWAIAN</li>
        <li>
            <a href="?<?php echo paramEncrypt('module=daftar-hadir&act=daftar-pegawai'); ?>">
                <i class="fa fa-calendar-check-o"></i> <span>Daftar Kehadiran Pegawai</span>
            </a>
        </li>
        <li>
            <a href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-maping-cuti-kepegawaian'); ?>">
                <i class="fa fa-calendar-check-o"></i> <span>Daftar Cuti Pegawai</span>
            </a>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-users"></i> <span>Data Pegawai</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-pegawai-pns'); ?>"><i
                            class="fa fa-circle-o"></i> PNS</a></li>
                <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-pegawai-non-pns'); ?>"><i
                            class="fa fa-circle-o"></i> NON PNS</a></li>
                <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-pegawai-spesialis'); ?>"><i
                            class="fa fa-circle-o"></i> SPESIALIS</a></li>
                <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-pegawai-isip'); ?>"><i
                            class="fa fa-circle-o"></i> INTERNSHIP</a></li>            
                <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-pegawai-pjlp'); ?>"><i
                            class="fa fa-circle-o"></i> PJLP</a></li>
                <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-pegawai-resign'); ?>"><i
                            class="fa fa-circle-o"></i> RESIGN/NONAKTIF</a></li>

                <li class="treeview">
                    <a href="#"><i class="fa fa-edit"></i> Form Pegawai
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="?<?php echo paramEncrypt('module=form-surat-peringatan&act=list-data-form-sp'); ?>"><i
                                    class="fa fa-circle-o"></i> Form SP/HUKDIS</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-hari-kerja'); ?>"><i
                                    class="fa fa-circle-o"></i> Form Hari Kerja</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-izin-belajar'); ?>"><i
                                    class="fa fa-circle-o"></i> Form Izin Belajar</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-grade'); ?>"><i
                                    class="fa fa-circle-o"></i> Grade Jabatan</a></li>
                        <?php
                        if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_surtug'] == '1') {
                            ?>
                            <li>
                                <a href="?<?php echo paramEncrypt('module=form-surat-tugas&act=list-data-form-tugas'); ?>">
                                    <i class="fa fa-book"></i> <span>Form Surat Tugas</span>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#"><i class="fa fa-database"></i> Master Data
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-skp'); ?>"><i
                                    class="fa fa-circle-o"></i> Master SKP</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-bagian'); ?>"><i
                                    class="fa fa-circle-o"></i> Master Bagian</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-kasatpel'); ?>"><i
                                    class="fa fa-circle-o"></i> Master Kasatpel</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-sanksi'); ?>"><i
                                    class="fa fa-circle-o"></i> Master Sanksi</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-absensi'); ?>"><i
                                    class="fa fa-circle-o"></i> Master Shift/Absensi</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-libur'); ?>"><i
                                    class="fa fa-circle-o"></i> Master Hari Libur</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-formasi'); ?>"><i
                                    class="fa fa-circle-o"></i> Master Formasi</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-bpjs'); ?>"><i
                                    class="fa fa-circle-o"></i> Master UMP BPJS</a></li>
                    </ul>
                </li>
                <li>
                    <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=pengaturan-absensi'); ?>"><i
                            class="fa fa-cog"></i> Pengaturan Shift Untuk Unit
                    </a>
                </li>
                <!--<li>-->
                <!--    <a href="?<?php echo paramEncrypt('module=master-data&act=upload-data-pegawai'); ?>"><i class="fa fa-upload"></i> Upload Pegawai-->
                <!--    </a>-->
                <!--</li>-->
            </ul>
        </li>

        <li class="treeview">
            <a href="#"><i class="fa fa-database"></i> Validasi
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                <li>
                    <a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=validasi-data-jadwal-pegawai'); ?>">
                        <i class="fa fa-check-square-o"></i> <span>Validasi Jadwal Kepegawaian</span>
                    </a>
                </li>
                <li><a href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=validasi-data-absensi'); ?>">
                        <i class="fa fa-calendar-check-o"></i> <span>Validasi Finger</span>
                    </a>
                </li>
                <li>
                    <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-cuti'); ?>">
                        <i class="fa fa-check-square-o"></i> <span>Validasi Cuti</span>
                    </a>
                </li>
                <li>
                    <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai'); ?>">
                        <i class="fa fa-check-square-o"></i> <span>Validasi Absensi Bulanan</span>
                    </a>
                </li>
                <li>
                    <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-spesialis'); ?>">
                        <i class="fa fa-check-square-o"></i> <span>Validasi Absensi Spesialis</span>
                    </a>
                </li> 
            </ul>
        </li>
        <?php
    }

    ?>




    <!-- MENU KEPEGAWAIAN PJLP -->
    <?php if ($idlevel == 'LVL-000108' or $idlevel == 'LVL-000110' or $hak_akses['menu_kepegawaian'] == '1') { ?>
        <li class="header">KEPEGAWAIAN PJLP & K3</li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-users"></i> <span>Data Pegawai PJLP</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-pegawai-pjlp'); ?>"><i
                            class="fa fa-circle-o"></i> PJLP</a></li>
                <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-pegawai-resign-pjlp'); ?>"><i
                            class="fa fa-circle-o"></i> RESIGN/NONAKTIF</a></li>

            </ul>
        </li>
        <li class="treeview">
            <a href="#"><i class="fa fa-amazon"></i>Absensi PJLP
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                <li><a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=laporan-absensi-pjlp'); ?>"><i
                            class="fa fa-circle-o"></i> Lap Absensi</a></li>
                <li><a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=laporan-absensi-pjlp-tahun'); ?>"><i
                            class="fa fa-circle-o"></i> Lap Absensi / Tahun</a></li>
            </ul>
        </li>
        <li>
            <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai-pjlp'); ?>">
                <i class="fa fa-check-square-o"></i> <span>Validasi Absensi PJLP</span>
            </a>
        </li>
        <li class="treeview">
            <a href="#"><i class="fa fa-calendar-check-o"></i> Laporan K3
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                <li><a
                        href="?<?php echo paramEncrypt('module=olahraga-pegawai&act=list-data-maping-olahraga-pegawai-laporan'); ?>"><i
                            class="fa fa-circle-o"></i> Laporan Olahraga Pegawai</a></li>
                <li><a href="?<?php echo paramEncrypt('module=imt-pegawai&act=list-data-maping-imt-pegawai-laporan'); ?>"><i
                            class="fa fa-circle-o"></i> Laporan IMT Pegawai</a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#"><i class="fa fa-check-square-o"></i>Tahu Si Kerdil
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                <li><a href="?<?php echo paramEncrypt('module=kesling&act=list-data-kesling'); ?>"><i
                            class="fa fa-circle-o"></i> Rekomendasi Kesling</a></li>
                <li><a href="?<?php echo paramEncrypt('module=view&act=list-data-kesling1'); ?>"><i
                            class="fa fa-circle-o"></i> Rekomendasi PPI</a></li>
                <li class="treeview">
                    <a href="#"><i class="fa fa-circle-o"></i>Pemantauan Harian
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="?<?php echo paramEncrypt('module=kebersihan-unit&act=list-data-kebersihan'); ?>"><i
                                    class="fa fa-circle-o"></i> Kebersihan Unit</a></li>
                        <li>
                            <a href="../../../keslingk3/data.php">
                                <i class="fa fa-circle-o"></i> <span>Checklist Harian</span></a>
                        </li>
                        <li>
                            <a href="../../../keslingk3/main">
                                <i class="fa fa-circle-o"></i> <span>Grafik Bulan Ini</span></a>
                        </li>
                    </ul>
                </li>
        </li>
    </ul>
    </li>
    <li>
        <?php
    }
    ?>



    <?php if ($hak_akses['menu_diklat'] == '1') { ?>
    <li class="header">DIKLAT</li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-book"></i> <span>Agenda</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="?<?php echo paramEncrypt('module=agenda-rapat&act=list-agenda-rapat'); ?>"><i
                        class="fa fa-circle-o"></i> List Agenda</a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-university"></i> <span>Pelatihan</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="?<?php echo paramEncrypt('module=diklat&act=list-diklat-pegawai'); ?>"><i
                        class="fa fa-circle-o"></i> List Pelatihan Pegawai</a></li>
        </ul>
    </li>
<?php } ?>
<?php if ($hak_akses['menu_keuangan'] == '1' or $superuser != '') { ?>
    <li class="header">KEUANGAN</li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-money"></i> <span>Form Keuangan</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-penyerapan'); ?>"><i
                        class="fa fa-circle-o"></i> Form Pendapatan</a></li>
            <li><a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-shifting'); ?>"><i
                        class="fa fa-circle-o"></i> Form Honor Shift</a></li>
        </ul>
    </li>
    <li><a href="?<?php echo paramEncrypt('module=master-data&act=set-spj'); ?>"><i class="fa fa-map-signs"></i> Maping TTD
            SPJ</a></li>
<?php } ?>


<!-- MENU LAPORAN -->
<?php if ($hak_akses['menu_keuangan'] == '1' or $hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_val_kasie'] == '1' or $hak_akses['menu_laporan'] == '1' or $superuser != '') { ?>
    <li class="header">Rekapitulasi</li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-book"></i> <span>Laporan</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <?php if ($hak_akses['menu_kepegawaian'] == '1' or $hak_akses['menu_laporan'] == '1' or $hak_akses['menu_val_kasie'] == '1' or $superuser != '') { ?>
                <li class="treeview">
                    <a href="#"><i class="fa fa-amazon"></i>Absensi
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=laporan-absensi-pegawai'); ?>"><i
                                    class="fa fa-circle-o"></i> Lap Absensi</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=laporan-absensi-pegawai-tahun'); ?>"><i
                                    class="fa fa-circle-o"></i> Lap Absensi / Tahun</a></li>
                    </ul>
                </li>

                <?php if ($hak_akses['menu_val_kasie'] == '1' or $hak_akses['menu_laporan'] == '1' or $hak_akses['menu_kepegawaian'] == '1' or $superuser != '') { ?>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-calendar-check-o"></i> Cuti
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-maping-cuti-pegawai'); ?>"><i
                                        class="fa fa-circle-o"></i> Kalender Cuti Pegawai</a></li>
                            <li><a
                                    href="?<?php echo paramEncrypt('module=cuti-pegawai&act=list-data-maping-cuti-pegawai-laporan'); ?>"><i
                                        class="fa fa-circle-o"></i> Laporan Cuti Pegawai</a></li>
                        </ul>
                    </li>
                    <?php
                }
            }
            ?>
            <?php if ($hak_akses['menu_keuangan'] == '1' or $superuser != '') { ?>
                <li class="treeview">
                    <a href="#"><i class="fa fa-dollar"></i> Gaji
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="?<?php echo paramEncrypt('module=laporan&act=laporan-gaji-bruto-pegawai'); ?>"><i
                                    class="fa fa-circle-o"></i> Gaji Pegawai</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=laporan&act=laporan-tunjangan-pegawai'); ?>"><i
                                    class="fa fa-circle-o"></i> Tunjangan Pegawai</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=laporan&act=laporan-potongan-pegawai'); ?>"><i
                                    class="fa fa-circle-o"></i> Potongan Pegawai</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=laporan&act=laporan-tunjangan-khusus'); ?>"><i
                                    class="fa fa-circle-o"></i> Tunjangan Khusus</a></li>
                        <li><a href="?<?php echo paramEncrypt('module=laporan&act=laporan-shift'); ?>"><i
                                    class="fa fa-circle-o"></i> Honor Shift</a></li>            
                    </ul>
                </li>

            <?php } ?>
            <?php if ($hak_akses['menu_val_kasie'] == '1' or $hak_akses['menu_kepegawaian'] == '1' or $superuser != '') { ?>
                <li class="treeview">
                    <a href="#"><i class="fa fa-file-o"></i> Penilaian Pegawai
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="?<?php echo paramEncrypt('module=laporan&act=laporan-penilaian-pegawai'); ?>"><i
                                    class="fa fa-circle-o"></i> Bulanan</a></li>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>

<!-- MENU PERPUSTAKAAN -->
<li class="header">PERPUSTAKAAN DAN SURAT-SURAT</li>
<li class="treeview">
    <a href="#">
        <i class="fa fa-book"></i> <span>Arsip</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li><a href="?<?php echo paramEncrypt('module=perpustakaan&act=ebook'); ?>"><i
                    class="fa fa-circle-o"></i>eBook</a></li>
        <li><a href="?<?php echo paramEncrypt('module=perpustakaan&act=kebijakan'); ?>"><i class="fa fa-circle-o"></i>
                Kebijakan</a></li>
        <li><a href="?<?php echo paramEncrypt('module=perpustakaan&act=spo'); ?>"><i class="fa fa-circle-o"></i> SPO</a>
        </li>
        <li><a href="?<?php echo paramEncrypt('module=perpustakaan&act=peraturan'); ?>"><i class="fa fa-circle-o"></i>
                Peraturan</a></li>
    </ul>
</li>

<!-- MENU KONFIGURASI -->

<?php if ($hak_akses['configurasi'] == '1' or $superuser != '') { ?>
    <li class="header">ADMIN</li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-cogs"></i> <span>Pengaturan</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li class="treeview">
                <a href="#"><i class="fa fa-users"></i>Level User/Jabatan
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="?<?php echo paramEncrypt('module=configurasi&act=list-hak-akses'); ?>"><i
                                class="fa fa-circle-o"></i> List Hak Akses/Jabatan</a></li>
                </ul>
            </li>
            <li><a href="?<?php echo paramEncrypt('module=configurasi&act=setup'); ?>"><i class="fa fa-cog"></i> Setup</a>
            </li>
            <li>
                <a href="?<?php echo paramEncrypt('module=daftar-hadir&act=id-password'); ?>">
                    <i class="fa fa-key"></i> <span>ID Password Pegawai</span>
                </a>
            </li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-check-square-o"></i> <span>Validasi</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-yanmed'); ?>"><i
                        class="fa fa-calendar-check-o"></i> Validasi Kasie Yanmed</a></li>
            <li><a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-penunjang'); ?>"><i
                        class="fa fa-calendar-check-o"></i> Validasi Kasie Penunjang</a></li>
            <li><a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-tu'); ?>"><i
                        class="fa fa-calendar-check-o"></i> Validasi Kasie Tata Usaha</a></li>
            <li>
        </ul>
    </li>
<?php } ?>


<!-- MENU KHUSUS -->

<?php if ($id_unit == '2112280051') { ?>
    <li class="header">TAHU SI KERDIL</li>
    <li class="treeview">
    <li><a href="?<?php echo paramEncrypt('module=view&act=list-data-kesling1'); ?>"><i class="fa fa-check-square-o"></i>
            Rekomendasi PPI</a></li>
    <li>
    <?php } ?>


    <?php if ($id_user == '2112280043') { ?>
        <!--<li class="header">Input Kinerja</li>-->
        <!--<li class="treeview">-->
        <!--    <li><a href="?<?php echo paramEncrypt('module=kinerja-pegawai&act=kinerja-pegawai'); ?>"><i class="fa fa-check-square-o"></i> Input Aktifitas</a></li>-->
        <!--<li>-->
    <?php } ?>

    <?php if ($id_user == '2112280096') { ?>
        <!--<li class="header">Input Kinerja</li>-->
        <!--<li class="treeview">-->
        <!--    <li><a href="?<?php echo paramEncrypt('module=kinerja-pegawai&act=add-kinerja-pegawai'); ?>"><i class="fa fa-check-square-o"></i> Input Aktifitas</a></li>-->
        <!--<li>-->
    <?php } ?>



    </ul>