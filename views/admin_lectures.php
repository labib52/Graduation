<?php
session_start();
include('../controller/db_connection.php');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    die("Access Denied");
}

// Fetch all lectures
$lectures_query = "SELECT lectures.id, lectures.title, courses.title AS course_title FROM lectures 
                   JOIN courses ON lectures.course_id = courses.id";
$lectures_result = mysqli_query($conn, $lectures_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lectures</title>
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
        <h1>Manage Lectures</h1>
        <a href="admin_dashboard.php">Back to Dashboard</a>
    </header>

    <main>
        <a href="admin_add_lecture.php" class="add-button">+ Add New Lecture</a>
        <table>
            <tr>
                <th>Lecture Title</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($lectures_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['course_title']); ?></td>
                    <td>
                        <a href="admin_edit_lecture.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                        <a href="admin_delete_lecture.php?id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </main>
</body>
</html>
