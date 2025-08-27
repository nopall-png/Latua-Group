<?php
// Set default page title
$page_title = isset($page_title) ? $page_title : 'Latuae Land';
?>

<nav class="bg-white shadow-md fixed top-0 left-0 w-full z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    
    <!-- Logo -->
    <div class="flex items-center">
      <a href="/LatuaGroup/pages/index.php" class="flex items-center space-x-2">
        <img src="/LatuaGroup/uploads/latualogo.png" alt="Latuae Land" class="h-8 w-auto">
      </a>
    </div>

    <!-- Navigation Menu -->
    <div class="hidden md:flex space-x-8">
      <a href="#" class="text-gray-700 hover:text-blue-900 font-medium">Properti</a>
      <a href="#" class="text-gray-700 hover:text-blue-900 font-medium">Layanan</a>
      <a href="#" class="text-gray-700 hover:text-blue-900 font-medium">Proyek</a>
      <a href="#" class="text-gray-700 hover:text-blue-900 font-medium">Tentang Kami</a>
    </div>

    <!-- Right Section -->
    <div class="flex items-center space-x-4">
      <!-- Language Selector -->
      <select class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none">
        <option>IND</option>
        <option>ENG</option>
      </select>

      <!-- Contact Button -->
      <a href="#" class="bg-blue-900 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-800 transition">
        Hubungi Kami
      </a>

      <!-- Search Bar -->
      <div class="relative">
        <input 
          type="text" 
          placeholder="Cari properti..." 
          class="pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
          <i class="fas fa-search"></i>
        </span>
      </div>
    </div>
  </div>
</nav>

<!-- Spacer supaya konten tidak ketiban navbar fixed -->
<div class="h-[72px]"></div>
