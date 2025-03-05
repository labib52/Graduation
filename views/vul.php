<?php
session_start();
include('../controller/db_connection.php'); // Include database connection

// Check if a user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username'] ?? 'User') : "Guest";

$resultsDisplay = ''; // Variable to store the result display

if (isset($_POST["submit"])) {
    // Get the input from the form
    $firstname = $_POST["firstname"];

    // Vulnerable SQL query - User input is directly concatenated
    $sql = "SELECT lastname FROM users_vul WHERE username = '$firstname'";
    $result = mysqli_query($conn, $sql);
    
    // Check if there are any rows returned
    if (mysqli_num_rows($result) > 0) {
        // Start creating an HTML table to display results
        $resultsDisplay .= '<table class="results-table">';
        $resultsDisplay .= '<thead><tr><th>LastName</th></tr></thead><tbody>';
        
        // Fetch and output data for each row
        while ($row = mysqli_fetch_assoc($result)) {
            $resultsDisplay .= '<tr>';
            $resultsDisplay .= '<td>' . htmlspecialchars($row["lastname"]) . '</td>';
            $resultsDisplay .= '</tr>';
        }
        
        $resultsDisplay .= '</tbody></table>';
    } else {
        $resultsDisplay = "<p>No results found.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/CSS/web.css">
    <style>
        .results-table {
            width: 50%;
            margin-top: 20px;
            border-collapse: collapse;
            text-align: left;
            background-color: #f9f9f9;
        }
        .results-table th, .results-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .results-table th {
            background-color: #007BFF;
            color: white;
        }
        .results-table td {
            color: #333;
        }
        .no-results {
            color: red;
            margin-top: 20px;
        }
        footer {
            text-align: center;
            padding: 1rem;
            background-color: #007BFF;
            color: white;
            margin-top: 20px;
            font-size: 1rem;
            width: 100%;
}
    </style>
</head>
<body>
    <header>
        <h1>Web Security Simulation</h1>
        <div class="user-info">
            Welcome, <?php echo $username; ?>!
        </div>
    </header>
    <div align="center">
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        First name : <input type="text" name="firstname" required>
        <input type="submit" name="submit" value="Submit">
    </form>

    <!-- Display results here -->
    <?php
        if (!empty($resultsDisplay)) {
            echo $resultsDisplay;
        }
    ?>
    </div>
     <!-- Back Button -->
     <a href="/Graduation/views/web.php" class="back-button">← Back</a>
    </main>
    <footer>
        <p>© 2025 Cybersecurity Awareness Platform. All Rights Reserved.</p>
    </footer>    
</body>
</html>
