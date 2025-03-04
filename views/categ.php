<?php
session_start();
include('../controller/db_connection.php'); // Include your database connection

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";

// Fetch categories dynamically from the database
$categories = [];
$query = $conn->prepare("SELECT id, name FROM categories");
$query->execute();
$result = $query->get_result();
while ($row = $result->fetch_assoc()) {
    $categories[$row['id']] = $row['name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../public/CSS/categ.css">

</head>
<body>
    <header class="header">
        <h1>CyberWise</h1>
        <div class="user-info">Welcome, <?php echo $username; ?>!</div>
    </header>
    <div class="container">
        <h2 class="section-title">Categories We Provide</h2>
        <div class="categories">
            <?php if (empty($categories)): ?>
                <p>No categories available at the moment.</p>
            <?php else: ?>
                <?php foreach ($categories as $id => $name): ?>
                    <div class="category-card" id="category-<?php echo $id; ?>">
                        <div class="category-icon">
                            <i class="fas fa-<?php echo strtolower($name) === 'wireless' ? 'wifi' : (strtolower($name) === 'network' ? 'network-wired' : (strtolower($name) === 'web' ? 'globe' : 'user-secret')); ?>"></i>
                        </div>
                        <h2><?php echo htmlspecialchars($name); ?></h2>
                        <p>Explore our <?php echo strtolower(htmlspecialchars($name)); ?> security courses.</p>
                        <a href="<?php echo strtolower(htmlspecialchars($name)); ?>.php" class="btn">Open</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <a href="homepage.php" class="back-button">← Back</a>
    </div>
    <footer class="footer">
        <p>&copy; 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
