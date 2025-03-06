<?php
session_start();
include 'config.php';  // Pastikan file config.php sudah terkonfigurasi dengan benar koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query validasi username
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();

        // Validasi password (sesuaikan jika menggunakan enkripsi/hashed)
        if ($password === $admin['password']) {
            $_SESSION['admin'] = $admin['username'];
            header("Location: dashboard.php");  // Redirect ke dashboard admin
            exit();
        } else {
            echo "<script>
                    alert('Password salah!');
                    window.location.href = 'login.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Username tidak ditemukan!');
                window.location.href = 'login.php';
              </script>";
    }
    $stmt->close();
}
$conn->close();
?>