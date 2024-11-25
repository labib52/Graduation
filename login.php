<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Fetch the user record
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();

    if ($user_id && password_verify($password, $hashed_password)) {
        // Set session variables
        $_SESSION['user_id'] = $user_id;
        header("Location: index.php");
    } else {
        echo "Invalid username or password.";
    }

    $stmt->close();
    $conn->close();
}
?>

<form action="login.php" method="POST">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <button type="submit">Login</button>
</form>
