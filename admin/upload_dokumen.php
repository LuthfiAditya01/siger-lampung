<?php
session_start();
require_once '../koneksi.php'; // pastikan ini file koneksi DB kamu

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama_dokumen = trim($_POST['nama_dokumen']);
    $file = $_FILES['file_pdf'];

    // Validasi awal
    if (empty($nama_dokumen) || $file['error'] !== 0) {
        $_SESSION['alert'] = "Judul dan file PDF harus diisi.";
        header("Location: chatbot_management.php");
        exit;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        $_SESSION['alert'] = "Hanya file PDF yang diperbolehkan.";
        header("Location: chatbot_management.php");
        exit;
    }

    // Persiapkan nama file
    $nama_file = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nama_dokumen));
    $target_pdf = "../bahan-chatbot/pdf/{$nama_file}.pdf";
    $target_txt = "../bahan-chatbot/txt/{$nama_file}.txt";

    if (!move_uploaded_file($file['tmp_name'], $target_pdf)) {
        $_SESSION['alert'] = "Gagal menyimpan file.";
        header("Location: chatbot_management.php");
        exit;
    }

    // Jalankan Python untuk ekstraksi
    $command = escapeshellcmd("python ../ai-backend/extract_text.py {$target_pdf} {$target_txt}");
    $output = shell_exec($command);

    if (strpos($output, "success") === false) {
        $_SESSION['alert'] = "Ekstraksi gagal: " . htmlspecialchars($output);
        header("Location: chatbot_management.php");
        exit;
    }

    // Simpan ke database
    $ukuran_file = filesize($target_pdf);
    $pengunggah = "admin"; // bisa kamu ganti dengan session login user
    $catatan = ''; // jika tidak ada

    $stmt = $conn->prepare("INSERT INTO dokumen_chatbot (nama_dokumen, nama_file_pdf, nama_file_txt, ukuran_file, pengunggah, catatan) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        $_SESSION['alert'] = "Gagal prepare query: " . $conn->error;
        header("Location: chatbot_management.php");
        exit;
    }

    $stmt->bind_param("sssiss", $nama_dokumen, $nama_file_pdf, $nama_file_txt, $ukuran_file, $pengunggah, $catatan);
    $nama_file_pdf = "{$nama_file}.pdf";
    $nama_file_txt = "{$nama_file}.txt";

    if ($stmt->execute()) {
        $_SESSION['alert'] = "Upload dan ekstraksi berhasil.";
    } else {
        $_SESSION['alert'] = "Gagal simpan ke database: " . $stmt->error;
    }

    $stmt->close();
    header("Location: chatbot_management.php");
    exit;
}
