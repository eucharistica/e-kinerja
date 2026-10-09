<?php
require_once('../../conf/conf.php');
require_once('../../libs/aes-encrypt/function.php');
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$page = isset($url['module']) ? $url['module'] : null; 
$action = isset($url['act']) ? $url['act'] : null;
$id = isset($url['id']) ? $url['id'] : null;


include('../../conf.php');
function antiinjection($data){
      $filter_sql = stripslashes(strip_tags(htmlspecialchars($data,ENT_QUOTES)));
      return $filter_sql;
      }
//menerima nilai dari kiriman form input-barang 
        $id_user = antiinjection($_POST['id_user']);
        $tgl_hasil = antiinjection($_POST['tgl_hasil']);
        $jenis = antiinjection($_POST['jenis']);
        $hasil = antiinjection($_POST['hasil']);
        $id_kegiatan = antiinjection($_POST['id_kegiatan']);
        $standar = antiinjection($_POST['standar']);
        $kesimpulan = antiinjection($_POST['kesimpulan']);
        $r_kesling = antiinjection($_POST['r_kesling']);
        $detail = antiinjection($_POST['detail']);


$sql="insert into tt_kesling (id_kegiatan,id_user,tgl_hasil,jenis,hasil,standar,kesimpulan,r_kesling,detail) values
    ('$id_kegiatan','$id_user','$tgl_hasil','$jenis','$hasil','$standar','$kesimpulan','$r_kesling','$detail')";

//Mengeksekusi/menjalankan query diatas 
  $hasil=mysqli_query($kon,$sql);

//Kondisi apakah berhasil atau tidak
  if ($hasil) {
  header('location:../../page-view?' . paramEncrypt('module=kesling&act=list-data-kesling'));
  }
else {
  echo "Gagal insert data";
  exit;
}  

?>