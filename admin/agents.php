<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/db_connect.php';
$agents = $pdo->query("SELECT * FROM agents ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Admin - Agen</title>
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
    <main class="flex-1 p-6">
      <h1 class="text-2xl font-bold mb-6">Daftar Agen</h1>
      <a href="add_agent.php" class="mb-6 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">+ Tambah Agen</a>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($agents as $a): ?>
          <div class="bg-white border rounded-lg shadow p-4 text-center">
            <img src="/LatuaGroup/uploads/agents/<?= htmlspecialchars($a['photo_path'] ?? 'default.jpg') ?>" 
                 class="w-24 h-24 rounded-full mx-auto object-cover">
            <h3 class="text-lg font-semibold mt-3"><?= htmlspecialchars($a['name']) ?></h3>
            <p class="text-gray-500 text-sm"><?= htmlspecialchars($a['phone_number']) ?></p>
            <p class="text-gray-500 text-sm"><?= htmlspecialchars($a['email']) ?></p>
            <div class="mt-3">
              <a href="delete_agent.php?id=<?= $a['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Hapus agen ini?')">Hapus</a>
            </div>
          </div>
        <?php endforeach ?>
      </div>
    </main>
  </div>
</body>
</html>
