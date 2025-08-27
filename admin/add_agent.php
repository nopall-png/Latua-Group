<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone_number'];
    $email = $_POST['email'];

    $photoName = null;
    if (!empty($_FILES['photo']['name'])) {
        $photoName = time() . "_" . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/LatuaGroup/Uploads/agents/" . $photoName);
    }

    $stmt = $pdo->prepare("INSERT INTO agents (name, phone_number, email, photo_path) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $phone, $email, $photoName]);

    header("Location: agents.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Tambah Agen</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-2xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-6">Tambah Agen</h1>
    <form method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-6 rounded shadow">
      <input type="text" name="name" placeholder="Nama Agen" class="w-full border rounded p-2" required>
      <input type="text" name="phone_number" placeholder="Nomor Telepon" class="w-full border rounded p-2" required>
      <input type="email" name="email" placeholder="Email" class="w-full border rounded p-2">
      <input type="file" name="photo" class="w-full">
      <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Simpan</button>
    </form>
  </div>
</body>
</html>
