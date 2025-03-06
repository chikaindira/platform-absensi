<?php
session_start();
include 'config.php'; // Ensure this file contains your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nip = $_POST['nip'];
    $password = $_POST['password'];

    // Query to check if the user exists
    $query = "SELECT * FROM guru WHERE nip = '$nip'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password (plain text comparison)
        if ($password == $user['password']) {
            $_SESSION['guru'] = $user['nip']; // Store NIP in session
            header("Location: dashboardguru.php");
            exit();
        } else {
            echo "<script>alert('Password salah!'); window.location.href='loginguru.php';</script>";
        }
    } else {
        echo "<script>alert('NIP tidak ditemukan!'); window.location.href='loginguru.php';</script>";
    }
} else {
    header("Location: loginguru.php");
    exit();
}
?>