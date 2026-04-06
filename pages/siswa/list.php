<?php
include_once __DIR__ . '/../../config/config.php';
include ROOT_PATH . "/includes/header.php";

$_can_manage = smps_can_manage_data();

// Query siswa AKTIF
$result_aktif = mysqli_query($conn, "
    SELECT siswa.nis, siswa.nama_siswa, siswa.jenis_kelamin, siswa.alamat, siswa.status,
           kelas.rombel, tingkat.tingkat, program_keahlian.program_keahlian, guru.nama_pengguna,
           ortu_wali.ayah, ortu_wali.ibu, ortu_wali.wali
    FROM siswa 
    JOIN ortu_wali ON siswa.id_ortu_wali = ortu_wali.id_ortu_wali
    JOIN kelas ON siswa.id_kelas = kelas.id_kelas
    JOIN tingkat ON kelas.id_tingkat = tingkat.id_tingkat
    JOIN program_keahlian ON kelas.id_program_keahlian = program_keahlian.id_program_keahlian
    JOIN guru ON kelas.kode_guru = guru.kode_guru
    WHERE siswa.status = 'aktif'
");

// Query siswa NON-AKTIF
$result_nonaktif = mysqli_query($conn, "
    SELECT siswa.nis, siswa.nama_siswa, siswa.jenis_kelamin, siswa.alamat, siswa.status,
           kelas.rombel, tingkat.tingkat, program_keahlian.program_keahlian, guru.nama_pengguna
    FROM siswa 
    JOIN kelas ON siswa.id_kelas = kelas.id_kelas
    JOIN tingkat ON kelas.id_tingkat = tingkat.id_tingkat
    JOIN program_keahlian ON kelas.id_program_keahlian = program_keahlian.id_program_keahlian
    JOIN guru ON kelas.kode_guru = guru.kode_guru
    WHERE siswa.status != 'aktif'
");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 700; color: var(--mocha);">Data Siswa</h2>
        <p style="color: #888;">Kelola data murid, kelas, dan informasi orang tua.</p>
</div>
    <?php if ($_can_manage): ?>
        <a href="add.php" class="btn btn-primary">
            <i data-lucide="user-plus" style="width: 18px; vertical-align: middle; margin-right: 5px;"></i>
            Tambah Siswa
        </a>
    <?php endif; ?>
</div>

<!-- Siswa Aktif -->
<div class="glass-card" style="margin-bottom: 40px;">
    <h3 style="margin-bottom: 20px; font-weight: 600;">Siswa Aktif</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>JK</th>
                    <th>Kelas</th>
                    <th>Wali Kelas</th>
                    <th>Orang Tua</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($result_aktif)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td style="font-weight: 600; color: var(--mocha);"><?= htmlspecialchars($row['nis']) ?></td>
                    <td style="font-weight: 600;"><?= htmlspecialchars($row['nama_siswa']) ?></td>
                    <td><?= $row['jenis_kelamin'] == 'Laki - Laki' ? 'L' : 'P' ?></td>
                    <td><span class="badge badge-role" style="background: rgba(150, 126, 118, 0.1); color: var(--mocha);"><?= htmlspecialchars($row['tingkat'].' '.$row['rombel']) ?></span></td>
                    <td style="font-size: 0.85rem;"><?= htmlspecialchars($row['nama_pengguna']) ?></td>
                    <td style="font-size: 0.85rem; color: #666;">
                        A: <?= htmlspecialchars($row['ayah'] ?: '-') ?><br>
                        I: <?= htmlspecialchars($row['ibu'] ?: '-') ?>
                    </td>
                    <td>
                        <?php if ($_can_manage): ?>
                            <div style="display: flex; gap: 5px;">
                                <a href="edit.php?nis=<?= $row['nis'] ?>" class="btn" style="background: rgba(150, 126, 118, 0.1); color: var(--mocha); padding: 5px 12px;">
                                    <i data-lucide="edit-3" style="width: 14px;"></i>
                                </a>
                                <form action="<?= BASE_URL ?>/process/siswa_process.php" method="post" onsubmit="return confirm('Hapus <?= $row['nama_siswa'] ?>?')">
                                    <input type="hidden" name="nis" value="<?= $row['nis'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn" style="background: rgba(207, 69, 69, 0.1); color: var(--danger); padding: 5px 12px;">
                                        <i data-lucide="trash-2" style="width: 14px;"></i>
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <span style="color: #888;">Read-only</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($result_aktif) == 0): ?>
                <tr><td colspan="8" style="text-align: center; padding: 30px;">Tidak ada siswa aktif.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Siswa Non-Aktif -->
<div class="glass-card">
    <h3 style="margin-bottom: 20px; font-weight: 600; color: #888;">Alumni & Siswa Pindah</h3>
    <div class="table-container">
        <table style="opacity: 0.8;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Kelas Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($result_nonaktif)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nis']) ?></td>
                    <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                    <td><span class="badge" style="background: #eee; color: #888;"><?= htmlspecialchars($row['status']) ?></span></td>
                    <td><?= htmlspecialchars($row['tingkat'].' '.$row['program_keahlian'].' '.$row['rombel']) ?></td>
                    <td>
                        <?php if ($_can_manage): ?>
                            <div style="display: flex; gap: 5px;">
                                <a href="edit.php?nis=<?= $row['nis'] ?>" class="btn" style="background: #eee; color: #888; padding: 5px 12px;">
                                    <i data-lucide="edit-2" style="width: 14px;"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <span style="color: #888;">Read-only</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($result_nonaktif) == 0): ?>
                <tr><td colspan="6" style="text-align: center; padding: 30px;">Tidak ada data siswa non-aktif.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>lucide.createIcons();</script>

<?php include ROOT_PATH . "/includes/footer.php"; ?>
