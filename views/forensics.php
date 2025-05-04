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
    <link rel="stylesheet" href="../public/CSS/forensic.css">

</head>

<body>
    <header>
        <h1>Forensics Science</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>
    <main class="content">
        <h2>Forensics Labs</h2>

        <?php if (!empty($courses)): ?>
            <?php foreach ($courses as $course): ?>
                <a href="lecture.php?id=<?php echo $course['id']; ?>">
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

</html>
