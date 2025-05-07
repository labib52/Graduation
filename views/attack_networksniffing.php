<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wireless Security Simulation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/CSS/network_2.css">
</head>
<body>
    <header>
        <h1>network sniffing </h1>
        <button id="theme-toggle" aria-label="Toggle theme">
                 🌓
            </button>
    </header>
    <main class="content">
        <!-- Lecture Card -->
        <a href="/Graduation/views/lecture.php?id=32">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>network sniffing lecture</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description"> A network sniffing attack is a type of cyberattack where an attacker intercepts
                and monitors network traffic to capture sensitive information</p>
            </div>
        </a>
        <!-- Lab Exercises Card -->
        <a href="/Graduation/views/lab.php?id=21">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Lab Exercises</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">A network sniffing attack is a type of cyberattack where an attacker intercepts
                and monitors network traffic to capture sensitive information</p>
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
<script>
    const themeToggle = document.getElementById('theme-toggle');
    const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

    // Set initial theme
    document.documentElement.setAttribute('data-theme', savedTheme);

    themeToggle.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);

        // Update button icon (optional)
        themeToggle.textContent = newTheme === 'dark' ? '🌞' : '🌒';
    });

    // Optional: Update button icon on load
    themeToggle.textContent = savedTheme === 'dark' ? '🌞' : '🌒'
</script>
</html>
