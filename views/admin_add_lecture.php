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
                    
                    // Handle multiple media files for this section
                    $section_files = [];
                    if (isset($_FILES['sections']['name'][$index]['media']) && 
                        is_array($_FILES['sections']['name'][$index]['media'])) {
                        
                        foreach ($_FILES['sections']['name'][$index]['media'] as $fileIndex => $fileName) {
                            if ($_FILES['sections']['error'][$index]['media'][$fileIndex] === UPLOAD_ERR_OK) {
                                $file = [
                                    'name' => $_FILES['sections']['name'][$index]['media'][$fileIndex],
                                    'type' => $_FILES['sections']['type'][$index]['media'][$fileIndex],
                                    'tmp_name' => $_FILES['sections']['tmp_name'][$index]['media'][$fileIndex],
                                    'error' => $_FILES['sections']['error'][$index]['media'][$fileIndex],
                                    'size' => $_FILES['sections']['size'][$index]['media'][$fileIndex]
                                ];
                                
                                $file_path = saveUploadedFile($file);
                                if ($file_path) {
                                    $section_files[] = $file_path;
                                }
                            }
                        }
                    }
                    $section_media[] = $section_files;
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

        .lecture-content p {
            margin: 0;
            padding: 0.5em 0;
            line-height: 2;
        }

        .media-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .media-preview {
            max-width: 200px;
            max-height: 200px;
            object-fit: contain;
            margin: 5px;
        }

        video.media-preview {
            width: 200px;
        }
    </style>
    <script src="../public/js/tinymce/tinymce.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (typeof tinymce === "undefined") {
                console.error("TinyMCE not found. Check the script path.");
            } else {
                console.log("TinyMCE loaded successfully.");
                initializeTinyMCE();
            }

            // Add form submit handler
            document.querySelector('form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Update all TinyMCE instances before form submission
                tinymce.triggerSave();
                
                // Now submit the form
                this.submit();
            });
        });

        // Modify the initializeTinyMCE function
        function initializeTinyMCE() {
            tinymce.init({
                selector: 'textarea[name^="sections"][name$="[content]"]',
                plugins: 'advlist autolink lists link charmap print preview anchor code',
                toolbar: 'bold italic underline | bullist numlist | alignleft aligncenter alignright alignjustify | code',
                menubar: false,
                entity_encoding: 'raw',
                encoding: 'html',
                content_style: `
                    body { line-height: 2; }
                    p { margin: 0; padding: 0.5em 0; }
                    br { line-height: 2; }
                `,
                forced_root_block: 'p',
                setup: function(editor) {
                    editor.on('init', function() {
                        console.log("TinyMCE initialized successfully.");
                    });
                    editor.on('change', function() {
                        editor.save();
                    });
                },
                height: 300
            });
        }

        // Update the addSection function to include a unique ID for the textarea
        function addSection() {
            const container = document.getElementById("sections-container");
            const sectionCount = container.getElementsByClassName("section-group").length;
            
            const sectionHTML = `
                <div class="section-group">
                    <label>Navigation Item:</label>
                    <input type="text" name="sections[${sectionCount}][nav_item]" required>
                    
                    <label>Content:</label>
                    <textarea id="section-content-${sectionCount}" name="sections[${sectionCount}][content]" required></textarea>
                    
                    <label>Media (Optional):</label>
                    <div class="media-upload-area" onclick="triggerFileInput(this)" 
                         ondrop="handleDrop(event, this)" ondragover="handleDragOver(event)" 
                         ondragleave="handleDragLeave(event)">
                        <p>Drag & drop media here or click to upload</p>
                        <input type="file" name="sections[${sectionCount}][media][]" 
                               accept="image/*,video/*" style="display: none" 
                               onchange="handleFileSelect(this)" multiple>
                        <div class="media-preview-container"></div>
                        <button type="button" class="remove-btn" onclick="removeSection(this)">Remove</button>
                    </div>
                </div>`;
            
            container.insertAdjacentHTML("beforeend", sectionHTML);
            
            // Initialize TinyMCE for the new textarea
            tinymce.init({
                selector: `#section-content-${sectionCount}`,
                plugins: 'advlist autolink lists link charmap print preview anchor code',
                toolbar: 'bold italic underline | bullist numlist | alignleft aligncenter alignright alignjustify | code',
                menubar: false,
                entity_encoding: 'raw',
                encoding: 'html',
                content_style: `
                    body { line-height: 2; }
                    p { margin: 0; padding: 0.5em 0; }
                    br { line-height: 2; }
                `,
                forced_root_block: 'p',
                height: 300,
                setup: function(editor) {
                    editor.on('change', function() {
                        editor.save();
                    });
                }
            });
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
            const previewContainer = input.parentElement.querySelector('.media-preview-container');
            previewContainer.innerHTML = ''; // Clear existing previews
            
            Array.from(input.files).forEach(file => {
                const fileType = file.type.split('/')[0];
                
                if (fileType === 'image') {
                    // Handle image preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.className = 'media-preview';
                        img.src = e.target.result;
                        img.style.display = 'block';
                        previewContainer.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                } else if (fileType === 'video') {
                    // Handle video preview
                    const video = document.createElement('video');
                    video.className = 'media-preview';
                    video.controls = true;
                    video.src = URL.createObjectURL(file);
                    video.style.display = 'block';
                    previewContainer.appendChild(video);
                }
            });
        }

        function removeSection(button) {
            const section = button.closest('.section-group');
            const textarea = section.querySelector('textarea');
            
            // Remove TinyMCE instance before removing the section
            if (textarea) {
                tinymce.get(textarea.id)?.remove();
            }
            
            section.remove();
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
                <textarea id="section-content-0" name="sections[0][content]" required></textarea>
                
                <label>Media (Optional):</label>
                <div class="media-upload-area" onclick="triggerFileInput(this)" 
                     ondrop="handleDrop(event, this)" ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)">
                    <p>Drag & drop media here or click to upload</p>
                    <input type="file" name="sections[0][media][]" 
                           accept="image/*,video/*" style="display: none" 
                           onchange="handleFileSelect(this)" multiple>
                    <div class="media-preview-container"></div>
                    <button type="button" class="remove-btn" onclick="removeSection(this)">Remove</button>
                </div>
            </div>
        </div>
        <button type="button" onclick="addSection()">+ Add Section</button>

        <button type="submit">Save</button>
    </form>
</body>
</html>
