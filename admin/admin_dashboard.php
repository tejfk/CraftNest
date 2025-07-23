<?php require_once 'includes/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Craftnest Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="./style.css">
    <script>
        tailwind.config = { darkMode: 'class', theme: { extend: { colors: { 'primary': '#8B5A2B', 'primary-light': '#a87a51', 'primary-dark': '#6d451f', 'background-light': '#F8F4F0', 'background-dark': '#1a1a1a', 'card-light': '#FFFFFF', 'card-dark': '#242424', 'text-light': '#1f2937', 'text-dark': '#e5e7eb', 'subtext-light': '#6b7280', 'subtext-dark': '#9ca3af', } } } }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <input type="checkbox" id="sidebar-toggle" class="hidden peer">
        <?php require_once 'includes/sidebar.php'; ?>
        <div class="flex-1 flex flex-col overflow-hidden">
            <?php require_once 'includes/header.php'; ?>
            <main id="main-content" class="flex-1 overflow-x-hidden overflow-y-auto p-6 lg:p-8">
                <div class="text-center p-10">Loading...</div>
            </main>
        </div>
        <label for="sidebar-toggle" class="fixed inset-0 bg-black/30 z-20 hidden peer-checked:block lg:hidden"></label>
    </div>
    <script src="./admin_script.js"></script>
</body>
</html>