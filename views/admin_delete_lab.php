<?php
session_start();
include('../controller/db_connection.php');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    die("Access Denied");
}

$lab_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($lab_id <= 0) {
    die("Invalid Lab ID");
}

// Start transaction
$conn->begin_transaction();

try {
    // Delete lab and all related questions/choices (cascade delete will handle this)
    $delete_query = "DELETE FROM labs WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $lab_id);
    $stmt->execute();
    
    $conn->commit();
    header("Location: admin_labs.php");
    exit();
    
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}
?> 