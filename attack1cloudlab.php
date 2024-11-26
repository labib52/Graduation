<?php
// Process form submission and calculate score
$score = 0;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Correct answers
    $correctAnswers = [
        "q1" => "b",
        "q2" => "a",
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
    <title>Cloud Lab</title>
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
        <h1>Cloud Lab Questions</h1>

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
                <a href="attack1cloudlab.php" class="nav-btn">Try Again</a>
            </div>
        <?php else: ?>
            <form method="post">
                <!-- Question 1 -->
                <div class="question">
                    <h3>1. What is a cloud attack?</h3>
                    <div class="options">
                        <label><input type="radio" name="q1" value="a"> A hardware failure in a data center.</label>
                        <label><input type="radio" name="q1" value="b"> An attempt to exploit vulnerabilities in cloud systems.</label>
                        <label><input type="radio" name="q1" value="c"> A natural disaster affecting cloud services.</label>
                        <label><input type="radio" name="q1" value="d"> A process to optimize cloud performance.</label>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="question">
                    <h3>2. What is account hijacking in the context of cloud security?</h3>
                    <div class="options">
                        <label><input type="radio" name="q2" value="a"> Unauthorized access to user accounts.</label>
                        <label><input type="radio" name="q2" value="b"> Stealing passwords from hardware devices.</label>
                        <label><input type="radio" name="q2" value="c"> Disabling encryption on cloud storage.</label>
                        <label><input type="radio" name="q2" value="d"> A lawful data access request.</label>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="question">
                    <h3>3. Which of the following best describes rogue cloud providers?</h3>
                    <div class="options">
                        <label><input type="radio" name="q3" value="a"> Unauthorized cloud applications installed by users.</label>
                        <label><input type="radio" name="q3" value="b"> Fraudulent providers offering fake cloud services to collect sensitive data..</label>
                        <label><input type="radio" name="q3" value="c"> Legal cloud providers with poor customer service.</label>
                        <label><input type="radio" name="q3" value="d"> Overpriced cloud services.</label>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="question">
                    <h3>4. What is the role of a VPN in cloud security?</h3>
                    <div class="options">
                        <label><input type="radio" name="q4" value="a"> To ensure only encrypted communication between users and cloud services.</label>
                        <label><input type="radio" name="q4" value="b"> To disable unauthorized apps on the cloud.</label>
                        <label><input type="radio" name="q4" value="c"> To enforce stricter access controls.</label>
                        <label><input type="radio" name="q4" value="d"> To identify potential phishing attempts.</label>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="question">
                    <h3>5. Which of the following is a common sign of a cloud attack?</h3>
                    <div class="options">
                        <label><input type="radio" name="q5" value="a"> Faster cloud service performance.</label>
                        <label><input type="radio" name="q5" value="b"> Unauthorized access attempts or unusual activity logs.</label>
                        <label><input type="radio" name="q5" value="c"> Increased user satisfaction with cloud services.</label>
                        <label><input type="radio" name="q5" value="d"> Lower resource usage than expected.</label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Submit</button>
                <a href="attack1cloudlec.php" class="nav-btn">Back to Lecture</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>


