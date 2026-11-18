<?php

session_start();

// Menghapus seluruh data sesi admin
$_SESSION = [];

// Menghapus sesi
session_destroy();

// Mengarahkan kembali ke halaman login
header('Location: login.php');
exit;

?>
