<?php
include '../koneksi.php';
session_start();


// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login');
    exit();
}

// Ambil data dokumen dari database
$query = $conn->query("SELECT * FROM users ORDER BY name DESC");
$dokumen = $query->fetch_all(MYSQLI_ASSOC);


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['hapus_dokumen'])) {
    $id = intval($_POST['hapus_id']);
    // Hapus dari database
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Redirect supaya nggak ke-resubmit
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Siger Lampung</title>
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
                    <h1 class="text-2xl font-semibold ml-4">Manajemen User</h1>
                </div>

                <!-- Content -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-5">
                    <h2 class="text-2xl font-semibold mb-4">Manajemen User</h2>
                </div>
                <div class="max-w-xl mx-auto bg-white p-6 rounded shadow-md">
                    <div class="max-w-6xl mx-auto bg-white shadow-md p-6 rounded-lg">
                        <div class="overflow-x-auto">
                            <table id="tabelDokumen" class="display w-full">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>username</th>
                                        <th>Nama</th>
                                        <th>Aksi</th> <!-- Tambah kolom aksi -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dokumen as $d): ?>
                                        <tr>
                                            <td><?= $d['id'] ?></td>
                                            <td><?= htmlspecialchars($d['username']) ?></td>
                                            <td><?= htmlspecialchars($d['name']) ?></td>
                                            <td>
                                                <form method="post" onsubmit="return confirm('Yakin ingin menghapus dokumen ini?');">
                                                    <input type="hidden" name="hapus_id" value="<?= $d['id'] ?>">
                                                    <button type="submit" name="hapus_dokumen" class="text-red-600 hover:underline">Hapus</button>
                                                </form>
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