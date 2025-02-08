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
    <title>Custom Spoofing - Lecture</title>
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
        <h1>Custom Spoofing - Lecture</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Navigation</h2>
        <a href="#introduction"><i class="fas fa-info-circle"></i> 1. Introduction</a>
        <a href="#caplet-creation"><i class="fas fa-file-code"></i> 2. Caplet Creation</a>
        <a href="#execution"><i class="fas fa-terminal"></i> 3. Execution</a>
        <a href="#verification"><i class="fas fa-check"></i> 4. Verification</a>
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
            <p>-In the previous lectures, we learned how to use Bettercap to discover all clients on the same network, run an ARP spoofing attack to intercept the data and then sniff data to see the usernames, passwords, and everything that's getting sent over the network. Now in order to do this, we actually had to run a number of commands. So first of all, we had to do net.probe on,
to turn on the probe module. We had to set the settings for the ARPspoof module, turn that on, and then turn the sniffing module on. Now, every time you want to do this, every time you 
want to intercept data and see it onscreen, you're gonna have to do all of the steps that I showed you in the previous lecture. Or if you're lazy like myself,

-you can use a caplet to do all of that automatically, which is exactly what I wanna show you in this lecture. So what do I mean by a caplet? Well, basically a caplet is just a text file that contains all of the commands that you want to run. I'm gonna open a text file and I'm gonna go to the first command that we had to run in order to do this. the first thing we did was net.probe on. So in my text file here, I'm gonna literally type this command, net.probe on and as we saw, this will automatically start the net.recon module. Again, we enabled both of these modules in order to discover the connected clients and keep automatically discovering any new clients that connect to the network. The next thing that we did was modify the settings for the ARPspoof module. So we did set ARPspoof full duplex to true. Then we set the target IP. You wanna make sure that you change the IP here to the IP of your target all the time. And if you are targeting multiple computers, you can just use the comma and type the next IP after the coma. Next we turned on the ARPspoof module. So again, this is what I'm gonna do here. I'm gonna do arp.spoof on. And finally we also run the sniffer by doing net.sniff on.</p>
        </div>
        <div id="caplet-creation" class="section">
            <h2>2. Caplet Creation</h2>
            <p>-So this is actually a nice summary of what we did in the previous lectures. Again, like I said, every time you wanna intercept the connections, you're gonna have to start Bettercap
and run all of these commands manually. You wanna start the probe module, you wanna enable the full duplex. So you spoof the target and the router. You wanna set your target IP
and you wanna turn on the spoof and turn on the sniff. So to make this very easy, instead of having to type this every time we want to run an ARP spoofing attack and intercept data,
I put all of this in a text file. I'm gonna save this text file. I'm gonna put it in my root directory and I'm gonna call it spoof.cap. So I'm gonna save this now and I can close it

*caplet content: 
net.probe on
set arp.spoof.fullduplex true
set arp.spoof.targets 10.0.2.7
arp.spoof on
net.sniff on</p>
        </div>
        <div id="execution" class="section">
            <h2>3. Execution</h2>
            <p>-And what I'm actually gonna do,  if I do LS to list all of the files and directories in the current working directory, because right now I am in root. So if I do LS, you can see we have a new file called spoof.cap. And just to confirm, if I go down to my file manager right here, you can see we have a new file, again in the root called spoof.cap. And all we want to do, is feed this spoof file to Bettercap before we start Bettercap. 

-So we're gonna run Bettercap like we used to do. First of all, we do Bettercap followed by iface to specify the interface that is connected to the target network and in my case this is ETH0. So, so far this is identical to what I've been doing in the previous lectures. The only difference now, is we're gonna use the -caplet option to specify my caplet file that I just created. So I'm gonna do -caplet followed by the file that I just created, which is called spoof.cap and that's it.
So after I run this, it should automatically start all of the modules that I just typed and it should run an ARP spoofing attack. Therefore, the router's MAC address should change to the MAC address of ETH0 that is connected to kali right here. 
As you can see, we got no errors at all. If I do help, as you can see automatically we have this spoof is running. We have the probe, the recon, and the sniff all running as soon as we run Bettercap.
*command: bettercap -iface eth0 -caplet spoof.cap</p>
        </div>
        <div id="verification" class="section">
            <h2>4. Verification</h2>
            <p>-If you remember the first time we ran it, we only had the stream running and we had to do everything manually and set the options manually. So this is a really, really nice way of doing it. Now let's confirm that everything is working as expected. So I'm gonna go to the windows machine and we're gonna do ARP-a again. And perfect, as you can see, the routers MAC address
has changed to the same MAC address as the kali machine, So this means that this windows machine is now spoofed, thinking that the kali machine is the router and the router now thinks
that the kali machine is the target machine, This will place kali in the middle of the connection.

-this will only work with HTTP. We will discuss HTTPS in the next lectures. But in this lecture I just wanted to show you an easy way
of scripting the commands that you often run with Bettercap because in the future we're gonna be doing a number of things that rely on us being the man in the middle.
So because I don't want to waste time enabling all of the modules that we're running here. So again, if I right click this and open with a normal text editor,
all you'll have to do is just put your commands in a file, give a file a specific name, and then when you're on Bettercap, all you have to do is just use the caplet argument,
followed by the name of your caplet file.</p>
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
