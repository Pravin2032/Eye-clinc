<?php
// Database connection
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "eye_clinic";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for editing staff details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    // Get form data
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $role = $_POST['role'];
    $department = $_POST['department'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $startDate = $_POST['startDate'];
    $qualifications = $_POST['qualifications'];

    // Retrieve the current email in the database
    $sql = "SELECT email FROM staff WHERE email = '$email'";
    $result = $conn->query($sql);

    // If email exists, proceed to update
    if ($result->num_rows > 0) {
        // Check if the email has been changed
        $currentEmail = $result->fetch_assoc()['email'];

        if ($email !== $currentEmail) {
            // If the email has changed, check for duplicates
            $checkEmailSql = "SELECT id FROM staff WHERE email = '$email'";
            $result = $conn->query($checkEmailSql);

            if ($result->num_rows > 0) {
                $message = ["type" => "error", "text" => "Error: Email address already exists."];
            } else {
                // Update the staff record with the new email and other details
                $updateSql = "UPDATE staff SET 
                                first_name = '$firstName', 
                                last_name = '$lastName', 
                                role = '$role', 
                                department = '$department', 
                                email = '$email', 
                                phone = '$phone', 
                                address = '$address', 
                                start_date = '$startDate', 
                                qualifications = '$qualifications' 
                              WHERE email = '$currentEmail'";

                if ($conn->query($updateSql) === TRUE) {
                    $message = ["type" => "success", "text" => "Record updated successfully"];
                    // Redirect to avoid resubmission on page refresh
                    header("Location: " . $_SERVER['PHP_SELF'] . "?message=updated");
                    exit();
                } else {
                    $message = ["type" => "error", "text" => "Error: " . $updateSql . "<br>" . $conn->error];
                }
            }
        } else {
            // If email hasn't been changed, only update other fields
            $updateSql = "UPDATE staff SET 
                            first_name = '$firstName', 
                            last_name = '$lastName', 
                            role = '$role', 
                            department = '$department', 
                            phone = '$phone', 
                            address = '$address', 
                            start_date = '$startDate', 
                            qualifications = '$qualifications' 
                          WHERE email = '$currentEmail'";

            if ($conn->query($updateSql) === TRUE) {
                $message = ["type" => "success", "text" => "Record updated successfully"];
                // Redirect to avoid resubmission on page refresh
                header("Location: " . $_SERVER['PHP_SELF'] . "?message=updated");
                exit();
            } else {
                $message = ["type" => "error", "text" => "Error: " . $updateSql . "<br>" . $conn->error];
            }
        }
    } else {
        $message = ["type" => "error", "text" => "Error: Staff with this email does not exist."];
    }
}

// Handle form submission for adding new staff
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET['edit']) && !isset($_POST['update'])) {
    // Get form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $role = $_POST['role'];
    $department = $_POST['department'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $startDate = $_POST['startDate'];
    $qualifications = $_POST['qualifications'];

    // Check if email already exists in the database
    $checkEmailSql = "SELECT id FROM staff WHERE email = '$email'";
    $result = $conn->query($checkEmailSql);

    if ($result->num_rows > 0) {
        $message = ["type" => "error", "text" => "Error: Email address already exists."];
    } else {
        // Insert data into the database
        $sql = "INSERT INTO staff (first_name, last_name, role, department, email, phone, address, start_date, qualifications)
                VALUES ('$firstName', '$lastName', '$role', '$department', '$email', '$phone', '$address', '$startDate', '$qualifications')";

        if ($conn->query($sql) === TRUE) {
            $message = ["type" => "success", "text" => "Staff added successfully"];
            // Redirect to avoid resubmission on page refresh
            header("Location: " . $_SERVER['PHP_SELF'] . "?message=added");
            exit();
        } else {
            $message = ["type" => "error", "text" => "Error: " . $sql . "<br>" . $conn->error];
        }
    }
}

// Handle staff removal
if (isset($_GET['remove_first_name'])) {
    $removeFirstName = $conn->real_escape_string($_GET['remove_first_name']);
    $removeSql = "DELETE FROM staff WHERE first_name = '$removeFirstName'";

    if ($conn->query($removeSql) === TRUE) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
    exit();
}

// Process URL parameters
if (isset($_GET['message'])) {
    $msgType = $_GET['message'];
    if ($msgType == 'added') {
        $message = ["type" => "success", "text" => "Staff member added successfully!"];
    } else if ($msgType == 'updated') {
        $message = ["type" => "success", "text" => "Staff member updated successfully!"];
    }
}

// Fetch all staff members from the database
$sql = "SELECT * FROM staff";
$result = $conn->query($sql);
$staffMembers = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $staffMembers[] = $row;
    }
}

$conn->close();

// Define role and department options for reuse
$roleOptions = [
    "nurse" => "Nurse",
    "receptionist" => "Receptionist",
    "technician" => "Technician"
];

$departmentOptions = [
    "clinical" => "Clinical",
    "administrative" => "Administrative",
    "technical" => "Technical"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management - Eye Clinic Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #4361ee15;
            --secondary-color: #3f37c9;
            --success-color: #4caf50;
            --danger-color: #f44336;
            --warning-color: #ff9800;
            --text-color: #333;
            --text-light: #666;
            --background-color: #f9fafb;
            --card-color: #fff;
            --border-color: #e0e0e0;
            --border-radius: 8px;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
            padding-bottom: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .card {
            background: var(--card-color);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .header {
            padding: 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header h1 {
            margin: 0;
            font-size: 1.8rem;
        }

        .header-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            border-radius: var(--border-radius);
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-size: 0.95rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-light {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .btn-light:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            opacity: 0.9;
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-color);
        }

        .btn-outline:hover {
            background-color: #f5f5f5;
        }

        .form {
            padding: 2rem;
        }

        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .form-section h2 {
            font-size: 1.3rem;
            margin-bottom: 1.2rem;
            color: var(--primary-color);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-light);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: #fff;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            overflow-y: auto;
        }

        .modal-content {
            background-color: var(--card-color);
            border-radius: var(--border-radius);
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .modal-header {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background-color: var(--card-color);
            z-index: 1;
        }

        .modal-header h2 {
            font-size: 1.5rem;
            margin: 0;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-light);
        }

        .search-container {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 64px;
            background-color: var(--card-color);
            z-index: 1;
        }

        .search-input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px;
            transition: var(--transition);
        }

        .search-input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .staff-list {
            padding: 1.5rem;
        }

        .staff-card {
            background-color: #fff;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            overflow: hidden;
            transition: var(--transition);
        }

        .staff-card:hover {
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .staff-details {
            display: flex;
            justify-content: space-between;
            padding: 1.2rem;
        }

        .staff-info {
            flex: 1;
        }

        .staff-info h3 {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .staff-info p {
            margin-bottom: 0.3rem;
            color: var(--text-light);
        }

        .staff-actions {
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
        }

        .btn-edit, .btn-delete {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
            padding: 0.5rem 0.8rem;
            border-radius: var(--border-radius);
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
        }

        .btn-edit {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .btn-edit:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-delete {
            background-color: #ffebee;
            color: var(--danger-color);
        }

        .btn-delete:hover {
            background-color: var(--danger-color);
            color: white;
        }

        .edit-form {
            display: none;
            padding: 1.5rem;
        }

        .edit-form h3 {
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            font-size: 1.3rem;
        }

        .no-staff {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-light);
            font-style: italic;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4caf50;
        }

        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border-left: 4px solid #f44336;
        }

        .alert i {
            font-size: 1.2rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header-buttons {
                width: 100%;
                justify-content: flex-start;
                margin-top: 1rem;
            }
            
            .staff-details {
                flex-direction: column;
            }
            
            .staff-actions {
                margin-top: 1rem;
                justify-content: flex-start;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <!-- Header -->
            <div class="header">
                <h1><i class="fas fa-user-md"></i> Staff Management</h1>
                <div class="header-buttons">
                    <button onclick="openHistoryModal()" class="btn btn-light">
                        <i class="fas fa-history"></i>
                        View Staff Directory
                    </button>
                    <a href="dashboard admin.php" class="btn btn-light" id="backToDashboard">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Notification area -->
            <?php if (isset($message)): ?>
                <div class="alert alert-<?= $message['type'] ?>">
                    <i class="fas fa-<?= $message['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <span><?= $message['text'] ?></span>
                </div>
            <?php endif; ?>

            <!-- Add Staff Form -->
            <form id="staffForm" method="POST" class="form">
                <!-- Personal Information -->
                <div class="form-section">
                    <h2><i class="fas fa-id-card"></i> Personal Information</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="lastName" required>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" required>
                                <option value="">Select Role</option>
                                <?php foreach ($roleOptions as $value => $label): ?>
                                    <option value="<?= $value ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Department</label>
                            <select name="department" required>
                                <option value="">Select Department</option>
                                <?php foreach ($departmentOptions as $value => $label): ?>
                                    <option value="<?= $value ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="form-section">
                    <h2><i class="fas fa-address-book"></i> Contact Information</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" required pattern="[0-9]{10}" placeholder="10 digits (e.g., 1234567890)">
                        </div>
                        <div class="form-group full-width">
                            <label>Address</label>
                            <input type="text" name="address" required>
                        </div>
                    </div>
                </div>

                <!-- Employment Details -->
                <div class="form-section">
                    <h2><i class="fas fa-briefcase"></i> Employment Details</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="startDate" required>
                        </div>
                        <div class="form-group">
                            <label>Qualifications</label>
                            <textarea name="qualifications" placeholder="Enter professional qualifications, certifications, etc."></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn btn-outline">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Add Staff Member
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Staff Directory Modal -->
    <div id="historyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-users"></i> Staff Directory</h2>
                <button onclick="closeHistoryModal()" class="btn-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Search Input -->
            <div class="search-container" id="searchInput">
                <input type="text" placeholder="Search by name, role, or department..." class="search-input" id="staffSearch">
            </div>

            <!-- Staff List -->
            <div id="staffList" class="staff-list">
                <?php if (!empty($staffMembers)): ?>
                    <?php foreach ($staffMembers as $staff): ?>
                        <div class="staff-card" data-search="<?= strtolower($staff['first_name'] . ' ' . $staff['last_name'] . ' ' . $staff['role'] . ' ' . $staff['department']) ?>">
                            <div class="staff-details">
                                <div class="staff-info">
                                    <h3><?= $staff['first_name'] ?> <?= $staff['last_name'] ?></h3>
                                    <p><strong>Role:</strong> <?= ucfirst($staff['role']) ?></p>
                                    <p><strong>Department:</strong> <?= ucfirst($staff['department']) ?></p>
                                    <p><strong>Email:</strong> <?= $staff['email'] ?></p>
                                    <p><strong>Phone:</strong> <?= $staff['phone'] ?></p>
                                    <p><strong>Join Date:</strong> <?= date('M d, Y', strtotime($staff['start_date'])) ?></p>
                                    <p><strong>Address:</strong> <?= $staff['address'] ?></p>
                                </div>
                                <div class="staff-actions">
                                    <button onclick="editStaff('<?= $staff['first_name'] ?>')" class="btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button onclick="removeStaff('<?= $staff['first_name'] ?>')" class="btn-delete">
                                        <i class="fas fa-trash-alt"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-staff">No staff members found. Add your first staff member using the form.</p>
                <?php endif; ?>
            </div>

            <!-- Edit Staff Form (Initially hidden) -->
            <div id="editStaffForm" class="edit-form">
                <h3><i class="fas fa-user-edit"></i> Edit Staff Member</h3>
                <form id="editStaff" method="POST">
                    <!-- Personal Information -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="firstName" required id="editFirstName" readonly>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="lastName" required id="editLastName">
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" required id="editRole">
                                <?php foreach ($roleOptions as $value => $label): ?>
                                    <option value="<?= $value ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Department</label>
                            <select name="department" required id="editDepartment">
                                <?php foreach ($departmentOptions as $value => $label): ?>
                                    <option value="<?= $value ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <!-- Contact Information -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" required id="editEmail">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" required id="editPhone" pattern="[0-9]{10}">
                        </div>
                        <div class="form-group full-width">
                            <label>Address</label>
                            <input type="text" name="address" required id="editAddress">
                        </div>
                    </div>
                    <!-- Employment Details -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="startDate" required id="editStartDate">
                        </div>
                        <div class="form-group">
                            <label>Qualifications</label>
                            <textarea name="qualifications" id="editQualifications"></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" onclick="backToStaffList()">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </button>
                        <button type="submit" name="update" value="1" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Staff Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        // DOM elements
const historyModal = document.getElementById('historyModal');
const editStaffForm = document.getElementById('editStaffForm');
const staffList = document.getElementById('staffList');
const staffSearch = document.getElementById('staffSearch');

// Open the staff directory modal
function openHistoryModal() {
    historyModal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent scrolling of background
}

// Close the staff directory modal
function closeHistoryModal() {
    historyModal.style.display = 'none';
    document.body.style.overflow = 'auto'; // Re-enable scrolling
    
    // Reset the edit form view
    editStaffForm.style.display = 'none';
    staffList.style.display = 'block';
}

// Filter staff list based on search input
if (staffSearch) {
    staffSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const staffCards = document.querySelectorAll('.staff-card');
        
        staffCards.forEach(card => {
            const searchData = card.getAttribute('data-search').toLowerCase();
            if (searchData.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// Handle staff member editing
function editStaff(firstName) {
    // Hide the staff list and show the edit form
    staffList.style.display = 'none';
    editStaffForm.style.display = 'block';
    
    // Find the staff member's data
    const staffCards = document.querySelectorAll('.staff-card');
    let staffData = null;
    
    staffCards.forEach(card => {
        const nameElement = card.querySelector('.staff-info h3');
        if (nameElement && nameElement.textContent.includes(firstName)) {
            staffData = {
                fullName: nameElement.textContent.trim(),
                role: card.querySelector('.staff-info p:nth-child(2)').textContent.replace('Role:', '').trim().toLowerCase(),
                department: card.querySelector('.staff-info p:nth-child(3)').textContent.replace('Department:', '').trim().toLowerCase(),
                email: card.querySelector('.staff-info p:nth-child(4)').textContent.replace('Email:', '').trim(),
                phone: card.querySelector('.staff-info p:nth-child(5)').textContent.replace('Phone:', '').trim(),
                address: card.querySelector('.staff-info p:nth-child(5)').textContent.replace('Address:', '').trim(),
                startDate: card.querySelector('.staff-info p:nth-child(6)').textContent.replace('Join Date:', '').trim()
            };
        }
    });
    
    if (staffData) {
        // Split full name into first and last name
        const nameParts = staffData.fullName.split(' ');
        const firstName = nameParts[0];
        const lastName = nameParts.slice(1).join(' ');
        
        // Set form fields
        document.getElementById('editFirstName').value = firstName;
        document.getElementById('editLastName').value = lastName;
        document.getElementById('editRole').value = staffData.role;
        document.getElementById('editDepartment').value = staffData.department;
        document.getElementById('editEmail').value = staffData.email;
        document.getElementById('editPhone').value = staffData.phone.replace(/\D/g, ''); // Remove non-digit characters
        document.getElementById('editAddress').value = staffData.address;

        // Convert date format for the date input (MM dd, YYYY to YYYY-MM-DD)
        try {
            const dateComponents = staffData.startDate.split(' ');
            const months = {
                'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04', 'May': '05', 'Jun': '06',
                'Jul': '07', 'Aug': '08', 'Sep': '09', 'Oct': '10', 'Nov': '11', 'Dec': '12'
            };
            const month = months[dateComponents[0]];
            const day = dateComponents[1].replace(',', '').padStart(2, '0');
            const year = dateComponents[2];
            document.getElementById('editStartDate').value = `${year}-${month}-${day}`;
        } catch (e) {
            console.error('Error parsing date:', e);
            document.getElementById('editStartDate').value = '';
        }
        
        // Try to find and set qualifications if available
        // Note: This assumes qualifications are stored in a data attribute or another element
        // that wasn't visible in the code snippet. You may need to adjust this part.
        const qualificationsEl = document.getElementById('editQualifications');
        if (qualificationsEl) {
            // Default to empty if not found
            qualificationsEl.value = '';
        }
    }
}

// Return to staff list from edit form
function backToStaffList() {
    editStaffForm.style.display = 'none';
    staffList.style.display = 'block';
}

// Handle staff member removal with confirmation
function removeStaff(firstName) {
    if (confirm(`Are you sure you want to remove ${firstName} from the staff list?`)) {
        // Send AJAX request to remove the staff member
        fetch(`?remove_first_name=${encodeURIComponent(firstName)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Find and remove the staff card from the DOM
                    const staffCards = document.querySelectorAll('.staff-card');
                    staffCards.forEach(card => {
                        const nameElement = card.querySelector('.staff-info h3');
                        if (nameElement && nameElement.textContent.includes(firstName)) {
                            card.remove();
                        }
                    });
                    
                    // Show success message
                    alert(`${firstName} has been removed from the staff list.`);
                } else {
                    // Show error message
                    alert(`Error: ${data.error || 'Could not remove staff member.'}`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while trying to remove the staff member.');
            });
    }
}

// Close modal when clicking outside of modal content
window.addEventListener('click', function(event) {
    if (event.target === historyModal) {
        closeHistoryModal();
    }
});

// Add event listener for form submission to prevent duplicate submissions
document.addEventListener('DOMContentLoaded', function() {
    const staffForm = document.getElementById('staffForm');
    if (staffForm) {
        staffForm.addEventListener('submit', function() {
            // Disable the submit button after clicking
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
            }
        });
    }
    
    // Handle URL parameters for displaying messages
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('message')) {
        const messageType = urlParams.get('message');
        if (messageType === 'added' || messageType === 'updated') {
            // Scroll to the top to ensure the message is visible
            window.scrollTo(0, 0);
        }
    }
});
    </script>
   </body>
    </html>