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

        .back-button {
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

        .back-button:hover {
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
        <h1>Bettercap Basics - Lecture</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>

    <main class="content">
        <p>-we're going to be using a tool called Better Cap. Better Cap can be used to do exactly what we did with ARP Spoof. So we can use it to run an ARP spoofing attack, to intercept connections, and it can be used to do so much more. So we can use it to capture data and analyze it and see usernames and passwords. We can use it to bypass HTTPS and potentially bypass HSTS. We can use it to do DNS spoofing, inject code into loaded pages and so much more.
For now, I'm going to show you how to install the tool and give you a quick overview on how to use it.
So I'm going to go to my kali machine here and to run Better Cap, all I have to do now is just type its name, 

-Better Cap followed by dash iface To specify the interface that is connected to the network that I want to run the attacks against.
And as you know, to get my interface, we can just do if config And I'm going to be running this against my net network, which is ETH0 is connected to,
so I'm going to set my interface to ETH0. And I'm going to hit enter to run the tool.
And as you can see, now we're inside the tool. We have a different prompt now in which we can use the commands of Better Cap.
*command: bettercap -iface eth0

-since we don't know how to use it, I'm actually going to type help.
As you can see, we get a full list of all of the commands that we can use.
What's really important. And you need to pay attention to right now is the modules.
So these are all of the modules that we can use or all of the things that we can get Better Cap to do.
by default, none of them is working, except for the event stream: which is basically the module that runs in the background to handle all the events.
*command: help

Now you can type, help. Followed by the name of any module you want. And this will show you a help menu that shows you how to use this specific module.
-For example, I want to show you in this lecture, the net dot probe and the net dot recon modules.
type help and I'm going to follow it by the name of the module Which is net dot probe.
you'll get a description of what this module does. So basically it keeps sending UDP packets to discover devices on the same network.
And we can do net probe on, to turn on the module and net dot probe off, to turn it off. You can also see all the options that you can modify for this module.
*command: help net.probe

-and I'm just going to do, net dot probe on to turn it on.
this'll automatically start discovering clients connected to the same network. So the 10.0.2.7 right here is actually my windows target machine.
So this is just another way of discovering connected clients quickly using better cap.
*command: net.probe on

-what you will notice is when we started the net dot probe, it automatically started the net dot recon. To confirm this, so if we go upright here, you can see the only module that was running is the events dot stream.
And now if I do help, you'll see, I actually have two modules running the net dot probe, which we just saw and we turned on manually. And the net dot recon, which got turned on automatically by Better Cap.
The reason for this is because the net dot probe sends probe requests to all possible IP's And then if we get a response, the net dot recon will be the one detecting this response by monitoring my ARP cache. And then adding all of these IP's in a nice list so we can target them.
*command: help

-So now, because the net dot recon is actually running, we can do net dot show, to see all of the connected clients.
we get a nice list of all of the connected clients. We can see their IP's. We can see the corresponding Mac addresses for these clients. And it can also show you information right here about each one of these IP's. you can also see at the vendor in here it's attempting to discover the manufacturer of the hardware used in each of these clients
Now you can also see here the 10.0.2.7 device. Like I said, this is my target Windows device right here.

-I'm going to show you how we can run an ARP spoofing attack using Better Cap to intercept the data and read usernames and passwords that flow through the network. Once we become the man in the middle, once we intercept the connection.

</p> 
    </main>

    <!-- Navigation Buttons -->
    <a href="attack_mitm_bettercap_basics.php" class="back-button">← Back</a>
    <a href="attack_mitm_bettercap_basics_lab.php" class="back-button">→ Next</a>

    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
