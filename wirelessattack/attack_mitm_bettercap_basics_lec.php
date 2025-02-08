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
    <title>Bettercap Basics - Lecture</title>
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
            margin-top: 70px; /* Account for fixed header */
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
            padding-top: 80px; /* Offset for header height */
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

        .button-container {
            display: flex;
            justify-content: center; /* Center the buttons horizontally */
            gap: 20px; /* Add spacing between the buttons */
            margin-top: 20px; /* Add margin above the buttons */
            margin-bottom: 50px; /* Add margin below the buttons to avoid overlapping with the footer */
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
            text-align: center;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>Bettercap Basics - Lecture</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Navigation</h2>
        <a href="#introduction"><i class="fas fa-info-circle"></i> 1. Introduction</a>
        <a href="#netprobe"><i class="fas fa-network-wired"></i> 2. Net Probe</a>
        <a href="#netrecon"><i class="fas fa-search"></i> 3. Net Recon</a>
        <a href="#arpspoof"><i class="fas fa-spider"></i> 4. ARP Spoof</a>
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
            <p>-we're going to be using a tool called Better Cap. Better Cap can be used to do exactly what we did with ARP Spoof. So we can use it to run an ARP spoofing attack, to intercept connections, and it can be used to do so much more. So we can use it to capture data and analyze it and see usernames and passwords. We can use it to bypass HTTPS and potentially bypass HSTS. We can use it to do DNS spoofing, inject code into loaded pages and so much more.
For now, I'm going to show you how to install the tool and give you a quick overview on how to use it.
So I'm going to go to my kali machine here and to run Better Cap, all I have to do now is just type its name, </p>
        </div>

        <div id="netprobe" class="section">
            <h2>2. Net Probe</h2>
            <p>-Better Cap followed by dash iface To specify the interface that is connected to the network that I want to run the attacks against.
And as you know, to get my interface, we can just do if config And I'm going to be running this against my net network, which is ETH0 is connected to,
so I'm going to set my interface to ETH0. And I'm going to hit enter to run the tool.
And as you can see, now we're inside the tool. We have a different prompt now in which we can use the commands of Better Cap.
*command: bettercap -iface eth0

-since we don't know how to use it, I'm actually going to type help.
As you can see, we get a full list of all of the commands that we can use.
What's really important. And you need to pay attention to right now is the modules.
So these are all of the modules that we can use or all of the things that we can get Better Cap to do.
by default, none of them is working, except for the event stream: which is basically the module that runs in the background to handle all the events.
*command: help</p>
        </div>

        <div id="netrecon" class="section">
            <h2>3. Net Recon</h2>
            <p>Now you can type, help. Followed by the name of any module you want. And this will show you a help menu that shows you how to use this specific module.
-For example, I want to show you in this lecture, the net dot probe and the net dot recon modules.
type help and I'm going to follow it by the name of the module Which is net dot probe.
you'll get a description of what this module does. So basically it keeps sending UDP packets to discover devices on the same network.
And we can do net probe on, to turn on the module and net dot probe off, to turn it off. You can also see all the options that you can modify for this module.
*command: help net.probe</p>
        </div>

        <div id="arpspoof" class="section">
            <h2>4. ARP Spoof</h2>
            <p>-what you will notice is when we started the net dot probe, it automatically started the net dot recon. To confirm this, so if we go upright here, you can see the only module that was running is the events dot stream.
And now if I do help, you'll see, I actually have two modules running the net dot probe, which we just saw and we turned on manually. And the net dot recon, which got turned on automatically by Better Cap.
The reason for this is because the net dot probe sends probe requests to all possible IP's And then if we get a response, the net dot recon will be the one detecting this response by monitoring my ARP cache. And then adding all of these IP's in a nice list so we can target them.
*command: help

-So now, because the net dot recon is actually running, we can do net dot show, to see all of the connected clients.
we get a nice list of all of the connected clients. We can see their IP's. We can see the corresponding Mac addresses for these clients. And it can also show you information right here about each one of these IP's. you can also see at the vendor in here it's attempting to discover the manufacturer of the hardware used in each of these clients
Now you can also see here the 10.0.2.7 device. Like I said, this is my target Windows device right here.

-I'm going to show you how we can run an ARP spoofing attack using Better Cap to intercept the data and read usernames and passwords that flow through the network. Once we become the man in the middle, once we intercept the connection.</p>
        </div>
    </div>

    <div class="button-container">
        <a href="attack_mitm_bettercap_basics.php" class="back-button">← Back</a>
        <a href="attack_mitm_bettercap_basics_lab.php" class="back-button">→ Next</a>
    </div>

    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>

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
                const offsetTop = targetSection.offsetTop - 70; // Account for header height
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
