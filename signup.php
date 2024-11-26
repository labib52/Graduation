<?php
session_start();
include 'db_connection.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $enable_2fa = isset($_POST['enable_2fa']);
    $security_question = $enable_2fa ? trim($_POST['security_question']) : null;
    $security_answer = $enable_2fa ? trim($_POST['security_answer']) : null;

    // Check if the username or email already exists
    $checkQuery = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $checkQuery->bind_param("ss", $username, $email);
    $checkQuery->execute();
    $checkQuery->store_result();

    if ($checkQuery->num_rows > 0) {
        echo "Username or email is already taken.";
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
            echo "Error: " . $insertQuery->error;
        }

        $insertQuery->close();
    }

    $checkQuery->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body>
<form action="signup.php" method="POST">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>

    <label for="enable_2fa">Enable 2FA:</label>
    <input type="checkbox" name="enable_2fa" id="enable_2fa" onchange="toggleSecurityQuestions()">

    <div id="security_questions" style="display: none;">
        <label for="security_question">Select a Security Question:</label>
        <select name="security_question" id="security_question">
            <option value="What is your mother\'s maiden name?">What is your mother's maiden name?</option>
            <option value="What was the name of your first pet?">What was the name of your first pet?</option>
            <option value="What was the name of your elementary school?">What was the name of your elementary school?</option>
        </select>

        <label for="security_answer">Answer:</label>
        <input type="text" name="security_answer" id="security_answer">
    </div>

    <button type="submit">Sign Up</button>
</form>

<script>
function toggleSecurityQuestions() {
    const enable2FA = document.getElementById('enable_2fa').checked;
    const securityQuestions = document.getElementById('security_questions');
    securityQuestions.style.display = enable2FA ? 'block' : 'none';
}
</script>
</body>
</html>
