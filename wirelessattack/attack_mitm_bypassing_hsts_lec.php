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
        <h1>Bypassing HSTS - Lecture</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Navigation</h2>
        <a href="#introduction"><i class="fas fa-info-circle"></i> 1. Introduction</a>
        <a href="#setup"><i class="fas fa-cogs"></i> 2. Setup</a>
        <a href="#execution"><i class="fas fa-play"></i> 3. Execution</a>
        <a href="#limitations"><i class="fas fa-ban"></i> 4. Limitations</a>
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
            <p>-In the previous lecture, we've seen how to downgrade HTTPS websites to HTTP, and this allowed us to basically see anything a user does on these websites because data in HTTP is sent in plain text. Therefore, we were able to see the usernames, the passwords, the URLs, and anything they do on HTTPS websites. At the end of the lecture, I also showed you that the method will not work against Facebook, Twitter, and other websites that use HSTS.

-The reason why it won't work against these websites is because modern web browsers come with a list of websites that they should only load over HTTPS. See, what we were doing in the previous lecture—whenever a browser requests a website, we load that website, even if it uses HTTPS, but we always give it back the HTTP version.
In HSTS, the browser knows that this website, for example, facebook.com, should always be loaded over HTTPS. So, even before sending this request to us, it'll always send it in HTTPS, and it'll always only accept it if it comes back as HTTPS.
So, there is nothing we can really do once we become the man in the middle. Because the browser is doing this check locally, it's checking this against a list that is stored on the computer itself.
Therefore, the only practical solution at the moment to bypass HSTS is to make the browser think that it is loading another website.</p>
        </div>
        <div id="setup" class="section">
            <h2>2. Setup</h2>
            <p>-To do this, we're going to replace all HSTS links in loaded pages with similar links, but they are not the same links. For example, we can replace facebook.com with facebook.corn
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
</p>
        </div>
        <div id="execution" class="section">
            <h2>3. Execution</h2>
            <p>-So, I have my Windows machine right here. This is Chrome, the latest version, and before I do anything, like I said, it's a good idea to always just remove the browsing data.
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
That's why this is considered a partial solution and not a full solution.</p>
        </div>
        <div id="limitations" class="section">
            <h2>4. Limitations</h2>
            <p>this is hstshijack.cap content if there is a problem:
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
dns.spoof   on</p>
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
