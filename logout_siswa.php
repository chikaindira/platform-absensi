<?php
session_start();
unset($_SESSION['siswa']);
session_destroy();
header("Location: loginsiswa.php");
exit();
?>