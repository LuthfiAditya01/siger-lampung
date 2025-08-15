<?php
session_start();

// Simpan pesan alert dulu di variabel biasa
$alert = "Anda berhasil logout.";

// Hapus semua session
session_unset();
session_destroy();

// Mulai session baru
session_start();
$_SESSION['alert_success'] = $alert;

// Redirect ke login
header('Location: ./login.php');
exit();
?>