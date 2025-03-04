<?php
session_start();
include('../controller/db_connection.php');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    die("Access Denied");
}

$lecture_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($lecture_id <= 0) {
    die("Invalid Lecture ID");
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Get media paths before deleting
    $media_query = "SELECT section_media FROM lectures WHERE id = ?";
    $stmt = $conn->prepare($media_query);
    $stmt->bind_param("i", $lecture_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $lecture = $result->fetch_assoc();

    if ($lecture && $lecture['section_media']) {
        $media_paths = json_decode($lecture['section_media'], true);
        // Delete media files
        foreach ($media_paths as $path) {
            if ($path) {
                $full_path = "../public/" . $path;
                if (file_exists($full_path)) {
                    unlink($full_path);
                }
            }
        }
    }

    // Delete lecture from database
    $delete_query = "DELETE FROM lectures WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $lecture_id);
    $stmt->execute();
    
    $conn->commit();
    header("Location: admin_lectures.php");
    exit();
    
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}
?> 