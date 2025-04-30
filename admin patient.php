<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "eye_clinic";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Form Submission for Adding New Patient
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_patient'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $eye_conditions = $_POST['eye_conditions'];
    $allergies = $_POST['allergies'];

    // Validate email
    if (strpos($email, '@') === false) {
        echo "<script>alert('Invalid email address. The email must contain an \"@\" symbol.');</script>";
        return;
    }

    // Validate phone number (should be exactly 10 digits)
    if (!preg_match('/^\d{10}$/', $phone)) {
        echo "<script>alert('Invalid phone number. Phone number must be exactly 10 digits.');</script>";
        return;
    }

    $sql = "INSERT INTO patients (first_name, last_name, dob, gender, email, phone, address, eye_conditions, allergies) 
            VALUES ('$first_name', '$last_name', '$dob', '$gender', '$email', '$phone', '$address', '$eye_conditions', '$allergies')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Patient added successfully!'); window.location='admin patient.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Remove Patient by First Name
if (isset($_GET['remove_name'])) {
    $remove_name = $_GET['remove_name'];
    $sql = "DELETE FROM patients WHERE first_name = '$remove_name'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Patient removed successfully!'); window.location='admin patient.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle Edit Patient Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_patient'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $eye_conditions = $_POST['eye_conditions'];
    $allergies = $_POST['allergies'];

    // Validate email
    if (strpos($email, '@') === false) {
        echo "<script>alert('Invalid email address. The email must contain an \"@\" symbol.');</script>";
        return;
    }

    // Validate phone number (should be exactly 10 digits)
    if (!preg_match('/^\d{10}$/', $phone)) {
        echo "<script>alert('Invalid phone number. Phone number must be exactly 10 digits.');</script>";
        return;
    }

    $sql = "UPDATE patients SET 
            last_name = '$last_name', 
            dob = '$dob', 
            gender = '$gender', 
            email = '$email', 
            phone = '$phone', 
            address = '$address', 
            eye_conditions = '$eye_conditions', 
            allergies = '$allergies' 
            WHERE first_name = '$first_name'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Patient details updated successfully!'); window.location='admin patient.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch Patient History
$patients = [];
$result = $conn->query("SELECT * FROM patients ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $patients[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management - Eye Clinic Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Custom Animations */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        /* Customize scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #2563eb;
        }
        
        /* Form focus effects */
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
            transition: all 0.2s ease;
        }
        
        /* Card hover effects */
        .patient-card {
            transition: all 0.3s ease;
        }
        
        .patient-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Modal animation */
        .modal {
            transition: opacity 0.3s ease;
        }
        
        /* Button hover effects */
        .btn {
            transition: all 0.2s ease;
        }
        
        .btn:hover {
            transform: translateY(-1px);
        }
        
        /* Responsive adjustments */
        @media (max-width: 640px) {
            .responsive-form {
                padding: 1rem;
            }
            
            .grid-cols-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-eye-dropper text-2xl mr-3"></i>
                <h1 class="text-xl font-semibold">Eye Clinic Administration</h1>
            </div>
            <div class="flex items-center space-x-4">
                <a href="dashboard admin.php" class="flex items-center hover:text-blue-200 transition">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    <span class="hidden sm:inline">Dashboard</span>
                </a>
                <button id="historyBtn" class="flex items-center hover:text-blue-200 transition">
                    <i class="fas fa-history mr-2"></i>
                    <span class="hidden sm:inline">Patient History</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto p-4 md:p-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 bg-white p-4 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold text-gray-800 mb-2 md:mb-0">
                <i class="fas fa-user-plus text-blue-600 mr-2"></i>Patient Registration
            </h2>
          
        </div>

        <!-- Patient Form Card -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8 animate-fadeIn">
            <!-- Form Header with Tabs -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white">
                <h3 class="text-xl font-semibold">New Patient Registration</h3>
                <p class="text-blue-100 text-sm">Complete the form below to register a new patient</p>
            </div>
            
            <!-- Patient Registration Form -->
            <form action="" method="POST" class="responsive-form p-6">
                <!-- Personal Information -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-4 flex items-center text-gray-700">
                        <i class="fas fa-id-card text-blue-500 mr-2"></i>Personal Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" name="first_name" required 
                                   class="form-input w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" name="last_name" required 
                                   class="form-input w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date" name="dob" required 
                                   class="form-input w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <select name="gender" required 
                                    class="form-select w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-4 flex items-center text-gray-700">
                        <i class="fas fa-address-book text-blue-500 mr-2"></i>Contact Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" required 
                                   class="form-input w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone" required placeholder="10 digits" 
                                   class="form-input w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="address" required 
                                   class="form-input w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Medical History -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-4 flex items-center text-gray-700">
                        <i class="fas fa-notes-medical text-blue-500 mr-2"></i>Medical History
                    </h4>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Previous Eye Conditions</label>
                            <textarea name="eye_conditions" 
                                      class="form-textarea w-full p-2 border border-gray-300 rounded-lg focus:outline-none"
                                      rows="3" placeholder="List any previous eye conditions or 'None'"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Allergies</label>
                            <textarea name="allergies" 
                                      class="form-textarea w-full p-2 border border-gray-300 rounded-lg focus:outline-none"
                                      rows="3" placeholder="List any allergies or 'None'"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Submission -->
                <div class="flex justify-end">
                    <button type="submit" name="add_patient" 
                            class="btn px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm">
                        <i class="fas fa-user-plus mr-2"></i>Register Patient
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Patient History Modal -->
    <div id="historyModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-5xl h-3/4 flex flex-col animate-fadeIn">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white rounded-t-lg flex justify-between items-center">
                <h2 class="text-xl font-bold">
                    <i class="fas fa-history mr-2"></i>Patient Records
                </h2>
                <div class="flex items-center">
                    <div class="relative mr-4">
                        <input type="text" id="searchInput" placeholder="Search patients..." 
                               class="pl-8 pr-4 py-1 border border-blue-300 rounded-md bg-blue-50 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <i class="fas fa-search absolute left-2 top-2 text-blue-500"></i>
                    </div>
                    <button id="closeModalBtn" class="text-white hover:text-blue-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Content -->
            <div class="flex-1 overflow-auto p-4">
                <!-- Patient List -->
                <div id="patientList" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($patients as $patient): ?>
                        <div class="patient-item patient-card bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:bg-blue-50">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-lg text-blue-700">
                                    <?= $patient['first_name'] . ' ' . $patient['last_name'] ?>
                                </h3>
                                <div>
                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                                        <?= ucfirst($patient['gender']) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-2 mb-3 text-sm">
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                                    <span><?= $patient['dob'] ?></span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-phone mr-2 text-blue-500"></i>
                                    <span><?= $patient['phone'] ?></span>
                                </div>
                                <div class="flex items-center text-gray-600 col-span-2">
                                    <i class="fas fa-envelope mr-2 text-blue-500"></i>
                                    <span class="truncate"><?= $patient['email'] ?></span>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <div class="text-sm font-semibold text-gray-700">Medical Information:</div>
                                <div class="text-xs bg-gray-50 rounded p-2 mt-1">
                                    <div><strong>Eye Conditions:</strong> <?= $patient['eye_conditions'] ?: 'None' ?></div>
                                    <div><strong>Allergies:</strong> <?= $patient['allergies'] ?: 'None' ?></div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end gap-2 mt-3">
                                <button onclick="openEditForm('<?= $patient['first_name'] ?>', '<?= $patient['last_name'] ?>', '<?= $patient['dob'] ?>', '<?= $patient['gender'] ?>', '<?= $patient['email'] ?>', '<?= $patient['phone'] ?>', '<?= $patient['address'] ?>', '<?= $patient['eye_conditions'] ?>', '<?= $patient['allergies'] ?>')" 
                                        class="btn text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded">
                                    <i class="fas fa-edit mr-1"></i><span class="text-sm">Edit</span>
                                </button>
                                <button onclick="removePatient('<?= $patient['first_name'] ?>')" 
                                        class="btn text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1 rounded">
                                    <i class="fas fa-trash-alt mr-1"></i><span class="text-sm">Remove</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Edit Form (Initially hidden) -->
                <div id="editForm" class="hidden bg-white rounded-lg border border-blue-200 shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-blue-700">Edit Patient Information</h3>
                        <button id="backToListBtn" class="text-blue-600 hover:text-blue-800 flex items-center">
                            <i class="fas fa-arrow-left mr-1"></i> Back to List
                        </button>
                    </div>
                    
                    <form action="" method="POST" class="space-y-6" onsubmit="return validateEditForm()">
                        <!-- Personal Information -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="text-md font-semibold mb-3 text-blue-700">Personal Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                    <input type="text" name="first_name" id="edit_first_name" required 
                                           class="form-input w-full p-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                    <input type="text" name="last_name" id="edit_last_name" required 
                                           class="form-input w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                    <input type="date" name="dob" id="edit_dob" required 
                                           class="form-input w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                    <select name="gender" id="edit_gender" required 
                                            class="form-select w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="text-md font-semibold mb-3 text-blue-700">Contact Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" id="edit_email" required 
                                           class="form-input w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" name="phone" id="edit_phone" required 
                                           class="form-input w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <input type="text" name="address" id="edit_address" required 
                                           class="form-input w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Medical History -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="text-md font-semibold mb-3 text-blue-700">Medical History</h4>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Previous Eye Conditions</label>
                                    <textarea name="eye_conditions" id="edit_eye_conditions" 
                                              class="form-textarea w-full p-2 border border-gray-300 rounded-lg focus:outline-none" rows="2"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Allergies</label>
                                    <textarea name="allergies" id="edit_allergies" 
                                              class="form-textarea w-full p-2 border border-gray-300 rounded-lg focus:outline-none" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" name="edit_patient" 
                                    class="btn px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm">
                                <i class="fas fa-save mr-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification (Hidden by default) -->
    <div id="toastNotification" class="hidden fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="toastMessage">Operation completed successfully!</span>
    </div>

    <script>
        // Show/Hide Patient History Modal
        document.getElementById('historyBtn').addEventListener('click', function() {
            document.getElementById('historyModal').classList.remove('hidden');
            document.getElementById('searchInput').style.display = 'block';
        });
        
        document.getElementById('closeModalBtn').addEventListener('click', function() {
            closeModal();
        });
        
        // Function to close the modal
        function closeModal() {
            document.getElementById('historyModal').classList.add('hidden');
            document.getElementById('editForm').classList.add('hidden');
            document.getElementById('patientList').classList.remove('hidden');
        }
        
        // Function to open the edit form
        function openEditForm(first_name, last_name, dob, gender, email, phone, address, eye_conditions, allergies) {
            document.getElementById('patientList').classList.add('hidden');
            document.getElementById('editForm').classList.remove('hidden');
            
            // Fill the edit form with patient details
            document.getElementById('edit_first_name').value = first_name;
            document.getElementById('edit_last_name').value = last_name;
            document.getElementById('edit_dob').value = dob;
            document.getElementById('edit_gender').value = gender;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_phone').value = phone;
            document.getElementById('edit_address').value = address;
            document.getElementById('edit_eye_conditions').value = eye_conditions;
            document.getElementById('edit_allergies').value = allergies;
        }
        
        // Function to remove patient with confirmation
        function removePatient(first_name) {
            if (confirm("Are you sure you want to remove this patient? This action cannot be undone.")) {
                window.location.href = "admin patient.php?remove_name=" + first_name;
            }
        }
        
        // Back to list button
        document.getElementById('backToListBtn').addEventListener('click', function() {
            document.getElementById('editForm').classList.add('hidden');
            document.getElementById('patientList').classList.remove('hidden');
        });
        
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            let searchValue = this.value.toLowerCase();
            let patientItems = document.querySelectorAll('.patient-item');
            
            patientItems.forEach(function(item) {
                let patientText = item.textContent.toLowerCase();
                if (patientText.includes(searchValue)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // Edit Form Validation
        function validateEditForm() {
            var email = document.getElementById('edit_email').value;
            var phone = document.getElementById('edit_phone').value;
            
            // Validate email
            if (email.indexOf('@') === -1) {
                showToast('Invalid email address. The email must contain an "@" symbol.', 'error');
                return false;
            }
            
            // Validate phone number (should be exactly 10 digits)
            if (!/^\d{10}$/.test(phone)) {
                showToast('Invalid phone number. Phone number must be exactly 10 digits.', 'error');
                return false;
            }
            
            return true;
        }
        
        // Show toast notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const toastMessage = document.getElementById('toastMessage');
            
            toastMessage.textContent = message;
            
            // Set color based on type
            if (type === 'error') {
                toast.classList.remove('bg-green-500');
                toast.classList.add('bg-red-500');
            } else {
                toast.classList.remove('bg-red-500');
                toast.classList.add('bg-green-500');
            }
            
            // Show toast
            toast.classList.remove('hidden');
            
            // Hide after 3 seconds
            setTimeout(function() {
                toast.classList.add('hidden');
            }, 3000);
        }
        
        // Add event listeners for form inputs to apply custom styling
        const formInputs = document.querySelectorAll('.form-input, .form-select, .form-textarea');
        formInputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.classList.add('ring-2', 'ring-blue-200', 'border-blue-300');
            });
            
            input.addEventListener('blur', () => {
                input.classList.remove('ring-2', 'ring-blue-200', 'border-blue-300');
            });
        });
    </script>
</body>
</html>