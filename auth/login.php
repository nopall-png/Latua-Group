<?php
include '../includes/db_connect.php';
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Logika khusus: Jika username "admin" dan password "123", arahkan ke admin
    if ($username === 'admin' && $password === '123') {
        $_SESSION['user_id'] = 1; // ID sementara untuk admin
        header('Location: ../admin/index.php');
        exit();
    }

    // Verifikasi kredensial lain dari database
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: ../index.php'); // Diarahkan ke index.php alih-alih user/index.php
        exit();
    } else {
        echo "<p style='color: red;'>Invalid username or password.</p>";
    }
}
?>

<h2>Login</h2>
<form method="POST" action="login.php">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>

<?php include '../includes/footer.php'; ?>