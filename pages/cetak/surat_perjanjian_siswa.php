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
        $cek_data = mysqli_query($conn, "SELECT id_pelanggaran_siswa, perjanjian_siswa.tanggal FROM perjanjian_siswa JOIN pelanggaran_siswa USING(id_pelanggaran_siswa) WHERE nis = '$nis' AND id_pelanggaran_siswa = '$id_pelanggaran_siswa[id_pelanggaran_siswa]'");
        if(mysqli_num_rows($cek_data) == 0){
            echo "<script>alert('Siswa melakukan pelanggaran baru, silahkan cetak surat lagi, dengan memilih nis " . $nis . "'); window.location.href = '<?= BASE_URL ?>/pages/cetak/add_perjanjian_siswa.php';</script>";
            exit;
        }
    }
    
}else{
    $nis = $_POST['nis'];


    // mengambil nis siswa, nama orang tua, pekerjaan orang tua, alamat orang tua, dan nomor telepon orang tua dari file add_perjanjian_siswa.php (dikirim dari file add_perjanjian_siswa.php menggunakan method POST)
    $nis = $_POST['nis'];
    $nama_ortu = $_POST['nama_ortu'];
    $pekerjaan = $_POST['pekerjaan'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];



    if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pelanggaran_siswa WHERE nis = '$nis'")) == 0){
        echo "<script>alert('Data siswa belum memiliki data pelanggaran 👍'); window.location.href = '<?= BASE_URL ?>/pages/cetak/add_perjanjian_siswa.php';</script>";
        exit;
    }


    // update data ortu/wali jika tidak ada di database atau data baru diinput
    $orang_tua = $_POST['orang_tua'];
    $id_ortu_wali = $_POST['id_ortu_wali'];
    if($orang_tua == "ayah"){
        $update_ortu = mysqli_query($conn, "UPDATE ortu_wali SET ayah = '$nama_ortu', pekerjaan_ayah = '$pekerjaan', alamat_ayah = '$alamat', no_telp_ayah = '$no_telp' WHERE id_ortu_wali = '$id_ortu_wali'");
    }else if($orang_tua == "ibu"){
        $update_ortu = mysqli_query($conn, "UPDATE ortu_wali SET ibu = '$nama_ortu', pekerjaan_ibu = '$pekerjaan', alamat_ibu = '$alamat', no_telp_ibu = '$no_telp' WHERE id_ortu_wali = '$id_ortu_wali'");
    }else{
        $update_ortu = mysqli_query($conn, "UPDATE ortu_wali SET wali = '$nama_ortu', pekerjaan_wali = '$pekerjaan', alamat_wali = '$alamat', no_telp_wali = '$no_telp' WHERE id_ortu_wali = '$id_ortu_wali'");
    }









    // Set zona waktu ke WITA (waktu Bali)
    date_default_timezone_set('Asia/Makassar'); 
    $tanggal = date("Y-m-d H:i:s");

    // mengambil data tingkat kelas dan nama wali kelas
    $data_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT tingkat, nama_pengguna FROM siswa JOIN kelas USING(id_kelas) JOIN tingkat USING(id_tingkat) JOIN guru USING(kode_guru) WHERE nis = '$nis'"));
    $tingkat = $data_siswa['tingkat'];
    $wali_kelas = $data_siswa['nama_pengguna'];

    // mengambil data guru bimbingan konseling berdasarkan tingkat siswa
    if($tingkat == 'XII'){
        $query_bk = mysqli_query($conn, "SELECT nama_pengguna FROM guru WHERE jabatan = 'Guru BK XII' AND aktif = 'Y'");
    }else if($tingkat == 'XI'){
        $query_bk = mysqli_query($conn, "SELECT nama_pengguna FROM guru WHERE jabatan = 'Guru BK XI' AND aktif = 'Y'");
    }else{
        $query_bk = mysqli_query($conn, "SELECT nama_pengguna FROM guru WHERE jabatan = 'Guru BK X' AND aktif = 'Y'");
    }
    $guru_bk = mysqli_fetch_assoc($query_bk)['nama_pengguna'];

    // mengambil data wakasek kesiswaan dari database
    $waka_kesiswaan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_pengguna FROM guru WHERE jabatan = 'Waka Kesiswaan' AND aktif = 'Y'"))['nama_pengguna'];









    // insert data ke database tabel perjanjian_siswa
    $pelanggaran_siswa = mysqli_query($conn, "SELECT id_pelanggaran_siswa FROM pelanggaran_siswa WHERE nis = '$nis'");
    while($id_pelanggaran_siswa = mysqli_fetch_assoc($pelanggaran_siswa)){

        // cek apakah data sudah ada
        $cek_data = mysqli_query($conn, "SELECT id_pelanggaran_siswa, perjanjian_siswa.tanggal FROM perjanjian_siswa JOIN pelanggaran_siswa USING(id_pelanggaran_siswa) WHERE nis = '$nis' AND id_pelanggaran_siswa = '$id_pelanggaran_siswa[id_pelanggaran_siswa]'");
        if(mysqli_num_rows($cek_data) > 0){
            continue;
        }else{
            $insert_perjanjian = mysqli_query($conn, "INSERT INTO perjanjian_siswa VALUES (NULL, '$tanggal', '$id_pelanggaran_siswa[id_pelanggaran_siswa]', 'Masih Proses', NULL, '$tingkat', '$nama_ortu', '$pekerjaan', '$alamat', '$no_telp', '$wali_kelas', '$guru_bk', '$waka_kesiswaan')");
        }
    }

}







// mengambil data siswa dari database untuk menampilkan di surat perjanjian siswa di bawah ini
$query_perjanjian = mysqli_query($conn, "SELECT *, perjanjian_siswa.tanggal as tanggal_surat, tingkat.tingkat as tingkat_perjanjian FROM perjanjian_siswa
JOIN pelanggaran_siswa USING(id_pelanggaran_siswa)
JOIN siswa USING(nis)
JOIN kelas USING(id_kelas)
JOIN tingkat USING(id_tingkat)
JOIN program_keahlian USING(id_program_keahlian)
JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
WHERE nis = '$nis' ORDER BY id_perjanjian_siswa DESC ");
$row_siswa = mysqli_fetch_assoc($query_perjanjian);



// buat array bulan (berfungsi untuk mengubah angka bulan menjadi nama bulan, contoh : 2 menjadi Februari)
$bulan_indo = ["", "Januari", "Pebruari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];


// mengubah format tanggal dari database menjadi format tanggal bulan indonesia
$tanggal = explode(" ", $row_siswa['tanggal_surat']);
$tanggal = explode("-", $tanggal[0]);
$tanggal_cetak_perjanjian = $tanggal[2] ." ". $bulan_indo[(int)$tanggal[1]] . " " . $tanggal[0];







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
</style>










<!-- tombol kembali -->
<center class="no-print">
    
    <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
        <!-- tombol ini berfungsi untuk kembali ke halaman add_perjanjian_siswa.php dan mengirimkan nis yang sudah di cek menggunakan method post -->
        <?php
        if(isset($_GET['nis'])){
        ?>
            <form action="<?= BASE_URL ?>/pages/laporan/list_perjanjian.php" style="margin: 0;">
                <button type="submit">
                    <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024">
                        <path d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"></path>
                    </svg>
                    <span>Kembali</span>
                </button>
            </form>
        <?php
        }else{
        ?>
        <form action="add_perjanjian_siswa.php" method="post" style="margin: 0;">
            <input type="text" name="nis" value="<?= $nis ?>" hidden>
            <button type="submit">
                <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024">
                    <path d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"></path>
                </svg>
                <span>Kembali</span>
            </button>
        </form>
        <?php
        }
        ?>

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
        <!-- menampilkan gambar kop surat dari folder gambar-->
        <img src="<?= BASE_URL ?>/images/kop.jpg" alt="kepala surat" width="100%">
    </div>

    <div class="title">SURAT PERNYATAAN SISWA</div>

    <div class="content">
        <p>Yang bertandatangan di bawah ini :</p>
        
        <div class="indent">
            <div class="form-row">
                <div class="label">Nama</div>
                <div class="separator">:</div>
                <!-- menampilkan nama siswa dari hasil query database line: 16-->
                <div class="field"><?php echo $row_siswa['nama_siswa']; ?></div>
            </div>
            <div class="form-row">
                <div class="label">NIS</div>
                <div class="separator">:</div>
                <!-- menampilkan nis siswa dari hasil query database line: 16-->
                <div class="field"><?php echo $row_siswa['nis']; ?></div>
            </div>
            <div class="form-row">
                <div class="label">Kelas</div>
                <div class="separator">:</div>
                <!-- menampilkan kelas siswa dari hasil query database line: 16-->
                <div class="field"><?php echo $row_siswa['tingkat_perjanjian'] . ' ' . $row_siswa['program_keahlian'] . ' ' . $row_siswa['rombel'] ?></div>
            </div>
            <div class="form-row">
                <div class="label">Program Keahlian</div>
                <div class="separator">:</div>
                <!-- menampilkan program keahlian siswa dari hasil query database line: 16-->
                <div class="field"><?php echo $row_siswa['deskripsi']; ?></div>
            </div>
            <div class="form-row">
                <div class="label">Masalah</div>
                <div class="separator">:</div>
                <!-- menampilkan masalah siswa dari database tabel masalah -->
                <div class="field-masalah">
                <?php
                // 1. Ambil data jenis pelanggaran siswa dari database (gunakan DISTINCT agar jenis yang sama hanya tampil 1x)
                $query_pelanggaran = mysqli_query($conn, "SELECT DISTINCT jenis FROM pelanggaran_siswa JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) WHERE nis = '$nis'");
                
                // 2. Siapkan tempat penampungan (array) kosong untuk menyimpan daftar nama pelanggaran
                $daftar_pelanggaran = [];
                
                // 3. Ambil data satu per satu dan masukkan ke tempat penampungan
                while($data_pelanggaran = mysqli_fetch_assoc($query_pelanggaran)){
                    // htmlspecialchars digunakan untuk keamanan agar teks aman saat ditampilkan
                    $daftar_pelanggaran[] = htmlspecialchars($data_pelanggaran['jenis']);
                }
                
                // 4. Jika daftar pelanggaran ada (tidak kosong), maka tampilkan ke layar
                if(!empty($daftar_pelanggaran)){
                    // Gabungkan semua pelanggaran dengan koma dan spasi, lalu akhiri dengan tanda titik
                    echo implode(', ', $daftar_pelanggaran) . '.';
                }
                ?></div>
            </div>
        </div>

        <div class="indent">
            <div class="form-row">
                <div class="label">Nama Orang Tua</div>
                <div class="separator">:</div>
                <!-- menampilkan nama orang tua dari halaman add_perjanjian_siswa line : 10 -->
                <div class="field"><?=$row_siswa['nama_ortu']?></div>
            </div>
            <div class="form-row">
                <div class="label">Pekerjaan</div>
                <div class="separator">:</div>
                <!-- menampilkan pekerjaan orang tua dari halaman add_perjanjian_siswa line : 11 -->
                <div class="field"><?=$row_siswa['pekerjaan_ortu']?></div>
            </div>
            <div class="form-row">
                <div class="label">Alamat Rumah</div>
                <div class="separator">:</div>
                <!-- menampilkan alamat orang tua dari halaman add_perjanjian_siswa line : 12 -->
                <div class="field"><?=$row_siswa['alamat_ortu']?></div>
            </div>
            <div class="form-row">
                <div class="label">No. Hp./Telp.</div>
                <div class="separator">:</div>
                <!-- menampilkan no. hp orang tua dari halaman add_perjanjian_siswa line : 13 -->
                <div class="field"><?=$row_siswa['no_telp_ortu']?></div>
            </div>
        </div>

        <p class="statement">
            Menyatakan dan berjanji akan bersungguh-sungguh berubah dan bersedia mentaati aturan dan tata tertib sekolah. 
            Apabila selama masa pembinaan tidak mengalami perubahan, maka siswa yang bersangkutan dikembalikan kepada orang tua/wali. <br>
            Demikian surat pernyataan ini saya buat dengan sesungguhnya tanpa ada tekanan dari siapapun.
        </p>

        <div class="signature-section">
            <div class="sig-block">
                <div>Mengetahui,</div>
                <div>Orang Tua/Wali siswa</div>
                <!-- menampilkan nama orang tua dari halaman add_perjanjian_siswa -->
                <div class="sig-name-plain"><?= $row_siswa['nama_ortu'] ?></div>
            </div>
            <div class="sig-block sig-right">
                <!-- menampilkan tanggal hari cetak surat menggunakan format tanggal indonesia -->
                <div>Denpasar, <?php echo $tanggal_cetak_perjanjian; ?></div>
                <div>Siswa yang bersangkutan</div>
                <!-- menampilkan nama siswa dari hasil query database -->
                <div class="sig-name-plain"><?php echo $row_siswa['nama_siswa']; ?></div>
            </div>

            <div class="sig-block">
                <div>Guru Bimbingan Konseling</div>
                <!-- menampilkan nama guru bimbingan konseling dari hasil query database -->
                <div class="sig-name" style="margin-top: 70px; border: none; text-decoration: underline;">
                    <?= $row_siswa['guru_bk'] ?>
                </div>
            </div>
            <div class="sig-block sig-right">
                <div>Guru Wali Kelas</div>
                <!-- menampilkan nama guru wali kelas dari hasil query database -->
                <div class="sig-name" style="margin-top: 70px;"><?=$row_siswa['wali_kelas'] ?></div>
            </div>
        </div>

        <div class="footer-sig">
            <div>Mengetahui</div>
            <div>Wakasek Kesiswaan</div>
            <div class="sig-name">
                <!-- menampilkan nama wakasek kesiswaan dari hasil query database -->
                <?= $row_siswa['wakasek_kesiswaan'] ?>
            </div>
        </div>

    </div>
</div>










<script>
    // Menyertakan bagian footer (penutup halaman)
    window.onload = function() {
        window.print();
    }
</script>
<?php 
// Menyertakan bagian footer (penutup halaman)
include "../../includes/footer.php"; 
?>









<!-- penjelasan kode :
  File ini menerima data dari form add_perjanjian_siswa.php, menyimpan data surat perjanjian baru ke database (hanya yang belum pernah ada), lalu langsung mencetak surat perjanjian siswa ke layar lengkap dengan nama siswa, orang tua, guru, wakasek kesiswaan, dan tanggal dalam format Indonesia. -->
