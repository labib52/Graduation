<?php
session_start();
include('../controller/db_connection.php'); // Include database connection

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";

// Fetch courses dynamically from the database for the Forensics category
$category_name = "Forensics"; // The category this page belongs to
$courses = [];

$query = $conn->prepare("
    SELECT courses.id, courses.title, courses.description, categories.name AS category_name
    FROM courses
    JOIN categories ON courses.category_id = categories.id
    WHERE categories.name = ?");
$query->bind_param("s", $category_name);
$query->execute();
$result = $query->get_result();

while ($row = $result->fetch_assoc()) {
    // Fetch the first lecture for this course
    $lectureQuery = $conn->prepare("SELECT id FROM lectures WHERE course_id = ? ORDER BY id ASC LIMIT 1");
    $lectureQuery->bind_param("i", $row['id']);
    $lectureQuery->execute();
    $lectureResult = $lectureQuery->get_result();
    $lectureId = null;

    if ($lectureRow = $lectureResult->fetch_assoc()) {
        $lectureId = $lectureRow['id'];
    }

    // Append lecture ID to the course data
    $row['lecture_id'] = $lectureId;
    $courses[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forensics Science</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/CSS/forensic_1.css">
</head>

<body>
    <header>
        <h1>Forensics Science</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
        <button id="theme-toggle" aria-label="Toggle theme">
                 🌓
            </button>
    </header>
    <main class="content">
        <h2>Forensics Labs</h2>

        <?php if (!empty($courses)): ?>
            <?php foreach ($courses as $course): ?>
                <?php if ($course['lecture_id']): ?>
                    <a href="lecture.php?id=<?php echo $course['lecture_id']; ?>">
                <?php else: ?>
                    <a href="#" onclick="alert('No lecture found for this course.'); return false;">
                <?php endif; ?>
                    <div class="simulation-card">
                        <div class="simulation-header">
                            <h2><?php echo htmlspecialchars($course['title']); ?></h2>
                            <span class="status active">Active</span>
                        </div>
                        <p class="description"><?php echo htmlspecialchars($course['description']); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No courses available at the moment.</p>
        <?php endif; ?>

        <!-- Back Button -->
        <a href="categ.php" class="back-button">← Back</a>
    </main>
    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
<script>
    const themeToggle = document.getElementById('theme-toggle');
    const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

    // Set initial theme
    document.documentElement.setAttribute('data-theme', savedTheme);

    themeToggle.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);

        // Update button icon (optional)
        themeToggle.textContent = newTheme === 'dark' ? '🌞' : '🌒';
    });

    // Optional: Update button icon on load
    themeToggle.textContent = savedTheme === 'dark' ? '🌞' : '🌒'
</script>
</html>