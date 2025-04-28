<?php
session_start();
include('../controller/db_connection.php'); // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}

// Fetch courses the user is enrolled in
$enrolled_courses_query = $conn->prepare("
    SELECT courses.title 
    FROM enrollments 
    INNER JOIN courses ON enrollments.course_id = courses.id 
    WHERE enrollments.student_id = ?
");
$enrolled_courses_query->bind_param("i", $user_id);
$enrolled_courses_query->execute();
$courses_result = $enrolled_courses_query->get_result();
$courses = [];
while ($row = $courses_result->fetch_assoc()) {
    $courses[] = $row['title'];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $username = $_POST['username'];
        $payment_info = $_POST['payment_info'];
        $is_2fa_enabled = isset($_POST['is_2fa_enabled']) ? 1 : 0;

        // Handle disabling 2FA
        if ($user['is_2fa_enabled'] && !$is_2fa_enabled) {
            $security_answer = $_POST['security_answer'] ?? '';
            if (!empty($security_answer) && password_verify($security_answer, $user['security_answer'])) {
                $disable_2fa_query = $conn->prepare("UPDATE users SET is_2fa_enabled = 0, security_question = NULL, security_answer = NULL WHERE id = ?");
                $disable_2fa_query->bind_param("i", $user_id);
                $disable_2fa_query->execute();
                $user['is_2fa_enabled'] = 0; // Update the user array for immediate UI update
            } else {
                $error = "Incorrect security answer.";
            }
        }

        // Handle enabling 2FA
        if ($is_2fa_enabled && empty($user['security_question'])) {
            $security_question = $_POST['security_question'] ?? '';
            $security_answer = $_POST['security_answer'] ?? '';
            if (!empty($security_question) && !empty($security_answer)) {
                $hashed_answer = password_hash($security_answer, PASSWORD_DEFAULT);
                $security_query = $conn->prepare("UPDATE users SET security_question = ?, security_answer = ? WHERE id = ?");
                $security_query->bind_param("ssi", $security_question, $hashed_answer, $user_id);
                $security_query->execute();
                $user['is_2fa_enabled'] = 1; // Update the user array for immediate UI update
            } else {
                $error = "Security question and answer must be provided to enable 2FA.";
            }
        }

        // Update user details
        if (!isset($error)) {
            $update_query = $conn->prepare("UPDATE users SET username = ?, payment_info = ?, is_2fa_enabled = ? WHERE id = ?");
            $update_query->bind_param("ssii", $username, $payment_info, $is_2fa_enabled, $user_id);
            $update_query->execute();
            header('Location: view_profile.php');
            exit;
        }
    }
}

// Language handling
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Translations
$translations = [
    'en' => [
        'your_profile' => 'Your Profile',
        'username' => 'Username',
        'email' => 'Email',
        'payment_info' => 'Payment Info',
        'account_created' => 'Account Created At',
        '2fa_status' => '2FA Status',
        'security_question' => 'Security Question',
        'security_answer' => 'Security Answer',
        'required_disable_2fa' => 'Required to disable 2FA',
        'select_security_question' => 'Select Security Question',
        'answer' => 'Answer',
        'enrolled_courses' => 'Enrolled Courses',
        'update_profile' => 'Update Profile',
        'back' => '← Back',
        'pet_name' => "What is your pet's name?",
        'maiden_name' => "What is your mother's maiden name?",
        'favorite_color' => "What is your favorite color?"
    ],
    'ar' => [
        'your_profile' => 'ملفك الشخصي',
        'username' => 'اسم المستخدم',
        'email' => 'البريد الإلكتروني',
        'payment_info' => 'معلومات الدفع',
        'account_created' => 'تم إنشاء الحساب في',
        '2fa_status' => 'حالة المصادقة الثنائية',
        'security_question' => 'سؤال الأمان',
        'security_answer' => 'إجابة الأمان',
        'required_disable_2fa' => 'مطلوب لتعطيل المصادقة الثنائية',
        'select_security_question' => 'اختر سؤال الأمان',
        'answer' => 'الإجابة',
        'enrolled_courses' => 'الدورات المسجلة',
        'update_profile' => 'تحديث الملف الشخصي',
        'back' => '← رجوع',
        'pet_name' => "ما اسم حيوانك الأليف؟",
        'maiden_name' => "ما هو اسم عائلة والدتك قبل الزواج؟",
        'favorite_color' => "ما هو لونك المفضل؟"
    ]
];
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/CSS/View_profile.css">
    <title><?php echo $translations[$lang]['your_profile']; ?></title>
</head>
<body>
    <div class="container">
        <div class="language-switcher">
            <?php if ($lang === 'en'): ?>
                <a href="?lang=ar">العربية</a>
            <?php else: ?>
                <a href="?lang=en">English</a>
            <?php endif; ?>
        </div>
        <h1><?php echo $translations[$lang]['your_profile']; ?></h1>
        <form method="POST">
            <div class="form-group">
                <label><?php echo $translations[$lang]['username']; ?></label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label><?php echo $translations[$lang]['email']; ?></label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            <div class="form-group">
                <label><?php echo $translations[$lang]['payment_info']; ?></label>
                <textarea name="payment_info"><?php echo htmlspecialchars($user['payment_info']); ?></textarea>
            </div>
            <div class="form-group">
                <label><?php echo $translations[$lang]['account_created']; ?></label>
                <input type="text" value="<?php echo htmlspecialchars($user['created_at']); ?>" disabled>
            </div>
            <div class="form-group">
                <label><?php echo $translations[$lang]['2fa_status']; ?></label>
                <input type="checkbox" name="is_2fa_enabled" <?php echo $user['is_2fa_enabled'] ? 'checked' : ''; ?>>
            </div>
            <?php if ($user['is_2fa_enabled']): ?>
                <div class="form-group">
                    <label><?php echo $translations[$lang]['security_question']; ?></label>
                    <input type="text" value="<?php echo htmlspecialchars($user['security_question']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label><?php echo $translations[$lang]['security_answer']; ?></label>
                    <input type="text" name="security_answer">
                    <small><?php echo $translations[$lang]['required_disable_2fa']; ?></small>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label><?php echo $translations[$lang]['select_security_question']; ?></label>
                    <select name="security_question">
                        <option value="<?php echo $translations[$lang]['pet_name']; ?>"><?php echo $translations[$lang]['pet_name']; ?></option>
                        <option value="<?php echo $translations[$lang]['maiden_name']; ?>"><?php echo $translations[$lang]['maiden_name']; ?></option>
                        <option value="<?php echo $translations[$lang]['favorite_color']; ?>"><?php echo $translations[$lang]['favorite_color']; ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label><?php echo $translations[$lang]['answer']; ?></label>
                    <input type="text" name="security_answer">
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label><?php echo $translations[$lang]['enrolled_courses']; ?></label>
                <ul>
                    <?php foreach ($courses as $course): ?>
                        <li><?php echo htmlspecialchars($course); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <button type="submit" name="update_profile"><?php echo $translations[$lang]['update_profile']; ?></button>
        </form>
        <a href="homepage.php" class="back-button"><?php echo $translations[$lang]['back']; ?></a>
    </div>
</body>
</html>
