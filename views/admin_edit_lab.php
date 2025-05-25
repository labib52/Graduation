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

// Fetch lab details
$lab_query = $conn->prepare("SELECT * FROM labs WHERE id = ?");
$lab_query->bind_param("i", $lab_id);
$lab_query->execute();
$lab = $lab_query->get_result()->fetch_assoc();

if (!$lab) {
    die("Lab not found");
}

// Fetch courses for dropdown
$courses_query = "SELECT id, title FROM courses";
$courses_result = mysqli_query($conn, $courses_query);

// Fetch questions and choices
$questions_query = $conn->prepare("
    SELECT q.*, GROUP_CONCAT(CONCAT(c.id, ':', c.choice_text, ':', c.is_correct) SEPARATOR '||') as choices
    FROM questions q
    LEFT JOIN choices c ON q.id = c.question_id
    WHERE q.lab_id = ?
    GROUP BY q.id
    ORDER BY q.id
");
$questions_query->bind_param("i", $lab_id);
$questions_query->execute();
$questions_result = $questions_query->get_result();
$questions = [];
while ($row = $questions_result->fetch_assoc()) {
    $choices = [];
    if ($row['choices']) {
        foreach (explode("||", $row['choices']) as $choice) {
            list($id, $text, $is_correct) = explode(":", $choice);
            $choices[] = [
                'id' => $id,
                'text' => $text,
                'is_correct' => $is_correct
            ];
        }
    }
    $row['choices_array'] = $choices;
    $questions[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];
    $lab_name = trim($_POST['lab_name']);
    $lab_description = trim($_POST['lab_description']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update lab
        $update_lab = $conn->prepare("UPDATE labs SET name = ?, description = ?, course_id = ? WHERE id = ?");
        $update_lab->bind_param("ssii", $lab_name, $lab_description, $course_id, $lab_id);
        $update_lab->execute();

        // Delete existing questions and choices (cascade delete will handle choices)
        $delete_questions = $conn->prepare("DELETE FROM questions WHERE lab_id = ?");
        $delete_questions->bind_param("i", $lab_id);
        $delete_questions->execute();

        // Insert updated questions and choices
        foreach ($_POST['questions'] as $index => $question) {
            if (!empty($question['text'])) {
                $question_query = "INSERT INTO questions (lab_id, question_text) VALUES (?, ?)";
                $stmt = $conn->prepare($question_query);
                $stmt->bind_param("is", $lab_id, $question['text']);
                $stmt->execute();
                $question_id = $conn->insert_id;
                
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
    <title>Edit Lab</title>
    <link rel="stylesheet" href="../public/CSS/admin_styles_1.css">
    <style>
        .question-group {
            background: #f8f9fa;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            position: relative;
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
            min-width: 200px;
        }

        .choice-item input[type="radio"] {
            width: 18px;
            height: 18px;
            margin: 0;
            cursor: pointer;
            flex-shrink: 0;
        }

        .choice-label {
            min-width: 80px;
            margin: 0 10px;
        }

        .delete-question-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--danger-red);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .delete-question-btn:hover {
            background-color: var(--hover-red);
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
        <h1>Edit Lab</h1>
        <a href="admin_labs.php">Back</a>
    </header>

    <form method="POST" class="add-form">
        <label>Course:</label>
        <select name="course_id" required>
            <?php while ($row = mysqli_fetch_assoc($courses_result)): ?>
                <option value="<?php echo $row['id']; ?>" 
                        <?php echo ($row['id'] == $lab['course_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($row['title']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Lab Name:</label>
        <input type="text" name="lab_name" required value="<?php echo htmlspecialchars($lab['name']); ?>">

        <label>Description:</label>
        <textarea name="lab_description" required><?php echo htmlspecialchars($lab['description']); ?></textarea>

        <div id="questions-container">
            <?php foreach($questions as $i => $question): ?>
                <div class="question-group">
                    <button type="button" class="delete-question-btn" onclick="deleteQuestion(this)">Delete Question</button>
                    <label>Question <?php echo $i + 1; ?>:</label>
                    <input type="text" 
                           name="questions[<?php echo $i; ?>][text]" 
                           value="<?php echo htmlspecialchars($question['question_text']); ?>" 
                           required>
                    
                    <div class="choices-group">
                        <?php foreach($question['choices_array'] as $j => $choice): ?>
                            <div class="choice-item">
                                <input type="radio" 
                                       name="questions[<?php echo $i; ?>][correct_answer]" 
                                       value="<?php echo $j; ?>" 
                                       <?php echo $choice['is_correct'] ? 'checked' : ''; ?>
                                       required>
                                <span class="choice-label">Choice <?php echo $j + 1; ?>:</span>
                                <input type="text" 
                                       name="questions[<?php echo $i; ?>][choices][]" 
                                       value="<?php echo htmlspecialchars($choice['text']); ?>"
                                       placeholder="Enter choice text" 
                                       required>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" id="add-question-btn" onclick="addQuestion()">+ Add Question</button>
        <button type="submit">Save Changes</button>
    </form>

    <script>
        function addQuestion() {
            const container = document.getElementById('questions-container');
            const questionCount = container.getElementsByClassName('question-group').length;
            
            const questionHTML = `
                <div class="question-group">
                    <button type="button" class="delete-question-btn" onclick="deleteQuestion(this)">Delete Question</button>
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
            reindexQuestions();
        }

        function deleteQuestion(button) {
            const container = document.getElementById('questions-container');
            const questionCount = container.getElementsByClassName('question-group').length;
            
            // Only allow deletion if there's more than one question
            if (questionCount > 1) {
                if (confirm('Are you sure you want to delete this question?')) {
                    button.closest('.question-group').remove();
                    reindexQuestions();
                }
            } else {
                alert('You cannot delete the last question. At least one question is required.');
            }
        }

        function reindexQuestions() {
            const container = document.getElementById('questions-container');
            const questions = container.getElementsByClassName('question-group');
            
            Array.from(questions).forEach((question, index) => {
                // Update question label
                question.querySelector('label').textContent = `Question ${index + 1}:`;
                
                // Update question text input name
                const questionInput = question.querySelector('input[type="text"]');
                questionInput.name = `questions[${index}][text]`;
                
                // Update choices
                const choices = question.getElementsByClassName('choice-item');
                Array.from(choices).forEach((choice, choiceIndex) => {
                    const radio = choice.querySelector('input[type="radio"]');
                    const textInput = choice.querySelector('input[type="text"]');
                    
                    radio.name = `questions[${index}][correct_answer]`;
                    radio.value = choiceIndex;
                    textInput.name = `questions[${index}][choices][]`;
                });
            });
        }
    </script>
</body>
</html> 
