<?php
session_start();
include('../controller/db_connection.php');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    die("Access Denied");
}

// Fetch all labs with their course names
$labs_query = "SELECT labs.*, courses.title AS course_title 
               FROM labs 
               JOIN courses ON labs.course_id = courses.id";
$labs_result = mysqli_query($conn, $labs_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Labs</title>
    <link rel="stylesheet" href="../public/CSS/admin_styles.css">
    <style>
        :root {
            --primary-blue: #007bff;
            --hover-blue: #0056b3;
            --danger-red: #dc3545;
            --hover-red: #c82333;
        }

        .edit-btn, .delete-btn {
            padding: 5px 15px;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.3s;
            color: white;
            margin: 0 5px;
        }

        .edit-btn {
            background-color: var(--primary-blue);
        }

        .edit-btn:hover {
            background-color: var(--hover-blue);
        }

        .delete-btn {
            background-color: var(--danger-red);
        }

        .delete-btn:hover {
            background-color: var(--hover-red);
        }
    </style>
</head>
<body>
    <header>
        <h1>Manage Labs</h1>
        <a href="admin_dashboard.php">Back to Dashboard</a>
    </header>

    <main>
        <a href="admin_add_lab.php" class="add-button">+ Add New Lab</a>
        <table>
            <tr>
                <th>Lab Name</th>
                <th>Course</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($labs_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['course_title']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <a href="admin_edit_lab.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                        <a href="admin_delete_lab.php?id=<?php echo $row['id']; ?>" 
                           class="delete-btn" 
                           onclick="return confirm('Are you sure? This will delete all associated questions and choices.')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </main>
</body>
</html> 