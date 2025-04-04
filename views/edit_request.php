<?php
session_start();
include('../controller/db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

// Check if request ID is provided
if (!isset($_GET['id'])) {
    header('Location: admin_requests.php');
    exit();
}

$request_id = $_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // First, get the request details and current status
            $get_request = $conn->prepare("
                SELECT r.user_id, r.course_id, r.status as current_status,
                       (SELECT COUNT(*) FROM enrollments e WHERE e.student_id = r.user_id AND e.course_id = r.course_id) as is_enrolled
                FROM requests r 
                WHERE r.id = ?
            ");
            $get_request->bind_param("i", $request_id);
            $get_request->execute();
            $request_details = $get_request->get_result()->fetch_assoc();
            
            if ($action === 'request_again') {
                // Reset the request status to pending
                $update_query = $conn->prepare("UPDATE requests SET status = 'pending' WHERE id = ?");
                $update_query->bind_param("i", $request_id);
                $update_query->execute();
                
                // Remove enrollment if exists
                if ($request_details['is_enrolled'] > 0) {
                    $delete_enrollment = $conn->prepare("
                        DELETE FROM enrollments 
                        WHERE student_id = ? AND course_id = ?
                    ");
                    $delete_enrollment->bind_param("ii", $request_details['user_id'], $request_details['course_id']);
                    $delete_enrollment->execute();
                }
                
                $message = "reset";
            } else {
                // Regular status update
                $new_status = $_POST['status'];
                
                // Update the request status
                $update_query = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
                $update_query->bind_param("si", $new_status, $request_id);
                $update_query->execute();
                
                // Handle enrollment based on status change
                if ($new_status === 'approved') {
                    // If new status is approved and not already enrolled, create enrollment
                    if ($request_details['is_enrolled'] == 0) {
                        $enroll_query = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
                        $enroll_query->bind_param("ii", $request_details['user_id'], $request_details['course_id']);
                        $enroll_query->execute();
                    }
                } else {
                    // If status changed from approved to something else, remove enrollment
                    if ($request_details['current_status'] === 'approved') {
                        $delete_enrollment = $conn->prepare("
                            DELETE FROM enrollments 
                            WHERE student_id = ? AND course_id = ?
                        ");
                        $delete_enrollment->bind_param("ii", $request_details['user_id'], $request_details['course_id']);
                        $delete_enrollment->execute();
                    }
                }
                
                $message = $new_status === 'approved' ? 'approved&enrolled=true' : $new_status;
            }
            
            // Commit the transaction
            $conn->commit();
            
            header("Location: admin_requests.php?success=true&status=" . $message);
            exit();
            
        } catch (Exception $e) {
            // If there's an error, rollback the changes
            $conn->rollback();
            header("Location: edit_request.php?id=" . $request_id . "&error=true");
            exit();
        }
    }
}

// Fetch request details
$query = $conn->prepare("
    SELECT r.*, u.username, c.title as course_title, cat.name as category_name,
           (SELECT COUNT(*) FROM enrollments e WHERE e.student_id = r.user_id AND e.course_id = r.course_id) as is_enrolled
    FROM requests r
    JOIN users u ON r.user_id = u.id
    JOIN courses c ON r.course_id = c.id
    JOIN categories cat ON c.category_id = cat.id
    WHERE r.id = ?
");
$query->bind_param("i", $request_id);
$query->execute();
$request = $query->get_result()->fetch_assoc();

if (!$request) {
    header('Location: admin_requests.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course Request</title>
    <link rel="stylesheet" href="../public/CSS/admin_styles.css">
    <style>
        .edit-form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .info-group {
            margin-bottom: 15px;
        }
        
        .info-group strong {
            display: inline-block;
            width: 120px;
        }
        
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .submit-btn:hover {
            background-color: #45a049;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
            margin-left: 10px;
        }
        
        .warning-message {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            border: 1px solid #ffeeba;
            border-radius: 4px;
            margin: 10px 0;
            display: none;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .reset-btn {
            background-color: #ff9800;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .reset-btn:hover {
            background-color: #f57c00;
        }
    </style>
    <script>
        function showWarning(action) {
            let message = '';
            if (action === 'status_change') {
                const currentStatus = '<?php echo $request['status']; ?>';
                const newStatus = document.getElementById('status').value;
                
                if (currentStatus === 'approved' && newStatus !== 'approved') {
                    message = 'Warning: Changing status from approved will remove the student\'s enrollment in this course.';
                }
            } else if (action === 'request_again') {
                message = 'Warning: This will reset the request to pending status and remove the student\'s enrollment if they are currently enrolled.';
            }
            
            const warningDiv = document.getElementById('statusWarning');
            warningDiv.textContent = message;
            warningDiv.style.display = message ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <header>
        <h1>Edit Course Request</h1>
        <a href="admin_requests.php" class="back">Back to Requests</a>
    </header>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert error">
            An error occurred while processing your request. Please try again.
        </div>
    <?php endif; ?>

    <div class="edit-form">
        <div class="info-group">
            <strong>Student:</strong> <?php echo htmlspecialchars($request['username']); ?>
        </div>
        <div class="info-group">
            <strong>Course:</strong> <?php echo htmlspecialchars($request['course_title']); ?>
        </div>
        <div class="info-group">
            <strong>Category:</strong> <?php echo htmlspecialchars($request['category_name']); ?>
        </div>
        <div class="info-group">
            <strong>Request Date:</strong> <?php echo $request['request_date']; ?>
        </div>
        <div class="info-group">
            <strong>Current Status:</strong> 
            <span class="status-badge status-<?php echo $request['status']; ?>">
                <?php echo ucfirst($request['status']); ?>
            </span>
            <?php if ($request['is_enrolled'] > 0): ?>
                <span class="enrolled-badge">Enrolled</span>
            <?php endif; ?>
        </div>

        <div id="statusWarning" class="warning-message"></div>

        <form method="POST" id="requestForm">
            <div class="form-group">
                <label for="status">Update Status:</label>
                <select name="status" id="status" required onchange="showWarning('status_change')">
                    <option value="pending" <?php echo $request['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $request['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $request['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            <div class="button-group">
                <button type="submit" name="action" value="update" class="submit-btn" 
                        onclick="return confirm('Are you sure you want to update this request status?');">
                    Update Status
                </button>
                <button type="submit" name="action" value="request_again" class="reset-btn"
                        onclick="showWarning('request_again'); return confirm('Are you sure you want to reset this request? This will remove any existing enrollment.');">
                    Request Again
                </button>
            </div>
        </form>
    </div>
</body>
</html> 