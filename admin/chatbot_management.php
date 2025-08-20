<?php
include '../koneksi.php';
session_start();


// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login');
    exit();
}

if (!empty($_SESSION['alert'])) {
    echo "<script>alert(" . json_encode($_SESSION['alert']) . ");</script>";
    unset($_SESSION['alert']);
}

$no = 1;

// Ambil data dokumen dari database
$query = $conn->query("SELECT * FROM dokumen_chatbot ORDER BY tanggal_upload DESC");
$dokumen = $query->fetch_all(MYSQLI_ASSOC);


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['hapus_dokumen'])) {
    $id = intval($_POST['hapus_id']);
    $file_pdf = basename($_POST['file_pdf']);
    $file_txt = basename($_POST['file_txt']);

    // Hapus file fisik
    $pdf_path = "../bahan-chatbot/pdf/" . $file_pdf;
    $txt_path = "../bahan-chatbot/txt/" . $file_txt;

    if (file_exists($pdf_path)) {
        unlink($pdf_path);
    }

    if (file_exists($txt_path)) {
        unlink($txt_path);
    }

    // Hapus dari database
    $stmt = $conn->prepare("DELETE FROM dokumen_chatbot WHERE id = ?");
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
    <title>Manajemen Chatbot - Siger Bandar Lampung</title>
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
                    <h1 class="text-2xl font-semibold ml-4">Manajemen Chatbot</h1>
                </div>

                <!-- Content -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-5">
                    <h2 class="text-2xl font-semibold mb-4">Manajemen Chatbot</h2>
                </div>
                <div class="max-w-xl mx-auto bg-white p-6 rounded shadow-md">
                    <div class="max-w-6xl mx-auto bg-white shadow-md p-6 rounded-lg">
                        <div class="flex items-center justify-between mb-6">
                            <!-- <h1 class="text-xl font-semibold">📄 Daftar Dokumen Chatbot</h1> -->
                            <button onclick="openModal()" type="submit" class="btn-cari">
                                Tambah Dokumen
                            </button>
                            <button type="button" onclick="window.location.href='chatbot_update.php'" class="btn-cari" style="background-color: #22c55e">
                                Update ChatBot
                            </button>

                        </div>
                        <div class="overflow-x-auto">
                            <table id="tabelDokumen" class="display w-full">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Dokumen</th>
                                        <th style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word;">Nama File PDF</th>
                                        <th style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word;">Nama File TXT</th>
                                        <th>Tanggal Upload</th>
                                        <th>Pengunggah</th>
                                        <!-- <th>Catatan</th> -->
                                        <th>Aksi</th> <!-- Tambah kolom aksi -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dokumen as $d): ?>
                                        <tr>
                                            <td><?= $no;
                                                $no++ ?></td>
                                            <td><?= htmlspecialchars($d['nama_dokumen']) ?></td>
                                            <td style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-width:200px;">
                                                <a style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-width:200px;" href="#" onclick="previewFile('<?= '../bahan-chatbot/pdf/' . $d['nama_file_pdf'] ?>', 'pdf'); return false;" class="text-blue-600 hover:underline">
                                                    <?= htmlspecialchars($d['nama_file_pdf']) ?>
                                                </a>
                                            </td>
                                            <td style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-width:200px;">
                                                <a style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word;  max-width:200px;" href="#" onclick="previewFile('<?= '../bahan-chatbot/txt/' . $d['nama_file_txt'] ?>', 'txt'); return false;" class="text-blue-600 hover:underline">
                                                    <?= htmlspecialchars($d['nama_file_txt']) ?>
                                                </a>
                                            </td>
                                            <td><?= $d['tanggal_upload'] ?></td>
                                            <td><?= htmlspecialchars($d['pengunggah']) ?></td>
                                            <!-- <td><?= nl2br(htmlspecialchars($d['catatan'])) ?></td> -->
                                            <td>
                                                <form method="post" onsubmit="return confirm('Yakin ingin menghapus dokumen ini?');">
                                                    <input type="hidden" name="hapus_id" value="<?= $d['id'] ?>">
                                                    <input type="hidden" name="file_pdf" value="<?= $d['nama_file_pdf'] ?>">
                                                    <input type="hidden" name="file_txt" value="<?= $d['nama_file_txt'] ?>">
                                                    <button type="submit" name="hapus_dokumen" class="text-red-600 hover:underline">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                        <!-- Modal -->
                        <div id="modalUpload" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
                            <div class="relative w-[90%] max-w-[500px] bg-white text-black rounded-lg shadow-lg p-6">

                                <!-- Tombol Close (kanan atas) -->
                                <button type="button" onclick="closeModal()"
                                    class="absolute top-3 right-3 text-gray-500 hover:text-red-600 text-2xl font-bold focus:outline-none z-10">
                                    &times;
                                </button>
                                <br>

                                <!-- Judul Modal -->
                                <h2 class="text-xl font-semibold mb-4">Upload Dokumen PDF</h2>

                                <!-- Form Upload -->
                                <form action="upload_dokumen.php" method="POST" enctype="multipart/form-data">
                                    <div class="mb-4">
                                        <label for="nama_dokumen" class="block text-sm font-medium text-gray-700">Nama Dokumen</label>
                                        <input type="text" name="nama_dokumen" id="nama_dokumen" required
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="file_pdf" class="block text-sm font-medium text-gray-700">Upload PDF</label>
                                        <input type="file" name="file_pdf" id="file_pdf" accept=".pdf" required
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" />
                                    </div>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" onclick="closeModal()"
                                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-black rounded">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Preview File -->
                        <div id="modalPreview" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50 w-[700px] h-[500px]">
                            <div class="bg-white rounded-lg shadow-lg w-full w-[700px] h-[500px] overflow-y-auto">
                                <div class="flex justify-between items-center p-4 border-b">
                                    <h3 class="text-lg font-semibold" id="modalTitle">Preview File</h3>
                                    <button onclick="closePreview()" class="text-gray-600 hover:text-black text-2xl leading-none">&times;</button>
                                </div>
                                <div id="previewContent" class="p-4">
                                    <!-- Isi preview muncul di sini -->
                                </div>
                            </div>
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
        const modal = document.getElementById('modalUpload');

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
    <script>
        const modalPreview = document.getElementById('modalPreview');
        const previewContent = document.getElementById('previewContent');

        function previewFile(filePath, type) {
            previewContent.innerHTML = ''; // clear previous

            if (type === 'pdf') {
                previewContent.innerHTML = `<iframe src="${filePath}" class="w-full h-[460px]" frameborder="0"></iframe>`;
            } else if (type === 'txt') {
                fetch(filePath)
                    .then(response => response.text())
                    .then(data => {
                        previewContent.innerHTML = `<pre class="whitespace-pre-wrap text-sm">${data}</pre>`;
                    })
                    .catch(err => {
                        previewContent.innerHTML = `<p class="text-red-600">Gagal memuat file</p>`;
                    });
            }

            modalPreview.classList.remove('hidden');
            modalPreview.classList.add('flex');
        }

        function closePreview() {
            modalPreview.classList.remove('flex');
            modalPreview.classList.add('hidden');
        }

        window.addEventListener('click', function(e) {
            if (e.target === modalPreview) {
                closePreview();
            }
        });
    </script>






</body>