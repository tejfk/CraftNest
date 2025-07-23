<?php require_once '../includes/auth_check.php'; ?>
<h3 class="text-3xl font-bold mb-6">Dashboard Overview</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Total Sales -->
    <div class="bg-card-light dark:bg-card-dark p-6 rounded-xl shadow-md flex items-center space-x-4 transform hover:scale-105 transition-transform duration-300">
        <div class="bg-blue-500/20 p-3 rounded-full"><svg class="h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg></div>
        <div><p class="text-sm text-subtext-dark">Total Sales</p><p class="text-2xl font-bold">1,287</p></div>
    </div>
    <!-- Card 2: Active Bidders (or Users) -->
    <div class="bg-card-light dark:bg-card-dark p-6 rounded-xl shadow-md flex items-center space-x-4 transform hover:scale-105 transition-transform duration-300">
        <div class="bg-green-500/20 p-3 rounded-full"><svg class="h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg></div>
        <div><p class="text-sm text-subtext-dark">Total Users</p><p class="text-2xl font-bold">452</p></div>
    </div>
    <!-- Card 3: New Orders -->
    <div class="bg-card-light dark:bg-card-dark p-6 rounded-xl shadow-md flex items-center space-x-4 transform hover:scale-105 transition-transform duration-300">
        <div class="bg-yellow-500/20 p-3 rounded-full"><svg class="h-8 w-8 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg></div>
        <div><p class="text-sm text-subtext-dark">New Orders</p><p class="text-2xl font-bold">38</p></div>
    </div>
    <!-- Card 4: Revenue -->
    <div class="bg-card-light dark:bg-card-dark p-6 rounded-xl shadow-md flex items-center space-x-4 transform hover:scale-105 transition-transform duration-300">
        <div class="bg-red-500/20 p-3 rounded-full"><svg class="h-8 w-8 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01M12 12v-2m0 2v-2m0 2v.01M12 12a2.5 2.5 0 00-2.5 2.5M12 12a2.5 2.5 0 012.5 2.5m-5 5A2.5 2.5 0 009.5 9.5m0 5a2.5 2.5 0 01-2.5-2.5M12 21a9 9 0 100-18 9 9 0 000 18z" /></svg></div>
        <div><p class="text-sm text-subtext-dark">Revenue</p><p class="text-2xl font-bold">$12,480</p></div>
    </div>
</div>
<!-- You can add more dashboard content like charts or recent activity here -->