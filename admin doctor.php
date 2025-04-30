<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Start the session

// Database connection
$servername = "localhost";  // your database host, e.g., localhost
$username = "root";         // your database username
$password = "";             // your database password
$dbname = "eye_clinic";     // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ""; // Variable to store the success/error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize the form inputs
    $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
    $licenseNumber = mysqli_real_escape_string($conn, $_POST['licenseNumber']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phoneNumber = mysqli_real_escape_string($conn, $_POST['phoneNumber']);
    $startTime = mysqli_real_escape_string($conn, $_POST['startTime']); // Start time of availability
    $endTime = mysqli_real_escape_string($conn, $_POST['endTime']); // End time of availability
    $availability = $startTime . ' - ' . $endTime; // Combine start and end times

    // Validate email
    if (strpos($email, '@') === false) {
        $_SESSION['message'] = "Invalid email address. The email must contain an \"@\" symbol.";
        $_SESSION['message_type'] = "error";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Validate phone number (should be exactly 10 digits)
    if (!preg_match('/^\d{10}$/', $phoneNumber)) {
        $_SESSION['message'] = "Invalid phone number. Phone number must be exactly 10 digits.";
        $_SESSION['message_type'] = "error";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        // Update the doctor details based on the first name (edit logic)
        $updateSql = "UPDATE doctors SET 
                first_name = '$firstName', 
                last_name = '$lastName',
                specialization = '$specialization',
                license_number = '$licenseNumber', 
                email = '$email', 
                phone_number = '$phoneNumber',
                availability = '$availability'
              WHERE first_name = '$firstName'";

        if (mysqli_query($conn, $updateSql)) {
            // Set success message
            $_SESSION['message'] = "Doctor information updated successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            // Set error message
            $_SESSION['message'] = "Error: " . mysqli_error($conn);
            $_SESSION['message_type'] = "error";
        }
    } else {
        // Add new doctor logic
        $checkEmailSql = "SELECT * FROM doctors WHERE email = '$email'";
        $emailResult = mysqli_query($conn, $checkEmailSql);

        if (mysqli_num_rows($emailResult) > 0) {
            // If the email exists, set an error message
            $_SESSION['message'] = "Error: Email already exists. Please use a unique email.";
            $_SESSION['message_type'] = "error";
        } else {
            // Check if the license number already exists
            $checkLicenseSql = "SELECT * FROM doctors WHERE license_number = '$licenseNumber'";
            $licenseResult = mysqli_query($conn, $checkLicenseSql);

            if (mysqli_num_rows($licenseResult) > 0) {
                // If the license number exists, set an error message
                $_SESSION['message'] = "Error: License number already exists. Please use a unique license number.";
                $_SESSION['message_type'] = "error";
            } else {
                // Insert the new doctor logic
                $sql = "INSERT INTO doctors (first_name, last_name, specialization, license_number, email, phone_number, availability) 
                        VALUES ('$firstName', '$lastName', '$specialization', '$licenseNumber', '$email', '$phoneNumber', '$availability')";

                if (mysqli_query($conn, $sql)) {
                    // Set success message
                    $_SESSION['message'] = "New doctor added successfully.";
                    $_SESSION['message_type'] = "success";
                } else {
                    // Set error message
                    $_SESSION['message'] = "Error: " . mysqli_error($conn);
                    $_SESSION['message_type'] = "error";
                }
            }
        }
    }

    // Redirect to the same page to show the message
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Remove doctor logic
if (isset($_GET['remove_id'])) {
    $doctorFirstName = $_GET['remove_id'];
    $removeSql = "DELETE FROM doctors WHERE first_name = '$doctorFirstName'";

    if (mysqli_query($conn, $removeSql)) {
        $_SESSION['message'] = "Doctor removed successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: " . mysqli_error($conn);
        $_SESSION['message_type'] = "error";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch doctors data for the list
$doctorData = [];
$sql = "SELECT first_name, last_name, specialization, license_number, email, phone_number, availability FROM doctors";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $doctorData[] = $row;
    }
}

// Close the connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Management - Eye Clinic Administration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation Bar -->
    <nav class="bg-blue-600 text-white px-6 py-4 shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-eye text-xl"></i>
                <span class="text-xl font-semibold">Eye Clinic Admin</span>
            </div>
            <a href="dashboard admin.php" class="flex items-center space-x-2 hover:bg-blue-700 px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Doctor Management</h1>
            <div class="flex space-x-4">
                <button onclick="toggleView('formView')" id="formViewBtn" class="flex items-center text-blue-600 hover:text-blue-800 px-4 py-2 border border-blue-600 rounded-lg hover:bg-blue-50 transition">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Add Doctor
                </button>
                <button onclick="toggleView('listView')" id="listViewBtn" class="flex items-center text-blue-600 hover:text-blue-800 px-4 py-2 border border-blue-600 rounded-lg hover:bg-blue-50 transition">
                    <i class="fas fa-list mr-2"></i>
                    View Doctors
                </button>
            </div>
        </div>

        <!-- Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="p-4 mb-6 <?php echo ($_SESSION['message_type'] == 'success') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?> rounded-md flex items-center">
                <i class="<?php echo ($_SESSION['message_type'] == 'success') ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'; ?> mr-2"></i>
                <?= $_SESSION['message']; ?>
                <button onclick="this.parentElement.style.display='none'" class="ml-auto">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Add Doctor Form View -->
        <div id="formView" class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-user-md mr-2 text-blue-600"></i> Add New Doctor
            </h2>

            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="space-y-6" id="addDoctorForm">
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700 flex items-center">
                        <i class="fas fa-id-card mr-2 text-blue-500"></i> Personal Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" name="firstName" required class="w-full pl-10 p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" name="lastName" required class="w-full pl-10 p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Specialization <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-stethoscope text-gray-400"></i>
                                </div>
                                <input type="text" name="specialization" required class="w-full pl-10 p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">License Number <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-id-badge text-gray-400"></i>
                                </div>
                                <input type="text" name="licenseNumber" required class="w-full pl-10 p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700 flex items-center">
                        <i class="fas fa-address-book mr-2 text-blue-500"></i> Contact Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" name="email" required class="w-full pl-10 p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Must contain @ symbol</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input type="tel" name="phoneNumber" required class="w-full pl-10 p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Must be 10 digits</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700 flex items-center">
                        <i class="fas fa-clock mr-2 text-blue-500"></i> Availability
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Time <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-hourglass-start text-gray-400"></i>
                                </div>
                                <input type="time" name="startTime" required class="w-full pl-10 p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Time <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-hourglass-end text-gray-400"></i>
                                </div>
                                <input type="time" name="endTime" required class="w-full pl-10 p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="action" value="add">

                <div class="flex justify-end space-x-4">
                    <button type="reset" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-undo mr-2"></i> Reset
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                        <i class="fas fa-save mr-2"></i> Add Doctor
                    </button>
                </div>
            </form>
        </div>

        <!-- Doctor List View -->
        <div id="listView" class="bg-white rounded-lg shadow-md p-6 hidden">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-user-md mr-2 text-blue-600"></i> Doctor Directory
            </h2>

            <!-- Search Input -->
            <div class="mb-6 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" placeholder="Search doctors by name, specialization..." 
                       class="w-full pl-10 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Doctor List -->
            <?php if (empty($doctorData)): ?>
            <div class="text-center py-8">
                <i class="fas fa-user-md text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500">No doctors found. Add your first doctor using the form.</p>
            </div>
            <?php else: ?>
            <div class="overflow-auto rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialization</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Availability</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="doctorTableBody">
                        <?php foreach ($doctorData as $doctor): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user-md text-blue-500"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo $doctor['first_name'] . ' ' . $doctor['last_name']; ?></div>
                                        <div class="text-sm text-gray-500">License: <?php echo $doctor['license_number']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?php echo $doctor['specialization']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><i class="fas fa-envelope mr-1 text-gray-400"></i> <?php echo $doctor['email']; ?></div>
                                <div class="text-sm text-gray-500"><i class="fas fa-phone mr-1 text-gray-400"></i> <?php echo $doctor['phone_number']; ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <i class="fas fa-clock mr-1 text-gray-400"></i> <?php echo $doctor['availability']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <button onclick="editDoctor('<?php echo $doctor['first_name']; ?>')" class="text-blue-600 hover:text-blue-900 mx-1">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="confirmRemoveDoctor('<?php echo $doctor['first_name']; ?>')" class="text-red-600 hover:text-red-900 mx-1">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Doctor Modal -->
    <div id="editDoctorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl p-6 max-h-[90vh] overflow-auto">
            <div class="flex justify-between items-center mb-6 border-b pb-3">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-user-edit mr-2 text-blue-600"></i> Edit Doctor
                </h2>
                <button onclick="closeEditDoctorModal()" class="text-gray-500 hover:text-gray-700 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Edit Form -->
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="space-y-6" id="editDoctorForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="editFirstName" name="firstName">
                
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Doctor Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" name="firstName" id="editFirstNameInput" required class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100" readonly>
                            <p class="text-xs text-gray-500 mt-1">First name cannot be changed</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" name="lastName" id="editLastName" required class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Specialization</label>
                            <input type="text" name="specialization" id="editSpecialization" required class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">License Number</label>
                            <input type="text" name="licenseNumber" id="editLicenseNumber" required class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="editEmail" required class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phoneNumber" id="editPhoneNumber" required class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Availability (Start)</label>
                            <input type="time" name="startTime" id="editStartTime" required class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Availability (End)</label>
                            <input type="time" name="endTime" id="editEndTime" required class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-4 border-t">
                    <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition" onclick="closeEditDoctorModal()">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-save mr-2"></i> Update Doctor
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Confirm Deletion</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to remove this doctor? This action cannot be undone.</p>
                <div class="flex justify-center space-x-4">
                    <button type="button" onclick="closeConfirmModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button type="button" id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Yes, Remove
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to toggle between form and list views
function toggleView(viewToShow) {
    const formView = document.getElementById('formView');
    const listView = document.getElementById('listView');
    const formViewBtn = document.getElementById('formViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    
    if (viewToShow === 'formView') {
        formView.classList.remove('hidden');
        listView.classList.add('hidden');
        formViewBtn.classList.add('bg-blue-100');
        listViewBtn.classList.remove('bg-blue-100');
    } else {
        formView.classList.add('hidden');
        listView.classList.remove('hidden');
        formViewBtn.classList.remove('bg-blue-100');
        listViewBtn.classList.add('bg-blue-100');
    }
}

// Doctor search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#doctorTableBody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Set default view - If URL has query parameter 'view=list', show list view
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('view') === 'list') {
        toggleView('listView');
    }
});

// Edit doctor functionality
function editDoctor(firstName) {
    const rows = document.querySelectorAll('#doctorTableBody tr');
    
    rows.forEach(row => {
        if (row.querySelector('.text-sm.font-medium').textContent.startsWith(firstName + ' ')) {
            // Get doctor details from the row
            const fullName = row.querySelector('.text-sm.font-medium').textContent.split(' ');
            const lastName = fullName.slice(1).join(' ');
            const licenseNumber = row.querySelector('.text-sm.text-gray-500').textContent.replace('License: ', '');
            const specialization = row.querySelector('.px-2.inline-flex').textContent.trim();
            const email = row.querySelectorAll('.text-sm.text-gray-900')[0].textContent.replace(/^\s*\S+\s*/, '').trim();
            const phone = row.querySelectorAll('.text-sm.text-gray-500')[1].textContent.replace(/^\s*\S+\s*/, '').trim();
            const availability = row.querySelector('.whitespace-nowrap.text-sm.text-gray-500').textContent.trim().replace(/^\s*\S+\s*/, '').trim();
            
            // Parse availability times
            let startTime = '';
            let endTime = '';
            if (availability.includes('-')) {
                const times = availability.split('-');
                startTime = times[0].trim();
                endTime = times[1].trim();
            }
            
            // Populate form fields
            document.getElementById('editFirstName').value = firstName;
            document.getElementById('editFirstNameInput').value = firstName;
            document.getElementById('editLastName').value = lastName;
            document.getElementById('editSpecialization').value = specialization;
            document.getElementById('editLicenseNumber').value = licenseNumber;
            document.getElementById('editEmail').value = email;
            document.getElementById('editPhoneNumber').value = phone;
            document.getElementById('editStartTime').value = convertTimeStringToInputTime(startTime);
            document.getElementById('editEndTime').value = convertTimeStringToInputTime(endTime);
            
            // Show the modal
            document.getElementById('editDoctorModal').classList.remove('hidden');
        }
    });
}

// Helper function to convert time string (like "09:00 AM") to time input value ("09:00")
function convertTimeStringToInputTime(timeString) {
    if (!timeString) return '';
    
    let hours = 0;
    let minutes = 0;
    
    // Handle different time formats
    if (timeString.includes(':')) {
        // Format like "09:00 AM" or "09:00"
        const parts = timeString.match(/(\d+):(\d+)(?:\s*([AP]M))?/i);
        if (parts) {
            hours = parseInt(parts[1]);
            minutes = parseInt(parts[2]);
            const meridian = parts[3] ? parts[3].toUpperCase() : null;
            
            if (meridian === 'PM' && hours < 12) hours += 12;
            if (meridian === 'AM' && hours === 12) hours = 0;
        }
    } else {
        // Simple format like "9 AM"
        const parts = timeString.match(/(\d+)(?:\s*([AP]M))?/i);
        if (parts) {
            hours = parseInt(parts[1]);
            const meridian = parts[2] ? parts[2].toUpperCase() : null;
            
            if (meridian === 'PM' && hours < 12) hours += 12;
            if (meridian === 'AM' && hours === 12) hours = 0;
        }
    }
    
    // Format hours and minutes to HH:MM
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
}

// Close edit doctor modal
function closeEditDoctorModal() {
    document.getElementById('editDoctorModal').classList.add('hidden');
}

// Delete confirmation functionality
let doctorToRemove = null;

function confirmRemoveDoctor(firstName) {
    doctorToRemove = firstName;
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    doctorToRemove = null;
}

// Setup the delete button event listener
document.addEventListener('DOMContentLoaded', function() {
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (doctorToRemove) {
                window.location.href = `${window.location.pathname}?remove_id=${encodeURIComponent(doctorToRemove)}`;
            }
        });
    }
    
    // Form validation
    const addDoctorForm = document.getElementById('addDoctorForm');
    if (addDoctorForm) {
        addDoctorForm.addEventListener('submit', function(event) {
            const phoneNumber = this.querySelector('input[name="phoneNumber"]').value;
            const email = this.querySelector('input[name="email"]').value;
            
            // Validate phone number
            if (!/^\d{10}$/.test(phoneNumber)) {
                event.preventDefault();
                alert('Phone number must be exactly 10 digits.');
                return false;
            }
            
            // Validate email
            if (!email.includes('@')) {
                event.preventDefault();
                alert('Email must contain an @ symbol.');
                return false;
            }
            
            return true;
        });
    }
    
    // Edit form validation
    const editDoctorForm = document.getElementById('editDoctorForm');
    if (editDoctorForm) {
        editDoctorForm.addEventListener('submit', function(event) {
            const phoneNumber = this.querySelector('#editPhoneNumber').value;
            const email = this.querySelector('#editEmail').value;
            
            // Validate phone number
            if (!/^\d{10}$/.test(phoneNumber)) {
                event.preventDefault();
                alert('Phone number must be exactly 10 digits.');
                return false;
            }
            
            // Validate email
            if (!email.includes('@')) {
                event.preventDefault();
                alert('Email must contain an @ symbol.');
                return false;
            }
            
            return true;
        });
    }
    
    // Handle responsive design for tables
    adjustTableForMobile();
    window.addEventListener('resize', adjustTableForMobile);
});

// Function to adjust table display on mobile devices
function adjustTableForMobile() {
    const table = document.querySelector('#doctorTableBody').closest('table');
    const windowWidth = window.innerWidth;
    
    if (windowWidth < 768) { // Mobile breakpoint
        table.classList.add('table-responsive');
        
        // Add data-label attributes to td elements if they don't exist
        const headerCells = table.querySelectorAll('thead th');
        const headerLabels = Array.from(headerCells).map(cell => cell.textContent.trim());
        
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, index) => {
                if (index < headerLabels.length && !cell.hasAttribute('data-label')) {
                    cell.setAttribute('data-label', headerLabels[index]);
                }
            });
        });
    } else {
        table.classList.remove('table-responsive');
    }
}

// Add styles for responsive tables
document.addEventListener('DOMContentLoaded', function() {
    // Add a style element for responsive tables
    const style = document.createElement('style');
    style.textContent = `
        @media (max-width: 767px) {
            .table-responsive thead {
                display: none;
            }
            
            .table-responsive tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #e5e7eb;
                border-radius: 0.5rem;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }
            
            .table-responsive td {
                display: block;
                text-align: right;
                position: relative;
                padding-left: 50% !important;
                border-bottom: 1px solid #e5e7eb;
            }
            
            .table-responsive td:last-child {
                border-bottom: none;
            }
            
            .table-responsive td:before {
                content: attr(data-label);
                position: absolute;
                left: 0.75rem;
                width: 45%;
                padding-right: 0.5rem;
                white-space: nowrap;
                text-align: left;
                font-weight: 600;
                color: #374151;
            }
            
            /* Make sure action buttons are centered on mobile */
            .table-responsive td:last-child {
                text-align: center;
                padding-left: 0.75rem !important;
            }
        }
    `;
    document.head.appendChild(style);
});
    </script>
    </body>
    </html>