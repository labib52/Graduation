<?php
// Process form submission and calculate score
$score = 0;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Correct answers
    $correctAnswers = [
        "q1" => "b",
        "q2" => "b",
        "q3" => "b",
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
    <title>WEP/WPA Lab</title>
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
        <h1>WEP/WPA Lab Questions</h1>

        <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <div class="result">
                <h2>Your Score: <strong><?php echo $score; ?>/5</strong></h2>
                <p>
                    <?php
                    if ($score == 5) {
                        echo "Excellent! You have a strong understanding of web.";
                    } elseif ($score >= 3) {
                        echo "Good job! You understand most of the concepts, but there's room for improvement.";
                    } else {
                        echo "Keep learning! Review the lecture to improve your understanding.";
                    }
                    ?>
                </p>
                <a href="attack1wirelesslab.php" class="nav-btn">Try Again</a>
            </div>
        <?php else: ?>
            <form method="post">
                <!-- Question 1 -->
                <div class="question">
                    <h3>1. What does WEP stand for in wireless security?</h3>
                    <div class="options">
                        <label><input type="radio" name="q1" value="a"> Wi-Fi Encryption Protocol</label>
                        <label><input type="radio" name="q1" value="b"> Wired Equivalent Privacy</label>
                        <label><input type="radio" name="q1" value="c"> Wireless Encryption Protocol</label>
                        <label><input type="radio" name="q1" value="d"> Web Encryption Policy</label>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="question">
                    <h3>2. Which of the following is a primary security weakness of WEP?</h3>
                    <div class="options">
                        <label><input type="radio" name="q2" value="a"> Strong password management.</label>
                        <label><input type="radio" name="q2" value="b"> Static encryption keys and weak IVs (Initialization Vectors).</label>
                        <label><input type="radio" name="q2" value="c"> Complex key exchange protocols.</label>
                        <label><input type="radio" name="q2" value="d"> Strong user authentication.</label>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="question">
                    <h3>3. Which feature distinguishes WPA from WEP in terms of key management?</h3>
                    <div class="options">
                        <label><input type="radio" name="q3" value="a"> WPA uses static keys while WEP uses dynamic keys</label>
                        <label><input type="radio" name="q3" value="b"> WPA uses dynamic keys, while WEP uses static keys</label>
                        <label><input type="radio" name="q3" value="c"> Both WPA and WEP use static keys</label>
                        <label><input type="radio" name="q3" value="d">  Both WPA and WEP use dynamic keys.</label>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="question">
                    <h3>4. Which of the following is a key difference between WEP and WPA encryption?</h3>
                    <div class="options">
                        <label><input type="radio" name="q4" value="a"> WPA uses more secure encryption algorithms like AES, while WEP uses weak RC4.</label>
                        <label><input type="radio" name="q4" value="b"> WEP uses dynamic key exchange while WPA uses static keys</label>
                        <label><input type="radio" name="q4" value="c"> WPA is more vulnerable to brute-force attacks than WEP</label>
                        <label><input type="radio" name="q4" value="d"> WEP uses stronger encryption and is more secure than WPA</label>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="question">
                    <h3>5. Which of the following is a recommended best practice to secure your wireless network?</h3>
                    <div class="options">
                        <label><input type="radio" name="q5" value="a"> Using WEP for encryption to ensure compatibility with old devices</label>
                        <label><input type="radio" name="q5" value="b"> Disabling WPA2 encryption for better performance</label>
                        <label><input type="radio" name="q5" value="c"> Using a strong, unique password for WPA2 encryption</label>
                        <label><input type="radio" name="q5" value="d"> Using WPA with RC4 encryption for all devices</label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Submit</button>
                <a href="attack1wirelesslec.php" class="nav-btn">Back to Lecture</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>


