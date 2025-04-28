<?php
session_start();

// Language handling
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Translations
$translations = [
    'en' => [
        'login' => 'Login',
        'welcome_back' => 'Welcome back to the Cybersecurity Training Platform',
        'username' => 'Username:',
        'password' => 'Password:',
        'verify' => 'Verify',
        'security_verification' => 'Security Verification',
        'security_question_prompt' => 'Please answer your security question to continue',
        'back_to_login' => '← Back to Login',
        'create_account' => 'Create Account',
        'forgot_password' => 'Forgot Password?',
        'invalid_security_answer' => 'Invalid security answer. Please try again.',
        'incorrect_password' => 'Incorrect password.',
        'username_not_exist' => 'Username does not exist.',
    ],
    'ar' => [
        'login' => 'تسجيل الدخول',
        'welcome_back' => 'مرحبًا بعودتك إلى منصة التدريب على الأمن السيبراني',
        'username' => 'اسم المستخدم:',
        'password' => 'كلمة المرور:',
        'verify' => 'تحقق',
        'security_verification' => 'التحقق الأمني',
        'security_question_prompt' => 'يرجى الإجابة على سؤال الأمان للمتابعة',
        'back_to_login' => '← العودة لتسجيل الدخول',
        'create_account' => 'إنشاء حساب',
        'forgot_password' => 'نسيت كلمة المرور؟',
        'invalid_security_answer' => 'إجابة الأمان غير صحيحة. حاول مرة أخرى.',
        'incorrect_password' => 'كلمة المرور غير صحيحة.',
        'username_not_exist' => 'اسم المستخدم غير موجود.',
    ]
];

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

include('../controller/db_connection.php'); // Include the database connection file

$errors = ["username" => "", "password" => ""];
$error = '';

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
                    $show_security_question = true;
                } else {
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
                        $error = $translations[$lang]['invalid_security_answer'];
                        $show_security_question = true;
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
            $errors['password'] = $translations[$lang]['incorrect_password'];
        }
    } else {
        $errors['username'] = $translations[$lang]['username_not_exist'];
    }
    
    $query->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations[$lang]['login']; ?> - Cybersecurity Training Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <?php if ($lang === 'ar'): ?>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; }
    </style>
    <?php endif; ?>
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
        <div class="language-switcher" style="text-align: right; margin-bottom: 10px;">
            <?php if ($lang === 'en'): ?>
                <a href="?lang=ar">العربية</a>
            <?php else: ?>
                <a href="?lang=en">English</a>
            <?php endif; ?>
        </div>
        <div class="login-header">
            <h1><?php echo $translations[$lang]['login']; ?></h1>
            <p><?php echo $translations[$lang]['welcome_back']; ?></p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!isset($show_security_question)): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username"><?php echo $translations[$lang]['username']; ?></label>
                    <input type="text" id="username" name="username" required>
                    <?php if (!empty($errors['username'])): ?>
                        <div class="error-message"><?php echo $errors['username']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password"><?php echo $translations[$lang]['password']; ?></label>
                    <input type="password" id="password" name="password" required>
                    <?php if (!empty($errors['password'])): ?>
                        <div class="error-message"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="submit-btn"><?php echo $translations[$lang]['login']; ?></button>
            </form>
        <?php else: ?>
            <div class="security-container">
                <div class="security-header">
                    <h1><?php echo $translations[$lang]['security_verification']; ?></h1>
                    <p><?php echo $translations[$lang]['security_question_prompt']; ?></p>
                </div>

                <form method="POST" action="">
                    <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                    <input type="hidden" name="password" value="<?php echo htmlspecialchars($password); ?>">
                    
                    <div class="form-group">
                        <label><?php echo htmlspecialchars($security_question); ?></label>
                        <input type="text" name="security_answer" required autofocus>
                    </div>

                    <button type="submit" class="submit-btn"><?php echo $translations[$lang]['verify']; ?></button>
                </form>

                <a href="login.php" class="back-link"><?php echo $translations[$lang]['back_to_login']; ?></a>
            </div>
        <?php endif; ?>

        <div class="links">
            <a href="signup.php"><?php echo $translations[$lang]['create_account']; ?></a>
            <a href="forgot_password.php"><?php echo $translations[$lang]['forgot_password']; ?></a>
        </div>
    </div>
</body>
</html>
