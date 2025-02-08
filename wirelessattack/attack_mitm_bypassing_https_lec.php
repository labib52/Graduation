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
    <title>Bypassing HTTPS - Lecture</title>
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
        <h1>Bypassing HTTPS - Lecture</h1>
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
        <a href="#examples"><i class="fas fa-globe"></i> 4. Examples</a>
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
            <p>-Now, everything that we did so far will only work against HTTP pages. The reason why it works against HTTP, because as we've seen, the data in HTTP is sent as plain text. So it's text that human like us can read and understand. That's why when we're the man in the middle, we're able to read this text and if we wanted, we're able to modify this text as we wish.
Now, this is obviously a problem and this problem was fixed in HTPPS. So as you know, most websites use HTTPS. The reason why, like I said, because it's a more secure version of HTTP and basically the way it works is, it adds an extra layer over HTTP, which is where the S comes from. So it's a secure HTTP protocol and this extra layer will encrypt the plain text data
that HTTP sends. So if a person manages to become the man in the middle, they will be able to read this data, but the data will be gibberish. It will not be readable to the person intercepting the connection. Now, HTTPS relies on TLS or SSL to encrypt the data and this is very difficult to break.
</p>
        </div>
        <div id="setup" class="section">
            <h2>2. Setup</h2>
            <p>-Therefore, in order to bypass this, the easiest method is to downgrade HTTPS connections to HTTP.
So since we're the man in the middle, we can check is the target is requesting a HTTPS website and instead of giving him the HTTPS version of that website, we will give him the HTTP version. This way, the data will be sent in plain text and we'll be able to read it exactly as I showed you in the previous lecture.
Luckily, Bettercap has a caplet that does all of that for us.
I can simply run Bettercap and use it, but before doing that, I just want to go to the home directory and modify the spoof caplet that we have been using in the previous lectures.
I just wanna modify one thing in this, so I'm gonna right click it and open it with Leaf pad and what I wanna modify is,
I want to add an option to the sniff in here. So as you know, this line, net.sniff on will turn on my sniffer, but before turning it on,
I want to set the net.sniff.local to true.
*command: set net.sniff.local true.
if the caplet option doesn't work with it is normal bug you can just write all the previous commands of the caplet manually 
And what this option will do, it will tell Bettercap to sniff all data, even if it thinks this data is local data. The reason why I set this option to true, because once we use the HTTPS bypass caplet, the data will seem as if it's being sent from our computer. So Bettercap will think these passwords belong to me, to my computer, and it will not display it to me on screen
That's why we're setting it to true so that we can see all the usernames and the passwords sent on the websites that we will downgrade from HTTPS to HTTP.</p>
        </div>
        <div id="execution" class="section">
            <h2>3. Execution</h2>
            <p>-So I'm gonna go to my terminal and I'm gonna use Bettercap, exactly as I've been using it before.
So we're doing bettercap, the name of the program, we're giving it our interface after the iface argument. We're using the caplet argument to specify a caplet to run
as soon as we run the program and we're running this spoof caplet, the one that we built in the previous lecture that run the ARP spoofing command and run the sniffer for us.
If we do help, we'll see all the running modules and we have the ARP spoof and the sniffer running with the recon and with the probe.
So this is exactly what we wanted from our caplet.

-So first of all, the HSTS bypass caplet is one of many caplets that Bettercap comes with. If you want to list all of these caplets you can do caplets.show and as you can see, you'll get a list of all of the caplets that you have and their location on the system.
Now, the caplet that we want to run is the hstshijack caplet, this one right here. And to run any of these caplets, all you have to do is literally just type its name.
So as you can see, because we don't see any errors, this means everything got executed as expected,
so let's go the Windows machine, browse some HTTPS pages and see if we can sniff data, usernames, passwords and URLS that they enter on their computer.

-So I have my Windows machine here. I have Chrome installed. This is the latest version of Chrome
A really good idea before trying all of these things is to removes your browsing data, because the websites that we're gonna try to access might be cached and they might be just loaded from your cache.
This will only happen if you're visiting the same website over and over again, mostly when testing, therefor, it's a really good idea to CONTROL + SHIFT + DELETE and click on clear browsing data, make sure all of this is clicked. Make sure it's set to all the time and click on clear to remove all of it and let's go ahead and go to a website that uses HTTPS.</p>
        </div>
        <div id="examples" class="section">
            <h2>4. Examples</h2>
            <p>-A good example would be linkedin.com. And perfect, if you look here at the top, you'll see the website is loading over HTTP, not over HTTPS, therefor, we'll be able to see anything
the user enters in these boxes. So let's put a username and any password doesn't really matter, you can use any password.
And I'm gonna hit ENTER to log in. This is wrong, so obviously we're getting an error message,
but if we go back to Kali, as you can see, we're capturing all of this data, because it's not being sent over HTTPS anymore. It's being sent over HTTP. And if you look in here,
you can see we captured log in information. It's sent to linkedin.com, sent to this specific URL, our log in URL
and you can see the username and the password

-Let's go ahead and test another HTTPS website. Let's go to stackoverflow.com. Again, you can see on top, it's loading over HTTP, not HTTPS. So I'm gonna click on log i
and again, I'm gonna put any email and password
let's go to the Kali machine again.
Scroll down this time, 'cause we're stuck on top. And perfect, you can see we have a post request in here. It's sent to this specific URL. Again, you can see log in the URL.
You can see the website itself, stackoverflow.com and if we scroll down a little bit more, we can see that the username and the password again

-Now we can downgrade any HTTPS connection to HTTP as long as the target website uses HTTPS not HSTS. So this method will work against pretty much all websites
that use HTTPS, except for the really popular websites, such as Facebook, Twitter and so on.
This is happening because Facebook is using HSTS, which is a little bit trickier to bypass.
In the next lecture, we'll talk more about what HSTS is, why it's tricky to bypass and how to partially bypass it and still get usernames and passwords from the websites that implement it,
such as Facebook, Twitter and so on.</p>
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
