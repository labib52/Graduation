<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$room_code = $_GET['room'] ?? null;
if (!$room_code) {
    header('Location: join_room.php');
    exit();
}

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get room information
    $stmt = $db->prepare("
        SELECT r.*, c.name as category_name, u.username as creator_name
        FROM game_rooms r
        JOIN categories c ON r.category_id = c.id
        JOIN users u ON r.created_by = u.id
        WHERE r.room_code = ?
    ");
    $stmt->execute([$room_code]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$room) {
        header('Location: join_room.php');
        exit();
    }
    
    // Check if user is a participant
    $stmt = $db->prepare("
        SELECT id, score, completion_time, completed_at
        FROM game_participants
        WHERE room_id = ? AND user_id = ?
    ");
    $stmt->execute([$room['id'], $_SESSION['user_id']]);
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$participant) {
        header('Location: join_room.php');
        exit();
    }
    
    // Get participants
    $stmt = $db->prepare("
        SELECT p.*, u.username
        FROM game_participants p
        JOIN users u ON p.user_id = u.id
        WHERE p.room_id = ?
        ORDER BY p.score DESC, p.completion_time ASC
    ");
    $stmt->execute([$room['id']]);
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get questions if game is active
    $questions = [];
    if ($room['status'] === 'active') {
        $stmt = $db->prepare("
            SELECT gq.*, q.question_text, c.id AS choice_id, c.choice_text, c.is_correct
            FROM game_questions gq
            JOIN questions q ON gq.question_id = q.id
            JOIN choices c ON q.id = c.question_id
            WHERE gq.room_id = ?
            ORDER BY gq.`order`, c.id
        ");
        $stmt->execute([$room['id']]);
        $raw_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organize questions and choices
        $current_question = null;
        foreach ($raw_questions as $row) {
            if ($current_question === null || $current_question['id'] !== $row['question_id']) {
                if ($current_question !== null) {
                    $questions[] = $current_question;
                }
                $current_question = [
                    'id' => $row['question_id'],
                    'order' => $row['order'],
                    'text' => $row['question_text'],
                    'choices' => []
                ];
            }
            $current_question['choices'][] = [
                'id' => $row['choice_id'],
                'text' => $row['choice_text'],
                'is_correct' => $row['is_correct']
            ];
        }
        if ($current_question !== null) {
            $questions[] = $current_question;
        }
    }
    
    // Fetch answered questions for this participant
    $answered_questions = [];
    $stmt = $db->prepare("
        SELECT question_id
        FROM game_answers
        WHERE participant_id = ?
    ");
    $stmt->execute([$participant['id']]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $answered_questions[] = $row['question_id'];
    }
    
    $game_start_time = $room['started_at']; // This is a datetime string
   
    ?>
    <head>
        <meta charset="UTF-8">
        <title>Quiz Game Room</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Poppins:400,600&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Poppins', Arial, sans-serif;
                background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
                min-height: 100vh;
            }
            .game-card {
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 4px 24px rgba(0,0,0,0.08);
                padding: 2rem 2.5rem;
                margin-bottom: 2rem;
            }
            .question-title {
                font-size: 1.3rem;
                font-weight: 600;
                color: #3b3b3b;
            }
            .answer-btn {
                min-width: 220px;
                margin: 0.5rem 0.5rem 0.5rem 0;
                font-size: 1rem;
                font-weight: 500;
                border-radius: 8px;
                transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            }
            .answer-btn:hover, .answer-btn:focus {
                background: #6366f1;
                color: #fff;
                box-shadow: 0 2px 8px rgba(99,102,241,0.15);
            }
            .timer-box {
                background: #6366f1;
                color: #fff;
                padding: 0.5rem 1.5rem;
                border-radius: 20px;
                font-size: 1.1rem;
                font-weight: 600;
                display: inline-block;
                margin-bottom: 1rem;
            }
            .leaderboard-card {
                background: #f1f5f9;
                border-radius: 12px;
                padding: 1.5rem;
            }
            .leaderboard-table th, .leaderboard-table td {
                vertical-align: middle;
            }
            .game-header {
                color: #6366f1;
                font-weight: 700;
                letter-spacing: 2px;
            }
        </style>
    </head>
    <body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="game-card">
                    <h2 class="game-header mb-2">Game Room: <?php echo htmlspecialchars($room_code); ?></h2>
                    <div class="mb-3">
                        <span class="badge bg-primary me-2"><?php echo htmlspecialchars($room['category_name']); ?></span>
                        <span class="text-muted">Created by: <b><?php echo htmlspecialchars($room['creator_name']); ?></b></span>
                    </div>
                    <?php if ($room['status'] === 'waiting'): ?>
                        <div id="waitingRoom">
                            <h4 class="mb-3">Waiting for players...</h4>
                            <div id="participantsList">
                                <h5>Players:</h5>
                                <ul class="list-group mb-3">
                                    <?php foreach ($participants as $p): ?>
                                        <li class="list-group-item"><?php echo htmlspecialchars($p['username']); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php if ($room['created_by'] === $_SESSION['user_id']): ?>
                                <button id="startGameBtn" class="btn btn-success">Start Game</button>
                            <?php endif; ?>
                        </div>
                    <?php elseif ($room['status'] === 'active'): ?>
                        <?php if ($participant['completed_at']): ?>
                            <div id="completedGame">
                                <h4 class="mb-3">You've completed the game!</h4>
                                <div class="alert alert-info">
                                    <b>Your score:</b> <?php echo $participant['score']; ?><br>
                                    <b>Time taken:</b> <?php echo $participant['completion_time']; ?> seconds
                                </div>
                            </div>
                        <?php else: ?>
                            <div id="gameArea">
                                <div class="timer-box mb-3">
                                    Time: <span id="timeElapsed">0</span> seconds
                                </div>
                                <div id="questionContainer">
                                    <h5 class="question-title mb-3">Question <span id="currentQuestion">1</span> of <?php echo count($questions); ?></h5>
                                    <div id="questionText" class="mb-3"></div>
                                    <div id="choicesContainer" class="mb-3"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div id="gameResults">
                            <h4 class="mb-3">Game Over!</h4>
                            <div class="leaderboard-card">
                                <h5>Final Results:</h5>
                                <table class="table leaderboard-table">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Player</th>
                                            <th>Score</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($participants as $index => $p): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><?php echo htmlspecialchars($p['username']); ?></td>
                                                <td><?php echo $p['score']; ?></td>
                                                <td><?php echo $p['completion_time']; ?>s</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="leaderboard-card">
                    <h5 class="mb-3">Leaderboard</h5>
                    <table class="table leaderboard-table">
                        <thead>
                            <tr>
                                <th>Player</th>
                                <th>Score</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participants as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['username']); ?></td>
                                    <td><?php echo $p['score']; ?></td>
                                    <td><?php echo $p['completion_time'] ? $p['completion_time'] . 's' : '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
    const roomCode = '<?php echo $room_code; ?>';
    const userId = <?php echo $_SESSION['user_id']; ?>;
    const isCreator = <?php echo $room['created_by'] === $_SESSION['user_id'] ? 'true' : 'false'; ?>;
    const gameStatus = '<?php echo $room['status']; ?>';
    const questions = <?php echo json_encode($questions); ?>;
    const gameStartTime = '<?php echo $game_start_time; ?>';
    const answeredQuestions = <?php echo json_encode($answered_questions); ?>;
    let unansweredQuestions = questions.filter(q => !answeredQuestions.includes(q.id));
    const storageKey = `currentQuestionIndex_${roomCode}_${userId}`;
    let currentQuestionIndex = parseInt(localStorage.getItem(storageKey)) || 0;
    let timerInterval = null;
    
    // Function to update the leaderboard
    function updateLeaderboard() {
        fetch(`get_leaderboard.php?room=${roomCode}`)
            .then(response => response.json())
            .then(data => {
                const leaderboardTables = document.querySelectorAll('.leaderboard-table tbody');
                leaderboardTables.forEach(tbody => {
                    tbody.innerHTML = '';
                    data.forEach(p => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${p.username}</td>
                                <td>${p.score}</td>
                                <td>${p.completion_time ? p.completion_time + 's' : '-'}</td>
                            </tr>
                        `;
                    });
                });
            });
    }
    
    // Function to start the game
    function startGame() {
        fetch('start_game.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ room_code: roomCode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to start game');
            }
        });
    }
    
    // Function to submit an answer
    function submitAnswer(choiceId) {
        fetch('submit_answer.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                room_code: roomCode,
                question_id: unansweredQuestions[currentQuestionIndex].id,
                choice_id: choiceId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                answeredQuestions.push(unansweredQuestions[currentQuestionIndex].id);
                currentQuestionIndex++;
                localStorage.setItem(storageKey, currentQuestionIndex);
                updateLeaderboard();
                if (currentQuestionIndex < unansweredQuestions.length) {
                    displayQuestion();
                } else {
                    localStorage.removeItem(storageKey);
                    endGame();
                }
            } else {
                alert(data.error || 'Failed to submit answer');
            }
        })
        .catch(error => {
            alert('Error submitting answer: ' + error.message);
        });
    }
    
    // Function to display the current question
    function displayQuestion() {
        if (currentQuestionIndex >= unansweredQuestions.length) {
            endGame();
            return;
        }
        const question = unansweredQuestions[currentQuestionIndex];
        document.getElementById('currentQuestion').textContent = currentQuestionIndex + 1;
        document.getElementById('questionText').textContent = question.text;
        
        const choicesContainer = document.getElementById('choicesContainer');
        choicesContainer.innerHTML = '';
        question.choices.forEach(choice => {
            const button = document.createElement('button');
            button.className = 'btn btn-outline-primary answer-btn';
            button.textContent = choice.text;
            button.onclick = () => submitAnswer(choice.id);
            choicesContainer.appendChild(button);
        });
    }
    
    // Function to end the game
    function endGame() {
        clearInterval(timerInterval);
        localStorage.removeItem(storageKey);
        const timeElapsed = updateTimer();
        fetch('end_game.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                room_code: roomCode,
                time_elapsed: timeElapsed
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to end game');
            }
        });
    }
    
    function updateTimer() {
        // Parse the server start time as local time
        const start = new Date(gameStartTime.replace(' ', 'T'));
        const now = new Date();
        const elapsed = Math.floor((now - start) / 1000);
        document.getElementById('timeElapsed').textContent = elapsed;
        return elapsed;
    }
    
    // Initialize the game
    if (gameStatus === 'waiting') {
        // Poll for updates every 5 seconds
        setInterval(() => {
            fetch(`get_room_status.php?room=${roomCode}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status !== 'waiting') {
                        location.reload();
                    }
                    updateLeaderboard();
                });
        }, 5000);
        
        if (isCreator) {
            document.getElementById('startGameBtn').onclick = startGame;
        }
    } else if (gameStatus === 'active' && !<?php echo $participant['completed_at'] ? 'true' : 'false'; ?>) {
        updateTimer(); // Set initial value
        timerInterval = setInterval(updateTimer, 1000);
        setInterval(updateLeaderboard, 3000);
        displayQuestion();
    } else if (gameStatus === 'completed') {
        // Poll for final results every 5 seconds
        setInterval(updateLeaderboard, 5000);
    }
    </script>
    </body>
    <?php

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?> 