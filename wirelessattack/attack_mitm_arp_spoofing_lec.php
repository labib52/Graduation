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
    <title>ARP Spoofing - Lecture</title>
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

        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
            margin-bottom: 50px;
        }
    </style>
</head>
<body>
    <header>
        <h1>ARP Spoofing - Lecture</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>

    <div class="sidebar" id="sidebar">
        <h2>Navigation</h2>
        <a href="#introduction"><i class="fas fa-info-circle"></i> 1. Introduction</a>
        <a href="#setup"><i class="fas fa-cogs"></i> 2. Setting Up</a>
        <a href="#execution"><i class="fas fa-play"></i> 3. Execution</a>
        <a href="#summary"><i class="fas fa-check-circle"></i> 4. Summary</a>
    </div>

    <button class="toggle-btn" id="toggle-btn">&laquo;</button>

    <div class="progress-bar-container">
        <div class="progress-bar" id="progress-bar"></div>
    </div>

    <div class="content-container" id="content-container">
        <div id="introduction" class="section">
            <h2>1. Introduction</h2>
            <p>-Now, I will show you how to run an ARP spoofing attack using Bettercap.
This will allow us to place our computer in the middle of the connection and intercept data. Not only that, but we're also gonna see how we can read this data. So we can see all the URLs and all the websites that the target visits and we'll see everything that they post. So anything any usernames, any passwords they send to any websites, we're gonna be able to capture them and see them. 
So, first we need to become the man in the middle.</p>
        </div>

        <div id="setup" class="section">
            <h2>2. Setting Up</h2>
            <p>-And we're gonna do this using a module called ARP spoof, 
we're gonna do 
*command: help arp.spoof

because we want to see how to use this module and see all the options that we can set for it.
Now, anything you see under the parameters are the options that we can set for this specific module.
So in this lecture,
we're actually gonna be modifying some of these options. Now as you can see, the tool is actually very helpful because first of all it's given us the option name in yellow here. And then it's also telling us a description of what this option does and the default value.

-So for example, we can see we have an option called arp.spoof.fullduplex.
it will spoof both the router and the target. So it's similar to what we did with ARP spoof when we executed the command twice to spoof both the router and the target.
So if you set this to true, both the router and the target will be spoofed and you will be in the middle of the connection.
If you leave it to the default, which is false, you will only spoof the target machine.
this can be useful if the router has some sort of protection against ARP spoofing attacks because you won't to be interacting with router at all.
But it's also limiting because we won't be able to do what I'm gonna do in the next lectures because the router will communicate with the target device directly. So we won't to be able to inject stuff in the responses that the router sends to the target device. </p>
        </div>

        <div id="execution" class="section">
            <h2>3. Execution</h2>
            <p>-Now, I actually wanna change this to true
and the method I'm gonna do this can be used to change any option in any module in Bettercapp. So not only in the arp.spoof. If you're using any module, you can do help followed by the module name to get help about that module name.
You can see all of the options that you can set in here. And then if you want to modify the value of any of these options, all we have to do is copy the option name and type 'set' followed by the option that you want to modify, followed by the value that you want to set.

-And in my case it's called arp.spoof.fullduplex. And I wanna set this to true.
If you don't see errors, that means it got executed properly.
*command: set arp.spoof.fullduplex true

-The next option that I wanna change is the targets.
So again, in the description, it's telling us that these are the targets that I want to run the attack against and I can use a coma if I wanted to target more than one IP at the same time.

*command: set arp.spoof.targets 10.0.2.7

which is the IP of my target and we can get this using net discover, using zenmap or using the result that I got in here. After I ran the recon module, I did net.show and we got all of this, which is the list of all of the computers connected to the same network, And my target right now, is this particular device, the 10.0.2.7.This is my windows virtual machine right here.

-Now, we're ready to run the tool.
So we're gonna do arp.spoof on.
*command: arp,spoof on

And if I do help, again, we're gonna get a list of all of the modules that are running right now. And as you can see, we can see that ARP spoofing is on.Also, it is very important that you make sure that the net.probe and the net.recon are running.

So right now, Bettercap should be doing what ARP spoofing was doing, fooling both the router and the target device and putting me in the middle of the connection</p>
        </div>

        <div id="summary" class="section">
            <h2>4. Summary</h2>
            <p>So, let's go to the windows machine right here. And I'm gonna do arp-a and as you can see, the routers MAC address right here is the same as the MAC address for this device, which is the 10.0.2.15.

And if I go back here to the Kali machine and do ifconfig, you'll see this is the same MAC address as the MAC address of the Kali ETH0 interface. So basically, what this means is this windows machine, every time it wants to send something to the router, it'll send it to the Kali machine. And because we set the full duplex option on, in Bettercap,
the router also thinks that this Kali machine is the target machine. Therefore, anytime it needs to send a response to the windows machine, it'll actually send it to Bettercap right here.</p>
        </div>
    </div>

    <div class="button-container">
        <a href="attack_mitm_arp_spoofing.php" class="back-button">&larr; Back</a>
        <a href="attack_mitm_arp_spoofing_lab.php" class="back-button">Next &rarr;</a>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');
        const contentContainer = document.getElementById('content-container');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('closed');
            contentContainer.classList.toggle('collapsed');
            toggleBtn.classList.toggle('closed');
        });

        window.addEventListener("scroll", function() {
            const progressBar = document.getElementById("progress-bar");
            const totalHeight = document.body.scrollHeight - window.innerHeight;
            const progress = (window.scrollY / totalHeight) * 100;
            progressBar.style.width = progress + "%";
        });

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
