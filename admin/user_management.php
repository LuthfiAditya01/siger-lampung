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

// Proses form
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tambah_user'])) {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO users (username, name, password, is_admin) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $name, $password, $is_admin);

    if ($stmt->execute()) {
        $_SESSION['alert'] = "User berhasil ditambahkan";
    } else {
        $_SESSION['alert'] = "Gagal menambahkan user";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Siger Bandar Lampung</title>
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
                    <h1 class="text-2xl font-semibold ml-4">Manajemen User</h1>
                </div>

                <!-- Content -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-5">
                    <h2 class="text-2xl font-semibold mb-4">Manajemen User</h2>
                </div>
                <div class="max-w-xl mx-auto bg-white p-6 rounded shadow-md">
                    <div class="max-w-6xl mx-auto bg-white shadow-md p-6 rounded-lg">
                        <!-- Tombol buka modal -->
                        <button onclick="openModal()" class="btn-cari bg-blue-500 text-white px-4 py-2 rounded">Tambah User</button>
                        <br><br>
                        <!-- Modal -->
                        <div id="modalTambahUser" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 items-center justify-center z-9999">
                            <div class="relative w-[90%] max-w-[300px] bg-white text-black rounded-lg shadow-lg p-6">

                                <!-- Tombol Close -->
                                <button type="button" onclick="closeModal()"
                                    class="bg-white-600 hover:bg-blue-700 text-black px-4 py-2 rounded">
                                    &times;
                                </button>

                                <h2 class=" text-xl font-semibold mb-4">Tambah User</h2>

                                    <form method="POST">
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium">Username</label>
                                            <input type="text" name="username" required class="mt-1 w-full border border-gray-300 rounded p-2">
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium">Nama</label>
                                            <input type="text" name="name" required class="mt-1 w-full border border-gray-300 rounded p-2">
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium">Password</label>
                                            <input type="password" name="password" required class="mt-1 w-full border border-gray-300 rounded p-2">
                                        </div>
                                        <div class="mb-4">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="is_admin" class="form-checkbox">
                                                <span class="ml-2">Admin</span>
                                            </label>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded">
                                                Batal
                                            </button>
                                            <button type="submit" name="tambah_user" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                                                Simpan
                                            </button>
                                        </div>
                                    </form>
                            </div>
                        </div>

                        <script>
                            function openModal() {
                                document.getElementById('modal').classList.remove('hidden');
                                document.getElementById('modal').classList.add('flex');
                            }

                            function closeModal() {
                                document.getElementById('modal').classList.add('hidden');
                                document.getElementById('modal').classList.remove('flex');
                            }
                        </script>
                        <div class="overflow-x-auto">
                            <table id="tabelDokumen" class="display w-full z-1000">
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
    <script>
        const modal = document.getElementById('modalTambahUser');

        function openModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    </script>
</body>