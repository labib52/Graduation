<?php
session_start();
include('../controller/db_connection.php');
include '../controller/admin_functions.php';

// Handle student enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enroll_student'])) {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    
    if (!empty($student_id) && !empty($course_id)) {
        enrollStudent($conn, $student_id, $course_id);
        header("Location: admin_enrollment.php");
        exit();
    }
}

// Fetch all students and courses
$students = getNonAdminStudents($conn); // Only fetch students where is_admin = 0
$courses = getCourses($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Student</title>
        <link rel="stylesheet" href="../public/CSS/admin_styles.css">

</head>
<body>
    <header>
        <h1>Enroll Student in Course</h1>
        <a href="admin_dashboard.php" class="back">Back to Dashboard</a>
    </header>
    <main>
        <form method="POST" class="enrollment-form">
            <label for="student_id">Select Student:</label>
            <select name="student_id" required>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo $student['id']; ?>">
                        <?php echo htmlspecialchars($student['username']); ?> (<?php echo htmlspecialchars($student['email']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="course_id">Select Course:</label>
            <select name="course_id" required>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['id']; ?>">
                        <?php echo htmlspecialchars($course['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" name="enroll_student">Enroll</button>
        </form>
    </main>
</body>
</html>
