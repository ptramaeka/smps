<?php
include_once __DIR__ . '/../config/config.php';
// Menentukan path utama proyek agar mudah memanggil file lain
include_once __DIR__ . "/../config/config.php";

// Mengecek apakah permintaan berasal dari metode POST (bukan GET)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $action = $_POST['action'];
    $kode_guru = $_POST['kode_guru'];
    
    // Proses tambah data guru
    if($action == 'add'){
        $nama_guru = $_POST['nama_guru'];
        $username = $_POST['username'];
        $jabatan = $_POST['jabatan'];
        $telp = $_POST['telp'];
        $password_input = password_hash("Guru12345*!", PASSWORD_DEFAULT);
        $role = $_POST["role"];
        
        $query = mysqli_query($conn, "INSERT INTO guru (kode_guru, nama_pengguna, role, username, password, aktif, jabatan, telp) VALUES ('$kode_guru', '$nama_guru', '$role', '$username', '$password_input', 'Y', '$jabatan', '$telp')");
        if($query){
            header("Location: ../pages/guru/list.php");
        }else{
            echo "Gagal Menambah Data Guru";
        }
    } elseif ($action == 'edit') {

        $username = $_POST['username'];
        $password = $_POST['password'];
        $jabatan = $_POST['jabatan'];
        $telp = $_POST['telp'];
        $role = $_POST['role'];
        $aktif = $_POST['aktif'];

        // ========================
        // 1. Update tabel guru
        // ========================
        $query_guru = "UPDATE guru SET 
            username = '$username',
            jabatan = '$jabatan',
            telp = '$telp',
            role = '$role',
            aktif = '$aktif'
            WHERE kode_guru = '$kode_guru'
        ";
        
        // Update password hanya jika diisi
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $query_guru = "UPDATE guru SET 
                username = '$username',
                password = '$password_hash',
                jabatan = '$jabatan',
                telp = '$telp',
                role = '$role',
                aktif = '$aktif'
                WHERE kode_guru = '$kode_guru'
            ";
        }
        
        if (mysqli_query($conn, $query_guru)) {
            $_SESSION['success'] = "Data guru berhasil diupdate!";
            header("Location: ../pages/guru/list.php");
        } else {
            echo "Gagal mengupdate data guru: " . mysqli_error($conn);
        }
    }

}
?>
