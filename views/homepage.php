<?php
session_start();
include('../controller/db_connection.php'); // Include database connection

// Language handling
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Translations
$translations = [
    'en' => [
        'welcome' => 'Welcome',
        'gateway' => 'Your gateway to mastering cybersecurity through real-world simulations and expert knowledge.',
        'start_learning' => 'Start Learning',
        'join_thousands' => 'Join thousands of learners in exploring cybersecurity threats, simulated attacks, and defense strategies. Protect yourself and your organization.',
        'choose_lesson' => 'Choose Your Lesson',
        'enrolled_courses' => 'Your Enrolled Courses',
        'no_lectures' => 'No lectures available',
        'realistic_simulations' => 'Realistic Attack Simulations',
        'simulations_desc' => 'Experience hands-on attack scenarios that prepare you for real-world cyber threats.',
        'expert_guidance' => 'Expert Guidance',
        'guidance_desc' => 'Learn from cybersecurity professionals with deep industry knowledge.',
        'interactive_courses' => 'Interactive Courses',
        'courses_desc' => 'Engage with practical exercises that solidify your knowledge of cyber defense.',
        'view_profile' => 'View Profile',
        'logout' => 'Logout',
        'login' => 'Login',
        'signup' => 'Sign Up',
        'admin_panel' => 'Admin Panel',
        'rights_reserved' => 'All Rights Reserved'
    ],
    'ar' => [
        'welcome' => 'مرحباً',
        'gateway' => 'بوابتك لإتقان الأمن السيبراني من خلال المحاكاة الواقعية والمعرفة الخبيرة.',
        'start_learning' => 'ابدأ التعلم',
        'join_thousands' => 'انضم إلى آلاف المتعلمين في استكشاف التهديدات السيبرانية وهجمات المحاكاة واستراتيجيات الدفاع. احمِ نفسك ومنظمتك.',
        'choose_lesson' => 'اختر درسك',
        'enrolled_courses' => 'دوراتك المسجلة',
        'no_lectures' => 'لا توجد محاضرات متاحة',
        'realistic_simulations' => 'محاكاة هجمات واقعية',
        'simulations_desc' => 'اختبر سيناريوهات الهجوم العملية التي تعدك للتهديدات السيبرانية في العالم الحقيقي.',
        'expert_guidance' => 'إرشاد الخبراء',
        'guidance_desc' => 'تعلم من محترفي الأمن السيبراني ذوي المعرفة العميقة في المجال.',
        'interactive_courses' => 'دورات تفاعلية',
        'courses_desc' => 'انخرط في التمارين العملية التي تعزز معرفتك بالدفاع السيبراني.',
        'view_profile' => 'عرض الملف الشخصي',
        'logout' => 'تسجيل الخروج',
        'login' => 'تسجيل الدخول',
        'signup' => 'إنشاء حساب',
        'admin_panel' => 'لوحة التحكم',
        'rights_reserved' => 'جميع الحقوق محفوظة'
    ]
];

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['admin_id']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";

// Fetch enrolled courses for the logged-in user
$enrolled_courses = [];
if ($loggedIn) {
    $user_id = $_SESSION['user_id'];
    $query = $conn->prepare("
        SELECT courses.id, courses.title, categories.name AS category_name, 
               (SELECT id FROM lectures WHERE course_id = courses.id ORDER BY id ASC LIMIT 1) AS first_lecture_id
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
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cybersecurity Training Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/CSS/homepage.css">
    <?php if ($lang === 'ar'): ?>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }
    </style>
    <?php endif; ?>
    <style>
        .auth-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .auth-links a {
            padding: 8px 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-size: 14px;
        }
        .auth-links a:hover {
            background-color: #0056b3;
        }
        .language-switcher a {
            background-color: #007BFF;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-size: 14px;
        }
        .language-switcher a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="auth-links">
            <?php if ($loggedIn): ?>
                <a href="view_profile.php"><?php echo $translations[$lang]['view_profile']; ?></a>
                <a href="logout.php"><?php echo $translations[$lang]['logout']; ?></a>
            <?php else: ?>
                <a href="login.php"><?php echo $translations[$lang]['login']; ?></a>
                <a href="signup.php"><?php echo $translations[$lang]['signup']; ?></a>
            <?php endif; ?>
            <?php if ($is_admin): ?>
                <a href="admin_dashboard.php" style="background: #ff9800;"><?php echo $translations[$lang]['admin_panel']; ?></a>
            <?php endif; ?>
            <div class="language-switcher">
                <?php if ($lang === 'en'): ?>
                    <a href="?lang=ar">العربية</a>
                <?php else: ?>
                    <a href="?lang=en">English</a>
                <?php endif; ?>
            </div>
        </div>
        <h1><?php echo $translations[$lang]['welcome']; ?>, <?php echo $username; ?>!</h1>
        <p><?php echo $translations[$lang]['gateway']; ?></p>
    </header>

    <section class="main-content">
        <h2><?php echo $translations[$lang]['start_learning']; ?></h2>
        <p><?php echo $translations[$lang]['join_thousands']; ?></p>
        <a href="<?php echo $loggedIn ? 'categ.php' : 'login.php'; ?>" class="cta-btn"><?php echo $translations[$lang]['choose_lesson']; ?></a>
    </section>

    <?php if ($loggedIn && !empty($enrolled_courses)): ?>
        <section class="enrolled-courses">
            <h2><?php echo $translations[$lang]['enrolled_courses']; ?></h2>
            <ul class="course-list">
                <?php foreach ($enrolled_courses as $course): ?>
                    <li>
                        <?php if (!empty($course['first_lecture_id'])): ?>
                            <a href="lecture.php?id=<?php echo htmlspecialchars($course['first_lecture_id']); ?>">
                                <?php echo htmlspecialchars($course['title']); ?>
                            </a>
                        <?php else: ?>
                            <span><?php echo htmlspecialchars($course['title']); ?> (<?php echo $translations[$lang]['no_lectures']; ?>)</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <section class="features">
        <div class="feature-box">
            <h3><?php echo $translations[$lang]['realistic_simulations']; ?></h3>
            <p><?php echo $translations[$lang]['simulations_desc']; ?></p>
        </div>
        <div class="feature-box">
            <h3><?php echo $translations[$lang]['expert_guidance']; ?></h3>
            <p><?php echo $translations[$lang]['guidance_desc']; ?></p>
        </div>
        <div class="feature-box">
            <h3><?php echo $translations[$lang]['interactive_courses']; ?></h3>
            <p><?php echo $translations[$lang]['courses_desc']; ?></p>
        </div>
    </section>

    <footer class="footer">
        &copy; <?php echo date("Y"); ?> Cybersecurity Training Platform | <?php echo $translations[$lang]['rights_reserved']; ?>
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
