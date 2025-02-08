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
        <p>A malicious script encrypted a very secret piece of information I had on my system. Can you recover the information for me please?</p>
        <p><b>Note: </b>This challenge is composed of only 1 flag and split into 2 parts.  </p>
        <p><b>Hint: </b>You’ll need the first half of the flag to get the second.  </p>
        <br>
        <p>You will need this additional tool to solve the challenge,</p>
        <p><b>$ sudo apt install steghide</b></p>
        <p><b>Challenge file:</b><a
                href="https://mega.nz/#!2ohlTAzL!1T5iGzhUWdn88zS1yrDJA06yUouZxC-VstzXFSRuzVg">MemLabs Lab3</a></p>
    </div>
    <div class="container">
        <h2>First half :</h2>
        <p>First we need to identify the operating system of the memory image.</p>
        <p><b> $ volatility -f MemoryDump_Lab3.raw imageinfo</b></p>
        <p>Next, let’s check the command line of the running processes.</p>
        <p><b>$ volatility -f MemoryDump_Lab3.raw --profile Win7SP1x86_23418 cmdline</b></p>
        <p>Interesting, we got two files. <b>evilscript.py</b> which as the name implies is evil and <b>vip.txt</b> which look like an important file.</p>
        <p>Let’s search for these two files in memory.</p>
        <p><b> $ volatility -f MemoryDump_Lab3.raw --profile Win7SP1x86_23418 filescan | egrep "evilscript.py|vip.txt"</b></p>
        <br>
        <p>Now that we have the offsets of the two files, let’s dump them.</p>
        <p><b>$ volatility -f MemoryDump_Lab3.raw --profile Win7SP1x86_23418 dumpfiles -Q 0x000000003de1b5f0 -D lab3_output/</b></p>
        <p><b>$ volatility -f MemoryDump_Lab3.raw --profile Win7SP1x86_23418 dumpfiles -Q 0x000000003de1b5f0 -D lab3_output</b></p>
        <p>Here is the dumped python file:</p>
        <p>
            <b>import sys<br>
            import string
            def xor(s): <br>
            a = ''.join(chr(ord(i)^3) for i in s) <br>
            return a <br>
            def encoder(x): <br>
	        return x.encode("base64") <br>

            if __name__ == "__main__": <br>
	        f = open("C:\\Users\\hello\\Desktop\\vip.txt", "w") <br>
	        arr = sys.argv[1]<br>
	        arr = encoder(xor(arr))<br>
	        f.write(arr)<br>
	        f.close()<br> 
            </b>
        </p>
        <br>
        <p>This evil script is XORing the file <b>vip.txt</b> with a single character then Base64 encoding it.</p>
        <p>And here is the content of the dumped text file:</p>
        <p><b>am1gd2V4M20wXGs3b2U=</b></p>
        <p>So we first need to Base64 decode it then XOR it again with same character to retrieve the original text.</p>
        <img src="../public/images/lab3flag1.png" alt="" width="750">
        <br>
        <p><b style="color:red">First half : inctf{0n3_h4lf</b></p>
    </div>
    <div class="container">
        <h2>Second half : </h2>
        <h3>Now that we have the first half of the flag, let’s hunt for the other half.</h3>
        <p>This one took me sometime, then I looked at the hint and it says something about <b>steghide</b></p>
        <br>
        <p>Steghide is a steganography program that is able to hide data in images and audio files and it supports JPEG and BMP images, so I decided to search memory for JPEG images.</p>
        <br>
        <p><b>$ volatility -f MemoryDump_Lab3.raw --profile Win7SP1x86_23418 filescan | grep ".jpeg"</b></p>
        <p>Would you look at that!!!, only one image and it looks suspicious :)</p>
        <img src="../public/images/lab3flag2.jpeg" alt="" width="700" height="400">
        <p><b>It’s just a normal image, or is it ???</b></p>
        <br>
        <p>Here comes <b>steghide</b>, this image must have something hidden</p>
        <br>
       <p><b>$ steghide extract -sf lab3_output/suspision1.jpeg</b></p>
       <p>It’s asking for a passphrase, the hint clearly says that: <b>You'll need the first half of the flag to get the second.</b></p>
       <p>Let’s try the first half of the flag as the passphrase.</p>
       <p><b>$ steghide extract -sf lab3_output/suspision1.jpeg</b></p>
       <br>
       <p>Voila!!! let’s get this secret text.</p>
       <p><b>cat secret\ text</b></p>
       <h4>Second half :<b>_1s_n0t_3n0ugh}</b></h4>
        <p><b style="color: red;">Flag: inctf{0n3_h4lf_1s_n0t_3n0ugh}</b></p>
    </div>

        <!-- Back Button -->
        <a href="../forensics.php" class="back-button">← Back</a>
    </div>

</body>

</html>
