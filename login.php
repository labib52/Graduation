<?php
session_start();
include 'db_connection.php'; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if the username exists
    $query = $conn->prepare("SELECT id, password, is_2fa_enabled, security_question, security_answer FROM users WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $query->store_result();

    if ($query->num_rows > 0) {
        $query->bind_result($id, $hashed_password, $is_2fa_enabled, $security_question, $security_answer);
        $query->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            if ($is_2fa_enabled) {
                if (!isset($_POST['security_answer'])) {
                    // Prompt for the security question
                    echo "<form method='POST'>";
                    echo "<input type='hidden' name='username' value='$username'>";
                    echo "<input type='hidden' name='password' value='$password'>";
                    echo "<label for='security_answer'>" . htmlspecialchars($security_question) . ":</label>";
                    echo "<input type='text' name='security_answer' id='security_answer' required>";
                    echo "<button type='submit'>Verify</button>";
                    echo "</form>";
                    exit;
                } else {
                    // Verify the security answer
                    $provided_answer = trim($_POST['security_answer']);
                    if (password_verify($provided_answer, $security_answer)) {
                        $_SESSION['user_id'] = $id;
                        echo "Login successful!";
                        header("Location: index.php");
                        exit();
                    } else {
                        echo "Invalid security answer. Please try again.";
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
