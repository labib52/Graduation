<?php
session_start();
include('../controller/db_connection.php'); // Include database connection

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";

// Fetch category ID for "Wireless" from the database
$category_id = null;
$category_query = $conn->prepare("SELECT id FROM categories WHERE name = 'Wireless'");
$category_query->execute();
$category_result = $category_query->get_result();
if ($category_row = $category_result->fetch_assoc()) {
    $category_id = $category_row['id'];
}

// Fetch courses dynamically for the "Wireless" category that start with "attack_mitm_"
$courses = [];
if ($category_id !== null) {
    $query = $conn->prepare("SELECT id, title, description FROM courses WHERE category_id = ? AND title LIKE 'attack_mitm_%'");
    $query->bind_param("i", $category_id);
    $query->execute();
    $result = $query->get_result();
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wireless Security Simulation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../public/CSS/wireless_1.css">
</head>
<body>

    <header>
        <h1>Wireless Security Simulation</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>  <button id="theme-toggle" aria-label="Toggle theme">
                 🌓
            </button>

    </header>

    <main class="content">
        <h2 class="section-title">Wireless Attacks</h2>

        <div class="simulations">
            <?php if (empty($courses)): ?>
                <p>No courses available at the moment.</p>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                    <a href="/Graduation/views/<?php echo strtolower(str_replace(' ', '_', $course['title'])); ?>.php">
                        <div class="simulation-card">
                            <div class="simulation-header">
                                <h2><?php echo htmlspecialchars($course['title']); ?></h2>
                                <p class="description"><?php echo htmlspecialchars($course['description']); ?></p>
                            </div>
                            <span class="status active">Active</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Back Button -->
        <a href="categ.php" class="back-button">← Back to Categories</a>
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
