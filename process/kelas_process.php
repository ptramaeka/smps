<?php
include_once __DIR__ . '/../config/config.php';
// Menentukan path utama proyek agar mudah memanggil file lain
include_once __DIR__ . "/../config/config.php";

// Mengecek apakah permintaan berasal dari metode POST (bukan GET)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $action = $_POST['action'];
    
    // Ambil ID dari form
    $id_kelas = isset($_POST['id_kelas']) ? $_POST['id_kelas'] : '';
    
    // Proses tambah data kelas
    if($action == 'add'){
        $tingkat = $_POST['tingkat'];
        $program_keahlian = $_POST['program_keahlian'];
        $rombel = $_POST['rombel'];
        $kode_guru = $_POST['kode_guru'];
        
        // Validasi: Cek apakah kode_guru ada di tabel guru
        $cek_guru = mysqli_query($conn, "SELECT kode_guru FROM guru WHERE kode_guru = '$kode_guru' AND aktif = 'Y'");
        if(mysqli_num_rows($cek_guru) == 0){
            echo "<script>alert('Kode guru tidak valid atau guru tidak aktif!'); window.location.href = '../pages/kelas/add.php';</script>";
            exit;
        }
        
        // Ambil id_tingkat dari tabel tingkat
        $tingkat_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_tingkat FROM tingkat WHERE tingkat = '$tingkat'"));
        $id_tingkat = $tingkat_data['id_tingkat'];
        
        // Ambil id_program_keahlian dari tabel program_keahlian
        $program_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_program_keahlian FROM program_keahlian WHERE program_keahlian = '$program_keahlian'"));
        $id_program_keahlian = $program_data['id_program_keahlian'];
        
        // Insert data ke tabel kelas
        $query = mysqli_query($conn, "INSERT INTO kelas (id_tingkat, id_program_keahlian, rombel, kode_guru) VALUES ('$id_tingkat', '$id_program_keahlian', '$rombel', '$kode_guru')");
        if($query){
            echo "<script>alert('Berhasil Menambah Data Kelas'); window.location.href = '../pages/kelas/list.php';</script>";
        }else{
            echo "<script>alert('Gagal Menambah Data Kelas'); window.location.href = '../pages/kelas/add.php';</script>";
        }
    } elseif ($action == 'edit') {
        
        $tingkat = $_POST['tingkat'];
        $program_keahlian = $_POST['program_keahlian'];
        $rombel = $_POST['rombel'];
        $kode_guru = $_POST['kode_guru'];
        
        // Ambil id_tingkat dari tabel tingkat
        $tingkat_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_tingkat FROM tingkat WHERE tingkat = '$tingkat'"));
        $id_tingkat = $tingkat_data['id_tingkat'];
        
        // Ambil id_program_keahlian dari tabel program_keahlian
        $program_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_program_keahlian FROM program_keahlian WHERE program_keahlian = '$program_keahlian'"));
        $id_program_keahlian = $program_data['id_program_keahlian'];
        
        // Update data kelas
        $query_kelas = "UPDATE kelas SET 
            id_tingkat = '$id_tingkat',
            id_program_keahlian = '$id_program_keahlian',
            rombel = '$rombel',
            kode_guru = '$kode_guru'
            WHERE id_kelas = '$id_kelas'
        ";
        
        if (mysqli_query($conn, $query_kelas)) {
            $_SESSION['success'] = "Data kelas berhasil diupdate!";
            header("Location: ../pages/kelas/list.php");
        } else {
            echo "Gagal mengupdate data kelas: " . mysqli_error($conn);
        }
    } elseif ($action == 'delete') {

        // Hapus data kelas (tombol delete hanya muncul jika aman)
        $query_delete = mysqli_query($conn, "DELETE FROM kelas WHERE id_kelas='$id_kelas'");
        
        if ($query_delete) {
            echo "<script>alert('Data kelas berhasil dihapus!'); window.location.href = '../pages/kelas/list.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus data kelas: " . mysqli_error($conn) . "'); window.location.href = '../pages/kelas/list.php';</script>";
        }
    }

}
?>
