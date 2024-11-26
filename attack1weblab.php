<?php
// Process form submission and calculate score
$score = 0;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Correct answers
    $correctAnswers = [
        "q1" => "b",
        "q2" => "b",
        "q3" => "b",
        "q4" => "c",
        "q5" => "a",
    ];

    // Check user answers
    foreach ($correctAnswers as $question => $correctAnswer) {
        if (isset($_POST[$question]) && $_POST[$question] == $correctAnswer) {
            $score++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MITM Lab</title>
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

        .options {
            margin: 10px 0 20px;
        }

        .options label {
            display: block;
            margin-bottom: 10px;
        }

        .nav-btn, .submit-btn {
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

        .nav-btn:hover, .submit-btn:hover {
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Man In The Middle Lab Questions</h1>

        <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <div class="result">
                <h2>Your Score: <strong><?php echo $score; ?>/5</strong></h2>
                <p>
                    <?php
                    if ($score == 5) {
                        echo "Excellent! You have a strong understanding of phishing.";
                    } elseif ($score >= 3) {
                        echo "Good job! You understand most of the concepts, but there's room for improvement.";
                    } else {
                        echo "Keep learning! Review the lecture to improve your understanding.";
                    }
                    ?>
                </p>
                <a href="attack1weblab.php" class="nav-btn">Try Again</a>
            </div>
        <?php else: ?>
            <form method="post">
                <!-- Question 1 -->
                <div class="question">
                    <h3>1. What is a Man-in-the-Middle (MITM) attack?</h3>
                    <div class="options">
                        <label><input type="radio" name="q1" value="a"> A direct denial of service attack on a network.</label>
                        <label><input type="radio" name="q1" value="b"> An attack where the attacker secretly intercepts and manipulates communication between two parties.</label>
                        <label><input type="radio" name="q1" value="c"> An attack involving physical theft of devices.</label>
                        <label><input type="radio" name="q1" value="d"> A type of ransomware attack.</label>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="question">
                    <h3>2. What is SSL stripping?</h3>
                    <div class="options">
                        <label><input type="radio" name="q2" value="a"> Upgrading HTTP connections to HTTPS.</label>
                        <label><input type="radio" name="q2" value="b"> Downgrading HTTPS connections to HTTP to expose sensitive data.</label>
                        <label><input type="radio" name="q2" value="c"> Encrypting network communication.</label>
                        <label><input type="radio" name="q2" value="d"> Spoofing a DNS query.</label>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="question">
                    <h3>3. What is a potential sign of a MITM attack?</h3>
                    <div class="options">
                        <label><input type="radio" name="q3" value="a"> Unusually fast network performance.</label>
                        <label><input type="radio" name="q3" value="b"> Receiving certificate warnings when accessing secure sites..</label>
                        <label><input type="radio" name="q3" value="c"> Seamless connection to public Wi-Fi.</label>
                        <label><input type="radio" name="q3" value="d"> Auto-saving of login credentials..</label>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="question">
                    <h3>4. Which of the following is NOT an effective way to prevent MITM attacks?</h3>
                    <div class="options">
                        <label><input type="radio" name="q4" value="a"> Regularly updating software and firmware.</label>
                        <label><input type="radio" name="q4" value="b"> Avoiding sensitive transactions on public Wi-Fi."</label>
                        <label><input type="radio" name="q4" value="c"> Clicking on all certificate warnings to bypass them.</label>
                        <label><input type="radio" name="q4" value="d"> SUsing intrusion detection systems (IDS).</label>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="question">
                    <h3>5. Why is monitoring access logs important in detecting MITM attacks?</h3>
                    <div class="options">
                        <label><input type="radio" name="q5" value="a">  It can reveal unauthorized access or unusual communication patterns.</label>
                        <label><input type="radio" name="q5" value="b"> It disables potential attackers’ connections.</label>
                        <label><input type="radio" name="q5" value="c"> It ensures data is encrypted during transit.</label>
                        <label><input type="radio" name="q5" value="d">  It removes all unencrypted traffic.</label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Submit</button>
                <a href="attack1weblec.php" class="nav-btn">Back to Lecture</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>


