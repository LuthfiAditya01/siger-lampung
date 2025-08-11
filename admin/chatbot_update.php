<?php
session_start();

// Jalankan update-txt.py
$python = "C:\Python313\python.exe"; // pakai double backslash di PHP
$script_path = __DIR__ . "/../ai-backend/update-txt.py";

$output = shell_exec("$python " . escapeshellarg($script_path) . " 2>&1");

// Simpan hasilnya ke session untuk alert
$_SESSION['alert'] = "Update ChatBot selesai!\n";

// Redirect kembali
header("Location: chatbot_management.php");
exit;

?>
