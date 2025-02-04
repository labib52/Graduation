<?php
session_start();

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['admin_id']);

// Get the username from the session if logged in
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cybersecurity Training Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
        }
        .header {
            background: linear-gradient(90deg, #0d47a1, #1976d2);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        .header h1 {
            font-size: 2.5rem;
            margin: 0;
        }
        .auth-links {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .auth-links a {
            color: white;
            text-decoration: none;
            margin-left: 10px;
            padding: 10px 20px;
            border: 2px solid white;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .auth-links a:hover {
            background-color: white;
            color: #0d47a1;
        }
        .lessons {
            padding: 60px 20px;
            text-align: center;
            background: #e3f2fd;
        }
        .lessons h2 {
            font-size: 2rem;
            margin-bottom: 15px;
        }
        .lessons p {
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        .lessons a {
            display: inline-block;
            background-color: #0d47a1;
            color: white;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 5px;
            font-size: 1.2rem;
            transition: background-color 0.3s;
        }
        .lessons a:hover {
            background-color: #1976d2;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="auth-links">
            <?php if ($loggedIn): ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
            <?php if ($is_admin): ?>
                <a href="admin/admin_dashboard.php" style="background: #ff9800;">Admin Panel</a>
            <?php endif; ?>
        </div>
        <h1>Welcome, <?php echo $username; ?>!</h1>
        <p>Your gateway to mastering cybersecurity through real-world simulations and expert knowledge.</p>
    </header>

    <section class="lessons">
        <h2>Start Learning</h2>
        <p>Select a lesson to begin your journey toward becoming a cybersecurity expert.</p>
        <a href="<?php echo $loggedIn ? 'categ.php' : 'login.php'; ?>">Choose Your Lesson</a>
    </section>
</body>
</html>
