<?php
// Add these lines at the very top of the file, before session_start()
ini_set('upload_max_filesize', '2048M');
ini_set('post_max_size', '2048M');
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');
ini_set('memory_limit', '256M');

session_start();
include('../controller/db_connection.php');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    die("Access Denied");
}

// Fetch courses for dropdown
$courses_query = "SELECT id, title FROM courses";
$courses_result = mysqli_query($conn, $courses_query);

// Add this function at the top
function saveUploadedFile($file) {
    $upload_dir = "../public/uploads/lectures/";
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        error_log("Invalid file extension: " . $file_extension);
        return false;
    }
    
    // Generate unique filename
    $new_filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    // For debugging
    error_log("File details: " . print_r($file, true));
    error_log("Upload path: " . $upload_path);
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return 'uploads/lectures/' . $new_filename;
    }
    
    error_log("Failed to move uploaded file. Error: " . error_get_last()['message']);
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['course_id'])) {
        die("Error: Course ID is required");
    }
    
    $course_id = $_POST['course_id'];
    $title = trim($_POST['title']);

    // Initialize arrays
    $nav_items = [];
    $content = [];
    $section_media = [];

    // Handling sections dynamically
    if (isset($_POST['sections']) && is_array($_POST['sections'])) {
        foreach ($_POST['sections'] as $index => $section) {
            if (isset($section['nav_item']) && isset($section['content'])) {
                $nav_item = trim($section['nav_item']);
                $section_content = trim($section['content']);
                
                if (!empty($nav_item) && !empty($section_content)) {
                    $nav_items[] = $nav_item;
                    $content[] = $section_content;
                    
                    // Handle media file for this section
                    if (isset($_FILES['sections']['name'][$index]['media']) && 
                        $_FILES['sections']['error'][$index]['media'] === UPLOAD_ERR_OK) {
                        
                        $file = [
                            'name' => $_FILES['sections']['name'][$index]['media'],
                            'type' => $_FILES['sections']['type'][$index]['media'],
                            'tmp_name' => $_FILES['sections']['tmp_name'][$index]['media'],
                            'error' => $_FILES['sections']['error'][$index]['media'],
                            'size' => $_FILES['sections']['size'][$index]['media']
                        ];
                        
                        $file_path = saveUploadedFile($file);
                        if ($file_path) {
                            $section_media[] = $file_path;
                        } else {
                            $section_media[] = null;
                        }
                    } else {
                        $section_media[] = null;
                    }
                }
            }
        }
    }

    // Debug output - you can remove this later
    error_log("Nav Items: " . print_r($nav_items, true));
    error_log("Content: " . print_r($content, true));

    // Debug output to check what's being saved
    error_log("Section Media Array: " . print_r($section_media, true));

    // Only proceed if we have at least one section
    if (!empty($nav_items) && !empty($content)) {
        try {
            $encoded_content = json_encode($content, JSON_UNESCAPED_UNICODE);
            $encoded_nav_items = json_encode($nav_items, JSON_UNESCAPED_UNICODE);
            $encoded_section_media = json_encode($section_media);

            if ($encoded_content === false || $encoded_nav_items === false || $encoded_section_media === false) {
                throw new Exception("JSON encoding failed");
            }

            $insert_query = "INSERT INTO lectures (course_id, title, content, nav_items, section_media) 
                            VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("issss", $course_id, $title, $encoded_content, $encoded_nav_items, $encoded_section_media);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            header("Location: admin_lectures.php");
            exit();

        } catch (Exception $e) {
            error_log("Error in lecture creation: " . $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Error: Please add at least one section with content";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Lecture</title>
    <link rel="stylesheet" href="../public/CSS/admin_styles.css">
    <style>
        :root {
            --primary-blue: #007bff;
            --hover-blue: #0056b3;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
        }

        header {
            background-color: var(--primary-blue);
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        header h1 {
            color: white;
            margin: 0;
            font-size: 2.5em;
            font-weight: bold;
        }

        header a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border: 2px solid white;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
            transition: all 0.3s;
        }

        header a:hover {
            background: white;
            color: var(--primary-blue);
        }

        form {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .section-group {
            background: #f8f9fa;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #444;
        }

        input[type="text"], textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        button[type="submit"] {
            background-color: var(--primary-blue);
            color: white;
            width: 100%;
            margin-top: 20px;
            font-size: 1.1em;
        }

        button[type="submit"]:hover {
            background-color: var(--hover-blue);
        }

        button[type="button"] {
            background-color: #6c757d;
            color: white;
        }

        button[type="button"]:hover {
            background-color: #5a6268;
        }

        .remove-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 15px;
            margin-top: 10px;
        }

        .remove-btn:hover {
            background-color: #c82333;
        }

        input[name="title"] {
            font-size: 1.2em;
            padding: 15px;
            text-align: center;
            font-weight: bold;
        }

        .media-upload-area {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            margin: 15px 0;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .media-upload-area:hover {
            border-color: var(--primary-blue);
            background: #f8f9fa;
        }

        .media-preview {
            max-width: 100%;
            margin-top: 10px;
            border-radius: 4px;
        }

        video.media-preview {
            width: 100%;
            max-height: 300px;
        }

        .media-upload-area.dragover {
            background: #e3f2fd;
            border-color: var(--primary-blue);
        }
    </style>
    <script>
        function addSection() {
            const container = document.getElementById("sections-container");
            const sectionCount = container.getElementsByClassName("section-group").length;
            
            const sectionHTML = `
                <div class="section-group">
                    <label>Navigation Item:</label>
                    <input type="text" name="sections[${sectionCount}][nav_item]" required>
                    
                    <label>Content:</label>
                    <textarea name="sections[${sectionCount}][content]" required></textarea>
                    
                    <label>Media (Optional):</label>
                    <div class="media-upload-area" onclick="triggerFileInput(this)" 
                         ondrop="handleDrop(event, this)" ondragover="handleDragOver(event)" 
                         ondragleave="handleDragLeave(event)">
                        <p>Drag & drop media here or click to upload</p>
                        <input type="file" name="sections[${sectionCount}][media]" 
                               accept="image/*,video/*" style="display: none" 
                               onchange="handleFileSelect(this)">
                        <img class="media-preview" src="" alt="Preview" style="display: none;">
                        <button type="button" class="remove-btn" onclick="removeSection(this)">Remove</button>
                    </div>
                </div>`;
            
            container.insertAdjacentHTML("beforeend", sectionHTML);
        }

        function triggerFileInput(area) {
            area.querySelector('input[type="file"]').click();
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.currentTarget.classList.add('dragover');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('dragover');
        }

        function handleDrop(e, area) {
            e.preventDefault();
            area.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length) {
                const fileInput = area.querySelector('input[type="file"]');
                fileInput.files = files;
                handleFileSelect(fileInput);
            }
        }

        function handleFileSelect(input) {
            const preview = input.parentElement.querySelector('.media-preview');
            const file = input.files[0];
            
            if (file) {
                const fileType = file.type.split('/')[0]; // 'image' or 'video'
                
                if (fileType === 'image') {
                    // Handle image preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (preview.tagName === 'VIDEO') {
                            // Replace video with img
                            const img = document.createElement('img');
                            img.className = 'media-preview';
                            img.src = e.target.result;
                            img.style.display = 'block';
                            preview.parentNode.replaceChild(img, preview);
                        } else {
                            preview.src = e.target.result;
                            preview.style.display = 'block';
                        }
                    }
                    reader.readAsDataURL(file);
                } else if (fileType === 'video') {
                    // Handle video preview
                    const videoURL = URL.createObjectURL(file);
                    if (preview.tagName === 'IMG') {
                        // Replace img with video
                        const video = document.createElement('video');
                        video.className = 'media-preview';
                        video.controls = true;
                        video.src = videoURL;
                        video.style.display = 'block';
                        preview.parentNode.replaceChild(video, preview);
                    } else {
                        preview.src = videoURL;
                        preview.style.display = 'block';
                    }
                }
            }
        }

        function removeSection(button) {
            button.parentElement.remove();
            // Reindex the remaining sections
            const container = document.getElementById("sections-container");
            const sections = container.getElementsByClassName("section-group");
            Array.from(sections).forEach((section, index) => {
                section.querySelector('input[type="text"]').name = `sections[${index}][nav_item]`;
                section.querySelector('textarea').name = `sections[${index}][content]`;
            });
        }
    </script>
</head>
<body>
    <header>
        <h1>Add Lecture</h1>
        <a href="admin_lectures.php">Back</a>
    </header>

    <form method="POST" enctype="multipart/form-data">
        <label>Course:</label>
        <select name="course_id" required>
            <?php while ($row = mysqli_fetch_assoc($courses_result)): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></option>
            <?php endwhile; ?>
        </select>

        <label>Title:</label>
        <input type="text" name="title" required>

        <div id="sections-container">
            <div class="section-group">
                <label>Navigation Item:</label>
                <input type="text" name="sections[0][nav_item]" required>
                
                <label>Content:</label>
                <textarea name="sections[0][content]" required></textarea>
                
                <label>Media (Optional):</label>
                <div class="media-upload-area" onclick="triggerFileInput(this)" 
                     ondrop="handleDrop(event, this)" ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)">
                    <p>Drag & drop media here or click to upload</p>
                    <input type="file" name="sections[0][media]" 
                           accept="image/*,video/*" style="display: none" 
                           onchange="handleFileSelect(this)">
                    <img class="media-preview" src="" alt="Preview" style="display: none;">
                    <button type="button" class="remove-btn" onclick="removeSection(this)">Remove</button>
                </div>
            </div>
        </div>
        <button type="button" onclick="addSection()">+ Add Section</button>

        <button type="submit">Save</button>
    </form>
</body>
</html>
