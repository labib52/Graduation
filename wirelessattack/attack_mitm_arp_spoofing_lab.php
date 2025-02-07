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
    "q5" => "a",
    "q6" => "c",
    "q7" => "b",
    "q8" => "d",
    "q9" => "a",
    "q10" => "c",
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
    <title>ARP Spoofing - Lab</title>
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
        <h1>ARP Spoofing - Lab Questions</h1>

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
                    <a href="attack_mitm_arp_spoofing.php" class="done-btn">Done</a>
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
                    "q1" => "What is the main purpose of ARP spoofing?",
                    "q2" => "Which Bettercap module is used for ARP spoofing?",
                    "q3" => "How can you enable ARP spoofing in Bettercap?",
                    "q4" => "What option allows full duplex communication in ARP spoofing?",
                    "q5" => "How can you target specific IPs in ARP spoofing?",
                    "q6" => "What command shows running Bettercap modules?",
                    "q7" => "What command enables ARP spoofing in Bettercap?",
                    "q8" => "How does Bettercap confirm it is in the middle of the connection?",
                    "q9" => "Which network tool helps identify the target IP for ARP spoofing?",
                    "q10" => "What is the effect of spoofing the router and target?"
                ];

                $options = [
                    "q1" => ["a" => "To scan networks", "b" => "To intercept data", "c" => "To secure devices", "d" => "To disconnect devices"],
                    "q2" => ["a" => "net.probe", "b" => "arp.spoof", "c" => "dns.spoof", "d" => "http.proxy"],
                    "q3" => ["a" => "run arp.spoof", "b" => "arp.spoof on", "c" => "set arp.spoof", "d" => "enable arp.spoof"],
                    "q4" => ["a" => "arp.fullduplex", "b" => "set duplex", "c" => "arp.spoof.fullduplex", "d" => "full.spoof.duplex"],
                    "q5" => ["a" => "set targets", "b" => "set arp.spoof.targets", "c" => "target.enable", "d" => "ip.targets"],
                    "q6" => ["a" => "show modules", "b" => "modules list", "c" => "help modules", "d" => "help"],
                    "q7" => ["a" => "arp.spoof on", "b" => "run arp", "c" => "enable spoof", "d" => "start arp.spoof"],
                    "q8" => ["a" => "By sniffing packets", "b" => "By matching MAC addresses", "c" => "By scanning ports", "d" => "By pinging hosts"],
                    "q9" => ["a" => "Netdiscover", "b" => "Nmap", "c" => "Wireshark", "d" => "Traceroute"],
                    "q10" => ["a" => "Blocks traffic", "b" => "Intercepts all data", "c" => "Hides the router", "d" => "Enables encryption"]
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

        <a href="attack_mitm_arp_spoofing_lec.php" class="nav-btn">Back</a>
    </div>
</body>
</html>
