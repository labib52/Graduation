<?php
session_start();
include('../controller/db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['request_id']) && isset($_POST['action'])) {
        $request_id = $_POST['request_id'];
        $new_status = $_POST['action']; // Will be either 'approved' or 'rejected'
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // First, get the request details to get user_id and course_id
            $get_request = $conn->prepare("SELECT user_id, course_id FROM requests WHERE id = ?");
            $get_request->bind_param("i", $request_id);
            $get_request->execute();
            $request_details = $get_request->get_result()->fetch_assoc();
            
            // Update the request status
            $update_query = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
            $update_query->bind_param("si", $new_status, $request_id);
            $update_query->execute();
            
            // If status is approved, create enrollment record
            if ($new_status === 'approved') {
                // Check if enrollment already exists
                $check_enrollment = $conn->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?");
                $check_enrollment->bind_param("ii", $request_details['user_id'], $request_details['course_id']);
                $check_enrollment->execute();
                
                if ($check_enrollment->get_result()->num_rows === 0) {
                    // Create new enrollment record
                    $enroll_query = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
                    $enroll_query->bind_param("ii", $request_details['user_id'], $request_details['course_id']);
                    $enroll_query->execute();
                }
            } else if ($new_status === 'rejected') {
                // Remove enrollment if exists when request is rejected
                $delete_enrollment = $conn->prepare("DELETE FROM enrollments WHERE student_id = ? AND course_id = ?");
                $delete_enrollment->bind_param("ii", $request_details['user_id'], $request_details['course_id']);
                $delete_enrollment->execute();
            }
            
            // Commit the transaction
            $conn->commit();
            
            // Redirect to refresh the page after update
            header("Location: admin_requests.php?success=true&status=" . $new_status);
            exit();
            
        } catch (Exception $e) {
            // If there's an error, rollback the changes
            $conn->rollback();
            header("Location: admin_requests.php?error=true");
            exit();
        }
    }
}

// Fetch all requests with user and course information
$query = $conn->query("
    SELECT r.*, u.username, c.title as course_title, cat.name as category_name,
           (SELECT COUNT(*) FROM enrollments e WHERE e.student_id = r.user_id AND e.course_id = r.course_id) as is_enrolled
    FROM requests r
    JOIN users u ON r.user_id = u.id
    JOIN courses c ON r.course_id = c.id
    JOIN categories cat ON c.category_id = cat.id
    ORDER BY r.request_date DESC
");
$requests = $query->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Access Requests</title>
    <link rel="stylesheet" href="../public/CSS/admin_styles_1.css">
    <style>
        .status-pending { 
            background-color: #ffd700;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .status-approved { 
            background-color: #90EE90;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .status-rejected { 
            background-color: #ffcccb;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 5px;
            color: white;
            font-weight: bold;
        }
        .approve-btn {
            background-color: #4CAF50;
        }
        .reject-btn {
            background-color: #f44336;
        }
        .approve-btn:hover {
            background-color: #45a049;
        }
        .reject-btn:hover {
            background-color: #da190b;
        }
        .enrolled-badge {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .alert {
            padding: 15px;
            margin: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .edit-btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .edit-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <header>
        <h1>Course Access Requests</h1>
        <a href="admin_dashboard.php" class="back">Back to Dashboard</a>
    </header>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert success">
            Status updated successfully!
            <?php 
            if (isset($_GET['status']) && $_GET['status'] === 'approved') {
                echo ' Student has been enrolled in the course.';
            }
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert error">
            An error occurred while processing your request. Please try again.
        </div>
    <?php endif; ?>

    <main>
        <table>
            <thead>
                <tr>
                    <th>STUDENT</th>
                    <th>COURSE</th>
                    <th>CATEGORY</th>
                    <th>REQUEST DATE</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['username']); ?></td>
                        <td><?php echo htmlspecialchars($request['course_title']); ?></td>
                        <td><?php echo htmlspecialchars($request['category_name']); ?></td>
                        <td><?php echo $request['request_date']; ?></td>
                        <td>
                            <span class="status-<?php echo $request['status']; ?>">
                                <?php echo ucfirst($request['status']); ?>
                            </span>
                            <?php if ($request['is_enrolled'] > 0): ?>
                                <span class="enrolled-badge">Enrolled</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_request.php?id=<?php echo $request['id']; ?>" class="edit-btn">Edit</a>
                            <?php if ($request['status'] === 'pending'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <button type="submit" name="action" value="approved" class="action-btn approve-btn">
                                        Approve
                                    </button>
                                    <button type="submit" name="action" value="rejected" class="action-btn reject-btn">
                                        Reject
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
