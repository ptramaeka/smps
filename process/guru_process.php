<?php
include_once __DIR__ . '/../config/config.php';
// Menentukan path utama proyek agar mudah memanggil file lain
include_once __DIR__ . "/../config/config.php";

smps_require_login();
smps_require_roles(['admin'], 'Akses ditolak. Hanya admin yang dapat mengelola data guru.');

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
        if ($role === 'guru') {
            $role = 'pengajar';
        }

        $role_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM roles WHERE name = '$role'"));
        if (!$role_row) {
            echo "Role tidak valid.";
            exit;
        }
        $role_id = $role_row['id'];
        
        $query = mysqli_query($conn, "INSERT INTO guru (kode_guru, nama_pengguna, role, role_id, username, password, aktif, jabatan, telp) VALUES ('$kode_guru', '$nama_guru', '$role', '$role_id', '$username', '$password_input', 'Y', '$jabatan', '$telp')");
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
        if ($role === 'guru') {
            $role = 'pengajar';
        }
        $aktif = $_POST['aktif'];

        $role_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM roles WHERE name = '$role'"));
        if (!$role_row) {
            echo "Role tidak valid.";
            exit;
        }
        $role_id = $role_row['id'];

        // ========================
        // 1. Update tabel guru
        // ========================
        $query_guru = "UPDATE guru SET 
            username = '$username',
            jabatan = '$jabatan',
            telp = '$telp',
            role = '$role',
            role_id = '$role_id',
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
                role_id = '$role_id',
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
