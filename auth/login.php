<?php
ob_start();
session_start();
require_once '../includes/db_connect.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../admin/index.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Hardcoded admin check
    if ($username === 'admin' && $password === '123') {
        $_SESSION['user_id'] = 'admin'; // Set a unique identifier for the hardcoded admin
        header('Location: ../admin/index.php');
        exit();
    }
    
    // Existing database check as fallback
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: ../admin/index.php');
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}
require_once '../includes/header.php';
?>
<div class="admin-container">
    <h2>Login</h2>
    <?php if ($error): ?>
        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</div>
<?php
include '../includes/footer.php';
ob_end_flush();
?>