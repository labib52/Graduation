<?php
session_start();
include('../controller/db_connection.php');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    die("Access Denied");
}

// Fetch all students (non-admin users)
$students_query = "SELECT id, username FROM users WHERE is_admin = 0 ORDER BY username";
$students_result = mysqli_query($conn, $students_query);

// Get selected user ID from POST or default to first student
$selected_user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
// Get sort order from POST or default to 'asc'
$score_sort = isset($_POST['score_sort']) && $_POST['score_sort'] === 'desc' ? 'desc' : 'asc';

// Fetch lab scores for selected user, ordered by lab then score
$scores_query = "SELECT 
    u.username,
    l.name as lab_name,
    sa.score
FROM students_answers sa
JOIN users u ON sa.user_id = u.id
JOIN labs l ON sa.lab_id = l.id
WHERE u.id = ?
ORDER BY l.name, sa.score $score_sort";

$stmt = $conn->prepare($scores_query);
$stmt->bind_param("i", $selected_user_id);
$stmt->execute();
$scores_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Scores</title>
    <link rel="stylesheet" href="../public/CSS/admin_styles.css">
    <style>
        .scores-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .scores-table th, .scores-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .scores-table th {
            background-color: #007bff;
            color: #fff;
            font-weight: 800;
            font-size: 20px;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-bottom: 3px solid #0056b3;
        }
        .scores-table tr:hover {
            background-color: #f5f5f5;
        }
        .score-cell {
            font-weight: 500;
        }
        .score-excellent {
            color: #28a745;
        }
        .score-good {
            color: #17a2b8;
        }
        .score-average {
            color: #ffc107;
        }
        .score-poor {
            color: #dc3545;
        }
        .user-select-form {
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .user-select-form select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            min-width: 200px;
        }
        .user-select-form button {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        .user-select-form button:hover {
            background-color: #0056b3;
        }
        .no-scores {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <header>
        <h1>Student Lab Scores</h1>
        <a href="admin_dashboard.php" class="back">Back to Dashboard</a>
    </header>

    <main>
        <form method="POST" class="user-select-form" id="scoreForm">
            <select name="user_id" required onchange="document.getElementById('scoreForm').submit();">
                <option value="">Select a student</option>
                <?php mysqli_data_seek($students_result, 0); while ($student = mysqli_fetch_assoc($students_result)): ?>
                    <option value="<?php echo $student['id']; ?>" <?php echo $selected_user_id == $student['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($student['username']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <input type="hidden" name="score_sort" id="score_sort" value="<?php echo $score_sort; ?>">
            <button type="submit">View Scores</button>
        </form>

        <?php if ($selected_user_id > 0): ?>
            <?php if ($scores_result->num_rows > 0): ?>
                <table class="scores-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Lab Name</th>
                            <th>Score
                                <button type="button" style="background:none;border:none;cursor:pointer;padding:0;margin-left:8px;vertical-align:middle;" onclick="toggleSort()">
                                    <?php if ($score_sort === 'asc'): ?>
                                        &#9650;
                                    <?php else: ?>
                                        &#9660;
                                    <?php endif; ?>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($scores_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
                                <td class="score-cell <?php 
                                    if ($row['score'] >= 9) echo 'score-excellent';
                                    elseif ($row['score'] >= 7) echo 'score-good';
                                    elseif ($row['score'] >= 5) echo 'score-average';
                                    else echo 'score-poor';
                                ?>">
                                    <?php echo $row['score']; ?>/10
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-scores">
                    No lab scores found for this student.
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <script>
    function toggleSort() {
        var sortInput = document.getElementById('score_sort');
        sortInput.value = sortInput.value === 'asc' ? 'desc' : 'asc';
        document.getElementById('scoreForm').submit();
    }
    </script>
</body>
</html> 