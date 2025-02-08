<?php
session_start();
include('db_connection.php'); // Include your database connection

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
    <style>
        body { margin: 0; font-family: 'Roboto', sans-serif; background-color: #f5f6fa; color: #333; }
        .header { background-color: #007BFF; color: white; padding: 30px; text-align: center; font-size: 1.8rem; font-weight: bold; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); position: relative; }
        .user-info { position: absolute; top: 15px; right: 20px; font-size: 1rem; font-weight: bold; color: white; background: rgba(255, 255, 255, 0.2); padding: 8px 15px; border-radius: 8px; }
        .container { max-width: 1000px; margin: 40px auto; padding: 20px; text-align: center; }
        .section-title { font-size: 2rem; font-weight: bold; margin-bottom: 20px; color: #007BFF; text-transform: uppercase; letter-spacing: 1px; border-bottom: 3px solid #007BFF; display: inline-block; padding-bottom: 5px; }
        .categories { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-top: 20px; }
        .category-card { background: white; border-radius: 10px; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); padding: 25px; flex: 1; text-align: center; transition: transform 0.3s ease-in-out, box-shadow 0.3s; cursor: pointer; min-width: 22%; }
        .category-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15); }
        .category-icon { font-size: 40px; color: #007BFF; margin-bottom: 15px; }
        .category-card h2 { font-size: 1.6rem; color: #333; margin-bottom: 10px; }
        .category-card p { font-size: 1rem; color: #555; margin-bottom: 15px; }
        .category-card .btn { padding: 10px 20px; background-color: #007BFF; color: white; border: none; border-radius: 5px; text-decoration: none; font-size: 1rem; font-weight: bold; transition: background-color 0.3s ease; display: inline-block; }
        .category-card .btn:hover { background-color: #0056b3; }
        .back-button { display: inline-block; margin: 20px 0; padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s; }
        .back-button:hover { background-color: #0056b3; }
        .footer { text-align: center; padding: 20px; background-color: #007BFF; color: white; margin-top: 40px; font-size: 1rem; }
    </style>
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
