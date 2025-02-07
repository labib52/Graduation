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
    <title>WEP Cracking - Lecture</title>
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
        <h1>WEP Cracking - Lecture</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>

    <main class="content">
        <p>wep cracking

-wired equivalent privacy
-old enc
-uses an algo called RC4
-still used in some networks
-can be cracked easily

how it works:

- client encrypts data using a key
- encrypted packet sent in the air
- router decrypts packet using the key


rc4 algorithm used to encrypt the data sent
if client wants to send something to router, it will be first encrypt using a key so, the normal message will be converted into gibberish. 
if a hacker captures this packet i the air he will not be able to read it even though it has useful info.
the access point receive encrypted packet and will be able to transform it to the original form because it has the key so can read the contents(decrypt) ;same happens when sending from the router to the client again

lets zoom in
- each packet is encrypted using a unique key stream
-random initialization vector (IV) is used to generate the key streams
- the (IV) is only 24 bits!!
- IV + key(password)= key stream

wep tries to generate a unique key for each packet sent in the air so, it generates a random 24 bits IV then added be to the password of the network, this generates a key stream and then this key stream is used to encrypt this packet and transform it into gibberish.
( keystream + data = encrypted message)
before sending this into the air , wep also append the IV
the reason why it adds the IV to the packet is because once router receives this packet it needs to be able to decrypt it which needs the key and the IV(router already has the key)


-IV is too small
-Iv is sent in plain text

the weakness is that the IV is sent in plain text(size of IV 24 bits is so small)

result:
- IV will be repeated on a busy network
-this makes wep vulnerable to statistical attacks( used tool aircrack-ng to determine key stream)
-repeated IV can be used to determine key stream and break the encryption

so to crack wep we need to :
1- capture a number if packets/Ivs( using airodump-ng)
2- analyze the captured IVs and crack the key(using aircrack-ng)
</p>
        <p>------------------------------------------------------------------------------
if network is busy
-first wireless adapter in monitor mode

-run airodump-ng to list all networks around

-run airodump-ng against the wep target network (using the bssid of the target)

in another terminal
-run aircrack-ng using the file. Cap (used to save the stream in airodump-ng command)

-key is found ( just remove the columns)</p>
        <p>--------------------------------------------
if network is not busy

-first wireless adapter in monitor mode

-run airodump-ng to list all networks around

we need to force the access point to generate new packets with new IVs
this happens by associating with this target network
this means that we tell the network that we want to communicate with it because by default access point ignores any requests it get unless the device has connected to this network or associated with it 

run airodump-ng against the wep target network (using the bssid of the target)
 will notice data is increasing slowly or not increasing at all( no IVs to collect)


you have to use aireplay-ng to associate with this network by doing fake authentication attack
by letting your adapter communicate with the target network

so run aireplay-ng 
new client will be associating with the network (adapter)
now we can start communicating with it and it will not ignore us so, now we can go and start injecting packets into the traffic and force the access point to generate new packets with new ivs , this will increase the number of data allowing s the to crack wep in minutes even if the network was not busy

the most reliable method to do this is using Arp request replay attack
- wait for an Arp packet
- capture it and replay it(retransmit it)
- this causes the access point to produce another packet with a new IV
- keep doing this till we have enough IVs to crack the key

 associate again with the aireplay-ng and then
- run Arpreplay attack in new terminal 

now number of data is increasing

run aircrack-ng using the file. Cap used to save the stream in airodump-ng command
key is found ( just remove the columns)
</p>
<p>———————————————————————————————————————————————————————
    Wep:

——If busy:

- Ifconfig wlan0 down
 Airmon-ng check kill
 Airmon-ng start wlan0

-Airodump-ng wlan0mon(to list networks)

-Airodump-ng --bssid f0:51:36:2a:1a:dc --channel 1 --write test wlan0mon (to capture devices on this network)

-aircrack-ng test_01.cap
So the key will be found (only remove the colons)
—————————————————————————————————————————————————————
</p>
<p>if not busy (data 0)

- Ifconfig wlan0 down
 Airmon-ng check kill
 Airmon-ng start wlan0

-Airodump-ng wlan0mon(to list networks)

-Airodump-ng --bssid f0:51:36:2a:1a:dc --channel 1 --write test wlan0mon (to capture devices on this network)(data 0)

In new terminal
-Aireplay-ng --fakeeauth 0 -a f0:51:36:2a:1a:dc -h 48-5d:60:2a:45:25 wlan0mon
(0 to make it once,-a macaddress of target)
(-a macaddress of target network)
(-h macaddress of adapter by ifconfig 1st 12)
(This step let adapter associate with target)

In new terminal
-Aireplay-ng --arpreplay -b f0:51:36:2a:1a:dc -h 48-5d:60:2a:45:25 wlan0mon
(Associate again before running this code)
(-b macaddress of target network)
(-h macaddress of adapter by ifconfig 1st 12)
(Adapter here waiting for arp packet to capture it and retransmit so access point is forced to generate a new packet with new iv)

in the terminal of the fake authenication
-aircrack-ng test_01.cap
Then key found (remove colons)</p>
       
    </main>

    <div style="text-align: center;">
        <!-- Back Button -->
        <a href="attack_wep_cracking.php" class="back-button">← Back</a>
        <!-- Next Button -->
        <a href="attack_wep_cracking_lab.php" class="back-button">→ Next</a>
    </div>

    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
