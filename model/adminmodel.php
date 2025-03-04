<?php
include "../controller/db_connection.php";

class adminModel
{
    // Get all courses
    public static function getCourses($conn) {
        $query = "SELECT * FROM courses";
        return mysqli_query($conn, $query);
    }

    // Get total courses count
    public static function getTotalCourses($conn) {
        $query = "SELECT COUNT(*) AS total FROM courses";
        return mysqli_query($conn, $query);
    }

    // Get only non-admin students
    public static function getNonAdminStudents($conn) {
        $query = "SELECT * FROM users WHERE is_admin = 0";
        return mysqli_query($conn, $query);
    }

    // Get total students count
    public static function getTotalStudents($conn) {
        $query = "SELECT COUNT(*) AS total FROM users";
        return mysqli_query($conn, $query);
    }

    // Get all categories
    public static function getCategories($conn) {
        $query = "SELECT * FROM categories";
        return mysqli_query($conn, $query);
    }

    // Add category
    public static function addCategory($conn, $name) {
        $query = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $name);
        return $stmt->execute();
    }

    // Add a course
    public static function addCourse($conn, $title, $description, $category_id, $level, $is_top_course, $requirements, $price, $status) {
        $query = "INSERT INTO courses (title, description, category_id, level, is_top_course, requirements, price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssisdsds", $title, $description, $category_id, $level, $is_top_course, $requirements, $price, $status);
        return $stmt->execute();
    }

    // Get all students
    public static function getStudents($conn) {
        $query = "SELECT * FROM users";
        return mysqli_query($conn, $query);
    }

    // Add a student
    public static function addStudent($conn, $name, $email, $password, $payment_info, $is_admin, $is_2fa_enabled, $security_question, $security_answer) {
        $query = "INSERT INTO users (username, email, password, payment_info, is_admin, is_2fa_enabled, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssisss", $name, $email, $password, $payment_info, $is_admin, $is_2fa_enabled, $security_question, $security_answer);
        return $stmt->execute();
    }

    // Update student
    public static function updateStudent($conn, $student_id, $username, $email, $password, $is_admin, $is_2fa_enabled, $security_question, $security_answer) {
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
        return $stmt->execute();
    }

    // Enroll student
    public static function enrollStudent($conn, $student_id, $course_id) {
        $query = "INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $student_id, $course_id);
        return $stmt->execute();
    }

    // Delete student
    public static function deleteStudent($conn, $student_id) {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $student_id);
        return $stmt->execute();
    }

    // Delete course
    public static function deleteCourse($conn, $course_id) {
        $query = "DELETE FROM courses WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $course_id);
        return $stmt->execute();
    }

    // Delete category
    public static function deleteCategory($conn, $category_id) {
        $query = "DELETE FROM categories WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $category_id);
        return $stmt->execute();
    }
}
?>
