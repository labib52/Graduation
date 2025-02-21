<?php
session_start();

// Prevent session hijacking
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = md5($_SERVER['HTTP_USER_AGENT']);
} elseif ($_SESSION['user_agent'] !== md5($_SERVER['HTTP_USER_AGENT'])) {
    // Destroy the session if the user agent doesn't match
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Secure the session
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true); // Regenerate session ID to prevent fixation
    $_SESSION['initiated'] = true;
}

// Redirect to homepage if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: homepage.php");
    exit();
}

include 'db_connection.php'; // Include the database connection file

$errors = ["username" => "", "password" => ""];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if the username exists
    $query = $conn->prepare("SELECT id, password, is_2fa_enabled, security_question, security_answer, is_admin FROM users WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $query->store_result();

    if ($query->num_rows > 0) {
        $query->bind_result($id, $hashed_password, $is_2fa_enabled, $security_question, $security_answer, $is_admin);
        $query->fetch();
    
        // Verify the password
        if (password_verify($password, $hashed_password)) {
            if ($is_2fa_enabled) {
                if (!isset($_POST['security_answer'])) {
                    // Prompt for the security question
                    echo "<!DOCTYPE html>";
                    echo "<html lang='en'>";
                    echo "<head>";
                    echo "<meta charset='UTF-8'>";
                    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
                    echo "<title>Security Question</title>";
                    echo "<link href='https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap' rel='stylesheet'>";
                    echo "<style>";
                    echo "body {";
                    echo "    font-family: 'Roboto', sans-serif;";
                    echo "    background-color: #f3f4f6;";
                    echo "    display: flex;";
                    echo "    justify-content: center;";
                    echo "    align-items: center;";
                    echo "    min-height: 100vh;";
                    echo "    margin: 0;";
                    echo "}";
                    echo ".container {";
                    echo "    background: #fff;";
                    echo "    border-radius: 8px;";
                    echo "    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);";
                    echo "    max-width: 500px;";
                    echo "    width: 100%;";
                    echo "    padding: 20px;";
                    echo "    position: relative;";
                    echo "}";
                    echo "header {";
                    echo "    text-align: center;";
                    echo "    margin-bottom: 20px;";
                    echo "    background-color: #4caf50;";
                    echo "    padding: 20px;";
                    echo "    color: white;";
                    echo "    border-radius: 8px;";
                    echo "    font-family: 'Roboto', sans-serif;";
                    echo "    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);";
                    echo "}";
                    echo "header h1 {";
                    echo "    margin: 0;";
                    echo "    font-size: 2.5em;";
                    echo "    font-weight: bold;";
                    echo "}";
                    echo "h2 {";
                    echo "    margin-bottom: 20px;";
                    echo "    text-align: center;";
                    echo "    color: #333;";
                    echo "}";
                    echo "label {";
                    echo "    font-weight: 500;";
                    echo "    margin-top: 10px;";
                    echo "    display: block;";
                    echo "    color: #555;";
                    echo "}";
                    echo "input, button {";
                    echo "    width: 100%;";
                    echo "    padding: 10px;";
                    echo "    margin-top: 5px;";
                    echo "    margin-bottom: 15px;";
                    echo "    border: 1px solid #ccc;";
                    echo "    border-radius: 5px;";
                    echo "    font-size: 1em;";
                    echo "}";
                    echo "button {";
                    echo "    background-color: #4caf50;";
                    echo "    color: white;";
                    echo "    border: none;";
                    echo "    cursor: pointer;";
                    echo "}";
                    echo "button:hover {";
                    echo "    background-color: #45a049;";
                    echo "}";
                    echo "</style>";
                    echo "</head>";
                    echo "<body>";
                    echo "<header><h1>CyberWise</h1></header>";
                    echo "<div class='container'>";
                    echo "<h2>Security Question</h2>";
                    echo "<form method='POST'>";
                    echo "<input type='hidden' name='username' value='$username'>";
                    echo "<input type='hidden' name='password' value='$password'>";
                    echo "<label for='security_answer'>" . htmlspecialchars($security_question) . ":</label>";
                    echo "<input type='text' name='security_answer' id='security_answer' required>";
                    echo "<button type='submit'>Verify</button>";
                    echo "</form>";
                    echo "</div>";
                    echo "</body>";
                    echo "</html>";
                    exit;
                }
                else {
                    // Verify the security answer
                    $provided_answer = trim($_POST['security_answer']);
                    if (password_verify($provided_answer, $security_answer)) {
                        // Start a session and store user data
                        $_SESSION['user_id'] = $id;
                        $_SESSION['username'] = $username;
                        $_SESSION['is_admin'] = $is_admin;
                        // Redirect based on user role
            if ($is_admin == 1) {
                header("Location: admin/admin_dashboard.php");
            } else {
                header("Location: homepage.php");
            }
            exit();
                        
                    } else {
                        // Invalid security answer
                        echo "Invalid security answer. Please try again.";
                    }
                }
            } else {
                // 2FA is not enabled
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['is_admin'] = $is_admin;
                $_SESSION['is_2fa_enabled'] = $is_2fa_enabled;
                // Redirect based on user role
            if ($is_admin == 1) {
                header("Location: admin/admin_dashboard.php");
            } else {
                header("Location: homepage.php");
            }
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
        <button> <a href="signup.php">Signup</a></button>
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
