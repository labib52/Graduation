<?php
session_start();

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";

// Track user attempts and completion
$pageKey = "wep_cracking_lab"; // Unique key for this lab
if (!isset($_SESSION[$pageKey]['attempts'])) {
    $_SESSION[$pageKey]['attempts'] = 0;
    $_SESSION[$pageKey]['incorrect_questions'] = []; // Store incorrect questions from the last attempt
    $_SESSION[$pageKey]['completed'] = false;
    $_SESSION[$pageKey]['final_score'] = 0;
}

$score = 0;
$incorrectQuestions = [];
$lastAttemptAnswers = [];
$showAllQuestions = false;

$correctAnswers = [
    "q1" => "a",
    "q2" => "c",
    "q3" => "b",
    "q4" => "d",
    "q5" => "a",
    "q6" => "d",
    "q7" => "c",
    "q8" => "b",
    "q9" => "a",
    "q10" => "c",
];

// If the user has completed the lab, show feedback and prevent further attempts
if ($_SESSION[$pageKey]['completed']) {
    $score = $_SESSION[$pageKey]['final_score'];
    $showAllQuestions = true;
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION[$pageKey]['attempts']++;
    foreach ($correctAnswers as $question => $correctAnswer) {
        if (isset($_POST[$question])) {
            $lastAttemptAnswers[$question] = $_POST[$question];
            if ($_POST[$question] == $correctAnswer) {
                $score++;
                unset($_SESSION[$pageKey]['incorrect_questions'][$question]); // Remove question from incorrect list if answered correctly
            } else {
                $_SESSION[$pageKey]['incorrect_questions'][$question] = $_POST[$question]; // Store incorrect answer
            }
        }
    }

    if ($score == 10) {
        $_SESSION[$pageKey]['completed'] = true;
        $_SESSION[$pageKey]['final_score'] = $score; // Save the final score
        $showAllQuestions = true;
    } elseif ($_SESSION[$pageKey]['attempts'] >= 3) {
        $showAllQuestions = true; // Show all questions with correct answers after 3 attempts
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEP Cracking - Lab</title>
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
        <h1>WEP Cracking - Lab Questions</h1>

        <?php if ($showAllQuestions): ?>
            <div class="result">
                <h2>Your Score: <strong><?php echo $_SESSION[$pageKey]['final_score']; ?>/10</strong></h2>
                <p>
                    <?php
                    if ($_SESSION[$pageKey]['completed']) {
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
                        <?php if (isset($_SESSION[$pageKey]['incorrect_questions'][$question])): ?>
                            <p>Your Last Answer: <span class="incorrect"><?php echo strtoupper($_SESSION[$pageKey]['incorrect_questions'][$question]); ?></span></p>
                        <?php endif; ?>
                    </div>
                <?php
                    $qNum++;
                endforeach;
                ?>

                <?php if ($_SESSION[$pageKey]['completed']): ?>
                    <a href="attack_wep_cracking.php" class="done-btn">Done</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="result">
                <h2>Your Score: <strong><?php echo $score; ?>/10</strong></h2>
                <p>Incorrect Question Numbers:</p>
                <ul>
                    <?php foreach ($_SESSION[$pageKey]['incorrect_questions'] as $question => $answer): ?>
                        <li><?php echo ucfirst($question); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <form method="post">
                <?php
                $questions = [
                    "q1" => "What does WEP stand for?",
                    "q2" => "What algorithm is used by WEP for encryption?",
                    "q3" => "What is the size of the IV in WEP?",
                    "q4" => "What is appended to each WEP packet for decryption?",
                    "q5" => "What is a major weakness of WEP?",
                    "q6" => "Which tool is used to capture packets for WEP cracking?",
                    "q7" => "What method generates new packets to increase IV collection?",
                    "q8" => "What is an ARP replay attack used for?",
                    "q9" => "Which tool cracks the WEP key from captured IVs?",
                    "q10" => "What happens when an IV is repeated in WEP?"
                ];

                $options = [
                    "q1" => ["a" => "Wired Equivalent Privacy", "b" => "Wireless Encrypted Protocol", "c" => "Wireless Encryption Privacy", "d" => "Wired Encryption Protocol"],
                    "q2" => ["a" => "AES", "b" => "DES", "c" => "RC4", "d" => "SHA-256"],
                    "q3" => ["a" => "16 bits", "b" => "24 bits", "c" => "32 bits", "d" => "64 bits"],
                    "q4" => ["a" => "Encryption Key", "b" => "Router Address", "c" => "IV (Initialization Vector)", "d" => "MAC Address"],
                    "q5" => ["a" => "Small IV size", "b" => "Strong encryption", "c" => "Complex authentication", "d" => "High computational cost"],
                    "q6" => ["a" => "Aireplay-ng", "b" => "Aircrack-ng", "c" => "Airodump-ng", "d" => "Wash"],
                    "q7" => ["a" => "Fake association", "b" => "Packet injection", "c" => "ARP replay attack", "d" => "Deauthentication attack"],
                    "q8" => ["a" => "Capture IVs for analysis", "b" => "Force the router to generate new packets", "c" => "Prevent IV repetition", "d" => "Intercept key exchanges"],
                    "q9" => ["a" => "Aireplay-ng", "b" => "Airodump-ng", "c" => "Aircrack-ng", "d" => "Wireshark"],
                    "q10" => ["a" => "The key becomes vulnerable to cracking", "b" => "Packets are discarded", "c" => "The IV resets to zero", "d" => "Decryption becomes impossible"]
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

        <a href="attack_wep_cracking_lec.php" class="nav-btn">Back</a>
    </div>
</body>
</html>
