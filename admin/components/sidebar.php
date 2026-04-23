<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 z-40 h-screen w-64 transition-transform -translate-x-full md:translate-x-0 bg-indigo-900 text-white shadow-2xl">
    <div class="h-full px-3 py-4 overflow-y-auto">
        <div class="flex items-center ps-2.5 mb-8">
            <i class="fas fa-graduation-cap text-3xl mr-3 text-indigo-400"></i>
            <span class="self-center text-xl font-bold whitespace-nowrap">Admin Panel</span>
        </div>
        <ul class="space-y-2 font-medium">
            <li>
                <a href="index.php" class="flex items-center p-3 rounded-xl hover:bg-indigo-800 transition-all <?php echo $current_page == 'index.php' ? 'bg-indigo-700' : ''; ?>">
                    <i class="fas fa-users w-6"></i>
                    <span class="ms-3">Data Siswa</span>
                </a>
            </li>
            <li>
                <a href="pengaturan.php" class="flex items-center p-3 rounded-xl hover:bg-indigo-800 transition-all <?php echo $current_page == 'pengaturan.php' ? 'bg-indigo-700' : ''; ?>">
                    <i class="fas fa-cog w-6"></i>
                    <span class="ms-3">Pengaturan</span>
                </a>
            </li>
            <li class="pt-10">
                <a href="logout.php" class="flex items-center p-3 rounded-xl text-red-300 hover:bg-red-900 hover:text-white transition-all">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span class="ms-3">Keluar</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- Top Navbar for Mobile -->
<nav class="md:hidden bg-indigo-900 text-white p-4 flex justify-between items-center fixed w-full top-0 z-50">
    <span class="text-xl font-bold">Admin Panel</span>
    <button id="toggleSidebar" class="p-2 rounded-lg hover:bg-indigo-800 focus:outline-none">
        <i class="fas fa-bars text-2xl"></i>
    </button>
</nav>

<script>
    document.getElementById('toggleSidebar')?.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('-translate-x-full');
    });
</script>
