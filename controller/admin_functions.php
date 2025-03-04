<?php
include('../controller/db_connection.php');
include('../model/adminmodel.php');

// Get total courses
function getTotalCourses($conn) {
    $result = adminModel::getTotalCourses($conn);
    return mysqli_fetch_assoc($result)['total'];
}

// Get only non-admin students
function getNonAdminStudents($conn) {
    $result = adminModel::getNonAdminStudents($conn);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Get total students
function getTotalStudents($conn) {
    $result = adminModel::getTotalStudents($conn);
    return mysqli_fetch_assoc($result)['total'];
}

// Get categories
function getCategories($conn) {
    $result = adminModel::getCategories($conn);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Add category
function addCategory($conn, $name) {
    return adminModel::addCategory($conn, $name);
}

// Get courses
function getCourses($conn) {
    $result = adminModel::getCourses($conn);
    $courses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }
    return $courses;
}

// Add course
function addCourse($conn, $title, $description, $category_id, $level, $is_top_course, $requirements, $price, $status) {
    return adminModel::addCourse($conn, $title, $description, $category_id, $level, $is_top_course, $requirements, $price, $status);
}

// Get students
function getStudents($conn) {
    $result = adminModel::getStudents($conn);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Add student
function addStudent($conn, $name, $email, $password, $payment_info, $is_admin, $is_2fa_enabled, $security_question, $security_answer) {
    return adminModel::addStudent($conn, $name, $email, $password, $payment_info, $is_admin, $is_2fa_enabled, $security_question, $security_answer);
}

// Update student
function updateStudent($conn, $student_id, $username, $email, $password, $is_admin, $is_2fa_enabled, $security_question, $security_answer) {
    return adminModel::updateStudent($conn, $student_id, $username, $email, $password, $is_admin, $is_2fa_enabled, $security_question, $security_answer);
}

// Enroll student
function enrollStudent($conn, $student_id, $course_id) {
    return adminModel::enrollStudent($conn, $student_id, $course_id);
}

// Delete student
function deleteStudent($conn, $student_id) {
    return adminModel::deleteStudent($conn, $student_id);
}

// Delete course
function deleteCourse($conn, $course_id) {
    return adminModel::deleteCourse($conn, $course_id);
}

// Delete category
function deleteCategory($conn, $category_id) {
    return adminModel::deleteCategory($conn, $category_id);
}
?>
