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
        <h1>Custom Spoofing - Lecture</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>

    <main class="content">
        <p>-In the previous lectures, we learned how to use Bettercap to discover all clients on the same network, run an ARP spoofing attack to intercept the data and then sniff data to see the usernames, passwords, and everything that's getting sent over the network. Now in order to do this, we actually had to run a number of commands. So first of all, we had to do net.probe on,
to turn on the probe module. We had to set the settings for the ARPspoof module, turn that on, and then turn the sniffing module on. Now, every time you want to do this, every time you 
want to intercept data and see it onscreen, you're gonna have to do all of the steps that I showed you in the previous lecture. Or if you're lazy like myself,

-you can use a caplet to do all of that automatically, which is exactly what I wanna show you in this lecture. So what do I mean by a caplet? Well, basically a caplet is just a text file that contains all of the commands that you want to run. I'm gonna open a text file and I'm gonna go to the first command that we had to run in order to do this. the first thing we did was net.probe on. So in my text file here, I'm gonna literally type this command, net.probe on and as we saw, this will automatically start the net.recon module. Again, we enabled both of these modules in order to discover the connected clients and keep automatically discovering any new clients that connect to the network. The next thing that we did was modify the settings for the ARPspoof module. So we did set ARPspoof full duplex to true. Then we set the target IP. You wanna make sure that you change the IP here to the IP of your target all the time. And if you are targeting multiple computers, you can just use the comma and type the next IP after the coma. Next we turned on the ARPspoof module. So again, this is what I'm gonna do here. I'm gonna do arp.spoof on. And finally we also run the sniffer by doing net.sniff on.

-So this is actually a nice summary of what we did in the previous lectures. Again, like I said, every time you wanna intercept the connections, you're gonna have to start Bettercap
and run all of these commands manually. You wanna start the probe module, you wanna enable the full duplex. So you spoof the target and the router. You wanna set your target IP
and you wanna turn on the spoof and turn on the sniff. So to make this very easy, instead of having to type this every time we want to run an ARP spoofing attack and intercept data,
I put all of this in a text file. I'm gonna save this text file. I'm gonna put it in my root directory and I'm gonna call it spoof.cap. So I'm gonna save this now and I can close it

*caplet content: 
net.probe on
set arp.spoof.fullduplex true
set arp.spoof.targets 10.0.2.7
arp.spoof on
net.sniff on

-And what I'm actually gonna do,  if I do LS to list all of the files and directories in the current working directory, because right now I am in root. So if I do LS, you can see we have a new file called spoof.cap. And just to confirm, if I go down to my file manager right here, you can see we have a new file, again in the root called spoof.cap. And all we want to do, is feed this spoof file to Bettercap before we start Bettercap. 

-So we're gonna run Bettercap like we used to do. First of all, we do Bettercap followed by iface to specify the interface that is connected to the target network and in my case this is ETH0. So, so far this is identical to what I've been doing in the previous lectures. The only difference now, is we're gonna use the -caplet option to specify my caplet file that I just created. So I'm gonna do -caplet followed by the file that I just created, which is called spoof.cap and that's it.
So after I run this, it should automatically start all of the modules that I just typed and it should run an ARP spoofing attack. Therefore, the router's MAC address should change to the MAC address of ETH0 that is connected to kali right here. 
As you can see, we got no errors at all. If I do help, as you can see automatically we have this spoof is running. We have the probe, the recon, and the sniff all running as soon as we run Bettercap.
*command: bettercap -iface eth0 -caplet spoof.cap


-If you remember the first time we ran it, we only had the stream running and we had to do everything manually and set the options manually. So this is a really, really nice way of doing it. Now let's confirm that everything is working as expected. So I'm gonna go to the windows machine and we're gonna do ARP-a again. And perfect, as you can see, the routers MAC address
has changed to the same MAC address as the kali machine, So this means that this windows machine is now spoofed, thinking that the kali machine is the router and the router now thinks
that the kali machine is the target machine, This will place kali in the middle of the connection.

-this will only work with HTTP. We will discuss HTTPS in the next lectures. But in this lecture I just wanted to show you an easy way
of scripting the commands that you often run with Bettercap because in the future we're gonna be doing a number of things that rely on us being the man in the middle.
So because I don't want to waste time enabling all of the modules that we're running here. So again, if I right click this and open with a normal text editor,
all you'll have to do is just put your commands in a file, give a file a specific name, and then when you're on Bettercap, all you have to do is just use the caplet argument,
followed by the name of your caplet file.</p>
    </main>

    <!-- Back Button -->
    <a href="attack_mitm_custom_spoofing.php" class="back-button">← Back</a>

    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
