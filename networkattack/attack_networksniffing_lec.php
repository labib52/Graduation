<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wireless Security Simulation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
.header {
  padding: 60px;
  text-align: center;
  width: 800px;
  height: 150px;
  background: #444;
  background-position: right;
  position:absolute;
  top:20%;
    left:50%;
transform:translate(-50%,-50%);
  background:rgb(53, 35, 211);
  color: white;
  font-size: 30px;  
}
.description{
    font-size:20px ;
    text-align: center;
}
div{
    font-size: large;
    text-align: center;

}
hr{
    background-color:rgb(65, 74, 210);
     height: 10px;
     width: 800px;
}
.img1{
    height: 50%;
}
.steps{
    font-size: 50px;
    color:rgb(65, 74, 210);
}
.parone{
    font-size: 25px;
}
    </style>
</head>
    <body>
    <header>
        <div class="header">
        <h1>Network Sniffing Attack </h1>
    </div>
    <br> <br> <br><br><br><br><br><br><br><br><br><br><br><br><br>
    </header>
    <main class="content">
        <div>
            <p>
            <h2>-What is a Network Sniffing Attack? </h2>
        </p>
        </div>
        <div class="description">
            <p><i class="fa-solid fa-star">
            A network sniffing attack is a type of cyberattack where an attacker intercepts
             and monitors network traffic to capture sensitive information, such as login credentials, 
             credit card details, or personal messages. It is often carried out using specialized 
             software or hardware tools called packet sniffers.
            </i>
            </p>
        </div>
        <br>
        <hr>
        <br>
        <div class="parone">
        <i class="fa-solid fa-1">
        1.Firstly you should understand this picture that explains what Ethernet Frame means:
        </i>
        </div>
        <br>
        <div class="img1"><img src="./network sniffing.jpg" alt=""></div>
        <br>
        <hr>
        <br>
<div class="parone">
 -After understanding Ethernet Frame you will start to applies what you have learned by using PYCHARM:   
</div>
<br>
<div class="steps">Step 1:</div>
<br>
<div class="parone">
<p><i class="fa-solid fa-star">
           def ethernet_frame(data):
          dest_mac,src_mac,,proto=struct.unpack('! 6s 6s H',data[:14])
          return get_mac_addr(dest_mac),get_mac_addr(src_mac),socket.htons(proto),data[14:]
            </i>
            </p>
            <br>
            meanings:
            <br>
            -dest_mac=destenation mac address
            <br>
            -src_mac=source mac address
</div>
<br><hr><br>
<div class="steps">Step 2:</div>
<br>
<br>
<div class="parone">
<i class="fa-solid fa-1">
-The next step is to return formatted mac address and this picture will help you understand more how and why we returned mac address:
</i>
</div>
<br>
<br>
<div class="img1">
<img src="./macaddress.jpg" alt="">
</div>
<br><br>
<div class="parone">
<i class="fa-solid fa-1">
    -To return mac address:
</i>

</div>
<br>
<div class="parone">
<i class="fa-solid fa-1">
    def get_mac_addr(bytes_addr);
    bytes_str=map('{:02x}'.format,bytes_addr)
    return ':'.join(bytes_str).upper()
</i>

</div>
    </main>
    </body>
