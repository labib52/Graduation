<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab4</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/CSS/foresnics_lab.css">
</head>

<body>
    <div class="container">
        <h1>Challenge Description</h1>
        <p>My system was recently compromised. The Hacker stole a lot of information but he also deleted a very
            important file of mine. I have no idea on how to recover it. The only evidence we have, at this point of
            time is this memory dump. Please help me.</p>
        <p><b>Note: </b>This challenge is composed of only 1 flag. </p>
        <p><b>Hint: </b>The flag format for this lab is: inctf{s0me_l33t_Str1ng}</p>
        <br>
        <p><b>Challenge file:</b><a
                href="https://mega.nz/#!Tx41jC5K!ifdu9DUair0sHncj5QWImJovfxixcAY-gt72mCXmYrE">MemLabs Lab4</a></p>
    </div>
    <div class="container">
        <h2>Flag :</h2>
        <p>First we need to identify the operating system of the memory image.</p>
        <p><b> $ volatility -f MemoryDump_Lab4.raw imageinfo</b></p>
        <p>The next thing is to check running processes.</p>
        <p><b>$ volatility -f MemoryDump_Lab4.raw --profile Win7SP1x64 pslist</b></p>
        <p>The only interesting process here is <b>StikyNot.exe</b> (this is a rabbit hole, nothing important there).
        </p>
        <br>
        <p>Looking back at the challenge description, it says something about files and a deleted file. So we can use
            <b>filescan</b> to search for interesting files in memory, but for the sake of variety, I will use <b>iehistory</b> plugin
            instead.</p>
            <h4><b>iehistory</b> plugin recovers fragments of IE history index.dat cache files. It can find basic accessed links (via FTP or HTTP), redirected links (–REDR), and deleted entries (–LEAK). It applies to any process which loads and uses the wininet.dll library, not just Internet Explorer. Typically that includes Windows Explorer and even malware samples.</h4>
            <br>
            <p>so we can use it to view the history of visited files and directories by windows explorer.</p>
        <p><b> $ volatility -f MemoryDump_Lab4.raw --profile Win7SP1x64 iehistory</b></p>
        <br>
        <p>What do we have here, a text file that looks important!!!</p>
        <br>
        <p>Now let’s scan for this file in memory to dump it out.</p>
        <p><b>$ volatility -f MemoryDump_Lab4.raw --profile Win7SP1x64 filescan | grep Important.txt</b></p>
        <br>
        <p><b>$ volatility -f MemoryDump_Lab4.raw --profile Win7SP1x64 dumpfiles -Q 0x000000003fc398d0 -D lab4_output/</b></p>
        <p>Unfortunately, <b>dumpfiles</b> was not able to dump the text file (it was deleted by the hacker).</p>
        <p>We need to know a little bit about the MFT table to solve this challenge.</p>
        <br>
        <h3>The NTFS file system contains a file called the master file table, or MFT. There is at least one entry in the MFT for every file on an NTFS file system volume. All information about a file, including its name, size, time and date stamps, permissions, and data content, is stored either in MFT entries, or in space outside the MFT that is described by MFT entries</h3>
        <br><h3>As files are added to an NTFS file system volume, more entries are added to the MFT and the MFT increases in size. When files are deleted from an NTFS file system volume, their MFT entries are marked as free and may be reused. However, disk space that has been allocated for these entries is not reallocated, and the size of the MFT does not decrease.</h3>
        <br><h3>A file whose size is less than or equal to 1024 bytes will be stored directly in the MFT table (named “resident” file), if it exceeds 1024 bytes the table will only contain the information of its location (named “non-resident” file)</h3>
        <br>
        <p>So let’s search for <b>Important.txt</b> in the MFT table.</p>
        <p><b>$ volatility -f MemoryDump_Lab4.raw --profile Win7SP1x64 mftparser > mft.txt</b></p>
        <img src="../public/images/lab4flag1.png" alt="" width="750">
        <p><b style="color:red">Flag : inctf{1_is_n0t_EQu4l_7o_2_bUt_th1s_d0s3nt_m4ke_s3ns3}</b></p>
    
    

    <!-- Back Button -->
    <a href="../forensics.php" class="back-button">← Back</a>
    </div>

</body>

</html>
