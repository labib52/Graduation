<?php
// Full path to vmrun (if it's not in the PATH)
$vmrunPath = '"C:\Program Files (x86)\VMware\VMware Workstation\vmrun.exe"';

// Handle start/stop actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['start'])) {
        $command = $vmrunPath . ' start "C:\Users\youssef\Downloads\Kali 2024 x64 Customized by zSecurity v1.3\Kali 2024 x64 Customized by zSecurity v1.3.vmx"';
        $output = shell_exec($command);
        echo "VM Started: " . nl2br($output);
    } elseif (isset($_POST['stop'])) {
        $command = $vmrunPath . ' stop "C:\Users\youssef\Downloads\Kali 2024 x64 Customized by zSecurity v1.3\Kali 2024 x64 Customized by zSecurity v1.3.vmx"';
        $output = shell_exec($command);
        echo "VM Stopped: " . nl2br($output);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Kali VM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
            background-color: #f4f4f9;
        }
        h1 {
            color: #007BFF;
        }
        form {
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            margin: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            color: white;
        }
        .start {
            background-color: #28a745;
        }
        .start:hover {
            background-color: #218838;
        }
        .stop {
            background-color: #dc3545;
        }
        .stop:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <h1>Control Virtual Machine</h1>
    <form method="POST">
        <button type="submit" name="start" class="start">Start VM</button>
        <button type="submit" name="stop" class="stop">Stop VM</button>
    </form>
</body>
</html>
