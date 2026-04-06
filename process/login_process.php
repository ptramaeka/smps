<?php
include_once __DIR__ . '/../config/config.php';

$username = $_POST["username"];
$password_hash = $_POST["password"];

$query_guru = mysqli_query($conn, "
    SELECT g.nama_pengguna, g.username, g.password, COALESCE(r.name, g.role) AS role_name
    FROM guru g
    LEFT JOIN roles r ON r.id = g.role_id
    WHERE g.username = '$username'
");
$query_siswa = mysqli_query($conn, "
    SELECT s.nis, s.nama_siswa, s.password, COALESCE(r.name, 'siswa') AS role_name
    FROM siswa s
    LEFT JOIN roles r ON r.id = s.role_id
    WHERE s.nis = '$username'
");

if(mysqli_num_rows($query_guru) >= 1){
    $query_guru = mysqli_fetch_assoc($query_guru);
    if(password_verify($password_hash, $query_guru['password'])){
        $role = $query_guru['role_name'];
        if ($role === 'guru') {
            $role = 'pengajar';
        }
        setcookie("nama", $query_guru['nama_pengguna'], time() + 36000, '/');
        setcookie("username", $query_guru['username'], time() + 36000, '/');
        setcookie("role", $role, time() + 36000, '/');
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
        setcookie("role", $query_siswa['role_name'], time() + 36000, '/');
        header('Location: ../pages/dashboard.php');
        exit;
    }else{
        echo "Password Salah";
    }
}else{
    echo "Akun tidak ditemukan";
}
?>
