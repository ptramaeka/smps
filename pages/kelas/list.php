<?php
include_once __DIR__ . '/../../config/config.php';
include ROOT_PATH . "/includes/header.php";

// Mengambil semua data kelas dari tabel 'kelas' 
$result = mysqli_query($conn, "SELECT kelas.id_kelas, tingkat.tingkat, program_keahlian.program_keahlian, kelas.rombel, guru.nama_pengguna FROM kelas JOIN tingkat ON kelas.id_tingkat = tingkat.id_tingkat JOIN program_keahlian ON kelas.id_program_keahlian = program_keahlian.id_program_keahlian LEFT JOIN guru ON kelas.kode_guru = guru.kode_guru ORDER BY kelas.id_tingkat DESC, kelas.id_program_keahlian ASC, kelas.rombel ASC");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 700; color: var(--mocha);">Data Kelas</h2>
        <p style="color: #888;">Kelola rombongan belajar dan penempatan wali kelas.</p>
    </div>
    <a href="add.php" class="btn btn-primary">
        <i data-lucide="plus" style="width: 18px; vertical-align: middle; margin-right: 5px;"></i>
        Tambah Kelas
    </a>
</div>

<div class="glass-card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kelas</th>
                    <th>Wali Kelas</th>
                    <th>Guru BK Pelaksana</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td style="font-weight: 600; color: var(--mocha);"><?= htmlspecialchars($row['tingkat'] . ' ' . $row['program_keahlian'] . ' ' . $row['rombel'] ) ?></td>
                    <td><?= htmlspecialchars($row['nama_pengguna']) ?></td>
                    <td>
                        <span class="badge" style="background: rgba(30, 30, 30, 0.05); color: var(--onyx);">
                        <?php
                        if( $row['tingkat'] == 'XII'){
                            $row2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_pengguna FROM guru WHERE jabatan = 'Guru BK XII'"));
                            echo htmlspecialchars($row2['nama_pengguna']);
                        }else if( $row['tingkat'] == 'XI'){
                            $row2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_pengguna FROM guru WHERE jabatan = 'Guru BK XI'"));
                            echo htmlspecialchars($row2['nama_pengguna']);
                        }else{
                            $row2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_pengguna FROM guru WHERE jabatan = 'Guru BK X'"));
                            echo htmlspecialchars($row2['nama_pengguna']);
                        }
                        ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="edit.php?id=<?= $row['id_kelas'] ?>" class="btn" style="background: rgba(150, 126, 118, 0.1); color: var(--mocha); padding: 5px 12px;">
                                <i data-lucide="edit-3" style="width: 14px;"></i>
                            </a>
                            <?php
                            $cek_siswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa WHERE id_kelas = '" . $row['id_kelas'] . "'");
                            $data_siswa = mysqli_fetch_assoc($cek_siswa);
                            
                            if ($data_siswa['total'] == 0): ?>
                            <form action="<?= BASE_URL ?>/process/kelas_process.php" method="post" onsubmit="return confirm('Hapus kelas?')">
                                <input type="hidden" name="id_kelas" value="<?= $row['id_kelas'] ?>">
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
