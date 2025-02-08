<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab2</title>
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
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        h1, h2 ,h3,h4,h5{
            color: #007BFF;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 1.5rem;
            margin-top: 20px;
        }
        

        p {
            margin: 10px 0;
        }

        ul {
            margin: 10px 0 10px 20px;
        }

        ul li {
            margin: 5px 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .nav-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background-color: #007BFF;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        .nav-btn:hover {
            background-color: #0056b3;
        }

        .nav-btn span {
            margin: 0 5px;
        }

        .arrow {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .arrow-back {
            margin-right: 10px;
        }

        .arrow-next {
            margin-left: 10px;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Challenge Description</h1>
        <p>One of the clients of our company, lost the access to his system due to an unknown error. He is supposedly a very popular “environmental” activist. As a part of the investigation, he told us that his go to applications are browsers, his password managers etc. We hope that you can dig into this memory dump and find his important stuff and give it back to us.</p>
        <h3>Volatility installer :</h3>
        <p><b>Note: </b>First of all, you need to install <a href="https://letsdefend.io/blog/how-to-install-volatility-2-and-volatility-3-on-linux">Volatility 2</a></p>


<p>This challenge is composed of 3 flags.</p>
<p><b>Challenge file:</b><a href="https://mega.nz/#!ChoDHaja!1XvuQd49c7-7kgJvPXIEAst-NXi8L3ggwienE1uoZTk">MemLabs Lab2</a></p>
</div>
<div class="container">
    <h2>First Flag :</h2>
        <h3>First we need to identify the operating system of the memory image.</h3>
        <p><b>  $ volatility -f MemoryDump_Lab2.raw imageinfo</b></p>
        <h3>Next, let’s check the processes list.</h3>
        <p><b>  $ volatility -f MemoryDump_Lab2.raw --profile Win7SP1x64 pslist</b></p>
        <h3>We can see interesting processes like chrome and KeePass. but first let’s look back at the description, note the quoted word "environmental". I think it’s a hint for environment variables, so let’s go down this way first.</h3>
        <p><b>  $ volatility -f MemoryDump_Lab2.raw --profile Win7SP1x64 envars</b></p>
        <h3>We can see the environment variable NEW_TMP in every process with a value that looks like Base64. so let’s decode it.</h3>
        <p><b>  $ echo ZmxhZ3t3M2xjMG0zX1QwXyRUNGczXyFfT2ZfTDRCXzJ9 | base64 -d</b></p>
        <br>
        <p><b>Result : flag{w3lc0m3_T0_$T4g3_!_Of_L4B_2}</b></p>
        <p><b style="color: red;">Great, first stage is done.</b></p>
    </div>
    <div class="container">
        <h2>Second Flag : </h2>
        <h3>Next, let’s check this KeePass process, looks like a password manager.</h3>
        <p>After some googling, I learned that KeePass stores the passwords in a database with the extension ".kdbx" and looks it with a master password</p>
        <p>So let’s check if this database is in memory.</p>
        <p><b>$ volatility -f MemoryDump_Lab2.raw --profile Win7SP1x64 filescan | grep ".kdbx"</b></p>
        <p>And here it’s, now let’s dump it.</p>
        <p><b>$ volatility -f MemoryDump_Lab2.raw --profile Win7SP1x64 dumpfiles -Q 0x000000003fb112a0 -D lab2_output/</b></p>
        <p>The only thing left is to get the master password, I tried scanning files for any password like file</p>
        <p><b>$ volatility -f MemoryDump_Lab2.raw --profile Win7SP1x64 filescan | grep -i "password"</b></p>
        <p>Look at that, an image named Password!!! looks interesting, let’s dump it.</p>
        <p><b>$ volatility -f MemoryDump_Lab2.raw --profile Win7SP1x64 dumpfiles -Q 0x000000003fce1c70 -D lab2_output/</b></p>
        <br>
        <p><b>Note: </b>If you look closely at the bottom right, you can spot the password.</p>
        <img src="../public/images/flag2.png" alt="">

        <p><b>Now let’s use this password to open the database in KeePass.</b></p>
        <img src="../public/images/flag22.png" alt=""><img src="../public/images/flag222.png" alt="">
        <br>
        <p><b style="color: red;">Flag 2: flag{w0w_th1s_1s_Th3SeC0nD_ST4g3!!}</b></p>
    </div>

    <div class="container">
<h2>Third Flag: </h2>
<h3>Now let’s return back the the chrome process, the first thing is to check the browsing history.</h3>
<p><b>volatility --plugins=plugins/ -f MemoryDump_Lab2.raw --profile Win7SP1x64 chromehistory > chromehistory.txt</b></p>
<br>
<p>We have a mega link, the mega folder name is MemLabs_Lab2_Stage3 and it contained a single zip file named <b>Important.zip</b> (password protected).</p>
<br>
<p>I tried unzipping it with <b>unzip</b> but it gave me an error, so I used <b>7z</b>.</p>
<br>
<h4>Let’s get the password.</h4>
<p><b>$ echo -n flag{w3ll_3rd_stage_was_easy} | sha1sum</b></p>
<h4>After unzipping the file, I got this image.</h4>
<img src="public/images/flag3.png" alt="">

<br><p><b style="color:red">Flag 3: flag{oK_So_Now_St4g3_3_is_DoNE!!}</b></p>
 <!-- Back Button -->
 <a href="../forensics.php" class="back-button">← Back</a>
    </div>
    
</body>
</html> 
