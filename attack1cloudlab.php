<?php
// Process form submission and calculate score
$score = 0;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Correct answers
    $correctAnswers = [
        "q1" => "b",
        "q2" => "c",
        "q3" => "a",
        "q4" => "a",
        "q5" => "c",
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
    <title>DoS Lab</title>
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
        <h1>DoS Lab Questions</h1>

        <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <div class="result">
                <h2>Your Score: <strong><?php echo $score; ?>/5</strong></h2>
                <p>
                    <?php
                    if ($score == 5) {
                        echo "Excellent! You have a strong understanding of cloud.";
                    } elseif ($score >= 3) {
                        echo "Good job! You understand most of the concepts, but there's room for improvement.";
                    } else {
                        echo "Keep learning! Review the lecture to improve your understanding.";
                    }
                    ?>
                </p>
                <a href="attack1cloudlab.php" class="nav-btn">Try Again</a>
            </div>
        <?php else: ?>
            <form method="post">
                <!-- Question 1 -->
                <div class="question">
                    <h3>1. What is the primary goal of a DoS attack?</h3>
                    <div class="options">
                        <label><input type="radio" name="q1" value="a"> To improve server performance.</label>
                        <label><input type="radio" name="q1" value="b"> To disrupt services by overwhelming resources.</label>
                        <label><input type="radio" name="q1" value="c"> To steal sensitive data.</label>
                        <label><input type="radio" name="q1" value="d"> To encrypt user data.</label>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="question">
                    <h3>2. What is one sign of a DoS attack on a target server?</h3>
                    <div class="options">
                        <label><input type="radio" name="q2" value="a"> Reduced CPU usage.</label>
                        <label><input type="radio" name="q2" value="b"> Increased availability.</label>
                        <label><input type="radio" name="q2" value="c"> Unusually high network traffic and resource usage.</label>
                        <label><input type="radio" name="q2" value="d"> Decreased number of active connections.</label>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="question">
                    <h3>3. What is a SYN flood?</h3>
                    <div class="options">
                        <label><input type="radio" name="q3" value="a"> An attack that floods the target with SYN packets, exhausting its resources.</label>
                        <label><input type="radio" name="q3" value="b"> An attack that uses encrypted messages to crash the server.</label>
                        <label><input type="radio" name="q3" value="c"> An attack that slows down internet speed.</label>
                        <label><input type="radio" name="q3" value="d"> An attack that manipulates DNS records.</label>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="question">
                    <h3>4. How can firewalls help mitigate DoS attacks?</h3>
                    <div class="options">
                        <label><input type="radio" name="q4" value="a"> By blocking malicious traffic based on patterns or rules.</label>
                        <label><input type="radio" name="q4" value="b"> By increasing the speed of legitimate connections.</label>
                        <label><input type="radio" name="q4" value="c"> By restarting the server automatically during attacks.</label>
                        <label><input type="radio" name="q4" value="d"> By disabling all incoming traffic permanently.</label>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="question">
                    <h3>5. Which of the following is NOT a recommended mitigation technique?</h3>
                    <div class="options">
                        <label><input type="radio" name="q5" value="a"> Using rate limiting.</label>
                        <label><input type="radio" name="q5" value="b"> Implementing intrusion detection systems.</label>
                        <label><input type="radio" name="q5" value="c"> Keeping software unpatched.</label>
                        <label><input type="radio" name="q5" value="d"> Using DDoS protection services.</label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Submit</button>
                <a href="attack1cloudlec.php" class="nav-btn">Back to Lecture</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>


