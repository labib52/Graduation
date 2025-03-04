<?php
session_start();
include('../controller/db_connection.php');; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $enable_2fa = isset($_POST['enable_2fa']);
    $security_question = $enable_2fa ? trim($_POST['security_question']) : null;
    $security_answer = $enable_2fa ? trim($_POST['security_answer']) : null;

    $errors = [];

    // Validate fields
    if (empty($username)) {
        $errors['username'] = "This field is required.";
    }
    if (empty($email)) {
        $errors['email'] = "This field is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors['password'] = "This field is required.";
    } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/\W/', $password)) {
        $errors['password'] = "Password must be at least 8 characters, include 1 capital letter, and 1 symbol.";
    }
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords don't match.";
    }

    if ($enable_2fa && (empty($security_question) || empty($security_answer))) {
        $errors['security'] = "Both security question and answer are required when 2FA is enabled.";
    }

    if (empty($errors)) {
        // Check if the username or email already exists
        $checkQuery = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $checkQuery->bind_param("ss", $username, $email);
        $checkQuery->execute();
        $checkQuery->store_result();

        if ($checkQuery->num_rows > 0) {
            $errors['general'] = "Username or email is already taken.";
        } else {
            // Hash the password securely
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $hashed_answer = $enable_2fa ? password_hash($security_answer, PASSWORD_BCRYPT) : null;

            // Insert the user into the database
            $insertQuery = $conn->prepare("INSERT INTO users (username, email, password, is_2fa_enabled, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?)");
            $insertQuery->bind_param("sssiss", $username, $email, $hashed_password, $enable_2fa, $security_question, $hashed_answer);

            if ($insertQuery->execute()) {
                echo "Registration successful!";
                header("Location: login.php");
                exit();
            } else {
                $errors['general'] = "Error: " . $insertQuery->error;
            }

            $insertQuery->close();
        }

        $checkQuery->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Cybersecurity Training Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #007bff;
            --hover-blue: #0056b3;
            --danger-red: #dc3545;
            --success-green: #28a745;
            --light-gray: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .signup-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }

        .signup-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .signup-header h1 {
            color: var(--primary-blue);
            font-size: 2em;
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus,
        select:focus {
            border-color: var(--primary-blue);
            outline: none;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: var(--hover-blue);
        }

        .links {
            margin-top: 20px;
            text-align: center;
        }

        .links a {
            color: var(--primary-blue);
            text-decoration: none;
            font-size: 14px;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .error-message {
            background-color: #fde8e8;
            color: var(--danger-red);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success-message {
            background-color: #e8f5e9;
            color: var(--success-green);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .two-fa-section {
            background: var(--light-gray);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .two-fa-section h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
        }

        .security-questions {
            margin-top: 15px;
            display: none;
        }

        .security-questions.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-header">
            <h1>Create Account</h1>
            <p>Join the Cybersecurity Training Platform</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="two-fa-section">
                <h3>Two-Factor Authentication</h3>
                <div class="checkbox-group">
                    <input type="checkbox" id="enable_2fa" name="enable_2fa" onchange="toggle2FA(this)">
                    <label for="enable_2fa">Enable Two-Factor Authentication</label>
                </div>

                <div id="security_questions" class="security-questions">
                    <div class="form-group">
                        <label for="security_question">Security Question:</label>
                        <select name="security_question" id="security_question">
                            <option value="What was your first pet's name?">What was your first pet's name?</option>
                            <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                            <option value="What city were you born in?">What city were you born in?</option>
                            <option value="What is your favorite book?">What is your favorite book?</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="security_answer">Security Answer:</label>
                        <input type="text" id="security_answer" name="security_answer">
                    </div>
                </div>
            </div>

            <button type="submit" class="submit-btn">Sign Up</button>
        </form>

        <div class="links">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </div>

    <script>
        function toggle2FA(checkbox) {
            const securityQuestions = document.getElementById('security_questions');
            if (checkbox.checked) {
                securityQuestions.classList.add('active');
                document.getElementById('security_question').required = true;
                document.getElementById('security_answer').required = true;
            } else {
                securityQuestions.classList.remove('active');
                document.getElementById('security_question').required = false;
                document.getElementById('security_answer').required = false;
            }
        }
    </script>
</body>
</html>
