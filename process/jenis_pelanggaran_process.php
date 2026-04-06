<?php
include_once __DIR__ . '/../config/config.php';
// Menentukan path utama proyek agar mudah memanggil file lain
include_once __DIR__ . "/../config/config.php";

// Mengecek apakah permintaan berasal dari metode POST (bukan GET)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $action = $_POST['action'];
    
    // Ambil ID dari form (semua form sekarang konsisten pakai id_jenis_pelanggaran)
    $id_pelanggaran = $_POST['id_jenis_pelanggaran'];
    
    // Proses tambah data jenis pelanggaran
    if($action == 'add'){
        $pelanggaran = $_POST['nama_pelanggaran'];
        $poin = $_POST['poin'];
        
        $query = mysqli_query($conn, "INSERT INTO jenis_pelanggaran (jenis, poin) VALUES ('$pelanggaran', '$poin')");
        if($query){
            echo "<script>alert('Berhasil Menambah Data Jenis Pelanggaran'); window.location.href = '../pages/jenis_pelanggaran/list.php';</script>";
        }else{
            echo "<script>alert('Gagal Menambah Data Jenis Pelanggaran'); window.location.href = '../pages/jenis_pelanggaran/add.php';</script>";
        }
    } elseif ($action == 'edit') {

        $nama_pelanggaran = $_POST['nama_pelanggaran'];
        $poin = $_POST['poin'];

        // ========================
        // 1. Update tabel jenis_pelanggaran
        // ========================
        $query_pelanggaran = "UPDATE jenis_pelanggaran SET 
            jenis = '$nama_pelanggaran',
            poin = '$poin'
            WHERE id_jenis_pelanggaran = '$id_pelanggaran'
        ";
        
        if (mysqli_query($conn, $query_pelanggaran)) {
            $_SESSION['success'] = "Data jenis pelanggaran berhasil diupdate!";
            header("Location: ../pages/jenis_pelanggaran/list.php");
        } else {
            echo "Gagal mengupdate data jenis pelanggaran: " . mysqli_error($conn);
        }
    } elseif ($action == 'delete') {

        // Hapus data utama
        mysqli_query($conn, "DELETE FROM jenis_pelanggaran WHERE id_jenis_pelanggaran='$id_pelanggaran'");
        
        $_SESSION['success'] = "Data jenis pelanggaran berhasil dihapus!";
        header("Location: ../pages/jenis_pelanggaran/list.php");
    }

}
?>
