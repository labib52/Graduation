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
    <title>WPS Cracking - Lecture</title>
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
        <h1>WPS Cracking - Lecture</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>

    <main class="content">
        <p>wps feature:
there is a feature that if enabled and misconfigured can be exploited to recover the key without having to crack the actual encryption this feature is called wps

- wps is a feature that can be used with wpa & wpa2
- allows clients to connect without password
- authentication is done using an 8 digit pin which is very small, can try all possible pins in relatively short time, then the wps pin can be used to computer the actual password</p> 
        <p>it allows devices to connect to the network easily without having to enter the key of the network. it was designed to simplify the process of connecting printers and such devices
you can actually see a wps button on most wireless enabled printers, if this button is pressed and you press the wps button on the router you will notice that the printer will connect to the router without entering a key; by this way the authentication is done by 8 digit pin( made by only numbers and only 8 digits)
if you get this pin it can be used to recover the actual wpa/wpa2 key
</p>
        <p>for this technique to work 
-we need wps to be enabled on the network because it cant be disabled
-also it needs to be misconfigured to use a normal pin authentication not a push button authentication (PBC) because if push button authentication is used then the router will refuse any pins that we will try unless the wps button is pressed on the router
this method will not work if push button is enabled
in most modern routers PBC comes enabled by default or wps will be disabled by default
so because wpa/wpa2 are so secure and so challenging it is always a good ides to check if wps is enabled</p>
        <p>-----------------------------------------------------------------------
wps attack:

-first enable adapter in monitor mode

-use tool called wash that scan all networks around that have wps enabled

the only reason it might fail is if the target uses pbc so it will refuses all the pins unless the button is pressed. the only way to know is to try and see if it works

-open another terminal and run reaver
it is a program that will brute force the pin; it will try every possible pin and then found the right one and from it will get the wpa actual key and only then you can associate with the target. Because otherwise aireplay-ng will fail to associate


associate with target network using a fake authentication attack , basically saying i want to communicate with you please don't ignore me 
-use aireplay-ng (to associate with the target network)

by both running in parallel(start reaver first)
-reaver will find both wps pin and wpa actual key
</p>
<p>-----------------------------------------------------------------------
    -ifconfig wlan0 down
 airmon-ng check kill
 airmon-ng start wlan0

-wash --interface wlan0mon( list networks enabling wps)


-reaver --bssid 00:10:18:90:2D:EE --channel 1 --interface wlan0mon -vvv --no-associate
*in another terminal (bssid of the target)(--vvv shows us more info)(--no-associate to tell reaver not to associate with the target network because we already manually doing it)

(run reaver then run this command in the first terminal)
-aireplay-ng --fakeauth 30 -a 00:10:18:90:2D:EE -h 48:50:60:5D:45:25 wlan0mon
(30 associate with the target network every 30 seconds)(-a macaddress of the target)(-h macaddress of the adapted to associate)

then it is done 
-wps pin found 
-wpa password found</p>
    </main>

    <!-- Back and Next Buttons -->
    <div style="text-align: center; margin-top: 20px;">
        <a href="attack_wps_cracking.php" class="back-button">← Back</a>
        <a href="attack_wps_cracking_lab.php" class="next-button">→ Next</a>
    </div>

    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
