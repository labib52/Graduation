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
    <title>Man in the Middle Attacks</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f4f9;
        }

        header {
            background-color: #007BFF;
            color: #fff;
            padding: 1rem 2rem;
            text-align: center;
            position: relative;
        }

        header h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .user-info {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            font-size: 1rem;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 15px;
            border-radius: 8px;
        }

        .content {
            padding: 2rem;
            margin: auto;
            max-width: 800px;
        }

        .simulation-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .simulation-card:hover {
            transform: translateY(-3px);
        }

        .simulation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .simulation-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .status {
            font-size: 0.9rem;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
            font-weight: bold;
        }

        .active {
            background-color: #28a745;
            color: white;
        }

        .description {
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        footer {
            text-align: center;
            padding: 1rem;
            background-color: #007BFF;
            color: white;
            margin-top: 2rem;
        }

        a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>
    <header>
        <h1>Man in the Middle Attacks</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>
    <main class="content">
        <!-- Lecture Card -->
        <a href="/Graduation/wirelessattack/attack_mitm_arp_poisoning.php">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>ARP Poisoning</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Learn how attackers manipulate ARP tables to intercept communications on a network.</p>
            </div>
        </a>

        <!-- Bettercap Basics -->
        <a href="/Graduation/wirelessattack/attack_mitm_bettercap_basics.php">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Bettercap Basics</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Understand the fundamentals of using Bettercap for network attacks and reconnaissance.</p>
            </div>
        </a>

        <!-- ARP Spoofing with Bettercap -->
        <a href="/Graduation/wirelessattack/attack_mitm_arp_spoofing.php">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>ARP Spoofing using Bettercap</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Explore how attackers use ARP spoofing techniques with Bettercap to perform MITM attacks.</p>
            </div>
        </a>

        <!-- Intercept Traffic -->
        <a href="/Graduation/wirelessattack/attack_mitm_intercept.php">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Intercepting Traffic</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">See how attackers capture and manipulate network traffic in real-time.</p>
            </div>
        </a>

        <!-- Creating Custom Spoofing Script -->
        <a href="/Graduation/wirelessattack/attack_mitm_custom_spoofing.php">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Creating a Custom Spoofing Script</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Learn how to write custom scripts to automate ARP spoofing and traffic manipulation.</p>
            </div>
        </a>

        <!-- Bypassing HTTPS -->
        <a href="/Graduation/wirelessattack/attack_mitm_bypassing_https.php">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Bypassing HTTPS</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Discover methods used to downgrade secure connections and capture sensitive data.</p>
            </div>
        </a>

        <!-- Bypassing HSTS -->
        <a href="/Graduation/wirelessattack/attack_mitm_bypassing_hsts.php">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Bypassing HSTS</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Learn about techniques used to bypass HTTP Strict Transport Security (HSTS) protections.</p>
            </div>
        </a>

        <!-- Back Button -->
        <a href="../wireless.php" class="back-button">← Back</a>
    </main>
    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
