<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$room_code = $data['room_code'] ?? null;
$question_id = $data['question_id'] ?? null;
$choice_id = $data['choice_id'] ?? null;

if (!$room_code || !$question_id || !$choice_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit();
}

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Start transaction
    $db->beginTransaction();
    
    // Get room and participant information
    $stmt = $db->prepare("
        SELECT r.id as room_id, r.status, p.id as participant_id, p.completed_at
        FROM game_rooms r
        JOIN game_participants p ON r.id = p.room_id
        WHERE r.room_code = ? AND p.user_id = ?
    ");
    $stmt->execute([$room_code, $_SESSION['user_id']]);
    $game_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$game_info) {
        $db->rollBack();
        http_response_code(404);
        echo json_encode(['error' => 'Game room or participant not found']);
        exit();
    }
    
    if ($game_info['status'] !== 'active') {
        $db->rollBack();
        http_response_code(400);
        echo json_encode(['error' => 'Game is not active']);
        exit();
    }
    
    if ($game_info['completed_at']) {
        $db->rollBack();
        http_response_code(400);
        echo json_encode(['error' => 'You have already completed the game']);
        exit();
    }
    
    // Check if the question belongs to this game
    $stmt = $db->prepare("
        SELECT 1
        FROM game_questions
        WHERE room_id = ? AND question_id = ?
    ");
    $stmt->execute([$game_info['room_id'], $question_id]);
    if (!$stmt->fetch()) {
        $db->rollBack();
        http_response_code(400);
        echo json_encode(['error' => 'Invalid question for this game']);
        exit();
    }
    
    // Check if the choice is correct
    $stmt = $db->prepare("
        SELECT is_correct
        FROM choices
        WHERE id = ? AND question_id = ?
    ");
    $stmt->execute([$choice_id, $question_id]);
    $choice = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$choice) {
        $db->rollBack();
        http_response_code(400);
        echo json_encode(['error' => 'Invalid choice for this question']);
        exit();
    }
    
    // Record the answer
    $stmt = $db->prepare("
        INSERT INTO game_answers (participant_id, question_id, choice_id, is_correct)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $game_info['participant_id'],
        $question_id,
        $choice_id,
        $choice['is_correct']
    ]);
    
    // Update participant's score if the answer is correct
    if ($choice['is_correct']) {
        $stmt = $db->prepare("
            UPDATE game_participants
            SET score = score + 1
            WHERE id = ?
        ");
        $stmt->execute([$game_info['participant_id']]);
    }
    
    $db->commit();
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    if (isset($db)) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 