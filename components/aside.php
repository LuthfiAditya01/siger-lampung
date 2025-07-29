<?php
// session_start();
include '../koneksi.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../login');
    exit();
}

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
                    <img src="../img/logo.png" alt="Logo" class="h-8 w-auto mr-2">
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
                <a href="pencarian" class="flex items-center justify-start p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">
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
            </nav>

            <!-- Logout Button -->
            <div class="mt-auto">
                <a href="?logout" class="flex items-center justify-start p-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
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