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

// Initialize variables
$appointments = [];
$filterFirstName = '';
$totalAppointments = 0;
$sortField = 'appointment_date';
$sortOrder = 'DESC';

// Handle sorting
if (isset($_GET['sort'])) {
    $sortField = $_GET['sort'];
}
if (isset($_GET['order'])) {
    $sortOrder = $_GET['order'] == 'ASC' ? 'ASC' : 'DESC';
}

// Handle first name filtering
if (isset($_GET['firstName']) && !empty($_GET['firstName'])) {
    $filterFirstName = $_GET['firstName'];
    
    // Extract and count all appointments matching the first name filter
    $countSql = "SELECT COUNT(*) as total FROM appointments WHERE patient_name LIKE ?";
    $stmt = $conn->prepare($countSql);
    $searchParam = $filterFirstName . '%';
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $countResult = $stmt->get_result();
    $totalAppointments = $countResult->fetch_assoc()['total'];
    $stmt->close();
    
    // Get the filtered appointments
    $sql = "SELECT * FROM appointments WHERE patient_name LIKE ? ORDER BY $sortField $sortOrder";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
    }
    $stmt->close();
} else {
    // Count all appointments
    $countSql = "SELECT COUNT(*) as total FROM appointments";
    $countResult = $conn->query($countSql);
    $totalAppointments = $countResult->fetch_assoc()['total'];
    
    // Get all appointments
    $sql = "SELECT * FROM appointments ORDER BY $sortField $sortOrder";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
    }
}

// Handle appointment status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id']) && isset($_POST['status'])) {
    $appointmentId = $_POST['appointment_id'];
    $newStatus = $_POST['status'];
    
    $updateStmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $updateStmt->bind_param("si", $newStatus, $appointmentId);
    
    if ($updateStmt->execute()) {
        $statusUpdateMessage = "Appointment status updated successfully!";
        
        // Update the status in our current array for immediate display
        foreach ($appointments as $key => $appointment) {
            if ($appointment['id'] == $appointmentId) {
                $appointments[$key]['status'] = $newStatus;
                break;
            }
        }
    } else {
        $statusUpdateError = "Error updating status: " . $updateStmt->error;
    }
    $updateStmt->close();
}

// Get doctor names for display
function getDoctorName($doctorCode) {
    $doctorNames = [
        'dr_smith' => 'Dr. Smith - Ophthalmologist',
        'dr_jones' => 'Dr. Jones - Optometrist',
        'dr_williams' => 'Dr. Williams - Eye Surgeon'
    ];
    
    return isset($doctorNames[$doctorCode]) ? $doctorNames[$doctorCode] : ucwords(str_replace('_', ' ', $doctorCode));
}

// Function to get the first name from the full name
function getFirstName($fullName) {
    $nameParts = explode(' ', $fullName);
    return $nameParts[0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment History - Eye Clinic Admin</title>
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
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .admin-table th {
            background-color: var(--light-gray);
            padding: 0.75rem 1rem;
            font-weight: 600;
            text-align: left;
            color: var(--secondary-color);
            border-bottom: 2px solid #e5e7eb;
        }
        
        .admin-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        
        .admin-table tr:last-child td {
            border-bottom: none;
        }
        
        .admin-table tr:hover {
            background-color: var(--primary-light);
        }
        
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
        
        .filter-form {
            background-color: var(--light-gray);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }
        
        .summary-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .stat-card {
            background-color: var(--white);
            padding: 1rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            flex: 1;
            min-width: 200px;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
        
        .sort-icon {
            margin-left: 0.25rem;
        }
        
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
        }
        
        .pagination .page-link {
            color: var(--primary-color);
            border-color: #e5e7eb;
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .app-container {
                margin: 1rem auto;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .header-actions {
                margin-top: 1rem;
                width: 100%;
            }
            
            .summary-stats {
                flex-direction: column;
            }
            
            .stat-card {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h1 class="page-title">
                    <i class="fas fa-history me-2"></i>
                    Appointment History
                </h1>
                <div class="header-actions">
                    <a href="dashboard admin.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>
            
            <div class="card-body p-4">
                <?php if (isset($statusUpdateMessage)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $statusUpdateMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($statusUpdateError)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $statusUpdateError; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Filter Form -->
                <div class="filter-form">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Filter by First Name</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="firstName" name="firstName" 
                                       placeholder="Enter patient's first name" value="<?php echo htmlspecialchars($filterFirstName); ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="sort" class="form-label">Sort by</label>
                            <select class="form-select" id="sort" name="sort" onchange="this.form.submit()">
                                <option value="appointment_date" <?php echo $sortField == 'appointment_date' ? 'selected' : ''; ?>>Date</option>
                                <option value="patient_name" <?php echo $sortField == 'patient_name' ? 'selected' : ''; ?>>Patient Name</option>
                                <option value="status" <?php echo $sortField == 'status' ? 'selected' : ''; ?>>Status</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="order" class="form-label">Order</label>
                            <select class="form-select" id="order" name="order" onchange="this.form.submit()">
                                <option value="DESC" <?php echo $sortOrder == 'DESC' ? 'selected' : ''; ?>>Descending</option>
                                <option value="ASC" <?php echo $sortOrder == 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                            </select>
                        </div>
                    </form>
                </div>
                
                <!-- Summary Stats -->
                <div class="summary-stats">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $totalAppointments; ?></div>
                        <div class="stat-label">Total Appointments</div>
                    </div>
                    
                    <?php
                    // Count appointments by status
                    $statusCounts = [
                        'pending' => 0,
                        'complete' => 0,
                        'cancelled' => 0
                    ];
                    
                    foreach ($appointments as $appointment) {
                        if (isset($statusCounts[$appointment['status']])) {
                            $statusCounts[$appointment['status']]++;
                        }
                    }
                    ?>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $statusCounts['pending']; ?></div>
                        <div class="stat-label">Pending Appointments</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $statusCounts['complete']; ?></div>
                        <div class="stat-label">Completed Appointments</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $statusCounts['cancelled']; ?></div>
                        <div class="stat-label">Cancelled Appointments</div>
                    </div>
                </div>
                
                <!-- Appointments Table -->
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Patient Name</th>
                                <th>First Name</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Doctor</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($appointments) > 0): ?>
                                <?php foreach ($appointments as $appointment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                        <td><?php echo htmlspecialchars(getFirstName($appointment['patient_name'])); ?></td>
                                        <td><?php echo date('F d, Y', strtotime($appointment['appointment_date'])); ?></td>
                                        <td><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></td>
                                        <td><?php echo getDoctorName($appointment['doctor']); ?></td>
                                        <td>
                                            <span class="status-pill status-<?php echo $appointment['status']; ?>">
                                                <i class="fas <?php 
                                                    echo $appointment['status'] == 'complete' ? 'fa-check-circle' : 
                                                        ($appointment['status'] == 'pending' ? 'fa-clock' : 'fa-times-circle'); 
                                                ?> me-1"></i>
                                                <?php echo ucfirst($appointment['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                        id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                    <li>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                            <input type="hidden" name="status" value="complete">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-check-circle text-success me-1"></i> Mark Complete
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                            <input type="hidden" name="status" value="pending">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-clock text-warning me-1"></i> Mark Pending
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-times-circle text-danger me-1"></i> Mark Cancelled
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-calendar-times text-muted" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">No appointments found</p>
                                        <?php if (!empty($filterFirstName)): ?>
                                            <p class="text-muted">Try a different name or clear the filter</p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
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