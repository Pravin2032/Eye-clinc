<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

// Database connection parameters
$servername = "localhost";
$dbUsername = "root";  // Change if needed
$dbPassword = "";      // Change if needed
$dbname = "eye_clinic";

// Create database connection
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Get user details
$email = $_SESSION['email'];
$query = "SELECT * FROM users WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle profile picture upload
$uploadSuccess = false;
$uploadError = "";
$infoUpdateSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateProfile'])) {
    // Process profile picture upload
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/profile_pictures/";
        
        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Generate unique filename
        $fileExtension = pathinfo($_FILES['profileImage']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('profile_') . '.' . $fileExtension;
        $targetFile = $targetDir . $newFileName;
        
        // Check if file is an image
        $imageFileType = strtolower($fileExtension);
        if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
            // Move uploaded file to destination
            if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $targetFile)) {
                // Update profile picture path in database - CHANGED 'profile_picture' to 'image'
                $updateQuery = "UPDATE users SET image = ? WHERE email = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("ss", $targetFile, $email);
                
                if ($updateStmt->execute()) {
                    $uploadSuccess = true;
                    
                    // Update user variable with new profile picture - CHANGED 'profile_picture' to 'image'
                    $user['image'] = $targetFile;
                } else {
                    $uploadError = "Failed to update profile picture in database.";
                }
            } else {
                $uploadError = "Failed to upload image. Please try again.";
            }
        } else {
            $uploadError = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }
    
    // Update user info if provided
    $fullName = isset($_POST['fullName']) ? $_POST['fullName'] : $user['full_name'];
    $phone = isset($_POST['phone']) ? $_POST['phone'] : $user['phone'];
    
    $updateInfoQuery = "UPDATE users SET full_name = ?, phone = ? WHERE email = ?";
    $updateInfoStmt = $conn->prepare($updateInfoQuery);
    $updateInfoStmt->bind_param("sss", $fullName, $phone, $email);
    
    if ($updateInfoStmt->execute()) {
        // Update the user array with new values
        $user['full_name'] = $fullName;
        $user['phone'] = $phone;
        $infoUpdateSuccess = true;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Eye Clinic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --success-color: #4caf50;
            --danger-color: #f44336;
            --warning-color: #ff9800;
            --info-color: #2196f3;
            --dark-color: #333;
            --light-color: #f9f9f9;
            --grey-color: #f0f0f0;
            --text-color: #333;
            --text-secondary: #666;
            --border-radius: 8px;
            --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: var(--text-color);
            line-height: 1.6;
        }

        /* Layout */
        .dashboard-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background-color: white;
            box-shadow: var(--box-shadow);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: var(--primary-color);
        }

        .logo i {
            font-size: 1.8rem;
        }

        .logo h1 {
            font-size: 1.4rem;
            font-weight: 600;
        }

        .user-nav {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            background-color: var(--grey-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-img i {
            font-size: 1.5rem;
            color: var(--text-secondary);
        }

        .user-nav span {
            font-weight: 500;
        }

        .logout-btn {
            background-color: var(--light-color);
            color: var(--text-color);
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background-color: var(--grey-color);
        }

        /* Content Layout */
        .content-area {
            display: flex;
            flex: 1;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: white;
            padding: 2rem 0;
            box-shadow: var(--box-shadow);
            height: calc(100vh - 73px);
            position: sticky;
            top: 73px;
            overflow-y: auto;
        }

        .nav-item {
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            cursor: pointer;
            transition: var(--transition);
            color: var(--text-secondary);
        }

        .nav-item:hover {
            background-color: rgba(67, 97, 238, 0.05);
            color: var(--primary-color);
        }

        .nav-item.active {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
            border-left: 4px solid var(--primary-color);
        }

        .nav-item i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem;
            background-color: #f5f7fa;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
            position: relative;
            padding-bottom: 0.5rem;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 4px;
            background-color: var(--primary-color);
            border-radius: 4px;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .alert-success {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .alert-error {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        /* Profile Container */
        .profile-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .profile-picture {
            position: relative;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            background-color: var(--grey-color);
            box-shadow: var(--box-shadow);
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .edit-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: var(--primary-color);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .edit-icon:hover {
            background-color: var(--secondary-color);
        }

        .user-info {
            flex: 1;
        }

        .user-info h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .user-info p {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        /* Profile Form */
        .profile-form {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-family: inherit;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .form-group input[readonly] {
            background-color: var(--grey-color);
            cursor: not-allowed;
        }

        .submit-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: var(--border-radius);
            font-family: inherit;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .submit-btn:hover {
            background-color: var(--secondary-color);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .upload-area {
            border: 2px dashed #ddd;
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            margin: 1.5rem 0;
            cursor: pointer;
            transition: var(--transition);
        }

        .upload-area:hover {
            border-color: var(--primary-color);
        }

        .upload-area i {
            font-size: 2.5rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        input[type="file"] {
            display: none;
        }

        .preview-container {
            display: none;
            margin-top: 1.5rem;
            text-align: center;
        }

        .image-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: var(--border-radius);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .content-area {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                padding: 0;
            }

            .nav-item {
                padding: 1rem 2rem;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .user-nav {
                width: 100%;
                justify-content: space-between;
            }

            .profile-container {
                flex-direction: column;
                text-align: center;
            }

            .profile-picture {
                margin: 0 auto;
            }

            .main-content {
                padding: 1rem;
            }
        }

        @media (max-width: 576px) {
            .logo h1 {
                font-size: 1.2rem;
            }

            .user-nav span {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-eye"></i>
                <h1>VisionCare Clinic</h1>
            </div>
            <div class="user-nav">
                <div class="profile-img">
                    <?php if(!empty($user['image']) && file_exists($user['image'])): ?>
                        <img src="<?php echo $user['image']; ?>" alt="Profile Picture">
                    <?php else: ?>
                        <i class="fas fa-user"></i>
                    <?php endif; ?>
                </div>
                <span><?php echo isset($user['full_name']) ? $user['full_name'] : $user['email']; ?></span>
                <a href="login.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        
        <div class="content-area">
            <div class="sidebar">
                <div class="nav-item active">
                   <i class="fas fa-user"></i> My Profile 
                </div>
                <div class="nav-item">
                    <a href="user appointment.php"><i class="fas fa-calendar-alt"></i> Appointment</a>
                </div>
                <div class="nav-item">
    <a href="user product.php"><i class="fas fa-shopping-bag"></i> Accessories</a>
</div>

                <div class="nav-item">
                    <a href="user settings.php"><i class="fas fa-cog"></i> Settings</a>
                </div>
            </div>
            
            <div class="main-content">
                <?php if($uploadSuccess || $infoUpdateSuccess): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php if($uploadSuccess && $infoUpdateSuccess): ?>
                        Profile information and picture updated successfully!
                    <?php elseif($uploadSuccess): ?>
                        Profile picture updated successfully!
                    <?php else: ?>
                        Profile information updated successfully!
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($uploadError)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $uploadError; ?>
                </div>
                <?php endif; ?>
                
                <h1 class="section-title">My Profile</h1>
                
                <div class="profile-container">
                    <div class="profile-picture">
                        <?php if(!empty($user['image']) && file_exists($user['image'])): ?>
                            <img src="<?php echo $user['image']; ?>" alt="Profile Picture">
                        <?php else: ?>
                            <i class="fas fa-user" style="font-size: 75px; color: #ccc; display: flex; align-items: center; justify-content: center; height: 100%;"></i>
                        <?php endif; ?>
                        <div class="edit-icon" onclick="openUploadModal()">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                    
                    <div class="user-info">
                        <h2><?php echo isset($user['full_name']) && !empty($user['full_name']) ? $user['full_name'] : 'Complete Your Profile'; ?></h2>
                        <p><i class="fas fa-envelope"></i> <?php echo $user['email']; ?></p>
                        <?php if(isset($user['phone']) && !empty($user['phone'])): ?>
                        <p><i class="fas fa-phone"></i> <?php echo $user['phone']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <h2 class="section-title">Personal Information</h2>
                
                <div class="profile-form">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" id="fullName" name="fullName" value="<?php echo isset($user['full_name']) ? $user['full_name'] : ''; ?>" placeholder="Enter your full name">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" value="<?php echo $user['email']; ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo isset($user['phone']) ? $user['phone'] : ''; ?>" placeholder="Enter your phone number">
                        </div>
                        
                        <!-- Hidden profile image input, updated to use 'image' -->
                        <input type="hidden" name="currentProfileImage" value="<?php echo isset($user['image']) ? $user['image'] : ''; ?>">
                        
                        <button type="submit" name="updateProfile" class="submit-btn">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Profile Picture Upload Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeUploadModal()">&times;</span>
            <h2>Update Profile Picture</h2>
            
            <form id="uploadForm" method="POST" action="" enctype="multipart/form-data">
                <div class="upload-area" id="uploadArea" onclick="triggerFileInput()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Click to select image or drag and drop</p>
                </div>
                
                <input type="file" id="profileImageInput" name="profileImage" accept="image/*" onchange="previewImage(this)">
                
                <div id="previewContainer" class="preview-container">
                    <img id="imagePreview" class="image-preview">
                </div>
                
                <button type="submit" name="updateProfile" class="submit-btn">
                    <i class="fas fa-upload"></i> Upload Image
                </button>
            </form>
        </div>
    </div>

    <script>
        // Modal functions
        function openUploadModal() {
            document.getElementById('uploadModal').style.display = 'block';
        }
        
        function closeUploadModal() {
            document.getElementById('uploadModal').style.display = 'none';
        }
        
        // File input functions
        function triggerFileInput() {
            document.getElementById('profileImageInput').click();
        }
        
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('previewContainer').style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Drag and drop functionality
        const uploadArea = document.getElementById('uploadArea');
        
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#4361ee';
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#ddd';
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#ddd';
            
            if (e.dataTransfer.files.length) {
                document.getElementById('profileImageInput').files = e.dataTransfer.files;
                previewImage(document.getElementById('profileImageInput'));
            }
        });
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('uploadModal');
            if (event.target == modal) {
                closeUploadModal();
            }
        }
        
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(function() {
                    alerts.forEach(function(alert) {
                        alert.style.opacity = '0';
                        alert.style.transition = 'opacity 0.5s ease';
                        setTimeout(function() {
                            alert.style.display = 'none';
                        }, 500);
                    });
                }, 5000);
            }
        });
    </script>
</body>
</html>