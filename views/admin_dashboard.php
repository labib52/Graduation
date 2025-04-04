<?php
session_start();
include('../controller/db_connection.php');;



// Fetch admin details
$admin_id = $_SESSION['user_id'];
$admin_query = "SELECT username FROM users WHERE id = ? AND is_admin = 1";
$stmt = $conn->prepare($admin_query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin_data = $result->fetch_assoc();
$admin_name = $admin_data ? $admin_data['username'] : "Admin";

// Get total students (Only count users where is_admin = 0)
$students_query = "SELECT COUNT(*) as total_students FROM users WHERE is_admin = 0";
$students_result = mysqli_query($conn, $students_query);
$students_data = mysqli_fetch_assoc($students_result);
$total_students = $students_data['total_students'];

// Get total courses (Grouped by Status)
$courses_query = "SELECT 
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_courses, 
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_courses 
                 FROM courses";
$courses_result = mysqli_query($conn, $courses_query);
$courses_data = mysqli_fetch_assoc($courses_result);
$active_courses = $courses_data['active_courses'];
$pending_courses = $courses_data['pending_courses'];
$total_courses = $active_courses + $pending_courses;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
        <link rel="stylesheet" href="../public/CSS/admin_styles.css">

</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <div class="admin-info">
            <span>Welcome, <?php echo htmlspecialchars($admin_name); ?>!</span>
            <a href="admin_logout.php">Logout</a>
        </div>
    </header>

    <main>
        <div class="dashboard-container">
            <div class="dashboard-card">
                <h2>Total Students</h2>
                <p><?php echo $total_students; ?></p>
            </div>
            <div class="dashboard-card">
                <h2>Total Courses</h2>
                <p><?php echo $total_courses; ?></p>
                <p style="color: green;">Active: <?php echo $active_courses; ?></p>
                <p style="color: orange;">Pending: <?php echo $pending_courses; ?></p>
            </div>
        </div>

        <div class="admin-links">
            <a href="admin_categories.php">Manage Categories</a>
            <a href="admin_courses.php">Manage Courses</a>
            <a href="admin_students.php">Manage Students</a>
            <a href="admin_enrollment.php">Enroll Students</a>
            <a href="admin_lectures.php">Manage lectures</a>
            <a href="admin_labs.php"> Manage Labs </a>
            <a href="admin_requests.php">Course Requests</a>
        </div>
    </main>
</body>
</html>
