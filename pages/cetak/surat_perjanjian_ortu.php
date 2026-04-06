<?php
include_once __DIR__ . '/../../config/config.php';
// Menentukan lokasi root folder proyek di server


// Menghubungkan ke file konfigurasi (koneksi database)
?>

<style>
    
/* CSS Print untuk memastikan tampilan cetak sama dengan web */
@media print {
    /* Reset margin dan padding */
    body {
        margin: 0;
        padding: 0;
        background: white !important;
    }
    
    /* Sembunyikan sidebar dan elemen tidak perlu */
    .sidebar {
        display: none !important;
    }
    
    /* Atur main content untuk print */
    main {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    
    /* Atur page untuk print */
    .page {
        width: 100% !important;
        min-height: auto !important;
        margin: 0 !important;
        padding: 20px !important;
        box-shadow: none !important;
        border: none !important;
        background: white !important;
    }
    
    /* Pastikan header tercetak dengan benar */
    .header img {
        width: 100% !important;
        height: auto !important;
    }
    
    /* Atur typography untuk print */
    .title {
        text-align: center !important;
        font-weight: bold !important;
        font-size: 14pt !important;
        text-transform: uppercase !important;
        margin-bottom: 20px !important;
    }
    
    /* Atur tabel untuk print */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin: 10px 0 !important;
    }
    
    table th, table td {
        border: 1px solid black !important;
        padding: 8px !important;
        text-align: left !important;
    }
    
    table th {
        background-color: #f5f5f5 !important;
        font-weight: bold !important;
        text-align: center !important;
    }
    
    /* Atur form rows untuk print */
    .form-row {
        display: flex !important;
        margin: 5px 0 !important;
    }
    
    .label {
        width: 160px !important;
        flex-shrink: 0 !important;
        font-weight: bold !important;
    }
    
    .separator {
        width: 10px !important;
        flex-shrink: 0 !important;
    }
    
    .field {
        flex-grow: 1 !important;
        border-bottom: 1px dotted black !important;
        position: relative !important;
        top: -5px !important;
    }
    
    /* Sembunyikan tombol dan elemen interaktif */
    .no-print {
        display: none !important;
    }
    
    /* Pastikan indent tetap */
    .indent {
        padding-left: 30px !important;
    }
    
    /* Atur spacing untuk print */
    br {
        line-height: 1.2 !important;
    }
    
    /* Pastikan content tercetak dengan benar */
    .content {
        text-align: justify !important;
    }
}
</style>
<?php


if(isset($_GET['nis'])){
    $nis = $_GET['nis'];


    $pelanggaran_siswa = mysqli_query($conn, "SELECT id_pelanggaran_siswa FROM pelanggaran_siswa WHERE nis = '$nis'");
    while($id_pelanggaran_siswa = mysqli_fetch_assoc($pelanggaran_siswa)){

        // cek apakah data sudah ada
        $cek_data = mysqli_query($conn, "SELECT id_pelanggaran_siswa, perjanjian_orang_tua.tanggal FROM perjanjian_orang_tua JOIN pelanggaran_siswa USING(id_pelanggaran_siswa) WHERE nis = '$nis' AND id_pelanggaran_siswa = '$id_pelanggaran_siswa[id_pelanggaran_siswa]'");
        if(mysqli_num_rows($cek_data) == 0){
            echo "<script>alert('Siswa melakukan pelanggaran baru, silahkan cetak surat lagi, dengan memilih nis " . $nis . "'); window.location.href = '<?= BASE_URL ?>/pages/cetak/add_perjanjian_ortu.php';</script>";
            exit;
        }
    }


}else{
    // mengambil nis siswa, nama orang tua, tempat lahir orang tua, pekerjaan orang tua, alamat orang tua, dan nomor telepon orang tua dari file add_perjanjian_siswa.php (dikirim dari file add_perjanjian_siswa.php menggunakan method POST)
    $nis = $_POST['nis'];
    $nama_ortu = $_POST['nama_ortu'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $pekerjaan = $_POST['pekerjaan'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $id_ortu_wali = $_POST['id_ortu_wali'];
    $orang_tua = $_POST['orang_tua'];


    if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pelanggaran_siswa WHERE nis = '$nis'")) == 0){
        echo "<script>alert('Data siswa belum memiliki data pelanggaran 👍'); window.location.href = '<?= BASE_URL ?>/pages/cetak/add_perjanjian_ortu.php';</script>";
        exit;
    }



    // update data ortu/wali jika tidak ada di database atau data baru diinput
    $orang_tua = $_POST['orang_tua'];
    $id_ortu_wali = $_POST['id_ortu_wali'];
    if($orang_tua == "ayah"){
        $update_ortu = mysqli_query($conn, "UPDATE ortu_wali SET ayah = '$nama_ortu', pekerjaan_ayah = '$pekerjaan', alamat_ayah = '$alamat', no_telp_ayah = '$no_telp', tanggal_lahir_ayah = '$tanggal_lahir', tempat_lahir_ayah = '$tempat_lahir' WHERE id_ortu_wali = '$id_ortu_wali'");
    }else if($orang_tua == "ibu"){
        $update_ortu = mysqli_query($conn, "UPDATE ortu_wali SET ibu = '$nama_ortu', pekerjaan_ibu = '$pekerjaan', alamat_ibu = '$alamat', no_telp_ibu = '$no_telp', tanggal_lahir_ibu = '$tanggal_lahir', tempat_lahir_ibu = '$tempat_lahir' WHERE id_ortu_wali = '$id_ortu_wali'");
    }else{
        $update_ortu = mysqli_query($conn, "UPDATE ortu_wali SET wali = '$nama_ortu', pekerjaan_wali = '$pekerjaan', alamat_wali = '$alamat', no_telp_wali = '$no_telp', tanggal_lahir_wali = '$tanggal_lahir', tempat_lahir_wali = '$tempat_lahir' WHERE id_ortu_wali = '$id_ortu_wali'");
    }








    // Set zona waktu ke WITA (waktu Bali)
    date_default_timezone_set('Asia/Makassar'); 
    $tanggal = date("Y-m-d H:i:s");

    // mengambil data tingkat kelas
    $data_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT tingkat FROM siswa JOIN kelas USING(id_kelas) JOIN tingkat USING(id_tingkat) WHERE nis = '$nis'"));
    $tingkat = $data_siswa['tingkat'];










    // insert data ke database tabel perjanjian_orang_tua
    $pelanggaran_siswa = mysqli_query($conn, "SELECT id_pelanggaran_siswa FROM pelanggaran_siswa WHERE nis = '$nis'");
    while($id_pelanggaran_siswa = mysqli_fetch_assoc($pelanggaran_siswa)){

        // cek apakah data sudah ada
        $cek_data = mysqli_query($conn, "SELECT id_pelanggaran_siswa, perjanjian_orang_tua.tanggal FROM perjanjian_orang_tua JOIN pelanggaran_siswa USING(id_pelanggaran_siswa) WHERE nis = '$nis' AND id_pelanggaran_siswa = '$id_pelanggaran_siswa[id_pelanggaran_siswa]'");
        if(mysqli_num_rows($cek_data) > 0){
            continue;
        }else{
            $insert_perjanjian = mysqli_query($conn, "INSERT INTO perjanjian_orang_tua VALUES (NULL, '$tanggal', '$id_pelanggaran_siswa[id_pelanggaran_siswa]', 'Masih Proses', NULL, '$tingkat', '$nama_ortu', '$pekerjaan', '$alamat', '$no_telp')");
        }
    }
}






// mengambil data siswa dari database untuk menampilkan di surat perjanjian orang tua/wali di bawah ini
$query_perjanjian = mysqli_query($conn, "SELECT perjanjian_orang_tua.tanggal, perjanjian_orang_tua.nama_ortu, tanggal_lahir_ayah, tanggal_lahir_ibu, tanggal_lahir_wali, tempat_lahir_ayah, tempat_lahir_ibu, tempat_lahir_wali, perjanjian_orang_tua.pekerjaan_ortu, perjanjian_orang_tua.alamat_ortu, perjanjian_orang_tua.no_telp_ortu, siswa.nama_siswa, siswa.nis, kelas.rombel, program_keahlian.program_keahlian, tingkat.tingkat, id_ortu_wali FROM perjanjian_orang_tua
JOIN pelanggaran_siswa USING(id_pelanggaran_siswa)
JOIN siswa USING(nis)
JOIN ortu_wali USING(id_ortu_wali)
JOIN kelas USING(id_kelas)
JOIN tingkat USING(id_tingkat)
JOIN program_keahlian USING(id_program_keahlian)
WHERE nis = '$nis' ORDER BY id_perjanjian_ortu DESC");
$row_ortu_wali = mysqli_fetch_assoc($query_perjanjian);

// Tentukan jenis orang tua berdasarkan data yang ada
$id_ortu_wali = $row_ortu_wali['id_ortu_wali'];
$ortu_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ayah, ibu, wali FROM ortu_wali WHERE id_ortu_wali = '$id_ortu_wali'"));

if(!empty($ortu_data['ayah']) && $ortu_data['ayah'] == $row_ortu_wali['nama_ortu']) {
    $orang_tua = 'ayah';
} elseif(!empty($ortu_data['ibu']) && $ortu_data['ibu'] == $row_ortu_wali['nama_ortu']) {
    $orang_tua = 'ibu';
} else {
    $orang_tua = 'wali';
}


// buat array bulan (berfungsi untuk mengubah angka bulan menjadi nama bulan, contoh : 2 menjadi Februari)
$bulan_indo = ["", "Januari", "Pebruari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];


// mengubah format tanggal dari database menjadi format tanggal bulan indonesia
$tanggal = explode(" ", $row_ortu_wali['tanggal']);
$tanggal = explode("-", $tanggal[0]);
$tanggal_cetak_perjanjian = $tanggal[2] ." ". $bulan_indo[(int)$tanggal[1]] . " " . $tanggal[0];


// hitung tanggal 3 bulan dari sekarang
$tgl_target = strtotime("+3 months");
// ambil 3 bulan dari sekarang
$bulan = $bulan_indo[date("n", $tgl_target)] . " " . date("Y", $tgl_target);




//ubah format tanggal lahir
$id_ortu_wali = $row_ortu_wali['id_ortu_wali'];
$tanggal_lahir = mysqli_fetch_assoc(mysqli_query($conn, "SELECT tanggal_lahir_$orang_tua AS tanggal_lahir FROM ortu_wali WHERE id_ortu_wali = '$id_ortu_wali'"))['tanggal_lahir'];
$tanggal_lahir = explode("-", $tanggal_lahir);
$tanggal_lahir_ortu = $tanggal_lahir[2] ." ". $bulan_indo[(int)$tanggal_lahir[1]] . " " . $tanggal_lahir[0];

// Menyertakan tampilan header (bagian atas halaman)
include ROOT_PATH . "/includes/header.php";

?>






<style>
button {
 display: flex;
 height: 3em;
 align-items: center;
 justify-content: center;
 background-color: #eeeeee4b;
 border-radius: 3px;
 letter-spacing: 1px;
 transition: all 0.2s linear;
 cursor: pointer;
 border: none;
 background: #fff;
}
button > svg {
 margin-right: 5px;
 margin-left: 5px;
 font-size: 20px;
 transition: all 0.4s ease-in;
}
button:hover > svg {
 font-size: 1.2em;
 transform: translateX(-5px);
}
button:hover {
 box-shadow: 9px 9px 33px #d1d1d1, -9px -9px 33px #ffffff;
 transform: translateY(-2px);
}
/* animasi icon printer */ 
.printer-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 100%;
}
.printer-container {
  height: 50%;
  width: 100%;
  display: flex;
  align-items: flex-end;
  justify-content: center;
}
.printer-container svg {
  width: 100%;
  height: auto;
  transform: translateY(4px);
}
.printer-page-wrapper {
  width: 100%;
  height: 50%;
  display: flex;
  align-items: flex-start;
  justify-content: center;
}
.printer-page {
  width: 70%;
  height: 10px;
  border: 1px solid black;
  background-color: white;
  transform: translateY(0px);
  transition: all 0.3s;
  transform-origin: top;
}
.print-btn:hover .printer-page {
  height: 16px;
}
/* animasi icon printer */

.statement b{
    text-decoration: underline 1px dotted black;
    text-underline-offset: 5px;
}
</style>











<!-- tombol kembali -->
<center class="no-print">
    
    <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
        <!-- tombol ini berfungsi untuk kembali ke halaman add_perjanjian_ortu.php dan mengirimkan nis yang sudah di cek menggunakan method post -->
        <form action="add_perjanjian_ortu.php" method="post" style="margin: 0;">
            <input type="text" name="nis" value="<?= $nis ?>" hidden>
            <button type="submit">
                <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024">
                    <path d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"></path>
                </svg>
                <span>Kembali</span>
            </button>
        </form>

        <!-- tombol ini berfungsi untuk print halaman ini -->
        <button class="print-btn" onclick="window.print()">
            <span class="printer-wrapper">
                <span class="printer-container">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 92 75">
                        <path stroke-width="5" stroke="black" d="M12 37.5H80C85.2467 37.5 89.5 41.7533 89.5 47V69C89.5 70.933 87.933 72.5 86 72.5H6C4.067 72.5 2.5 70.933 2.5 69V47C2.5 41.7533 6.75329 37.5 12 37.5Z"></path>
                        <mask fill="white" id="path-2-inside-1_30_7">
                            <path d="M12 12C12 5.37258 17.3726 0 24 0H57C70.2548 0 81 10.7452 81 24V29H12V12Z"></path>
                        </mask>
                        <path mask="url(#path-2-inside-1_30_7)" fill="black" d="M7 12C7 2.61116 14.6112 -5 24 -5H57C73.0163 -5 86 7.98374 86 24H76C76 13.5066 67.4934 5 57 5H24C20.134 5 17 8.13401 17 12H7ZM81 29H12H81ZM7 29V12C7 2.61116 14.6112 -5 24 -5V5C20.134 5 17 8.13401 17 12V29H7ZM57 -5C73.0163 -5 86 7.98374 86 24V29H76V24C76 13.5066 67.4934 5 57 5V-5Z"></path>
                        <circle fill="black" r="3" cy="49" cx="78"></circle>
                    </svg>
                </span>
                <span class="printer-page-wrapper"><span class="printer-page"></span></span>
            </span>
            <span>&nbsp;&nbsp;Cetak Lagi</span>
        </button>
    </div>
    
</center>






<div class="page">
    <!-- Header -->
    <div class="header">
        <img src="<?= BASE_URL ?>/images/kop.jpg" alt="kepala surat" width="100%">
    </div>

    <div class="title">SURAT PERNYATAAN ORANG TUA</div>
    <br> 
    <div class="content">
        <p>Yang bertandatangan di bawah ini orang tua/wali siswa SMK TI Bali Global Denpasar :</p>
        
        <div class="indent">
            <div class="form-row">
                <div class="label">Nama</div>
                <div class="separator">:</div>
                <div class="field"><?=$row_ortu_wali['nama_ortu']?></div>
            </div>
            <div class="form-row">
                <div class="label">Tempat/ tanggal Lahir</div>
                <div class="separator">:</div>
                <div class="field"><?=$row_ortu_wali['tempat_lahir_'.$orang_tua]?>/ <?=$tanggal_lahir_ortu?></div>
            </div>
            <div class="form-row">
                <div class="label">Pekerjaan</div>
                <div class="separator">:</div>
                <div class="field"><?=$row_ortu_wali['pekerjaan_ortu']?></div>
            </div>
            <div class="form-row">
                <div class="label">Alamat Rumah</div>
                <div class="separator">:</div>
                <div class="field"><?=$row_ortu_wali['alamat_ortu']?></div>
            </div>
            <div class="form-row">
                <div class="label">No. Hp./Telp.</div>
                <div class="separator">:</div>
                <div class="field"><?=$row_ortu_wali['no_telp_ortu']?></div>
            </div>
        </div>
        <br> 
        <br> 

        <!-- menampilkan isi surat perjanjian orang tua/wali dengan menampilkan data siswa dari hasil query database -->
        <p class="statement">
            Menyatakan memang benar sanggup membina anak kami yang bernama <b><?php echo $row_ortu_wali['nama_siswa']; ?></b>, Kelas : <b><?php echo $row_ortu_wali['tingkat'] . ' ' . $row_ortu_wali['program_keahlian'] . ' ' . $row_ortu_wali['rombel'] ?></b> untuk lebih disiplin mengikuti proses pembelajaran dan mengikuti Tata Tertib Sekolah. <br><br>
            Demikian pernyataan kami dan jika tidak sesuai dengan pernyataan diatas, anak kami dapat dikeluarkan dari sekolah ini dengan rekomendasi pindah ke SMK lain yang serumpun.
        </p>

        <div class="signature-section">
            <div class="sig-block"></div>
            <div class="sig-block sig-right">
                <!-- menampilkan tanggal hari ini dengan menggunakan bulan bahasa indonesia -->
                <div>Denpasar, <?php echo $tanggal_cetak_perjanjian ?></div>
                <div>
                    Yang membuat pernyataan<br> 
                    Orang Tua/Wali siswa
                </div>
                <!-- menampilkan nama orang tua/wali -->
                <div class="sig-name-plain"><?=$row_ortu_wali['nama_ortu']?></div>
            </div>
        </div>

        <div class="indent">
            <div class="form-row">
                <div><u>NB : <br>
                    <!-- menampilkan bulan hari ini + 3 bulan kedepan -->
                    Jika siswa tidak bisa mengikuti proses pembelajaran sampai bulan <?=$bulan?> maka <br>
                    Siswa dinyatakan mengundurkan diri.</u>
                </div>
            </div>
        </div>
    </div>
</div>






<script>
    // ketika halaman selesai loading maka halaman akan otomatis di print
    window.onload = function() {
        window.print();
    }
</script>
<?php 
// Menyertakan bagian footer (penutup halaman)
include "../../includes/footer.php"; 
?>





<!-- penjelasan kode :
  File ini menerima data dari form add_perjanjian_ortu.php, menyimpan data surat perjanjian baru ke database (hanya yang belum pernah ada), lalu langsung mencetak surat perjanjian orang tua/wali ke layar lengkap dengan nama siswa, orang tua, dll. -->
