<?php
session_start();
require_once 'db_connection.php'; // Ensure this file exists with your database connection



// Initialize variables
$report_type = '';
$start_date = '';
$end_date = '';
$doctor = '';
$status = '';
$category = '';
$reportData = [];
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $report_type = $_POST['report_type'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $doctor = $_POST['doctor'] ?? '';
    $status = $_POST['status'] ?? '';
    $category = $_POST['category'] ?? '';
    
    // Generate report based on type
    switch ($report_type) {
        case 'appointments':
            generateAppointmentsReport($conn, $start_date, $end_date, $doctor, $status);
            break;
        case 'patients':
            generatePatientsReport($conn, $start_date, $end_date);
            break;
        case 'doctors':
            generateDoctorsReport($conn);
            break;
        case 'inventory':
            generateInventoryReport($conn, $category);
            break;
        case 'staff':
            generateStaffReport($conn);
            break;
        case 'suppliers':
            generateSuppliersReport($conn);
            break;
        default:
            $message = "Please select a report type.";
    }
}

// Function to generate appointments report
function generateAppointmentsReport($conn, $start_date, $end_date, $doctor, $status) {
    global $reportData, $message;
    
    $sql = "SELECT a.*, p.first_name, p.last_name 
            FROM appointments a 
            LEFT JOIN patients p ON a.patient_email = p.email 
            WHERE 1=1";
    
    if (!empty($start_date)) {
        $sql .= " AND a.appointment_date >= '$start_date'";
    }
    
    if (!empty($end_date)) {
        $sql .= " AND a.appointment_date <= '$end_date'";
    }
    
    if (!empty($doctor)) {
        $sql .= " AND a.doctor = '$doctor'";
    }
    
    if (!empty($status)) {
        $sql .= " AND a.status = '$status'";
    }
    
    $sql .= " ORDER BY a.appointment_date, a.appointment_time";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $reportData = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $message = "No appointments found matching the criteria.";
        }
    } else {
        $message = "Error executing query: " . mysqli_error($conn);
    }
}

// Function to generate patients report
function generatePatientsReport($conn, $start_date, $end_date) {
    global $reportData, $message;
    
    $sql = "SELECT * FROM patients WHERE 1=1";
    
    if (!empty($start_date)) {
        $sql .= " AND created_at >= '$start_date 00:00:00'";
    }
    
    if (!empty($end_date)) {
        $sql .= " AND created_at <= '$end_date 23:59:59'";
    }
    
    $sql .= " ORDER BY last_name, first_name";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $reportData = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $message = "No patients found matching the criteria.";
        }
    } else {
        $message = "Error executing query: " . mysqli_error($conn);
    }
}

// Function to generate doctors report
function generateDoctorsReport($conn) {
    global $reportData, $message;
    
    $sql = "SELECT * FROM doctors ORDER BY last_name, first_name";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $reportData = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $message = "No doctors found in the database.";
        }
    } else {
        $message = "Error executing query: " . mysqli_error($conn);
    }
}

// Function to generate inventory report
function generateInventoryReport($conn, $category) {
    global $reportData, $message;
    
    $sql = "SELECT * FROM inventory WHERE 1=1";
    
    if (!empty($category)) {
        $sql .= " AND category = '$category'";
    }
    
    $sql .= " ORDER BY product_name";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $reportData = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $message = "No inventory items found matching the criteria.";
        }
    } else {
        $message = "Error executing query: " . mysqli_error($conn);
    }
}

// Function to generate staff report
function generateStaffReport($conn) {
    global $reportData, $message;
    
    $sql = "SELECT * FROM staff ORDER BY department, role, last_name";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $reportData = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $message = "No staff found in the database.";
        }
    } else {
        $message = "Error executing query: " . mysqli_error($conn);
    }
}

// Function to generate suppliers report
function generateSuppliersReport($conn) {
    global $reportData, $message;
    
    $sql = "SELECT * FROM suppliers ORDER BY companyName";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $reportData = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $message = "No suppliers found in the database.";
        }
    } else {
        $message = "Error executing query: " . mysqli_error($conn);
    }
}

// Function to fetch all doctors for dropdown
function getDoctors($conn) {
    $doctors = [];
    $sql = "SELECT CONCAT(first_name, ' ', last_name) AS name FROM doctors ORDER BY last_name, first_name";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $doctors[] = $row['name'];
        }
    }
    
    return $doctors;
}

// Function to fetch inventory categories for dropdown
function getInventoryCategories($conn) {
    $categories = [];
    $sql = "SELECT DISTINCT category FROM inventory ORDER BY category";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row['category'];
        }
    }
    
    return $categories;
}

// Get doctors for dropdown
$doctors = getDoctors($conn);

// Get inventory categories for dropdown
$categories = getInventoryCategories($conn);

// Function to render report output
function renderReportTable($reportData, $report_type) {
    if (empty($reportData)) {
        return "<p>No data to display.</p>";
    }
    
    $output = "<div class='table-responsive'><table class='table table-striped table-bordered'><thead><tr>";
    
    // Get headers from the first row
    $headers = array_keys($reportData[0]);
    foreach ($headers as $header) {
        // Format header for display
        $display_header = ucwords(str_replace('_', ' ', $header));
        $output .= "<th>$display_header</th>";
    }
    
    $output .= "</tr></thead><tbody>";
    
    // Add rows
    foreach ($reportData as $row) {
        $output .= "<tr>";
        foreach ($row as $key => $value) {
            // Format specific columns
            if ($key == 'created_at' || $key == 'appointment_date' || $key == 'start_date' || $key == 'dob') {
                $value = !empty($value) ? date('Y-m-d', strtotime($value)) : '';
            } elseif ($key == 'appointment_time') {
                $value = !empty($value) ? date('H:i', strtotime($value)) : '';
            } elseif ($key == 'status') {
                $value = ucfirst($value);
            } elseif ($key == 'eye_conditions' || $key == 'allergies' || $key == 'products' || $key == 'address' || $key == 'qualifications') {
                $value = nl2br(htmlspecialchars($value));
            }
            
            $output .= "<td>$value</td>";
        }
        $output .= "</tr>";
    }
    
    $output .= "</tbody></table></div>";
    
    return $output;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eye Clinic - Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f0f2f5;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: #2196f3;
            color: white;
            padding: 1rem;
            height: 100vh;
            position: fixed;
        }

        .sidebar-header {
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }

        .sidebar-header h2 {
            margin-bottom: 0.5rem;
        }

        .sidebar-header p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .nav-links {
            list-style: none;
        }

        .nav-links li {
            margin-bottom: 0.5rem;
        }

        .nav-links li a {
            color: white;
            text-decoration: none;
            padding: 0.8rem 1rem;
            display: block;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .nav-links li.active a, .nav-links li a:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-links li.logout {
            margin-top: 2rem;
        }

        .nav-links li.logout a {
            color: #ff4444;
            background: rgba(255, 255, 255, 0.9);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 2rem;
            margin-left: 250px;
            background: #f0f2f5;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification {
            font-size: 1.5rem;
            cursor: pointer;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: #2196f3;
            border: none;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: #1976d2;
        }

        .btn-success {
            background: #4caf50;
            border: none;
            transition: background 0.3s ease;
        }

        .btn-success:hover {
            background: #388e3c;
        }

        .btn-info {
            background: #03a9f4;
            border: none;
            color: white;
            transition: background 0.3s ease;
        }

        .btn-info:hover {
            background: #0288d1;
            color: white;
        }

        .alert-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            color: #0d47a1;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #333;
        }

        .table th {
            background: #f5f5f5;
            border-bottom: 2px solid #ddd;
            padding: 0.75rem;
            font-weight: 600;
        }

        .table td {
            padding: 0.75rem;
            border-bottom: 1px solid #ddd;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Print styles */
        @media print {
            .sidebar, .btn, form {
                display: none;
            }
            
            .main-content {
                margin-left: 0;
                padding: 0;
            }
            
            .card {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar navigation -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>Eye Clinic</h2>
                <p>Administrator</p>
            </div>
            <ul class="nav-links">
            <li> <a href="dashboard admin.php">Dashboard</a></li>
                <li><a href="admin patient.php">Patients</a></li>
                <li><a href="admin appointment.php">Appointments</a></li>
                <li><a href="admin doctor.php">Doctors</a></li>
                <li><a href="admin inv.php">Inventory</a></li>
                <li><a href="admin staff.php">Staff</a></li>
                <li><a href="admin supplier.php">Supplier</a></li>
                <li class="active"><a href="report.php">Reports</a></li>
                <li><a href="#settings">Settings</a></li>
                <li class="logout"><a href="login admin.php" onclick="logout()">Logout</a></li>
            </ul>
        </nav>

        <!-- Main content -->
        <main class="main-content">
            <header>
                <h1>Generate Reports</h1>
                <div class="user-info">
                    <span class="notification">🔔</span>
                </div>
            </header>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Report Parameters</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="report_type" class="form-label">Report Type</label>
                                <select name="report_type" id="report_type" class="form-select" required>
                                    <option value="" <?php echo empty($report_type) ? 'selected' : ''; ?>>Select Report Type</option>
                                    <option value="appointments" <?php echo $report_type == 'appointments' ? 'selected' : ''; ?>>Appointments</option>
                                    <option value="patients" <?php echo $report_type == 'patients' ? 'selected' : ''; ?>>Patients</option>
                                    <option value="doctors" <?php echo $report_type == 'doctors' ? 'selected' : ''; ?>>Doctors</option>
                                    <option value="inventory" <?php echo $report_type == 'inventory' ? 'selected' : ''; ?>>Inventory</option>
                                    <option value="staff" <?php echo $report_type == 'staff' ? 'selected' : ''; ?>>Staff</option>
                                    <option value="suppliers" <?php echo $report_type == 'suppliers' ? 'selected' : ''; ?>>Suppliers</option>
                                </select>
                            </div>

                            <!-- Date filters - show for appointments and patients reports -->
                            <div class="col-md-4 date-filter <?php echo in_array($report_type, ['appointments', 'patients']) ? '' : 'd-none'; ?>">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo $start_date; ?>">
                            </div>
                            
                            <div class="col-md-4 date-filter <?php echo in_array($report_type, ['appointments', 'patients']) ? '' : 'd-none'; ?>">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $end_date; ?>">
                            </div>
                        </div>
                        
                        <!-- Doctor filter - show for appointments report only -->
                        <div class="row mb-3 doctor-filter <?php echo $report_type == 'appointments' ? '' : 'd-none'; ?>">
                            <div class="col-md-4">
                                <label for="doctor" class="form-label">Doctor</label>
                                <select name="doctor" id="doctor" class="form-select">
                                    <option value="">All Doctors</option>
                                    <?php foreach ($doctors as $doc): ?>
                                        <option value="<?php echo htmlspecialchars($doc); ?>" <?php echo $doctor == $doc ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($doc); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="complete" <?php echo $status == 'complete' ? 'selected' : ''; ?>>Complete</option>
                                    <option value="cancelled" <?php echo $status == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Category filter - show for inventory report only -->
                        <div class="row mb-3 category-filter <?php echo $report_type == 'inventory' ? '' : 'd-none'; ?>">
                            <div class="col-md-4">
                                <label for="category" class="form-label">Category</label>
                                <select name="category" id="category" class="form-select">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category == $cat ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-info">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($reportData)): ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>
                            <?php 
                            $title = ucfirst($report_type) . " Report";
                            if (!empty($start_date) && !empty($end_date)) {
                                $title .= " (" . $start_date . " to " . $end_date . ")";
                            }
                            echo $title;
                            ?>
                        </h5>
                        <div>
                            <button class="btn btn-success" onclick="exportToExcel('report_table')">Export to Excel</button>
                            <a href="report_pdf.php?report_type=<?php echo $report_type; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&doctor=<?php echo urlencode($doctor); ?>&status=<?php echo $status; ?>&category=<?php echo urlencode($category); ?>" target="_blank" class="btn btn-info">View Printable Report</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="report_table">
                            <?php echo renderReportTable($reportData, $report_type); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/hide filters based on report type
        document.getElementById('report_type').addEventListener('change', function() {
            const reportType = this.value;
            
            // Date filter visibility
            const dateFilters = document.querySelectorAll('.date-filter');
            dateFilters.forEach(filter => {
                filter.classList.toggle('d-none', !['appointments', 'patients'].includes(reportType));
            });
            
            // Doctor filter visibility
            const doctorFilters = document.querySelectorAll('.doctor-filter');
            doctorFilters.forEach(filter => {
                filter.classList.toggle('d-none', reportType !== 'appointments');
            });
            
            // Category filter visibility
            const categoryFilters = document.querySelectorAll('.category-filter');
            categoryFilters.forEach(filter => {
                filter.classList.toggle('d-none', reportType !== 'inventory');
            });
        });
        
        // Export to Excel function
        function exportToExcel(tableID) {
            let tableHTML = document.getElementById(tableID).outerHTML;
            let filename = 'eye_clinic_report_' + new Date().toISOString().slice(0, 10) + '.xls';
            
            let downloadLink = document.createElement("a");
            document.body.appendChild(downloadLink);
            
            // Specify file format using MIME types
            let dataType = 'application/vnd.ms-excel';
            
            // Add BOM for proper UTF-8 encoding
            tableHTML = '\ufeff' + tableHTML;
            
            // Create a download link
            downloadLink.href = 'data:' + dataType + ', ' + encodeURIComponent(tableHTML);
            downloadLink.download = filename;
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
        
        // Logout function
        function logout() {
            console.log('Logging out user...');
            localStorage.removeItem('adminLoggedIn');
            sessionStorage.clear();
            return true;
        }
        
        // Set active navigation based on current page
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.pathname.split('/').pop();
            
            const navLinks = document.querySelectorAll('.nav-links li a');
            navLinks.forEach(link => {
                const linkHref = link.getAttribute('href');
                if (linkHref === currentPage) {
                    // Remove active from all
                    document.querySelectorAll('.nav-links li').forEach(item => {
                        item.classList.remove('active');
                    });
                    // Add active to current
                    link.parentElement.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>