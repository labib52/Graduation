<?php
session_start();

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";

// Track user attempts and completion
$labId = 'mitm_arp_poisoning_lab'; // Unique identifier for this lab
if (!isset($_SESSION[$labId])) {
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
    "q2" => "a",
    "q3" => "c",
    "q4" => "d",
    "q5" => "a",
    "q6" => "b",
    "q7" => "c",
    "q8" => "a",
    "q9" => "d",
    "q10" => "b",
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
                unset($_SESSION[$labId]['incorrect_questions'][$question]);
            } else {
                $_SESSION[$labId]['incorrect_questions'][$question] = $_POST[$question];
            }
        }
    }

    if ($score == 10) {
        $_SESSION[$labId]['completed'] = true;
        $_SESSION[$labId]['final_score'] = $score;
        $showAllQuestions = true;
    } elseif ($_SESSION[$labId]['attempts'] >= 3) {
        $showAllQuestions = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARP Poisoning - Lab</title>
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
        <h1>ARP Poisoning - Lab Questions</h1>

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
                    <a href="attack_mitm_arp_poisoning.php" class="done-btn">Done</a>
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
                    "q1" => "What is the purpose of ARP in networking?",
                    "q2" => "What is the key weakness exploited in ARP spoofing?",
                    "q3" => "What command displays the ARP table in Linux?",
                    "q4" => "Which tool is commonly used for ARP spoofing?",
                    "q5" => "How many ARP responses are sent in an ARP spoofing attack?",
                    "q6" => "What role does port forwarding play in ARP spoofing?",
                    "q7" => "What is the correct syntax for arpspoof command?",
                    "q8" => "How does ARP spoofing enable Man-in-the-Middle attacks?",
                    "q9" => "What is the default IP range in an ARP spoofing attack?",
                    "q10" => "How can ARP spoofing attacks be mitigated?"
                ];

                $options = [
                    "q1" => ["a" => "To encrypt data", "b" => "To map IP to MAC", "c" => "To assign dynamic IPs", "d" => "To monitor traffic"],
                    "q2" => ["a" => "No verification of responses", "b" => "Weak passwords", "c" => "Lack of encryption", "d" => "All of the above"],
                    "q3" => ["a" => "arp -a", "b" => "ifconfig", "c" => "netstat", "d" => "ipconfig"],
                    "q4" => ["a" => "nmap", "b" => "arpspoof", "c" => "aircrack-ng", "d" => "wireshark"],
                    "q5" => ["a" => "2", "b" => "4", "c" => "6", "d" => "1"],
                    "q6" => ["a" => "Drops unwanted packets", "b" => "Enables packet forwarding", "c" => "Encrypts packets", "d" => "None"],
                    "q7" => ["a" => "arpspoof -i interface -t victimIP gatewayIP", "b" => "arpspoof gatewayIP victimIP", "c" => "arp -spoof interface victim", "d" => "None"],
                    "q8" => ["a" => "Intercepts communication", "b" => "Blocks data flow", "c" => "Encrypts data", "d" => "None"],
                    "q9" => ["a" => "10.0.0.0/24", "b" => "192.168.1.0/24", "c" => "Depends on network", "d" => "127.0.0.1/24"],
                    "q10" => ["a" => "Static ARP tables", "b" => "Encryption", "c" => "Monitoring", "d" => "All of the above"]
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

        <a href="attack_mitm_arp_poisoning_lec.php" class="nav-btn">Back</a>
    </div>
</body>
</html>
