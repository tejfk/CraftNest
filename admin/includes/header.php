<?php
// This file is included by other admin pages where the session is already started.
$admin_username = $_SESSION['admin_username'] ?? 'Admin';
?>
<header class="flex items-center justify-between p-4 bg-card-light dark:bg-card-dark shadow-md z-20">
    <!-- Left side: Hamburger menu for mobile and Title -->
    <div class="flex items-center">
        <label for="sidebar-toggle" class="cursor-pointer lg:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </label>
        <h2 class="text-xl font-semibold ml-4 hidden md:block">Craftnest Admin</h2>
    </div>

    <!-- Center: Search Bar -->
    <div class="relative w-full max-w-xs hidden sm:block">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3"><svg class="h-5 w-5 text-subtext-dark" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>
        <input class="w-full pl-10 pr-4 py-2 rounded-full bg-background-light dark:bg-background-dark border border-transparent focus:border-primary focus:ring-0 transition" placeholder="Search..." type="search" id="header-search">
    </div>

    <!-- Right side: Theme toggle and Admin Profile Dropdown -->
    <div class="flex items-center space-x-4">
        <button id="theme-toggle" type="button" class="text-subtext-dark hover:text-text-dark dark:hover:text-text-light focus:outline-none" aria-label="Toggle dark mode">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        </button>
        <div class="relative">
            <details class="group">
                <summary class="flex items-center cursor-pointer list-none">
                    <img class="w-10 h-10 rounded-full object-cover" src="https://i.pravatar.cc/150?u=admin" alt="Admin Avatar">
                    <span class="hidden md:inline-block ml-3 font-medium"><?php echo htmlspecialchars($admin_username); ?></span>
                    <svg class="w-4 h-4 ml-1 hidden md:inline-block transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </summary>
                <div class="absolute right-0 mt-2 w-48 bg-card-light dark:bg-card-dark rounded-md shadow-lg py-1 z-20">
                    <!-- The "Profile" link is a button to be handled by the SPA JavaScript -->
                    <button data-page="profile_content.php" class="nav-link w-full text-left block px-4 py-2 text-sm text-subtext-dark hover:bg-gray-200 dark:hover:bg-gray-700">Profile</button>
                    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                    <!-- FIXED: This is a standard <a> tag that will force a page navigation -->
                    <a href="admin_logout.php" class="block px-4 py-2 text-sm text-red-500 hover:bg-gray-200 dark:hover:bg-gray-700">Logout</a>
                </div>
            </details>
        </div>
    </div>
</header>