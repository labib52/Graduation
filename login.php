<?php
session_start();
include 'db_connection.php'; // Include the database connection file

$errors = ["username" => "", "password" => ""];

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
            $errors['password'] = "Incorrect password.";
        }
    } else {
        $errors['username'] = "Username does not exist.";
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
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
        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 5px;
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
    </style>
</head>
<body>
    <header>
        <h1>CyberWise</h1>
    </header>
    <div class="container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
            <div class="error"><?php echo $errors['username']; ?></div>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <div class="error"><?php echo $errors['password']; ?></div>

            <button type="submit">Login</button>
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
</body>
</html>
