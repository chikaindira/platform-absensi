<?php
session_start();
unset($_SESSION['guru']);
session_destroy();
header("Location: loginguru.php");
exit();
?>