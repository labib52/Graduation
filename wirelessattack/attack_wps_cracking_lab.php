<?php
session_start();

// Unique session keys for this specific lab
$labKey = 'wps_cracking_lab';
if (!isset($_SESSION[$labKey])) {
    $_SESSION[$labKey] = [
        'attempts' => 0,
        'completed' => false,
        'final_score' => 0,
        'incorrect_questions' => []
    ];
}

$score = 0;
$incorrectQuestions = [];
$lastAttemptAnswers = [];
$showAllQuestions = false;

$correctAnswers = [
    "q1" => "c",
    "q2" => "a",
    "q3" => "b",
    "q4" => "d",
    "q5" => "a",
    "q6" => "b",
    "q7" => "c",
    "q8" => "d",
    "q9" => "a",
    "q10" => "c",
];

// Check if the lab is completed or process the POST request
if ($_SESSION[$labKey]['completed']) {
    $score = $_SESSION[$labKey]['final_score'];
    $showAllQuestions = true;
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION[$labKey]['attempts']++;
    foreach ($correctAnswers as $question => $correctAnswer) {
        if (isset($_POST[$question])) {
            $lastAttemptAnswers[$question] = $_POST[$question];
            if ($_POST[$question] == $correctAnswer) {
                $score++;
                unset($_SESSION[$labKey]['incorrect_questions'][$question]); // Remove if answered correctly
            } else {
                $_SESSION[$labKey]['incorrect_questions'][$question] = $_POST[$question]; // Store incorrect answer
            }
        }
    }

    // Update session state based on the score
    if ($score == 10) {
        $_SESSION[$labKey]['completed'] = true;
        $_SESSION[$labKey]['final_score'] = $score; // Save final score
        $showAllQuestions = true;
    } elseif ($_SESSION[$labKey]['attempts'] >= 3) {
        $showAllQuestions = true; // Show all questions after 3 attempts
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WPS Cracking - Lab</title>
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
        <h1>WPS Cracking - Lab Questions</h1>

        <?php if ($showAllQuestions): ?>
            <div class="result">
                <h2>Your Score: <strong><?php echo $_SESSION[$labKey]['final_score']; ?>/10</strong></h2>
                <p>
                    <?php
                    if ($_SESSION[$labKey]['completed']) {
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
                        <?php if (isset($_SESSION[$labKey]['incorrect_questions'][$question])): ?>
                            <p>Your Last Answer: <span class="incorrect"><?php echo strtoupper($_SESSION[$labKey]['incorrect_questions'][$question]); ?></span></p>
                        <?php endif; ?>
                    </div>
                <?php
                    $qNum++;
                endforeach;
                ?>

                <?php if ($_SESSION[$labKey]['completed']): ?>
                    <a href="attack_wps_cracking.php" class="done-btn">Done</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="result">
                <h2>Your Score: <strong><?php echo $score; ?>/10</strong></h2>
                <p>Incorrect Question Numbers:</p>
                <ul>
                    <?php foreach ($_SESSION[$labKey]['incorrect_questions'] as $question => $answer): ?>
                        <li><?php echo ucfirst($question); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <form method="post">
                <?php
                $questions = [
                    "q1" => "What does WPS stand for?",
                    "q2" => "What is the primary purpose of WPS?",
                    "q3" => "How is authentication performed in WPS?",
                    "q4" => "What does the WPS button on a printer do?",
                    "q5" => "What kind of PIN is used in WPS authentication?",
                    "q6" => "Which mode must be disabled for WPS attacks to work?",
                    "q7" => "Which tool is used to find networks with WPS enabled?",
                    "q8" => "What is the role of the tool Reaver in WPS attacks?",
                    "q9" => "Why might Reaver fail during a WPS attack?",
                    "q10" => "Which tool is used to associate with a target network during a WPS attack?"
                ];

                $options = [
                    "q1" => ["a" => "Wireless Protected Server", "b" => "Wi-Fi Protected Setup", "c" => "Wireless Protocol System", "d" => "Wi-Fi Protected System"],
                    "q2" => ["a" => "To connect devices securely without a password", "b" => "To enhance network speed", "c" => "To provide encryption", "d" => "To disable wireless access"],
                    "q3" => ["a" => "Using a 16-digit code", "b" => "Using an 8-digit PIN", "c" => "Using a password", "d" => "Using device certificates"],
                    "q4" => ["a" => "Connects to a router using a password", "b" => "Connects to a router without entering a key", "c" => "Scans for networks", "d" => "Disables network connections"],
                    "q5" => ["a" => "An 8-digit numeric PIN", "b" => "An alphanumeric key", "c" => "A 4-digit numeric PIN", "d" => "A biometric PIN"],
                    "q6" => ["a" => "WPA2", "b" => "Push Button Authentication (PBC)", "c" => "MAC Filtering", "d" => "DHCP"],
                    "q7" => ["a" => "Reaver", "b" => "Nmap", "c" => "Wash", "d" => "Aireplay-ng"],
                    "q8" => ["a" => "It scans for networks", "b" => "It finds MAC addresses", "c" => "It brute forces the WPS PIN", "d" => "It disables WPS"],
                    "q9" => ["a" => "If the router uses PBC mode", "b" => "If the PIN is too complex", "c" => "If WPS is disabled", "d" => "If WPA2 is enabled"],
                    "q10" => ["a" => "Reaver", "b" => "Nmap", "c" => "Aireplay-ng", "d" => "Wash"]
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

        <a href="attack_wps_cracking_lec.php" class="nav-btn">Back</a>
    </div>
</body>
</html>
