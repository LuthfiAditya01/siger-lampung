<?php
include '../koneksi.php';

session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login');
    exit();
}
// Cek dan tampilkan alert
if (!empty($_SESSION['alert_success'])) {
    echo "<script>alert('" . addslashes($_SESSION['alert_success']) . "');</script>";
    unset($_SESSION['alert_success']); // Hapus supaya tidak muncul lagi saat refresh
}

$user_id = $_SESSION['user_id'];
$query = $conn->query("SELECT name FROM `users` WHERE id=$user_id");
$data = $query->fetch_assoc();
$nama_user = $data['name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Siger Lampung</title>
    <link rel="stylesheet" href="../css/output.css" />
    <!-- Add Heroicons via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- <img src="https://unpkg.com/heroicons@2.0.18/24/outline/user.svg" alt="user icon" width="24"> -->
    <link rel="shortcut icon" href="../img/logo.jpg" type="image/x-icon">

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
                    <h1 class="text-2xl font-semibold ml-4">Dashboard</h1>
                </div>

                <!-- Content -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-5">
                    <h2 class="text-2xl font-semibold mb-4">Hai <?=$nama_user?>! Selamat Datang di Dashboard</h2>
                    <!-- <p class="text-gray-600">You are logged in as an administrator. Use the sidebar to navigate through different sections.</p> -->
                </div>

                <!-- Tableau -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <!-- <h2 class="text-2xl font-semibold mb-4">Dimensi Wilayah</h2> -->
                    <div class='tableauPlaceholder' id='viz1754147765448' style='position: relative'><noscript><a href='#'><img alt=' ' src='https:&#47;&#47;public.tableau.com&#47;static&#47;images&#47;Da&#47;DashboardSIGERLAMPUNG-BPS1871&#47;Dashboard3&#47;1_rss.png' style='border: none' /></a></noscript><object class='tableauViz' style='display:none;'>
                            <param name='host_url' value='https%3A%2F%2Fpublic.tableau.com%2F' />
                            <param name='embed_code_version' value='3' />
                            <param name='site_root' value='' />
                            <param name='name' value='DashboardSIGERLAMPUNG-BPS1871&#47;Dashboard3' />
                            <param name='tabs' value='yes' />
                            <param name='toolbar' value='yes' />
                            <param name='static_image' value='https:&#47;&#47;public.tableau.com&#47;static&#47;images&#47;Da&#47;DashboardSIGERLAMPUNG-BPS1871&#47;Dashboard3&#47;1.png' />
                            <param name='animate_transition' value='yes' />
                            <param name='display_static_image' value='yes' />
                            <param name='display_spinner' value='yes' />
                            <param name='display_overlay' value='yes' />
                            <param name='display_count' value='yes' />
                            <param name='language' value='en-US' />
                        </object></div>
                    <script type='text/javascript'>
                        var divElement = document.getElementById('viz1754147765448');
                        var vizElement = divElement.getElementsByTagName('object')[0];
                        if (divElement.offsetWidth > 800) {
                            vizElement.style.minWidth = '1024px';
                            vizElement.style.maxWidth = '1400px';
                            vizElement.style.width = '100%';
                            vizElement.style.minHeight = '650px';
                            vizElement.style.maxHeight = '950px';
                            vizElement.style.height = (divElement.offsetWidth * 0.75) + 'px';
                        } else if (divElement.offsetWidth > 500) {
                            vizElement.style.minWidth = '1024px';
                            vizElement.style.maxWidth = '1400px';
                            vizElement.style.width = '100%';
                            vizElement.style.minHeight = '650px';
                            vizElement.style.maxHeight = '950px';
                            vizElement.style.height = (divElement.offsetWidth * 0.75) + 'px';
                        } else {
                            vizElement.style.width = '100%';
                            vizElement.style.height = '1350px';
                        }
                        var scriptElement = document.createElement('script');
                        scriptElement.src = 'https://public.tableau.com/javascripts/api/viz_v1.js';
                        vizElement.parentNode.insertBefore(scriptElement, vizElement);
                    </script>
                </div>
            </div>
        </main>
    </div>
</body>

</html>