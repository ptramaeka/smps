<?php
include_once __DIR__ . '/../../config/config.php';
// Menentukan lokasi awal (root) folder proyek di server agar pembacaan alamat file selalu akurat


// Memanggil file konfigurasi agar sistem bisa terhubung dengan database MySQL
// ----------------------------------------------------------------------------------------------------------
// QUERY 1: Mengambil data siswa dan menjumlahkan total poin pelanggaran khusus bagian Surat Perjanjian Siswa
// ----------------------------------------------------------------------------------------------------------
// Penjelasan tentang perbaikan Nginx (ONLY_FULL_GROUP_BY):
// Pada MySQL versi baru (secara standar ada di Nginx / server produksi), aturan 'ONLY_FULL_GROUP_BY' diaktifkan.
// Artinya, semua kolom yang kita panggil di SELECT (kecuali yang memakai rumus matematika seperti SUM atau COUNT) 
// wajib dituliskan kembali ke dalam blok GROUP BY untuk mencegah tampilan data yang tidak menentu.
$query_perjanjian_siswa = mysqli_query($conn, "
    SELECT 
        data_utama.*, 
        total_pelanggaran.total_poin
    FROM (
        SELECT 
            siswa.nis, 
            siswa.nama_siswa, 
            ps.tingkat, 
            ps.tanggal AS tanggal_surat, 
            ps.status AS status_dokumen, 
            ps.foto_dokumen
        FROM siswa
        JOIN pelanggaran_siswa USING(nis)
        JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
        LEFT JOIN perjanjian_siswa ps USING(id_pelanggaran_siswa)
        WHERE siswa.status = 'aktif' AND ps.status = 'Selesai'
        -- Semua kolom yang di SELECT (yang tidak di-SUM) harus dimasukkan ke GROUP BY ini
        GROUP BY 
            siswa.nis, 
            siswa.nama_siswa, 
            ps.tingkat, 
            ps.tanggal, 
            ps.status, 
            ps.foto_dokumen
        ORDER BY 
            siswa.nis, 
            ps.tanggal DESC
    ) AS data_utama
    JOIN (
        SELECT 
            nis, 
            SUM(poin) AS total_poin
        FROM pelanggaran_siswa
        JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
        GROUP BY nis
    ) AS total_pelanggaran USING(nis)
");

// ----------------------------------------------------------------------------------------------------------
// QUERY 2: Mengambil porsi data yang sama tapi khusus bagian Surat Panggilan Orang Tua
// ----------------------------------------------------------------------------------------------------------
$query_panggilan_ortu = mysqli_query($conn, "
    SELECT 
        data_utama.*, 
        total_pelanggaran.total_poin
    FROM (
        SELECT 
            siswa.nis, 
            siswa.nama_siswa, 
            po.tingkat, 
            po.tanggal AS tanggal_surat, 
            po.status AS status_dokumen, 
            po.foto_dokumen
        FROM siswa
        JOIN pelanggaran_siswa USING(nis)
        JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
        LEFT JOIN perjanjian_orang_tua po USING(id_pelanggaran_siswa)
        WHERE siswa.status = 'aktif' AND po.status = 'Selesai'
        -- Penerapan perbaikan GROUP BY yang sama untuk kompatibilitas Nginx
        GROUP BY 
            siswa.nis, 
            siswa.nama_siswa, 
            po.tingkat, 
            po.tanggal, 
            po.status, 
            po.foto_dokumen
        ORDER BY 
            siswa.nis, 
            po.tanggal DESC
    ) AS data_utama
    JOIN (
        SELECT 
            nis, 
            SUM(poin) AS total_poin
        FROM pelanggaran_siswa
        JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
        GROUP BY nis
    ) AS total_pelanggaran USING(nis)
");

// Menampilkan desain header berupa navigasi atau menu bagian atas halaman
include ROOT_PATH . "/includes/header.php";

smps_require_roles(['admin', 'bk', 'pengajar'], 'Akses ditolak. Halaman laporan hanya untuk admin/BK/pengajar.');

$_can_print = smps_can_print_reports();

?>

<!-- Kumpulan blok kode gaya (CSS) untuk merias tampilan tombol cetak -->
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
/* Kumpulan animasi untuk pergerakan icon kertas yang keluar masuk printer */ 
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

<!-- Tombol Cetak Dokumen. Class 'no-print' membuat div ini tidak akan ikut tergambar saat dicetak ke PDF -->
<?php if ($_can_print): ?>
    <center class="no-print">
        <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
            <!-- onClick="window.print()" adalah fungsi JavaScript untuk memanggil menu print bawaan browser -->
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
                <span>&nbsp;&nbsp;Cetak Dokumen</span>
            </button>
        </div>
    </center>
<?php endif; ?>

<!-- Elemen utama yang memuat konten laporan yang akan dicetak -->
<div class="page">
    <!-- Header -->
    <div class="header">
        <!-- Menarik gambar logo dan teks kop surat menggunakan elemen <img> -->
        <img src="<?= BASE_URL ?>/images/kop.jpg" alt="kepala surat" width="100%">
    </div>
    
    <div class="title">LAPORAN REKAPITULASI SURAT PERJANJIAN</div>
    <br>
    
    <div class="content">
        <!-- TABEL 1: Tabel Perjanjian Khusus Siswa -->
        <h1>Surat Perjanjian Siswa</h1>

        <div class="indent">
            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <thead align="center">
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pembuatan Surat</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Tingkat</th>
                        <th>Status Dokumen</th>
                        <th>Total Poin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Membuat variabel 'nomor_urut' untuk dijadikan auto-increment tampilan pada baris tabel
                    $nomor_urut = 1;
                    
                    // mysqli_fetch_assoc() menjalankan proses pengambilan data baris demi baris dari $query_perjanjian_siswa
                    // Selama datanya masih ada, perintah pengulangan tulisan <tr> (baris tabel) di bawah ini akan diulangi.
                    while ($data_siswa = mysqli_fetch_assoc($query_perjanjian_siswa)){
                    ?>
                    <tr>
                        <td align="center"><?= $nomor_urut++ ?></td> <!-- Penambahan ++ membuat variabel naik 1 angka setiap dilooping -->
                        <td>
                            <?php
                            // ---- BLOK PENGATURAN TAMPILAN TANGGAL INDONESIA ----
                            
                            // 1. Mengubah format gabungan tanggal bawaan dari database (Tahun-Bulan-Tanggal) ke format umum (Tanggal-Bulan-Tahun)
                            $waktu_lengkap = date("d-m-Y H:i:s", strtotime($data_siswa['tanggal_surat']));
                            
                            // 2. Fungsi explode() bertugas menggunting string. Disini kalimat kita gunting berdasarkan lokasi spasi (" ").
                            //    Hasilnya dibagi dua: indeks [0] berisi tanggal, dan indeks [1] berisi deretan jam saja.
                            $pecah_waktu = explode(" ", $waktu_lengkap);
                            
                            // 3. Simpan bagian jam saja
                            $jam_saja = $pecah_waktu[1];
                            
                            // 4. Kita gunting lagi potongan yang pertama berdasarkan simbol strip ("-")
                            //    Sekarang kita memiliki daftar angka tanggal, kode_bulan, tahun.
                            $pecah_tanggal = explode("-", $pecah_waktu[0]);

                            // 5. Membuat daftar susunan arti (Array) dalam bahasa Indonesia
                            //    Jadi jika aplikasinya bilang '02', itu artinya "Pebruari", dan seterusnya.
                            $nama_bulan_indonesia = array(
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
                            
                            // 6. Merakit kembali pecahan teksnya secara urut dengan titik "." sebagai perekat teks PHP.
                            $tanggal_bersih = $pecah_tanggal[0] . " " . $nama_bulan_indonesia[$pecah_tanggal[1]] . " " . $pecah_tanggal[2];
                            
                            // 7. Mencetak hasil akhir teks ke layar browser
                            echo $tanggal_bersih;
                            echo "<br>"; // <br> artinya pindah garis kebawah / enter di HTML
                            echo $jam_saja;
                            ?>
                        </td>   
                        
                        <!-- htmlspecialchars() berguna untuk mengamankan data pengguna agar kode-kode mencurigakan dinetralkan kembali menjadi teks biasa -->
                        <td align="center"><?= htmlspecialchars($data_siswa['nis']) ?></td>
                        <td align="center"><?= htmlspecialchars($data_siswa['nama_siswa']) ?></td>
                        <td align="center"><?= htmlspecialchars($data_siswa['tingkat']) ?></td>
                        <td align="center"><?= htmlspecialchars($data_siswa['status_dokumen']) ?></td>
                        <td align="center"><?= htmlspecialchars($data_siswa['total_poin']) ?></td>
                    </tr>
                    <?php
                        } // akhir dari perulangan baris tabel 1 
                    ?>
                </tbody>
            </table>
        </div>
    
        <br><br>

        <!-- TABEL 2: Tabel Perjanjian Khusus Orang Tua -->
        <h1>Surat Perjanjian Orang Tua</h1>
        
        <div class="indent">
            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <thead align="center">
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pembuatan Surat</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Tingkat</th>
                        <th>Status Dokumen</th>
                        <th>Total Poin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Mengulang (reset) angka urut kembali ke 1 untuk tabel di bawah
                    $nomor_urut = 1;
                    
                    // Metode perulangan (loop) tabel yang mirip persis seperti yang ada di atas
                    // Hanya saja diisi data orang tua dari variabel $query_panggilan_ortu
                    while ($data_ortu = mysqli_fetch_assoc($query_panggilan_ortu)){
                    ?>
                    <tr>
                        <td align="center"><?= $nomor_urut++ ?></td>
                        <td>
                            <?php
                            // Proses konversi waktu yang sama seperti sebelumnya (Re-use logic)
                            // 1. Ambil & Susun Ulang
                            $waktu_lengkap = date("d-m-Y H:i:s", strtotime($data_ortu['tanggal_surat']));
                            $pecah_waktu = explode(" ", $waktu_lengkap);
                            
                            // 2. Ambil jam
                            $jam_saja = $pecah_waktu[1];
                            
                            // 3. Ambil dan pisahkan string tanggal
                            $pecah_tanggal = explode("-", $pecah_waktu[0]);

                            // 4. Data kamus terjemahan kode bulan
                            $nama_bulan_indonesia = array(
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
                            
                            // 5. Perakitan
                            $tanggal_bersih = $pecah_tanggal[0] . " " . $nama_bulan_indonesia[$pecah_tanggal[1]] . " " . $pecah_tanggal[2];
                            
                            // 6. Menampilkan teks di browser
                            echo $tanggal_bersih;
                            echo "<br>";
                            echo $jam_saja;
                            ?>
                        </td>   
                        
                        <!-- Penarikan info pada setiap baris khusus dari info orang tua -->
                        <td align="center"><?= htmlspecialchars($data_ortu['nis']) ?></td>
                        <td align="center"><?= htmlspecialchars($data_ortu['nama_siswa']) ?></td>
                        <td align="center"><?= htmlspecialchars($data_ortu['tingkat']) ?></td>
                        <td align="center"><?= htmlspecialchars($data_ortu['status_dokumen']) ?></td>
                        <td align="center"><?= htmlspecialchars($data_ortu['total_poin']) ?></td>
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
// Menyisipkan template footer (Biasanya berisi penuntup skrip <body> dan <html>)
include "../../includes/footer.php"; 
?>
