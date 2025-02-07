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
    <title>Discovering Sensitive Info - Lecture</title>
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
        <h1>Discovering Sensitive Info - Lecture</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Navigation</h2>
        <a href="#introduction"><i class="fas fa-info-circle"></i> 1. Introduction</a>
        <a href="#netdiscover"><i class="fas fa-network-wired"></i> 2. Net Discover</a>
        <a href="#nmap"><i class="fas fa-search"></i> 3. Nmap</a>
        <a href="#zenmap"><i class="fas fa-map"></i> 4. Zenmap</a>
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
            <p>Information gathering is one of the most important steps when it comes to hacking or penetration testing. If you think of it, you can't really gain access to a system, if you don't have enough information about it. So for example, let's say you're connected to a network and one of the devices connected to this network is your target. Now for you to hack into that target, first, you need to discover all of the connected clients to this network, get their Mac address, their IP address. And then from there, try to maybe gather more information or run some attacks in order to gain access to your target. Examples are Net Discover and Nmap or its gui Zen map.
Remember, we're still in the network hacking section, So both you and the target machine need to be connected to the same network. the method that I'm going to show you will work exactly the same, whether you're using it against a virtual network or against real network. And even if your target is a WIFI or a wireless network. So all you have to do is type the name of the program.
for a real network, you cannot access the built in wireless card from a virtual machine.
Therefore, if you want to do this or run any of the wireless attacks that we're going to see in the future against a real computer and a real wireless network, you're going to need to use a wireless adapter.</p>
        </div>

        <div id="netdiscover" class="section">
            <h2>2. Net Discover</h2>
            <p>-lets start with the simple one net discover:
connect the wireless adapter, And what I'm going to do is I need to connect this adapter to a WIFI network first, before I can discover all the connected clients to this network. So I'm going to go to my network manager. I'm going to click in here and you want to click on select network, enter the password and then you will be connected
*type net discover and then type dash R  to specify an IP range to search for. This needs to be a range that can be accessed by you. So for example right now my IP is 10.0.2.16, and I can only access IPS on the same sub-net. So IP is on the same, sub-net start at 10.0.2.0 And they would end at 10.0.2.254, because 254 is the last IP that a client can have. So my range is going to be 10.0.2.1, and I want to search for clients that might have an IP of 10.0.2.1, 10.0.2.2, 10.0.2.3, all the way up to 10.0.2.225, 4.So instead of manually typing all of these IPS, I can just type over 24 and net discover will automatically know that I'm trying to search for all of the IPS that start at 10.0.2.1 and end 10.0.2.254. So this is a way of specifying an IP range for the whole subnet.
So if I hit enter now, you'll see that net discover will show me all the IPS of the devices connected to the same network and note that the first three parts of the IPS are always the same because they are on the same sub-net. And I also have the Mac addresses of these clients and Net Discovers also attempting to guess the device vendor.
Now, if I press Q, this will quit the program.
And right now we have a list of all the connected clients to the same network.
example of command: netdiscover -r 10.0.2.1/24</p>
        </div>

        <div id="nmap" class="section">
            <h2>3. Nmap</h2>
            <p>The second program that we'll use for network mapping is Nmap. Now in the previous method we use Netdiscover and we've seen how nice it is to quickly discover all the devices connected to our network, see their MAC address and maybe get the vendor. Nmap takes scanning to a whole new level. It might be a little bit slower than Netdiscover, but it'll show you much, much more information about the target. So you'll be able to see the open ports, you'll be able to see the running programs or the running services on these open ports, you'll be able to determine the computer name, the operating system running on that computer. If you're in a network, you'll be able to discover all of the connected clients, you'll be able to bypass security, bypass firewalls, and so much more. Nmap is actually a huge tool.</p>
        </div>

        <div id="zenmap" class="section">
            <h2>4. Zenmap</h2>
            <p>We're actually going to be used in Zenmap,
- which is the graphical user interface of Nmap. So to run it in terminal, you just have to type Zenmap or you can find it under your applications menu.
 Now, as you can see, it has a very, very simple interface. The first thing that we see is the target input box. In here, you can put your target. You can scan any IP that you can reach, whether it's a personal computer, whether it's a server, whether it's an IP for a web server for a website, for example, that you want to discover all the open ports and all the running services on it. Or, like what we're going to do right now, we can put a range similar to what we did with Net discover and it'll scan this whole range, discover all the live IPS or the IPS of the connected machines on the same network and display information about them. So it's 192.168.1.1/24 At the bottom,
 you can see the command. This is actually the Nmap command that will be executed when I hit this Command button. So like I said, Zenmap, what we're using right now is just the graphical interface that will run this Nmap command in the background and show me the results.

Alternatively, if you don't really know much about Nmap and its commands, you can use one of their ID profiles in here. So in this lecture, we're actually gonna be using a number of these profiles and we'll see the difference between them in terms of speed and the information gathered.
- So I'm gonna start with the Ping scan. 
This is a very quick scan. It literally just pings every possible IP in the range and if it gets a response, it'll record this response and it'll show me the devices that gave me a response, which means that these are the devices connected to the network. Now, a lot of devices do not respond to ping requests even if they are alive. So the list that you'll get in this scan might not include all the devices connected to your network. Now once the scan is done, as you can see, we can see the list of all the connected devices in here. And in here, we can *also see the MAC addresses for each of these devices. We also can see the vendor. so now you can make assumptions about the information appeared and start searching for exploits and vulnerabilities.
So again, it was a very quick scan, but as you can see, it still gave us much more information than what we got from Netdiscover.

-The next scan that I wanna show you is the Quick scan.
Now this is gonna be slightly slower than the Ping scan but it's gonna show us more information. So right now you can see that the scan is showing us the same information that we've seen *before with the ping scan, but it's also showing us the open ports on each one of the discover devices.

-the next scan is the quick scan plus.
This scan takes the quick scan one step further. So first of all it'll be slower, but it's going to show us even more information. So first we're gonna be able to see the operating system running on the discovered devices. We will also be able to see the device type, whether it's a phone or a laptop or a router, and we'll be able to discover the program, and the program version running on the discovered ports. So the first thing you'll notice is the icons beside the IPs of the discovered devices. These icons represent the operating system running on these devices. So right now we have the operating system for all of the connected devices.
You can also browse by the services. So from here on the left if you click on services you'll be able to categorize the discovered clients based on the services. So if we click on http we'll see all the clients that have a http service running. If you click on ssh we can see only the  devices that has a ssh service running, etc..

for an example:
If we go back here to the hosts and go back to the apple device, As we see we know it's a phone, we know it's an Apple phone, we know that it has an ssh service installed on it running on port 22, and we know that ssh is a service that allows you to remotely execute system commands on the computer that has the ssh service installed. Now obviously before you can use this service you have to use a username and a password. Once you authenticate it will allow you to execute system commands remotely on that computer or on that phone. Now by default iOS devices do not have an ssh server. Usually when you jailbreak the phone or the device it will automatically install an ssh server and the password for that server
is set to "alpine", by default.
Now since we know that this is an iPhone and it has port 22 open with open ssh server, we know that that this phone has been jailbroken. Now since the phone is jailbroken, we know the password to log into ssh is "alpine" unless the user changed it. Now most users do not even know about this, and even the ones that know about this, are too lazy to change it. So it's always worth a try if you discover a phone like this in the same network. It's always worth a try to go and try to connect to it with the default password.

So just gonna go to my terminal and try to connect to this phone using ssh.
*So type "ssh root", which is the username for the admin in Linux, "@192.168.1.12". This is the IP of the phone. I'm gonna hit enter. It's asking me if I should trust this connection, I'm gonna say yes, and now it's asking me for the password. And like I said, when the phone is jailbroken the password is set to "alpine". So I'm gonna type alpine I'm gonna hit enter. And as you can see, I logged in as root. So right now I have the highest privileges on the phone and I can do whatever I want on the system. And now we can use system commands to completely control the phone.
command: ssh root@192.168.1.12
</p>
        </div>

    </div>
    <div class="button-container">
    <a href="attack_discovering_info.php" class="back-button">← Back</a>
    <a href="attack_discovering_info_lab.php" class="back-button">→ Next</a>
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
