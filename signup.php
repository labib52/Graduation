<?php
require 'includes/GoogleAuthenticator.php'; // Include Google Authenticator library
session_start();
include 'db_connection.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $enable_2fa = isset($_POST['enable_2fa']);

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

        // Generate a 2FA secret if 2FA is enabled
        $secret = null;
        if ($enable_2fa) {
            $gAuth = new PHPGangsta_GoogleAuthenticator();
            $secret = $gAuth->createSecret();
        }

        // Insert the user into the database
        $insertQuery = $conn->prepare("INSERT INTO users (username, email, password, is_2fa_enabled, 2fa_secret) VALUES (?, ?, ?, ?, ?)");
        $insertQuery->bind_param("sssis", $username, $email, $hashed_password, $enable_2fa, $secret);

        if ($insertQuery->execute()) {
            echo "Registration successful!";
            if ($enable_2fa) {
                // Generate and display the QR code for Google Authenticator
                $qrCodeUrl = $gAuth->getQRCodeGoogleUrl('YourApp', $secret);
                echo "<p>Scan this QR code with Google Authenticator:</p>";
                echo "<img src='$qrCodeUrl' alt='QR Code'>";
            }
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
    <input type="checkbox" name="enable_2fa" id="enable_2fa">

    <button type="submit">Sign Up</button>
</form>
</body>
</html>
