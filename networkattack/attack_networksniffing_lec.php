<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Security Simulation</title>
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
.steps{
    font-size: 50px;
    color:rgb(65, 74, 210);
}
.parone{
    font-size: 25px;
}
.hint{
    font-size: 15px;
    font-weight: bold;
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
        <div><img src="../public/images/network sniffing.jpg"  style="height: 400px;" width="700px" alt=""></div>
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
<div>
<img src="../public/images/macaddress.jpg" style="height: 400px;" width="700px" alt="">
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
<br><br><hr><br><br>
<div class="steps">Step 3:</div>
<div class="parone">
<i class="fa-solid fa-1">
   <p> -In this step you should make a socket to have connections to other computers:</p> 
</i>
</div>
<div class="hint">Hint:Use main loop for recursion</div>
<br><br>
<div class="parone">
<i class="fa-solid fa-1">
   <p> def main(): <br>
conn=socket.socket(socket.AF_PACKET,socket-SOCK_RAW,socket,ntohs(3)) <br>
while true: <br>
raw_data,addr=conn-rcvfrom(65536) <br>
dest_mac,src_mac,eth_proto,data=ethernet_frame(raw_data) <br>
print('\n EthernetFrame:')
print('Destination:{},source{},Protocol:{}',format(dest_mac,src_mac,eth_proto))
</p> 
</i>
</div>
<br><br>
<div>
<p class="parone">The result: the destination, the source and the protocol</p>
<br>
<img src="../public/images/destport.jpg" style="height: 400px;" width="700px" alt="">
</div>
<br>
<hr>
<br>
<div class="steps">Step 4:</div>
<br>

<div class="parone">
<i class="fa-solid fa-1">
    -In this step you are going to unpack IP packet, this pic will help you understand IP more:
    <br>
    <br>
    <img src="../public/images/IPheader.jpg" style="height: 400px;" width="700px" alt="">

</i>
</div>
<br><br>
<div class="parone">
<i class="fa-solid fa-1">
def ipv4_packet(data): <br>
version_header_length=data[0] <br>
version=version_header_length >> 4 <br>
header_length=(version_header_length & 15) * 4 <br>
ttl,proto,src,target=struct.unpack('! 8x B 2x 4s 4s',data[:20])<br>
returnn version,header_length,ttl,proto,ipv4(src),ipv4(target),data[headere_length:]  <br><br>
-#returns properly formatted ipv4 address <br>
def ipv4(addr): <br>
return'.'.join(map(str,addr))
</i>
</div>
<br><div class="hint">
   NOTE: The header length is used to determine where the data starts because <br>
after the header ends that's where actual data begins
</div>
<br><hr><br>
<div class="steps">Step 5:</div>
<br><br>
<div class="parone">
<i class="fa-solid fa-1">
-In this step you are going to unpack ICMP:
</i>
</div>
<br><br>
<div class="parone">
<i class="fa-solid fa-1">
<p>def icmp_packet(data): <br>
icmp_type,code,checksum=struct.unpack('! B B H',data[:4]) <br>
return icmp_type,code,checksum,data[4:] <br>
</p>
</i>
</div>
<br>
<div class="parone">
<i class="fa-solid fa-1">
-Then you are going to unpack TCP segment by learning and understanding this pic:
</i>
</div>
<br><br>
<div>
    <img src="../public/images/TCP.jpg" style="height: 500px;" width="600px" alt="">
</div>
<div class="parone">
<i class="fa-solid fa-1">
<p>
    def tcp_segment(data): <br>
    (src_port,dest_port,sequence,acknowledgement,offset_reserved_flags)=struct.unpack('! H H L L H',data[:14]) <br>
    offset=(offset_reserved_flags >> 12) * 4 <br><br>
    -To establish base connection: <br><br>
    flag_urg=(offset_reserved_flags & 32) >> 5 <br>
    flag_ack=(offset_reserved_flags & 32) >> 5 <br>
    flag_psh=(offset_reserved_flags & 32) >> 5 <br>
    flag_rst=(offset_reserved_flags & 32) >> 5 <br>
    flag_syn=(offset_reserved_flags & 32) >> 5 <br>
    flag_fin=offset_reserved_flags & 1 <br>
    return src_port,dest_port,sequence,acknowledgement,flag_urg,flag_ack,flag_psh,flag_rst,flag_syn,flag_fin,data[offset] <br>

</p>
</i>
</div>
<br><hr><br>
<div class="steps">Step 6:</div>
<div class="parone">
<i class="fa-solid fa-1">
-This is last step that will show you results of IPV4 packet which in it is tcp segment and the destination and protocol and source <br>

</i>
</div>
<br><br>
<div class="parone">

<footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</div>
    </main>
    </body>
  
