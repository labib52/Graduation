<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Understanding Man-in-the-Middle (MITM) Attacks: An Overview</title>
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
        <h1>Understanding Man-in-the-Middle (MITM) Attacks: An Overview</h1>
        <p>A Man-in-the-Middle (MITM) attack is a type of cyberattack where the attacker secretly intercepts and potentially alters the communication between two parties without their knowledge. The goal is often to steal sensitive information, manipulate communication, or disrupt normal data exchange. MITM attacks can target various types of communication, including web sessions, emails, or even real-time messaging applications.</p>

        <h2>How Man-in-the-Middle Attacks Work</h2>
        <p>MITM attacks exploit vulnerabilities in network communication or authentication processes. The attacker inserts themselves between the victim and the intended recipient, masquerading as a trusted entity.</p>
        <ul>
            <li><strong>Interception: </strong> The attacker intercepts communication between two parties by compromising a network or using malicious software. <ul>This interception is often achieved through:
        <li><strong>Rogue Wi-Fi hotspots:</strong> Fake public Wi-Fi access points that redirect user traffic through the attacker’s device.</li>
    <li><strong>Packet sniffing: </strong>Using tools to capture data packets sent over unsecured networks.</li></ul></li>


            <li><strong>Decryption: </strong> If the communication is encrypted, the attacker attempts to decrypt it using stolen credentials, session cookies, or compromised certificates.
            Common methods include:
            <ul><li><strong>SSL stripping: </strong> Downgrading a secure HTTPS connection to an unencrypted HTTP connection.</li>
                <li><strong>Certificate spoofing:</strong>Presenting a fake SSL certificate to the victim's browser.</li>
            </ul>
            </li>


            <li><strong>Manipulation or Theft</strong> The attacker reads, modifies, or injects malicious content into the intercepted communication.
            Possible outcomes include:
        <ul><li>Theft of login credentials or financial information.</li>
        <li>Injection of malicious payloads into legitimate communication.</li>
        <li>Disruption of communication between the two parties.</li>
        </ul>
        </li>
        </ul>

        <h2>Types of MITM Attacks</h2>
        <ol>
            <li><strong>Wi-Fi Eavesdropping: </strong> The attacker sets up a fake Wi-Fi hotspot to capture traffic from connected devices.(Eg. A victim connects to "Free Coffee Shop Wi-Fi," unknowingly routed through the attacker’s system.)</li>
            <li><strong>Session Hijacking: </strong> The attacker steals session cookies from a victim’s browser to impersonate them and gain access to their accounts.</li>
            <li><strong>Email Hijacking: </strong> Cybercriminals compromise email accounts to intercept communication, often redirecting payments or sensitive information.</li>
            <li><strong>DNS Spoofing: </strong> The attacker redirects the victim’s DNS queries to a malicious site that mimics a legitimate one. (Eg. Redirecting traffic from "www.bank.com" to a fake website designed to steal login credentials.)</li>
        <li><strong>HTTPS Spoofing (SSL Stripping): </strong> The attacker downgrades a secure HTTPS connection to HTTP, exposing sensitive data to interception.</li>
        </ol>

        <h2>Recognizing Signs of MITM Attacks</h2>
           <ul>
            <li><strong>Unusual Certificate Warnings: </strong> Receiving warnings about invalid or untrusted SSL certificates when visiting a secure site.</li>
            <li><strong>Slower Network Performance:</strong> The communication delay caused by routing through the attacker’s system.</li>
            <li><strong>Mismatched URLs: </strong> Being redirected to a URL that looks suspicious or does not match the intended website.</li>
            <li><strong>Unexpected Login Prompts: </strong>Repeated login prompts for accounts you are already signed into.</li>
        </ul>
        <h2>How to Protect Against MITM Attacks</h2>
        <ol>
            <li><strong>Use Encrypted Communication:</strong> <ul><li>Always connect to websites using HTTPS.</li><li>Use secure protocols like SSH, VPNs, or TLS for sensitive communication.</li></ul></li>
            <li><strong>Avoid Public Wi-Fi: </strong> <ul><li>Avoid accessing sensitive information or logging into accounts over public Wi-Fi.</li><li>Use a Virtual Private Network (VPN) for encryption if you must use public Wi-Fi.</li></ul></li>
            <li><strong>Enable Multi-Factor Authentication (MFA): </strong> <ul><li>Add an extra layer of security to prevent account compromise even if credentials are stolen.</li></ul></li>
            <li><strong>Verify Certificates: </strong> <ul><li>Check for secure lock icons and certificate validity when visiting websites.</li></ul></li>
            <li><strong>Secure Your Network: </strong> <ul><li>Use WPA3 encryption for personal and corporate Wi-Fi networks.</li><li>Regularly update router firmware to patch vulnerabilities.</li></ul></li>
            <li><strong>Monitor Unusual Activity: </strong> <ul><li>Use intrusion detection systems (IDS) and security monitoring tools to detect anomalies.</li></ul></li>
        </ol>

        <h2>Consequences of MITM Attacks</h2>
        <ul>
            <li><strong>Data Theft: </strong> Exposure of personal, financial, or corporate information.</li>
            <li><strong>Financial Loss: </strong> Theft of funds from compromised bank accounts or fraudulent transactions.</li>
            <li><strong>Reputation Damage: </strong> For businesses, the fallout of customer data breaches or compromised communications can damage trust.</li>
            <li><strong>Service Disruption: </strong> Altered or disrupted communication can impact business operations.</li>
        </ul>
 <!-- Dynamically display the GIF using PHP -->
 <?php
    $gifPath = "mitm.gif"; // Replace with the path to your GIF
    echo "<img src='$gifPath' alt='Animated GIF'>";
    ?>
        <h2>Conclusion</h2>
        <p>
        Man-in-the-Middle attacks are among the most dangerous forms of cyberattacks due to their stealth and potential impact. Recognizing the signs, implementing strong security practices, and maintaining vigilance can significantly reduce the risk of falling victim to these attacks. Security tools, encryption protocols, and user education are critical components in mitigating the threat of MITM attacks.
        </p>
       

        <div class="navigation">
            <a href="web.php" class="nav-btn">
                <span class="arrow arrow-back">&larr;</span>
                <span>Back</span>
            </a>
            <a href="attack1weblab.php" class="nav-btn">
                <span>Next</span>
                <span class="arrow arrow-next">&rarr;</span>
            </a>
        </div>
    </div>
</body>
</html>


