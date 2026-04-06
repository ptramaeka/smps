<?php
include_once __DIR__ . '/../../config/config.php';
// ============================================================
// File   : list_perjanjian.php
// Fungsi : Menampilkan daftar siswa yang perlu/sudah membuat
//          surat perjanjian, berdasarkan jumlah poin pelanggaran
// ============================================================

// Langkah 1: Tentukan lokasi folder utama proyek di server
// Ini seperti menentukan "alamat rumah" agar file lain mudah ditemukan


// Langkah 2: Hubungkan ke database (seperti membuka buku catatan data siswa)
// ============================================================
// FUNGSI PEMBANTU: Ubah format tanggal ke Bahasa Indonesia
// Contoh: "2025-03-01" → "01 Maret 2025"
// ============================================================
function formatTanggalIndo($tanggal_database) {
    // Daftar nama bulan dalam Bahasa Indonesia
    $nama_bulan = [
        "01" => "Januari",  "02" => "Pebruari", "03" => "Maret",
        "04" => "April",    "05" => "Mei",       "06" => "Juni",
        "07" => "Juli",     "08" => "Agustus",   "09" => "September",
        "10" => "Oktober",  "11" => "November",  "12" => "Desember"
    ];
    // Ubah format tanggal dari database (YYYY-MM-DD) menjadi DD-MM-YYYY
    // lalu pecah berdasarkan tanda "-" menjadi array [hari, bulan, tahun]
    $bagian_tanggal = explode("-", date("d-m-Y", strtotime($tanggal_database)));

    // Gabungkan: hari + nama bulan (bukan angka) + tahun
    return $bagian_tanggal[0] . " " . $nama_bulan[$bagian_tanggal[1]] . " " . $bagian_tanggal[2];
}


// ============================================================
// PROSES UPLOAD FOTO DOKUMEN
// Dijalankan saat guru mengunggah foto surat perjanjian yang
// sudah ditandatangani oleh siswa/orang tua
// ============================================================
if (isset($_POST['upload']) && isset($_FILES["foto_dokumen"])) {

    // Ambil nama file foto yang dikirim lewat form
    $nama_file_foto   = $_FILES["foto_dokumen"]['name'];
    $data_file_foto   = $_FILES["foto_dokumen"];

    // Tentukan folder tujuan penyimpanan foto di server
    $folder_tujuan    = ROOT_PATH . "/images/";
    $lokasi_file_foto = $folder_tujuan . $nama_file_foto;

    // Ambil tanggal surat dan jenis upload (siswa atau orang tua)
    $tanggal_surat = $_POST['tanggal_surat'];
    $jenis_upload  = $_POST['jenis_upload']; // nilai: "siswa" atau "perjanjian_orang_tua"

    // Pindahkan file foto dari folder sementara (tmp) ke folder gambar
    if (move_uploaded_file($data_file_foto["tmp_name"], $lokasi_file_foto)) {

        // Tentukan nama tabel yang akan diupdate berdasarkan jenis upload
        // Jika jenis = "siswa" → update tabel perjanjian_siswa
        // Jika jenis lain → update tabel perjanjian_orang_tua
        if ($jenis_upload == "siswa") {
            $nama_tabel = "perjanjian_siswa";
        } else {
            $nama_tabel = "perjanjian_orang_tua";
        }

        // Bersihkan data dari karakter berbahaya sebelum disimpan ke database
        $nama_file_foto_aman  = mysqli_real_escape_string($conn, $nama_file_foto);
        $tanggal_surat_aman   = mysqli_real_escape_string($conn, $tanggal_surat);

        // Simpan nama foto dan ubah status menjadi "Selesai" di database
        $hasil_update = mysqli_query($conn,
            "UPDATE $nama_tabel
             SET foto_dokumen = '$nama_file_foto_aman', status = 'Selesai'
             WHERE tanggal = '$tanggal_surat_aman'"
        );

        if ($hasil_update) {
            // Jika berhasil: tampilkan pesan lalu kembali ke halaman ini
            echo "<script>alert('Berhasil Mengunggah Foto Dokumen');window.location.href='laporan_perjanjian.php'</script>";
        } else {
            // Jika gagal: tampilkan pesan error dari database
            echo "Gagal Mengunggah Foto Dokumen: " . mysqli_error($conn);
        }
    }
}


// ============================================================
// FUNGSI PEMBANTU: Tampilkan daftar jenis pelanggaran siswa
// Digunakan berulang di beberapa tabel, dibuat fungsi agar
// tidak perlu menulis kode yang sama berkali-kali
// ============================================================
function tampilkanJenisPelanggaran($conn, $nis_siswa) {
    // Ambil jenis pelanggaran yang BERBEDA (DISTINCT) dari database
    // agar pelanggaran yang sama tidak tampil lebih dari 1 kali
    $query = mysqli_query($conn,
        "SELECT DISTINCT jenis
         FROM pelanggaran_siswa
         JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
         WHERE nis = '$nis_siswa'"
    );

    // Siapkan tempat penampungan kosong (array) untuk daftar pelanggaran
    $daftar = [];

    // Ambil data satu per satu dan masukkan ke tempat penampungan
    while ($baris = mysqli_fetch_assoc($query)) {
        // htmlspecialchars = membersihkan teks agar aman ditampilkan di halaman web
        $daftar[] = htmlspecialchars($baris['jenis']);
    }

    // Jika daftar tidak kosong, gabungkan dengan koma dan tampilkan
    if (!empty($daftar)) {
        echo implode(', ', $daftar) . '.';
    }
}


// ============================================================
// QUERY 1: Daftar siswa CALON pembuat surat perjanjian SISWA
// Syarat: poin antara 25-50 dan siswa masih aktif
// Satu siswa bisa muncul lebih dari 1 baris jika punya
// perjanjian di tanggal yang berbeda
// ============================================================

// Cek apakah guru sedang mencari siswa tertentu (via form pencarian)
if (isset($_GET['cari_daftar_siswa'])) {

    // Bersihkan kata kunci pencarian dari karakter berbahaya
    $kata_cari_siswa = mysqli_real_escape_string($conn, $_GET['cari_daftar_siswa']);

    // Query dengan filter nama atau NIS siswa
    $sql_calon_perjanjian_siswa = "
        SELECT main.*, sub.total_poin
        FROM (
            -- Bagian dalam: ambil data siswa dikelompokkan per NIS dan tanggal perjanjian
            -- GROUP BY nis, ps.tanggal: satu siswa bisa muncul >1 baris jika beda tanggal perjanjian
            -- Kompatibel dengan ONLY_FULL_GROUP_BY di Nginx/MySQL strict mode
            SELECT siswa.*, ps.tanggal AS tanggal_surat, ps.status AS status_dokumen, ps.foto_dokumen
            FROM siswa
            JOIN pelanggaran_siswa USING(nis)
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            LEFT JOIN perjanjian_siswa ps USING(id_pelanggaran_siswa)
            WHERE siswa.status = 'aktif'
              AND (siswa.nama_siswa LIKE '%$kata_cari_siswa%' OR siswa.nis LIKE '%$kata_cari_siswa%')
            GROUP BY siswa.nis, ps.tanggal, ps.status, ps.foto_dokumen
            ORDER BY siswa.nis, ps.tanggal DESC
        ) main

        JOIN (
            -- Bagian luar: hitung TOTAL poin keseluruhan per siswa (semua pelanggaran)
            -- Dipisah agar total_poin tidak terpengaruh oleh GROUP BY tanggal di atas
            SELECT nis, SUM(poin) AS total_poin
            FROM pelanggaran_siswa
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            GROUP BY nis
        ) sub USING(nis)

        WHERE sub.total_poin BETWEEN 25 AND 50
    ";

} else {
    // Tidak ada pencarian → tampilkan semua siswa dengan poin 25-50
    $sql_calon_perjanjian_siswa = "
        SELECT main.*, sub.total_poin
        FROM (
            SELECT siswa.*, ps.tanggal AS tanggal_surat, ps.status AS status_dokumen, ps.foto_dokumen
            FROM siswa
            JOIN pelanggaran_siswa USING(nis)
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            LEFT JOIN perjanjian_siswa ps USING(id_pelanggaran_siswa)
            WHERE siswa.status = 'aktif'
            GROUP BY siswa.nis, ps.tanggal, ps.status, ps.foto_dokumen
            ORDER BY siswa.nis, ps.tanggal DESC
        ) main

        JOIN (
            SELECT nis, SUM(poin) AS total_poin
            FROM pelanggaran_siswa
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            GROUP BY nis
        ) sub USING(nis)

        WHERE sub.total_poin BETWEEN 25 AND 50
    ";
}
// Jalankan query ke database dan simpan hasilnya
$hasil_calon_perjanjian_siswa = mysqli_query($conn, $sql_calon_perjanjian_siswa);


// ============================================================
// QUERY 3: Laporan surat perjanjian SISWA yang sudah dicetak
// Hanya tampilkan yang sudah selesai untuk riwayat cetak
// ============================================================

if (isset($_GET['cari_laporan_siswa'])) {
    $kata_cari_laporan_siswa = mysqli_real_escape_string($conn, $_GET['cari_laporan_siswa']);
    $sql_laporan_perjanjian_siswa = "
        SELECT
            ps.tanggal   AS tanggal_surat,
            ps.foto_dokumen,
            ps.status    AS status_dokumen,
            ps.tingkat,
            s.nis,
            s.nama_siswa
        FROM perjanjian_siswa ps
        JOIN pelanggaran_siswa pel USING(id_pelanggaran_siswa)
        JOIN siswa s USING(nis)
        WHERE s.status = 'aktif'
          AND ps.status = 'Selesai'
          AND (s.nama_siswa LIKE '%$kata_cari_laporan_siswa%' OR s.nis LIKE '%$kata_cari_laporan_siswa%')
        GROUP BY s.nis, s.nama_siswa, ps.tanggal, ps.foto_dokumen, ps.status, ps.tingkat
        ORDER BY ps.tanggal DESC
    ";
} else {
    $sql_laporan_perjanjian_siswa = "
        SELECT
            ps.tanggal   AS tanggal_surat,
            ps.foto_dokumen,
            ps.status    AS status_dokumen,
            ps.tingkat,
            s.nis,
            s.nama_siswa
        FROM perjanjian_siswa ps
        JOIN pelanggaran_siswa pel USING(id_pelanggaran_siswa)
        JOIN siswa s USING(nis)
        WHERE s.status = 'aktif'
          AND ps.status = 'Selesai'
        GROUP BY s.nis, s.nama_siswa, ps.tanggal, ps.foto_dokumen, ps.status, ps.tingkat
        ORDER BY ps.tanggal DESC
    ";
}
$hasil_laporan_perjanjian_siswa = mysqli_query($conn, $sql_laporan_perjanjian_siswa);


// ============================================================
// QUERY 3: Daftar siswa CALON pembuat surat perjanjian ORTU
// Syarat: poin antara 50-100 dan siswa masih aktif
// ============================================================

if (isset($_GET['cari_daftar_ortu'])) {
    // PENTING: gunakan variabel $kata_cari_ortu (bukan $kata_cari_siswa)
    // agar pencarian di tabel ortu tidak tercampur dengan tabel siswa
    $kata_cari_ortu = mysqli_real_escape_string($conn, $_GET['cari_daftar_ortu']);

    $sql_calon_perjanjian_ortu = "
        SELECT main.*, sub.total_poin
        FROM (
            SELECT siswa.*, po.tanggal AS tanggal_surat, po.status AS status_dokumen, po.foto_dokumen
            FROM siswa
            JOIN pelanggaran_siswa USING(nis)
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            LEFT JOIN perjanjian_orang_tua po USING(id_pelanggaran_siswa)
            WHERE siswa.status = 'aktif'
              AND (siswa.nama_siswa LIKE '%$kata_cari_ortu%' OR siswa.nis LIKE '%$kata_cari_ortu%')
            GROUP BY siswa.nis, po.tanggal, po.status, po.foto_dokumen
            ORDER BY siswa.nis, po.tanggal DESC
        ) main

        JOIN (
            SELECT nis, SUM(poin) AS total_poin
            FROM pelanggaran_siswa
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            GROUP BY nis
        ) sub USING(nis)

        WHERE sub.total_poin BETWEEN 50 AND 100
    ";
} else {
    $sql_calon_perjanjian_ortu = "
        SELECT main.*, sub.total_poin
        FROM (
            SELECT siswa.*, po.tanggal AS tanggal_surat, po.status AS status_dokumen, po.foto_dokumen
            FROM siswa
            JOIN pelanggaran_siswa USING(nis)
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            LEFT JOIN perjanjian_orang_tua po USING(id_pelanggaran_siswa)
            WHERE siswa.status = 'aktif'
            GROUP BY siswa.nis, po.tanggal, po.status, po.foto_dokumen
            ORDER BY siswa.nis, po.tanggal DESC
        ) main

        JOIN (
            SELECT nis, SUM(poin) AS total_poin
            FROM pelanggaran_siswa
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            GROUP BY nis
        ) sub USING(nis)

        WHERE sub.total_poin BETWEEN 50 AND 100
    ";
}
$hasil_calon_perjanjian_ortu = mysqli_query($conn, $sql_calon_perjanjian_ortu);


// ============================================================
// QUERY 4: Laporan surat perjanjian ORTU yang sudah dicetak
// Hanya tampilkan yang sudah selesai untuk riwayat cetak
// ============================================================

if (isset($_GET['cari_laporan_ortu'])) {
    $kata_cari_laporan_ortu = mysqli_real_escape_string($conn, $_GET['cari_laporan_ortu']);
    $sql_laporan_perjanjian_ortu = "
        SELECT
            po.tanggal   AS tanggal_surat,
            po.foto_dokumen,
            po.status    AS status_dokumen,
            po.tingkat,
            s.nis,
            s.nama_siswa
        FROM perjanjian_orang_tua po
        JOIN pelanggaran_siswa pel USING(id_pelanggaran_siswa)
        JOIN siswa s USING(nis)
        WHERE s.status = 'aktif'
          AND po.status = 'Selesai'
          AND (s.nama_siswa LIKE '%$kata_cari_laporan_ortu%' OR s.nis LIKE '%$kata_cari_laporan_ortu%')
        GROUP BY s.nis, s.nama_siswa, po.tanggal, po.foto_dokumen, po.status, po.tingkat
        ORDER BY po.tanggal DESC
    ";
} else {
    $sql_laporan_perjanjian_ortu = "
        SELECT
            po.tanggal   AS tanggal_surat,
            po.foto_dokumen,
            po.status    AS status_dokumen,
            po.tingkat,
            s.nis,
            s.nama_siswa
        FROM perjanjian_orang_tua po
        JOIN pelanggaran_siswa pel USING(id_pelanggaran_siswa)
        JOIN siswa s USING(nis)
        WHERE s.status = 'aktif'
          AND po.status = 'Selesai'
        GROUP BY s.nis, s.nama_siswa, po.tanggal, po.foto_dokumen, po.status, po.tingkat
        ORDER BY po.tanggal DESC
    ";
}
$hasil_laporan_perjanjian_ortu = mysqli_query($conn, $sql_laporan_perjanjian_ortu);


// Langkah terakhir sebelum HTML: pasang header/tampilan atas halaman
include ROOT_PATH . "/includes/header.php";

smps_require_roles(['admin', 'bk', 'pengajar'], 'Akses ditolak. Halaman laporan hanya untuk admin/BK/pengajar.');

$_can_print = smps_can_print_reports();
?>


<!-- ICON PRINTER (SVG) disimpan dalam variabel PHP agar tidak perlu ditulis ulang -->
<?php
$ikon_printer = '
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
</span>';
?>


<!-- ═══════════════════════════════════════════════════════════════════
     BAGIAN 1: TABEL CALON PEMBUAT SURAT PERJANJIAN SISWA (25-50 poin)
     Siswa yang poinnya sudah 25-50 perlu membuat surat perjanjian
════════════════════════════════════════════════════════════════════ -->
<center>

    <!-- Tombol untuk langsung mencetak surat perjanjian siswa baru -->
    <?php if ($_can_print): ?>
        <button class="print-btn" onclick="window.location.href='<?= BASE_URL ?>/pages/cetak/add_perjanjian_siswa.php'">
            <?= $ikon_printer ?>
            &nbsp;&nbsp;Cetak Surat Perjanjian Siswa
        </button><br>
    <?php endif; ?>

    <fieldset style="width: 80%;">
        <legend>Daftar Pelanggaran Per Siswa</legend>
        <div class="scroll">
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="6" align="right">
                            <h3 style="float:left; margin: 0;">Daftar Siswa di atas 25 Poin</h3>

                            <!-- Form pencarian siswa berdasarkan NIS atau nama -->
                            <form action="laporan_perjanjian.php" method="get">

                                <!-- Datalist = daftar pilihan yang muncul saat mengetik di kotak pencarian -->
                                <datalist id="pilihan_siswa_25_50">
                                    <?php
                                    // Ambil daftar NIS dan nama siswa yang poinnya antara 25-50
                                    // untuk ditampilkan sebagai pilihan autocomplete di kotak pencarian
                                    $query_pilihan_siswa = mysqli_query($conn,
                                        "SELECT nama_siswa, nis
                                         FROM siswa
                                         JOIN pelanggaran_siswa USING(nis)
                                         JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
                                         LEFT JOIN perjanjian_siswa ps USING(id_pelanggaran_siswa)
                                         WHERE siswa.status = 'aktif'
                                         GROUP BY nis, nama_siswa
                                         HAVING SUM(poin) BETWEEN 25 AND 50"
                                    );
                                    while ($baris_pilihan = mysqli_fetch_assoc($query_pilihan_siswa)) {
                                         echo "<option value='" . $baris_pilihan['nis'] . "'>" . $baris_pilihan['nis'] . " - " . $baris_pilihan['nama_siswa'] . " (" . $baris_pilihan['tingkat'] . " " . $baris_pilihan['program_keahlian'] . " " . $baris_pilihan['rombel'] . ")</option>"; 
                                    }
                                    ?>
                                </datalist>

                                <input type="text"
                                       name="cari_daftar_siswa"
                                       value="<?= isset($_GET['cari_daftar_siswa']) ? htmlspecialchars($_GET['cari_daftar_siswa']) : '' ?>"
                                       placeholder="Masukkan NIS / Nama Siswa"
                                       list="pilihan_siswa_25_50"
                                       style="padding:8px 15px;width:200px;border-radius:5px;"
                                       autocomplete="off">
                                <input type="submit" class="btn-warning" style="color:white;font-weight:bold;" value="Cari">
                                <a href="laporan_perjanjian.php" class="btn-danger"
                                   style="text-decoration:none;color:white;font-family:'Arial';font-size:13px;">Reset</a>
                            </form>
                        </th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Jenis Pelanggaran</th>
                        <th>Poin</th>
                        <th width="150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $nomor_urut = 1;

                    // Cek dulu apakah query berhasil dijalankan (tidak error)
                    // dan apakah ada data yang ditemukan
                    if (!$hasil_calon_perjanjian_siswa || mysqli_num_rows($hasil_calon_perjanjian_siswa) == 0) {
                        echo "<tr><td colspan='6' align='center'>Data Tidak Ditemukan</td></tr>";
                        // Jika query gagal total (misalnya syntax error), tampilkan pesan error
                        if (!$hasil_calon_perjanjian_siswa) {
                            echo "<tr><td colspan='6' style='color:red;'>Query Error: " . mysqli_error($conn) . "</td></tr>";
                        }
                    } else {
                        // Ambil data satu per satu dari hasil query
                        while ($data_siswa = mysqli_fetch_assoc($hasil_calon_perjanjian_siswa)) {
                    ?>
                    <tr>
                        <td align="center"><?= $nomor_urut++ ?></td>
                        <td align="center"><?= htmlspecialchars($data_siswa['nis']) ?></td>
                        <td><?= htmlspecialchars($data_siswa['nama_siswa']) ?></td>
                        <td align="center" width="400px">
                            <?php
                            // Tampilkan semua jenis pelanggaran siswa ini (dipisah koma)
                            // Memanggil fungsi yang sudah dibuat di atas
                            tampilkanJenisPelanggaran($conn, $data_siswa['nis']);
                            ?>
                        </td>
                        <td align="center"><?= htmlspecialchars($data_siswa['total_poin']) ?></td>
                        <td>
                            <?php
                            // Tampilkan tombol aksi berbeda berdasarkan status dokumen perjanjian:
                            // - NULL       = belum ada surat → tampilkan tombol "Detail" dan "Cetak"
                            // - Masih Proses = sudah cetak, belum upload foto → tampilkan tombol upload
                            // - Selesai    = sudah upload foto → tampilkan link lihat gambar

                            if ($data_siswa['status_dokumen'] == NULL) { ?>
                                <!-- Status: Belum ada surat perjanjian -->
                                <button class="btn-primary">
                                    <a href="<?= BASE_URL ?>/pages/laporan/detail_pelanggaran.php?nis=<?= $data_siswa['nis'] ?>&tanggal=<?= $data_siswa['tanggal_surat'] ?>">Detail</a>
                                </button>
                                <hr>
                                <!-- Form untuk mencetak surat perjanjian baru -->
                                <?php if ($_can_print): ?>
                                    <form action="<?= BASE_URL ?>/pages/cetak/add_perjanjian_siswa.php" method="post">
                                        <input type="hidden" name="nis" value="<?= $data_siswa['nis'] ?>">
                                        <input type="submit" value="Cetak" style="padding:10px 15px;font-weight:bold;background-color:#fff;border-radius:5px;border:1px solid #ccc;">
                                    </form>
                                <?php else: ?>
                                    <span style="color: #888;">Read-only</span>
                                <?php endif; ?>

                            <?php } elseif ($data_siswa['status_dokumen'] == "Masih Proses") { ?>
                                <!-- Status: Surat sudah dicetak, menunggu upload foto -->
                                <button class="btn-primary">
                                    <a href="<?= BASE_URL ?>/pages/laporan/detail_pelanggaran.php?nis=<?= $data_siswa['nis'] ?>&tanggal=<?= $data_siswa['tanggal_surat'] ?>">Detail Pelanggaran</a>
                                </button>
                                <hr>
                                <?php if ($_can_print): ?>
                                    <button class="btn-primary">
                                        <a href="<?= BASE_URL ?>/pages/cetak/surat_perjanjian_siswa.php?nis=<?= $data_siswa['nis'] ?>">Cetak Surat</a>
                                    </button>
                                <?php else: ?>
                                    <span style="color: #888;">Read-only</span>
                                <?php endif; ?>
                                <hr>
                                <!-- Form upload foto dokumen yang sudah ditandatangani -->
                                <form action="" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="tanggal_surat" value="<?= htmlspecialchars($data_siswa['tanggal_surat']) ?>">
                                    <input type="hidden" name="jenis_upload" value="siswa">
                                    <input type="file" name="foto_dokumen" accept="image/*" required>
                                    <input type="submit" name="upload" value="Upload" class="btn-warning" style="color:white;font-weight:bold;">
                                </form>

                            <?php } elseif ($data_siswa['status_dokumen'] == "Selesai") { ?>
                                <!-- Status: Selesai, foto sudah diupload → tampilkan link foto -->
                                <a href="<?= BASE_URL ?>/images/<?= htmlspecialchars($data_siswa['foto_dokumen']) ?>"
                                   target="_blank" class="btn-primary"
                                   style="text-decoration:none;color:white;font-family:'Arial';font-size:13px;">Lihat Gambar</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                        } // akhir while
                    } // akhir else
                    ?>
                </tbody>
            </table>
        </div>
    </fieldset>
</center>


<br><br>


<!-- ═══════════════════════════════════════════════════════════════════
     BAGIAN 2: TABEL CALON PEMBUAT SURAT PERJANJIAN ORTU (50-100 poin)
     Siswa yang poinnya sudah 50-100 perlu membuat surat perjanjian
     yang ditandatangani juga oleh orang tua/wali
════════════════════════════════════════════════════════════════════ -->
<center>

    <!-- Tombol untuk langsung mencetak surat perjanjian orang tua baru -->
    <?php if ($_can_print): ?>
        <button class="print-btn" onclick="window.location.href='<?= BASE_URL ?>/pages/cetak/add_perjanjian_ortu.php'">
            <?= $ikon_printer ?>
            &nbsp;&nbsp;Cetak Surat Perjanjian Ortu/Wali
        </button><br>
    <?php endif; ?>

    <fieldset style="width: 80%;">
        <legend>Daftar Surat Perjanjian Ortu/Wali</legend>
        <div class="scroll">
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="6" align="right">
                            <h3 style="float:left; margin: 0;">Daftar Siswa di atas 50 Poin</h3>

                            <!-- Form pencarian siswa ortu berdasarkan NIS atau nama -->
                            <form action="list_perjanjian.php" method="get">
                                <datalist id="pilihan_siswa_50_100">
                                    <?php
                                    // Ambil daftar NIS dan nama siswa yang poinnya antara 50-100
                                    $query_pilihan_ortu = mysqli_query($conn,
                                        "SELECT nama_siswa, nis
                                         FROM siswa
                                         JOIN pelanggaran_siswa USING(nis)
                                         JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
                                         WHERE siswa.status = 'aktif'
                                         GROUP BY nis, nama_siswa
                                         HAVING SUM(poin) BETWEEN 50 AND 100"
                                    );
                                    while ($baris_pilihan_ortu = mysqli_fetch_assoc($query_pilihan_ortu)) {
                                        echo "<option value='" . htmlspecialchars($baris_pilihan_ortu['nis']) . "'>";
                                        echo "<option value='" . htmlspecialchars($baris_pilihan_ortu['nama_siswa']) . "'>";
                                    }
                                    ?>
                                </datalist>
                                <input type="text"
                                       name="cari_daftar_ortu"
                                       value="<?= isset($_GET['cari_daftar_ortu']) ? htmlspecialchars($_GET['cari_daftar_ortu']) : '' ?>"
                                       placeholder="Masukkan NIS / Nama Siswa"
                                       list="pilihan_siswa_50_100"
                                       style="padding:8px 15px;width:200px;border-radius:5px;"
                                       autocomplete="off">
                                <input type="submit" class="btn-warning" style="color:white;font-weight:bold;" value="Cari">
                                <a href="list_perjanjian.php" class="btn-danger"
                                   style="text-decoration:none;color:white;font-family:'Arial';font-size:13px;">Reset</a>
                            </form>
                        </th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Jenis Pelanggaran</th>
                        <th>Poin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $nomor_urut = 1;

                    if (!$hasil_calon_perjanjian_ortu || mysqli_num_rows($hasil_calon_perjanjian_ortu) == 0) {
                        echo "<tr><td colspan='6' align='center'>Data Tidak Ditemukan</td></tr>";
                        if (!$hasil_calon_perjanjian_ortu) {
                            echo "<tr><td colspan='6' style='color:red;'>Query Error: " . mysqli_error($conn) . "</td></tr>";
                        }
                    } else {
                        while ($data_ortu = mysqli_fetch_assoc($hasil_calon_perjanjian_ortu)) {
                    ?>
                    <tr>
                        <td align="center"><?= $nomor_urut++ ?></td>
                        <td align="center"><?= htmlspecialchars($data_ortu['nis']) ?></td>
                        <td align="center"><?= htmlspecialchars($data_ortu['nama_siswa']) ?></td>
                        <td align="center" width="400px">
                            <?php tampilkanJenisPelanggaran($conn, $data_ortu['nis']); ?>
                        </td>
                        <td><?= htmlspecialchars($data_ortu['total_poin']) ?></td>
                        <td>
                            <?php if ($data_ortu['status_dokumen'] == NULL) { ?>
                                <!-- Status: Belum ada surat → tampilkan tombol cetak panggilan & perjanjian -->
                                <button class="btn-primary">
                                    <a href="<?= BASE_URL ?>/pages/laporan/detail_pelanggaran.php?nis=<?= $data_ortu['nis'] ?>&tanggal=<?= $data_ortu['tanggal_surat'] ?>">Detail</a>
                                </button>

                                <?php
                                // Cek apakah surat perjanjian ortu sudah pernah dicetak
                                $cek_surat_perjanjian_ortu = mysqli_query($conn,
                                    "SELECT nis FROM surat_keluar
                                     WHERE nis = '" . mysqli_real_escape_string($conn, $data_ortu['nis']) . "'
                                     AND jenis_surat = 'Perjanjian Ortu'"
                                );
                                if (mysqli_num_rows($cek_surat_perjanjian_ortu) == 0) { ?>
                                    <hr>
                                    <?php if ($_can_print): ?>
                                        <form action="<?= BASE_URL ?>/pages/cetak/add_perjanjian_ortu.php" method="post">
                                            <input type="hidden" name="nis" value="<?= $data_ortu['nis'] ?>">
                                            <input type="submit" value="Cetak Perjanjian Ortu" style="padding:10px 15px;font-weight:bold;background-color:#fff;border-radius:5px;border:1px solid #ccc;">
                                        </form>
                                    <?php else: ?>
                                        <span style="color: #888;">Read-only</span>
                                    <?php endif; ?>
                                <?php }  } elseif ($data_ortu['status_dokumen'] == "Masih Proses") { ?>
                                <!-- Status: Surat sudah dicetak, menunggu upload foto -->
                                <button class="btn-primary">
                                    <a href="<?= BASE_URL ?>/pages/laporan/detail_pelanggaran.php?nis=<?= $data_ortu['nis'] ?>&tanggal=<?= $data_ortu['tanggal_surat'] ?>">Detail Pelanggaran</a>
                                </button>
                                <hr>
                                <?php if ($_can_print): ?>
                                    <button class="btn-primary">
                                        <a href="<?= BASE_URL ?>/pages/cetak/surat_perjanjian_ortu.php?nis=<?= $data_ortu['nis'] ?>">Cetak Surat</a>
                                    </button>
                                <?php else: ?>
                                    <span style="color: #888;">Read-only</span>
                                <?php endif; ?>
                                <hr>
                                <form action="" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="tanggal_surat" value="<?= htmlspecialchars($data_ortu['tanggal_surat']) ?>">
                                    <input type="hidden" name="jenis_upload" value="perjanjian_orang_tua">
                                    <input type="file" name="foto_dokumen" accept="image/*" required>
                                    <input type="submit" name="upload" value="Upload" class="btn-warning" style="color:white;font-weight:bold;">
                                </form>

                            <?php } elseif ($data_ortu['status_dokumen'] == "Selesai") { ?>
                                <!-- Status: Selesai → tampilkan link foto dokumen -->
                                <a href="<?= BASE_URL ?>/gambar/<?= htmlspecialchars($data_ortu['foto_dokumen']) ?>"
                                   target="_blank" class="btn-primary"
                                   style="text-decoration:none;color:white;font-family:'Arial';font-size:13px;">Lihat Gambar</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                        } // akhir while
                    } // akhir else
                    ?>
                </tbody>
            </table>
        </div>
    </fieldset>
</center>


<br><br>
<hr>


<!-- ═══════════════════════════════════════════════════════════════════
     BAGIAN 3: LAPORAN - Daftar surat perjanjian SISWA yang sudah dicetak
════════════════════════════════════════════════════════════════════ -->
<center>
    <h1>Laporan Surat Perjanjian</h1>

    <fieldset style="width: 80%;">
        <legend>Daftar Surat Perjanjian Siswa</legend>
        <div class="scroll">
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="6" align="right">
                            <h3 style="float:left; margin: 0;">Daftar Siswa Sudah Cetak Surat Perjanjian Siswa</h3>
                            <form action="list_perjanjian.php" method="get">
                                <datalist id="pilihan_laporan_siswa">
                                    <?php
                                    // Ambil daftar siswa yang sudah pernah membuat perjanjian siswa
                                    $query_pilihan_laporan_siswa = mysqli_query($conn,
                                        "SELECT DISTINCT s.nis, s.nama_siswa
                                         FROM perjanjian_siswa ps
                                         JOIN pelanggaran_siswa pel USING(id_pelanggaran_siswa)
                                         JOIN siswa s USING(nis)"
                                    );
                                    while ($baris = mysqli_fetch_assoc($query_pilihan_laporan_siswa)) {
                                        echo "<option value='" . htmlspecialchars($baris['nis']) . "'>";
                                        echo "<option value='" . htmlspecialchars($baris['nama_siswa']) . "'>";
                                    }
                                    ?>
                                </datalist>
                                <input type="text"
                                       name="cari_laporan_siswa"
                                       value="<?= isset($_GET['cari_laporan_siswa']) ? htmlspecialchars($_GET['cari_laporan_siswa']) : '' ?>"
                                       placeholder="Masukkan NIS / Nama Siswa"
                                       list="pilihan_laporan_siswa"
                                       style="padding:8px 15px;width:200px;border-radius:5px;"
                                       autocomplete="off">
                                <input type="submit" class="btn-warning" style="color:white;font-weight:bold;" value="Cari">
                                <a href="list_perjanjian.php" class="btn-danger"
                                   style="text-decoration:none;color:white;font-family:'Arial';font-size:13px;">Reset</a>
                            </form>
                        </th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pembuatan Surat</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Tingkat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $nomor_urut = 1;
                    if (!$hasil_laporan_perjanjian_siswa || mysqli_num_rows($hasil_laporan_perjanjian_siswa) == 0) {
                        echo "<tr><td colspan='6' align='center'>Data Tidak Ditemukan</td></tr>";
                        if (!$hasil_laporan_perjanjian_siswa) {
                            echo "<tr><td colspan='6' style='color:red;'>Query Error: " . mysqli_error($conn) . "</td></tr>";
                        }
                    } else {
                        while ($data_laporan_siswa = mysqli_fetch_assoc($hasil_laporan_perjanjian_siswa)) {
                    ?>
                    <tr>
                        <td align="center"><?= $nomor_urut++ ?></td>
                        <!-- Tampilkan tanggal dalam format Bahasa Indonesia -->
                        <td align="center"><?= formatTanggalIndo($data_laporan_siswa['tanggal_surat']) ?></td>
                        <td align="center"><?= htmlspecialchars($data_laporan_siswa['nis']) ?></td>
                        <td align="center"><?= htmlspecialchars($data_laporan_siswa['nama_siswa']) ?></td>
                        <td><?= htmlspecialchars($data_laporan_siswa['tingkat']) ?></td>
                        <td>
                            <!-- Hanya tampilkan hasil cetak yang sudah selesai -->
                            <?php if ($_can_print): ?>
                                <button class="btn-primary">
                                    <a href="<?= BASE_URL ?>/pages/cetak/surat_perjanjian_siswa.php?nis=<?= $data_laporan_siswa['nis'] ?>" target="_blank">Cetak Ulang</a>
                                </button>
                            <?php else: ?>
                                <span style="color: #888;">Read-only</span>
                            <?php endif; ?>
                            <hr>
                            <a href="<?= BASE_URL ?>/images/<?= htmlspecialchars($data_laporan_siswa['foto_dokumen']) ?>"
                               target="_blank" class="btn-primary"
                               style="text-decoration:none;color:white;font-family:'Arial';font-size:13px;">Lihat Gambar</a>
                        </td>
                    </tr>
                    <?php
                        } // akhir while
                    } // akhir else
                    ?>
                </tbody>
            </table>
        </div>
    </fieldset>
</center>


<br><br>


<!-- ═══════════════════════════════════════════════════════════════════
     BAGIAN 4: LAPORAN - Daftar surat perjanjian ORTU yang sudah dicetak
════════════════════════════════════════════════════════════════════ -->
<center>

    <fieldset style="width: 80%;">
        <legend>Daftar Surat Perjanjian Ortu/Wali</legend>
        <div class="scroll">
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="6" align="right">
                            <h3 style="float:left; margin: 0;">Daftar Siswa Sudah Cetak Surat Perjanjian Ortu / Wali</h3>
                            <form action="list_perjanjian.php" method="get">
                                <datalist id="pilihan_laporan_ortu">
                                    <?php
                                    // Ambil daftar siswa yang sudah pernah membuat perjanjian ortu
                                    $query_pilihan_laporan_ortu = mysqli_query($conn,
                                        "SELECT DISTINCT s.nis, s.nama_siswa
                                         FROM perjanjian_orang_tua po
                                         JOIN pelanggaran_siswa pel USING(id_pelanggaran_siswa)
                                         JOIN siswa s USING(nis)"
                                    );
                                    while ($baris_ortu = mysqli_fetch_assoc($query_pilihan_laporan_ortu)) {
                                        echo "<option value='" . htmlspecialchars($baris_ortu['nis']) . "'>";
                                        echo "<option value='" . htmlspecialchars($baris_ortu['nama_siswa']) . "'>";
                                    }
                                    ?>
                                </datalist>
                                <input type="text"
                                       name="cari_laporan_ortu"
                                       value="<?= isset($_GET['cari_laporan_ortu']) ? htmlspecialchars($_GET['cari_laporan_ortu']) : '' ?>"
                                       placeholder="Masukkan NIS / Nama Siswa"
                                       list="pilihan_laporan_ortu"
                                       style="padding:8px 15px;width:200px;border-radius:5px;"
                                       autocomplete="off">
                                <input type="submit" class="btn-warning" style="color:white;font-weight:bold;" value="Cari">
                                <a href="list_perjanjian.php" class="btn-danger"
                                   style="text-decoration:none;color:white;font-family:'Arial';font-size:13px;">Reset</a>
                            </form>
                        </th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pembuatan Surat</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Tingkat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $nomor_urut = 1;
                    if (!$hasil_laporan_perjanjian_ortu || mysqli_num_rows($hasil_laporan_perjanjian_ortu) == 0) {
                        echo "<tr><td colspan='6' align='center'>Data Tidak Ditemukan</td></tr>";
                        if (!$hasil_laporan_perjanjian_ortu) {
                            echo "<tr><td colspan='6' style='color:red;'>Query Error: " . mysqli_error($conn) . "</td></tr>";
                        }
                    } else {
                        while ($data_laporan_ortu = mysqli_fetch_assoc($hasil_laporan_perjanjian_ortu)) {
                    ?>
                    <tr>
                        <td align="center"><?= $nomor_urut++ ?></td>
                        <td align="center"><?= formatTanggalIndo($data_laporan_ortu['tanggal_surat']) ?></td>
                        <td align="center"><?= htmlspecialchars($data_laporan_ortu['nis']) ?></td>
                        <td align="center"><?= htmlspecialchars($data_laporan_ortu['nama_siswa']) ?></td>
                        <td><?= htmlspecialchars($data_laporan_ortu['tingkat']) ?></td>
                        <td>
                            <!-- Hanya tampilkan hasil cetak yang sudah selesai -->
                            <?php if ($_can_print): ?>
                                <button class="btn-primary">
                                    <a href="<?= BASE_URL ?>/pages/cetak/surat_perjanjian_ortu.php?nis=<?= $data_laporan_ortu['nis'] ?>" target="_blank">Cetak Ulang</a>
                                </button>
                            <?php else: ?>
                                <span style="color: #888;">Read-only</span>
                            <?php endif; ?>
                            <hr>
                            <a href="<?= BASE_URL ?>/images/<?= htmlspecialchars($data_laporan_ortu['foto_dokumen']) ?>"
                               target="_blank" class="btn-primary"
                               style="text-decoration:none;color:white;font-family:'Arial';font-size:13px;">Lihat Gambar</a>
                        </td>
                    </tr>
                    <?php
                        } // akhir while
                    } // akhir else
                    ?>
                </tbody>
            </table>
        </div>
    </fieldset>
</center>


<?php
// Pasang footer (bagian bawah halaman)
include ROOT_PATH . "/includes/footer.php";
?>
