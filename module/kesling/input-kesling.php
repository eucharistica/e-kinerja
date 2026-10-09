<?php
$aksi = "module/kesling/aksi-input-kesling.php?";
$aksidel = "module/kesling/aksi-del-kesling.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;

    case "list-data-kesling":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST DATA HASIL LABORATORIUM</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">                
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-kesling">
                    Tambah Hasil Laboratorium
                </button>
                <a href="https://rsudmatraman.jakarta.go.id/keslingk3/main/rekomend.php" type="button" class="btn btn-instagram fa fa-search" title="cek rekapitulasi"></a>

                <!--Modal Add IMT -->
                <div class="modal fade" id="modal-add-kesling">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Tambah Hasil Laboratorium </h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=kesling&act=add-kesling&id=' . $id_user . ''); ?>" method="post"> 
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Unit / Ruangan</label>
                                        <select class="form-control select2" name="id_user"  data-placeholder=" -- Pilih Unit / Ruangan --" style="width: 100%;" required>
                                            <option selected="selected" value="">
                                            <option value="2112280088">SIMRS
                                            <option value="2112280073">IGD
                                            <option value="2112280040">Rawat Inap Dewasa
                                            <option value="2112280062">Farmasi
                                            <option value="2112280185">Pendaftaran
                                            <option value="2112280029">Laundry
                                            <option value="2112280032">Driver
                                            <option value="2112280074">Jenazah & Gasmedis
                                            <option value="2112280021">Rawat Jalan
                                            <option value="2112280129">Fisioterapi
                                            <option value="2112280072">Komite
                                            <option value="2112280085">Rawat Inap Anak
                                            <option value="2112280149">Radiologi
                                            <option value="2112280142">Laboratorium
                                            <option value="2112280059">Kamar Oprasi
                                            <option value="2112280187">CSSD
                                            <option value="2112280064">Keuangan
                                            <option value="2112280010">Gudang Barang
                                            <option value="2112280128">Kesling
                                            <option value="2112280022">Rekam Medis
                                            <option value="2112280038">Manajemen
                                            <option value="2112280037">Casemix
                                            <option value="2112280108">Pengadaan
                                            <option value="2112280148">Kalimaya
                                            <option value="2112280112">Tata Boga
                                            <option value="2112280063">Gizi
                                            <option value="2112280114">IPSRS & Atem
                                            </option>               
                                        </select>
                                        <br>
                                        <br>
                                        <label>Detail Lokasi</label>
                                        <input class="form-control" name="detail" placeholder="Masukan Detail" rows="5" required/>
                                        <br>
                                        <label>Tanggal Pemeriksaan</label>
                                        <input type="date" class="form-control" placeholder="mm/dd/yyyy" name="tgl_hasil" required>
                                        <br>
                                        <label>Aktifitas</label>
                                        <select class="form-control select2" name="id_kegiatan"  data-placeholder="-Pilih Aktifitas-" style="width: 100%;" required>
                                            <option selected="selected" value="3">Rekomendasi Kesehatan Lingkungan Unit</option>               
                                        </select>
                                        <br>
                                        <br>
                                        <label>Jenis Pemeriksaan</label>
                                        <select class="form-control select2" name="jenis"  data-placeholder="- Pilih Jenis Pemeriksaan - " style="width: 100%;" required>
                                            <option selected="selected" value=""> -- Pilih Jenis Pemeriksaan -- </option>
                                            <option value="Air Bersih Mikrobiologi">  Air Bersih Mikrobiologi</option>
                                            <option value="Air Bersih Kimia">  Air Bersih Kimia</option> 
                                            <option value="Air Minum Mikrobiologi">  Air Minum Mikrobiologi</option> 
                                            <option value="Air Minum Kimia">  Air Minum Kimia</option> 
                                            <option value="Air Limbah Mikrobiologi">  Air Limbah Mikrobiologi</option> 
                                            <option value="Air Limbah Kimia">  Air Limbah Kimia</option> 
                                            <option value="Cemaran Udara">  Cemaran Udara</option> 
                                            <option value="SWAB Dinding">  SWAB Dinding</option> 
                                            <option value="SWAB Lantai">  SWAB Lantai</option>
                                            <option value="SWAB AC">  SWAB AC</option>
                                            <option value="Udara Ambient">  Udara Ambient</option> 
                                            <option value="Uji Emisi">  Uji Emisi</option> 
                                            <option value="Pencahayaan">  Pencahayaan</option> 
                                            <option value="Kebisingan">  Kebisingan</option> 
                                            <option value="Makanan Matang">  Makanan Matang</option> 
                                            <option value="Makanan Mentah">  Makanan Mentah</option> 
                                            <option value="Alat Makan Pasien">  Alat Makan Pasien</option> 
                                            <option value="Alat Medis">  Alat Medis</option> 
                                        </select>
                                        <br>
                                        <label>Hasil</label>
                                        <input class="form-control" name="hasil" placeholder="Masukan Hasil Laboratorium" rows="5" required/>
                                        <br>
                                        <label>Standar</label>
                                        <input class="form-control" name="standar" placeholder="Masukan Standar" rows="5" required/>
                                        <br>
                                        <label>Kesimpulan</label>
                                        <select class="form-control select2" name="kesimpulan"  data-placeholder="-Pilih Kesimpulan-" style="width: 100%;" required>
                                            <option selected="selected" value="">
                                            <option value="Memenuhi Syarat">Memenuhi Syarat
                                            <option value="Tidak Memenuhi Syarat">Tidak Memenuhi Syarat
                                            </option>              
                                        </select>
                                        <br>
                                        <br>
                                        <label>Rekomendasi Kesling</label>
                                        <input class="form-control" name="r_kesling" placeholder="Masukan Rekomendasi Kesling" rows="5" required/>
                                        
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
                    <table id="imt" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><center>No</center></th>
                                <th><center>Ruang / Unit</center></th>
                                <th><center>Detail Lokasi</center></th>
                                <th><center>Tgl. Penilaian</center></th>
                                <th><center>Kegiatan</center></th>
                                <th><center>Jenis Pemeriksaan</center></th>
                                <th><center>Hasil</center></th>
                                <th><center>Standar</center></th>
                                <th><center>Kesimpulan</center></th>
                                <th><center>Rekomendasi Kesling</center></th>
                                <th><center>Rekomendasi PPI</center></th>
                                <th><center>Tindak Lanjut Unit</center></th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                                    <?php
                                    $no = 0;

                                        $kesling = bukaquery("SELECT * FROM tt_kesling,tm_pegawai,tm_unit,kegiatan WHERE tt_kesling.id_user = tm_pegawai.id_user and tm_pegawai.id_unit=tm_unit.id_unit and tt_kesling.id_kegiatan=kegiatan.id_kegiatan ORDER BY tt_kesling.id_kesling DESC");
                                        $no = 1;
                                        while ($b = mysqli_fetch_array($kesling)){
                                    ?>
                             <!-- Edit Modal kesling -->
                            <div class="modal fade" id="modal-ubah-<?php echo $b['id_kesling']; ?>"  role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksidel . paramEncrypt('module=kesling&act=update-kesling-utama&id=' . $b['id_kesling'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Edit Hasil Laboratorium</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <h4><label><?php echo $b['nama_unit']; ?></label></h4>
                                                        <br>
                                                        <label>Detail Lokasi</label>
                                                        <input type="text" class="form-control" value="<?php echo $b['detail']; ?>" name="detail" required>
                                                             <br>
                                                        <label>Tanggal Pemeriksaan</label>
                                                        <input type="date" class="form-control" value="<?php echo $b['tgl_hasil']; ?>" name="tgl_hasil" required>
                                                             <br>
                                                        <label>Aktifitas</label>
                                                        <select class="form-control select2" name="id_kegiatan"  value="<?php echo $b['id_kegiatan']; ?>" style="width: 100%;" required>
                                                        <option selected="selected" value="3">Rekomendasi Kesehatan Lingkungan Unit</option>               
                                                         </select>
                                                         <br>
                                                         <br>
                                                        <label>Jenis</label>
                                                        <select class="form-control select2" name="jenis"  data-placeholder="<?php echo $b['jenis']; ?>" style="width: 100%;" required>
                                                        <option selected="selected" value="<?php echo $b['jenis']; ?>"><?php echo $b['jenis']; ?></option>
                                                        <option value="Air Bersih Mikrobiologi">  Air Bersih Mikrobiologi</option>
                                                        <option value="Air Bersih Kimia">  Air Bersih Kimia</option> 
                                                        <option value="Air Minum Mikrobiologi">  Air Minum Mikrobiologi</option> 
                                                        <option value="Air Minum Kimia">  Air Minum Kimia</option> 
                                                        <option value="Air Limbah Mikrobiologi">  Air Limbah Mikrobiologi</option> 
                                                        <option value="Air Limbah Kimia">  Air Limbah Kimia</option> 
                                                        <option value="Cemaran Udara">  Cemaran Udara</option> 
                                                        <option value="SWAB Dinding">  SWAB Dinding</option>
                                                        <option value="SWAB Lantai">  SWAB Lantai</option>
                                                        <option value="SWAB AC">  SWAB AC</option>
                                                        <option value="Udara Ambient">  Udara Ambient</option> 
                                                        <option value="Uji Emisi">  Uji Emisi</option> 
                                                        <option value="Pencahayaan">  Pencahayaan</option> 
                                                        <option value="Kebisingan">  Kebisingan</option> 
                                                        <option value="Makanan Matang">  Makanan Matang</option> 
                                                        <option value="Makanan Mentah">  Makanan Mentah</option> 
                                                        <option value="Alat Makan Pasien">  Alat Makan Pasien</option> 
                                                        <option value="Alat Medis">  Alat Medis</option>
                                                        </select>
                                                        <br>
                                                        <br>
                                                        <label>Hasil</label>
                                                        <input type="text" class="form-control" name="hasil" value="<?php echo $b['hasil']; ?>" required>
                                                        <br>
                                                        <label>Standar</label>
                                                        <input type="text" class="form-control" name="standar" value="<?php echo $b['standar']; ?>" required>
                                                        <br>
                                                        <label>Kesimpulan</label>
                                                        <select class="form-control select2" name="kesimpulan"   style="width: 100%;" required>
                                                        <option selected="selected" value="<?php echo $b['kesimpulan']; ?>"><?php echo $b['kesimpulan']; ?>
                                                        <option value="Memenuhi Syarat">Memenuhi Syarat
                                                        <option value="Tidak Memenuhi Syarat">Tidak Memenuhi Syarat
                                                        </option>              
                                                        </select>
                                                        <br>
                                                        <br>
                                                        <label>Rekomendasi Kesling</label>
                                                        <input type="text" class="form-control" name="r_kesling" value="<?php echo $b['r_kesling']; ?>" required>
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
                            <div class="modal fade" id="modal-hapus-<?php echo $b['id_kesling']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksidel . paramEncrypt('module=kesling&act=delete-kesling&id=' . $b['id_kesling'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Delete Hasil</h4>
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
                                        <td><?php echo $b['nama_unit'] ?></td>
                                        <td><?php echo $b['detail'] ?></td>
                                        <td><?php echo $b['tgl_hasil'] ?></td>
                                        <td><?php echo $b['nama_kegiatan'] ?></td>
                                        <td><?php echo $b['jenis'] ?></td>
                                        <td><?php echo $b['hasil'] ?></td>
                                        <td><?php echo $b['standar'] ?></td>
                                        <td><?
                                        if ($b['kesimpulan']=='Memenuhi Syarat') {
                                        echo '<font color="green">'.$b['kesimpulan'].'</p>';
                                        }else{
                                        echo '<font color="red">'.$b['kesimpulan'].'</p>';
                                        }
                                        ?></td>
                                        <td><?php echo $b['r_kesling'] ?></td>
                                        <td><?php echo $b['r_ppi'] ?></td>
                                        <td><?php echo $b['t_unit'] ?></td>
                                        <td><spain data-toggle="modal" data-target="#modal-ubah-<?php echo $b['id_kesling']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"/></spain>
                                            <spain data-toggle="modal" data-target="#modal-hapus-<?php echo $b['id_kesling']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"/></spain>

                                        </td>
                                    </tr>
                                    <?php $no++; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
        break;

}
?>


