<?php
session_start();
include 'db_connection.php'; // Database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if email or username already exists
    $checkQuery = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $checkQuery->bind_param("ss", $username, $email);
    $checkQuery->execute();
    $checkQuery->store_result();

    if ($checkQuery->num_rows > 0) {
        echo "Username or email already taken.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert the user into the database
        $insertQuery = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $insertQuery->bind_param("sss", $username, $email, $hashed_password);

        if ($insertQuery->execute()) {
            echo "Registration successful!";
            header("Location: index.php");
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
    <title>SignUp</title>
</head>
<body>
<form action="signup.php" method="POST">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <button type="submit">Sign Up</button>
</form>
</body>
</html>

