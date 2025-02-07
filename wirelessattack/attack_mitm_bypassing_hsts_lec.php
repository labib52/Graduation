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
    <title>Bypassing HSTS - Lecture</title>
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
        <h1>Bypassing HSTS - Lecture</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>

    <main class="content">
        <p>-In the previous lecture, we've seen how to downgrade HTTPS websites to HTTP, and this allowed us to basically see anything a user does on these websites because data in HTTP is sent in plain text. Therefore, we were able to see the usernames, the passwords, the URLs, and anything they do on HTTPS websites. At the end of the lecture, I also showed you that the method will not work against Facebook, Twitter, and other websites that use HSTS.

-The reason why it won't work against these websites is because modern web browsers come with a list of websites that they should only load over HTTPS. See, what we were doing in the previous lecture—whenever a browser requests a website, we load that website, even if it uses HTTPS, but we always give it back the HTTP version.
In HSTS, the browser knows that this website, for example, facebook.com, should always be loaded over HTTPS. So, even before sending this request to us, it'll always send it in HTTPS, and it'll always only accept it if it comes back as HTTPS.
So, there is nothing we can really do once we become the man in the middle. Because the browser is doing this check locally, it's checking this against a list that is stored on the computer itself.
Therefore, the only practical solution at the moment to bypass HSTS is to make the browser think that it is loading another website.

-To do this, we're going to replace all HSTS links in loaded pages with similar links, but they are not the same links. For example, we can replace facebook.com with facebook.corn
Now, I know this seems very suspicious, but trust me, when it goes into the URL bar, the RN in the middle will seem very similar to the M letter.
Another way of doing this is replacing twitter.com with twiter.com, but with a single T instead of a double T.
I know this sounds a little bit confusing right now, but let me go and do it practically, and you will see how this is going to work.
So, right here, I have my Kali machine, and we're actually going to use the HSTS caplet that we used in the previous lecture.
As mentioned in the previous lecture, this caplet is already installed in the custom Kali. If you want to use it with the original Kali, you'll have to manually download it and place it in the right path:
/usr/local/share/bettercap/caplets/

-If we go inside it, we have a file called hstshijack.cap. This is the configuration file of the caplet, so I'm going to right-click it, open it with Other Application, click on View All Applications, and pick any text editor that I have.
So, I'm going to keep this at Leaf pad. we have a normal text file with all the configurations that we can set, and I've already pre-configured this for you.
The main things that you want to understand and maybe change are the targets and the replacements. Targets are the domains that use HSTS that you want to replace.
For example, I have twitter.com in here, and I also have *.twitter.com. The asterisk (*) is a wildcard, meaning any subdomain of twitter.com is also a target.
Replacements tell the program what to replace the target with.
For example, whenever we see twitter.com, we're going to replace it with twitter.corn. The same applies to Facebook, Apple, and a few other domains that I set.
You can also play around with the obfuscate and encode options. I've set both of these to false because some browsers, like Firefox, will block obfuscated or encoded code.
That's why I set both of these to false, so the code is left as is.
Here in the payloads, you can set any other JavaScript code that you want to inject. Leave this as is; we'll talk about JavaScript injection in a future lecture.
Finally, you want to make sure that the dns.spoof.domains are set exactly the same as the replacements in here. So, I literally copy replacements line and paste it here.


-Running this attack is actually going to be identical to what we did in the previous lecture. You just want to make sure you modify this file properly. So, going back to Bettercap, run Bettercap with the same command, loading the spoof caplet, so we can do all of the ARP spoofing commands and run the sniffer automatically. And perfect! As you can see, everything is running as expected with no errors. If you run this and get an error, just type Exit and run Bettercap again.
Next, we want to run the caplet, the HSTShijack caplet, exactly as shown in the previous lecture.
All we have to do is type HS, press Tab, it will autocomplete for us, and hit Enter to run it.

-So, I have my Windows machine right here. This is Chrome, the latest version, and before I do anything, like I said, it's a good idea to always just remove the browsing data.
Before I actually load any websites, it is very important to understand that even with everything we are doing right now, if you try to go to Facebook and type .com at the end here, it will not work. What we're doing right now will not work because Chrome has a list stored on this computer that says: "Do not load facebook.com unless it is loaded over HTTPS."
So, if you type facebook.com directly, it will not work.
The only way we can do this is if the user first goes to a search engine, for example, Google.ie (for Ireland). Google doesn’t use HSTS, so we bypass this using the normal HTTPS bypass.
Then, if the user searches for Facebook, our script will run in the background and replace all links on this page for facebook.com with facebook.corn.
If I actually hover over this, you'll see in the status bar that the website being loaded is facebook.corn, not facebook.com.
This is fine because in the HTML code, Facebook.com got replaced with facebook.corn. If I click on this link, you’ll see we get a normal Facebook page.
But if you look at the top, you'll notice there is no HTTPS, and if you check the domain name, you'll see it says .corn instead of .com.
Now, once we're here, we can log in normally with: Username, Password 
Hit Enter, and if we go back... Scroll up.
Username, Password Like I said, the only way for this to work is if the user gets to Facebook through another website that does not use HSTS. If they type facebook.com directly, we will not be able to do this.
That's why this is considered a partial solution and not a full solution.

this is hstshijack.cap content if there is a problem:
set hstshijack.log             /usr/local/share/bettercap/caplets/hstshijack/ssl.log
set hstshijack.ignore          *
set hstshijack.targets         twitter.com,*.twitter.com,facebook.com,*.facebook.com,apple.com,*.apple.com,ebay.com,*.ebay.com,*.instagram.com,instagram.com,*.github.com,github.com,*.tiktok.com,tiktok.com,amazon.com,*.amazon.com
set hstshijack.replacements    twitter.corn,*.twitter.corn,facebook.corn,*.facebook.corn,apple.corn,*.apple.corn,ebay.corn,*.ebay.corn,*.instagram.corn,instagram.corn,*.github.corn,github.corn,*.tiktok.corn,tiktok.corn,amazon.corn,*.amazon.corn
set hstshijack.obfuscate       false
set hstshijack.encode          false
set hstshijack.payloads        *:/usr/local/share/bettercap/caplets/hstshijack/payloads/keylogger.js 


set http.proxy.script  /usr/local/share/bettercap/caplets/hstshijack/hstshijack.js
set dns.spoof.domains  twitter.corn,*.twitter.corn,facebook.corn,*.facebook.corn,apple.corn,*.apple.corn,ebay.corn,*.ebay.corn,*.instagram.corn,instagram.corn,*.github.corn,github.corn,linkedin.com,*.linkedin.com,stackoverflow.com,*.stackoverflow.com,google.ie,*.google.ie,winzip.com,*.winzip.com,avg.com,*.avg.com,tiktok.corn,*.tiktok.corn,bbc.com,*.bbc.com,cnn.com,*.cnn.com,microsoft.com,*.microsoft.com,reddit.com,*.reddit.com,amazon.corn,*.amazon.corn

http.proxy  on
dns.spoof   on
</p>
    </main>

    <!-- Back Button -->
    <a href="attack_mitm_bypassing_hsts.php" class="back-button">← Back</a>

    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
