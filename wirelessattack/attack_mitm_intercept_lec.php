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
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
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

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background: #fff;
            color: #007BFF;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s ease-in-out;
            z-index: 1000;
        }

        .sidebar.closed {
            transform: translateX(-250px);
        }

        .sidebar h2 {
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 20px;
            color: #007BFF;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: #007BFF;
            text-decoration: none;
            font-size: 1rem;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .sidebar a:hover {
            background: #007BFF;
            color: white;
        }

        .sidebar a i {
            margin-right: 10px;
        }

        .toggle-btn {
            position: fixed;
            top: 20px;
            left: 260px;
            background: #007BFF;
            color: white;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 10px;
            border-radius: 50%;
            transition: transform 0.3s ease-in-out, background-color 0.3s ease;
            z-index: 1001;
        }

        .toggle-btn:hover {
            background: #0056b3;
        }

        .toggle-btn.closed {
            left: 20px;
            transform: rotate(180deg);
        }

        /* Progress Bar */
        .progress-bar-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #f4f4f9;
            z-index: 999;
            height: 10px;
        }

        .progress-bar {
            height: 10px;
            background-color: #007BFF;
            width: 0;
            transition: width 0.25s;
        }

        /* Content */
        .content-container {
            margin-top: 70px;
            margin-left: 270px;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
        }

        .content-container.collapsed {
            margin-left: 20px;
            width: calc(100% - 40px);
        }

        .section {
            padding-top: 80px;
            margin-bottom: 2rem;
        }

        footer {
            text-align: center;
            padding: 1rem;
            background-color: #007BFF;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .back-button {
            display: inline-block;
            margin-top: 0.1px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #0056b3;
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

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Navigation</h2>
        <a href="#introduction"><i class="fas fa-info-circle"></i> 1. Introduction</a>
        <a href="#sniffing"><i class="fas fa-network-wired"></i> 2. Network Sniffing</a>
        <a href="#packet-capture"><i class="fas fa-file-alt"></i> 3. Packet Capture</a>
        <a href="#password-capture"><i class="fas fa-key"></i> 4. Password Capture</a>
    </div>

    <button class="toggle-btn" id="toggle-btn">&laquo;</button>

    <!-- Progress Bar -->
    <div class="progress-bar-container">
        <div class="progress-bar" id="progress-bar"></div>
    </div>

    <!-- Main Content -->
    <div class="content-container" id="content-container">
        <div id="introduction" class="section">
            <h2>1. Introduction</h2>
            <p>-So right now, we are already in the middle of the connection and this data is already flowing through our computer. So all we have to do is just use a program to capture this data and analyze it. Now we can use Wireshark to do that but for now I'm gonna use a really nice module that comes with BetterCAP, that will automatically capture all of this data,
analyze it and show me the interesting stuff. So all we have to do now is to tell BetterCAP
to capture all of the data that is flowing through this computer and analyze it for me.
And to do this, we can use the net.sniff module.
</p>
        </div>
        <div id="sniffing" class="section">
            <h2>2. Network Sniffing</h2>
            <p>-so I'm just gonna do 
*command: net.sniff on

so now let's go to the target Windows computer I'm gonna open my web browser and we're gonna generate some traffic and see if that's gonna be captured by BetterCAP.
What we're doing right now will not work against HTTPS but don't worry, we'll talk about how to bypass HTTPS later on and why this won't work.
But for now, for testing, I'm just gonna to a website called vulnweb and I'm gonna include it's link of the website: http://vulnweb.com/</p>
        </div>
        <div id="packet-capture" class="section">
            <h2>3. Packet Capture</h2>
            <p>So as you can see this is a normal website that doesn't use HTTPS. It also has a number of links here, so if I click, for example, on this link, everything is loading fine as you can see here.
But if we go to the Kali machine, you'll see that every request that we sent was actually captured by this computer So you can do this to any computer that is connected to the same network as you, whether it's a wired or a wireless network. So you can see there were requests sent to Google,
if we scroll down, you will see we made a request for this website, vulnweb.com.
You can also see all of the other files that this website loaded. So you can see we have a logo loaded here. You can see we have a styles file being loaded here.
Again, if there were more images, you'll actually see links to all of the images that are being loaded. You can see here this is the second link that we clicked on, the testphp.vulnweb.com So this is what we have right here, here in the top at the URL
</p>
        </div>
        <div id="password-capture" class="section">
            <h2>4. Password Capture</h2>
            <p>Now also, let me just go back and maybe click on the first one.
And as you can see, this is another website. It has the login functionality in here.
And let's try, for example, login with a username. Let's set the username to my name
And let's put the password I'm gonna click on Login.
Again, as you can see, we got logged in, no issues at all.
But if I go back to the Kali computer and scroll up, as you can see, we captured a login that was sent to this website, testhtml5.vulnweb.com.
Again, this is exactly the website that we have here
and if you look in here, you can see that the username and the password


anything that the target computer sends or receives right now will be captured by the Kali machine. And like I said, we can do this to any computer or any phone that is connected to the same network as us whether it's a WIFI or a wired network.</p>
        </div>
    </div>

    <script>
        // Sidebar toggle functionality
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');
        const contentContainer = document.getElementById('content-container');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('closed');
            contentContainer.classList.toggle('collapsed');
            toggleBtn.classList.toggle('closed');
        });

        // Progress Bar Functionality
        window.addEventListener("scroll", function() {
            const progressBar = document.getElementById("progress-bar");
            const totalHeight = document.body.scrollHeight - window.innerHeight;
            const progress = (window.scrollY / totalHeight) * 100;
            progressBar.style.width = progress + "%";
        });

        // Fix navigation scroll offset
        const links = document.querySelectorAll('.sidebar a');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);
                const offsetTop = targetSection.offsetTop - 70; 
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
