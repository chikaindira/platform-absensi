<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $siswa_id = $_POST['siswa_id'];
    $status = $_POST['status'];

    $sql = "INSERT INTO absensi (siswa_id, status) VALUES ('$siswa_id', '$status')";
    if ($conn->query($sql) === TRUE) {
        header("Location: absensi.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>