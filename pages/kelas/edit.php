<?php
include_once __DIR__ . '/../../config/config.php';
// Menentukan lokasi folder utama proyek di server


// Menghubungkan ke file konfigurasi (koneksi database)
// Menyertakan file header (biasanya berisi tampilan atas halaman dan koneksi dasar)
include ROOT_PATH . "/includes/header.php";

// Ambil ID dari URL
$id_kelas = $_GET["id"];

// Query untuk mengambil data kelas beserta data terkait
$query = "SELECT kelas.*, tingkat.tingkat, program_keahlian.program_keahlian, guru.nama_pengguna as nama_guru 
          FROM kelas 
          JOIN tingkat ON kelas.id_tingkat = tingkat.id_tingkat 
          JOIN program_keahlian ON kelas.id_program_keahlian = program_keahlian.id_program_keahlian 
          LEFT JOIN guru ON kelas.kode_guru = guru.kode_guru 
          WHERE kelas.id_kelas = '$id_kelas'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// Cek apakah data ditemukan
if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href = 'list.php';</script>";
    exit;
}
?>

<!-- Membuat tampilan form untuk edit data kelas -->
<center>
    <h2>Edit Data Kelas</h2>

    <!-- Form untuk mengirim data ke file proses -->
    <form action="<?= BASE_URL ?>/process/kelas_process.php" method="POST">
        <table cellpadding="10">
            
            <!-- Hidden fields untuk ID dan action -->
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="id_kelas" value="<?= $data['id_kelas'] ?>" />
            
            <tr>
                <td><label><strong>Tingkat:</strong></label></td>
                <td>
                    <select name="tingkat" style="width: 100%;">
                        <option value="X" <?php if($data['tingkat'] == 'X'){ echo "selected"; } ?>>X</option>
                        <option value="XI" <?php if($data['tingkat'] == 'XI'){ echo "selected"; } ?>>XI</option>
                        <option value="XII" <?php if($data['tingkat'] == 'XII'){ echo "selected"; } ?>>XII</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><strong>Program Keahlian:</strong></label></td>
                <td>
                    <select name="program_keahlian" style="width: 100%;">
                        <option value="RPL" <?php if($data['program_keahlian'] == 'RPL'){ echo "selected"; } ?>>RPL</option>
                        <option value="TKJ" <?php if($data['program_keahlian'] == 'TKJ'){ echo "selected"; } ?>>TKJ</option>
                        <option value="DKV" <?php if($data['program_keahlian'] == 'DKV'){ echo "selected"; } ?>>DKV</option>
                        <option value="BD" <?php if($data['program_keahlian'] == 'BD'){ echo "selected"; } ?>>BD</option>
                        <option value="AN" <?php if($data['program_keahlian'] == 'AN'){ echo "selected"; } ?>>AN</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><strong>Rombel:</strong></label></td>
                <td><input type="number" name="rombel" autocomplete="off" value="<?= htmlspecialchars($data['rombel']) ?>" required placeholder="Masukkan nomor rombel (contoh: 1, 2, 3)" style="width: 100%;"/></td>
            </tr>
            <tr>
                <td><label><strong>Wali Kelas:</strong></label></td>
                <td>
                    <select name="kode_guru" style="width: 100%;" required>
                        <option value="">Pilih Wali Kelas</option>
                        <?php
                        $guru_query = mysqli_query($conn, "SELECT kode_guru, nama_pengguna FROM guru WHERE aktif = 'Y' ORDER BY nama_pengguna");
                        while($guru = mysqli_fetch_assoc($guru_query)){
                            $selected = ($guru['kode_guru'] == $data['kode_guru']) ? 'selected' : '';
                            echo "<option value='{$guru['kode_guru']}' $selected>{$guru['nama_pengguna']}</option>";
                        }
                        ?>
                    </select>
                </td>
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
	•	File ini digunakan untuk menampilkan form edit kelas.
	•	Data diambil dari database berdasarkan ID yang dikirim dari list.php.
	•	Data kelas ditampilkan dengan JOIN ke tabel tingkat, program_keahlian, dan guru untuk menampilkan data yang lengkap.
	•	Setiap field memiliki nilai default dari data existing.
	•	Setelah pengguna mengedit data, data akan dikirim ke <?= BASE_URL ?>/process/kelas_process.php menggunakan metode POST.
	•	File header dan footer dipakai agar tampilan halaman tetap konsisten di seluruh situs. 
-->