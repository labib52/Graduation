<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wireless Security Simulation</title>
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
        }

        header {
            background-color: #007BFF;
            color: #fff;
            padding: 1rem 2rem;
            text-align: left;
        }

        header h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .content {
            padding: 2rem;
            margin: auto;
            max-width: 800px;
        }

        .simulation-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .simulation-card:hover {
            transform: translateY(-3px);
        }

        .simulation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .simulation-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .status {
            font-size: 0.9rem;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
            font-weight: bold;
        }

        .active {
            background-color: #28a745;
            color: white;
        }

        .description {
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        footer {
            text-align: center;
            padding: 1rem;
            background-color: #ddd;
            margin-top: 2rem;
        }

        a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>
    <header>
        <h1>Wireless Security Simulation</h1>
    </header>
    <main class="content">
        <!-- Lecture Card -->
        <a href="attack1networklec.php">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Wireless Security Lecture</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Learn about wireless security protocols, threats, and mitigation techniques.</p>
            </div>
        </a>

        <!-- Lab Exercises Card -->
        <a href="attack1networklab.php">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Lab Exercises</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Practice securing wireless networks and identifying vulnerabilities through simulations.</p>
            </div>
        </a>

        <!-- Tools Card -->
        <a href="https://wirelesssecuritytools.example.com" target="_blank">
            <div class="simulation-card">
                <div class="simulation-header">
                    <h2>Wireless Security Tools</h2>
                    <span class="status active">Active</span>
                </div>
                <p class="description">Explore tools to analyze, secure, and monitor wireless networks effectively.</p>
            </div>
        </a>
    </main>
    <footer>
        <p>© 2024 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>
</body>
</html>
