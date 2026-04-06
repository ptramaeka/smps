<?php
// Cek autentikasi dasar
smps_require_login();

$role = smps_get_role();
$current_url = $_SERVER['PHP_SELF'];

$can_view_data = ($role !== 'siswa');
$can_view_data_guru = ($role === 'admin');
$can_view_reports = in_array($role, ['admin', 'bk', 'pengajar'], true);
$can_view_entry = ($role !== 'siswa');

// Validasi role yang dikenal
$known_roles = ['admin', 'siswa', 'bk', 'pengajar'];
if (!in_array($role, $known_roles, true)) {
    smps_deny_access('Role tidak dikenali.', BASE_URL . '/login.php');
}

// SISTEM PENCEGAHAN AKSES HALAMAN BERDASARKAN ROLE
// Tentukan path/folder URL apa saja yang tidak boleh diakses oleh masing-masing role
$allowlist_pages = [
    // Siswa hanya boleh melihat dashboard
    'siswa' => [
        '/pages/dashboard.php'
    ]
];

if (isset($allowlist_pages[$role])) {
    $is_allowed = false;
    foreach ($allowlist_pages[$role] as $allowed) {
        if (strpos($current_url, $allowed) !== false) {
            $is_allowed = true;
            break;
        }
    }
    if (!$is_allowed) {
        if ($role === 'siswa') {
            smps_deny_access("Akses Ditolak: Role ($role) hanya dapat mengakses dashboard.", BASE_URL . '/pages/dashboard.php');
        } else {
            smps_deny_access("Akses Ditolak: Role ($role) tidak memiliki izin untuk halaman ini!");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Poin Pelanggaran Siswa</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Central Design System -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    
    <style>
        /* Small override for print since it's hard to move to external CSS for now */
        @media print {
            .no-print { display: none !important; }
            main { padding: 0 !important; margin: 0 !important; }
            .page { box-shadow: none !important; border: none !important; width: 100% !important; margin: 0 !important; }
        }
    </style>
</head>

<body>
    <!-- Floating Navigation Bar -->
    <header class="no-print">
        <nav class="floating-nav">
            <div class="nav-brand">
                <h1>POIN.SISWA</h1>
            </div>
            
            <ul>
                <li><a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a></li>
                
                <?php if ($can_view_data): ?>
                <li class="dropdown">
                    <a href="#">Data <i data-lucide="chevron-down" style="width: 14px; display: inline-block; vertical-align: middle;"></i></a>
                    <div class="dropdown-content">
                        <?php if ($can_view_data_guru): ?>
                        <a href="<?= BASE_URL ?>/pages/guru/list.php">Data Guru</a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/pages/kelas/list.php">Data Kelas</a>
                        <a href="<?= BASE_URL ?>/pages/siswa/list.php">Data Siswa</a>
                        <a href="<?= BASE_URL ?>/pages/jenis_pelanggaran/list.php">Jenis Pelanggaran</a>
                    </div>
                </li>
                <?php endif; ?>
                
                <?php if ($can_view_entry): ?>
                <li><a href="<?= BASE_URL ?>/pages/entri_pelanggaran/list.php">Entri Pelanggaran</a></li>
                <?php endif; ?>
                
                <?php if ($can_view_reports): ?>
                <li class="dropdown">
                    <a href="#">Laporan <i data-lucide="chevron-down" style="width: 14px; display: inline-block; vertical-align: middle;"></i></a>
                    <div class="dropdown-content">
                        <a href="<?= BASE_URL ?>/pages/laporan/pelanggaran_siswa.php">Pelanggaran Siswa</a>
                        <a href="<?= BASE_URL ?>/pages/laporan/laporan_panggilan_ortu.php">Surat Panggilan</a>
                        <a href="<?= BASE_URL ?>/pages/laporan/laporan_perjanjian.php">Surat Perjanjian</a>
                        <a href="<?= BASE_URL ?>/pages/laporan/laporan_pindah.php">Surat Pindah</a>
                        <a href="<?= BASE_URL ?>/pages/laporan/laporan_rekaptulasi.php">Rekapitulasi</a>
                    </div>
                </li>
                <?php endif; ?>
                <li class="dropdown dropdown-right">
                    <a href="#" style="background: var(--mocha); color: white; padding: 10px 20px;">
                        <i data-lucide="user" style="width: 16px; margin-right: 5px;"></i>
                        <?= htmlspecialchars($_COOKIE['nama']) ?>
                        <span class="badge badge-role" style="margin-left: 8px;"><?= htmlspecialchars($role) ?></span>
                    </a>
                    <div class="dropdown-content">
                        <a href="#" onclick="alert('Fitur Edit Profil akan segera hadir!')"><i data-lucide="settings" style="width: 14px;"></i> Edit Profil</a>
                        <a href="<?= BASE_URL ?>/logout.php" style="color: var(--danger);"><i data-lucide="log-out" style="width: 14px;"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <main>
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
    </script>


        <!-- 
    💡 Penjelasan ringkas struktur HTML-nya:
	•	<!DOCTYPE html> → Menentukan dokumen ini memakai standar HTML5.
	•	<html lang="id"> → Bahasa halaman adalah bahasa Indonesia.
	•	<head> → Bagian kepala, berisi pengaturan halaman (judul, karakter, style).
	•	<body> → Bagian isi tampilan halaman.
	•	<header> → Bagian atas, biasanya berisi judul dan menu navigasi.
	•	<nav> → Area navigasi untuk berpindah ke halaman lain.
	•	<ul> dan <li> → Menyusun daftar menu.
	•	<main> → Area utama yang nanti berisi konten dari halaman lain. 
    -->
