<?php
session_start();
include('../controller/db_connection.php'); // Include database connection

// Check if user is logged in
$loggedIn = isset($_SESSION['user_id']);
$user_id = $loggedIn ? $_SESSION['user_id'] : null;
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";

// Fetch category ID for "Wireless" from the database
$category_id = null;
$category_query = $conn->prepare("SELECT id FROM categories WHERE name = 'Wireless'");
$category_query->execute();
$category_result = $category_query->get_result();
if ($category_row = $category_result->fetch_assoc()) {
    $category_id = $category_row['id'];
}

// Fetch courses for this category with request status
$courses = [];
if ($category_id !== null) {
    $query = $conn->prepare("
        SELECT c.*, 
            (SELECT status FROM requests 
             WHERE user_id = ? AND course_id = c.id 
             ORDER BY request_date DESC LIMIT 1) as request_status,
            (SELECT COUNT(*) FROM enrollments 
             WHERE student_id = ? AND course_id = c.id) as is_enrolled,
            (SELECT id FROM lectures WHERE course_id = c.id LIMIT 1) as lecture_id
        FROM courses c 
        WHERE c.category_id = ? AND title NOT LIKE 'attack_mitm_%'");
    $query->bind_param("iii", $user_id, $user_id, $category_id);
    $query->execute();
    $result = $query->get_result();
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

// Handle course request submission
if (isset($_POST['request_course']) && $loggedIn) {
    $course_id = $_POST['course_id'];
    
    // Insert new request
    $insert_query = $conn->prepare("INSERT INTO requests (user_id, course_id, status) VALUES (?, ?, 'pending')");
    $insert_query->bind_param("ii", $user_id, $course_id);
    $insert_query->execute();
    header("Location: " . $_SERVER['PHP_SELF'] . "?request_sent=true");
    exit();
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
    <style>
        .request-btn, .request-again-btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s;
        }
        .request-btn {
            background-color: #007BFF;
            color: white;
        }
        .request-again-btn {
            background-color: #6c757d;
            color: white;
        }
        .request-btn:hover, .request-again-btn:hover {
            opacity: 0.9;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
            margin-left: 10px;
        }
        .status-pending { background-color: #ffd700; color: #000; }
        .status-approved { background-color: #90EE90; color: #000; }
        .status-rejected { background-color: #ffcccb; color: #000; }
        .alert {
            padding: 15px;
            margin: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>

    <header>
        <h1>Wireless Security Simulation</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
        <button id="theme-toggle" aria-label="Toggle theme">
                 🌓
            </button>
    </header>

    <main class="content">
        <h2 class="section-title">Wireless Attacks</h2>

        <?php if (isset($_GET['request_sent'])): ?>
            <div class="alert success">Course request has been sent successfully!</div>
        <?php endif; ?>

        <div class="simulations">
            <?php if (empty($courses)): ?>
                <p>No courses available at the moment.</p>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                    <div class="simulation-card">
                        <div class="simulation-header">
                            <h2><?php echo htmlspecialchars($course['title']); ?></h2>
                            <p class="description"><?php echo htmlspecialchars($course['description']); ?></p>
                            
                            <?php if ($course['is_enrolled'] > 0): ?>
                                <a href="attack_<?php echo strtolower(str_replace(' ', '_', $course['title'])); ?>.php" class="request-btn">Access Course</a>
                            <?php elseif ($loggedIn): ?>
                                <?php if (empty($course['request_status'])): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                        <button type="submit" name="request_course" class="request-btn">Request Access</button>
                                    </form>
                                <?php else: ?>
                                    <span class="status-badge status-<?php echo $course['request_status']; ?>">
                                        Status: <?php echo ucfirst($course['request_status']); ?>
                                    </span>
                                    <?php if ($course['request_status'] === 'rejected'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                            <button type="submit" name="request_course" class="request-again-btn">Request Again</button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="login.php" class="request-btn">Login to Request Access</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Back Button -->
        <a href="categ.php" class="back-button">← Back to Categories</a>
    </main>

    <footer>
        <p>© 2024 Cybersecurity Training Platform. All Rights Reserved.</p>
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