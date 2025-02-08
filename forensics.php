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
    <title>Network Security Simulation</title>
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
            max-width: 800px;
            text-align: center;
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
        <h1>Forensics Science</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>
    <main class="content">
        <!-- Lecture Card -->
        <a href="forensics/lab1.php">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Lab1</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">My sister’s computer crashed. We were very fortunate to recover this memory dump.
                    Your job is get all her important files from the system. From what we remember, we suddenly saw a
                    black window pop up with some thing being executed. When the crash happened, she was trying to draw
                    something. Thats all we remember from the time of crash.</p>
            </div>
        </a>
        <!-- Lecture Card -->
        <a href="forensics/lab2.php">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Lab2</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">One of the clients of our company, lost the access to his system due to an
                    unknown error. He is supposedly a very popular “environmental” activist. As a part of the
                    investigation, he told us that his go to applications are browsers, his password managers etc. We
                    hope that you can dig into this memory dump and find his important stuff and give it back to us</p>
            </div>
        </a>

        <!-- Lecture Card -->
        <a href="forensics/lab3.php">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Lab3</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">A malicious script encrypted a very secret piece of information I had on my system. Can you recover the information for me please?</p>
            </div>
        </a>
        <!-- Lecture Card  -->
        <a href="forensics/lab5.php">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Lab5</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">This challenge is composed of 2 flags but do you really think so? Maybe a little flag is hiding somewhere.</p>
            </div>
        </a>
       <!-- Back Button -->
        <a href="categ.php" class="back-button">← Back</a>
    </main>
    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>

</html>
