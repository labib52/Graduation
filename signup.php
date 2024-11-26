<?php
session_start();
include 'db_connection.php'; // Include the database connection

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
                header("Location: homepage.php");
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
    <title>Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
header {
    text-align: center;
    margin-bottom: 20px;
    background-color: #4caf50;
    padding: 20px;
    color: white;
    border-radius: 8px;
    font-family: 'Roboto', sans-serif;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

header h1 {
    margin: 0;
    font-size: 2.5em;
    font-weight: bold;
}



        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            padding: 20px;
            position: relative;
        }
        .password-instructions {
            position: absolute;
            top: 20px;
            right: -320px;
            width: 300px;
            background-color: #eef;
            padding: 15px;
            border-radius: 8px;
            font-size: 0.9em;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        label {
            font-weight: 500;
            margin-top: 10px;
            display: block;
            color: #555;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        button {
            background-color: #4caf50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-size: 0.9em;
        }
        #security_questions {
            display: none;
        }
    </style>
</head>
<body>
<header>
    <h1>CyberWise</h1>
</header>
    <div class="container">
        <h2>Sign Up</h2>
        <form action="signup.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            <div class="error"><?php echo $errors['username'] ?? ''; ?></div>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            <div class="error"><?php echo $errors['email'] ?? ''; ?></div>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
            <div class="error"><?php echo $errors['password'] ?? ''; ?></div>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password">
            <div class="error"><?php echo $errors['confirm_password'] ?? ''; ?></div>

            <label for="enable_2fa">Enable 2FA:</label>
            <input type="checkbox" name="enable_2fa" id="enable_2fa" onchange="toggleSecurityQuestions()">

            <div id="security_questions">
                <label for="security_question">Select a Security Question:</label>
                <select name="security_question" id="security_question">
                    <option value="">-- Select a question --</option>
                    <option value="What is your mother\'s maiden name?">What is your mother's maiden name?</option>
                    <option value="What was the name of your first pet?">What was the name of your first pet?</option>
                    <option value="What was the name of your elementary school?">What was the name of your elementary school?</option>
                </select>

                <label for="security_answer">Answer:</label>
                <input type="text" name="security_answer" id="security_answer">
                <div class="error"><?php echo $errors['security'] ?? ''; ?></div>
            </div>

            <div class="error"><?php echo $errors['general'] ?? ''; ?></div>

            <button type="submit">Sign Up</button>
        </form>

        <div class="password-instructions">
            <p><strong>Password Requirements:</strong></p>
            <ul>
                <li>At least 8 characters long</li>
                <li>Include at least 1 capital letter</li>
                <li>Include at least 1 symbol</li>
            </ul>
        </div>
    </div>
    <script>
        function toggleSecurityQuestions() {
            const enable2FA = document.getElementById('enable_2fa').checked;
            const securityQuestions = document.getElementById('security_questions');
            securityQuestions.style.display = enable2FA ? 'block' : 'none';
        }
    </script>
</body>
</html>
