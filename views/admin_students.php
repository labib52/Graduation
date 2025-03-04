<?php
session_start();
include('../controller/db_connection.php');
include '../controller/admin_functions.php';

// Handle student addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $payment_info = isset($_POST['payment_info']) ? trim($_POST['payment_info']) : '';
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $is_2fa_enabled = isset($_POST['is_2fa_enabled']) ? 1 : 0;
    $security_question = isset($_POST['security_question']) ? trim($_POST['security_question']) : NULL;
    $security_answer = isset($_POST['security_answer']) ? trim($_POST['security_answer']) : NULL;

    // Hash security_answer if provided
    $hashed_security_answer = !empty($security_answer) ? password_hash($security_answer, PASSWORD_DEFAULT) : NULL;

    if (!empty($username) && !empty($email) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        addStudent($conn, $username, $email, $hashed_password, $payment_info, $is_admin, $is_2fa_enabled, $security_question, $hashed_security_answer);
        header("Location: admin_students.php");
        exit();
    } else {
        $error_message = "Error: All required fields must be filled.";
    }
}

// Handle student update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_student'])) {
    $student_id = intval($_POST['student_id']);
    $new_username = trim($_POST['new_username']);
    $new_email = trim($_POST['new_email']);
    $new_password = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_BCRYPT) : NULL;
    $new_is_admin = isset($_POST['new_is_admin']) ? 1 : 0;
    $new_is_2fa_enabled = isset($_POST['new_is_2fa_enabled']) ? 1 : 0;
    $new_security_question = isset($_POST['new_security_question']) ? trim($_POST['new_security_question']) : NULL;
    $new_security_answer = isset($_POST['new_security_answer']) ? trim($_POST['new_security_answer']) : NULL;

    // Hash the new security_answer if provided
    $hashed_new_security_answer = !empty($new_security_answer) ? password_hash($new_security_answer, PASSWORD_DEFAULT) : NULL;

    updateStudent($conn, $student_id, $new_username, $new_email, $new_password, $new_is_admin, $new_is_2fa_enabled, $new_security_question, $hashed_new_security_answer);
    header("Location: admin_students.php");
    exit();
}

// Handle student deletion
if (isset($_GET['delete_id'])) {
    $student_id = intval($_GET['delete_id']);
    deleteStudent($conn, $student_id);
    header("Location: admin_students.php");
    exit();
}

// Fetch all students
$students = getStudents($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
        <link rel="stylesheet" href="../public/CSS/admin_styles.css">

    <script>
        function toggleSecurityFields() {
            const is2FAEnabled = document.getElementById('is_2fa_enabled').checked;
            document.getElementById('security_fields').style.display = is2FAEnabled ? 'block' : 'none';
        }

        function editStudent(id, username, email, isAdmin, is2FA, securityQuestion, securityAnswer) {
            document.getElementById('edit_student_id').value = id;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_is_admin').checked = isAdmin == 1;
            document.getElementById('edit_is_2fa_enabled').checked = is2FA == 1;
            document.getElementById('edit_security_question').value = securityQuestion;
            document.getElementById('edit_security_answer').value = securityAnswer;
            document.getElementById('editForm').style.display = 'block';
        }

        function confirmDelete(studentId) {
            if (confirm("Are you sure you want to delete this student?")) {
                window.location.href = "admin_students.php?delete_id=" + studentId;
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Manage Students</h1>
        <a href="admin_dashboard.php" class="back">Back to Dashboard</a>
    </header>
    <main>
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        
        <form method="POST" class="student-form">
            <input type="text" name="username" placeholder="Student Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="payment_info" placeholder="Payment Info (if any)">
            <label><input type="checkbox" name="is_admin"> Make Admin</label>
            <label><input type="checkbox" name="is_2fa_enabled" id="is_2fa_enabled" onclick="toggleSecurityFields()"> Enable 2FA</label>
            <div id="security_fields" style="display: none;">
                <select name="security_question">
                    <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                    <option value="What was the name of your first pet?">What was the name of your first pet?</option>
                    <option value="What was the name of your elementary school?">What was the name of your elementary school?</option>
                </select>
                <input type="text" name="security_answer" placeholder="Security Answer">
            </div>
            <button type="submit" name="add_student">Add Student</button>
        </form>

        <section class="student-list">
            <h2>Registered Students</h2>
            <ul>
                <?php foreach ($students as $student): ?>
                    <li>
                        <?php echo htmlspecialchars($student['username']); ?> - <?php echo htmlspecialchars($student['email']); ?>
                        <button onclick="editStudent(
                            <?php echo $student['id']; ?>,
                            '<?php echo htmlspecialchars($student['username']); ?>',
                            '<?php echo htmlspecialchars($student['email']); ?>',
                            <?php echo $student['is_admin']; ?>,
                            <?php echo $student['is_2fa_enabled']; ?>,
                            '<?php echo htmlspecialchars($student['security_question']); ?>',
                            ''
                        )">Edit</button>
                        <button onclick="confirmDelete(<?php echo $student['id']; ?>)" class="delete">Remove</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>

    <!-- Hidden Edit Form -->
    <div id="editForm" style="display: none;">
        <h3>Edit Student</h3>
        <form method="POST" action="admin_students.php">
            <input type="hidden" name="student_id" id="edit_student_id">
            <input type="text" name="new_username" id="edit_username" required>
            <input type="email" name="new_email" id="edit_email" required>
            <input type="password" name="new_password" placeholder="New Password (leave blank to keep current)">
            <label><input type="checkbox" name="new_is_admin" id="edit_is_admin"> Make Admin</label>
            <label><input type="checkbox" name="new_is_2fa_enabled" id="edit_is_2fa_enabled"> Enable 2FA</label>
            <select name="new_security_question" id="edit_security_question">
                <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                <option value="What was the name of your first pet?">What was the name of your first pet?</option>
                <option value="What was the name of your elementary school?">What was the name of your elementary school?</option>
            </select>
            <input type="text" name="new_security_answer" id="edit_security_answer" placeholder="New Security Answer">
            <button type="submit" name="update_student">Update Student</button>
        </form>
    </div>
</body>
</html>
