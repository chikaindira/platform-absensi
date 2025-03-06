<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis      = $_POST['username'];
    $password = $_POST['password'];
    
    // Query validasi NIS
    $sql = "SELECT * FROM siswa WHERE nis = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nis);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $siswa = $result->fetch_assoc();
        
        // Jika password yang diinputkan sama dengan password di database
        if ($password === $siswa['password']) {
            // Simpan data siswa ke session
            $_SESSION['siswa']     = $nis;
            $_SESSION['siswa_id']  = $siswa['id'];
            $_SESSION['siswa_nama']= $siswa['nama'];
            
            // Redirect langsung ke dashboard siswa
            header("Location: dashboardsiswa.php");
            exit();
        } else {
            echo "<script>
                    alert('Password salah!');
                    window.location.href = 'loginsiswa.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('NIS tidak ditemukan!');
                window.location.href = 'loginsiswa.php';
              </script>";
    }
    
    $stmt->close();
}
$conn->close();
?>