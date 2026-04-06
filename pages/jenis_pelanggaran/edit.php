<?php
include_once __DIR__ . '/../../config/config.php';
// Menentukan lokasi folder utama proyek di server


// Menghubungkan ke file konfigurasi (koneksi database)
// Menyertakan file header (biasanya berisi tampilan atas halaman dan koneksi dasar)
include ROOT_PATH . "/includes/header.php";

if (!smps_can_manage_data()) {
    smps_deny_access('Akses ditolak. Hanya admin yang dapat mengubah data.', BASE_URL . '/pages/jenis_pelanggaran/list.php');
}

// Ambil ID dari URL
$id_jenis_pelanggaran = $_GET["id_jenis_pelanggaran"];

// Query untuk mengambil data jenis pelanggaran berdasarkan ID
$query = "SELECT * FROM jenis_pelanggaran WHERE id_jenis_pelanggaran = '$id_jenis_pelanggaran'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// Cek apakah data ditemukan
if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href = 'list.php';</script>";
    exit;
}
?>

<!-- Membuat tampilan form untuk edit data jenis pelanggaran -->
<center>
    <h2>Edit Data Jenis Pelanggaran</h2>

    <!-- Form untuk mengirim data ke file proses -->
    <form action="<?= BASE_URL ?>/process/jenis_pelanggaran_process.php" method="POST">
        <table cellpadding="10">
            
            <!-- Hidden fields untuk ID dan action -->
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="id_jenis_pelanggaran" value="<?= $data['id_jenis_pelanggaran'] ?>" />
            
            <tr>
                <td><input type="text" name="nama_pelanggaran" autocomplete="off" value="<?= htmlspecialchars($data['jenis']) ?>" required placeholder="Nama Pelanggaran"/></td>
            </tr>
            <tr>
                <td><input type="number" name="poin" autocomplete="off" value="<?= htmlspecialchars($data['poin']) ?>" required placeholder="Poin"/></td>
            </tr>
        </table>
        <button type="submit">Update Data</button>
    </form>
</center>

<?php
// Menyertakan file footer (biasanya berisi bagian bawah halaman)
include ROOT_PATH . "/includes/footer.php";
?>

<!-- 
    🧠 Penjelasan Singkat:
	•	File ini digunakan untuk menampilkan form edit jenis pelanggaran.
	•	Data diambil dari database berdasarkan ID yang dikirim dari list.php.
	•	Setelah pengguna mengedit data, data akan dikirim ke <?= BASE_URL ?>/process/jenis_pelanggaran_process.php menggunakan metode POST.
	•	File header dan footer dipakai agar tampilan halaman tetap konsisten di seluruh situs. 
-->
