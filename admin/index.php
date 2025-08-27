<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/db_connect.php';

// Hitung jumlah properti & agen
$total_properties = $pdo->query("SELECT COUNT(*) FROM properties")->fetchColumn();
$total_agents = $pdo->query("SELECT COUNT(*) FROM agents")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Latuae Land</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#0E1B4D] text-white p-6 space-y-6">
      <h1 class="text-2xl font-bold">Latuae Admin</h1>
      <nav class="space-y-4">
        <a href="index.php" class="block hover:text-gray-300">🏠 Dashboard</a>
        <a href="properties.php" class="block hover:text-gray-300">🏡 Properti</a>
        <a href="agents.php" class="block hover:text-gray-300">👨‍💼 Agen</a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">
      <h2 class="text-3xl font-semibold text-gray-800 mb-6">Dashboard</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-xl font-bold text-gray-700">Total Properti</h3>
          <p class="text-4xl mt-4 text-blue-600"><?= $total_properties ?></p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-xl font-bold text-gray-700">Total Agen</h3>
          <p class="text-4xl mt-4 text-green-600"><?= $total_agents ?></p>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
