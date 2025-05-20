<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_code = $_POST['room_code'] ?? null;
    
    if (!$room_code) {
        echo json_encode(['error' => 'Room code is required']);
        exit();
    }
    
    try {
        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if room exists and is in waiting status
        $stmt = $db->prepare("
            SELECT id, status 
            FROM game_rooms 
            WHERE room_code = ?
        ");
        $stmt->execute([$room_code]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$room) {
            echo json_encode(['error' => 'Room not found']);
            exit();
        }
        
        if ($room['status'] !== 'waiting') {
            echo json_encode(['error' => 'Game has already started or ended']);
            exit();
        }
        
        // Check if user is already in the room
        $stmt = $db->prepare("
            SELECT id 
            FROM game_participants 
            WHERE room_id = ? AND user_id = ?
        ");
        $stmt->execute([$room['id'], $_SESSION['user_id']]);
        
        if ($stmt->fetch()) {
            echo json_encode(['error' => 'You are already in this room']);
            exit();
        }
        
        // Add user to the room
        $stmt = $db->prepare("
            INSERT INTO game_participants (room_id, user_id) 
            VALUES (?, ?)
        ");
        $stmt->execute([$room['id'], $_SESSION['user_id']]);
        
        echo json_encode([
            'success' => true,
            'room_id' => $room['id']
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    ?>
    <head>
        <meta charset="UTF-8">
        <title>Join Game Room</title>
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
            .btn-primary {
                font-weight: 600;
                border-radius: 8px;
                font-size: 1.1rem;
                padding: 0.5rem 2rem;
                background: #6366f1;
                border: none;
            }
            .btn-primary:hover {
                background: #4f46e5;
            }
        </style>
    </head>
    <body>
    <div class="center-card">
        <h2 class="mb-4 text-center" style="color:#6366f1;font-weight:700;">Join Game Room</h2>
        <form id="joinRoomForm" class="mt-4">
            <div class="mb-3">
                <label for="roomCode" class="form-label">Enter Room Code:</label>
                <input type="text" class="form-control" id="roomCode" name="room_code" 
                       required maxlength="6" pattern="[A-Z0-9]{6}" 
                       placeholder="Enter 6-character room code">
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">Join Room</button>
        </form>
    </div>
    <script>
    document.getElementById('joinRoomForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        try {
            const response = await fetch('join_room.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                window.location.href = `game.php?room=${formData.get('room_code')}`;
            } else {
                alert(data.error || 'Failed to join room');
            }
        } catch (error) {
            alert('Error joining room: ' + error.message);
        }
    });
    // Convert input to uppercase
    document.getElementById('roomCode').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });
    </script>
    </body>
    <?php
}
?> 