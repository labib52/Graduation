<?php
session_start();
include('../controller/db_connection.php'); // Include database connection

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['admin_id']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";

// Fetch enrolled courses for the logged-in user
$enrolled_courses = [];
if ($loggedIn) {
    $user_id = $_SESSION['user_id'];
    $query = $conn->prepare("
        SELECT courses.id, courses.title, categories.name AS category_name
        FROM enrollments 
        JOIN courses ON enrollments.course_id = courses.id 
        JOIN categories ON courses.category_id = categories.id
        WHERE enrollments.student_id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();
    while ($row = $result->fetch_assoc()) {
        $enrolled_courses[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cybersecurity Training Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/CSS/homepage.css">

</head>
<body>
    <header class="header">
        <div class="auth-links">
            <?php if ($loggedIn): ?>
                <a href="view_profile.php">View Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
            <?php if ($is_admin): ?>
                <a href="admin_dashboard.php" style="background: #ff9800;">Admin Panel</a>
            <?php endif; ?>
        </div>
        <h1>Welcome, <?php echo $username; ?>!</h1>
        <p>Your gateway to mastering cybersecurity through real-world simulations and expert knowledge.</p>
    </header>

    <section class="main-content">
        <h2>Start Learning</h2>
        <p>Join thousands of learners in exploring cybersecurity threats, simulated attacks, and defense strategies. Protect yourself and your organization.</p>
        <a href="<?php echo $loggedIn ? 'categ.php' : 'login.php'; ?>" class="cta-btn">Choose Your Lesson</a>
    </section>

   <?php if ($loggedIn && !empty($enrolled_courses)): ?>
        <section class="enrolled-courses">
            <h2>Your Enrolled Courses</h2>
            <ul class="course-list">
                <?php foreach ($enrolled_courses as $course): ?>
                    <li>
                        <a href="lecture.php?id=<?php echo htmlspecialchars($course['id']); ?>">
                            <?php echo htmlspecialchars($course['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>
    

    <section class="features">
        <div class="feature-box">
            <h3>Realistic Attack Simulations</h3>
            <p>Experience hands-on attack scenarios that prepare you for real-world cyber threats.</p>
        </div>
        <div class="feature-box">
            <h3>Expert Guidance</h3>
            <p>Learn from cybersecurity professionals with deep industry knowledge.</p>
        </div>
        <div class="feature-box">
            <h3>Interactive Courses</h3>
            <p>Engage with practical exercises that solidify your knowledge of cyber defense.</p>
        </div>
    </section>

    <footer class="footer">
        &copy; <?php echo date("Y"); ?> Cybersecurity Training Platform | All Rights Reserved.
       <!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/67e95bf057b42a191471b8d5/1injou94l';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
    </footer>
</body>
</html>
