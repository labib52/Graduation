<?php
session_start();
include('../controller/db_connection.php');;

// Handle category addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    if (!empty($category_name)) {
        $query = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $category_name);
        $stmt->execute();
        header("Location: admin_categories.php");
        exit();
    }
}

// Handle category update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
    $category_id = intval($_POST['category_id']);
    $new_category_name = trim($_POST['new_category_name']);
    if (!empty($new_category_name)) {
        $query = "UPDATE categories SET name = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $new_category_name, $category_id);
        $stmt->execute();
        header("Location: admin_categories.php");
        exit();
    }
}

// Handle category deletion
if (isset($_GET['delete_id'])) {
    $category_id = intval($_GET['delete_id']);
    $query = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    header("Location: admin_categories.php");
    exit();
}

// Fetch all categories
$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="../public/CSS/admin_styles_1.css">
</head>
<body>
    <header>
        <h2>Manage Categories</h2>
        <a href="admin_dashboard.php" class="back">Back to Dashboard</a> 
        
    </header>
   
    <form action="admin_categories.php" method="POST">
        <input type="text" name="category_name" placeholder="Category Name" required>
        <button type="submit" name="add_category">Add Category</button>
    </form>

    <table>
        <tr><th>Category Name</th><th>Actions</th></tr>
        
        <?php while ($row = mysqli_fetch_assoc($categories)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td>
                    <button onclick="editCategory(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['name']); ?>')">Edit</button>
                    <a href="admin_categories.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <!-- Hidden Edit Form -->
    <div id="editForm" style="display: none;">
        <h3>Edit Category</h3>
        <form method="POST" action="admin_categories.php">
            <input type="hidden" name="category_id" id="edit_category_id">
            <input type="text" name="new_category_name" id="edit_category_name" required>
            <button type="submit" name="update_category">Update Category</button>
            <button type="button" onclick="document.getElementById('editForm').style.display='none';">Cancel</button>
        </form>
    </div>

    <script>
        function editCategory(id, name) {
            document.getElementById('edit_category_id').value = id;
            document.getElementById('edit_category_name').value = name;
            document.getElementById('editForm').style.display = 'block';
        }
    </script>
</body>
</html>
