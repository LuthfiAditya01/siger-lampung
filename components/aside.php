<?php
include '../koneksi.php';
$user_id = $_SESSION['user_id'];
$query_adm = $conn->query("SELECT * FROM `users` WHERE id=$user_id");
$data_adm = $query_adm->fetch_assoc();
$is_admin = $data_adm['is_admin'];
?>

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

<!-- Sidebar Backdrop -->
<div id="sidebarBackdrop" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-20 lg:hidden hidden" onclick="toggleSidebar()"></div>

<!-- Sidebar Container - This helps with layout -->
<div id="sidebarContainer" class="transition-all duration-300 ease-in-out w-0 lg:w-[280px]">
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed lg:relative w-[280px] min-h-screen bg-white shadow-lg transition-all duration-300 ease-in-out z-30">
        <div class="p-4 h-full flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <img src="../img/logo.jpg" alt="Logo" class="h-8 w-auto mr-2">
                    <span class="text-xl font-semibold sidebar-text">Siger Lampung</span>
                </div>
            </div>

            <!-- User Info -->
            <div class="mb-6 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600 sidebar-text">Welcome,</p>
                <p class="font-semibold sidebar-text"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-2 flex-grow">
                <a href="beranda" class="flex items-center justify-start p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="sidebar-text">Beranda</span>
                </a>
                <!-- <a href="pencarian" class="flex items-center justify-start p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span class="sidebar-text">Pencarian</span>
                </a> -->
                <a href="pencarian-new" class="flex items-center justify-start p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span class="sidebar-text">Pencarian</span>
                </a>
                <a href="data" class="flex items-center justify-start p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                    <span class="sidebar-text">Data</span>
                </a>
                <?php if ($is_admin == 1): ?>
                    <hr class="hr" />
                    <a href="chatbot_management.php" class="flex items-center justify-start p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" stroke-width="2" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                        </svg>

                        <span class="sidebar-text">Manajemen ChatBot</span>
                    </a>
                    <a href="news_management.php" class="flex items-center justify-start p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" stroke-width="2" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                        </svg>

                        <span class="sidebar-text">Manajemen Berita</span>
                    </a>
                    <a href="user_management.php" class="flex items-center justify-start p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                        </svg>

                        <span class="sidebar-text">Manajemen User</span>
                    </a>
                <?php endif; ?>
            </nav>
            <hr class="hr" />
            <!-- Logout Button -->
            <div class="mt-auto">
                <a href="../logout.php" class="flex items-center justify-start p-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="sidebar-text">Logout</span>
                </a>
            </div>
        </div>
    </aside>
</div>
<script>
    // Initialize sidebar state based on screen size
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const sidebarContainer = document.getElementById('sidebarContainer');
        // Get stored sidebar state or default to open for desktop, closed for mobile
        const isSidebarOpen = localStorage.getItem('sidebarOpen') !== 'false';
        const isMobile = window.innerWidth < 1024;

        // For mobile: always start with sidebar closed
        if (isMobile) {
            sidebar.classList.add('-translate-x-full');
            localStorage.setItem('sidebarOpen', 'false');
        }
        // For desktop: respect saved preference
        else if (!isSidebarOpen) {
            sidebar.classList.add('-translate-x-full');
            sidebarContainer.classList.remove('lg:w-[280px]');
            sidebarContainer.classList.add('lg:w-0');
        }
    });

    // Make toggleSidebar function global so it can be called from anywhere
    window.toggleSidebar = function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarContainer = document.getElementById('sidebarContainer');
        const backdrop = document.getElementById('sidebarBackdrop');
        const isOpen = !sidebar.classList.contains('-translate-x-full');

        // Toggle sidebar
        sidebar.classList.toggle('-translate-x-full');

        // Adjust container width for desktop
        if (window.innerWidth >= 1024) {
            if (isOpen) {
                sidebarContainer.classList.remove('lg:w-[280px]');
                sidebarContainer.classList.add('lg:w-0');
            } else {
                sidebarContainer.classList.remove('lg:w-0');
                sidebarContainer.classList.add('lg:w-[280px]');
            }
        }

        // Store sidebar state
        localStorage.setItem('sidebarOpen', !isOpen);

        // Toggle backdrop in mobile view
        if (window.innerWidth < 1024) {
            backdrop.classList.toggle('hidden');
        }
    };

    // Handle window resize
    window.addEventListener('resize', () => {
        const sidebar = document.getElementById('sidebar');
        const sidebarContainer = document.getElementById('sidebarContainer');
        const backdrop = document.getElementById('sidebarBackdrop');
        const isOpen = !sidebar.classList.contains('-translate-x-full');

        if (window.innerWidth >= 1024) {
            backdrop.classList.add('hidden');

            // Maintain correct container width
            if (isOpen) {
                sidebarContainer.classList.remove('lg:w-0');
                sidebarContainer.classList.add('lg:w-[280px]');
            } else {
                sidebarContainer.classList.remove('lg:w-[280px]');
                sidebarContainer.classList.add('lg:w-0');
            }
        }
    });
</script>