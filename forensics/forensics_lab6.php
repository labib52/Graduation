<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab6</title>
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

        h1,
        h2,
        h3,
        h4,
        h5 {
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
<h1>Challenge Description</h1>
        <p>The flag format for this lab is: inctf{s0me_l33t_Str1ng}</p>
<p><b>Note: </b>This challenge is composed of only 1 flag. </p>
<br>
<p><b>The flag format for this lab is: inctf{s0me_l33t_Str1ng}</b></p>
<p><b>Challenge file:</b><a
                href="https://mega.nz/file/C0pjUKxI#LnedePAfsJvFgD-Uaa4-f1Tu0kl5bFDzW6Mn2Ng6pnM">MemLabs Lab6</a></p>
    <br><br>
    <p>-First we need to identify the operating system of the memory image.</p>
    <br>
    <p><b>$ volatility -f MemoryDump_Lab6.raw imageinfo</b></p>
    <br>
    <img src="../public/images/lab61.png" style="height: 300px;" width="500px" alt="">
    <br><br>
    <p>-Next, let’s check the running processes.</p>
    <br>
    <p><b>$ volatility -f MemoryDump_Lab6.raw --profile Win7SP1x64 pslist</b></p>
    <br>
    <img src="../public/images/lab62.png" style="height: 400px;" width="500px" alt="">
    <br>
    <p>-We can see some interesting processes here like WinRAR, chrome and firefox so let’s start with WinRAR.</p>
    <br>
    <p><b>$ volatility --plugins=plugins/ -f MemoryDump_Lab6.raw --profile Win7SP1x64 cmdline | grep WinRAR.exe <br>
Volatility Foundation Volatility Framework 2.6.1 <br>
WinRAR.exe pid:   3716 <br>
Command line : "C:\Program Files\WinRAR\WinRAR.exe" "C:\Users\Jaffa\Desktop\pr0t3ct3d\flag.rar"</b></p>
<br><br>
<p>-Oh, that file name is interesting, let’s dump it.</p>
<p><b>$ volatility -f MemoryDump_Lab6.raw --profile Win7SP1x64 filescan | grep flag.rar <br>
0x000000005fcfc4b0     16      0 R--rwd \Device\HarddiskVolume2\Users\Jaffa\Desktop\pr0t3ct3d\flag.rar <br>

$ volatility -f MemoryDump_Lab6.raw --profile Win7SP1x64 dumpfiles -Q 0x000000005fcfc4b0 -D lab6_output/ <br>
Volatility Foundation Volatility Framework 2.6.1 <br>
DataSectionObject 0x5fcfc4b0   None   \Device\HarddiskVolume2\Users\Jaffa\Desktop\pr0t3ct3d\flag.rar</b></p>
<br><br>
<p>-Next, let’s try to unrar it.</p>
<br>
<p><b>$ unrar e flag.rar <br>
UNRAR 5.61 beta 1 freeware      Copyright (c) 1993-2018 Alexander Roshal <br>
Extracting from flag.rar <br>
Enter password (will not be echoed) for flag2.png:</b></p>
<br><br>
<p>-Of course it’s encrypted :( <br>

Let’s take a step back and try more plugins.</p>
<br>
<p><b>$ volatility --plugins=plugins/ -f MemoryDump_Lab6.raw --profile Win7SP1x64 consoles</b></p>
<br><br>
<img src="../public/images/lab63.png" style="height: 400px;" width="500px" alt="">
<br><br>
<p> -noticed the author is running env command, I suspect it’s a hint for us.
<br>
-So let’s try dumping the environment variables for WinRAR.</p>
<br>
<img src="../public/images/lab64.png" style="height: 400px;" width="500px" alt="">
<br><br>
<p>-Awesome, not we now that the rar password is: easypeasyvirus</p>
<br>
<p><b>$ unrar e flag.rar <br>
UNRAR 5.61 beta 1 freeware      Copyright (c) 1993-2018 Alexander Roshal <br>
Extracting from flag.rar <br>
Enter password (will not be echoed) for flag2.png: <br>
Extracting  flag2.png                                                 OK <br>
All OK <br>
</b></p>
<img src="../public/images/lab65.png" alt="">
<br><br>
<p>-Great, that looks like the second half of the flag</p>
<br>
<p><b>Second half: aN_Am4zINg_!_i_gU3Ss???_}</b></p>
<p>Let’s return back the the chrome process, the first thing is to check the browsing history</p>
<br>
<p>This amazing github repo has the plugin we need</p>
<a href="https://github.com/superponible/volatility-plugins">Volatility-plugins</a>
<br>
<p><b>$ volatility --plugins=plugins/ -f MemoryDump_Lab6.raw --profile Win7SP1x64 chromehistory > chromehistory.txt</b></p>
<br><br>
<img src="../public/images/lab66.png" style="height: 400px;" width="500px" alt="">
<br>
<br>
<p>-Here is what I found.</p>

<img src="../public/images/lab67.png"  style="height: 400px;" width="500px" alt="">
<br>
<p>-There is a link to a google drive doc along with the note David sent the key in mail.</p>
<br>
<p>-The doc file is just some lorem ipsum text, but if you look carefully you can see a mega link (took me a while).</p>

<img src="../public/images/lab68.png" style="height: 400px;" width="500px" alt="">
<br><br>
<p>-Let’s see what this mega link has.</p>
<br>
<img src="../public/images/lab69.png" style="height: 400px;" width="500px" alt="">
<br><br>
<p>Another password, I hate my life :( <br>

At this point I got stuck, so I tried every volatility plugin I know about. Then the magic happened. <br>

The screenshot plugin saved the day
</p>
<br>
<p>-It dumped 13 images, all of them are just white images except for this one.</p>
<br>
<img src="../public/images/lab610.png" style="height: 400px;" width="500px" alt="">
<br>
<p>-There is a windows with the title Mega Drive Key ...., that looks promising. so let’s search for this string in memory.</p>
<br>
<p><b>$ strings MemoryDump_Lab6.raw | grep "Mega Drive Key" <br>
.........
Mega Drive Key - davidbenjamin939@gmail.com - Gmail <br>
top['GM_TRACING_THREAD_DETAILS_CHUNK_START'] = (window.performance && window.performance.now) ? window.performance.now() : null; top._GM_setData <br>({"Cl6csf":[["simls",0,"{\"2\":[{\"1\":0,\"2\":{\"1\":\"Mega Drive Key\",\"2\":\"THE KEY IS zyWxCjCYYSEMA-hZe552qWVXiPwa5TecODbjnsscMIU\"
<br>........</b></p>
<br>
<P>-Look at that, we got the key (a good pair of eyes required). the key is:<b>zyWxCjCYYSEMA-hZe552qWVXiPwa5TecODbjnsscMIU.</b></P>

<p>-After decrypting the file, it turned out to be an image. but unfortunately it was corrupted</p>
<br>
<p>-Opening it with hexedit, the IHDR part was corrupted (iHDR). so all we need to do is to change i (69) to I (49).</p>
<img src="../public/images/lab611.png"  style="height: 400px;" width="500px" alt="">
<br>
<p>-Finally we got the first part of the flag, that was a long journey</p>
<img src="../public/images/lab612.png" style="height: 400px;" width="500px" alt="">
<br>
<p><b>Flag: inctf{thi5cH4LL3Ng3_!s_g0nn4_b3_?_aN_Am4zINg_!_i_gU3Ss???}</b></p>
<br><br>
<a href="../forensics.php" class="back-button">← Back</a>
</body>