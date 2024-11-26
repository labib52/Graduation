<?php
session_start();

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);

// Get the username from the session if logged in
$username = $loggedIn ? htmlspecialchars($_SESSION['username']) : "Guest";
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

        /* Header */
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

        .header p {
            margin: 10px 0;
            font-size: 1.2rem;
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

        /* Lessons Section */
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

        /* Features Section */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 50px 20px;
            background: white;
        }

        .feature-box {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            background: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .feature-box h3 {
            margin-bottom: 10px;
            font-size: 1.5rem;
            color: #1976d2;
        }

        .feature-box p {
            font-size: 1rem;
        }

        /* Feedback Section */
        .feedback {
            padding: 60px 20px;
            background: #f4f4f4;
            text-align: center;
        }

        .feedback h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #333;
        }

        .feedback .feedback-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .feedback-card {
            max-width: 300px;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .feedback-card p {
            font-size: 1rem;
            color: #555;
            margin-bottom: 15px;
        }

        .feedback-card .author {
            font-weight: bold;
            color: #0d47a1;
        }

        /* About Us Section */
        .about-us {
            padding: 60px 20px;
            background: #f4f4f4;
            text-align: center;
        }

        .about-us h2 {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .about-us p {
            font-size: 1.1rem;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Footer */
        .footer {
            background: #0d47a1;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .footer p {
            margin: 0;
            font-size: 0.9rem;
        }

        .footer a {
            color: #bbdefb;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="auth-links">
            <?php if ($loggedIn): ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
        </div>
        <h1>Welcome, <?php echo $username; ?>!</h1>
        <p>Your gateway to mastering cybersecurity through real-world simulations and expert knowledge.</p>
    </header>

    <!-- Lessons Section -->
    <section class="lessons">
        <h2>Start Learning</h2>
        <p>Select a lesson to begin your journey toward becoming a cybersecurity expert.</p>
        <a href="<?php echo $loggedIn ? 'categ.php' : 'login.php'; ?>">Choose Your Lesson</a>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="feature-box">
            <h3>Interactive Lessons</h3>
            <p>Hands-on simulations and in-depth content to solidify your cybersecurity expertise.</p>
        </div>
        <div class="feature-box">
            <h3>Real-world Scenarios</h3>
            <p>Practice tackling realistic cybersecurity challenges in a safe environment.</p>
        </div>
        <div class="feature-box">
            <h3>Expert Guidance</h3>
            <p>Learn from professionals with years of experience in the cybersecurity industry.</p>
        </div>
    </section>

    <!-- Feedback Section -->
    <section class="feedback">
        <h2>What Our Users Say</h2>
        <div class="feedback-container">
            <div class="feedback-card">
                <p>"This platform has completely changed the way I approach cybersecurity. The lessons are interactive and easy to follow."</p>
                <p class="author">- Alex J.</p>
            </div>
            <div class="feedback-card">
                <p>"I love how practical the simulations are. I feel confident applying what I've learned in real-world scenarios."</p>
                <p class="author">- Maria K.</p>
            </div>
            <div class="feedback-card">
                <p>"As a beginner, I found this platform invaluable. The guidance and resources are top-notch."</p>
                <p class="author">- David L.</p>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="about-us">
        <h2>About Us</h2>
        <p>
            Our platform provides interactive lessons, engaging simulations, and valuable resources to help you
            identify, mitigate, and respond to cybersecurity threats effectively. Suitable for all levels, our training 
            offers a comprehensive learning experience.
        </p>
    </section>

    <!-- Footer Section -->
    <footer class="footer">
        <p>&copy; 2024 Cybersecurity Training Platform. All Rights Reserved.</p>
        <p>
            <a href="#">Privacy Policy</a> | 
            <a href="#">Terms of Use</a> | 
            <a href="contact.php">Contact Us</a>
        </p>
    </footer>
</body>
</html>
