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

include('../controller/db_connection.php');; // Include the database connection file

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
                    ?>
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Security Question - Cybersecurity Training Platform</title>
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

                            .security-container {
                                background: white;
                                padding: 40px;
                                border-radius: 10px;
                                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                                width: 100%;
                                max-width: 400px;
                            }

                            .security-header {
                                text-align: center;
                                margin-bottom: 30px;
                            }

                            .security-header img {
                                width: 150px;
                                margin-bottom: 20px;
                            }

                            .security-header h1 {
                                color: var(--primary-blue);
                                font-size: 2em;
                                margin-bottom: 10px;
                            }

                            .form-group {
                                margin-bottom: 20px;
                            }

                            label {
                                display: block;
                                margin-bottom: 12px;
                                color: #333;
                                font-weight: 500;
                                font-size: 1.1em;
                            }

                            input[type="text"] {
                                width: 100%;
                                padding: 12px;
                                border: 2px solid #e1e1e1;
                                border-radius: 5px;
                                font-size: 16px;
                                transition: border-color 0.3s ease;
                            }

                            input[type="text"]:focus {
                                border-color: var(--primary-blue);
                                outline: none;
                                box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
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
                                transition: all 0.3s ease;
                                margin-top: 10px;
                            }

                            .submit-btn:hover {
                                background-color: var(--hover-blue);
                                transform: translateY(-1px);
                            }

                            .submit-btn:active {
                                transform: translateY(0);
                            }

                            .back-link {
                                display: block;
                                text-align: center;
                                margin-top: 20px;
                                color: var(--primary-blue);
                                text-decoration: none;
                                font-size: 14px;
                            }

                            .back-link:hover {
                                text-decoration: underline;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="security-container">
                            <div class="security-header">
                                <h1>Security Verification</h1>
                                <p>Please answer your security question to continue</p>
                            </div>

                            <form method="POST" action="">
                                <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                                <input type="hidden" name="password" value="<?php echo htmlspecialchars($password); ?>">
                                
                                <div class="form-group">
                                    <label><?php echo htmlspecialchars($security_question); ?></label>
                                    <input type="text" name="security_answer" required autofocus>
                                </div>

                                <button type="submit" class="submit-btn">Verify</button>
                            </form>

                            <a href="login.php" class="back-link">← Back to Login</a>
                        </div>
                    </body>
                    </html>
                    <?php
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
                header("Location: admin_dashboard.php");
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
                header("Location: admin_dashboard.php");
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
    <title>Login - Cybersecurity Training Platform</title>
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

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
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
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
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
            margin: 0 10px;
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

        /* 2FA Form Styling */
        .security-question-form {
            background: var(--light-gray);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .security-question {
            font-weight: 500;
            color: #333;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Login</h1>
            <p>Welcome back to the Cybersecurity Training Platform</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!isset($show_security_question)): ?>
            <form method="POST" action="">
                <div class="form-group">
            <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
            <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="submit-btn">Login</button>
            </form>
        <?php else: ?>
            <div class="security-container">
                <div class="security-header">
                    <h1>Security Verification</h1>
                    <p>Please answer your security question to continue</p>
                </div>

                <form method="POST" action="">
                    <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                    <input type="hidden" name="password" value="<?php echo htmlspecialchars($password); ?>">
                    
                    <div class="form-group">
                        <label><?php echo htmlspecialchars($security_question); ?></label>
                        <input type="text" name="security_answer" required autofocus>
                    </div>

                    <button type="submit" class="submit-btn">Verify</button>
        </form>

                <a href="login.php" class="back-link">← Back to Login</a>
            </div>
        <?php endif; ?>

        <div class="links">
            <a href="signup.php">Create Account</a>
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>
</body>
</html>
