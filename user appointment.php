<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Hard-coded user email for demonstration (replace with actual logic in production)
$userEmail = "rahul08@gmail.com"; // This would normally come from your authentication system

// Get user details from the database
$userQuery = "SELECT full_name, email, phone FROM users WHERE email = ?";
$stmt = $conn->prepare($userQuery);

$stmt->bind_param("s", $userEmail);
$stmt->execute();
$userResult = $stmt->get_result();
$userData = $userResult->fetch_assoc();
$stmt->close();

// If user data is not found, initialize it with empty values to prevent null errors
if (!$userData) {
    $userData = [
        'full_name' => Rahul, // Use email as fallback name
        'email' => $userEmail,
        'phone' => ''
    ];
}

// Handle new appointment insertion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointmentDate'])) {
    $patientName = $userData['full_name']; // Use the specific user's name
    $patientEmail = $userData['email']; // Use the specific user's email
    $appointmentDate = $_POST['appointmentDate'];
    $appointmentTime = $_POST['appointmentTime'];
    $doctor = $_POST['doctor'];

    $stmt = $conn->prepare("INSERT INTO appointments (patient_name, patient_email, appointment_date, appointment_time, doctor, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("sssss", $patientName, $patientEmail, $appointmentDate, $appointmentTime, $doctor);

    if ($stmt->execute()) {
        // Set a success message for display
        $successMessage = "Your appointment has been scheduled successfully!";
    } else {
        $errorMessage = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch upcoming appointments for this specific user
$sql = "SELECT * FROM appointments WHERE patient_email = ? ORDER BY appointment_date ASC, appointment_time ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

$upcomingAppointments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $upcomingAppointments[] = $row;
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Appointment - Eye Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #eef2ff;
            --secondary-color: #2a2d43;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-gray: #f3f4f6;
            --white: #ffffff;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --border-radius: 0.5rem;
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1f2937;
            line-height: 1.6;
        }
        
        .app-container {
            max-width: 1280px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: none;
            margin-bottom: 2rem;
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid #e5e7eb;
            padding: 1.5rem;
        }
        
        .page-title {
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 0;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 0.375rem;
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            transition: var(--transition);
        }
        
        .btn-primary:hover {
            background-color: #3651d4;
            border-color: #3651d4;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background-color: transparent;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: var(--white);
        }
        
        .form-control, .form-select {
            padding: 0.625rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            transition: var(--transition);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #4b5563;
        }
        
        .form-section {
            background-color: var(--light-gray);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .section-title {
            font-weight: 600;
            margin-bottom: 1.25rem;
            color: var(--secondary-color);
        }
        
        .appointment-card {
            border: 1px solid #e5e7eb;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            background-color: var(--white);
            transition: var(--transition);
        }
        
        .appointment-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }
        
        .text-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        
        .text-value {
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 1rem;
        }
        
        .icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        
        .icon-btn i {
            margin-right: 0.5rem;
        }
        
        /* Status pill styles */
        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 500;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-complete {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-cancelled {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        
        /* Alert styles */
        .alert {
            border-radius: var(--border-radius);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        
        /* User info display */
        .user-info {
            background-color: var(--primary-light);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .user-info-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .user-info-item i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .app-container {
                margin: 1rem auto;
            }
            
            .card-header {
                padding: 1.25rem;
            }
            
            .page-title {
                font-size: 1.25rem;
            }
            
            .form-section {
                padding: 1.25rem;
            }
            
            .appointment-card {
                padding: 1.25rem;
            }
        }
        
        @media (max-width: 576px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .header-actions {
                margin-top: 1rem;
                width: 100%;
            }
            
            .btn-toolbar {
                width: 100%;
                justify-content: space-between;
            }
        }
        
        /* Animation effects */
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="card fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h1 class="page-title">
                    <i class="fas fa-calendar-plus me-2"></i>
                    Schedule an Appointment
                </h1>
                <div class="header-actions">
                    <div class="btn-toolbar">
                        <button id="viewAppointmentsBtn" class="btn btn-outline-primary icon-btn me-2">
                            <i class="fas fa-calendar"></i> My Appointments
                        </button>
                        <a href="dashboard user.php" class="btn btn-outline-primary icon-btn">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-4">
                <?php if (isset($successMessage)): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $successMessage; ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($errorMessage)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $errorMessage; ?>
                </div>
                <?php endif; ?>
                
                <div class="form-section mb-4">
                    <h2 class="section-title">
                        <i class="fas fa-user me-2"></i>
                        Your Information
                    </h2>
                    
                    <div class="user-info">
                        <div class="user-info-item">
                            <i class="fas fa-user-circle"></i>
                            <strong>Name:</strong>&nbsp;<?php echo $userData['full_name']; ?>
                        </div>
                        <div class="user-info-item">
                            <i class="fas fa-envelope"></i>
                            <strong>Email:</strong>&nbsp;<?php echo $userData['email']; ?>
                        </div>
                        <?php if (isset($userData['phone']) && !empty($userData['phone'])): ?>
                        <div class="user-info-item">
                            <i class="fas fa-phone"></i>
                            <strong>Phone:</strong>&nbsp;<?php echo $userData['phone']; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <p class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Your appointment will be created using your account information.
                    </p>
                </div>
                
                <form method="POST" id="appointmentForm">
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Appointment Details
                        </h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointmentDate" class="form-label">
                                        <i class="fas fa-calendar me-1"></i> Preferred Date
                                    </label>
                                    <input type="date" class="form-control" id="appointmentDate" name="appointmentDate" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointmentTime" class="form-label">
                                        <i class="fas fa-clock me-1"></i> Preferred Time
                                    </label>
                                    <input type="time" class="form-control" id="appointmentTime" name="appointmentTime" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="doctor" class="form-label">
                                        <i class="fas fa-user-md me-1"></i> Preferred Doctor
                                    </label>
                                    <select class="form-select" id="doctor" name="doctor" required>
                                        <option value="" disabled selected>Select a Doctor</option>
                                        <option value="Dr. Gajanan Pendkar">Dr. Gajanan Pendkar - Ophthalmologist</option>
                                        <option value="Dr. Seema Mane">Dr. Seema Mane - Optometrist</option>
                                        <option value="Dr. Ajit kumar">Dr. Ajit kumar - Eye Surgeon</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="reset" class="btn btn-light me-2">
                            <i class="fas fa-times me-1"></i> Clear Form
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-check me-1"></i> Schedule Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- My Appointments Modal -->
        <div class="modal fade" id="appointmentsModal" tabindex="-1" aria-labelledby="appointmentsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="appointmentsModalLabel">
                            <i class="fas fa-calendar me-2"></i>
                            My Upcoming Appointments
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php if (count($upcomingAppointments) > 0): ?>
                            <?php foreach ($upcomingAppointments as $appointment): ?>
                                <div class="appointment-card">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="mb-3">
                                                <div class="text-label">
                                                    <i class="fas fa-calendar-alt me-1"></i> Date & Time
                                                </div>
                                                <div class="text-value">
                                                    <?php 
                                                    if (isset($appointment['appointment_date']) && $appointment['appointment_date']) {
                                                        echo date('F d, Y', strtotime($appointment['appointment_date'])); 
                                                    } else {
                                                        echo "No date specified";
                                                    }
                                                    ?> at 
                                                    <?php 
                                                    if (isset($appointment['appointment_time']) && $appointment['appointment_time']) {
                                                        echo date('g:i A', strtotime($appointment['appointment_time'])); 
                                                    } else {
                                                        echo "No time specified";
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="text-label">
                                                    <i class="fas fa-user-md me-1"></i> Doctor
                                                </div>
                                                <div class="text-value">
                                                    <?php 
                                                    if (isset($appointment['doctor']) && $appointment['doctor']) {
                                                        echo ucwords(str_replace('_', ' ', $appointment['doctor']));
                                                    } else {
                                                        echo "No doctor specified";
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-5">
                                            <div class="mb-3">
                                                <div class="text-label">Status</div>
                                                <div class="mt-2">
                                                    <span class="status-pill 
                                                        <?php
                                                            if (isset($appointment['status'])) {
                                                                switch($appointment['status']) {
                                                                    case 'complete':
                                                                        echo 'status-complete';
                                                                        break;
                                                                    case 'pending':
                                                                        echo 'status-pending';
                                                                        break;
                                                                    case 'cancelled':
                                                                        echo 'status-cancelled';
                                                                        break;
                                                                    default:
                                                                        echo 'status-pending';
                                                                }
                                                            } else {
                                                                echo 'status-pending';
                                                            }
                                                        ?>">
                                                        <i class="fas 
                                                            <?php
                                                                if (isset($appointment['status'])) {
                                                                    switch($appointment['status']) {
                                                                        case 'complete':
                                                                            echo 'fa-check-circle';
                                                                            break;
                                                                        case 'pending':
                                                                            echo 'fa-clock';
                                                                            break;
                                                                        case 'cancelled':
                                                                            echo 'fa-times-circle';
                                                                            break;
                                                                        default:
                                                                            echo 'fa-clock';
                                                                    }
                                                                } else {
                                                                    echo 'fa-clock';
                                                                }
                                                            ?> me-1"></i>
                                                        <span class="capitalize">
                                                            <?php echo isset($appointment['status']) ? $appointment['status'] : 'pending'; ?>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
                                </div>
                                <h5>No upcoming appointments</h5>
                                <p class="text-muted">Schedule your first appointment to get started.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap components
        const appointmentsModal = new bootstrap.Modal(document.getElementById('appointmentsModal'));
        
        // Set minimum date for appointment date picker (today)
        const appointmentDateInput = document.getElementById('appointmentDate');
        if (appointmentDateInput) {
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            appointmentDateInput.setAttribute('min', formattedDate);
        }
        
        // Open appointments modal button
        document.getElementById('viewAppointmentsBtn').addEventListener('click', function() {
            appointmentsModal.show();
        });
        
        // Form validation
        const appointmentForm = document.getElementById('appointmentForm');
        if (appointmentForm) {
            appointmentForm.addEventListener('submit', function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                this.classList.add('was-validated');
            });
        }
        
        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        if (alerts.length > 0) {
            setTimeout(function() {
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        }
    });
    </script>
</body>
</html>