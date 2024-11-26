<?php
require 'includes/GoogleAuthenticator.php';
session_start();
include 'db_connection.php'; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if the username exists
    $query = $conn->prepare("SELECT id, password, is_2fa_enabled, 2fa_secret FROM users WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $query->store_result();

    if ($query->num_rows > 0) {
        $query->bind_result($id, $hashed_password, $is_2fa_enabled, $secret);
        $query->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            if ($is_2fa_enabled) {
                if (!isset($_POST['2fa_code'])) {
                    // Prompt for the 2FA code
                    echo "<form method='POST'>";
                    echo "<input type='hidden' name='username' value='$username'>";
                    echo "<input type='hidden' name='password' value='$password'>";
                    echo "<label for='2fa_code'>Enter 2FA Code:</label>";
                    echo "<input type='text' name='2fa_code' id='2fa_code' required>";
                    echo "<button type='submit'>Verify</button>";
                    echo "</form>";
                    exit;
                } else {
                    // Verify the 2FA code
                    $gAuth = new PHPGangsta_GoogleAuthenticator();
                    $code = $_POST['2fa_code'];

                    if ($gAuth->verifyCode($secret, $code, 2)) { // 2 = 2-minute tolerance
                        $_SESSION['user_id'] = $id;
                        echo "Login successful!";
                        header("Location: index.php");
                        exit();
                    } else {
                        echo "Invalid 2FA code. Please try again.";
                    }
                }
            } else {
                // 2FA is not enabled
                $_SESSION['user_id'] = $id;
                echo "Login successful!";
                header("Location: index.php");
                exit();
            }
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Invalid username or password.";
    }

    $query->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
<form action="login.php" method="POST">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Login</button>
</form>
</body>
</html>
