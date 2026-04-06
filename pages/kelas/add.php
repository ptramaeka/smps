<?php
include_once __DIR__ . '/../../config/config.php';
// Menentukan lokasi folder utama proyek di server


// Menghubungkan ke file konfigurasi (koneksi database)
// Menyertakan file header (biasanya berisi tampilan atas halaman dan koneksi dasar)
include ROOT_PATH . "/includes/header.php";

if (!smps_can_manage_data()) {
    smps_deny_access('Akses ditolak. Hanya admin yang dapat menambah data.', BASE_URL . '/pages/kelas/list.php');
}

// Form tambah data kelas, tidak perlu mengambil data existing
?>

<!-- Membuat tampilan form untuk menambah data jenis pelanggaran -->
<center>
    <h2>Tambah Data kelas</h2>

    <!-- Form untuk mengirim data jenis pelanggaran baru ke file proses -->
    <form action="<?= BASE_URL ?>/process/kelas_process.php" method="POST">
            <table cellpadding="10">

                <!-- Menyembunyikan input action agar file proses tahu ini adalah aksi 'add' dan akan di kirim ke guru_process.php -->
                <input type="hidden" name="action" value="add" />
                <tr>
                    <td><label><strong>Tingkat:</strong></label></td>
                    <td>
                        <select name="tingkat" style="width: 100%;">
                            <option value="X">X</option>
                            <option value="XI">XI</option>  
                            <option value="XII">XII</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><strong>Program Keahlian:</strong></label></td>
                    <td>
                        <select name="program_keahlian" style="width: 100%;">
                            <option value="RPL">RPL</option>
                            <option value="TKJ">TKJ</option>
                            <option value="DKV">DKV</option>
                            <option value="BD">BD</option>
                            <option value="AN">AN</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><strong>Rombel:</strong></label></td>
                    <td><input type="number" name="rombel" autocomplete="off" required placeholder="Masukkan nomor rombel (contoh: 1, 2, 3)" style="width: 100%;"/></td>
                </tr>
                <tr>
                    <td><label><strong>Wali Kelas:</strong></label></td>
                    <td>
                        <select name="kode_guru" style="width: 100%;" required>
                            <option value="">Pilih Wali Kelas</option>
                            <?php
                            $guru_query = mysqli_query($conn, "SELECT kode_guru, nama_pengguna FROM guru WHERE aktif = 'Y' ORDER BY nama_pengguna");
                            while($guru = mysqli_fetch_assoc($guru_query)){
                                echo "<option value='{$guru['kode_guru']}'>{$guru['nama_pengguna']}</option>";
                            }
                            ?>
                        </select>
                    </td>
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
