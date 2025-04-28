<?php
session_start();
include('../controller/db_connection.php'); // Include your database connection

// Language handling
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Translations
$translations = [
    'en' => [
        'welcome' => 'Welcome',
        'categories_title' => 'Categories We Provide',
        'no_categories' => 'No categories available at the moment.',
        'explore' => 'Explore our',
        'security_courses' => 'security courses.',
        'open' => 'Open',
        'back' => '← Back',
        'rights_reserved' => 'All Rights Reserved.',
        'platform' => 'Cybersecurity Awareness Platform',
    ],
    'ar' => [
        'welcome' => 'مرحباً',
        'categories_title' => 'الفئات التي نقدمها',
        'no_categories' => 'لا توجد فئات متاحة حالياً.',
        'explore' => 'استكشف دورات',
        'security_courses' => 'الأمنية لدينا.',
        'open' => 'افتح',
        'back' => '← رجوع',
        'rights_reserved' => 'جميع الحقوق محفوظة.',
        'platform' => 'منصة التوعية بالأمن السيبراني',
    ]
];

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
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../public/CSS/categ.css">
    <?php if ($lang === 'ar'): ?>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; }
    </style>
    <?php endif; ?>
</head>
<body>
    <header class="header">
        <h1>CyberWise</h1>
        <div class="user-info"><?php echo $translations[$lang]['welcome']; ?>, <?php echo $username; ?>!</div>
    </header>
    <div class="container">
        <h2 class="section-title"><?php echo $translations[$lang]['categories_title']; ?></h2>
        <div class="categories">
            <?php if (empty($categories)): ?>
                <p><?php echo $translations[$lang]['no_categories']; ?></p>
            <?php else: ?>
                <?php foreach ($categories as $id => $name): ?>
                    <div class="category-card" id="category-<?php echo $id; ?>">
                        <div class="category-icon">
                            <i class="fas fa-<?php echo strtolower($name) === 'wireless' ? 'wifi' : (strtolower($name) === 'network' ? 'network-wired' : (strtolower($name) === 'web' ? 'globe' : 'user-secret')); ?>"></i>
                        </div>
                        <h2><?php echo htmlspecialchars($name); ?></h2>
                        <p><?php echo $translations[$lang]['explore'] . ' ' . strtolower(htmlspecialchars($name)) . ' ' . $translations[$lang]['security_courses']; ?></p>
                        <a href="<?php echo strtolower(htmlspecialchars($name)); ?>.php" class="btn"><?php echo $translations[$lang]['open']; ?></a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <a href="homepage.php" class="back-button"><?php echo $translations[$lang]['back']; ?></a>
    </div>
    <footer class="footer">
        <p>&copy; 2024 <?php echo $translations[$lang]['platform']; ?>. <?php echo $translations[$lang]['rights_reserved']; ?></p>
    </footer>
</body>
</html>
