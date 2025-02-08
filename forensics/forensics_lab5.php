<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab1</title>
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
            text-align: center;
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
        .imglab5{
            text-align: center;
        }
</style>
</head>
<body>
<body>
    <div class="container">
        <h1>Challenge Description</h1>
        <p>This challenge is composed of 2 flags but do you really think so? Maybe a little flag is hiding somewhere.</p>
        <p><b>Note: </b>There was a small mistake when making this challenge. If you find any string which has the string “L4B_3_D0n3*!!**” in it, please change it to “L4B_5_D0n3*!!**” and then proceed. </p>
        <p><b>Hint: </b>You’ll get the stage 2 flag only when you have the stage 1 flag.  </p>
        <br>
        <p>You will need this additional tool to solve the challenge,</p>
        <p><b>$ sudo apt install steghide</b></p>
        <p><b>Challenge file:</b><a
                href="https://mega.nz/file/Ps5ViIqZ#UQtKmUuKUcqqtt6elP_9OJtnAbpwwMD7lVKN1iWGoec">MemLabs Lab5</a></p>
    </div>
    <div class="container">
        <h2>First Flag:</h2>
        <p>-First we need to identify the operating system of the memory image.</p>
        <p><b>$ volatility -f MemoryDump_Lab5.raw imageinfo</b></p>
        <p>-Next, let’s check the command line of the running processes.</p>
        <p><b>$ volatility -f MemoryDump_Lab5.raw --profile Win7SP1x64 pslist</b></p>
        <p>-Interesting, there’s a WinRAR.exe process, let’s see what the cmdline for that process is</p>
        <p><b>$ volatility -f MemoryDump_Lab5.raw --profile Win7SP1x64 cmdline | grep WinRAR.exe
Volatility Foundation Volatility Framework 2.6.1
WinRAR.exe pid:   2924
Command line : "C:\Program Files\WinRAR\WinRAR.exe" "C:\Users\SmartNet\Documents\SW1wb3J0YW50.rar"</b></p>
<br><br>
<p>-The rar file name is SW1wb3J0YW50.rar, let’s dump this file.</p>
        <p><b>$ volatility -f MemoryDump_Lab5.raw --profile Win7SP1x64 filescan | grep SW1wb3J0YW50.rar <br>
Volatility Foundation Volatility Framework 2.6.1 <br>
0x000000003eed56f0      1      0 R--r-- \Device\HarddiskVolume2\Users\SmartNet\Documents\SW1wb3J0YW50.rar <br></b></p>
<p>$ volatility -f MemoryDump_Lab5.raw --profile Win7SP1x64 dumpfiles -Q 0x000000003eed56f0 -D lab5_output/ <br></p>
<p><b>$ volatility -f MemoryDump_Lab5.raw --profile Win7SP1x64 filescan | grep SW1wb3J0YW50.rar <br>
Volatility Foundation Volatility Framework 2.6.1 <br>
DataSectionObject 0x3eed56f0   None   \Device\HarddiskVolume2\Users\SmartNet\Documents\SW1wb3J0YW50.rar<br></b></p>
<br>
<br>
<p>-Going ahead to unrar this file and I saw this comment.</p>
<br>
<p>
            <b>$ unrar e SW1wb3J0YW50.rar<br>
            UNRAR 5.61 beta 1 freeware      Copyright (c) 1993-2018 Alexander Roshal <br>
            Extracting from SW1wb3J0YW50.rar <br>
            Enter password (will not be echoed) for Stage2.png:<br>
           <br>
            </b>
        </p>
<p>-Clearly this is stage2’s flag and the password for this file is stage1’s flag, so we need to get stage1’s flag first.</p> 
<br>
<p>-At this point I had no clue of what to do, so I tried my luck with iehistory (I explained it in the previous lab) and I notices something interesting.</p>
<br><br>
<p><b>
$ volatility -f MemoryDump_Lab5.raw --profile Win7SP1x64 iehistory <br>
......... <br>
Process: 1396 explorer.exe <br>
Cache type "URL " at 0x28c5900 <br>
Record length: 0x100 <br>
Location: Visited: Alissa Simpson@file:///C:/Users/Alissa%20Simpson/Pictures/ <br> ZmxhZ3shIV93M0xMX2QwbjNfU3Q0ZzMtMV8wZl9MNEJfM19EMG4zXyEhfQ.bmp <br>
......... <br>
Process: 1396 explorer.exe <br>
Cache type "URL " at 0x28c5a00 <br>
Record length: 0x100 <br>
Location: Visited: Alissa Simpson@file:///C:/Users/Alissa%20Simpson/Pictures/ZmxhZ3shI <br> V93M0xMX2QwbjNfU3Q0ZzMtMV8wZl9MNEJfNV9EMG4zXyEhfQ.bmp <br>
......... <br>
Process: 1396 explorer.exe <br>
Cache type "URL " at 0x28c5c00 <br>
Record length: 0x100 <br>
Location: Visited: Alissa Simpson@file:///C:/Windows/AppPatch/ <br> ZmxhZ3shIV93M0xMX2QwbjNfU3Q0ZzMtMV8wZl9MNEJfNV9EMG4zXyEhfQ.bmp <br>
</b></p>  
<br>
<br>
<p>-This .bmp file is repeated multiple times and it’s names looks like Base64 string, so I tries to decode it.</p>  
<br>
<p><b>$ echo ZmxhZ3shIV93M0xMX2QwbjNfU3Q0ZzMtMV8wZl9MNEJfNV9EMG4zXyEhfQ | base64 -d <br>
flag{!!_w3LL_d0n3_St4g3-1_0f_L4B_5_D0n3_!!}</b></p>
<br>
<p>Voila! we got the flag of stage1.</p> 
<br>
<p><b>Flag 1: flag{!!w3LL_d0n3_St4g3-1_0f_L4B_5_D0n3!!}</b></p>
<h2>Second Flag</h2>
<br>
<br>
<p>-Now let’s return back to the rar file.</p>
<br>
<p><b>
$ unrar e SW1wb3J0YW50.rar <br>
UNRAR 5.61 beta 1 freeware      Copyright (c) 1993-2018 Alexander Roshal
Extracting from SW1wb3J0YW50.rar <br>
Enter password (will not be echoed) for Stage2.png: <br>
Extracting  Stage2.png                                                OK <br>
All OK 
</b></p>
<br> <br>
<div class="imglab5">
<img src="../public/images/lab5.png" style="height: 400px;" width="600px" alt="">
</div>
<br>
<div><p><b>Flag 2: flag{W1th_th1s_$taGe_2_1s_c0mPL3T3_!!}</b></p></div>
<br><br>
<a href="../forensics.php" class="back-button">← Back</a>
</body>
