<?php
include '../koneksi.php';
session_start();


// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login');
    exit();
}
$no = 1;
// Ambil data dokumen dari database
$query = $conn->query("SELECT * FROM news ORDER BY tanggal_berita DESC ");
$dokumen = $query->fetch_all(MYSQLI_ASSOC);

$query2 = $conn->query("SELECT tanggal_update FROM news ORDER BY tanggal_update DESC LIMIT 1");

if ($query2 && $query2->num_rows > 0) {
    $data = $query2->fetch_assoc();
    $tanggalUpdateTerbaru = $data['tanggal_update'];
} else {
    $tanggalUpdateTerbaru = "(Berita Belum Tersedia)";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $python = "C:\\Python313\\python.exe";
    $script = "C:\\laragon\\www\\siger-lampung\\scrapper\\dtsen-scraper.py";

    $command = "\"$python\" \"$script\" 2>&1";
    $output = shell_exec($command);

    // Folder log
    $logDir = __DIR__ . "/logs";
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    if ($output !== null && stripos($output, 'Traceback') === false && trim($output) !== '') {
        echo "<script>
            alert('Berita terbaru berhasil diambil!');
            window.location.href = window.location.pathname;
        </script>";
    } else {
        // Simpan log error
        $logFile = $logDir . "/scraper_error.log";
        file_put_contents($logFile, "[" . date("Y-m-d H:i:s") . "] " . $output . PHP_EOL, FILE_APPEND);

        echo "<script>
            alert('Gagal mengambil berita terbaru! Cek log di logs/scraper_error.log');
            window.location.href = window.location.pathname;
        </script>";
    }
    exit;
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Berita - Siger Bandar Lampung</title>
    <link rel="stylesheet" href="../css/output.css" />
    <link rel="shortcut icon" href="../img/logo.jpg" type="image/x-icon">
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->

    <!-- Add Heroicons via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/@heroicons/react@2.0.18/outline.min.css" rel="stylesheet">
    <!-- Outline CSS -->
    <link rel="stylesheet" href="https://unpkg.com/outline-css/dist/outline.min.css" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .btn-cari {
            background-color: #2563eb;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>

</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <?php include '../components/aside.php'; ?>
        <!-- Main Content -->
        <main id="mainContent" class="flex-1 overflow-x-hidden overflow-y-auto transition-all duration-300 ease-in-out">
            <div class="p-6">
                <!-- Header with Toggle Button -->
                <div class="flex items-center mb-6">
                    <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-2xl font-semibold ml-4">Manajemen Berita</h1>
                </div>

                <!-- Content -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-5">
                    <h2 class="text-2xl font-semibold mb-4">Manajemen Berita</h2>
                </div>
                <div class="max-w-xl mx-auto bg-white p-6 rounded shadow-md">
                    <div class="max-w-6xl mx-auto bg-white shadow-md p-6 rounded-lg">
                        <form method="POST">
                            <div class="form-button-group">
                                <button type="submit" class="btn-cari">Tarik Data</button>
                                <i> Update Terakhir : <?= $tanggalUpdateTerbaru ?></i>
                            </div>
                        </form>
                        <br>
                        <div class="overflow-x-auto">
                            <table id="tabelDokumen" class="display w-full">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Berita</th>
                                        <th>Sumber</th>
                                        <th>Tanggal Berita</th>
                                        <th>Tanggal Update</th>
                                        <th>Link</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dokumen as $d): ?>
                                        <tr>
                                            <td><?= $no;
                                                $no++ ?></td>
                                            <td><?= $d['nama'] ?></td>
                                            <td><?= $d['sumber'] ?></td>
                                            <td><?= $d['tanggal_berita'] ?></td>
                                            <td><?= $d['tanggal_update'] ?></td>
                                            <td>
                                                <a href="<?= $d['link'] ?>" target="_blank" rel="noopener noreferrer" class="group inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" stroke-width="2" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 
          1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                                    </svg>
                                                </a>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>

                    </div>
                    <!-- DataTables JS -->
                    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                    <script>
                        $(document).ready(function() {
                            $('#tabelDokumen').DataTable();
                        });
                    </script>
                </div>

            </div>
        </main>
    </div>

</body>