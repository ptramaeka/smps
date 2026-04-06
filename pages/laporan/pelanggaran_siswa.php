<?php
include_once __DIR__ . '/../../config/config.php';
include ROOT_PATH . "/includes/header.php";

// Query dasar untuk mengambil semua data pelanggaran siswa
$sql = "SELECT * FROM pelanggaran_siswa 
        JOIN siswa ON pelanggaran_siswa.nis = siswa.nis
        JOIN jenis_pelanggaran ON pelanggaran_siswa.id_jenis_pelanggaran = jenis_pelanggaran.id_jenis_pelanggaran";

// Tambahkan filter pencarian jika ada
if(isset($_GET['cari']) && !empty($_GET['cari'])){
    $cari = mysqli_real_escape_string($conn, $_GET['cari']);
    $sql .= " WHERE siswa.nama_siswa LIKE '%$cari%' OR siswa.nis LIKE '%$cari%'";
}

$sql .= " ORDER BY pelanggaran_siswa.tanggal DESC";
$result = mysqli_query($conn, $sql);
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 700; color: var(--mocha);">Laporan Pelanggaran</h2>
        <p style="color: #888;">Data riwayat pelanggaran seluruh siswa.</p>
    </div>
    
    <form method="get" action="" style="display: flex; gap: 10px; align-items: center;">
        <div style="position: relative;">
            <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; color: #aaa;"></i>
            <input list="siswa_list" name="cari" placeholder="Cari NIS/Nama..." 
                   value="<?php if(isset($_GET['cari'])) echo htmlspecialchars($_GET['cari']); ?>" 
                   autocomplete="off" 
                   style="padding: 10px 10px 10px 35px; border-radius: 20px; border: 1px solid var(--glass-border); background: white; font-size: 0.9rem; width: 220px;" />
        </div>
        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Cari</button>
        <?php if(isset($_GET['cari'])): ?>
            <a href="?" class="btn" style="background: #eee; color: #666; padding: 10px 20px;">Reset</a>
        <?php endif; ?>
    </form>
</div>

<datalist id="siswa_list">
    <?php
        $query_siswa = mysqli_query($conn, "SELECT nis, nama_siswa FROM siswa");
        while ($s = mysqli_fetch_assoc($query_siswa)) { 
            echo "<option value='" . $s['nis'] . "'>" . $s['nama_siswa'] . "</option>"; 
        }
    ?>
</datalist>

<div class="glass-card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu Kejadian</th>
                    <th>Siswa</th>
                    <th>Pelanggaran</th>
                    <th>Poin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)): 
                    $datetime = date("d M Y", strtotime($row['tanggal']));
                    $jam = date("H:i", strtotime($row['tanggal']));
                ?>
                <tr>
                    <td rowspan="2" style="vertical-align: top; padding-top: 25px;"><?= $no++ ?></td>
                    <td>
                        <div style="font-weight: 600;"><?= $datetime ?></div>
                        <div style="font-size: 0.8rem; color: #888;"><?= $jam ?> WIB</div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--mocha);"><?= htmlspecialchars($row['nis']) ?></div>
                        <div style="font-size: 0.9rem;"><?= htmlspecialchars($row['nama_siswa']) ?></div>
                    </td>
                    <td>
                        <span class="badge" style="background: rgba(150, 126, 118, 0.1); color: var(--mocha);"><?= htmlspecialchars($row['jenis']) ?></span>
                    </td>
                    <td style="font-weight: 700; color: var(--danger);">+<?= $row['poin'] ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/pages/laporan/detail_pelanggaran.php?nis=<?=$row['nis']?>" class="btn" style="background: var(--onyx); color: white; padding: 5px 15px; font-size: 0.8rem;">
                            Detail
                        </a>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" style="padding: 10px 15px 25px; border-bottom: 2px solid #f5f5f5;">
                        <div style="font-size: 0.85rem; color: #666; font-style: italic;">
                            <i data-lucide="info" style="width: 12px; vertical-align: middle; margin-right: 5px;"></i>
                            Keterangan: <?= htmlspecialchars($row['keterangan'] ?: 'Tidak ada keterangan tambahan.') ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($result) == 0): ?>
                <tr><td colspan="6" style="text-align: center; padding: 50px; color: #aaa;">Data pelanggaran tidak ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>lucide.createIcons();</script>

<?php include ROOT_PATH . "/includes/footer.php"; ?>
