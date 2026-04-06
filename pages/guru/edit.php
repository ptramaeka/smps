<?php
include_once __DIR__ . '/../../config/config.php';
// Menentukan lokasi folder utama proyek di server


// Menghubungkan ke file konfigurasi (koneksi database)
// Menyertakan file header (biasanya berisi tampilan atas halaman dan koneksi dasar)
include ROOT_PATH . "/includes/header.php";

if (!smps_can_manage_data()) {
    smps_deny_access('Akses ditolak. Hanya admin yang dapat mengubah data.', BASE_URL . '/pages/guru/list.php');
}

// Mengambil data guru
$kode_guru = $_GET["kode_guru"];
$result = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT g.*, COALESCE(r.name, g.role) AS role_name
    FROM guru g
    LEFT JOIN roles r ON r.id = g.role_id
    WHERE g.kode_guru = $kode_guru
"));
?>

<!-- Membuat tampilan form untuk menambah data guru -->
<center>
    <h2>Edit Data Guru</h2>

    <!-- Form untuk mengirim data guru baru ke file proses -->
    <form action="<?= BASE_URL ?>/process/guru_process.php" method="POST">
            <table cellpadding="10">
            
                <!-- Menyembunyikan input action agar file proses tahu ini adalah aksi 'add' dan akan di kirim ke guru_process.php -->
                <input type="hidden" name="action" value="edit" />

                <tr>
                    <td><input type="text" name="kode_guru" autocomplete="off" value="<?=$result['kode_guru']?>" required placeholder="Kode Guru" readonly/></td>
                </tr>
                <tr>
                    <td><input type="text" name="nama_guru" autocomplete="off" value="<?=$result['nama_pengguna']?>" required placeholder="Nama guru" readonly/></td>
                </tr>
                <tr>
                    <td><input type="text" name="username" autocomplete="off" value="<?=$result['username']?>" required placeholder="Username"/></td>
                </tr>
                <tr>
                    <td><input type="password" name="password" autocomplete="off" required placeholder="Ganti Password"/></td>
                </tr>
                <tr>
                    <td><input type="text" name="jabatan" autocomplete="off" value="<?=$result['jabatan']?>" required placeholder="Jabatan"/></td>
                </tr>
                <tr>
                    <td><input type="number" name="telp" autocomplete="off" value="<?=$result['telp']?>" required placeholder="Telepon"/></td>
                </tr>
                <tr>
                    <td>
                        <select name="role" id="" style="width: 100%;">
                            <option <?php if($result['role_name'] == 'admin'){ echo "selected"; } ?> value="admin">Admin</option>
                            <option <?php if($result['role_name'] == 'bk'){ echo "selected"; } ?> value="bk">BK</option>
                            <option <?php if($result['role_name'] == 'pengajar' || $result['role_name'] == 'guru'){ echo "selected"; } ?> value="pengajar">Pengajar</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <select name="aktif" id="" style="width: 100%;">
                            <option <?php if($result['aktif'] == 'Y'){ echo "selected"; } ?> value="Y">Aktif</option>
                            <option <?php if($result['aktif'] == 'N'){ echo "selected"; } ?> value="N">Non-Aktif</option>
                        </select>
                    </td>
                </tr>
            </table>
            <button type="submit">Edit Data Guru</button>
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
