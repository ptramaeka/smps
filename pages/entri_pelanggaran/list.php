<?php
include_once __DIR__ . '/../../config/config.php';
include ROOT_PATH . "/includes/header.php";
?>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 30px; text-align: center;">
        <h2 style="font-size: 2rem; font-weight: 700; color: var(--mocha);">Entri Pelanggaran</h2>
        <p style="color: #888;">Catat pelanggaran siswa dengan cepat dan mudah.</p>
    </div>

    <div class="glass-card">
        <form action="<?= BASE_URL ?>/process/entri_pelanggaran_process.php" method="POST">
            <input type="hidden" name="action" value="add" />
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 600; margin-bottom: 10px; color: var(--mocha);">Pilih Siswa</label>
                <div style="position: relative;">
                    <i data-lucide="search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); width: 18px; color: #aaa;"></i>
                    <input list="siswa_list" name="nis" placeholder="Ketik NIS atau Nama Siswa..." autocomplete="off" required 
                           style="width: 100%; padding: 12px 15px 12px 45px; border-radius: 12px; border: 1px solid var(--glass-border); background: white; font-size: 1rem;">
                </div>
                <datalist id="siswa_list">
                    <?php
                        $query_siswa = mysqli_query($conn, "SELECT siswa.nis, siswa.nama_siswa, tingkat.tingkat, program_keahlian.program_keahlian, kelas.rombel FROM siswa JOIN kelas ON siswa.id_kelas = kelas.id_kelas JOIN program_keahlian USING(id_program_keahlian) JOIN tingkat USING(id_tingkat)");
                        while ($siswa = mysqli_fetch_assoc($query_siswa)) { 
                            echo "<option value='" . $siswa['nis'] . "'>" . $siswa['nis'] . " - " . $siswa['nama_siswa'] . " (" . $siswa['tingkat'] . " " . $siswa['program_keahlian'] . " " . $siswa['rombel'] . ")</option>"; 
                        }
                    ?>
                </datalist>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 600; margin-bottom: 10px; color: var(--mocha);">Jenis Pelanggaran</label>
                <select name="jenis_pelanggaran" required 
                        style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1px solid var(--glass-border); background: white; font-size: 1rem; appearance: none; cursor: pointer;">
                    <option value="">-- Pilih Jenis Pelanggaran --</option>
                    <?php
                    $pelanggaran_query = mysqli_query($conn, "SELECT id_jenis_pelanggaran, jenis, poin FROM jenis_pelanggaran ORDER BY jenis ASC");
                    while($pelanggaran = mysqli_fetch_assoc($pelanggaran_query)){
                        echo "<option value='{$pelanggaran['jenis']}'>{$pelanggaran['jenis']} ({$pelanggaran['poin']} Poin)</option>";
                    }
                    ?>
                </select>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display: block; font-weight: 600; margin-bottom: 10px; color: var(--mocha);">Keterangan Tambahan</label>
                <textarea name="keterangan" placeholder="Berikan detail kejadian (opsional)..." 
                          style="width: 100%; height: 120px; padding: 15px; border-radius: 12px; border: 1px solid var(--glass-border); background: white; font-size: 1rem; resize: vertical;"></textarea>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 40px; font-size: 1rem; display: inline-flex; align-items: center; gap: 10px;">
                    Simpan Pelanggaran
                    <i data-lucide="save" style="width: 18px;"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>lucide.createIcons();</script>

<?php include ROOT_PATH . "/includes/footer.php"; ?>