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
    <title>ARP Poisoning - Lecture</title>
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
        <h1>ARP Poisoning - Lecture</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>

    <main class="content">
        <p>-Now, in this lecture and the next few lectures, I will start talking about Man In the Middle Attacks.
These are attacks that we can launch only if we are able to intercept the communication between two devices.
So a normal communication would look like this, where the device is directly communicating with the entity that they want to communicate with.
In a Man In The Middle Attack, the hacker would be able to place themselves in the middle of the connection, allowing them to intercept and see anything that is being transferred between the two devices.

-the first method to achieve this is using an ARP spoofing attack.
ARP spoofing allow us to redirect the flow of packets so instead of it flowing directly through the two devices communicating it would flow through my own computer(attacker).
So any requests sent and any responses received by the target computer, will have to flow through the hacker computer. This means that any messages, any websites, any images, any usernames, any passwords entered by the target will have to flow through my computer. This allows me to read this information, modify
 it or drop it.

-ARP stands for Address Resolution Protocol, and it's a very simple protocol that allow us to link IP addresses to MAC addresses.
So for example, let's say we have a network here, we have devices A, B, C, and D.
They're all connected to the same network And we have the router here for this network. We can see that each device has an IP and a MAC address.
Let's assume that device A needs to communicate with device C. Now we're also gonna assume that device A knows the IP of device C.
But as we know so far, in order for these devices to communicate within the same network, device A needs to know the MAC address of device C.
Because like we said before, the communication inside the network is carried out using the MAC address and not using the IP address.
So this is a perfectly normal situation where have a client that needs to know the MAC address of another client so that it can communicate with this client. So what this client does, it uses the ARP protocol.
Basically, it sends a broadcast message. So it sends an ARP request to all the clients on the network saying who has 10.0.2.6? Now all of these devices will ignore this packet
except the one that has this IP address, which is 10.0.2.6, which is device C. the only device that will respond is device C sending an ARP response.
In this response, device C is gonna say I have 10.0.2.6, my MAC address is this MAC address.
This way device A will have the MAC address of device C and now it will be able to communicate with device C and do whatever task that it wanted to do initially.
So all of this communication is facilitated using the ARP protocol. All it has is requests and responses
and the whole point of it is so that we can link IP addresses to MAC addresses or translate IP addresses to MAC addresses.
you can see your Arp table by command: ARP-a

-in any typical network any device that's connected to the network, if it wants to send a request, it will send them to the router, the router will go and send that request to the Internet, wait for the response and then forward the response to the device that requested it.

-Now what we can do is we can exploit the ARP protocol and send two ARP responses, one to the gateway and one to the victim. We're gonna tell the gateway that I am at the IP of the victim,
so the access point will update its ARP table and it'll associate the IP of the target with my MAC address.
We'll do the same with the victim, so we'll send it an ARP response. We're gonna tell it that I am at 10.0.2.1 so it's going to update its ARP table and associate the IP of 10.0.2.1 with my own MAC address.
So the result of this, the victim is gonna think that I am the router and the router is gonna think that I am the victim.
So anytime the victim wants to send any requests, the requests will have to flow through my computer and I'm gonna forward them to the router.
And then anytime the access point or the router wants to send responses, they're gonna go to my machine because it thinks that I am the victim and then I'm going to forward it to the victim.

-this is a very serious and very powerful attack. And the reason why it is possible is because ARP is not very secure.
Because first of all, clients can accept responses even if they did not send a request.
So as I said before, we're gonna send a response to the access point and a response to the victim
telling them that I am at a specific IP without them asking who am I or without them asking for this IP.
I'm just gonna send the response and they're gonna accept that response anyway.

Not only that,
well, they're also not going to verify who I am. So when I say that I am a 10.0.2.7. I am clearly not at that IP But the access point will trust this and it'll actually update its ARP table based on the information that I sent.
Same goes to the victim.
I'm gonna tell it that I am at 10.0.2.1 it's gonna trust and believe this, even though I am clearly not at this IP
So these are the two main weaknesses with ARP protocol that allow us to run ARP spoofing attacks.
-------------------------------------------------------------------
I will show you how to use a very simple yet reliable tool called arpspoof,

Now, using arpspoof is very simple.
arpspoof -i [interface] -t [client Ip] [gateway Ip]
arpspoof -i [interface] -t [gateway Ip] [client Ip]


-arpspoof -i eth0 -t 10.0.2.7 10.0.2.1
(this will spoof the target, telling him that I am the router.)

 in another terminal
-arpspoof -i eth0 -t 10.0.2.1 10.0.2.7
(we're gonna be telling the router that I am the victim,)

so the first one will fool the victim, the second will fool the router.

Now, this computer is not a router so when it gets requests, it's actually going to stop them from flowing and going to the router. This is a security feature in Linux.
So, you need to enable port forwarding so that this computer would allow packets to flow through it just like a router.
Now to enable port forwarding,
in another terminal
-echo 1 > /proc/sys/net/ipv4/ip_forward
</p>

        <!-- Navigation Buttons -->
        <a href="attack_mitm_arp_poisoning.php" class="back-button">← Back</a>
        <a href="attack_mitm_arp_poisoning_lab.php" class="next-button">→ Next</a>
    </main>

    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
