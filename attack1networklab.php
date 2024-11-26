<?php
// Process form submission and calculate score
$score = 0;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Correct answers
    $correctAnswers = [
        "q1" => "a",
        "q2" => "c",
        "q3" => "c",
        "q4" => "b",
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
    <title>Network Lab</title>
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
        <h1>Network Lab Questions</h1>

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
                <a href="attack1networklab.php" class="nav-btn">Try Again</a>
            </div>
        <?php else: ?>
            <form method="post">
                <!-- Question 1 -->
                <div class="question">
                    <h3>1. What is a network attack?</h3>
                    <div class="options">
                        <label><input type="radio" name="q1" value="a"> An attempt to disrupt, access, or exploit a computer network or its resources..</label>
                        <label><input type="radio" name="q1" value="b"> A method of securely transmitting data across networks.</label>
                        <label><input type="radio" name="q1" value="c"> A way to encrypt sensitive information during transit.</label>
                        <label><input type="radio" name="q1" value="d"> A strategy for improving network speed..</label>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="question">
                    <h3>2. What is the goal of a Denial of Service (DoS) attack?</h3>
                    <div class="options">
                        <label><input type="radio" name="q2" value="a"> To access sensitive data from a database.</label>
                        <label><input type="radio" name="q2" value="b"> To steal credentials during a network session.</label>
                        <label><input type="radio" name="q2" value="c"> To overload a network or server, making it unavailable to users.</label>
                        <label><input type="radio" name="q2" value="d"> To inject malicious scripts into trusted websites.</label>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="question">
                    <h3>3. What is the primary target of a SQL injection attack?</h3>
                    <div class="options">
                        <label><input type="radio" name="q3" value="a"> A network router.</label>
                        <label><input type="radio" name="q3" value="b"> A user’s browser session.</label>
                        <label><input type="radio" name="q3" value="c"> A database query.</label>
                        <label><input type="radio" name="q3" value="d"> An unencrypted Wi-Fi connection.</label>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="question">
                    <h3>4. Which of the following is a common sign of a network attack?</h3>
                    <div class="options">
                        <label><input type="radio" name="q4" value="a"> Faster-than-usual network performance.</label>
                        <label><input type="radio" name="q4" value="b">Unauthorized access attempts or login failures.</label>
                        <label><input type="radio" name="q4" value="c"> Increased employee awareness about phishing emails.</label>
                        <label><input type="radio" name="q4" value="d"> Reduced volume of outgoing traffic.</label>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="question">
                    <h3>5. Which of the following is a recommended practice for protecting against network attacks?</h3>
                    <div class="options">
                        <label><input type="radio" name="q5" value="a"> Keeping software and systems up to date.</label>
                        <label><input type="radio" name="q5" value="b"> Avoiding the use of encryption for sensitive data.</label>
                        <label><input type="radio" name="q5" value="c"> Sharing passwords with trusted team members via email.</label>
                        <label><input type="radio" name="q5" value="d"> Disabling firewalls to reduce latency.</label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Submit</button>
                <a href="attack1networklec.php" class="nav-btn">Back to Lecture</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>


