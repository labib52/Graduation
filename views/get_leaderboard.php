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
    
    // Get room information
    $stmt = $db->prepare("
        SELECT id
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
    
    // Get participants with their scores and completion times
    $stmt = $db->prepare("
        SELECT p.*, u.username
        FROM game_participants p
        JOIN users u ON p.user_id = u.id
        WHERE p.room_id = ?
        ORDER BY p.score DESC, p.completion_time ASC
    ");
    $stmt->execute([$room['id']]);
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($participants);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 