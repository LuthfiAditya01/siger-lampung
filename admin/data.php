<?php
include '../koneksi.php';
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login');
    exit();
}

$query = "SELECT * FROM dtsen";
$result = $conn->query($query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Pencarian - Siger Lampung</title>
    <link rel="stylesheet" href="../css/output.css" />
    <link rel="shortcut icon" href="../img/logo.jpg" type="image/x-icon">

    <!-- Add Heroicons via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/@heroicons/react@2.0.18/outline.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- DataTables Buttons CSS & JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <style>
        body {
            font-size: 12px;
            /* Ukuran font kecil */
            font-family: Arial, sans-serif;
        }

        table.dataTable thead th,
        table.dataTable tbody td {
            white-space: nowrap;
            /* Biar tidak membungkus teks */
        }

        .dataTables_wrapper {
            width: 100%;
            overflow-x: auto;
            /* Scroll horizontal */
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
                    <h1 class="text-2xl font-semibold ml-4">Data</h1>
                </div>

                <!-- Content -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-5">
                    <h2 class="text-2xl font-semibold mb-4">Halaman Data</h2>
                </div>

                <!-- Data -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <div style="overflow-x:auto">
                        <table id="tabelPenduduk" class="display compact" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Provinsi</th>
                                    <th>Kabupaten/Kota</th>
                                    <th>Kecamatan</th>
                                    <th>Kelurahan/Desa</th>
                                    <th>No KK</th>
                                    <th>NIK</th>
                                    <th>Desil Nasional</th>
                                    <th>Nama</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Umur</th>
                                    <th>Alamat</th>
                                    <th>Jumlah Anggota Keluarga</th>
                                    <th>Nama Kepala Keluarga</th>
                                    <th>Partisipasi Sekolah</th>
                                    <th>Jenjang Tertinggi</th>
                                    <th>Kelas Tertinggi</th>
                                    <th>Ijazah Tertinggi</th>
                                    <th>Nama Sekolah</th>
                                    <th>Lapangan Usaha KK</th>
                                    <th>Status Pekerjaan KK</th>
                                    <th>Status Kepemilikan Rumah</th>
                                    <th>Luas Bangunan</th>
                                    <th>Sumber Penerangan</th>
                                    <th>Daya Terpasang</th>
                                    <th>ID PLN</th>
                                </tr>
                            </thead>

                        </table>
                    </div>


                    <script>
                        $('#tabelPenduduk').DataTable({
                            processing: true,
                            serverSide: true,
                            scrollX: true,
                            ajax: "load_data.php",
                            dom: 'Bfrtip',
                            buttons: [{
                                    extend: 'csvHtml5',
                                    text: 'Download CSV',
                                    className: 'btn btn-sm btn-primary'
                                },
                                {
                                    extend: 'excelHtml5',
                                    text: 'Download Excel',
                                    className: 'btn btn-sm btn-success'
                                }
                            ]
                        });
                    </script>
                </div>
            </div>
        </main>
    </div>
</body>

</html>