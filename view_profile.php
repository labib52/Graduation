<?php
session_start();
include('db_connection.php'); // Include your database connection

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .error {
            color: red;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Profile</h1>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Payment Info</label>
                <textarea name="payment_info"><?php echo htmlspecialchars($user['payment_info']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Account Created At</label>
                <input type="text" value="<?php echo htmlspecialchars($user['created_at']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>2FA Status</label>
                <input type="checkbox" name="is_2fa_enabled" <?php echo $user['is_2fa_enabled'] ? 'checked' : ''; ?>>
            </div>
            <?php if ($user['is_2fa_enabled']): ?>
                <div class="form-group">
                    <label>Security Question</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['security_question']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Security Answer</label>
                    <input type="text" name="security_answer">
                    <small>Required to disable 2FA</small>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label>Select Security Question</label>
                    <select name="security_question">
                        <option value="What is your pet's name?">What is your pet's name?</option>
                        <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                        <option value="What is your favorite color?">What is your favorite color?</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Answer</label>
                    <input type="text" name="security_answer">
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label>Enrolled Courses</label>
                <ul>
                    <?php foreach ($courses as $course): ?>
                        <li><?php echo htmlspecialchars($course); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <button type="submit" name="update_profile">Update Profile</button>
        </form>
        <a href="homepage.php" class="back-button">← Back</a>
    </div>
</body>
</html>
