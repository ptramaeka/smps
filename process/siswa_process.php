<?php
include_once __DIR__ . '/../config/config.php';
// Menentukan path utama proyek agar mudah memanggil file lain
include_once __DIR__ . "/../config/config.php";

// Mengecek apakah permintaan berasal dari metode POST (bukan GET)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Mengambil data dari form
    $action = $_POST['action'];                             // Jenis aksi (add, edit, delete)
    $nis = $_POST['nis'];                                   // NIS siswa

    // Jika aksi adalah "add", maka tambahkan data siswa baru ke tabel
    if ($action == 'add') {

        $nama_siswa = $_POST['nama_siswa'];                     // Nama siswa
        $jenis_kelamin = $_POST['jenis_kelamin'];               // Jenis kelamin siswa
        $alamat = $_POST['alamat_siswa'];                       // Alamat siswa
        $kelas = $_POST['kelas'];                               // Kelas siswa

        //kode untuk memecah string kelas menjadi array (contoh "XII RPL 1" menjadi array ["XII", "RPL", "1"])
        $kelas = explode(" ", $kelas);
        $tingkat = $kelas[0];                                   //XII
        $program_keahlian = $kelas[1];                          //RPL
        $rombel = $kelas[2];                                    //1

        $query_kelas = mysqli_query($conn, "SELECT id_kelas FROM kelas JOIN program_keahlian USING(id_program_keahlian) JOIN tingkat USING(id_tingkat) WHERE tingkat = '$tingkat' AND program_keahlian = '$program_keahlian' AND rombel = '$rombel'");
        $id_kelas = mysqli_fetch_assoc($query_kelas)['id_kelas']; //mengambil id kelas

        $ayah = $_POST['ayah'];                                 // Ayah siswa
        $ibu = $_POST['ibu'];                                   // Ibu siswa
        $wali = $_POST['wali'];                                 // Wali siswa
        $tempat_lahir_ayah = $_POST['tempat_lahir_ayah'];       // Tempat lahir ayah
        $tempat_lahir_ibu = $_POST['tempat_lahir_ibu'];         // Tempat lahir ibu
        $tempat_lahir_wali = $_POST['tempat_lahir_wali'];       // Tempat lahir wali
        if($_POST['tanggal_lahir_ayah'] == ""){
            $tanggal_lahir_ayah = "NULL";
        }else{
            $tanggal_lahir_ayah = "'" . $_POST['tanggal_lahir_ayah'] . "'";     // Tanggal lahir ayah
        }
        if($_POST['tanggal_lahir_ibu'] == ""){
            $tanggal_lahir_ibu = "NULL";
        }else{
            $tanggal_lahir_ibu = "'" . $_POST['tanggal_lahir_ibu'] . "'";       // Tanggal lahir ibu
        }
        if($_POST['tanggal_lahir_wali'] == ""){
            $tanggal_lahir_wali = "NULL";
        }else{
            $tanggal_lahir_wali = "'" . $_POST['tanggal_lahir_wali'] . "'";     // Tanggal lahir wali
        }
        $pekerjaan_ayah = $_POST['pekerjaan_ayah'];             // Pekerjaan ayah
        $pekerjaan_ibu = $_POST['pekerjaan_ibu'];               // Pekerjaan ibu
        $pekerjaan_wali = $_POST['pekerjaan_wali'];             // Pekerjaan wali
        $telp_ayah = $_POST['telp_ayah'];                       // no telp ayah
        $telp_ibu = $_POST['telp_ibu'];                         // no telp ibu
        $telp_wali = $_POST['telp_wali'];                       // no telp wali
        $alamat_ayah = $_POST['alamat_ayah'];                   // Alamat ayah
        $alamat_ibu = $_POST['alamat_ibu'];                     // Alamat ibu
        $alamat_wali = $_POST['alamat_wali'];                   // Alamat wali

        // Insert data ortu_wali
        $query_ortu = "INSERT INTO ortu_wali VALUES (NULL, '$ayah', '$ibu', '$wali', '$tempat_lahir_ayah', '$tempat_lahir_ibu', '$tempat_lahir_wali', $tanggal_lahir_ayah, $tanggal_lahir_ibu, $tanggal_lahir_wali, '$pekerjaan_ayah', '$pekerjaan_ibu', '$pekerjaan_wali', '$telp_ayah', '$telp_ibu', '$telp_wali', '$alamat_ayah', '$alamat_ibu', '$alamat_wali')";
        // var_dump($query_ortu);
        mysqli_query($conn, $query_ortu);

        // Mengambil ID terakhir yang di-generate oleh tabel ortu_wali
        $id_ortu_wali = mysqli_insert_id($conn); 

        // Mengenkripsi password default 'Siswa12345*!'
        $password_enkripsi = password_hash('Siswa12345*!', PASSWORD_DEFAULT);

        // Insert data siswa
        $query = "INSERT INTO siswa (nis, nama_siswa, jenis_kelamin, alamat, password, status, id_ortu_wali, id_kelas) 
        VALUES ('$nis', '$nama_siswa', '$jenis_kelamin', '$alamat', '$password_enkripsi', 'aktif', '$id_ortu_wali', '$id_kelas')";
        mysqli_query($conn, $query);

        if($query){
            echo "<script>alert('Berhasil Menambah Data Siswa'); window.location.href = '/Poin_Pelanggaran_Siswa_XIIRPL4/pages/siswa/list.php';</script>";
        }else{
            echo "<script>alert('Gagal Menambah Data Siswa'); window.location.href = '/Poin_Pelanggaran_Siswa_XIIRPL4/pages/siswa/add.php';</script>";
        }
    // Jika aksi adalah "edit", maka ubah data siswa berdasarkan NIS
    } elseif ($action == 'edit') {

    $alamat = $_POST['alamat_siswa'];
    $kelas = $_POST['kelas'];
    $status = $_POST['status'];  

    // ========================
    // 2. Proses kelas
    // ========================
    $kelasArr = explode(" ", $kelas);
    $tingkat = $kelasArr[0];
    $program_keahlian = $kelasArr[1];
    $rombel = $kelasArr[2];

    $query_kelas = mysqli_query($conn, "
        SELECT id_kelas FROM kelas 
        JOIN program_keahlian USING(id_program_keahlian) 
        JOIN tingkat USING(id_tingkat) 
        WHERE tingkat = '$tingkat' 
        AND program_keahlian = '$program_keahlian' 
        AND rombel = '$rombel'
    ");

    $data_kelas = mysqli_fetch_assoc($query_kelas);
    $id_kelas = $data_kelas['id_kelas'];

    $pekerjaan_ayah = $_POST['pekerjaan_ayah'];
    $pekerjaan_ibu = $_POST['pekerjaan_ibu'];
    $pekerjaan_wali = $_POST['pekerjaan_wali'];

    $telp_ayah = $_POST['telp_ayah'];
    $telp_ibu = $_POST['telp_ibu'];
    $telp_wali = $_POST['telp_wali'];

    $alamat_ayah = $_POST['alamat_ayah'];
    $alamat_ibu = $_POST['alamat_ibu'];
    $alamat_wali = $_POST['alamat_wali'];

    $tempat_lahir_ayah = $_POST['tempat_lahir_ayah'];       // Tempat lahir ayah
    $tempat_lahir_ibu = $_POST['tempat_lahir_ibu'];         // Tempat lahir ibu
    $tempat_lahir_wali = $_POST['tempat_lahir_wali'];  

    // ========================
    // 3. Update tabel siswa
    // ========================
    $query_siswa = "UPDATE siswa SET 
        alamat = '$alamat',
        id_kelas = '$id_kelas',
        status = '$status'
        WHERE nis = '$nis'
    ";

    mysqli_query($conn, $query_siswa);

    // ========================
    // 4. Update ortu_wali (ringkas)
    // ========================
    $siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_ortu_wali FROM siswa WHERE nis = '$nis'"));
    
    mysqli_query($conn, "UPDATE ortu_wali SET 
        pekerjaan_ayah = '$pekerjaan_ayah',
        pekerjaan_ibu = '$pekerjaan_ibu',
        pekerjaan_wali = '$pekerjaan_wali',
        no_telp_ayah = '$telp_ayah',
        no_telp_ibu = '$telp_ibu',
        no_telp_wali = '$telp_wali',
        alamat_ayah = '$alamat_ayah',
        alamat_ibu = '$alamat_ibu',
        alamat_wali = '$alamat_wali'
        WHERE id_ortu_wali = '{$siswa['id_ortu_wali']}'
    ");

    echo "Data berhasil diupdate!";

        // Jika aksi adalah "delete", maka hapus data siswa berdasarkan NIS
    } elseif ($action == 'delete') {
        // Ambil id_ortu_wali sebelum hapus
        $siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_ortu_wali FROM siswa WHERE nis='$nis'"));
        $id_ortu_wali = $siswa['id_ortu_wali'];
        
        // Hapus perjanjian yang terhubung ke pelanggaran_siswa
        mysqli_query($conn, "DELETE FROM perjanjian_siswa WHERE id_pelanggaran_siswa IN (SELECT id_pelanggaran_siswa FROM pelanggaran_siswa WHERE nis='$nis')");
        mysqli_query($conn, "DELETE FROM perjanjian_orang_tua WHERE id_pelanggaran_siswa IN (SELECT id_pelanggaran_siswa FROM pelanggaran_siswa WHERE nis='$nis')");
        
        // Hapus data utama
        mysqli_query($conn, "DELETE FROM pelanggaran_siswa WHERE nis='$nis'");
        mysqli_query($conn, "DELETE FROM surat_keluar WHERE nis='$nis'");
        mysqli_query($conn, "DELETE FROM siswa WHERE nis='$nis'");
        
        // Hapus ortu_wali jika tidak ada siswa lain yang menggunakannya
        if ($id_ortu_wali) {
            $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM siswa WHERE id_ortu_wali='$id_ortu_wali'"));
            if ($check['count'] == 0) {
                mysqli_query($conn, "DELETE FROM ortu_wali WHERE id_ortu_wali='$id_ortu_wali'");
            }
        }
        
        $_SESSION['success'] = "Data siswa berhasil dihapus!";
    }

    // Setelah selesai, arahkan kembali ke halaman daftar siswa
    header("Location: ../pages/siswa/list.php");
    exit;
}
?>

<!-- 
🧠 Penjelasan Singkat:

Kode ini berfungsi sebagai file proses (process file) untuk tabel siswa — menangani semua aksi dari form seperti:
	•	Tambah data (add)
	•	Edit data (edit)
	•	Hapus data (delete)

Setelah aksi dijalankan, pengguna akan otomatis diarahkan kembali ke halaman daftar siswa (list.php).

👉 File ini dipakai dari form add.php(fungsi insert), edit.php(fungsi update), dan list(fungsi delete).php 
-->