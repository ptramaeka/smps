<?php
include_once __DIR__ . '/../../config/config.php';
include ROOT_PATH . "/includes/header.php";

// Mengambil semua data guru dari tabel 'guru' 
$result = mysqli_query($conn, "SELECT * FROM guru WHERE aktif = 'Y'");
$result_nonaktif = mysqli_query($conn, "SELECT * FROM guru WHERE aktif = 'N'");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 700; color: var(--mocha);">Data Guru</h2>
        <p style="color: #888;">Kelola data guru pengajar dan staf sekolah.</p>
    </div>
    <a href="add.php" class="btn btn-primary">
        <i data-lucide="plus" style="width: 18px; vertical-align: middle; margin-right: 5px;"></i>
        Tambah Guru
    </a>
</div>

<!-- Guru Aktif -->
<div class="glass-card" style="margin-bottom: 40px;">
    <h3 style="margin-bottom: 20px; font-weight: 600;">Guru Aktif</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Guru</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Jabatan</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td style="font-weight: 600; color: var(--mocha);"><?= htmlspecialchars($row['kode_guru']) ?></td>
                    <td><?= htmlspecialchars($row['nama_pengguna']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><span class="badge badge-role"><?= htmlspecialchars($row['jabatan']) ?></span></td>
                    <td><?= htmlspecialchars($row['telp']) ?></td>
                    <td>
                        <a href="edit.php?kode_guru=<?= $row['kode_guru'] ?>" class="btn" style="background: rgba(150, 126, 118, 0.1); color: var(--mocha); padding: 5px 15px;">
                            <i data-lucide="edit-3" style="width: 14px;"></i> Edit
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($result) == 0): ?>
                <tr><td colspan="7" style="text-align: center; padding: 30px;">Data tidak ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Guru Non-Aktif -->
<div class="glass-card">
    <h3 style="margin-bottom: 20px; font-weight: 600; color: #888;">Guru Non-Aktif</h3>
    <div class="table-container">
        <table style="opacity: 0.7;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Guru</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Jabatan</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($result_nonaktif)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['kode_guru']) ?></td>
                    <td><?= htmlspecialchars($row['nama_pengguna']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['jabatan']) ?></td>
                    <td><?= htmlspecialchars($row['telp']) ?></td>
                    <td>
                        <a href="edit.php?kode_guru=<?= $row['kode_guru'] ?>" class="btn" style="background: #eee; color: #888; padding: 5px 15px;">
                            <i data-lucide="edit-3" style="width: 14px;"></i> Edit
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($result_nonaktif) == 0): ?>
                <tr><td colspan="7" style="text-align: center; padding: 30px;">Tidak ada data guru non-aktif.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>lucide.createIcons();</script>

<?php include ROOT_PATH . "/includes/footer.php"; ?>
