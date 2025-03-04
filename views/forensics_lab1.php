<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab1</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/CSS/foresnics_lab.css">
</head>

<body>
    <div class="container">
        <h1>Challenge Description</h1>
        <p>My sister’s computer crashed. We were very fortunate to recover this memory dump. Your job is get all her
            important files from the system. From what we remember, we suddenly saw a black window pop up with some
            thing being executed. When the crash happened, she was trying to draw something. Thats all we remember from
            the time of crash.</p>
        <h3>Volatility installer :</h3>
        <p><b>Note: </b>First of all, you need to install <a
                href="https://letsdefend.io/blog/how-to-install-volatility-2-and-volatility-3-on-linux">Volatility 2</a>
        </p>


        <p>This challenge is composed of 3 flags.</p>
        <p><b>Challenge file:</b><a
                href="https://mega.nz/#!6l4BhKIb!l8ATZoliB_ULlvlkESwkPiXAETJEF7p91Gf9CWuQI70">MemLabs Lab1</a></p>
    </div>
    <div class="container">
        <h2>First Flag :</h2>
        <p>The first thing to do with a memory dump file is to identify the operating system, for that we use imageinfo
            plugin.</p>
        <p><b> $ volatility -f MemoryDump_Lab1.raw imageinfo</b></p>
        <p>We can see volatility has a lot of suggestions for the profile, usually the first one is sufficient.</p>
        <p>Next we check the running processes using <b>pslist</b> plugin.</p>
        <p><b> $volatility -f MemoryDump_Lab1.raw --profile Win7SP1x64 pslist</b></p>
        <p>There are 3 interesting processes here, let’s start with <b>cmd.exe</b>. This process indicates that
            commands were executed on the system.</p>
        <p>We can use <b>consoles</b> plugin to see the output</p>
        <p><b> $ volatility -f MemoryDump_Lab1.raw --profile Win7SP1x64 consoles</b></p>
        <p>If you look closely to the output of the command <b>St4G3$1</b>, you can spot some <b>Base64</b> text. If we
            decode it we get the flag of stage 1.</p>
        <p><b> $ echo ZmxhZ3t0aDFzXzFzX3RoM18xc3Rfc3Q0ZzMhIX0= | base64 -d</b></p>
        <br>
        <p><b>Result : flag{th1s_1s_th3_1st_st4g3!!}</b></p>
        <p><b style="color: red;">Great, first stage is done.</b></p>
    </div>
    <div class="container">
        <h2>Second Flag : </h2>
        <h3>Next we will focus on the second interesting process, which is<b>mspaint.exe</b>. The PID of this process is
            2424.</h3>
        <p>If you go back to the challenge description, we can see that the user was drawing something (using mspaint of
            course).</p>
        <br>
        <p>After some googling, I found that we can dump the mspaint’s process memory to extract the image back.</p>
        <br>
        <p>So let’s use <b>memdump</b> plugin to extract some data.</p>
        <p><b>$ volatility -f MemoryDump_Lab1.raw --profile Win7SP1x64 memdump -p 2424 -D lab1_output/</b></p>
        <p>The output is written to <b>2424.dmp</b>, we need to rename it to <b>2424.data</b> to be able to open it in
            Gimp.</p>
        <br>
        <p>After playing a bit with the width and offset. I got an image which is somewhat flipped. I rotated it 180
            degrees then flipped it horizontally and Voila!, I got the flag.</p>
        <br>
        <img src="../public/images/lab1flag2.png" alt="" width="700" height="400">
        <img src="../public/images/lab1flag22.png" alt="" width="700" height="400">
        <p><b style="color: red;">Flag 2: flag{G00d_Boy_good_girL}</b></p>
    </div>

    <div class="container">
        <h2>Third Flag: </h2>
        <p>The third interesting process is <b>WinRAR.exe</b> with PID 1512, we can use cmdline plugin to see the
            associated command line.</p>
        <p><b>$ volatility -f MemoryDump_Lab1.raw --profile Win7SP1x64 cmdline | grep WinRAR</b></p>
        <br>
        <p>Great, we got the name of the rar file which is <b>Important.rar</b> (looks important).</p>
        <br>
        <p>Next we can use <b>filescan</b> plugin to get the psychical offset of that file in memory.</p>
        <br>
        <p><b>$ volatility -f MemoryDump_Lab1.raw --profile Win7SP1x64 filescan | grep Important.rar</b></p>
        <p>We can pick any of these offsets, To dump the file we can use <b>dumpfiles</b> plugin.</p>
        <p><b>$ volatility -f MemoryDump_Lab1.raw --profile Win7SP1x64 dumpfiles -Q 0x000000003fa3ebc0 -D
                lab1_output/</b></p>
        <br>
        <p>The file is dumped under the name <b>file.None.0xfffffa8001034450.dat</b>, let’s rename and unrar it.</p>
        <p><b>$ mv file.None.0xfffffa8001034450.dat Important.rar</b></p>
        <p><b>$ unrar e Important.rar</b></p>

        <br>
        <p>The file is password protected, but we can see a comment that says the password is the NTLM hash of Alissa’s
            account passwd.</p>
        <p>To get the password hash, we can use <b>hashdump</b> plugin.</p>
        <p><b>$ volatility -f MemoryDump_Lab1.raw --profile Win7SP1x64 hashdump</b></p>
        <br>
        <h3>Windows stores two hashes with each password, delimited by colons. The first one is an extremely insecure,
            obsolete hash using the LANMAN algorithm. Windows operating systems since Vista no longer use LANMAN hashes,
            so they are filled with a dummy value starting with “aad”.

            The second hash is the newer NTLM hash, which is much better than LANMAN hashes, but still extremely
            insecure and much more easily cracked than Linux or Mac OS X hashes.</h3>
<br>
        <p>The desired NTLM hash is <b>f4ff64c8baac57d22f22edc681055ba6</b> (remember it must be in uppercase).</p>
        <p>After decompressing the file, we get an image with the flag.</p>
        <p><b style="color:red">Flag 3: flag{w3ll_3rd_stage_was_easy}</b></p>
        <!-- Back Button -->
        <a href="../forensics.php" class="back-button">← Back</a>
    </div>

</body>

</html>
