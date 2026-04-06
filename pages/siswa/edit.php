<?php
include_once __DIR__ . '/../../config/config.php';
$query = "
SELECT siswa.*, ortu_wali.*, kelas.*, program_keahlian.*, tingkat.*, guru.nama_pengguna 
FROM siswa
JOIN ortu_wali ON siswa.id_ortu_wali = ortu_wali.id_ortu_wali
JOIN kelas ON siswa.id_kelas = kelas.id_kelas
JOIN program_keahlian USING(id_program_keahlian)
JOIN tingkat USING(id_tingkat)
LEFT JOIN guru ON kelas.kode_guru = guru.kode_guru
WHERE siswa.nis = '$nis'
";

$result = mysqli_fetch_assoc(mysqli_query($conn, $query));
?>

<center>
    <h2>Edit Data Siswa</h2>

    <!-- Form -->
    <form action="<?= BASE_URL ?>/process/siswa_process.php" method="POST">
        

        <!-- 🔥 WAJIB: penentu action -->
        <input type="hidden" name="action" value="edit">

        <table cellpadding="10">
            <tr>
                <td><center>NIS Siswa</center></td>
                <td><center>Nama Siswa</center></td>
                <td><center>Alamat Siswa</center></td>
            </tr>
            <tr>
                <!-- 🔥 FIX: name harus 'nis' -->
                <td>
                    <input type="number" name="nis" value="<?= $result['nis'] ?>" readonly>
                </td>

                <td>
                    <input type="text" name="nama_siswa" value="<?= $result['nama_siswa'] ?>" readonly>
                </td>

                <td>
                    <input type="text" name="alamat_siswa" value="<?= $result['alamat'] ?>" required>
                </td>
            </tr>

            <tr>
                <td><center>Nama Ayah</center></td>
                <td><center>Nama Ibu</center></td>
                <td><center>Nama Wali</center></td>
            </tr>
            <tr>
                <td><input type="text" value="<?= $result['ayah'] ?>" readonly></td>
                <td><input type="text" value="<?= $result['ibu'] ?>" readonly></td>
                <td><input type="text" value="<?= $result['wali'] ?>" readonly></td>
            </tr>

            <tr>
                <td><center>Pekerjaan Ayah</center></td>
                <td><center>Pekerjaan Ibu</center></td>
                <td><center>Pekerjaan Wali</center></td>
            </tr>
            <tr>
                <td><input type="text" name="pekerjaan_ayah" value="<?= $result['pekerjaan_ayah'] ?>"></td>
                <td><input type="text" name="pekerjaan_ibu" value="<?= $result['pekerjaan_ibu'] ?>"></td>
                <td><input type="text" name="pekerjaan_wali" value="<?= $result['pekerjaan_wali'] ?>"></td>
            </tr>

            <tr>
                <td><center>Nomor Telpon Ayah</center></td>
                <td><center>Nomor Telpon Ibu</center></td>
                <td><center>Nomor Telpon Wali</center></td>
            </tr>
            <tr>
                <td><input type="text" name="telp_ayah" value="<?= $result['no_telp_ayah'] ?>"></td>
                <td><input type="text" name="telp_ibu" value="<?= $result['no_telp_ibu'] ?>"></td>
                <td><input type="text" name="telp_wali" value="<?= $result['no_telp_wali'] ?>"></td>
            </tr>

            <tr>
                <td><center>Alamat Ayah</center></td>
                <td><center>Alamat Ibu</center></td>
                <td><center>Alamat Wali</center></td>
            </tr>
            <tr>
                <td><input type="text" name="alamat_ayah" value="<?= $result['alamat_ayah'] ?>"></td>
                <td><input type="text" name="alamat_ibu" value="<?= $result['alamat_ibu'] ?>"></td>
                <td><input type="text" name="alamat_wali" value="<?= $result['alamat_wali'] ?>"></td>
            </tr>

            <tr>
                <td><center>Kelas Siswa</center></td>
                <td><center>Wali Kelas</center></td>
                <td><center>status</center></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="kelas"
                        value="<?= $result['tingkat'] . ' ' . $result['program_keahlian'] . ' ' . $result['rombel'] ?>"
                        readonly>
                </td>
                <td><input type="text" name="wali_kelas" value="<?= $result['nama_pengguna'] ?>"></td>
                <td>
                    <input type="radio" name="status" value="aktif"> aktif <br>
                    <input type="radio" name="status" value="tidak_aktif"> tidak aktif <br>
                    <input type="radio" name="status" value="lulus"> lulus <br>
                    <input type="radio" name="status" value="pindah"> pindah <br>
                </td>
            </tr>

            <tr>
                <td colspan="3" align="right">
                    <button type="submit">Update Data</button>
                </td>
            </tr>
        </table>
    </form>
</center>

<?php include ROOT_PATH . "/includes/footer.php"; ?>