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
$time_elapsed = $data['time_elapsed'] ?? null;

if (!$room_code || $time_elapsed === null) {
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
    
    // Update participant's completion time and status
    $stmt = $db->prepare("
        UPDATE game_participants
        SET completion_time = ?, completed_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    $stmt->execute([$time_elapsed, $game_info['participant_id']]);
    
    // Check if all participants have completed the game
    $stmt = $db->prepare("
        SELECT COUNT(*) as total,
               SUM(CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed
        FROM game_participants
        WHERE room_id = ?
    ");
    $stmt->execute([$game_info['room_id']]);
    $completion_status = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If all participants have completed, end the game
    if ($completion_status['total'] === $completion_status['completed']) {
        $stmt = $db->prepare("
            UPDATE game_rooms
            SET status = 'completed', ended_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $stmt->execute([$game_info['room_id']]);
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