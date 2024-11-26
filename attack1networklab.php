<?php
// Process form submission and calculate score
$score = 0;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Correct answers
    $correctAnswers = [
        "q1" => "b",
        "q2" => "c",
        "q3" => "b",
        "q4" => "a",
        "q5" => "b",
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
        <h1>Port Scanning Lab Questions</h1>

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
                    <h3>1. What is port scanning?</h3>
                    <div class="options">
                        <label><input type="radio" name="q1" value="a"> The process of closing unused network ports.</label>
                        <label><input type="radio" name="q1" value="b"> A technique used to identify open ports on a target system.</label>
                        <label><input type="radio" name="q1" value="c"> A method for improving network speed</label>
                        <label><input type="radio" name="q1" value="d"> A strategy to protect against malware.</label>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="question">
                    <h3>2. Which of the following is NOT a type of port scanning technique?</h3>
                    <div class="options">
                        <label><input type="radio" name="q2" value="a"> SYN Scan.</label>
                        <label><input type="radio" name="q2" value="b"> ACK Scan.</label>
                        <label><input type="radio" name="q2" value="c"> DNS Scan.</label>
                        <label><input type="radio" name="q2" value="d"> UDP Scan.</label>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="question">
                    <h3>3. What role do honeypots play in detecting port scans?</h3>
                    <div class="options">
                        <label><input type="radio" name="q3" value="a"> They automatically encrypt scanned data.</label>
                        <label><input type="radio" name="q3" value="b"> They provide fake services to attract and analyze malicious traffic.</label>
                        <label><input type="radio" name="q3" value="c"> They filter out all incoming traffic.</label>
                        <label><input type="radio" name="q3" value="d"> They provide real-time alerts for successful login attempts.</label>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="question">
                    <h3>4. What is the benefit of using port knocking as a defense against port scanning?</h3>
                    <div class="options">
                        <label><input type="radio" name="q4" value="a"> It hides open ports by requiring a secret sequence of network requests before the ports are revealed.</label>
                        <label><input type="radio" name="q4" value="b">It blocks all traffic from scanning IP addresses.</label>
                        <label><input type="radio" name="q4" value="c"> It automatically closes ports after scanning is completed.</label>
                        <label><input type="radio" name="q4" value="d"> It sends a warning message to the attacker.</label>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="question">
                    <h3>5. How can an IDS (Intrusion Detection System) help detect port scanning?</h3>
                    <div class="options">
                        <label><input type="radio" name="q5" value="a"> It automatically blocks port scanning attempts.</label>
                        <label><input type="radio" name="q5" value="b">  It looks for suspicious patterns in network traffic, such as frequent requests to different ports.</label>
                        <label><input type="radio" name="q5" value="c"> It logs all open ports in real time.</label>
                        <label><input type="radio" name="q5" value="d"> It encrypts the data to prevent scanning.</label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Submit</button>
                <a href="attack1networklec.php" class="nav-btn">Back to Lecture</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>


