<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wireless Security Simulation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/CSS/reflectedxss.css">

</head>
<body>
    <header>
        <h1>Dos Attack</h1>
    </header>
    <main class="content">
        <!-- Lecture Card -->
        <a href="/Graduation/views/lecture.php?id=39">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>DOS Atttack Lecture</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Learn about DOS attack and mitigation techniques.</p>
            </div>
        </a>

        <!-- Lab Exercises Card -->
        <a href="/Graduation/views/lab.php?id=19">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Lab Exercises</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">demonstrates intercepting and manipulating communication between two parties to explore vulnerabilities and secure data transmission.</p>
            </div>
        </a>

          <!-- Tools Card (Now opens index.php without auto-starting VM) -->
       <a href="index.php" target="_blank">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Try with Virtual Machine</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Click here to open the VM control panel and manually start Kali Linux.</p>
            </div>
        </a>
    </main>
    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
