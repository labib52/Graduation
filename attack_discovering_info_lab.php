<?php
session_start();

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";

// Track user attempts and completion
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['incorrect_questions'] = []; // Store incorrect questions from the last attempt
}
if (!isset($_SESSION['completed'])) {
    $_SESSION['completed'] = false;
}
if (!isset($_SESSION['final_score'])) {
    $_SESSION['final_score'] = 0; // Initialize the final score
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
    "q5" => "b",
    "q6" => "a",
    "q7" => "d",
    "q8" => "c",
    "q9" => "b",
    "q10" => "a",
];

// If the user has completed the lab, show feedback and prevent further attempts
if ($_SESSION['completed']) {
    $score = $_SESSION['final_score'];
    $showAllQuestions = true;
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['attempts']++;
    foreach ($correctAnswers as $question => $correctAnswer) {
        if (isset($_POST[$question])) {
            $lastAttemptAnswers[$question] = $_POST[$question];
            if ($_POST[$question] == $correctAnswer) {
                $score++;
                unset($_SESSION['incorrect_questions'][$question]); // Remove question from incorrect list if answered correctly
            } else {
                $_SESSION['incorrect_questions'][$question] = $_POST[$question]; // Store incorrect answer
            }
        }
    }

    if ($score == 10) {
        $_SESSION['completed'] = true;
        $_SESSION['final_score'] = $score; // Save the final score
        $showAllQuestions = true;
    } elseif ($_SESSION['attempts'] >= 3) {
        $showAllQuestions = true; // Show all questions with correct answers after 3 attempts
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discovering Sensitive Info - Lab</title>
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
        <h1>Discovering Sensitive Info - Lab Questions</h1>

        <?php if ($showAllQuestions): ?>
            <div class="result">
                <h2>Your Score: <strong><?php echo $_SESSION['final_score']; ?>/10</strong></h2>
                <p>
                    <?php
                    if ($_SESSION['completed']) {
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
                        <?php if (isset($_SESSION['incorrect_questions'][$question])): ?>
                            <p>Your Last Answer: <span class="incorrect"><?php echo strtoupper($_SESSION['incorrect_questions'][$question]); ?></span></p>
                        <?php endif; ?>
                    </div>
                <?php
                    $qNum++;
                endforeach;
                ?>

                <?php if ($_SESSION['completed']): ?>
                    <a href="attack_discovering_info.php" class="done-btn">Done</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="result">
                <h2>Your Score: <strong><?php echo $score; ?>/10</strong></h2>
                <p>Incorrect Question Numbers:</p>
                <ul>
                    <?php foreach ($_SESSION['incorrect_questions'] as $question => $answer): ?>
                        <li><?php echo ucfirst($question); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <form method="post">
                <?php
                $questions = [
                    "q1" => "What is the main goal of information gathering in penetration testing?",
                    "q2" => "Which tool is commonly used to discover devices on the same network?",
                    "q3" => "What does an IP range define in network scanning?",
                    "q4" => "What information can be gathered using Nmap?",
                    "q5" => "Which scanning method sends pings to detect live hosts?",
                    "q6" => "What is the purpose of using a wireless adapter for information gathering?",
                    "q7" => "What command is used in Netdiscover to scan a subnet?",
                    "q8" => "How can Nmap be used to identify open ports on a network?",
                    "q9" => "What does the command 'ssh root@192.168.1.12' attempt to do?",
                    "q10" => "What is the risk of using default SSH passwords on a jailbroken iPhone?"
                ];

                $options = [
                    "q1" => ["a" => "Immediate attack execution", "b" => "Collecting details about the target", "c" => "Bypassing firewalls", "d" => "Installing malware"],
                    "q2" => ["a" => "Netdiscover", "b" => "Wireshark", "c" => "Metasploit", "d" => "John the Ripper"],
                    "q3" => ["a" => "MAC address scope", "b" => "Router configurations", "c" => "Set of IPs to scan", "d" => "Firewall settings"],
                    "q4" => ["a" => "Only IP addresses", "b" => "MAC addresses only", "c" => "Hardware specifications", "d" => "Open ports and services"],
                    "q5" => ["a" => "Quick scan", "b" => "Ping scan", "c" => "Full scan", "d" => "Port scan"],
                    "q6" => ["a" => "To scan wireless networks", "b" => "To increase internet speed", "c" => "To enable MAC address spoofing", "d" => "To disable security features"],
                    "q7" => ["a" => "netdiscover -l", "b" => "netdiscover -m", "c" => "netdiscover -f", "d" => "netdiscover -r"],
                    "q8" => ["a" => "Using the -l flag", "b" => "By enabling SSH", "c" => "With the -p flag", "d" => "By running Netdiscover"],
                    "q9" => ["a" => "Scan for live hosts", "b" => "Connect to a remote system via SSH", "c" => "Perform a ping scan", "d" => "Check network speed"],
                    "q10" => ["a" => "Unauthorized access", "b" => "Faster network speed", "c" => "Improved security", "d" => "Reduced battery life"]
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

        <a href="attack_discovering_info_lec.php" class="nav-btn">Back</a>
    </div>
</body>
</html>
