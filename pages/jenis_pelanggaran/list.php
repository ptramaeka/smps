<?php
include_once __DIR__ . '/../../config/config.php';
include ROOT_PATH . "/includes/header.php";

// Mengambil semua data jenis pelanggaran dari tabel 'jenis_pelanggaran'
$result = mysqli_query($conn, "SELECT * FROM jenis_pelanggaran");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 700; color: var(--mocha);">Jenis Pelanggaran</h2>
        <p style="color: #888;">Kelola klasifikasi pelanggaran dan bobot poin masing-masing.</p>
    </div>
    <a href="add.php" class="btn btn-primary">
        <i data-lucide="plus" style="width: 18px; vertical-align: middle; margin-right: 5px;"></i>
        Tambah Jenis
    </a>
</div>

<div class="glass-card" style="max-width: 900px; margin: 0 auto;">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelanggaran</th>
                    <th>Bobot Poin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td style="font-weight: 500;"><?= htmlspecialchars($row['jenis']) ?></td>
                    <td><span class="badge" style="background: rgba(207, 69, 69, 0.1); color: var(--danger); font-weight: 700;">+<?= htmlspecialchars($row['poin']) ?></span></td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="edit.php?id_jenis_pelanggaran=<?= $row['id_jenis_pelanggaran'] ?>" class="btn" style="background: rgba(150, 126, 118, 0.1); color: var(--mocha); padding: 5px 12px;">
                                <i data-lucide="edit-3" style="width: 14px;"></i>
                            </a>
                            <?php
                            $cek_penggunaan = mysqli_query($conn, "SELECT COUNT(*) as total FROM pelanggaran_siswa WHERE id_jenis_pelanggaran = '" . $row['id_jenis_pelanggaran'] . "'");
                            $data_penggunaan = mysqli_fetch_assoc($cek_penggunaan);
                            
                            if ($data_penggunaan['total'] == 0): ?>
                            <form action="<?= BASE_URL ?>/process/jenis_pelanggaran_process.php" method="post" onsubmit="return confirm('Hapus jenis pelanggaran?')">
                                <input type="hidden" name="id_jenis_pelanggaran" value="<?= $row['id_jenis_pelanggaran'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn" style="background: rgba(207, 69, 69, 0.1); color: var(--danger); padding: 5px 12px;">
                                    <i data-lucide="trash-2" style="width: 14px;"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>lucide.createIcons();</script>

<?php include ROOT_PATH . "/includes/footer.php"; ?>
