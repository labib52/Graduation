<?php
session_start();
include('../controller/db_connection.php');

$lecture_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($lecture_id <= 0) {
    die("Invalid Lecture ID");
}

// Fetch lecture details
$lecture_query = $conn->prepare("SELECT * FROM lectures WHERE id = ?");
$lecture_query->bind_param("i", $lecture_id);
$lecture_query->execute();
$lecture_result = $lecture_query->get_result();
$lecture = $lecture_result->fetch_assoc();

if (!$lecture) {
    die("Lecture not found");
}

// Convert JSON navigation items and content
$nav_items = json_decode($lecture['nav_items'], true) ?: [];
$contents = json_decode($lecture['content'], true);
$media = json_decode($lecture['section_media'], true) ?: [];

?>

<!DOCTYPE html>
<html lang="en">
<div id="google_translate_element"></div>

<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
                pageLanguage: 'en'
            },
            'google_translate_element'
        );
    }
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lecture['title']); ?></title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../public/CSS/wirelesslec.css">
    <style>
        :root {
            --primary-blue: #007bff;
            --hover-blue: #0056b3;
        }

        .lecture-container {
            display: flex;
            min-height: 100vh;
        }

        .side-nav {
            width: 300px;
            background: #f8f9fa;
            padding: 20px;
            border-right: 1px solid #dee2e6;
            transition: all 0.3s ease;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .side-nav.closed {
            transform: translateX(-250px);
        }

        .side-nav-header {
            color: var(--primary-blue);
            font-size: 1.5em;
            font-weight: bold;
            text-align: center;
            padding: 15px 0;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--primary-blue);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .nav-toggle {
            position: absolute;
            right: -40px;
            top: 20px;
            background: var(--primary-blue);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 2px 0 4px rgba(0,0,0,0.1);
            font-size: 1.2em;
            z-index: 1000;
        }

        .content-area {
            flex: 1;
            padding: 30px;
            margin-left: 300px;
            transition: all 0.3s ease;
            background: #fff;
            width: calc(100% - 300px);
        }

        .content-area.expanded {
            margin-left: 50px;
            width: calc(100% - 50px);
        }

        .lecture-title {
            background: var(--primary-blue);
            color: white;
            padding: 20px;
            margin: -30px -30px 30px -30px;
            text-align: center;
            font-size: 2em;
            font-weight: bold;
        }

        .nav-items {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-items li {
            margin-bottom: 15px;
        }

        .nav-items a {
            color: #2c3e50;
            text-decoration: none;
            display: block;
            padding: 12px 15px;
            border-radius: 5px;
            transition: all 0.3s;
            text-transform: capitalize;
            font-weight: 500;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin-bottom: 5px;
        }

        .nav-items a:hover {
            background: var(--primary-blue);
            color: white;
            transform: translateX(5px);
        }

        .section-content {
            background: white;
            padding: 30px;
            margin-bottom: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 100%;
        }

        .back-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--primary-blue);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            z-index: 1000;
        }

        .back-button:hover {
            background: var(--hover-blue);
        }

        .section-media {
            display: flex;
            flex-direction: column;
            gap: 30px;
            margin-top: 20px;
        }

        .section-media img,
        .section-media video {
            width: 100%;
            height: auto;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            display: block;
        }

        .section-media img,
        .section-media video {
            border: 1px solid #e0e0e0;
        }

        .lecture-content {
            line-height: 2;
        }

        .lecture-content p {
            margin: 0;
            padding: 0.5em 0;
        }

        /* For any lists within the content */
        .lecture-content ul,
        .lecture-content ol {
            line-height: 2;
            margin: 0.5em 0;
        }

        /* For any other text elements that might be in the content */
        .lecture-content div,
        .lecture-content span {
            line-height: 2;
        }
    </style>
</head>
<body>

    <a href="javascript:history.back()" class="back-button">← Back</a>

    <div class="lecture-container">
        <nav class="side-nav">
            <div class="side-nav-header">YOUR NAVIGATION</div>
            <button class="nav-toggle">←</button>
            <ul class="nav-items">
                <?php foreach($nav_items as $index => $item): ?>
                    <li><a href="#section-<?php echo $index; ?>"><?php echo htmlspecialchars($item); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <main class="content-area">
            <h1 class="lecture-title"><?php echo htmlspecialchars($lecture['title']); ?></h1>
            <?php foreach ($contents as $index => $content): ?>
                <div id="section-<?php echo $index; ?>" class="section-content">
                    <div class="lecture-content">
                        <?php echo html_entity_decode(htmlspecialchars_decode($content)); ?>
                    </div>
                    
                    <?php if (!empty($media[$index]) && is_array($media[$index])): ?>
                        <div class="section-media">
                            <?php foreach ($media[$index] as $media_path): ?>
                                <?php
                                $extension = strtolower(pathinfo($media_path, PATHINFO_EXTENSION));
                                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                    <img src="../public/<?php echo htmlspecialchars($media_path); ?>" alt="Section media">
                                <?php elseif (in_array($extension, ['mp4', 'webm'])): ?>
                                    <video controls>
                                        <source src="../public/<?php echo htmlspecialchars($media_path); ?>" type="video/<?php echo $extension; ?>">
                                        Your browser does not support the video tag.
                                    </video>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sideNav = document.querySelector('.side-nav');
            const contentArea = document.querySelector('.content-area');
            const navToggle = document.querySelector('.nav-toggle');

            navToggle.addEventListener('click', function() {
                sideNav.classList.toggle('closed');
                contentArea.classList.toggle('expanded');
                this.textContent = sideNav.classList.contains('closed') ? '→' : '←';
            });

            // Smooth scroll for navigation links
            document.querySelectorAll('.nav-items a').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const section = document.querySelector(this.getAttribute('href'));
                    section.scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>

</body>
</html>
