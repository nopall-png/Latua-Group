<?php
// Set default page title
$page_title = isset($page_title) ? $page_title : 'Latuae Land';
?>

<nav class="bg-[#000000] text-white py-2 px-4 flex justify-between items-center">
    <!-- Logo Section -->
    <div class="logo">
        <a href="../pages/index.php">
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex space-x-6 pl-16">
        <a href="#" class="hover:text-gray-300 flex items-center font-raleway">Properti <span class="ml-1"></span></a>
        <a href="#" class="hover:text-gray-300 flex items-center font-raleway">Layanan <span class="ml-1"></span></a>
        <a href="#" class="hover:text-gray-300 flex items-center font-raleway">Tentang Kami <span class="ml-1"></span></a>
    </div>

    <!-- Right Section (Language, Contact, Search) -->
    <div class="flex items-center space-x-4">
        <div class="relative">
            <select class="bg-[#000000] text-white border-none outline-none appearance-none pr-2">
                <option>IND</option>
                <option>ENG</option>
            </select>
            <span class="absolute right-0 top-0 mt-1 mr-2"></span>
        </div>
        <a href="#" class="bg-blue-900 text-white px-4 py-2 rounded hover:bg-blue-800">Hubungi Kami</a>
        <div class="relative">
            <input type="text" placeholder="Search..." class="bg-white text-black px-3 py-1 rounded-md focus:outline-none">
            <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500">🔍</span>
        </div>
    </div>
</nav>