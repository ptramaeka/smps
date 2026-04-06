<?php
include_once __DIR__ . '/../config/config.php';

$username = $_POST["username"];
$password_hash = $_POST["password"];

$query_guru = mysqli_query($conn, "SELECT nama_pengguna, username, password, role FROM guru WHERE username = '$username'");
$query_siswa = mysqli_query($conn, "SELECT nis, nama_siswa, password FROM siswa WHERE nis = '$username'");

if(mysqli_num_rows($query_guru) >= 1){
    $query_guru = mysqli_fetch_assoc($query_guru);
    if(password_verify($password_hash, $query_guru['password'])){
        setcookie("nama", $query_guru['nama_pengguna'], time() + 36000, '/');
        setcookie("username", $query_guru['username'], time() + 36000, '/');
        setcookie("role", $query_guru['role'], time() + 36000, '/');
        header('Location: ../pages/dashboard.php');
        exit;
    }else{
        echo "Password Salah";
    }
}elseif(mysqli_num_rows($query_siswa) >= 1){
    $query_siswa = mysqli_fetch_assoc($query_siswa);
    if(password_verify($password_hash, $query_siswa['password'])){
        setcookie("nama", $query_siswa['nama_siswa'], time() + 36000, '/');
        setcookie("username", $query_siswa['nis'], time() + 36000, '/');
        setcookie("role", "siswa", time() + 36000, '/');
        header('Location: ../pages/dashboard.php');
        exit;
    }else{
        echo "Password Salah";
    }
}else{
    echo "Akun tidak ditemukan";
}
?>
