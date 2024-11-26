<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Understanding Phishing: An Overview</title>
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
            line-height: 1.6;
            padding: 20px;
        }

        h1, h2 {
            color: #007BFF;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 1.5rem;
            margin-top: 20px;
        }

        p {
            margin: 10px 0;
        }

        ul {
            margin: 10px 0 10px 20px;
        }

        ul li {
            margin: 5px 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .nav-btn {
            display: flex;
            align-items: center;
            justify-content: center;
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

        .nav-btn:hover {
            background-color: #0056b3;
        }

        .nav-btn span {
            margin: 0 5px;
        }

        .arrow {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .arrow-back {
            margin-right: 10px;
        }

        .arrow-next {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Understanding Broken Authentication: An Overview</h1>
        <p>
        Broken authentication is a critical vulnerability that occurs when the mechanisms responsible for verifying the identity of users fail. This vulnerability allows attackers to impersonate legitimate users, often leading to unauthorized access, data theft, or system compromise. It is listed as one of the top security risks by the OWASP (Open Web Application Security Project).
        </p>

        <h2>How Broken authentication Happens:</h2>
        <p>
        Broken authentication refers to flaws in the authentication process that attackers exploit to gain unauthorized access to systems, services, or sensitive information.
        </p>
        <ul>
            <li><strong>Weak or predictable passwords.</strong> </li>
            <li><strong>Poorly implemented session management.</strong> </li>
            <li><strong>Lack of multi-factor authentication (MFA).</strong> </li>
            <li><strong>Insecure storage or transmission of credentials.</strong></li>
        </ul>

        <h2>Types of Broken Authentication Attacks</h2>
        <ol>
            <li><strong>Credential Stuffing: </strong> Attackers use stolen username-password pairs from data breaches to attempt logins on multiple platforms.</li>
            <li><strong>Brute Force Attacks: </strong> Automated tools are used to guess passwords until the correct one is found.</li>
            <li><strong>Insecure Password Reset Mechanisms: </strong> Weak reset questions or unsecured reset links can allow attackers to reset user credentials.</li>
            <li><strong>Improper Logout or Session Expiry:</strong> Failure to end user sessions properly allows attackers to reuse session tokens.</li>
        </ol>
        <h2>Consequences of Broken Authentication</h2>
        <ul>
            <li><strong>Unauthorized Access:</strong> Attackers can impersonate users and gain access to sensitive systems or data.</li>
            <li><strong>Data Theft:</strong> Exposed credentials can lead to large-scale breaches.</li>
            <li><strong>Reputation Damage:</strong> Organizations lose customer trust due to compromised security.</li>
            <li><strong>Financial Loss:</strong> Companies may face fines, lawsuits, and operational disruptions..</li>
        </ul>
<!-- Dynamically display the GIF using PHP -->
<?php
    $gifPath = "ba.gif"; // Replace with the path to your GIF
    echo "<img src='$gifPath' alt='Animated GIF' style='width: 300px; height: auto;'>";
    ?>
        <h2>Conclusion</h2>
        <p>
        Broken authentication remains a significant threat in cybersecurity. By understanding its causes and employing robust mitigation strategies, organizations can protect their systems and users from devastating attacks. A combination of strong policies, secure development practices, and regular audits is essential to combat this vulnerability.
        </p>

        <div class="navigation">
            <a href="api.php" class="nav-btn">
                <span class="arrow arrow-back">&larr;</span>
                <span>Back</span>
            </a>
            <a href="attack1apilab.php" class="nav-btn">
                <span>Next</span>
                <span class="arrow arrow-next">&rarr;</span>
            </a>
        </div>
    </div>
</body>
</html>


