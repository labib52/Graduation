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

// Get lecture ID from URL
$lecture_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($lecture_id <= 0) {
    die("Invalid Lecture ID");
}

// Fetch lecture details
$lecture_query = $conn->prepare("SELECT * FROM lectures WHERE id = ?");
$lecture_query->bind_param("i", $lecture_id);
$lecture_query->execute();
$lecture = $lecture_query->get_result()->fetch_assoc();

if (!$lecture) {
    die("Lecture not found");
}

// Fetch courses for dropdown
$courses_query = "SELECT id, title FROM courses";
$courses_result = mysqli_query($conn, $courses_query);

// Function to handle file uploads
function saveUploadedFile($file) {
    $upload_dir = "../public/uploads/lectures/";
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        error_log("Invalid file extension: " . $file_extension);
        return false;
    }
    
    $new_filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return 'uploads/lectures/' . $new_filename;
    }
    
    error_log("Failed to move uploaded file. Error: " . error_get_last()['message']);
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $course_id = $_POST['course_id'];
        $title = trim($_POST['title']);

        // Initialize arrays
        $nav_items = [];
        $content = [];
        $section_media = [];

        // Get existing media paths
        $existing_media = json_decode($lecture['section_media'], true) ?: [];

        // Handling sections dynamically
        if (isset($_POST['sections']) && is_array($_POST['sections'])) {
            foreach ($_POST['sections'] as $index => $section) {
                if (isset($section['nav_item']) && isset($section['content'])) {
                    $nav_item = trim($section['nav_item']);
                    $section_content = trim($section['content']);
                    
                    if (!empty($nav_item) && !empty($section_content)) {
                        $nav_items[] = $nav_item;
                        $content[] = $section_content;
                        
                        // Keep existing media if no new file is uploaded
                        if (isset($section['existing_media']) && empty($_FILES['sections']['name'][$index]['media'])) {
                            $section_media[] = $section['existing_media'];
                        }
                        // Handle new media file upload
                        else if (isset($_FILES['sections']['name'][$index]['media']) && 
                            $_FILES['sections']['error'][$index]['media'] === UPLOAD_ERR_OK) {
                            
                            $file = [
                                'name' => $_FILES['sections']['name'][$index]['media'],
                                'type' => $_FILES['sections']['type'][$index]['media'],
                                'tmp_name' => $_FILES['sections']['tmp_name'][$index]['media'],
                                'error' => $_FILES['sections']['error'][$index]['media'],
                                'size' => $_FILES['sections']['size'][$index]['media']
                            ];
                            
                            // Delete old media file if it exists
                            if (isset($existing_media[$index]) && $existing_media[$index]) {
                                $old_file = "../public/" . $existing_media[$index];
                                if (file_exists($old_file)) {
                                    unlink($old_file);
                                }
                            }
                            
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

        // Only proceed if we have at least one section
        if (!empty($nav_items) && !empty($content)) {
            $encoded_content = json_encode($content, JSON_UNESCAPED_UNICODE);
            $encoded_nav_items = json_encode($nav_items, JSON_UNESCAPED_UNICODE);
            $encoded_section_media = json_encode($section_media);

            if ($encoded_content === false || $encoded_nav_items === false || $encoded_section_media === false) {
                throw new Exception("JSON encoding failed");
            }

            $update_query = "UPDATE lectures SET course_id = ?, title = ?, content = ?, nav_items = ?, section_media = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("issssi", $course_id, $title, $encoded_content, $encoded_nav_items, $encoded_section_media, $lecture_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            header("Location: admin_lectures.php");
            exit();
        }
    } catch (Exception $e) {
        error_log("Error in lecture update: " . $e->getMessage());
        echo "Error: " . $e->getMessage();
    }
}

// Decode existing lecture data
$nav_items = json_decode($lecture['nav_items'], true) ?: [];
$content = json_decode($lecture['content'], true) ?: [];
$section_media = json_decode($lecture['section_media'], true) ?: [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lecture</title>
    <link rel="stylesheet" href="../public/CSS/admin_styles.css">
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

        // Function to add new section (modified for edit page)
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
                        <input type="file" name="sections[${sectionCount}][media]" 
                               accept="image/*,video/*" style="display: none" 
                               onchange="handleFileSelect(this)">
                        <img class="media-preview" src="" alt="Preview" style="display: none;">
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

        // Add all the media handling functions
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
                const fileType = file.type.split('/')[0];
                
                if (fileType === 'image') {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (preview.tagName === 'VIDEO') {
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
                    const videoURL = URL.createObjectURL(file);
                    if (preview.tagName === 'IMG') {
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
            const section = button.closest('.section-group');
            const textarea = section.querySelector('textarea');
            
            // Remove TinyMCE instance before removing the section
            if (textarea) {
                tinymce.get(textarea.id)?.remove();
            }
            
            section.remove();
        }
    </script>
    <style>
        /* Add the same styles as in admin_add_lecture.php */
        .media-preview {
            max-width: 100%;
            margin-top: 10px;
            border-radius: 4px;
        }

        video.media-preview {
            width: 100%;
            max-height: 300px;
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

        .media-upload-area.dragover {
            background-color: #f8f9fa;
            border-color: var(--primary-blue);
        }

        .media-upload-area p {
            margin: 0;
            color: #666;
        }

        .delete-media-btn {
            background-color: var(--danger-red, #dc3545);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            margin-right: 10px;
        }

        .delete-media-btn:hover {
            background-color: var(--hover-red, #c82333);
        }

        .remove-section-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        .remove-section-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <header>
        <h1>Edit Lecture</h1>
        <a href="admin_lectures.php">Back</a>
    </header>

    <form method="POST" enctype="multipart/form-data">
        <label>Course:</label>
        <select name="course_id" required>
            <?php while ($row = mysqli_fetch_assoc($courses_result)): ?>
                <option value="<?php echo $row['id']; ?>" 
                        <?php echo $row['id'] == $lecture['course_id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($row['title']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Title:</label>
        <input type="text" name="title" required value="<?php echo htmlspecialchars($lecture['title']); ?>">

        <div id="sections-container">
            <?php foreach($nav_items as $i => $nav_item): ?>
                <div class="section-group">
                    <label>Navigation Item:</label>
                    <input type="text" 
                           name="sections[<?php echo $i; ?>][nav_item]" 
                           value="<?php echo htmlspecialchars($nav_item); ?>" 
                           required>
                    
                    <label>Content:</label>
                    <textarea name="sections[<?php echo $i; ?>][content]" required><?php 
                        echo htmlspecialchars($content[$i]); 
                    ?></textarea>
                    
                    <label>Media (Optional):</label>
                    <div class="media-upload-area" onclick="triggerFileInput(this)" 
                         ondrop="handleDrop(event, this)" ondragover="handleDragOver(event)" 
                         ondragleave="handleDragLeave(event)">
                        <p>Drag & drop media here or click to upload</p>
                        <input type="file" name="sections[<?php echo $i; ?>][media]" 
                               accept="image/*,video/*" style="display: none" 
                               onchange="handleFileSelect(this)">
                        <input type="hidden" name="sections[<?php echo $i; ?>][existing_media]" 
                               value="<?php echo htmlspecialchars($section_media[$i] ?? ''); ?>">
                        
                        <?php if (!empty($section_media[$i])): ?>
                            <?php
                            $media_path = $section_media[$i];
                            $extension = strtolower(pathinfo($media_path, PATHINFO_EXTENSION));
                            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                <img class="media-preview" src="../public/<?php 
                                    echo htmlspecialchars($media_path); 
                                ?>" alt="Preview" style="display: block;">
                            <?php elseif (in_array($extension, ['mp4', 'webm'])): ?>
                                <video class="media-preview" controls style="display: block;">
                                    <source src="../public/<?php echo htmlspecialchars($media_path); ?>" 
                                            type="video/<?php echo $extension; ?>">
                                </video>
                            <?php endif; ?>
                        <?php else: ?>
                            <img class="media-preview" src="" alt="Preview" style="display: none;">
                        <?php endif; ?>
                        
                        <?php if (!empty($section_media[$i])): ?>
                            <button type="button" class="delete-media-btn" onclick="deleteMedia(this)">Delete Media</button>
                        <?php endif; ?>
                        <button type="button" class="remove-section-btn" onclick="removeSection(this)">Remove Section</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" onclick="addSection()">+ Add Section</button>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html> 
