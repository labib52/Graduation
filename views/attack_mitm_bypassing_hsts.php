<?php
session_start();

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bypassing HSTS Security</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/CSS/wirelesshome_1.css">
</head>
<body>
    <header>
        <h1>Bypassing HSTS Security</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>
    <main class="content">
        <!-- Lecture Card -->
        <a href="/Graduation/views/lecture.php?id=27">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Bypassing HSTS Lecture</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Understand how attackers attempt to bypass HSTS security mechanisms and downgrade HTTPS connections in a MITM attack.</p>
            </div>
        </a>
           <!-- Tools Card (Now opens index.php without auto-starting VM) -->
       <a href="index.php" target="_blank">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Try with Virtual Machine</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Click here to open the VM control panel and manually start Kali Linux.</p>
            </div>
        </a>
    </main>

    <!-- Back Button -->
    <a href="attack_man_in_the_middle.php" class="back-button">← Back</a>

    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
