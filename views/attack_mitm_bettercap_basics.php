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
    <title>Bettercap Basics Simulation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/CSS/wirelesshome.css">
</head>
<body>
    <header>
        <h1>Bettercap Basics Simulation</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>
    <main class="content">
        <!-- Lecture Card -->
        <a href="/Graduation/views/lecture.php?id=23">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Bettercap Basics Lecture</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Learn the fundamentals of Bettercap, a powerful MITM attack framework used for network manipulation and security testing.</p>
            </div>
        </a>

        <!-- Lab Exercises Card -->
        <a href="/Graduation/views/lab.php?id=5">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Lab Exercises</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Engage in hands-on labs to understand and simulate the functionality of Bettercap for ethical hacking and network security assessments.</p>
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
        <a href="attack_man_in_the_middle.php" class="back-button">← Back</a>
    </main>
    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
