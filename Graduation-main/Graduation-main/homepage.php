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
            background: #f4f4f4;
        }
        .header {
            background: linear-gradient(90deg, #0d47a1, #1976d2);
            color: white;
            padding: 40px 20px;
            text-align: center;
            position: relative;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header h1 {
            font-size: 3rem;
            margin: 10px 0;
        }
        .header p {
            font-size: 1.2rem;
            margin-top: 5px;
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
            transition: all 0.3s ease-in-out;
        }
        .auth-links a:hover {
            background-color: white;
            color: #0d47a1;
        }
        .main-content {
            text-align: center;
            padding: 80px 20px;
            background: #fff;
        }
        .main-content h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #0d47a1;
        }
        .main-content p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
        }
        .cta-btn {
            display: inline-block;
            background-color: #0d47a1;
            color: white;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 5px;
            font-size: 1.3rem;
            transition: background-color 0.3s;
            font-weight: bold;
        }
        .cta-btn:hover {
            background-color: #1976d2;
        }
        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 50px 20px;
            background: #e3f2fd;
        }
        .feature-box {
            background: white;
            padding: 30px;
            width: 300px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }
        .feature-box:hover {
            transform: translateY(-5px);
        }
        .feature-box h3 {
            color: #0d47a1;
            margin-bottom: 10px;
        }
        .feature-box p {
            font-size: 1rem;
            color: #555;
        }
        .footer {
            background: #0d47a1;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
            font-size: 1rem;
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

    <section class="main-content">
        <h2>Start Learning</h2>
        <p>Join thousands of learners in exploring cybersecurity threats, simulated attacks, and defense strategies. Protect yourself and your organization.</p>
        <a href="<?php echo $loggedIn ? 'categ.php' : 'login.php'; ?>" class="cta-btn">Choose Your Lesson</a>
    </section>

    <section class="features">
        <div class="feature-box">
            <h3>Realistic Attack Simulations</h3>
            <p>Experience hands-on attack scenarios that prepare you for real-world cyber threats.</p>
        </div>
        <div class="feature-box">
            <h3>Expert Guidance</h3>
            <p>Learn from cybersecurity professionals with deep industry knowledge.</p>
        </div>
        <div class="feature-box">
            <h3>Interactive Courses</h3>
            <p>Engage with practical exercises that solidify your knowledge of cyber defense.</p>
        </div>
    </section>

    <footer class="footer">
        &copy; <?php echo date("Y"); ?> Cybersecurity Training Platform | All Rights Reserved.
    </footer>
</body>
</html>
