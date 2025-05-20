<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$room_code = $_GET['room'] ?? null;

if (!$room_code) {
    http_response_code(400);
    echo json_encode(['error' => 'Room code is required']);
    exit();
}

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get room status and participant count
    $stmt = $db->prepare("
        SELECT r.status,
               COUNT(p.id) as participant_count,
               SUM(CASE WHEN p.completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed_count
        FROM game_rooms r
        LEFT JOIN game_participants p ON r.id = p.room_id
        WHERE r.room_code = ?
        GROUP BY r.id, r.status
    ");
    $stmt->execute([$room_code]);
    $room_status = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$room_status) {
        http_response_code(404);
        echo json_encode(['error' => 'Room not found']);
        exit();
    }
    
    echo json_encode([
        'status' => $room_status['status'],
        'participant_count' => (int)$room_status['participant_count'],
        'completed_count' => (int)$room_status['completed_count']
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 