<?php
session_start();
include('../controller/db_connection.php');

// Language handling
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Translations
$translations = [
    'en' => [
        'admin_dashboard' => 'Admin Dashboard',
        'welcome' => 'Welcome',
        'logout' => 'Logout',
        'total_students' => 'Total Students',
        'total_courses' => 'Total Courses',
        'active' => 'Active',
        'pending' => 'Pending',
        'manage_categories' => 'Manage Categories',
        'manage_courses' => 'Manage Courses',
        'manage_students' => 'Manage Students',
        'enroll_students' => 'Enroll Students',
        'manage_lectures' => 'Manage Lectures',
        'manage_labs' => 'Manage Labs',
        'course_requests' => 'Course Requests',
        'statistics' => 'Statistics'
    ],
    'ar' => [
        'admin_dashboard' => 'لوحة التحكم',
        'welcome' => 'مرحباً',
        'logout' => 'تسجيل الخروج',
        'total_students' => 'إجمالي الطلاب',
        'total_courses' => 'إجمالي الدورات',
        'active' => 'نشط',
        'pending' => 'قيد الانتظار',
        'manage_categories' => 'إدارة التصنيفات',
        'manage_courses' => 'إدارة الدورات',
        'manage_students' => 'إدارة الطلاب',
        'enroll_students' => 'تسجيل الطلاب',
        'manage_lectures' => 'إدارة المحاضرات',
        'manage_labs' => 'إدارة المعامل',
        'course_requests' => 'طلبات الدورات',
        'statistics' => 'الإحصائيات'
    ]
];

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
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations[$lang]['admin_dashboard']; ?></title>
    <link rel="stylesheet" href="../public/CSS/admin_styles.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
        }
        header {
            background-color: #3498db;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .admin-info {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
            gap: 20px;
        }
        .admin-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .admin-actions a {
            background-color: #007BFF;
            padding: 8px 15px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-weight: 500;
            font-size: 14px;
        }
        .admin-actions a:hover {
            background-color: #0056b3;
        }
        .dashboard-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            padding: 40px;
            flex-wrap: wrap;
        }
        .dashboard-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
            width: 280px;
        }
        .dashboard-card h2 {
            margin-bottom: 15px;
            font-size: 20px;
            color: #333;
        }
        .dashboard-card p {
            margin: 5px 0;
            font-size: 18px;
        }
        .admin-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin: 30px 0;
        }
        .admin-links a {
            background-color: #007BFF;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-size: 15px;
            min-width: 160px;
            text-align: center;
        }
        .admin-links a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1><?php echo $translations[$lang]['admin_dashboard']; ?></h1>
        <div class="admin-info">
            <span><?php echo $translations[$lang]['welcome']; ?>, <?php echo htmlspecialchars($admin_name); ?>!</span>
            <div class="admin-actions">
                <div class="language-switcher">
                    <?php if ($lang === 'en'): ?>
                        <a href="?lang=ar">العربية</a>
                    <?php else: ?>
                        <a href="?lang=en">English</a>
                    <?php endif; ?>
                </div>
                <a href="admin_logout.php"><?php echo $translations[$lang]['logout']; ?></a>
            </div>
        </div>
    </header>

    <main>
        <div class="dashboard-container">
            <div class="dashboard-card">
                <h2><?php echo $translations[$lang]['total_students']; ?></h2>
                <p><?php echo $total_students; ?></p>
            </div>
            <div class="dashboard-card">
                <h2><?php echo $translations[$lang]['total_courses']; ?></h2>
                <p><?php echo $total_courses; ?></p>
                <p style="color: green;"><?php echo $translations[$lang]['active']; ?>: <?php echo $active_courses; ?></p>
                <p style="color: orange;"><?php echo $translations[$lang]['pending']; ?>: <?php echo $pending_courses; ?></p>
            </div>
        </div>

        <div class="admin-links">
            <a href="admin_categories.php"><?php echo $translations[$lang]['manage_categories']; ?></a>
            <a href="admin_courses.php"><?php echo $translations[$lang]['manage_courses']; ?></a>
            <a href="admin_students.php"><?php echo $translations[$lang]['manage_students']; ?></a>
            <a href="admin_enrollment.php"><?php echo $translations[$lang]['enroll_students']; ?></a>
            <a href="admin_lectures.php"><?php echo $translations[$lang]['manage_lectures']; ?></a>
            <a href="admin_labs.php"><?php echo $translations[$lang]['manage_labs']; ?></a>
            <a href="admin_requests.php"><?php echo $translations[$lang]['course_requests']; ?></a>
            <a href="admin_statistics.php"><?php echo $translations[$lang]['statistics']; ?></a>
        </div>
    </main>
</body>
</html>
