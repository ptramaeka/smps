<?php
include_once __DIR__ . '/../../config/config.php';
include ROOT_PATH . "/includes/header.php";

// Query untuk mengambil siswa dengan total poin 50+ yang BELUM dicetak suratnya
$result = mysqli_query($conn, "
    SELECT 
        siswa.nis, 
        siswa.nama_siswa, 
        tingkat.tingkat, 
        program_keahlian.program_keahlian, 
        kelas.rombel,
        SUM(jenis_pelanggaran.poin) as total_poin,
        COUNT(pelanggaran_siswa.id_pelanggaran_siswa) as jumlah_pelanggaran
    
    FROM siswa
    JOIN pelanggaran_siswa ON siswa.nis = pelanggaran_siswa.nis
    JOIN jenis_pelanggaran ON pelanggaran_siswa.id_jenis_pelanggaran = jenis_pelanggaran.id_jenis_pelanggaran
    JOIN kelas ON siswa.id_kelas = kelas.id_kelas
    JOIN program_keahlian ON kelas.id_program_keahlian = program_keahlian.id_program_keahlian
    JOIN tingkat ON kelas.id_tingkat = tingkat.id_tingkat
    
    WHERE siswa.nis NOT IN (
        SELECT DISTINCT nis FROM surat_keluar 
        WHERE jenis_surat = 'Panggilan Orang Tua'
    )
    
    GROUP BY siswa.nis, siswa.nama_siswa, tingkat.tingkat, program_keahlian.program_keahlian, kelas.rombel
    
    HAVING SUM(jenis_pelanggaran.poin) BETWEEN 50 AND 99
    
    ORDER BY total_poin DESC
");

// Query untuk mengambil surat yang sudah dicetak
$surat_sudah = mysqli_query($conn, "
    SELECT 
        surat_keluar.*, 
        siswa.nama_siswa, 
        tingkat.tingkat, 
        program_keahlian.program_keahlian, 
        kelas.rombel
    
    FROM surat_keluar
    JOIN siswa ON surat_keluar.nis = siswa.nis
    JOIN kelas ON siswa.id_kelas = kelas.id_kelas
    JOIN program_keahlian ON kelas.id_program_keahlian = program_keahlian.id_program_keahlian
    JOIN tingkat ON kelas.id_tingkat = tingkat.id_tingkat
    
    WHERE surat_keluar.jenis_surat = 'Panggilan Orang Tua'
    
    ORDER BY surat_keluar.tanggal_pembuatan_surat DESC
");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 700; color: var(--mocha);">Pemanggilan Orang Tua</h2>
        <p style="color: #888;">Kelola administrasi panggilan orang tua berdasarkan akumulasi poin siswa.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="<?= BASE_URL ?>/pages/cetak/add_panggilan_ortu.php" class="btn btn-primary">
            <i data-lucide="plus" style="width: 18px; vertical-align: middle; margin-right: 5px;"></i>
            Cetak Surat Baru
        </a>
    </div>
</div>

<!-- Perlu Dipanggil -->
<div class="glass-card" style="margin-bottom: 40px;">
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
        <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--danger);"></div>
        <h3 style="font-weight: 600;">Siswa yang Perlu Dipanggil (Poin 50 - 99)</h3>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Identitas Siswa</th>
                    <th>Kelas</th>
                    <th>Pelanggaran</th>
                    <th>Total Poin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <div style="font-weight: 700; color: var(--mocha);"><?= htmlspecialchars($row['nis']) ?></div>
                        <div><?= htmlspecialchars($row['nama_siswa']) ?></div>
                    </td>
                    <td><span class="badge" style="background: rgba(150, 126, 118, 0.1); color: var(--mocha);"><?= htmlspecialchars($row['tingkat'].' '.$row['rombel']) ?></span></td>
                    <td><?= htmlspecialchars($row['jumlah_pelanggaran']) ?> kali</td>
                    <td>
                        <div style="font-size: 1.2rem; font-weight: 700; color: var(--danger);"><?= htmlspecialchars($row['total_poin']) ?></div>
                    </td>
                    <td>
                        <form action="<?= BASE_URL ?>/pages/cetak/add_panggilan_ortu.php" method="post">
                            <input type="hidden" name="nis" value="<?= $row['nis'] ?>">
                            <button type="submit" class="btn btn-primary" style="padding: 8px 15px; font-size: 0.8rem;">Cetak Panggilan</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($result) == 0): ?>
                <tr><td colspan="6" style="text-align: center; padding: 30px; color: #888;">Tidak ada siswa dalam ambang batas pemanggilan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Riwayat Surat -->
<div class="glass-card">
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
        <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--mocha);"></div>
        <h3 style="font-weight: 600;">Riwayat Surat Terbit</h3>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No Surat</th>
                    <th>Tanggal Terbit</th>
                    <th>Siswa</th>
                    <th>Jadwal Panggilan</th>
                    <th>Keperluan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                while ($surat = mysqli_fetch_assoc($surat_sudah)): ?>
                <tr>
                    <td style="font-weight: 600; font-size: 0.85rem;"><?= htmlspecialchars($surat['no_surat']) ?></td>
                    <td style="font-size: 0.85rem; color: #666;"><?= date('d M Y', strtotime($surat['tanggal_pembuatan_surat'])) ?></td>
                    <td>
                        <div style="font-weight: 600;"><?= htmlspecialchars($surat['nama_siswa']) ?></div>
                        <div style="font-size: 0.8rem; color: #888;"><?= htmlspecialchars($surat['nis']) ?></div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--mocha);"><?= date('d M Y', strtotime($surat['tanggal_pemanggilan'])) ?></div>
                        <div style="font-size: 0.8rem;"><?= date('H:i', strtotime($surat['tanggal_pemanggilan'])) ?> WIB</div>
                    </td>
                    <td style="font-size: 0.85rem;"><?= htmlspecialchars($surat['keperluan']) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/pages/cetak/surat_panggilan_ortu.php?no_surat=<?= urlencode($surat['no_surat']) ?>" class="btn" style="background: var(--onyx); color: white; padding: 6px 12px; font-size: 0.75rem;">
                            <i data-lucide="printer" style="width: 12px; vertical-align: middle; margin-right: 4px;"></i> Cetak Ulang
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($surat_sudah) == 0): ?>
                <tr><td colspan="6" style="text-align: center; padding: 30px; color: #888;">Belum ada riwayat surat panggilan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>lucide.createIcons();</script>

<?php include ROOT_PATH . "/includes/footer.php"; ?>
