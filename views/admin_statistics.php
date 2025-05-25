<?php
session_start();
include('../controller/db_connection.php');

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Language handling
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Translations
$translations = [
    'en' => [
        'statistics' => 'Website Statistics',
        'top_users' => 'Top Users',
        'top_courses' => 'Top Courses',
        'lab_failures' => 'Lab Failures',
        'user_name' => 'User Name',
        'course_name' => 'Course Name',
        'enrollments' => 'Enrollments',
        'completion_rate' => 'Completion Rate',
        'failure_rate' => 'Failure Rate',
        'back_to_dashboard' => 'Back to Dashboard',
        'user_levels' => 'User Course Levels',
        'enrolled_courses' => 'Enrolled Courses',
        'course_levels' => 'Course Levels',
        'user_level' => 'User Level'
    ],
    'ar' => [
        'statistics' => 'إحصائيات الموقع',
        'top_users' => 'أفضل المستخدمين',
        'top_courses' => 'أفضل الدورات',
        'lab_failures' => 'فشل المعامل',
        'user_name' => 'اسم المستخدم',
        'course_name' => 'اسم الدورة',
        'enrollments' => 'التسجيلات',
        'completion_rate' => 'معدل الإكمال',
        'failure_rate' => 'معدل الفشل',
        'back_to_dashboard' => 'العودة للوحة التحكم',
        'user_levels' => 'مستويات دورات المستخدم',
        'enrolled_courses' => 'الدورات المسجلة',
        'course_levels' => 'مستويات الدورات',
        'user_level' => 'مستوى المستخدم'
    ]
];

// Get top users based on multiple performance factors
$top_users_query = "SELECT 
    u.username,
    COUNT(DISTINCT e.course_id) as enrollments,
    (SELECT AVG(score) FROM students_answers WHERE user_id = u.id) as avg_score,
    COUNT(sa.id) as completed_labs,
    (
        COUNT(DISTINCT e.course_id) * 0.3 +                    -- 30% weight for number of enrollments
        (SELECT AVG(score) FROM students_answers WHERE user_id = u.id) * 0.4 +    -- 40% weight for average score
        COUNT(sa.id) * 0.3                                     -- 30% weight for number of completed labs
    ) as user_rank
FROM users u 
JOIN enrollments e ON u.id = e.student_id
LEFT JOIN labs l ON l.course_id = e.course_id
LEFT JOIN students_answers sa ON sa.lab_id = l.id AND sa.user_id = u.id
WHERE u.is_admin = 0 
GROUP BY u.id, u.username
HAVING enrollments > 0
ORDER BY user_rank DESC 
LIMIT 4";
$top_users_result = mysqli_query($conn, $top_users_query);

// Get top courses based on multiple factors
$top_courses_query = "SELECT 
    c.title,
    COUNT(DISTINCT e.student_id) as enrollments,
    COALESCE(AVG(sa.score), 0) as avg_score,
    LEAST(COUNT(DISTINCT sa.user_id) * 100.0 / NULLIF(COUNT(DISTINCT e.student_id), 0), 100) as completion_rate,
    (
        COUNT(DISTINCT e.student_id) * 0.4 + 
        COALESCE(AVG(sa.score), 0) * 0.3 +
        (LEAST(COUNT(DISTINCT sa.user_id) * 100.0 / NULLIF(COUNT(DISTINCT e.student_id), 0), 100)) * 0.3
    ) as course_rank
FROM courses c 
LEFT JOIN enrollments e ON c.id = e.course_id
LEFT JOIN labs l ON c.id = l.course_id
LEFT JOIN students_answers sa ON l.id = sa.lab_id
WHERE c.status = 'active'
GROUP BY c.id
HAVING enrollments > 0
ORDER BY course_rank DESC
LIMIT 4";
$top_courses_result = mysqli_query($conn, $top_courses_query);

// Get lab failure statistics
$lab_failures_query = "SELECT c.title as course_title, 
                              COUNT(CASE WHEN sa.score < 5 THEN 1 END) as failed_labs,
                              COUNT(sa.id) as total_labs,
                              ROUND((COUNT(CASE WHEN sa.score < 5 THEN 1 END) / COUNT(sa.id)) * 100, 2) as failure_rate
                       FROM courses c 
                       JOIN labs l ON c.id = l.course_id
                       JOIN students_answers sa ON l.id = sa.lab_id
                       GROUP BY c.id 
                       ORDER BY failure_rate DESC";
$lab_failures_result = mysqli_query($conn, $lab_failures_query);

// Get course categories distribution
$categories_query = "SELECT 
    cat.name, 
    COUNT(c.id) as course_count,
    COUNT(DISTINCT e.student_id) as student_count
FROM categories cat
LEFT JOIN courses c ON cat.id = c.category_id
LEFT JOIN enrollments e ON c.id = e.course_id
GROUP BY cat.id";
$categories_result = mysqli_query($conn, $categories_query);

// Get lab performance distribution
$performance_query = "SELECT 
    CASE 
        WHEN score >= 9 THEN 'Excellent (9-10)'
        WHEN score >= 7 THEN 'Good (7-8.9)'
        WHEN score >= 5 THEN 'Average (5-6.9)'
        ELSE 'Need Improvement (<5)'
    END as performance_category,
    COUNT(*) as student_count
FROM students_answers
GROUP BY performance_category
ORDER BY MIN(score)";
$performance_result = mysqli_query($conn, $performance_query);

// Get user levels based on enrolled courses
$user_levels_query = "SELECT 
    u.username,
    GROUP_CONCAT(c.title SEPARATOR ', ') as enrolled_courses,
    GROUP_CONCAT(c.level SEPARATOR ', ') as course_levels,
    CASE 
        WHEN COUNT(CASE WHEN c.level = 'advanced' THEN 1 END) = COUNT(*) THEN 'Advanced'
        WHEN COUNT(CASE WHEN c.level = 'beginner' THEN 1 END) = COUNT(*) THEN 'Beginner'
        WHEN COUNT(CASE WHEN c.level = 'advanced' THEN 1 END) = COUNT(CASE WHEN c.level = 'beginner' THEN 1 END) THEN 'Intermediate'
        WHEN COUNT(CASE WHEN c.level = 'advanced' THEN 1 END) >= 2 THEN 'Advanced'
        WHEN COUNT(CASE WHEN c.level = 'advanced' THEN 1 END) = 1 AND COUNT(CASE WHEN c.level = 'beginner' THEN 1 END) >= 1 THEN 'Intermediate'
        WHEN COUNT(CASE WHEN c.level = 'beginner' THEN 1 END) >= 2 THEN 'Intermediate'
        ELSE 'Beginner'
    END as user_level
FROM users u
JOIN enrollments e ON u.id = e.student_id
JOIN courses c ON e.course_id = c.id
WHERE u.is_admin = 0
GROUP BY u.id, u.username
ORDER BY u.username";
$user_levels_result = mysqli_query($conn, $user_levels_query);
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations[$lang]['statistics']; ?></title>
    <link rel="stylesheet" href="../public/CSS/admin_styles_1.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .statistics-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .statistics-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .statistics-section h2 {
            color: #333;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chart-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .chart-col {
            flex: 1;
            min-height: 300px;
        }
    </style>
</head>
<body>
    <div class="statistics-container">
        <a href="admin_dashboard.php" class="back-button"><?php echo $translations[$lang]['back_to_dashboard']; ?></a>

        <div class="statistics-section">
            <h2><?php echo $translations[$lang]['top_users']; ?></h2>
            <table>
                <thead>
                    <tr>
                        <th><?php echo $translations[$lang]['user_name']; ?></th>
                        <th>Enrollments</th>
                        <th>Avg Score</th>
                        <th>Completed Labs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($top_users_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo $user['enrollments']; ?></td>
                            <td><?php echo number_format($user['avg_score'], 2); ?></td>
                            <td><?php echo $user['completed_labs']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="statistics-section">
            <h2><?php echo $translations[$lang]['top_courses']; ?></h2>
            <table>
                <thead>
                    <tr>
                        <th><?php echo $translations[$lang]['course_name']; ?></th>
                        <th>Enrollments</th>
                        <th>Avg Score</th>
                        <th>Completion Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($course = mysqli_fetch_assoc($top_courses_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['title']); ?></td>
                            <td><?php echo $course['enrollments']; ?></td>
                            <td><?php echo number_format($course['avg_score'], 2); ?></td>
                            <td><?php echo number_format($course['completion_rate'], 1); ?>%</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="statistics-section">
            <h2><?php echo $translations[$lang]['lab_failures']; ?></h2>
            <table>
                <thead>
                    <tr>
                        <th><?php echo $translations[$lang]['course_name']; ?></th>
                        <th><?php echo $translations[$lang]['failure_rate']; ?></th>
                        <th>Failed Labs</th>
                        <th>Total Labs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($lab = mysqli_fetch_assoc($lab_failures_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($lab['course_title']); ?></td>
                            <td><?php echo $lab['failure_rate']; ?>%</td>
                            <td><?php echo $lab['failed_labs']; ?></td>
                            <td><?php echo $lab['total_labs']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="statistics-section">
            <h2><?php echo $translations[$lang]['user_levels']; ?></h2>
            <table>
                <thead>
                    <tr>
                        <th><?php echo $translations[$lang]['user_name']; ?></th>
                        <th><?php echo $translations[$lang]['enrolled_courses']; ?></th>
                        <th><?php echo $translations[$lang]['course_levels']; ?></th>
                        <th><?php echo $translations[$lang]['user_level']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($user_levels_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['enrolled_courses']); ?></td>
                            <td><?php echo htmlspecialchars($user['course_levels']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_level']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="chart-row">
            <div class="chart-col">
                <div class="chart-container">
                    <h2>Course Categories Distribution</h2>
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>
            <div class="chart-col">
                <div class="chart-container">
                    <h2>Lab Performance Distribution</h2>
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prepare data for charts
        const categoriesData = {
            labels: [<?php 
                $labels = [];
                $courseCounts = [];
                $studentCounts = [];
                mysqli_data_seek($categories_result, 0);
                while ($row = mysqli_fetch_assoc($categories_result)) {
                    $labels[] = "'" . addslashes($row['name']) . "'";
                    $courseCounts[] = $row['course_count'];
                    $studentCounts[] = $row['student_count'];
                }
                echo implode(',', $labels);
            ?>],
            courseCounts: [<?php echo implode(',', $courseCounts); ?>],
            studentCounts: [<?php echo implode(',', $studentCounts); ?>]
        };

        const performanceData = {
            labels: [<?php 
                $perfLabels = [];
                $perfCounts = [];
                mysqli_data_seek($performance_result, 0);
                while ($row = mysqli_fetch_assoc($performance_result)) {
                    $perfLabels[] = "'" . addslashes($row['performance_category']) . "'";
                    $perfCounts[] = $row['student_count'];
                }
                echo implode(',', $perfLabels);
            ?>],
            counts: [<?php echo implode(',', $perfCounts); ?>]
        };

        // Create Charts
        new Chart(document.getElementById('categoriesChart'), {
            type: 'bar',
            data: {
                labels: categoriesData.labels,
                datasets: [{
                    label: 'Number of Courses',
                    data: categoriesData.courseCounts,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }, {
                    label: 'Number of Students',
                    data: categoriesData.studentCounts,
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        new Chart(document.getElementById('performanceChart'), {
            type: 'bar',
            data: {
                labels: performanceData.labels,
                datasets: [{
                    data: performanceData.counts,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.raw;
                                const percentage = Math.round(value / total * 100);
                                return `${value} submissions (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Submissions'
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        left: 15,
                        right: 15,
                        top: 15,
                        bottom: 15
                    }
                }
            }
        });
    </script>
</body>
</html> 
