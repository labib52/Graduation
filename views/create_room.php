<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'] ?? null;

    if (!$category_id) {
        echo json_encode(['error' => 'Category is required']);
        exit();
    }

    $room_code = strtoupper(substr(md5(uniqid()), 0, 6));

    try {
        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create the game room
        $stmt = $db->prepare("INSERT INTO game_rooms (room_code, category_id, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$room_code, $category_id, $_SESSION['user_id']]);
        $room_id = $db->lastInsertId();

        // Add the creator as a participant
        $stmt = $db->prepare("INSERT INTO game_participants (room_id, user_id) VALUES (?, ?)");
        $stmt->execute([$room_id, $_SESSION['user_id']]);

        // Get all active courses for the selected category
        $stmt = $db->prepare("SELECT id FROM courses WHERE category_id = ? AND status = 'active'");
        $stmt->execute([$category_id]);
        $courses = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $course_count = count($courses);
        if ($course_count === 0) {
            echo json_encode(['error' => 'No active courses found in this category.']);
            exit();
        }

        $total_questions_needed = 20;
        $questions_collected = [];

        shuffle($courses); // Randomize course order

        // 1st pass: try to collect questions equally
        foreach ($courses as $course_id) {
            $limit = ceil($total_questions_needed / $course_count);
            $limit = (int)$limit;

            $sql = "
                SELECT q.id 
                FROM questions q
                JOIN labs l ON q.lab_id = l.id
                WHERE l.course_id = ?
                ORDER BY RAND()
                LIMIT $limit
            ";
            $stmt = $db->prepare($sql);
            $stmt->execute([$course_id]);
            $questions = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $questions_collected = array_merge($questions_collected, $questions);
        }

        // If still not enough, get additional from the category pool
        if (count($questions_collected) < $total_questions_needed && count($courses) > 0) {
            $needed = $total_questions_needed - count($questions_collected);
        
            $additional_questions = [];
        
            if (count($questions_collected) > 0) {
                // Build with NOT IN
                $placeholders = rtrim(str_repeat('?,', count($courses)), ',');
                $exclude_placeholders = rtrim(str_repeat('?,', count($questions_collected)), ',');
                $sql = "
                    SELECT q.id 
                    FROM questions q
                    JOIN labs l ON q.lab_id = l.id
                    WHERE l.course_id IN ($placeholders)
                    AND q.id NOT IN ($exclude_placeholders)
                    ORDER BY RAND()
                    LIMIT $needed
                ";
                $params = array_merge($courses, $questions_collected);
            } else {
                // No need for NOT IN
                $placeholders = rtrim(str_repeat('?,', count($courses)), ',');
                $sql = "
                    SELECT q.id 
                    FROM questions q
                    JOIN labs l ON q.lab_id = l.id
                    WHERE l.course_id IN ($placeholders)
                    ORDER BY RAND()
                    LIMIT $needed
                ";
                $params = $courses;
            }
        
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $additional_questions = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
            $questions_collected = array_merge($questions_collected, $additional_questions);
        }
        

        // Ensure final set is exactly 20 questions
        $questions_collected = array_slice($questions_collected, 0, $total_questions_needed);
        shuffle($questions_collected);

        $question_order = 1;
        foreach ($questions_collected as $question_id) {
            $stmt = $db->prepare("INSERT INTO game_questions (room_id, question_id, `order`) VALUES (?, ?, ?)");
            $stmt->execute([$room_id, $question_id, $question_order++]);
        }

        echo json_encode([
            'success' => true,
            'room_code' => $room_code,
            'room_id' => $room_id
        ]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    try {
        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $db->query("SELECT id, name FROM categories ORDER BY name");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <head>
            <meta charset="UTF-8">
            <title>Create Game Room</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css?family=Poppins:400,600&display=swap" rel="stylesheet">
            <style>
                body {
                    font-family: 'Poppins', Arial, sans-serif;
                    background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
                    min-height: 100vh;
                }
                .center-card {
                    max-width: 480px;
                    margin: 60px auto;
                    background: #fff;
                    border-radius: 18px;
                    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
                    padding: 2.5rem 2.5rem 2rem 2.5rem;
                }
                .form-label {
                    font-weight: 600;
                    color: #6366f1;
                }
                .btn-primary, .btn-success {
                    font-weight: 600;
                    border-radius: 8px;
                    font-size: 1.1rem;
                    padding: 0.5rem 2rem;
                }
                .btn-primary {
                    background: #6366f1;
                    border: none;
                }
                .btn-primary:hover {
                    background: #4f46e5;
                }
                .btn-success {
                    background: #22c55e;
                    border: none;
                }
                .btn-success:hover {
                    background: #16a34a;
                }
                .room-info-box {
                    background: #f1f5f9;
                    border-radius: 12px;
                    padding: 1.5rem;
                    margin-top: 1.5rem;
                    text-align: center;
                }
                .room-code {
                    font-size: 2rem;
                    font-weight: 700;
                    color: #6366f1;
                    letter-spacing: 2px;
                }
            </style>
        </head>
        <body>
        <div class="center-card">
            <h2 class="mb-4 text-center" style="color:#6366f1;font-weight:700;">Create Game Room</h2>
            <form id="createRoomForm" class="mt-4">
                <div class="mb-3">
                    <label for="category" class="form-label">Select Category:</label>
                    <select class="form-select" id="category" name="category_id" required>
                        <option value="">Choose a category...</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3">Create Room</button>
            </form>
            <div id="roomInfo" class="room-info-box" style="display: none;">
                <h4 class="mb-3">Room Created!</h4>
                <div class="room-code mb-2" id="roomCode"></div>
                <p class="mb-2">Share this code with other players to join the game.</p>
                <button id="startGameBtn" class="btn btn-success w-100">Start Game</button>
            </div>
        </div>
        <script>
        document.getElementById('createRoomForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            try {
                const response = await fetch('create_room.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    document.getElementById('roomCode').textContent = data.room_code;
                    document.getElementById('roomInfo').style.display = 'block';
                    document.getElementById('createRoomForm').style.display = 'none';
                } else {
                    alert(data.error || 'Failed to create room');
                }
            } catch (error) {
                alert('Error creating room: ' + error.message);
            }
        });

        document.getElementById('startGameBtn').addEventListener('click', () => {
            const roomCode = document.getElementById('roomCode').textContent;
            window.location.href = `game.php?room=${roomCode}`;
        });
        </script>
        </body>
        <?php
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}
?>
