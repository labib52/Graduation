<?php
session_start();
include('../controller/db_connection.php');

// Handle course addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_course'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category_id = $_POST['category'];
    $level = $_POST['level'];
    $price = $_POST['price'];

    if (!empty($title) && !empty($category_id) && !empty($level)) {
        $query = "INSERT INTO courses (title, description, category_id, level, price) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssiss", $title, $description, $category_id, $level, $price);
        $stmt->execute();
        header("Location: admin_courses.php");
        exit();
    }
}

// Handle course update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_course'])) {
    $course_id = intval($_POST['course_id']);
    $new_title = trim($_POST['new_title']);
    $new_description = trim($_POST['new_description']);
    $new_category_id = $_POST['new_category'];
    $new_level = $_POST['new_level'];
    $new_price = $_POST['new_price'];

    if (!empty($new_title) && !empty($new_category_id) && !empty($new_level)) {
        $query = "UPDATE courses SET title = ?, description = ?, category_id = ?, price = ?, level = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssissi", $new_title, $new_description, $new_category_id, $new_price, $new_level, $course_id);
        $stmt->execute();
        header("Location: admin_courses.php");
        exit();
    }
}

// Handle course deletion
if (isset($_GET['delete_id'])) {
    $course_id = intval($_GET['delete_id']);

    $stmt1 = $conn->prepare("DELETE FROM requests WHERE course_id = ?");
    if ($stmt1) {
        $stmt1->bind_param("i", $course_id);
        $stmt1->execute();
    }

    $stmt2 = $conn->prepare("DELETE FROM enrollments WHERE course_id = ?");
    if ($stmt2) {
        $stmt2->bind_param("i", $course_id);
        $stmt2->execute();
    }

    $stmt3 = $conn->prepare("DELETE FROM lectures WHERE course_id = ?");
    if ($stmt3) {
        $stmt3->bind_param("i", $course_id);
        $stmt3->execute();
    }

    $stmt4 = $conn->prepare("DELETE FROM courses WHERE id = ?");
    if ($stmt4) {
        $stmt4->bind_param("i", $course_id);
        $stmt4->execute();
    }

    header("Location: admin_courses.php");
    exit();
}

// Fetch all courses and categories
$courses = mysqli_query($conn, "SELECT courses.*, categories.name AS category_name FROM courses 
                                LEFT JOIN categories ON courses.category_id = categories.id");
$categories = mysqli_query($conn, "SELECT * FROM categories");

// Fetch categories again for edit form
$edit_categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link rel="stylesheet" href="../public/CSS/admin_styles.css">
</head>
<body>
    <header>
        <h2>Manage Courses</h2>
        <a href="admin_dashboard.php" class="back">Back to Dashboard</a>
    </header>

    <!-- Add Course Form -->
    <form action="admin_courses.php" method="POST">
        <input type="text" name="title" placeholder="Course Title" required>
        <textarea name="description" placeholder="Description"></textarea>
        <select name="category">
            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
            <?php endwhile; ?>
        </select>

        <select name="level">
            <option value="beginner">Beginner</option>
            <option value="advanced">Advanced</option>
        </select>
        <input type="number" name="price" placeholder="Price (0 for free)">
        <button type="submit" name="add_course">Add Course</button>
    </form>

    <!-- Existing Courses -->
    <h3>Existing Courses</h3>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Category</th>
            <th>Level</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($courses)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                <td><?php echo htmlspecialchars($row['level']); ?></td>
                <td><?php echo htmlspecialchars($row['price']); ?></td>
                <td>
                    <button onclick="editCourse(
                        <?php echo $row['id']; ?>, 
                        '<?php echo htmlspecialchars(addslashes($row['title'])); ?>', 
                        '<?php echo htmlspecialchars(addslashes($row['description'])); ?>', 
                        <?php echo $row['category_id']; ?>, 
                        '<?php echo htmlspecialchars($row['level']); ?>',
                        '<?php echo htmlspecialchars($row['price']); ?>'
                    )">Edit</button>
                    <a href="admin_courses.php?delete_id=<?php echo $row['id']; ?>" 
                       onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <!-- Edit Course Form -->
    <div id="editForm" style="display: none;">
        <h3>Edit Course</h3>
        <form method="POST" action="admin_courses.php">
            <input type="hidden" name="course_id" id="edit_course_id">
            <input type="text" name="new_title" id="edit_course_title" required>
            <textarea name="new_description" id="edit_course_description"></textarea>

            <select name="new_category" id="edit_course_category">
                <?php while ($cat = mysqli_fetch_assoc($edit_categories)): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                <?php endwhile; ?>
            </select>

            <select name="new_level" id="edit_course_level">
                <option value="beginner">Beginner</option>
                <option value="advanced">Advanced</option>
            </select>
            <input type="number" name="new_price" id="edit_course_price">
            <button type="submit" name="update_course">Update Course</button>
            <button type="button" onclick="document.getElementById('editForm').style.display='none';">Cancel</button>
        </form>
    </div>

    <!-- JavaScript for Edit -->
    <script>
        function editCourse(id, title, description, categoryId, level, price) {
            document.getElementById('edit_course_id').value = id;
            document.getElementById('edit_course_title').value = title;
            document.getElementById('edit_course_description').value = description;
            document.getElementById('edit_course_category').value = categoryId;
            document.getElementById('edit_course_level').value = level;
            document.getElementById('edit_course_price').value = price;
            document.getElementById('editForm').style.display = 'block';
        }
    </script>
</body>
</html>
