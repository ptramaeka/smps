<?php
include_once __DIR__ . '/../config/config.php';
include ROOT_PATH . '/includes/header.php';

// Fetch Statistics
$total_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa"))['total'];
$total_guru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM guru"))['total'];
$total_pelanggaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pelanggaran_siswa"))['total'];
$total_poin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(poin) as total FROM pelanggaran_siswa JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)"))['total'] ?? 0;

// Fetch Recent Activity (Limit 5)
$recent_query = "
    SELECT ps.tanggal, s.nama_siswa, jp.jenis, jp.poin 
    FROM pelanggaran_siswa ps
    JOIN siswa s ON ps.nis = s.nis
    JOIN jenis_pelanggaran jp ON ps.id_jenis_pelanggaran = jp.id_jenis_pelanggaran
    ORDER BY ps.tanggal DESC
    LIMIT 5
";
$recent_result = mysqli_query($conn, $recent_query);
?>

<div class="dashboard-header" style="margin-bottom: 30px;">
    <h2 style="font-size: 2rem; font-weight: 700; color: var(--mocha);">Selamat Datang, <?= $_COOKIE['nama'] ?></h2>
    <p style="color: #888;">Berikut adalah ringkasan data poin pelanggaran siswa hari ini.</p>
</div>

<!-- Statistik Utama -->
<div class="stats-grid">
    <div class="glass-card stat-card">
        <div class="stat-icon" style="background: #E7AB79;">
            <i data-lucide="users"></i>
        </div>
        <div class="stat-info">
            <h3>Total Siswa</h3>
            <p><?= number_format($total_siswa) ?></p>
        </div>
    </div>

    <div class="glass-card stat-card">
        <div class="stat-icon" style="background: #967E76;">
            <i data-lucide="user-check"></i>
        </div>
        <div class="stat-info">
            <h3>Total Guru</h3>
            <p><?= number_format($total_guru) ?></p>
        </div>
    </div>

    <div class="glass-card stat-card">
        <div class="stat-icon" style="background: #cf4545;">
            <i data-lucide="alert-triangle"></i>
        </div>
        <div class="stat-info">
            <h3>Total Pelanggaran</h3>
            <p><?= number_format($total_pelanggaran) ?></p>
        </div>
    </div>

    <div class="glass-card stat-card">
        <div class="stat-icon" style="background: #A0D995;">
            <i data-lucide="star"></i>
        </div>
        <div class="stat-info">
            <h3>Total Poin</h3>
            <p><?= number_format($total_poin) ?></p>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <!-- Recent Activity -->
    <div class="glass-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-weight: 700;">Aktivitas Pelanggaran Terbaru</h3>
            <a href="<?= BASE_URL ?>/pages/laporan/pelanggaran_siswa.php" class="btn btn-primary" style="font-size: 0.8rem; padding: 8px 20px;">Lihat Semua</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Jenis</th>
                        <th>Poin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($recent_result)): ?>
                    <tr>
                        <td style="font-size: 0.85rem; color: #666;"><?= date('d M Y, H:i', strtotime($row['tanggal'])) ?></td>
                        <td style="font-weight: 600;"><?= htmlspecialchars($row['nama_siswa']) ?></td>
                        <td><span class="badge" style="background: rgba(150, 126, 118, 0.1); color: var(--mocha);"><?= htmlspecialchars($row['jenis']) ?></span></td>
                        <td style="font-weight: 700; color: var(--danger);">+<?= $row['poin'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if(mysqli_num_rows($recent_result) == 0): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: #888;">Belum ada aktivitas hari ini.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="glass-card" style="background: var(--onyx); color: white;">
        <h3 style="font-weight: 700; margin-bottom: 20px; color: var(--cream);">Aksi Cepat</h3>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <a href="<?= BASE_URL ?>/pages/entri_pelanggaran/list.php" class="btn" style="background: rgba(255,255,255,0.1); color: white; display: flex; align-items: center; justify-content: space-between;">
                Catat Pelanggaran <i data-lucide="plus-circle" style="width: 18px;"></i>
            </a>
            <a href="<?= BASE_URL ?>/pages/siswa/list.php" class="btn" style="background: rgba(255,255,255,0.1); color: white; display: flex; align-items: center; justify-content: space-between;">
                Cari Data Siswa <i data-lucide="search" style="width: 18px;"></i>
            </a>
            <a href="<?= BASE_URL ?>/pages/laporan/laporan_rekaptulasi.php" class="btn" style="background: rgba(255,255,255,0.1); color: white; display: flex; align-items: center; justify-content: space-between;">
                Cetak Rekapitulasi <i data-lucide="printer" style="width: 18px;"></i>
            </a>
        </div>
        
        <div style="margin-top: 40px; padding: 20px; background: rgba(231, 171, 121, 0.1); border-radius: 15px; border: 1px solid rgba(231, 171, 121, 0.2);">
            <h4 style="font-size: 0.85rem; text-transform: uppercase; color: var(--accent); margin-bottom: 10px;">Tips</h4>
            <p style="font-size: 0.8rem; color: rgba(255,255,255,0.7);">Gunakan menu Laporan untuk mencetak surat panggilan orang tua secara otomatis berdasarkan total poin siswa.</p>
        </div>
    </div>
</div>

<script>
    // Refresh icons for dynamic content if any
    lucide.createIcons();
</script>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
