<?php
// Full path to vmrun (if it's not in the PATH)
$vmrunPath = '"C:\Program Files (x86)\VMware\VMware Workstation\vmrun.exe"';

// Define virtual machines and their paths
$virtualMachines = [
    'kali' => 'C:\Users\youssef\Downloads\Kali 2024 x64 Customized by zSecurity v1.3\Kali 2024 x64 Customized by zSecurity v1.3.vmx',
    'windows10' => 'C:\Users\youssef\Documents\Virtual Machines\windows 10\windows 10.vmx',
    'eve' => 'C:\Users\youssef\Documents\Virtual Machines\EVE-NG\EVE-NG.VMX'
];

// Handle start/stop actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['start']) && isset($_POST['vm_select'])) {
        $selectedVM = $_POST['vm_select'];
        if (isset($virtualMachines[$selectedVM])) {
            $command = $vmrunPath . ' start "' . $virtualMachines[$selectedVM] . '"';
            $output = shell_exec($command);
            $message = "VM Started Successfully!";
            $messageType = "success";
        }
    } elseif (isset($_POST['stop']) && isset($_POST['vm_select'])) {
        $selectedVM = $_POST['vm_select'];
        if (isset($virtualMachines[$selectedVM])) {
            $command = $vmrunPath . ' stop "' . $virtualMachines[$selectedVM] . '"';
            $output = shell_exec($command);
            $message = "VM Stopped Successfully!";
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control VMs</title>
    <link rel="stylesheet" href="../public/CSS/index.css">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .vm-control-panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .vm-status {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            width: 100%;
            max-width: 500px;
        }

        select {
            padding: 12px;
            width: 300px;
            border-radius: 6px;
            border: 2px solid #e0e0e0;
            font-size: 16px;
            background-color: white;
            transition: all 0.3s ease;
        }

        select:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .vm-icon {
            width: 40px;
            height: 40px;
            margin-right: 10px;
            vertical-align: middle;
        }

        .title-section {
            margin-bottom: 30px;
        }

        .title-section h1 {
            margin-bottom: 10px;
        }

        .subtitle {
            color: #6c757d;
            font-size: 18px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title-section">
            <h1>Virtual Machine Control Panel</h1>
            <p class="subtitle">Manage your virtual machines with ease</p>
        </div>
        
        <div class="vm-control-panel">
            <form method="POST">
                <select name="vm_select" required>
                    <option value="">Select a Virtual Machine</option>
                    <option value="kali">🐧 Kali Linux</option>
                    <option value="windows10">🪟 Windows 10</option>
                    <option value="eve">🌐 EVE-NG</option>
                </select>
                
                <div class="button-group">
                    <button type="submit" name="start" class="start">
                        ▶️ Start VM
                    </button>
                    <button type="submit" name="stop" class="stop">
                        ⏹️ Stop VM
                    </button>
                </div>
            </form>

            <?php if (isset($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <div class="vm-status">
                <h3>Available Virtual Machines:</h3>
                <ul style="list-style: none; padding: 0; text-align: left;">
                    <li style="padding: 10px 0;">🐧 Kali Linux - Penetration Testing Platform</li>
                    <li style="padding: 10px 0;">🪟 Windows 10 - Development Environment</li>
                    <li style="padding: 10px 0;">🌐 EVE-NG - Network Emulation Platform</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
