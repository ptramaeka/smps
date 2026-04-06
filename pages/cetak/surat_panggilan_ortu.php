<?php
include_once __DIR__ . '/../../config/config.php';
// Menentukan lokasi root folder proyek di server

smps_require_login();
smps_require_roles(['admin', 'bk'], 'Akses ditolak. Halaman cetak hanya untuk admin/BK.');


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
$nis = $_POST['nis'];

if(isset($_GET['no_surat'])){
    $no_surat = $_GET['no_surat'];
}else{
    $no_surat = $_POST['no_surat'];
    

    // ubah format bulan menjadi romawi (untuk bagian no surat)
    $bulan_romawi = ["", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
    $bulan_romawi = $bulan_romawi[date("n")];
    $no_surat = $no_surat . "/SMK TI/BG/" . $bulan_romawi . "/" . date("Y");
    
    
    

    // cek apakah data sudah ada di tabel surat_keluar
    $cek_data = mysqli_query($conn, "SELECT no_surat FROM surat_keluar WHERE no_surat = '$no_surat'");
    if(mysqli_num_rows($cek_data) > 0){
        echo "<script>alert('No surat sudah ada di database'); window.location.href = 'add_panggilan_ortu.php';</script>";
    }else{
        $jenis_surat = "Panggilan Orang Tua";
        $tanggal = $_POST['tanggal'];
        $jam = $_POST['jam'];

        // mengubah format tanggal dan jam digabung dan dipisah dengan spasi mengikuti format datetime di database
        echo $tanggal_pemanggilan = implode(" ", [$tanggal, $jam]);

        $tanggal_pembuatan_surat = date("Y-m-d");
        $id_profil_sekolah = 1;
        $id_tahun_ajaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_tahun_ajaran FROM tahun_ajaran WHERE aktif = 'Y'"))['id_tahun_ajaran'];
        $tingkat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT tingkat FROM siswa JOIN kelas USING(id_kelas) JOIN tingkat USING(id_tingkat) WHERE nis = '$nis'"))['tingkat'];
        $keperluan = $_POST['keperluan'];

        // insert data ke database tabel surat_keluar
        $insert_surat_keluar = mysqli_query($conn, "INSERT INTO surat_keluar (no_surat, jenis_surat, nis, tanggal_pembuatan_surat, id_profil_sekolah, id_tahun_ajaran, tingkat, tanggal_pemanggilan, keperluan) VALUES ('$no_surat', '$jenis_surat', '$nis', '$tanggal_pembuatan_surat', '$id_profil_sekolah', '$id_tahun_ajaran', '$tingkat', '$tanggal_pemanggilan', '$keperluan')");
    }    
}











// mengambil data siswa dari database join ke tabel ortu_wali, kelas, tingkat, dan program_keahlian
$query_siswa = mysqli_query($conn, "SELECT surat_keluar.no_surat, siswa.nama_siswa, siswa.nis, program_keahlian.program_keahlian, kelas.rombel, surat_keluar.tingkat, surat_keluar.tanggal_pembuatan_surat, surat_keluar.tanggal_pemanggilan, surat_keluar.keperluan FROM surat_keluar 
JOIN siswa USING(nis)
JOIN kelas USING(id_kelas)
JOIN tingkat USING(id_tingkat)
JOIN program_keahlian USING(id_program_keahlian) WHERE no_surat = '$no_surat'");
$row_siswa = mysqli_fetch_assoc($query_siswa);


// query untuk menampilkan data guru BK
$tingkat = $row_siswa['tingkat'];
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


// pisah format tanggal dan jam nya
$tanggal_pemanggilan = explode(" ", $row_siswa['tanggal_pemanggilan']);
$tanggal_input = $tanggal_pemanggilan[0];
$jam = $tanggal_pemanggilan[1];

// pisah format tanggal dan hari nya
$ambil_tanggal = explode("-",$tanggal_input);
// ambil tanggal nya
$hari = date("l", strtotime($tanggal_input));
// ubah format hari menjadi nama hari indonesia
$hari_indo = ["Monday"=>"Senin", "Tuesday"=>"Selasa", "Wednesday"=>"Rabu", "Thursday"=>"Kamis", "Friday"=>"Jumat", "Saturday"=>"Sabtu", "Sunday"=>"Minggu"];
$hari = $hari_indo[$hari];

// buat array bulan (berfungsi untuk mengubah angka bulan menjadi nama bulan, contoh : 2 menjadi Februari)
$bulan_indo = ["", "Januari", "Pebruari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

// ubah format tanggal input menjadi nama bulan
$tanggal_pemanggilan = date("d", strtotime($tanggal_input)) ." ". $bulan_indo[date("n", strtotime($tanggal_input))] . " " . date("Y", strtotime($tanggal_input));



// mengubah format tanggal dari database menjadi format tanggal bulan indonesia
$tanggal_surat = explode("-", $row_siswa['tanggal_pembuatan_surat']);
$tanggal_cetak_surat = $tanggal_surat[2] ." ". $bulan_indo[(int)$tanggal_surat[1]] . " " . $tanggal_surat[0];



// // Menyertakan tampilan header (bagian atas halaman)
include ROOT_PATH . "/includes/header.php";
?>






<style>
/* Animasi icon printer dari template sebelumnya */ 
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
</style>






<!-- tombol navigasi no-print -->
<center class="no-print">
    <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
        <!-- tombol ini berfungsi untuk kembali ke halaman add_panggilan_ortu.php dan mengirimkan nis yang sudah di cek menggunakan method post -->
        <?php
        if(isset($_GET['no_surat'])){
        ?>
            <form action="<?= BASE_URL ?>/pages/laporan/list_panggilan_ortu.php" style="margin: 0;">
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
            <form action="add_panggilan_ortu.php" method="post" style="margin: 0;">
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
    <!-- Header / Kop Surat -->
    <div class="header">
        <img src="<?= BASE_URL ?>/images/kop.jpg" alt="kepala surat" width="100%">
    </div><br>

    <!-- Body Surat -->
    <div class="body-surat">
        <table class="table-info" style="width: 100%; margin-bottom: 25px;">
            <tr>
                <td style="width: 90px;">No.</td>
                <td style="width: 15px;">:</td>
                <!-- 
                Pada nomor surat contoh : 230/SMK TI/BG/II/2026, bagian angka Romawi “II” biasanya menunjukkan bulan diterbitkannya surat, bukan tanggal lengkap, bagian angka 2026 merujuk ke tahun pembuatan surat.

                Penjelasan Struktur Umum Nomor Surat :
                Nomor Surat Keluar / Kode Sekolah / Kode Perihal / Bulan (Romawi) / Tahun
                Jadi:
                    •	230 → Nomor urut surat keluar (surat ke-230 yang dicatat di buku agenda).
                    •	SMK TI → Kode nama sekolah.
                    •	BG → Bali Global.
                    •	II → Bulan surat dibuat (Pebruari).
                    •	2026 → Tahun pembuatan surat. 
                -->
                <td><?=$no_surat?></td>
            </tr>
            <tr>
                <td>Lamp.</td>
                <td>:</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>:</td>
                <td><b>Pemanggilan Orang Tua / Wali Siswa</b></td>
            </tr>
        </table>

        <p style="margin: 0; margin-bottom: 5px;">
            Kepada<br>
            Yth. Bapak/ Ibu
        </p>
        <table class="table-info" style="width: 100%; margin-left: 35px; margin-bottom: 25px;">
            <tr>
                <td style="width: 190px;">Orang Tua / Wali dari</td>
                <td style="width: 15px;">:</td>
                <td><?=$row_siswa['nama_siswa']?></td>
            </tr>
            <tr>
                <td>Kelas / Nis</td>
                <td>:</td>
                <!-- menampilkan data kelas, program keahlian, rombel, dan nis -->
                <td><?=$row_siswa['tingkat'] . ' ' . $row_siswa['program_keahlian'] . ' ' . $row_siswa['rombel']?> / <?=$row_siswa['nis']?></td>
            </tr>
        </table>

        <p style="margin: 0; margin-bottom: 5px;">
            Dengan hormat,
        </p>
        <p style="margin: 0; margin-bottom: 10px;">
            Bersama surat ini, kami mengharapkan kehadiran Bapak / Ibu pada :
        </p>

        <table class="table-info" style="width: 100%; margin-left: 35px; margin-bottom: 25px;">
            <tr>
                <td style="width: 160px;">Hari / Tanggal</td>
                <td style="width: 15px;">:</td>
                <!-- menampilkan hari dan tanggal berdasarkan dari data yang di input dari file add_panggilan_ortu -->
                <td><?php echo $hari; ?> / <?=$tanggal_pemanggilan?></td>
            </tr>
            <tr>
                <td>Pukul</td>
                <td>:</td>
                <!-- menampilkan jam berdasarkan dari data yang di input dari file add_panggilan_ortu -->
                <td><?=date("H:i", strtotime($jam))?> WITA</td>
            </tr>
            <tr>
                <td>Tempat</td>
                <td>:</td>
                <td>SMK TI Bali Global Denpasar</td>
            </tr>
            <tr>
                <td>Keperluan</td>
                <td>:</td>
                <!-- menampilkan keperluan berdasarkan dari data yang di input dari file add_panggilan_ortu -->
                <td><?=$row_siswa['keperluan']?></td>
            </tr>
        </table>

        <p style="margin:0;">
            <span style="display:inline-block; width: 45px;"></span>Demikian surat ini kami sampaikan, besar harapan kami pertemuan ini agar tidak diwakilkan.<br>
            Atas perhatian dan kerjasamanya, kami ucapkan terimakasih.
        </p>

        <br><br><br>
        <table class="table-info" style="width: 100%; text-align: left;margin-left: 40px;">
            <tr>
                <td style="width: 55%;">Mengetahui,</td>
                <td style="width: 45%;">Denpasar, <?= $tanggal_cetak_surat ?></td>
            </tr>
            <tr>
                <td>Waka Kesiswaan</td>
                <td>Guru BK</td>
            </tr>
            <tr>
                <td colspan="2"><br><br><br></td>
            </tr>
            <tr>
                <!-- menampilkan nama waka kesiswaan dari database -->
                <td><u><?= $waka_kesiswaan ?></u></td>
                <!-- menampilkan nama guru bk dari database -->
                <td><u><?= $guru_bk ?></u></td>
            </tr>
        </table>
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
