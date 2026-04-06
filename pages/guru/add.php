<?php
include_once __DIR__ . '/../../config/config.php';
// Menentukan lokasi folder utama proyek di server


// Menghubungkan ke file konfigurasi (koneksi database)
// Menyertakan file header (biasanya berisi tampilan atas halaman dan koneksi dasar)
include ROOT_PATH . "/includes/header.php";
?>

<!-- Membuat tampilan form untuk menambah data guru -->
<center>
    <h2>Tambah Data guru</h2>

    <!-- Form untuk mengirim data guru baru ke file proses -->
    <form action="<?= BASE_URL ?>/process/guru_process.php" method="POST">
            <table cellpadding="10">

                <!-- Menyembunyikan input action agar file proses tahu ini adalah aksi 'add' dan akan di kirim ke guru_process.php -->
                <input type="hidden" name="action" value="add" />

                <tr>
                    <?php
                    // Mengambil kode guru terakhir
                    $result = mysqli_query($conn, "SELECT kode_guru FROM guru ORDER BY kode_guru DESC LIMIT 1");
                    $row = mysqli_fetch_assoc($result);
                    $kode_guru = $row['kode_guru'];
                    $kode_guru = explode(".", $kode_guru);
                    $kode_guru = str_pad($kode_guru[1] + 1, 3, "0", STR_PAD_LEFT);
                    ?>
                    <td><input type="text" name="kode_guru" autocomplete="off" value="0021.<?=$kode_guru?>" required placeholder="Kode Guru" readonly/></td>
                </tr>
                <tr>
                    <td><input type="text" name="nama_guru" autocomplete="off" required placeholder="Nama guru"/></td>
                </tr>
                <tr>
                    <td><input type="text" name="username" autocomplete="off" required placeholder="Username"/></td>
                </tr>
                <tr>
                    <td>
                        <select name="jabatan" id="" style="width: 100%;">
                            <option value="">Pilih Jabatan</option>
                            <option value="Guru Mapel">Guru Mapel</option>
                            <option value="Kepala Sekolah">Kepala Sekolah</option>
                            <option value="Waka Kurikulum">Waka Kurikulum</option>
                            <option value="Waka Kesiswaan">Waka Kesiswaan</option>
                            <option value="Waka Sarana Prasarana">Waka Sarana Prasarana</option>
                            <option value="Waka Humas">Waka Humas</option>
                            <option value="Komka AN">Komka AN</option>
                            <option value="Komka RPL">Komka RPL</option>
                            <option value="Komka DKV">Komka DKV</option>
                            <option value="Komka TKJ">Komka TKJ</option>
                            <option value="Komka BD">Komka BD</option>
                            <option value="Guru BK XII">Guru BK XII</option>
                            <option value="Guru BK XI">Guru BK XI</option>
                            <option value="Guru BK X">Guru BK X</option>                            
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><input type="number" name="telp" autocomplete="off" required placeholder="Telepon"/></td>
                </tr>
                <tr>
                    <td>
                        <select name="role" id="" style="width: 100%;">
                            <option value="">Pilih Role</option>
                            <option value="guru">Guru</option>
                            <option value="bk">BK</option>
                        </select>
                    </td>
                </tr>
            </table>
            <button type="submit">Tambah Data Guru</button>
    </form>
</center>

<?php
// Menyertakan file footer (biasanya berisi bagian bawah halaman)
include ROOT_PATH . "/includes/footer.php";
?>

<!-- 
    🧠 Penjelasan Singkat:
	•	File ini digunakan untuk menampilkan form tambah guru.
	•	Setelah pengguna mengisi data guru, data akan dikirim ke <?= BASE_URL ?>/process/guru_process.php menggunakan metode POST.
	•	File header dan footer dipakai agar tampilan halaman tetap konsisten di seluruh situs. 
-->
