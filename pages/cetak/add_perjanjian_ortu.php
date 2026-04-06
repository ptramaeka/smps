<?php
include_once __DIR__ . '/../../config/config.php';
// Menentukan lokasi root folder proyek di server


// Menghubungkan ke file konfigurasi (koneksi database)
// Menyertakan tampilan header (bagian atas halaman)
include ROOT_PATH . "/includes/header.php";

?>


<center>
    <h2>Surat Perjanjian Orang Tua</h2>


    <!-- Form Pilih NIS -->
    <form action="" method="post">
        <!-- datalist ini berfungsi untuk menampilkan data nis dan nama siswa yang akan dipilih -->
        <datalist id="nis" name="nis">
            <?php 
            $result = mysqli_query($conn, "SELECT nis, nama_siswa FROM siswa");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['nis'] . "'>" . $row['nis'] . " - " . $row['nama_siswa'] . "</option>";
            }
            ?>
        </datalist>
        <!-- input ini berfungsi untuk menampilkan data nis dan nama siswa yang akan dipilih -->
        <input type="text" name="nis" value="<?php if(isset($_POST['nis'])) { echo $_POST['nis']; } else { echo ""; } ?>" list="nis" placeholder="pilih NIS" autocomplete="off">
        <input class="btn-warning" style="color:#fff; font-weight:bold" type="submit" value="cek">
    </form>


    <br><br>
    
    
    <!-- Form Input Data Orang Tua -->
    <?php
    // jika nis sudah diinput
    if(isset($_POST['nis'])) {
        $nis = $_POST['nis'];
        // query untuk menampilkan data siswa dan orang tua
        $result_ortu_wali = mysqli_query($conn, "SELECT * FROM siswa JOIN ortu_wali USING(id_ortu_wali) WHERE nis = '$nis'");
        $row_ortu_wali = mysqli_fetch_assoc($result_ortu_wali);

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

        if(mysqli_num_rows($cek_poin) == 0) {
            ?>
            <h3 style="text-align: center;">Siswa memiliki poin yang tidak sesuai</h3>
            <?php
        } else {
        ?>

        <!-- Form Input Data Ayah -->
        <?php
        // jika data ayah ada
        if(!empty($row_ortu_wali["ayah"])) {
        ?>
        <form action="surat_perjanjian_ortu.php" method="post">
            <fieldset style="width:20%">
            <legend>Data Ayah</legend>

            <!-- input ini berfungsi untuk menyimpan data nis -->
            <input type="hidden" name="nis" value="<?php echo $nis; ?>">
            <input type="hidden" name="id_ortu_wali" value="<?php echo $row_ortu_wali['id_ortu_wali']; ?>">
            <input type="hidden" name="orang_tua" value="ayah">

            <table cellspacing="10">
                <tr>
                    <td>Nama Ayah</td>
                    <td>:</td>
                    <td><input type="text" name="nama_ortu" value="<?php echo $row_ortu_wali['ayah']; ?>" required></td>
                </tr>
                <tr>
                    <td>Tempat Lahir</td>
                    <td>:</td>
                    <td><input type="text" name="tempat_lahir" value="<?php echo $row_ortu_wali['tempat_lahir_ayah']; ?>" required></td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>:</td>
                    <td><input type="date" name="tanggal_lahir" value="<?php echo $row_ortu_wali['tanggal_lahir_ayah']; ?>" required></td>
                </tr>
                <tr>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td><input type="text" name="pekerjaan" value="<?php echo $row_ortu_wali['pekerjaan_ayah']; ?>" required></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td><textarea name="alamat" id="" required><?php echo $row_ortu_wali['alamat_ayah']; ?></textarea></td>
                </tr>
                <tr>
                    <td>No. Hp/Telp</td>
                    <td>:</td>
                    <td><input type="number" name="no_telp" value="<?php echo $row_ortu_wali['no_telp_ayah']; ?>" required></td>
                </tr>
            </table>
            <br>

            <input type="submit" value="cetak surat">
            </fieldset>
        </form>
        <?php
        }
        ?>



        <!-- Form Input Data Ibu -->
        <?php
        // jika data ibu ada
        if(!empty($row_ortu_wali["ibu"])) {
        ?>
        <form action="surat_perjanjian_ortu.php" method="post">
            <fieldset style="width:20%">
            <legend>Data Ibu</legend>
            
            <!-- input ini berfungsi untuk menyimpan data nis -->
            <input type="hidden" name="nis" value="<?php echo $nis; ?>">
            <input type="hidden" name="id_ortu_wali" value="<?php echo $row_ortu_wali['id_ortu_wali']; ?>">
            <input type="hidden" name="orang_tua" value="ibu">

            <table cellspacing="10">
                <tr>
                    <td>Nama Ibu</td>
                    <td>:</td>
                    <td><input type="text" name="nama_ortu" value="<?php echo $row_ortu_wali['ibu']; ?>" required></td>
                </tr>
                <tr>
                    <td>Tempat Lahir</td>
                    <td>:</td>
                    <td><input type="text" name="tempat_lahir" value="<?php echo $row_ortu_wali['tempat_lahir_ibu']; ?>" required></td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>:</td>
                    <td><input type="date" name="tanggal_lahir" value="<?php echo $row_ortu_wali['tanggal_lahir_ibu']; ?>" required></td>
                </tr>
                <tr>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td><input type="text" name="pekerjaan" value="<?php echo $row_ortu_wali['pekerjaan_ibu']; ?>" required></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td><textarea name="alamat" id="" required><?php echo $row_ortu_wali['alamat_ibu']; ?></textarea></td>
                </tr>
                <tr>
                    <td>No. Hp/Telp</td>
                    <td>:</td>
                    <td><input type="number" name="no_telp" value="<?php echo $row_ortu_wali['no_telp_ibu']; ?>" required></td>
                </tr>
            </table>
            <br>

            <input type="submit" value="cetak surat">
            </fieldset>
        </form>
        <?php
        }
        ?>




        <!-- Form Input Data Wali -->   
        <?php
        // jika data wali ada
        if(!empty($row_ortu_wali["wali"])) {
        ?>
        <form action="surat_perjanjian_ortu.php" method="post">
            <fieldset style="width:20%">
            <legend>Data Wali</legend>

            <!-- input ini berfungsi untuk menyimpan data nis -->
            <input type="hidden" name="nis" value="<?php echo $nis; ?>">
            <input type="hidden" name="id_ortu_wali" value="<?php echo $row_ortu_wali['id_ortu_wali']; ?>">
            <input type="hidden" name="orang_tua" value="wali">

            <table cellspacing="10">
                <tr>
                    <td>Nama Wali</td>
                    <td>:</td>
                    <td><input type="text" name="nama_ortu" value="<?php echo $row_ortu_wali['wali']; ?>" required></td>
                </tr>
                <tr>
                    <td>Tempat Lahir</td>
                    <td>:</td>
                    <td><input type="text" name="tempat_lahir" value="<?php echo $row_ortu_wali['tempat_lahir_wali']; ?>" required></td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>:</td>
                    <td><input type="date" name="tanggal_lahir" value="<?php echo $row_ortu_wali['tanggal_lahir_wali']; ?>" required></td>
                </tr>
                <tr>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td><input type="text" name="pekerjaan" value="<?php echo $row_ortu_wali['pekerjaan_wali']; ?>" required></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td><textarea name="alamat" id="" required><?php echo $row_ortu_wali['alamat_wali']; ?></textarea></td>
                </tr>
                <tr>
                    <td>No. Hp/Telp</td>
                    <td>:</td>
                    <td><input type="number" name="no_telp" value="<?php echo $row_ortu_wali['no_telp_wali']; ?>" required></td>
                </tr>
            </table>
            <br>

            <input type="submit" value="cetak surat">
            </fieldset>
        </form>
        <?php
        }
        ?>




        <!-- Form Input Data Jika Tidak Ada Data Orang Tua -->   
        <?php
        // jika data ayah, ibu, dan wali tidak ada
        if(empty($row_ortu_wali["ayah"]) && empty($row_ortu_wali["ibu"]) && empty($row_ortu_wali["wali"])) {
        ?>
        <form action="surat_perjanjian_ortu.php" method="post">
            <fieldset style="width:20%">
            <legend>Data Orang Tua / Wali</legend>

            
            <input type="hidden" name="nis" value="<?php echo $nis; ?>">
            <input type="hidden" name="id_ortu_wali" value="<?php echo $row_ortu_wali['id_ortu_wali']; ?>">

            <table cellspacing="10">
                <tr>
                    <td>Nama Orang Tua / Wali</td>
                    <td>:</td>
                    <td><input type="text" name="nama_ortu" required></td>
                </tr>
                <tr>
                    <td>Tempat Lahir</td>
                    <td>:</td>
                    <td><input type="text" name="tempat_lahir" required></td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>:</td>
                    <td><input type="date" name="tanggal_lahir" required></td>
                </tr>
                <tr>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td><input type="text" name="pekerjaan" required></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td><textarea name="alamat" id="" required></textarea></td>
                </tr>
                <tr>
                    <td>No. Hp/Telp</td>
                    <td>:</td>
                    <td><input type="number" name="no_telp" required></td>
                </tr>
                <tr>
                    <td>Hubungan Sebagai</td>
                    <td>:</td>
                    <td>
                        <select name="orang_tua" id="" required>
                            <option value="ayah">Ayah</option>
                            <option value="ibu">Ibu</option>
                            <option value="wali">Wali</option>
                        </select>
                    </td>
                </tr>
            </table>
            <br>

            <input type="submit" value="cetak surat">
            </fieldset>
        </form>
        <?php
        }
        
    }
    }
    ?>
    
</center>


<?php 
// Menyertakan bagian footer (penutup halaman)
include "../../includes/footer.php"; 
?>
