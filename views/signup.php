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
        'signup' => 'Sign Up',
        'create_account' => 'Create Account',
        'join_platform' => 'Join the Cybersecurity Training Platform',
        'username' => 'Username:',
        'email' => 'Email:',
        'password' => 'Password:',
        'confirm_password' => 'Confirm Password:',
        'two_factor' => 'Two-Factor Authentication',
        'enable_2fa' => 'Enable Two-Factor Authentication',
        'security_question' => 'Security Question:',
        'security_answer' => 'Security Answer:',
        'already_have_account' => 'Already have an account? Login',
        'required' => 'This field is required.',
        'invalid_email' => 'Invalid email format.',
        'password_requirements' => 'Password must be at least 8 characters, include 1 capital letter, and 1 symbol.',
        'passwords_no_match' => "Passwords don't match.",
        'security_required' => 'Both security question and answer are required when 2FA is enabled.',
        'username_email_taken' => 'Username or email is already taken.',
        'registration_success' => 'Registration successful!',
        'error' => 'Error:',
        'pet_name' => "What was your first pet's name?",
        'maiden_name' => "What is your mother's maiden name?",
        'city_born' => "What city were you born in?",
        'favorite_book' => "What is your favorite book?",
    ],
    'ar' => [
        'signup' => 'إنشاء حساب',
        'create_account' => 'إنشاء حساب',
        'join_platform' => 'انضم إلى منصة التدريب على الأمن السيبراني',
        'username' => 'اسم المستخدم:',
        'email' => 'البريد الإلكتروني:',
        'password' => 'كلمة المرور:',
        'confirm_password' => 'تأكيد كلمة المرور:',
        'two_factor' => 'المصادقة الثنائية',
        'enable_2fa' => 'تفعيل المصادقة الثنائية',
        'security_question' => 'سؤال الأمان:',
        'security_answer' => 'إجابة الأمان:',
        'already_have_account' => 'لديك حساب بالفعل؟ تسجيل الدخول',
        'required' => 'هذا الحقل مطلوب.',
        'invalid_email' => 'تنسيق البريد الإلكتروني غير صالح.',
        'password_requirements' => 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل، وتحتوي على حرف كبير ورمز واحد.',
        'passwords_no_match' => 'كلمتا المرور غير متطابقتين.',
        'security_required' => 'يجب إدخال سؤال وإجابة الأمان عند تفعيل المصادقة الثنائية.',
        'username_email_taken' => 'اسم المستخدم أو البريد الإلكتروني مستخدم بالفعل.',
        'registration_success' => 'تم التسجيل بنجاح!',
        'error' => 'خطأ:',
        'pet_name' => 'ما اسم أول حيوان أليف لديك؟',
        'maiden_name' => 'ما هو اسم عائلة والدتك قبل الزواج؟',
        'city_born' => 'في أي مدينة وُلدت؟',
        'favorite_book' => 'ما هو كتابك المفضل؟',
    ]
];

include('../controller/db_connection.php'); // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $enable_2fa = isset($_POST['enable_2fa']);
    $security_question = $enable_2fa ? trim($_POST['security_question']) : null;
    $security_answer = $enable_2fa ? trim($_POST['security_answer']) : null;

    $errors = [];

    // Validate fields
    if (empty($username)) {
        $errors['username'] = $translations[$lang]['required'];
    }
    if (empty($email)) {
        $errors['email'] = $translations[$lang]['required'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = $translations[$lang]['invalid_email'];
    }
    if (empty($password)) {
        $errors['password'] = $translations[$lang]['required'];
    } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/\W/', $password)) {
        $errors['password'] = $translations[$lang]['password_requirements'];
    }
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = $translations[$lang]['passwords_no_match'];
    }

    if ($enable_2fa && (empty($security_question) || empty($security_answer))) {
        $errors['security'] = $translations[$lang]['security_required'];
    }

    if (empty($errors)) {
        // Check if the username or email already exists
        $checkQuery = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $checkQuery->bind_param("ss", $username, $email);
        $checkQuery->execute();
        $checkQuery->store_result();

        if ($checkQuery->num_rows > 0) {
            $errors['general'] = $translations[$lang]['username_email_taken'];
        } else {
            // Hash the password securely
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $hashed_answer = $enable_2fa ? password_hash($security_answer, PASSWORD_BCRYPT) : null;

            // Insert the user into the database
            $insertQuery = $conn->prepare("INSERT INTO users (username, email, password, is_2fa_enabled, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?)");
            $insertQuery->bind_param("sssiss", $username, $email, $hashed_password, $enable_2fa, $security_question, $hashed_answer);

            if ($insertQuery->execute()) {
                // echo $translations[$lang]['registration_success'];
                header("Location: login.php");
                exit();
            } else {
                $errors['general'] = $translations[$lang]['error'] . ' ' . $insertQuery->error;
            }

            $insertQuery->close();
        }

        $checkQuery->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations[$lang]['signup']; ?> - Cybersecurity Training Platform</title>
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

        .signup-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }

        .signup-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .signup-header h1 {
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
        input[type="email"],
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus,
        select:focus {
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

        .two-fa-section {
            background: var(--light-gray);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .two-fa-section h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
        }

        .security-questions {
            margin-top: 15px;
            display: none;
        }

        .security-questions.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="language-switcher" style="text-align: right; margin-bottom: 10px;">
            <?php if ($lang === 'en'): ?>
                <a href="?lang=ar">العربية</a>
            <?php else: ?>
                <a href="?lang=en">English</a>
            <?php endif; ?>
        </div>
        <div class="signup-header">
            <h1><?php echo $translations[$lang]['create_account']; ?></h1>
            <p><?php echo $translations[$lang]['join_platform']; ?></p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="error-message"><?php echo $errors['general']; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username"><?php echo $translations[$lang]['username']; ?></label>
                <input type="text" id="username" name="username" required>
                <?php if (!empty($errors['username'])): ?>
                    <div class="error-message"><?php echo $errors['username']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email"><?php echo $translations[$lang]['email']; ?></label>
                <input type="email" id="email" name="email" required>
                <?php if (!empty($errors['email'])): ?>
                    <div class="error-message"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password"><?php echo $translations[$lang]['password']; ?></label>
                <input type="password" id="password" name="password" required>
                <?php if (!empty($errors['password'])): ?>
                    <div class="error-message"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirm_password"><?php echo $translations[$lang]['confirm_password']; ?></label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <?php if (!empty($errors['confirm_password'])): ?>
                    <div class="error-message"><?php echo $errors['confirm_password']; ?></div>
                <?php endif; ?>
            </div>

            <div class="two-fa-section">
                <h3><?php echo $translations[$lang]['two_factor']; ?></h3>
                <div class="checkbox-group">
                    <input type="checkbox" id="enable_2fa" name="enable_2fa" onchange="toggle2FA(this)">
                    <label for="enable_2fa"><?php echo $translations[$lang]['enable_2fa']; ?></label>
                </div>

                <div id="security_questions" class="security-questions">
                    <div class="form-group">
                        <label for="security_question"><?php echo $translations[$lang]['security_question']; ?></label>
                        <select name="security_question" id="security_question">
                            <option value="<?php echo $translations[$lang]['pet_name']; ?>"><?php echo $translations[$lang]['pet_name']; ?></option>
                            <option value="<?php echo $translations[$lang]['maiden_name']; ?>"><?php echo $translations[$lang]['maiden_name']; ?></option>
                            <option value="<?php echo $translations[$lang]['city_born']; ?>"><?php echo $translations[$lang]['city_born']; ?></option>
                            <option value="<?php echo $translations[$lang]['favorite_book']; ?>"><?php echo $translations[$lang]['favorite_book']; ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="security_answer"><?php echo $translations[$lang]['security_answer']; ?></label>
                        <input type="text" id="security_answer" name="security_answer">
                    </div>
                </div>
                <?php if (!empty($errors['security'])): ?>
                    <div class="error-message"><?php echo $errors['security']; ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="submit-btn"><?php echo $translations[$lang]['signup']; ?></button>
        </form>

        <div class="links">
            <a href="login.php"><?php echo $translations[$lang]['already_have_account']; ?></a>
        </div>
    </div>

    <script>
        function toggle2FA(checkbox) {
            const securityQuestions = document.getElementById('security_questions');
            if (checkbox.checked) {
                securityQuestions.classList.add('active');
                document.getElementById('security_question').required = true;
                document.getElementById('security_answer').required = true;
            } else {
                securityQuestions.classList.remove('active');
                document.getElementById('security_question').required = false;
                document.getElementById('security_answer').required = false;
            }
        }
    </script>
</body>
</html>
