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

$questions = [];
while ($row = $questionsResult->fetch_assoc()) {
    $q_id = $row['question_id'];
    if (!isset($questions[$q_id])) {
        $questions[$q_id] = [
            'text' => $row['question_text'],
            'choices' => [],
            'correct_answer' => ''
        ];
    }
    $questions[$q_id]['choices'][] = [
        'id' => $row['choice_id'],
        'text' => $row['choice_text'],
        'correct' => $row['is_correct']
    ];
    if ($row['is_correct']) {
        $questions[$q_id]['correct_answer'] = $row['choice_text'];
    }
}

// Initialize session tracking for this lab
if (!isset($_SESSION["lab_$lab_id"])) {
    $_SESSION["lab_$lab_id"] = [
        'attempts' => 0,
        'incorrect_questions' => [],
        'completed' => false,
        'final_score' => 0,
        'last_attempt_answers' => []
    ];
}

$labSession = &$_SESSION["lab_$lab_id"];
$score = 0;
$showAllQuestions = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $labSession['attempts']++;
    $labSession['incorrect_questions'] = [];
    $labSession['last_attempt_answers'] = [];

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
                unset($labSession['incorrect_questions'][$q_id]); // Remove from incorrect list if corrected
            } else {
                $labSession['incorrect_questions'][$q_id] = $userChoice; // Keep incorrect answer
            }
        }
    }

    if ($score == count($questions)) {
        $labSession['completed'] = true;
        $labSession['final_score'] = $score;
        $showAllQuestions = true;
    } elseif ($labSession['attempts'] >= 3) {
        $showAllQuestions = true;
    }

    // **Save the score to the database**
    $insertQuery = $conn->prepare("INSERT INTO students_answers (user_id, lab_id, score) VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE score = VALUES(score)");
    $insertQuery->bind_param("iii", $user_id, $lab_id, $score);
    $insertQuery->execute();
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
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($lab['name']); ?> - Lab Questions</h1>
        <p><?php echo htmlspecialchars($lab['description']); ?></p>

        <div class="score-section">
            <h2>Score: <span><?php echo $score; ?>/<?php echo count($questions); ?></span></h2>
            <p>Attempts: <?php echo $labSession['attempts']; ?>/3</p>
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
                            $answerClass = $choice['correct'] ? "correct-answer highlight" : "incorrect-answer highlight";
                        } elseif ($choice['correct']) {
                            $answerClass = "correct-answer highlight";
                        }
                    }

                    echo "<label class='$answerClass'>";
                    echo "<input type='radio' name='q$q_id' value='{$choice['id']}' $checked required> " . htmlspecialchars($choice['text']);
                    echo "</label>";
                }

                echo "</div></div>";
                $qNum++;
            }
            ?>
            <button type="submit" class="submit-btn">Submit</button>
        </form>

        <a href="index.php" class="nav-btn">Back</a>
    </div>
</body>
</html>
