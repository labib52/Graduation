<?php
session_start();

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";

// Track user attempts and completion
$labId = 'wpa_cracking_lab'; // Unique identifier for this lab
if (!isset($_SESSION[$labId]['attempts'])) {
    $_SESSION[$labId] = [
        'attempts' => 0,
        'incorrect_questions' => [],
        'completed' => false,
        'final_score' => 0
    ];
}

$score = 0;
$incorrectQuestions = [];
$lastAttemptAnswers = [];
$showAllQuestions = false;

$correctAnswers = [
    "q1" => "b",
    "q2" => "c",
    "q3" => "a",
    "q4" => "d",
    "q5" => "a",
    "q6" => "c",
    "q7" => "d",
    "q8" => "b",
    "q9" => "a",
    "q10" => "c",
];

// If the user has completed the lab, show feedback and prevent further attempts
if ($_SESSION[$labId]['completed']) {
    $score = $_SESSION[$labId]['final_score'];
    $showAllQuestions = true;
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION[$labId]['attempts']++;
    foreach ($correctAnswers as $question => $correctAnswer) {
        if (isset($_POST[$question])) {
            $lastAttemptAnswers[$question] = $_POST[$question];
            if ($_POST[$question] == $correctAnswer) {
                $score++;
                unset($_SESSION[$labId]['incorrect_questions'][$question]); // Remove question from incorrect list if answered correctly
            } else {
                $_SESSION[$labId]['incorrect_questions'][$question] = $_POST[$question]; // Store incorrect answer
            }
        }
    }

    if ($score == 10) {
        $_SESSION[$labId]['completed'] = true;
        $_SESSION[$labId]['final_score'] = $score; // Save the final score
        $showAllQuestions = true;
    } elseif ($_SESSION[$labId]['attempts'] >= 3) {
        $showAllQuestions = true; // Show all questions with correct answers after 3 attempts
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WPA Cracking - Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f4f9;
            color: #333;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #007BFF;
            margin-bottom: 20px;
        }

        .question {
            margin: 20px 0;
        }

        .options label {
            display: block;
            margin-bottom: 10px;
        }

        .nav-btn, .submit-btn, .done-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background-color: #007BFF;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        .nav-btn:hover, .submit-btn:hover, .done-btn:hover {
            background-color: #0056b3;
        }

        .result {
            margin-top: 20px;
            padding: 15px;
            background: #eaf4ff;
            border-left: 5px solid #007BFF;
        }

        .result strong {
            font-weight: 600;
        }

        .incorrect {
            color: red;
            font-weight: bold;
        }

        .correct-answer {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>WPA Cracking - Lab Questions</h1>

        <?php if ($showAllQuestions): ?>
            <div class="result">
                <h2>Your Score: <strong><?php echo $_SESSION[$labId]['final_score']; ?>/10</strong></h2>
                <p>
                    <?php
                    if ($_SESSION[$labId]['completed']) {
                        echo "🎉 Congratulations! You have completed the lab with a perfect score.";
                    } elseif ($score >= 7) {
                        echo "👍 Good job! You understand most of the concepts, but there's room for improvement.";
                    } else {
                        echo "📖 Keep learning! Review the lecture to improve your understanding.";
                    }
                    ?>
                </p>

                <h3>Review Correct Answers:</h3>
                <?php
                $qNum = 1;
                foreach ($correctAnswers as $question => $correctAnswer):
                ?>
                    <div class="question">
                        <h3><?php echo "Question $qNum"; ?></h3>
                        <p>Correct Answer: <span class="correct-answer"><?php echo strtoupper($correctAnswer); ?></span></p>
                        <?php if (isset($_SESSION[$labId]['incorrect_questions'][$question])): ?>
                            <p>Your Last Answer: <span class="incorrect"><?php echo strtoupper($_SESSION[$labId]['incorrect_questions'][$question]); ?></span></p>
                        <?php endif; ?>
                    </div>
                <?php
                    $qNum++;
                endforeach;
                ?>

                <?php if ($_SESSION[$labId]['completed']): ?>
                    <a href="attack_wpa_cracking.php" class="done-btn">Done</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="result">
                <h2>Your Score: <strong><?php echo $score; ?>/10</strong></h2>
                <p>Incorrect Question Numbers:</p>
                <ul>
                    <?php foreach ($_SESSION[$labId]['incorrect_questions'] as $question => $answer): ?>
                        <li><?php echo ucfirst($question); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <form method="post">
                <?php
                $questions = [
                    "q1" => "What is the main difference between WPA and WPA2?",
                    "q2" => "What does WPA use for encryption integrity?",
                    "q3" => "What packets are essential for cracking WPA?",
                    "q4" => "What tool is used to generate a wordlist?",
                    "q5" => "How many packets are exchanged during a WPA handshake?",
                    "q6" => "What is the role of the MIC in WPA cracking?",
                    "q7" => "Which tool is used to capture WPA handshakes?",
                    "q8" => "What is the function of the deauthentication attack?",
                    "q9" => "How does aircrack-ng verify a password during WPA cracking?",
                    "q10" => "What factors influence the success of WPA cracking?"
                ];

                $options = [
                    "q1" => ["a" => "Speed", "b" => "Encryption integrity", "c" => "Hardware support", "d" => "None"],
                    "q2" => ["a" => "AES", "b" => "SHA-256", "c" => "TKIP", "d" => "HMAC"],
                    "q3" => ["a" => "Handshake packets", "b" => "Initialization packets", "c" => "Key packets", "d" => "IV packets"],
                    "q4" => ["a" => "aircrack-ng", "b" => "crunch", "c" => "airodump-ng", "d" => "reaver"],
                    "q5" => ["a" => "4", "b" => "2", "c" => "6", "d" => "3"],
                    "q6" => ["a" => "Decrypts the handshake", "b" => "Validates the key", "c" => "Generates the IV", "d" => "None"],
                    "q7" => ["a" => "crunch", "b" => "aircrack-ng", "c" => "aireplay-ng", "d" => "airodump-ng"],
                    "q8" => ["a" => "Generates IVs", "b" => "Forces client reconnection", "c" => "Increases data traffic", "d" => "None"],
                    "q9" => ["a" => "Compares MICs", "b" => "Decrypts packets", "c" => "Finds key length", "d" => "None"],
                    "q10" => ["a" => "Wordlist quality", "b" => "Handshake capture", "c" => "Network configuration", "d" => "All of the above"]
                ];

                $qNum = 1;
                foreach ($questions as $key => $qText) {
                    echo "<div class='question'><h3>$qNum. $qText</h3><div class='options'>";
                    foreach ($options[$key] as $optKey => $optValue) {
                        $checked = isset($lastAttemptAnswers[$key]) && $lastAttemptAnswers[$key] == $optKey ? "checked" : "";
                        echo "<label><input type='radio' name='$key' value='$optKey' $checked required> $optValue</label>";
                    }
                    echo "</div></div>";
                    $qNum++;
                }
                ?>
                <button type="submit" class="submit-btn">Submit</button>
            </form>
        <?php endif; ?>

        <a href="attack_wpa_cracking_lec.php" class="nav-btn">Back</a>
    </div>
</body>
</html>
