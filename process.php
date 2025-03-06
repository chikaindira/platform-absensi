<?php
session_start();
include 'config.php'; // Ensure this file exists and includes the database connection
include 'functions.php'; // Ensure this file exists and is in the correct path

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['action'] == "add_user") {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
        $nama_lengkap = $_POST['nama_lengkap'];
        $role_id = $_POST['role_id'];

        $query = "INSERT INTO users (username, password, nama_lengkap, role_id) VALUES ('$username', '$password', '$nama_lengkap', '$role_id')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo "<script>alert('Admin berhasil ditambahkan!'); window.location='manajemen_admin.php';</script>";
        } else {
            echo "<script>alert('Gagal menambah admin!'); window.history.back();</script>";
        }
    }
} elseif ($_GET['action'] == "reset_password" && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $new_password = password_hash('defaultpassword', PASSWORD_DEFAULT); // Set a default password

    $query = "UPDATE users SET password='$new_password' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Password berhasil direset!'); window.location='manajemen_admin.php';</script>";
    } else {
        echo "<script>alert('Gagal mereset password!'); window.history.back();</script>";
    }
} elseif ($_GET['action'] == "toggle_status" && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $query = "UPDATE users SET status = NOT status WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Status admin berhasil diubah!'); window.location='manajemen_admin.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah status admin!'); window.history.back();</script>";
    }
} elseif ($_GET['action'] == "delete_user" && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $query = "DELETE FROM users WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Admin berhasil dihapus!'); window.location='manajemen_admin.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus admin!'); window.history.back();</script>";
    }
}
?>