<?php
include_once __DIR__ . '/../../config/config.php';
// Menentukan lokasi folder utama proyek di server


// Menghubungkan ke file konfigurasi (koneksi database)
// Menyertakan file header (biasanya berisi tampilan atas halaman dan koneksi dasar)
include ROOT_PATH . "/includes/header.php";

if (!smps_can_manage_data()) {
    smps_deny_access('Akses ditolak. Hanya admin yang dapat menambah data.', BASE_URL . '/pages/jenis_pelanggaran/list.php');
}
?>

<!-- Membuat tampilan form untuk menambah data jenis pelanggaran -->
<center>
    <h2>Tambah Data Jenis Pelanggaran</h2>

    <!-- Form untuk mengirim data jenis pelanggaran baru ke file proses -->
    <form action="<?= BASE_URL ?>/process/jenis_pelanggaran_process.php" method="POST">
            <table cellpadding="10">

                <!-- Menyembunyikan input action agar file proses tahu ini adalah aksi 'add' dan akan di kirim ke guru_process.php -->
                <input type="hidden" name="action" value="add" />

                <tr>
                    <td><input type="text" name="nama_pelanggaran" autocomplete="off" required placeholder="Nama Pelanggaran"/></td>
                </tr>
                <tr>
                    <td><input type="number" name="poin" autocomplete="off" required placeholder="Poin"/></td>
                </tr>
            </table>
            <button type="submit">Tambah Data</button>
    </form>
</center>

<?php
// Menyertakan file footer (biasanya berisi bagian bawah halaman)
include ROOT_PATH . "/includes/footer.php";
?>

<!-- 
    🧠 Penjelasan Singkat:
	•	File ini digunakan untuk menampilkan form tambah jenis pelanggaran.
	•	Setelah pengguna mengisi data jenis pelanggaran, data akan dikirim ke <?= BASE_URL ?>/process/jenis_pelanggaran_process.php menggunakan metode POST.
	•	File header dan footer dipakai agar tampilan halaman tetap konsisten di seluruh situs. 
-->
