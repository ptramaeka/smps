<?php
include_once __DIR__ . '/../config/config.php';
// Menentukan path utama proyek agar mudah memanggil file lain
include_once __DIR__ . "/../config/config.php";

smps_require_login();
smps_require_roles(['admin', 'bk'], 'Akses ditolak. Hanya admin dan BK yang dapat menambah pelanggaran.');

// Mengecek apakah permintaan berasal dari metode POST (bukan GET)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $action = $_POST['action'];
    $nama_pelanggaran = $_POST['jenis_pelanggaran']; // Variabel pertama: nama dari form
    
    if ($action == 'add') {

        // Ambil data dari form
        $nis = $_POST['nis'];                     // NIS siswa
        $keterangan = $_POST['keterangan'];       // Keterangan pelanggaran
        
        // Variabel kedua: Cari ID berdasarkan nama dari variabel pertama
        $query_id = mysqli_query($conn, "SELECT id_jenis_pelanggaran FROM jenis_pelanggaran WHERE jenis = '$nama_pelanggaran'");
        $data_id = mysqli_fetch_assoc($query_id);
        $id_jenis_pelanggaran = $data_id['id_jenis_pelanggaran']; // Variabel kedua: ID dari nama
        
        // Insert data pelanggaran ke database
        $query_pelanggaran = "INSERT INTO pelanggaran_siswa (tanggal, nis, id_jenis_pelanggaran, keterangan) 
        VALUES (NOW(), '$nis', '$id_jenis_pelanggaran', '$keterangan')";
        
        // Eksekusi query
        if (mysqli_query($conn, $query_pelanggaran)) {
            echo "<script>alert('Entri berhasil ditambahkan!'); window.location.href='" . BASE_URL . "/pages/laporan/detail_pelanggaran.php?nis=" . urlencode($nis) . "';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan entri!'); window.history.back();</script>";
        }

    }
}
?>  
