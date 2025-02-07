<?php
session_start();

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";

// Track user attempts and completion
$labId = 'bettercap_basics_lab'; // Unique identifier for this lab
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
    "q1" => "a",
    "q2" => "b",
    "q3" => "c",
    "q4" => "d",
    "q5" => "b",
    "q6" => "c",
    "q7" => "d",
    "q8" => "a",
    "q9" => "c",
    "q10" => "d",
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
    <title>BetterCap Basics - Lab</title>
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
        <h1>BetterCap Basics - Lab Questions</h1>

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
                    <a href="attack_mitm_bettercap_basics.php" class="done-btn">Done</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <form method="post">
                <?php
                $questions = [
                    "q1" => "What is the main purpose of BetterCap?",
                    "q2" => "How can you specify a network interface for BetterCap?",
                    "q3" => "Which BetterCap module discovers devices on a network?",
                    "q4" => "What does the net.probe module do?",
                    "q5" => "How does BetterCap identify device manufacturers?",
                    "q6" => "Which command enables the net.probe module?",
                    "q7" => "What happens when the net.probe module is enabled?",
                    "q8" => "How can you see all connected clients in BetterCap?",
                    "q9" => "What is the purpose of net.recon in BetterCap?",
                    "q10" => "Which attack can be launched using BetterCap?"
                ];

                $options = [
                    "q1" => ["a" => "Intercept and analyze network traffic", "b" => "Encrypt traffic", "c" => "Assign IP addresses", "d" => "Manage firewalls"],
                    "q2" => ["a" => "Use the -net flag", "b" => "Use the -iface flag", "c" => "Use the -netiface flag", "d" => "No need to specify"],
                    "q3" => ["a" => "dns.spoof", "b" => "net.probe", "c" => "events.stream", "d" => "hsts.bypass"],
                    "q4" => ["a" => "Captures network packets", "b" => "Sends UDP packets to discover devices", "c" => "Forwards packets", "d" => "Manages ARP cache"],
                    "q5" => ["a" => "Using ARP cache", "b" => "Using vendor MAC lookup", "c" => "Using IP addresses", "d" => "Using DNS spoofing"],
                    "q6" => ["a" => "enable net.probe", "b" => "start net.probe", "c" => "net.probe on", "d" => "activate net.probe"],
                    "q7" => ["a" => "Traffic analysis begins", "b" => "ARP cache updates", "c" => "Devices respond to probes", "d" => "Network interface resets"],
                    "q8" => ["a" => "net.show", "b" => "net.view", "c" => "net.clients", "d" => "net.dot show"],
                    "q9" => ["a" => "Intercepts HTTP traffic", "b" => "Detects probe responses", "c" => "Manages DNS requests", "d" => "Captures MAC addresses"],
                    "q10" => ["a" => "DNS spoofing", "b" => "HSTS bypass", "c" => "ARP spoofing", "d" => "All of the above"]
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

        <a href="attack_mitm_bettercap_basics_lec.php" class="nav-btn">Back</a>
    </div>
</body>
</html>
