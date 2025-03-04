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
    <link rel="stylesheet" href="../public/CSS/index.css">
</head>
<body>
    <h1>Control Virtual Machine</h1>
    <form method="POST">
        <button type="submit" name="start" class="start">Start VM</button>
        <button type="submit" name="stop" class="stop">Stop VM</button>
    </form>
</body>
</html>
