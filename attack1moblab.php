<?php
// Process form submission and calculate score
$score = 0;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Correct answers
    $correctAnswers = [
        "q1" => "b",
        "q2" => "b",
        "q3" => "c",
        "q4" => "b",
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
    <title>Smishing Attacks Lab</title>
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
        <h1>Smishing Attacks Lab Questions</h1>

        <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <div class="result">
                <h2>Your Score: <strong><?php echo $score; ?>/5</strong></h2>
                <p>
                    <?php
                    if ($score == 5) {
                        echo "Excellent! You have a strong understanding of mobile attack.";
                    } elseif ($score >= 3) {
                        echo "Good job! You understand most of the concepts, but there's room for improvement.";
                    } else {
                        echo "Keep learning! Review the lecture to improve your understanding.";
                    }
                    ?>
                </p>
                <a href="attack1moblab.php" class="nav-btn">Try Again</a>
            </div>
        <?php else: ?>
            <form method="post">
                <!-- Question 1 -->
                <div class="question">
                    <h3>1. What is SMS Phishing (Smishing)?</h3>
                    <div class="options">
                        <label><input type="radio" name="q1" value="a">A type of malware that targets mobile devices.</label>
                        <label><input type="radio" name="q1" value="b">An attack that uses text messages to steal sensitive information or install malware.</label>
                        <label><input type="radio" name="q1" value="c"> A form of email phishing using mobile apps.</label>
                        <label><input type="radio" name="q1" value="d"> A legitimate way to promote products via SMS.</label>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="question">
                    <h3>2. How do attackers generally impersonate trusted entities in a smishing attack?</h3>
                    <div class="options">
                        <label><input type="radio" name="q2" value="a">By sending phishing emails from fake email addresses.</label>
                        <label><input type="radio" name="q2" value="b"> By spoofing phone numbers and using them to send SMS messages.</label>
                        <label><input type="radio" name="q2" value="c"> By creating fake websites that mimic legitimate companies.</label>
                        <label><input type="radio" name="q2" value="d"> By calling the target directly and asking for personal information.</label>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="question">
                    <h3>3. Which of the following is a common indicator that an SMS message might be a smishing attempt?</h3>
                    <div class="options">
                        <label><input type="radio" name="q3" value="a"> The message comes from a phone number you recognize.</label>
                        <label><input type="radio" name="q3" value="b"> The message contains a link that you were expecting.</label>
                        <label><input type="radio" name="q3" value="c"> The message uses threatening language, such as a claim that your bank account will be frozen unless you act immediately.</label>
                        <label><input type="radio" name="q3" value="d"> The message includes a personal greeting and asks for a follow-up call.</label>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="question">
                    <h3>4. How can updating your device’s operating system help protect against smishing?</h3>
                    <div class="options">
                        <label><input type="radio" name="q4" value="a"> It can make your device completely immune to all types of phishing attacks.</label>
                        <label><input type="radio" name="q4" value="b"> It ensures that security vulnerabilities are patched, reducing the risk of malware installation.</label>
                        <label><input type="radio" name="q4" value="c"> It helps in identifying suspicious phone numbers.</label>
                        <label><input type="radio" name="q4" value="d"> It makes your phone less likely to receive messages.
                        </label>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="question">
                    <h3>5. What is the best way to verify the legitimacy of a suspicious SMS message?</h3>
                    <div class="options">
                        <label><input type="radio" name="q5" value="a"> Responding to the text with your personal information.</label>
                        <label><input type="radio" name="q5" value="b"> Visiting the URL provided in the message.</label>
                        <label><input type="radio" name="q5" value="c"> Contacting the company directly using a trusted phone number or website to verify the message.</label>
                        <label><input type="radio" name="q5" value="d"> Forwarding the message to a friend for advice.</label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Submit</button>
                <a href="attack1moblec.php" class="nav-btn">Back to Lecture</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>


