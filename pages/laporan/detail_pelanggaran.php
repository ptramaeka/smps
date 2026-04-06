<?php
include_once __DIR__ . '/../../config/config.php';
// Menentukan lokasi root folder proyek di server


// Menghubungkan ke file konfigurasi (koneksi database)
$nis = $_GET['nis'];

// mengambil data siswa dari database join ke tabel ortu_wali, kelas, tingkat, program_keahlian, dan guru
$query_siswa = mysqli_query($conn, "SELECT nis, nama_siswa, tingkat, program_keahlian, rombel, deskripsi FROM siswa
JOIN kelas USING(id_kelas)
JOIN tingkat USING(id_tingkat)
JOIN program_keahlian USING(id_program_keahlian)
WHERE nis = '$nis'");
$row_siswa = mysqli_fetch_assoc($query_siswa);


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










<!-- tombol kembali -->
<center class="no-print">
    
    <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
        <form action="<?= BASE_URL ?>/pages/laporan/pelanggaran_siswa.php" style="margin: 0;">
            <button type="submit">
                <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024">
                    <path d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"></path>
                </svg>
                <span>Kembali</span>
            </button>
        </form>
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
            <span>&nbsp;&nbsp;Cetak</span>
        </button>
    </div>
    
</center>

<div class="page">
    <!-- Header -->
    <div class="header">
        <!-- menampilkan gambar kop surat dari folder gambar-->
        <img src="<?= BASE_URL ?>/images/kop.jpg" alt="kepala surat" width="100%">
    </div>
    
    <div class="title">LAPORAN PELANGGARAN SISWA</div>
    <br>
    <div class="content">
        
        <div class="indent">
            <div class="form-row">
                <div class="label">Nama</div>
                <div class="separator">:</div>
                <div class="field"><?php echo $row_siswa['nama_siswa']; ?></div>
            </div>
            <div class="form-row">
                <div class="label">NIS</div>
                <div class="separator">:</div>
                <div class="field"><?php echo $row_siswa['nis']; ?></div>
            </div>
            <div class="form-row">
                <div class="label">Kelas</div>
                <div class="separator">:</div>
                <div class="field"><?php echo $row_siswa['tingkat'] . ' ' . $row_siswa['program_keahlian'] . ' ' . $row_siswa['rombel'] ?></div>
            </div>
            <div class="form-row">
                <div class="label">Program Keahlian</div>
                <div class="separator">:</div>
                <div class="field"><?php echo $row_siswa['deskripsi']; ?></div>
            </div>
            <div class="form-row">
                <div class="label">Pelanggaran</div>
                <div class="separator">:</div>
            </div>
            <br>
            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <thead align="center">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jenis Pelanggaran</th>
                        <th>Point</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $result_pelanggaran = mysqli_query($conn, "SELECT id_pelanggaran_siswa, tanggal, jenis, keterangan, poin FROM pelanggaran_siswa JOIN siswa USING(nis) JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) WHERE nis = '$nis'");
                    
                    while ($row_pelanggaran = mysqli_fetch_assoc($result_pelanggaran)){
                    ?>
                    <tr>
                        <td align="center"><?= $no++?></td>
                        <td>
                        
                        <?php
                        // ubah format tanggal dari YYYY-MM-DD H:i:s menjadi DD-MM-YYYY H:i:s
                        $datetime = date("d-m-Y H:i:s", strtotime($row_pelanggaran['tanggal']));
                        // memecah tanggal dan jam
                        $tanggal = explode(" ", $datetime);
                        // memecah jam 
                        $jam = $tanggal[1];
                        // memecah tanggal
                        $tanggal = explode("-", $tanggal[0]);

                        // array bulan dalam bahasa indonesia
                        $bulan = array(
                            "01" => "Januari",
                            "02" => "Pebruari",
                            "03" => "Maret",
                            "04" => "April",
                            "05" => "Mei",
                            "06" => "Juni",
                            "07" => "Juli",
                            "08" => "Agustus",
                            "09" => "September",
                            "10" => "Oktober",
                            "11" => "November",
                            "12" => "Desember"
                        );
                        // menggabungkan tanggal dan bulan dalam bahasa indonesia
                        $tanggal = $tanggal[0] . " " . $bulan[$tanggal[1]] . " " . $tanggal[2];
                        // tampilkan tanggal yang sudah dimodifikasi menjadi bahasa indonesia agar mudah dibaca
                        echo $tanggal;
                        echo "<br>";
                        echo $jam;
                        ?>
                    
                    </td>
                        <td><?= htmlspecialchars($row_pelanggaran['jenis']) ?></td>
                        <td rowspan="2" align="center"><?= htmlspecialchars($row_pelanggaran['poin']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">Detail Pelanggaran : <?= htmlspecialchars($row_pelanggaran['keterangan']) ?></td>
                    </tr>
                    <?php
                        } 
                    ?>
                    <tr>
                        <td colspan="3" align="right">Total Poin</td>
                        <td align="center">
                            <?php
                             $total_poin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(poin) FROM pelanggaran_siswa JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) WHERE nis = '$nis'"))['SUM(poin)'];

                            // menampilkan total poin
                            echo $total_poin;
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    

    </div>
</div>






<?php 
// Menyertakan bagian footer (penutup halaman)
include "../../includes/footer.php"; 
?>
