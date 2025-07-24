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
        /* Tambahkan class khusus untuk mode collapsed */
        .collapsed .sidebar-text {
            display: none;
        }

        .collapsed .justify-start {
            justify-content: center;
        }

        .collapsed .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
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
                    <h1 class="text-2xl font-semibold ml-4">Dashboard</h1>
                </div>

                <!-- Content -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-5">
                    <h2 class="text-2xl font-semibold mb-4">Welcome to Admin Dashboard</h2>
                    <p class="text-gray-600">You are logged in as an administrator. Use the sidebar to navigate through different sections.</p>
                </div>

                <!-- Tableau -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <!-- <h2 class="text-2xl font-semibold mb-4">Dimensi Wilayah</h2> -->
                    <div class='tableauPlaceholder' id='viz1753325516855' style='width: 100%; height: 800px; position: relative; margin-top: 20px;'>
                        <noscript>
                            <a href='#'>
                                <img alt=' '
                                    src='https://public.tableau.com/static/images/Da/DashboardSIGERLAMPUNG-BPS1871/Wilayah_1/1_rss.png'
                                    style='border: none' />
                            </a>
                        </noscript>
                        <object class='tableauViz' style='width: 100%; height: 100%;'>
                            <param name='host_url' value='https%3A%2F%2Fpublic.tableau.com%2F' />
                            <param name='embed_code_version' value='3' />
                            <param name='site_root' value='' />
                            <param name='name' value='DashboardSIGERLAMPUNG-BPS1871&#47;Wilayah_1' />
                            <param name='tabs' value='yes' />
                            <param name='toolbar' value='yes' />
                            <param name='static_image'
                                value='https://public.tableau.com/static/images/Da/DashboardSIGERLAMPUNG-BPS1871/Wilayah_1/1.png' />
                            <param name='animate_transition' value='yes' />
                            <param name='display_static_image' value='yes' />
                            <param name='display_spinner' value='yes' />
                            <param name='display_overlay' value='yes' />
                            <param name='display_count' value='yes' />
                            <param name='language' value='en-US' />
                        </object>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Tableau Initialization Script -->
    <script type="text/javascript">
        var divElement = document.getElementById('viz1753325516855');
        var vizElement = divElement.getElementsByTagName('object')[0];
        if (vizElement) {
            vizElement.style.width = '100%';
            vizElement.style.minHeight = '800px';
            vizElement.style.maxHeight = '900px';
            vizElement.style.height = (divElement.offsetWidth * 0.65) + 'px';
            var scriptElement = document.createElement('script');
            scriptElement.src = 'https://public.tableau.com/javascripts/api/viz_v1.js';
            vizElement.parentNode.insertBefore(scriptElement, vizElement);
        }
    </script>
</body>
</html>