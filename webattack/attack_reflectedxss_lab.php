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
        "q6" => "c",
        "q7" => "b",
        "q8" => "b",
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
    <title>Reflected XSS Lab</title>
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
            border: none;
            cursor: pointer;
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
        <h1>Reflected XSS Lab Questions</h1>

        <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <div class="result">
                <h2>Your Score: <strong><?php echo $score; ?>/8</strong></h2>
                <p>
                    <?php
                    if ($score == 8) {
                        echo "Excellent! You have a strong understanding of Reflected XSS.";
                    } elseif ($score >= 5) {
                        echo "Good job! You understand most of the concepts, but there's room for improvement.";
                    } else {
                        echo "Keep learning! Review the lecture to improve your understanding.";
                    }
                    ?>
                </p>
                <a href="attack_reflectedxss_lab.php" class="nav-btn">Try Again</a>
            </div>
        <?php else: ?>
            <form method="post">
                <!-- Question 1 -->
                <div class="question">
                    <h3>1. What is Reflected XSS?</h3>
                    <div class="options">
                        <label><input type="radio" name="q1" value="a"> A vulnerability where malicious scripts are stored on the server.</label>
                        <label><input type="radio" name="q1" value="b"> A vulnerability where malicious scripts are reflected back to the user's browser from the server.</label>
                        <label><input type="radio" name="q1" value="c"> A vulnerability where malicious scripts are executed only on the server.</label>
                        <label><input type="radio" name="q1" value="d"> A vulnerability where malicious scripts are stored in the browser's cache.</label>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="question">
                    <h3>2. How does Reflected XSS differ from Stored XSS?</h3>
                    <div class="options">
                        <label><input type="radio" name="q2" value="a"> Reflected XSS is stored on the server, while Stored XSS is not.</label>
                        <label><input type="radio" name="q2" value="b"> Reflected XSS is not stored on the server, while Stored XSS is.</label>
                        <label><input type="radio" name="q2" value="c"> Reflected XSS only affects the server, while Stored XSS affects the client.</label>
                        <label><input type="radio" name="q2" value="d"> Reflected XSS is less dangerous than Stored XSS.</label>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="question">
                    <h3>3. What is a common way for attackers to exploit Reflected XSS?</h3>
                    <div class="options">
                        <label><input type="radio" name="q3" value="a"> By injecting malicious scripts into the server's database.</label>
                        <label><input type="radio" name="q3" value="b"> By crafting malicious URLs or input that is reflected back to the user.</label>
                        <label><input type="radio" name="q3" value="c"> By exploiting vulnerabilities in the browser's cache.</label>
                        <label><input type="radio" name="q3" value="d"> By physically stealing user devices.</label>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="question">
                    <h3>4. Which of the following is NOT a potential impact of Reflected XSS?</h3>
                    <div class="options">
                        <label><input type="radio" name="q4" value="a"> Stealing session cookies or credentials.</label>
                        <label><input type="radio" name="q4" value="b"> Redirecting users to malicious websites.</label>
                        <label><input type="radio" name="q4" value="c"> Encrypting user data on the server.</label>
                        <label><input type="radio" name="q4" value="d"> Performing actions on behalf of the user.</label>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="question">
                    <h3>5. Which function in PHP can be used to prevent Reflected XSS by escaping HTML characters?</h3>
                    <div class="options">
                        <label><input type="radio" name="q5" value="a"> `htmlspecialchars()`.</label>
                        <label><input type="radio" name="q5" value="b"> `mysql_real_escape_string()`.</label>
                        <label><input type="radio" name="q5" value="c"> `urlencode()`.</label>
                        <label><input type="radio" name="q5" value="d"> `base64_encode()`.</label>
                    </div>
                </div>

                <!-- Question 6 -->
                <div class="question">
                    <h3>6. Which of the following is NOT a best practice to prevent Reflected XSS?</h3>
                    <div class="options">
                        <label><input type="radio" name="q6" value="a"> Validating and sanitizing user input.</label>
                        <label><input type="radio" name="q6" value="b"> Using output escaping for HTML contexts.</label>
                        <label><input type="radio" name="q6" value="c"> Allowing users to input raw HTML and JavaScript.</label>
                        <label><input type="radio" name="q6" value="d"> Applying context-specific encoding for URLs and JavaScript.</label>
                    </div>
                </div>

                <!-- Question 7 -->
                <div class="question">
                    <h3>7. In a Reflected XSS attack, where is the malicious script typically executed?</h3>
                    <div class="options">
                        <label><input type="radio" name="q7" value="a"> On the server.</label>
                        <label><input type="radio" name="q7" value="b"> In the user's browser.</label>
                        <label><input type="radio" name="q7" value="c"> In the browser's local storage.</label>
                        <label><input type="radio" name="q7" value="d"> In the website's CSS files.</label>
                    </div>
                </div>

                <!-- Question 8 -->
                <div class="question">
                    <h3>8. Which of the following tools or techniques can help detect Reflected XSS vulnerabilities?</h3>
                    <div class="options">
                        <label><input type="radio" name="q8" value="a"> Network packet sniffing.</label>
                        <label><input type="radio" name="q8" value="b"> Static code analysis tools.</label>
                        <label><input type="radio" name="q8" value="c"> DNS spoofing.</label>
                        <label><input type="radio" name="q8" value="d"> Firewall configuration.</label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Submit</button>
                <a href="attack_reflectedxss_lec.php" class="nav-btn">Back </a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
