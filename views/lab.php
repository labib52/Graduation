<?php
session_start();
include('../controller/db_connection.php');

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit answers.");
}

$user_id = $_SESSION['user_id']; // Get user ID from session
$lab_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($lab_id <= 0) {
    die("Invalid Lab ID");
}

// Fetch lab details
$labQuery = $conn->prepare("SELECT name, description FROM labs WHERE id = ?");
$labQuery->bind_param("i", $lab_id);
$labQuery->execute();
$labResult = $labQuery->get_result();
$lab = $labResult->fetch_assoc();
if (!$lab) {
    die("Lab not found");
}

// Initialize session tracking for this lab first
if (!isset($_SESSION["lab_$lab_id"])) {
    $_SESSION["lab_$lab_id"] = [
        'incorrect_questions' => [],
        'completed' => false,
        'final_score' => 0,
        'last_attempt_answers' => [],
        'submission_count' => 0,
        'current_score' => 0,
        'selected_questions' => []
    ];
}

$labSession = &$_SESSION["lab_$lab_id"];

// Ensure all session keys exist
if (!isset($labSession['submission_count'])) {
    $labSession['submission_count'] = 0;
}
if (!isset($labSession['current_score'])) {
    $labSession['current_score'] = 0;
}
if (!isset($labSession['selected_questions'])) {
    $labSession['selected_questions'] = [];
}

// Fetch questions and choices
$questionsQuery = $conn->prepare("
    SELECT q.id AS question_id, q.question_text, c.id AS choice_id, c.choice_text, c.is_correct 
    FROM questions q 
    JOIN choices c ON q.id = c.question_id 
    WHERE q.lab_id = ?
");
$questionsQuery->bind_param("i", $lab_id);
$questionsQuery->execute();
$questionsResult = $questionsQuery->get_result();

$allQuestions = [];
while ($row = $questionsResult->fetch_assoc()) {
    $q_id = $row['question_id'];
    if (!isset($allQuestions[$q_id])) {
        $allQuestions[$q_id] = [
            'text' => $row['question_text'],
            'choices' => [],
            'correct_answer' => ''
        ];
    }
    $allQuestions[$q_id]['choices'][] = [
        'id' => $row['choice_id'],
        'text' => $row['choice_text'],
        'correct' => $row['is_correct']
    ];
    if ($row['is_correct']) {
        $allQuestions[$q_id]['correct_answer'] = $row['choice_text'];
    }
}

// Initialize or get user's random questions
if (empty($labSession['selected_questions'])) {
    // Select 10 random questions
    $questionIds = array_keys($allQuestions);
    if (count($questionIds) > 10) {
        shuffle($questionIds);
        $selectedIds = array_slice($questionIds, 0, 10);
    } else {
        $selectedIds = $questionIds;
    }
    
    $labSession['selected_questions'] = $selectedIds;
}

// Get only the selected questions
$questions = [];
foreach ($labSession['selected_questions'] as $q_id) {
    if (isset($allQuestions[$q_id])) {
        $questions[$q_id] = $allQuestions[$q_id];
    }
}

$score = $labSession['current_score'];
$showAllQuestions = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reset'])) {
        // Clear all answers and reset the session
        $_SESSION["lab_$lab_id"] = [
            'incorrect_questions' => [],
            'completed' => false,
            'final_score' => 0,
            'last_attempt_answers' => [],
            'submission_count' => 0,
            'current_score' => 0,
            'selected_questions' => []
        ];
        $labSession = &$_SESSION["lab_$lab_id"];
        $score = 0;
        $showAllQuestions = false;
    } else if (isset($_POST['check_answers'])) {
        $showAllQuestions = true;
    } else {
        $labSession['submission_count']++;
        $labSession['incorrect_questions'] = [];
        $labSession['last_attempt_answers'] = [];
        $score = 0; // Reset score for new calculation

        foreach ($questions as $q_id => $question) {
            if (isset($_POST["q$q_id"])) {
                $userChoice = intval($_POST["q$q_id"]);
                $labSession['last_attempt_answers'][$q_id] = $userChoice;
                $correctChoice = false;

                foreach ($question['choices'] as $choice) {
                    if ($choice['id'] == $userChoice && $choice['correct'] == 1) {
                        $correctChoice = true;
                        break;
                    }
                }

                if ($correctChoice) {
                    $score++;
                    unset($labSession['incorrect_questions'][$q_id]);
                } else {
                    $labSession['incorrect_questions'][$q_id] = $userChoice;
                }
            }
        }

        $labSession['current_score'] = $score; // Save the new score in session

        if ($score == count($questions)) {
            $labSession['completed'] = true;
            $labSession['final_score'] = $score;
            $showAllQuestions = true;
        }

        // Save the score to the database
        $insertQuery = $conn->prepare("INSERT INTO students_answers (user_id, lab_id, score) VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE score = VALUES(score)");
        $insertQuery->bind_param("iii", $user_id, $lab_id, $score);
        $insertQuery->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lab['name']); ?> - Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/CSS/wirelesslab.css">
    <style>
        .incorrect-answer { color: red; font-weight: bold; }
        .correct-answer { color: green; font-weight: bold; }
        .highlight { padding: 5px; border-radius: 5px; }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .reset-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .reset-btn:hover {
            background-color: #c82333;
        }
        .check-answers-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .check-answers-btn:hover {
            background-color: #218838;
        }
        .question-status {
            margin-top: 5px;
            font-weight: bold;
        }
        .question-status.correct {
            color: green;
        }
        .question-status.incorrect {
            color: red;
        }
        .correct-indicator {
            color: green;
            font-weight: bold;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($lab['name']); ?> - Lab Questions</h1>
        <p><?php echo htmlspecialchars($lab['description']); ?></p>

        <div class="score-section">
            <h2>Score: <span><?php echo $score; ?>/<?php echo count($questions); ?></span></h2>
        </div>

        <form method="post">
            <?php
            $qNum = 1;
            foreach ($questions as $q_id => $question) {
                echo "<div class='question'>";
                echo "<h3>$qNum. " . htmlspecialchars($question['text']) . "</h3>";
                echo "<div class='options'>";

                foreach ($question['choices'] as $choice) {
                    $checked = isset($labSession['last_attempt_answers'][$q_id]) && $labSession['last_attempt_answers'][$q_id] == $choice['id'] ? "checked" : "";
                    
                    // Style answers
                    $answerClass = "";
                    if (isset($labSession['last_attempt_answers'][$q_id])) {
                        if ($choice['id'] == $labSession['last_attempt_answers'][$q_id]) {
                            $answerClass = $choice['correct'] ? "correct-answer" : "incorrect-answer";
                        }
                    }

                    // Add correct answer indicator if showing all answers
                    $correctIndicator = "";
                    if ($showAllQuestions && $choice['correct']) {
                        $correctIndicator = "<span class='correct-indicator'>✓</span>";
                    }

                    echo "<label class='$answerClass'>";
                    echo "<input type='radio' name='q$q_id' value='{$choice['id']}' $checked required> " . htmlspecialchars($choice['text']) . $correctIndicator;
                    echo "</label>";
                }

                // Add status indicator after the question
                if (isset($labSession['last_attempt_answers'][$q_id])) {
                    $isCorrect = false;
                    foreach ($question['choices'] as $choice) {
                        if ($choice['id'] == $labSession['last_attempt_answers'][$q_id] && $choice['correct']) {
                            $isCorrect = true;
                            break;
                        }
                    }
                    echo "<div class='question-status " . ($isCorrect ? "correct" : "incorrect") . "'>";
                    echo $isCorrect ? "✓ Correct" : "✗ Incorrect";
                    echo "</div>";
                }

                echo "</div></div>";
                $qNum++;
            }
            ?>
            <div class="button-group">
                <button type="submit" class="submit-btn">Submit</button>
                <button type="submit" name="reset" class="reset-btn">Reset Answers</button>
                <?php if ($labSession['submission_count'] >= 3): ?>
                    <button type="submit" name="check_answers" class="check-answers-btn">Check Answers</button>
                <?php endif; ?>
            </div>
        </form>

        <a href="javascript:history.back()" class="nav-btn">Back</a>
    </div>
</body>
</html>
