<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Web</title>
    <link rel="stylesheet" href="/LatuaGroup/css/style.css">
</head>
<body>
    <div class="navbar">
        <a href="/LatuaGroup/index.php">Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php
            // Logika khusus untuk admin
            if ($_SESSION['user_id'] == 1) { // ID 1 diasumsikan untuk admin (berdasarkan login "admin" dan "123")
                echo '<a href="/LatuaGroup/admin/index.php">Admin Dashboard</a>';
            }
            ?>
            <a href="/LatuaGroup/auth/logout.php">Logout</a>
        <?php else: ?>
            <a href="/LatuaGroup/auth/login.php">Login</a>
            <a href="/LatuaGroup/auth/register.php">Register</a>
        <?php endif; ?>
    </div>
    <div class="container">