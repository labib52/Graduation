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
    <title>Wireless Security Simulation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
            font-size: 1.8rem;
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
            max-width: 900px;
            text-align: center;
        }

        .section-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #007BFF;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 3px solid #007BFF;
            display: inline-block;
            padding-bottom: 5px;
        }

        .simulations {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .simulation-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            cursor: pointer;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .simulation-card:hover {
            transform: translateY(-3px);
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.15);
        }

        .simulation-icon {
            font-size: 2rem;
            color: #007BFF;
            margin-right: 15px;
        }

        .simulation-header {
            flex: 1;
        }

        .simulation-header h2 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .status {
            font-size: 0.9rem;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
            font-weight: bold;
            background-color: #28a745;
            color: white;
        }

        .description {
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .back-button {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
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
            margin-top: 20px;
            font-size: 1rem;
        }

        a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>

    <header>
        <h1>Wireless Security Simulation</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>

    <main class="content">
        <h2 class="section-title">Wireless Attacks</h2>

        <div class="simulations">
            <!-- Attack 1: Discovering & Gathering Sensitive Info -->
            <a href="attack_discovering_info.php">
                <div class="simulation-card">
                    <div class="simulation-header">
                        <h2>Discovering Sensitive Info</h2>
                        <p class="description">Gathering sensitive information about devices connected to the same network, including IPs, MAC addresses, and open ports.</p>
                    </div>
                    <span class="status active">Active</span>
                </div>
            </a>

            <!-- Attack 2: WPS Cracking -->
            <a href="attack_wps_cracking.php">
                <div class="simulation-card">
                    <div class="simulation-header">
                        <h2>WPS Cracking</h2>
                        <p class="description">Exploiting vulnerabilities in the WPS protocol to gain unauthorized access to wireless networks.</p>
                    </div>
                    <span class="status active">Active</span>
                </div>
            </a>

            <!-- Attack 3: WEP Cracking -->
            <a href="attack_wep_cracking.php">
                <div class="simulation-card">
                    <div class="simulation-header">
                        <h2>WEP Cracking</h2>
                        <p class="description">Breaking into networks using weak WEP encryption by exploiting security flaws.</p>
                    </div>
                    <span class="status active">Active</span>
                </div>
            </a>

            <!-- Attack 4: WPA/WPA2 Cracking -->
            <a href="attack_wpa_cracking.php">
                <div class="simulation-card">
                    <div class="simulation-header">
                        <h2>WPA/WPA2 Cracking</h2>
                        <p class="description">Performing brute-force and dictionary attacks to bypass WPA/WPA2 security measures.</p>
                    </div>
                    <span class="status active">Active</span>
                </div>
            </a>

            <!-- Attack 5: Man in the Middle Attacks -->
            <a href="attacks_for_man_in_the_middle.php">
                <div class="simulation-card">
                    <div class="simulation-header">
                        <h2>Man in the Middle Attacks</h2>
                        <p class="description">Intercepting and manipulating communications between two parties without their knowledge.</p>
                    </div>
                    <span class="status active">Active</span>
                </div>
            </a>
        </div>

        <!-- Back Button -->
        <a href="categ.php" class="back-button">← Back to Categories</a>
    </main>

    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>

</body>
</html>
