<?php
session_start();
include('../controller/db_connection.php');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    die("Access Denied");
}

// Fetch courses for dropdown
$courses_query = "SELECT id, title FROM courses";
$courses_result = mysqli_query($conn, $courses_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];
    $lab_name = trim($_POST['lab_name']);
    $lab_description = trim($_POST['lab_description']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert lab
        $lab_query = "INSERT INTO labs (name, description, course_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($lab_query);
        $stmt->bind_param("ssi", $lab_name, $lab_description, $course_id);
        $stmt->execute();
        $lab_id = $conn->insert_id;
        
        // Insert questions and choices
        foreach ($_POST['questions'] as $index => $question) {
            if (!empty($question['text'])) {
                // Insert question
                $question_query = "INSERT INTO questions (lab_id, question_text) VALUES (?, ?)";
                $stmt = $conn->prepare($question_query);
                $stmt->bind_param("is", $lab_id, $question['text']);
                $stmt->execute();
                $question_id = $conn->insert_id;
                
                // Insert choices
                foreach ($question['choices'] as $choice_index => $choice_text) {
                    if (!empty($choice_text)) {
                        $is_correct = isset($question['correct_answer']) && 
                                    $question['correct_answer'] == $choice_index ? 1 : 0;
                        
                        $choice_query = "INSERT INTO choices (question_id, choice_text, is_correct) 
                                       VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($choice_query);
                        $stmt->bind_param("isi", $question_id, $choice_text, $is_correct);
                        $stmt->execute();
                    }
                }
            }
        }
        
        $conn->commit();
        header("Location: admin_labs.php");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Lab</title>
    <link rel="stylesheet" href="../public/CSS/admin_styles_1.css">
    <style>
        .question-group {
            background: #f8f9fa;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .choices-group {
            margin-left: 20px;
            margin-top: 10px;
        }

        .choice-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            width: 100%;
        }

        .choice-item input[type="text"] {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            min-width: 200px;
        }

        .choice-item input[type="radio"] {
            margin: 0;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .choice-label {
            margin-right: 10px;
            min-width: 80px;
        }

        #add-question-btn {
            margin: 20px 0;
            background-color: var(--primary-blue);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Add New Lab</h1>
        <a href="admin_labs.php">Back</a>
    </header>

    <form method="POST" class="add-form">
        <label>Course:</label>
        <select name="course_id" required>
            <?php while ($row = mysqli_fetch_assoc($courses_result)): ?>
                <option value="<?php echo $row['id']; ?>">
                    <?php echo htmlspecialchars($row['title']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Lab Name:</label>
        <input type="text" name="lab_name" required>

        <label>Description:</label>
        <textarea name="lab_description" required></textarea>

        <div id="questions-container">
            <?php for($i = 0; $i < 10; $i++): ?>
                <div class="question-group">
                    <label>Question <?php echo $i + 1; ?>:</label>
                    <input type="text" name="questions[<?php echo $i; ?>][text]" required>
                    
                    <div class="choices-group">
                        <?php for($j = 0; $j < 4; $j++): ?>
                            <div class="choice-item">
                                <input type="radio" 
                                       name="questions[<?php echo $i; ?>][correct_answer]" 
                                       value="<?php echo $j; ?>" 
                                       required>
                                <span class="choice-label">Choice <?php echo $j + 1; ?>:</span>
                                <input type="text" 
                                       name="questions[<?php echo $i; ?>][choices][]" 
                                       placeholder="Enter choice text" 
                                       required>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>

        <button type="button" id="add-question-btn" onclick="addQuestion()">+ Add Question</button>
        <button type="submit">Save Lab</button>
    </form>

    <script>
        function addQuestion() {
            const container = document.getElementById('questions-container');
            const questionCount = container.getElementsByClassName('question-group').length;
            
            const questionHTML = `
                <div class="question-group">
                    <label>Question ${questionCount + 1}:</label>
                    <input type="text" name="questions[${questionCount}][text]" required>
                    
                    <div class="choices-group">
                        ${Array.from({length: 4}, (_, i) => `
                            <div class="choice-item">
                                <input type="radio" 
                                       name="questions[${questionCount}][correct_answer]" 
                                       value="${i}" 
                                       required>
                                <span class="choice-label">Choice ${i + 1}:</span>
                                <input type="text" 
                                       name="questions[${questionCount}][choices][]" 
                                       placeholder="Enter choice text" 
                                       required>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', questionHTML);
        }
    </script>
</body>
</html> 
