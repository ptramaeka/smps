<?php
// Tentukan protokol (http atau https)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host_name = $_SERVER['HTTP_HOST'];

// Tentukan BASE_URL proyek (misal: http://localhost/smps)
// Sesuaikan '/smps' jika nama folder proyek berubah
define('BASE_URL', $protocol . '://' . $host_name . '/smps');

// Tentukan ROOTPATH (path absolut di server)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1') {
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "poin_pelanggaran_siswa";
} else {
    // Jika diakses dari HP atau laptop lain dalam satu jaringan, 
    // database biasanya tetap berada di server (localhost bagi PHP).
    $host = "localhost"; 
    $user = "root";
    $password = "";
    $database = "poin_pelanggaran_siswa";
}

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// -----------------------------
// Helper autentikasi & role
// -----------------------------
function smps_get_role() {
    if (!isset($_COOKIE['role'])) {
        return null;
    }

    // Normalisasi role legacy
    $role = $_COOKIE['role'];
    if ($role === 'guru') {
        $role = 'pengajar';
    }

    return $role;
}

function smps_require_login() {
    if (!isset($_COOKIE['username']) || !isset($_COOKIE['role'])) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function smps_deny_access($message = 'Akses ditolak.', $redirect = null) {
    $safe_message = addslashes($message);
    if ($redirect) {
        echo "<script>alert('{$safe_message}');window.location.href='" . $redirect . "';</script>";
    } else {
        echo "<script>alert('{$safe_message}');window.history.back();</script>";
    }
    exit;
}

function smps_require_roles(array $allowed_roles, $message = 'Akses ditolak.') {
    $role = smps_get_role();
    if ($role === null || !in_array($role, $allowed_roles, true)) {
        smps_deny_access($message, BASE_URL . '/login.php');
    }
}

function smps_is_admin() {
    return smps_get_role() === 'admin';
}

function smps_can_manage_data() {
    return smps_is_admin();
}

function smps_can_add_violation() {
    $role = smps_get_role();
    return in_array($role, ['admin', 'bk'], true);
}

function smps_can_print_reports() {
    $role = smps_get_role();
    return in_array($role, ['admin', 'bk'], true);
}
?>
