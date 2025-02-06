<?php
// Admin utility functions
include 'admin_db.php';

// Get total courses
function getTotalCourses($conn) {
    $query = "SELECT COUNT(*) AS total FROM courses";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    return $data['total'];
}
// Get only non-admin students (is_admin = 0)
function getNonAdminStudents($conn) {
    $query = "SELECT * FROM users WHERE is_admin = 0";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
// Get total students
function getTotalStudents($conn) {
    $query = "SELECT COUNT(*) AS total FROM users";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    return $data['total'];
}

// Get categories
function getCategories($conn) {
    $query = "SELECT * FROM categories";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Add category
function addCategory($conn, $name) {
    $query = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $name);
    $stmt->execute();
}

// Get courses
function getCourses($conn) {
    $query = "SELECT * FROM courses";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Add course
function addCourse($conn, $title, $description, $category_id, $level, $is_top_course, $requirements, $price, $status) {
    $query = "INSERT INTO courses (title, description, category_id, level, is_top_course, requirements, price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssisdsds", $title, $description, $category_id, $level, $is_top_course, $requirements, $price, $status);
    $stmt->execute();
}

// Get students
function getStudents($conn) {
    $query = "SELECT * FROM users";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Add student
function addStudent($conn, $name, $email, $password, $payment_info, $is_admin, $is_2fa_enabled, $security_question, $security_answer) {
    $query = "INSERT INTO users (username, email, password, payment_info, is_admin, is_2fa_enabled, security_question, security_answer) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssisss", $name, $email, $password, $payment_info, $is_admin, $is_2fa_enabled, $security_question, $security_answer);
    $stmt->execute();
}

// Update student
function updateStudent($conn, $student_id, $username, $email, $password, $is_admin, $is_2fa_enabled, $security_question, $security_answer) {
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $query = "UPDATE users SET username = ?, email = ?, password = ?, is_admin = ?, is_2fa_enabled = ?, security_question = ?, security_answer = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssisssi", $username, $email, $hashed_password, $is_admin, $is_2fa_enabled, $security_question, $security_answer, $student_id);
    } else {
        $query = "UPDATE users SET username = ?, email = ?, is_admin = ?, is_2fa_enabled = ?, security_question = ?, security_answer = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssisssi", $username, $email, $is_admin, $is_2fa_enabled, $security_question, $security_answer, $student_id);
    }
    $stmt->execute();
}

// Enroll student
function enrollStudent($conn, $student_id, $course_id) {
    $query = "INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $student_id, $course_id);
    $stmt->execute();
}

// Delete student
function deleteStudent($conn, $student_id) {
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
}

// Delete course
function deleteCourse($conn, $course_id) {
    $query = "DELETE FROM courses WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
}

// Delete category
function deleteCategory($conn, $category_id) {
    $query = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
}
