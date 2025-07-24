<?php
include '../koneksi.php';
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Siger Lampung</title>
    <link rel="stylesheet" href="../css/output.css" />
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
                    <!-- <p class="text-gray-600">You are logged in as an administrator. Use the sidebar to navigate through different sections.</p> -->
                </div>

                <!-- Tableau -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <form action="#" method="post">
                        <div>
                            <label for="small-input" class="block mb-2 text-sm font-medium text-gray-900">Masukkan Nomor KK</label>
                            <input type="text" id="small-input" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="small-input" class="block mb-2 text-sm font-medium text-gray-900">Masukkan Nomor NIK Kepala Keluarga</label>
                            <input type="text" id="small-input" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500"> <br>
                        </div>
                        <!-- <button type="submit" class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:to-cyan-500 hover:from-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-cyan-300  font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">Submit</button> -->
                        <div class="mt-6">
                            <button type="submit" class="gradient-btn text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:to-cyan-500 hover:from-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-cyan-300  font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 transition ease-out">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>


</body>

</html>