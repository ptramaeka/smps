<?php
// Tentukan protokol (http atau https)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host_name = $_SERVER['HTTP_HOST'];

// Tentukan BASE_URL proyek (misal: http://localhost/Poin_Pelanggaran_Siswa)
// Sesuaikan '/Poin_Pelanggaran_Siswa' jika nama folder proyek berubah
define('BASE_URL', $protocol . '://' . $host_name . '/Poin_Pelanggaran_Siswa');

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
?>
