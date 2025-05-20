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

if (!$room_code) {
    http_response_code(400);
    echo json_encode(['error' => 'Room code is required']);
    exit();
}

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if user is the room creator
    $stmt = $db->prepare("
        SELECT id, created_by, status
        FROM game_rooms
        WHERE room_code = ?
    ");
    $stmt->execute([$room_code]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$room) {
        http_response_code(404);
        echo json_encode(['error' => 'Room not found']);
        exit();
    }
    
    if ($room['created_by'] !== $_SESSION['user_id']) {
        http_response_code(403);
        echo json_encode(['error' => 'Only the room creator can start the game']);
        exit();
    }
    
    if ($room['status'] !== 'waiting') {
        http_response_code(400);
        echo json_encode(['error' => 'Game has already started or ended']);
        exit();
    }
    
    // Start the game
    $stmt = $db->prepare("
        UPDATE game_rooms
        SET status = 'active', started_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    $stmt->execute([$room['id']]);
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 