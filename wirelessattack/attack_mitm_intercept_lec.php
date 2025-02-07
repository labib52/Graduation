<?php
session_start();

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MITM Interception - Lecture</title>
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
        }

        header {
            background-color: #007BFF;
            color: #fff;
            padding: 1rem 2rem;
            text-align: center;
            position: relative;
        }

        header h1 {
            font-size: 1.8rem;
            font-weight: 600;
        }

        .user-info {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            font-size: 1rem;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 15px;
            border-radius: 8px;
        }

        .content {
            padding: 2rem;
            margin: auto;
            max-width: 900px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: justify;
            line-height: 1.8;
            overflow-wrap: break-word;
            white-space: pre-wrap;
        }

        .back-button, .next-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .back-button:hover, .next-button:hover {
            background-color: #0056b3;
        }

        footer {
            text-align: center;
            padding: 1rem;
            background-color: #007BFF;
            color: white;
            margin-top: 2rem;
        }

        a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>
    <header>
        <h1>MITM Interception - Lecture</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>

    <main class="content">
        <p>-So right now, we are already in the middle of the connection and this data is already flowing through our computer. So all we have to do is just use a program to capture this data and analyze it. Now we can use Wireshark to do that but for now I'm gonna use a really nice module that comes with BetterCAP, that will automatically capture all of this data,
analyze it and show me the interesting stuff. So all we have to do now is to tell BetterCAP
to capture all of the data that is flowing through this computer and analyze it for me.
And to do this, we can use the net.sniff module.

-so I'm just gonna do 
*command: net.sniff on

so now let's go to the target Windows computer I'm gonna open my web browser and we're gonna generate some traffic and see if that's gonna be captured by BetterCAP.
What we're doing right now will not work against HTTPS but don't worry, we'll talk about how to bypass HTTPS later on and why this won't work.
But for now, for testing, I'm just gonna to a website called vulnweb and I'm gonna include it's link of the website: http://vulnweb.com/

So as you can see this is a normal website that doesn't use HTTPS. It also has a number of links here, so if I click, for example, on this link, everything is loading fine as you can see here.
But if we go to the Kali machine, you'll see that every request that we sent was actually captured by this computer So you can do this to any computer that is connected to the same network as you, whether it's a wired or a wireless network. So you can see there were requests sent to Google,
if we scroll down, you will see we made a request for this website, vulnweb.com.
You can also see all of the other files that this website loaded. So you can see we have a logo loaded here. You can see we have a styles file being loaded here.
Again, if there were more images, you'll actually see links to all of the images that are being loaded. You can see here this is the second link that we clicked on, the testphp.vulnweb.com So this is what we have right here, here in the top at the URL

Now also, let me just go back and maybe click on the first one.
And as you can see, this is another website. It has the login functionality in here.
And let's try, for example, login with a username. Let's set the username to my name
And let's put the password I'm gonna click on Login.
Again, as you can see, we got logged in, no issues at all.
But if I go back to the Kali computer and scroll up, as you can see, we captured a login that was sent to this website, testhtml5.vulnweb.com.
Again, this is exactly the website that we have here
and if you look in here, you can see that the username and the password


anything that the target computer sends or receives right now will be captured by the Kali machine. And like I said, we can do this to any computer or any phone that is connected to the same network as us whether it's a WIFI or a wired network.</p>
    </main>

    <!-- Back and Next Buttons -->
    <div class="navigation">
        <a href="attack_mitm_intercept.php" class="back-button">← Back</a>
        
    </div>

    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
