<?php
include_once __DIR__ . '/../../config/config.php';
// Menentukan lokasi root folder proyek di server


// Menghubungkan ke file konfigurasi (koneksi database)
// Menyertakan tampilan header (bagian atas halaman)
include ROOT_PATH . "/includes/header.php";

// jika ada(isset) tombol ditekan dengan method GET berisi value cari maka jalankan perintah dalam if
if(isset($_GET['cari'])){
    $cari = $_GET['cari'];
    $result = mysqli_query($conn, "SELECT * FROM surat_keluar JOIN siswa USING(nis) JOIN surat_pindah USING(id_surat_pindah) WHERE jenis_surat = 'Pindah Sekolah' AND (nama_siswa like '%".$cari."%' OR nis like '%".$cari."%') ORDER BY tanggal_pembuatan_surat DESC");	
    
// else akan berjalan atau tampil ketika tombol cari belum ditekan 
}else{
    $result = mysqli_query($conn, "SELECT * FROM surat_keluar JOIN siswa USING(nis) JOIN surat_pindah USING(id_surat_pindah) WHERE jenis_surat = 'Pindah Sekolah' ORDER BY tanggal_pembuatan_surat DESC");
}


?>

<center>
    <!-- Tombol cetak langsung surat pindah sekolah / manual -->
    <button class="print-btn" onclick="window.location.href='<?= BASE_URL ?>/pages/cetak/add_pindah_sekolah.php'">
        <!-- icon printer (gambar mesin pencetak) -->
        <span class="printer-wrapper">
            <span class="printer-container">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 92 75">
                    <path stroke-width="5" stroke="black" d="M12 37.5H80C85.2467 37.5 89.5 41.7533 89.5 47V69C89.5 70.933 87.933 72.5 86 72.5H6C4.067 72.5 2.5 70.933 2.5 69V47C2.5 41.7533 6.75329 37.5 12 37.5Z"></path>
                    <mask fill="white" id="path-2-inside-1_30_7"><path d="M12 12C12 5.37258 17.3726 0 24 0H57C70.2548 0 81 10.7452 81 24V29H12V12Z"></path></mask>
                    <path mask="url(#path-2-inside-1_30_7)" fill="black" d="M7 12C7 2.61116 14.6112 -5 24 -5H57C73.0163 -5 86 7.98374 86 24H76C76 13.5066 67.4934 5 57 5H24C20.134 5 17 8.13401 17 12H7ZM81 29H12H81ZM7 29V12C7 2.61116 14.6112 -5 24 -5V5C20.134 5 17 8.13401 17 12V29H7ZM57 -5C73.0163 -5 86 7.98374 86 24V29H76V24C76 13.5066 67.4934 5 57 5V-5Z"></path>
                    <circle fill="black" r="3" cy="49" cx="78"></circle>
                </svg>
            </span>
            <span class="printer-page-wrapper"><span class="printer-page"></span></span>
        </span>
        &nbsp;&nbsp;Cetak Surat Pindah Sekolah
    </button><br>
    
    <fieldset style="width: 70%;">
        <legend>Daftar Surat Pindah</legend>
        <div class="scroll">
            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th colspan="8" align="right">
                            <h3 style="float:left; margin: 0;">Daftar Surat Pindah Sekolah</h3>
                            <form action="list_pindah.php" method="get">
                                <!-- menampilkan data nis dan nama siswa -->
                                <datalist id="nama_siswa">
                                    <?php
                                    $result_siswa = mysqli_query($conn, "SELECT nama_siswa, nis FROM surat_keluar JOIN siswa USING(nis) JOIN surat_pindah USING(id_surat_pindah) WHERE jenis_surat = 'Pindah Sekolah' GROUP BY nis");
                                    while ($row_siswa = mysqli_fetch_assoc($result_siswa)) {
                                        echo "<option value='" . $row_siswa['nis'] . "'>";
                                        echo "<option value='" . $row_siswa['nama_siswa'] . "'>";
                                    }
                                    ?>
                                </datalist>
                                <input type="text" value="<?php if(isset($_GET['cari'])) { echo $_GET['cari']; } else { echo ""; } ?>" name="cari" placeholder="Masukkan NIS / Nama Siswa" list="nama_siswa" style="padding: 8px 15px;width: 200px;border-radius: 5px;" autocomplete="off">
                                <input type="submit" class="btn-warning" style="color:white; font-weight:bold;" value="Cari">
                                <a href="list_pindah.php" class="btn-danger" style="text-decoration: none; color: white; font-family:'Arial'; font-size:13px;">Reset</a>
                            </form>
                        </th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pembuatan Surat</th>
                        <th>No Surat</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Sekolah Tujuan</th>
                        <th>Alasan Pindah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($result)==0){
                        echo "
                        <tr><td colspan='8' align='center'>Data Tidak Ditemukan</td></tr>";
                    }else{
                        while ($row = mysqli_fetch_assoc($result)){
                    ?>
                    <tr>
                        <td align="center"><?= $no++?></td>
                        <td align="center">
                            <?php
                            // ubah format tanggal dari YYYY-MM-DD H:i:s menjadi DD-MM-YYYY H:i:s
                            $datetime = date("d-m-Y", strtotime($row['tanggal_pembuatan_surat']));
                            // memecah tanggal
                            $tanggal = explode("-", $datetime);
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
                            ?>
                        </td>
                        <td align="center"><?= htmlspecialchars($row['no_surat']) ?></td>
                        <td align="center"><?= htmlspecialchars($row['nis']) ?></td>
                        <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                        <td><?= htmlspecialchars($row['sekolah_tujuan']) ?></td>
                        <td><?= htmlspecialchars($row['alasan_pindah']) ?></td>
                        <td>
                            <!-- tombol untuk menampilkan detail pelanggaran dengan mengirim nis terpilih melalui method GET -->
                            <button class="btn-primary"><a href="<?= BASE_URL ?>/pages/cetak/surat_pindah_sekolah.php?no_surat=<?=$row['no_surat']?>">Cetak</a></button>
                        </td>
                    </tr>
                    <?php
                        } 
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </fieldset>
</center>









<?php 
include "../../includes/footer.php"; 
?>
