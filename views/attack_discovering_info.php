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
    <title>Discovering Sensitive Info Simulation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/CSS/wirelesshome.css">
</head>
<body>
    <header>
        <h1>Discovering Sensitive Info Simulation</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>
    <main class="content">
        <!-- Lecture Card -->
        <a href="/Graduation/views/lecture.php?id=20">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Discovering Sensitive Info Lecture</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Understand how attackers gather sensitive information about devices connected to the same network, including IPs, MAC addresses, and open ports, and learn prevention strategies.</p>
            </div>
        </a>

        <!-- Lab Exercises Card -->
        <a href="/Graduation/views/lab.php?id=1">
    <div class="simulation-card">
        <div class="simulation-header">
            <h2>Lab Exercises</h2>
            <span class="status active">Active</span>
        </div>
        <p class="description">Engage in hands-on activities that simulate discovering and gathering sensitive information from network devices.</p>
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

        <!-- Back Button -->
        <a href="/Graduation/views/wireless.php" class="back-button">← Back</a>
    </main>
    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
