<?php
include '../koneksi.php';
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login');
    exit();
}

// Inisialisasi variabel hasil
$hasil = null;
$pesan = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Jika tombol Reset ditekan
    if (isset($_POST['reset'])) {
        // Kosongkan hasil pencarian dan pesan
        $hasil = null;
        $pesan = '';
        $_POST['kk'] = '';
        $_POST['nik'] = '';
    } else {
        $kk = $_POST['kk'] ?? '';
        $nik = $_POST['nik'] ?? '';

        if (strlen($kk) !== 16 || strlen($nik) !== 16) {
            $pesan = "Nomor KK dan NIK harus 16 digit.";
        } else {
            $query = "SELECT nomor_kartu_keluarga, nomor_induk_kependudukan, desil_nasional 
                      FROM dtsen 
                      WHERE nomor_kartu_keluarga = ? AND nomor_induk_kependudukan = ? 
                      LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $kk, $nik);
            $stmt->execute();
            $result = $stmt->get_result();
            $hasil = $result->fetch_assoc();

            if (!$hasil) {
                $pesan = "Data Tidak Ditemukan";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Pencarian - Siger Bandar Lampung</title>
    <link rel="stylesheet" href="../css/output.css" />
    <link rel="shortcut icon" href="../img/logo.jpg" type="image/x-icon">
    <!-- Add Heroicons via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/@heroicons/react@2.0.18/outline.min.css" rel="stylesheet">
    <style>
        /* Gradient button with smooth transition */
        .gradient-btn {
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .gradient-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, #0891b2, #2563eb);
            z-index: -1;
            transition: opacity 0.3s ease;
            opacity: 1;
        }

        .gradient-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom left, #0891b2, #2563eb);
            z-index: -2;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gradient-btn:hover::before {
            opacity: 0;
        }

        .gradient-btn:hover::after {
            opacity: 1;
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
                    <h1 class="text-2xl font-semibold ml-4">Pencarian</h1>
                </div>

                <!-- Content -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-5">
                    <h2 class="text-2xl font-semibold mb-4">Halaman Pencarian</h2>
                </div>

                <!-- Pencarian -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <form action="" method="post">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Masukkan Nomor KK</label>
                            <input type="number" name="kk"
                                value="<?= isset($_POST['reset']) ? '' : htmlspecialchars($_POST['kk'] ?? '') ?>"
                                class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-sm focus:ring-blue-500 focus:border-blue-500"
                                required>
                        </div>
                        <br>
                        <div class="mt-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Masukkan Nomor NIK</label>
                            <input type="number" name="nik"
                                value="<?= isset($_POST['reset']) ? '' : htmlspecialchars($_POST['nik'] ?? '') ?>"
                                class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-sm focus:ring-blue-500 focus:border-blue-500"
                                required>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="gradient-btn text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-blue-400 hover:to-cyan-400 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Cari</button>
                            <button type="submit" name="reset" value="1" class="gradient-btn text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-blue-400 hover:to-cyan-400 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                Reset
                            </button>
                        </div>
                    </form>

                    <?php if ($hasil): ?>
                        <div class="mt-6 text-sm text-gray-800 bg-green-50 p-4 rounded">
                            <p><strong>Nomor KK:</strong> <?= htmlspecialchars($hasil['nomor_kartu_keluarga']) ?></p>
                            <p><strong>NIK:</strong> <?= htmlspecialchars($hasil['nomor_induk_kependudukan']) ?></p>
                            <p><strong>Desil:</strong> <?= htmlspecialchars($hasil['desil_nasional']) ?></p>
                        </div>
                    <?php elseif ($pesan): ?>
                        <div class="mt-6 text-sm text-red-600 bg-red-50 p-4 rounded">
                            <?= $pesan ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function validasiForm() {
            const kk = document.getElementById("kk").value;
            const nik = document.getElementById("nik").value;

            if (kk.length !== 16 || nik.length !== 16) {
                alert("Nomor KK dan NIK harus 16 digit.");
                return false;
            }
            return true;
        }
    </script>
</body>

</html>