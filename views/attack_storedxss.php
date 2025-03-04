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
    <title>Cross Site Scripting (XSS) Simulation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/CSS/reflectedxss.css">

</head>
<body>
    <header>
        <h1>Cross Site Scripting (XSS) Simulation</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>
    <main class="content">
        <!-- Lecture Card -->
        <a href="attack_storedxss_lec.php">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>stored xss lecture</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Learn how attackers inject malicious scripts into web applications, exploiting vulnerabilities in user inputs.</p>
            </div>
        </a>

        <!-- Lab Exercises Card -->
        <a href="/Graduation/views/lab.php?id=7">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Lab Exercises</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Practice hands-on activities related to XSS vulnerabilities, identifying and mitigating security risks.</p>
            </div>
        </a>

         <!-- Tools Card (Now opens index.php without auto-starting VM) -->
       <a href="index.php" target="_blank">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Kali Linux</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Click here to open the VM control panel and manually start Kali Linux.</p>
            </div>
        </a>

        <!-- Back Button -->
        <a href="../web.php" class="back-button">← Back</a>
    </main>
    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
