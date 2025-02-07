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
    <title>WPA Cracking - Lecture</title>
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
        <h1>WPA Cracking - Lecture</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>

    <main class="content">
        <p>wpa/wpa2 cracking

- both can be cracked using the same methods
- made to address the issues in wep
- much more secure than wep
- each packet is encrypted using a unique temporary key
- packets contain no useful information

the difference between wpa and wpa2 is the encryption used to ensure message integrity
wpa uses TKIP and wpa2 uses CCMP  //aes
but this will not affect anything any following methods will crack any of them

both were made after wep and they were designed to address the weakness in it so they are much more secure and cracking the for sure are more complex

- both fixed all weaknesses in wep
- packets contain no useful data
- only packets that can aid with the cracking process are the handshake packets(these are 4 packets sent when a client connects to the network)

after learning about wps:
if wps is disabled on your target network or it is enabled but configured to use push button authentication then the wps method will not work then you have to go and crack the actual wpa encryption.
the developers make sure that they properly fix all wep weaknesses. 

in wpa2 the keys are unique and temporary mush longer than wep so packets in the air have no useful info for us except the handshake packets</p>
        <p>----------------------------------------------------
capture handshake:
---------

- adapter in monitor mode

- run airodump-ng to list networks around you

-run airodump-ng against your target network and store the data in a file

now you need to wait for the handshake to be captured ; the handshake is sent when a client connects to a network
so wait until a new client connects to the network and the airodump will tell us that the handshake is captured

alternatively we can use deauthentication attack(disconnect a client from the network for a short period of time and he will automatically connect once you stop the deauthentication attack so we can capture the handshake with waiting for a new user to connect

in another terminal
-run deauthentication attack

now the client connected again and we recieve the handshake from the airodump-ng and stored in the file wpa_handshake

--quit airodump-ng (ctrl c)</p>
<p>----------------------------------------------------------------
- the handshake doesn't contain data that helps recovering the key
- it contains data that can be used to check if a key is valid or not

we will create a wordlist which is basically a big text file that contains a large number of passwords, then go through the passwords one by one and use them with the handshake in order to check whether the password with valid or not. you can download readymade wordlists from the internet or create one

-use tool called crunch to create word list
crunch[min][max][characters]-t[pattern]-o[filename]

(min> minimum no of characters to be generated)
(max> maximum no of characters to be generated)
(characters>specify the characters that you want to generate password from)
(-t> to give a pattern)
(-o> to specify file name where the passwords will be stored in)
(-p> option let crunch to generate password that don't have repeating characters)

example:
crunch 6 8 123abc$ -o wordlist.txt -t a@@@@b
(minimum 6 , maximum 8, combination of 123abc$,and store it in a file called wordlist, start with a and end with b)
you can open the passwords file by : cat wordlist.txt

when you have both the handshake and a wordlist, aircrack-ng is going to unpack the handshake and extrct the useful info. 
-message integrity code (MIC) is what's used by the access point to verify whether a password is valid or not.

aircrack-ng is going to separate the MIC and use all the other info combined with the first password from the wordlist to generate an MIC and then compare this new generated MIC with the old MIC already captured in the handshake ; if they are the same so the password used to generated the MIC is the correct password else it is wrong and it will move to the next password in the wordlist making the same loop until MIC match
 so the success of this attack really depend on your wordlist

-now you have both the handshake and a wordlist
run aircrack-ng providing the handshake file and the wordlist file

-then the key is found


--some links to wordlists
ftp://ftp.openwall.com/pub/wordlists/
http://www.openwall.com/mirrors/
https://github.com/danielmiessler/SecLists
http://www.outpost9.com/files/WordLists.html
http://www.vulnerabilityassessment.co.uk/passwords.htm
http://packetstormsecurity.org/Crackers/wordlists/
http://www.ai.uga.edu/ftplib/natural-language/moby/
http://www.cotse.com/tools/wordlists1.htm
http://www.cotse.com/tools/wordlists2.htm
http://wordlist.sourceforge.net/</p>
<p>----------------------------------------------------------------
capture handshake:
---------

-ifconfig wlan0 down
 airmon-ng check kill
 airmon-ng start wlan0

-airodump-ng wlan0mon

-airodump-ng --bssid 00:10:18:90:2D:EE --channel 1 --write wpa_handshake wlan0mon

-aireplay-ng --deauth 4 -a 00:10:18:90:2D:EE -c 00:10:18:90:2D:EE wlan0mon
(-a macaddress of target network)(-c macaddress of client we want to disconnect)

-quit airodump-ng (ctrl c)
---------------

-crunch 6 8 123abc$ -o wordlist.txt -t a@@@@b

-aircrack-ng wpa_handshake-01.cap -w wordlist.txt

-then the key is found</p>
<p>----------------------------------------------------------------
    Securing Your Network From Hackers</p>
<p>Now that we know how to test the security of all known wireless encryptions (WEP/WPA/WPA2), it is relatively easy to secure our networks against these attacks as we know all the weaknesses that can be used by hackers to crack these encryptions.

So lets have a look on each of these encryptions one by one:

1. WEP: WEP is an old encryption, and its really weak, as we seen in the course there are a number of methods that can be used to crack this encryption regardless of the strength of the password and even if there is nobody connected to the network. These attacks are possible because of the way WEP works, we discussed the weakness of WEP and how it can be used to crack it, some of these methods even allow you to crack the key in a few minutes.

2. WPA/WPA2: WPA and WPA2 are very similar, the only difference between them is the algorithm used to encrypt the information but both encryptions work in the same way. WPA/WPA2 can be cracked in two ways

1. If WPS feature is enabled then there is a high chance of obtaining the key regardless of its complexity, this can be done by exploiting a weakness in the WPS feature. WPS is used to allow users to connect to their wireless network without entering the key, this is done by pressing a WPS button on both the router and the device that they want to connect, the authentication works using an eight digit pin, hackers can brute force this pin in relatively short time (in an average of 10 hours), once they get the right pin they can use a tool called reaver to reverse engineer the pin and get the key, this is all possible due to the fact that the WPS feature uses an easy pin (only 8 characters and only contains digits), so its not a weakness in WPA/WPA2, its a weakness in a feature that can be enabled on routers that use WPA/WPA2 which can be exploited to get the actual WPA/WPA2 key.

2. If WPS is not enabled, then the only way to crack WPA/WPA2 is using a dictionary attack, in this attack a list of passwords (dictionary) is compared against a file (handshake file) to check if any of the passwords is the actual key for the network, so if the password does not exist in the wordlist then the attacker will not be able to find the password.

Conclusion:

1.Do not use WEP encryption, as we seen how easy it is to crack it regardless of the complexity of the password and even if there is nobody connected to the network.

2. Use WPA2 with a complex password, make sure the password contains small letters, capital letters, symbols and numbers and;

3. Ensure that the WPS feature is disabled as it can be used to crack your complex WPA2 key by brute-forcing the easy WPS pin.


some practical steps:

-navigate to the Ip of your gateway(router) enter the Ip on the browser to access your router settings

-enter the username and password of the admin written at the back of the router

-enter WIFI settings

-navigate to security and make sure you are using wpa2 for maximum security

-make sure to use a long password that is made of small, capital letters and special characters and numbers and make it at least 14 characters that way it is very difficult to crack

-find where is your wps settings are, make sure it is disabled

-another optional that could be useful which is mac filtering ; this allows us to define a list of MAC addresses that can connect or should be disconnected from the network by creating allow or deny list

-make sure to apply changes

-also if you want that the de-authentication attack don't work against you can connect to your network through ethernet cable
</p>

        <!-- Back Button -->
        <a href="attack_wpa_cracking.php" class="back-button">← Back</a>
        <!-- Next Button -->
        <a href="attack_wpa_cracking_lab.php" class="back-button">→ Next</a>
    </main>

    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
