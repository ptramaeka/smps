<?php
include_once __DIR__ . '/../../config/config.php';

smps_require_login();
smps_require_roles(['admin', 'bk'], 'Akses ditolak. Halaman cetak hanya untuk admin/BK.');

include ROOT_PATH . "/includes/header.php";
?>

<center>
    <h2>Surat Panggilan Orang Tua</h2>

    <!-- Form Pilih NIS -->
    <form action="" method="post">
        <datalist id="nis" name="nis">
            <?php 
            $result = mysqli_query($conn, "SELECT nis, nama_siswa FROM siswa");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['nis'] . "'>" . $row['nis'] . " - " . $row['nama_siswa'] . "</option>";
            }
            ?>
        </datalist>
        <input type="text" name="nis" value="<?php if(isset($_POST['nis'])) { echo $_POST['nis']; } ?>" list="nis" placeholder="pilih NIS" autocomplete="off">
        <input class="btn-warning" style="color:#fff; font-weight:bold" type="submit" value="cek">
    </form>

    <br><br>

    <?php
    // Jika NIS sudah diinput
    if(isset($_POST['nis']) && !empty($_POST['nis'])) {
        $nis = mysqli_real_escape_string($conn, $_POST['nis']);

        // ✅ VALIDASI POIN PELANGGARAN (50-99)
        $cek_poin = mysqli_query($conn, "
            SELECT 
                siswa.nis, 
                siswa.nama_siswa,
                SUM(jenis_pelanggaran.poin) as total_poin,
                COUNT(pelanggaran_siswa.id_pelanggaran_siswa) as jumlah_pelanggaran
            FROM siswa 
            JOIN pelanggaran_siswa ON siswa.nis = pelanggaran_siswa.nis
            JOIN jenis_pelanggaran ON pelanggaran_siswa.id_jenis_pelanggaran = jenis_pelanggaran.id_jenis_pelanggaran
            WHERE siswa.nis = '$nis'
            GROUP BY siswa.nis
            HAVING total_poin BETWEEN 50 AND 99
        ");

        // Jika poin TIDAK memenuhi syarat (kurang dari 50 atau lebih dari 99)
        if(mysqli_num_rows($cek_poin) == 0) {
            ?>
            <h3 style="text-align: center;">Siswa memiliki poin yang tidak sesuai</h3>
            <?php
        } else {
            // Poin MEMENUHI SYARAT (50-99) → Lanjutkan proses
            $row_poin = mysqli_fetch_assoc($cek_poin);
            $result_ortu_wali = mysqli_query($conn, "SELECT * FROM siswa JOIN ortu_wali USING(id_ortu_wali) WHERE nis = '$nis'");
            $row_ortu_wali = mysqli_fetch_assoc($result_ortu_wali);
            ?>

            <!-- Tampilkan info poin siswa -->
            <div style="background: #a1a1a1; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <strong><?php echo $row_ortu_wali['nama_siswa']; ?></strong> | Total Poin: <strong><?php echo $row_poin['total_poin']; ?></strong> | Pelanggaran: <strong><?php echo $row_poin['jumlah_pelanggaran']; ?></strong>
            </div>

            <!-- Form Input Data Surat -->
            <form action="surat_panggilan_ortu.php" method="post">
                <fieldset style="width:20%">
                <legend>Input Data Surat</legend>
                <input type="hidden" name="nis" value="<?php echo $nis; ?>">
                
                <table cellspacing="10">
                    <tr>
                        <td>No Surat</td>
                        <td>:</td>
                        <td><input type="number" name="no_surat" required></td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td><input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required></td>
                    </tr>
                    <tr>
                        <td>Jam</td>
                        <td>:</td>
                        <td><input type="time" name="jam" value="08:00" required></td>
                    </tr>
                    <tr>
                        <td>Keperluan</td>
                        <td>:</td>
                        <td><textarea name="keperluan" required style="width: 100%; height: 50px;"></textarea></td>
                    </tr>
                </table>
                <br>
                <input type="submit" value="Cetak Surat" style="background: #007bff; color: white; padding: 10px; border: none; border-radius: 5px;">
                </fieldset>
            </form>
        <?php
        }
    }
    ?>
</center>

<?php include "../../includes/footer.php"; ?>
