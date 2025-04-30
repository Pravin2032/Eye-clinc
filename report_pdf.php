<?php
session_start();
require_once 'db_connection.php';


// Initialize variables
$report_type = $_GET['report_type'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$doctor = $_GET['doctor'] ?? '';
$status = $_GET['status'] ?? '';
$category = $_GET['category'] ?? '';
$reportData = [];
$message = '';

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
        $message = "Invalid report type.";
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

// Function to get report title based on type
function getReportTitle($report_type, $start_date, $end_date, $doctor, $status, $category) {
    $title = ucfirst($report_type) . " Report";
    
    // Add date range if applicable
    if (!empty($start_date) && !empty($end_date)) {
        $title .= " (" . date('F d, Y', strtotime($start_date)) . " to " . date('F d, Y', strtotime($end_date)) . ")";
    } else if (!empty($start_date)) {
        $title .= " (From " . date('F d, Y', strtotime($start_date)) . ")";
    } else if (!empty($end_date)) {
        $title .= " (Until " . date('F d, Y', strtotime($end_date)) . ")";
    }
    
    // Add doctor filter if applicable
    if (!empty($doctor) && $report_type == 'appointments') {
        $title .= " for Dr. " . $doctor;
    }
    
    // Add status filter if applicable
    if (!empty($status) && $report_type == 'appointments') {
        $title .= " - " . ucfirst($status) . " appointments";
    }
    
    // Add category filter if applicable
    if (!empty($category) && $report_type == 'inventory') {
        $title .= " - " . $category . " category";
    }
    
    return $title;
}

// Function to render report output
function renderReportTable($reportData, $report_type) {
    if (empty($reportData)) {
        return "<p>No data to display.</p>";
    }
    
    $output = "<table class='report-table'><thead><tr>";
    
    // Get headers from the first row
    $headers = array_keys($reportData[0]);
    foreach ($headers as $header) {
        // Skip certain columns for PDF format to keep it clean
        if (in_array($header, ['id', 'created_at', 'updated_at'])) {
            continue;
        }
        
        // Format header for display
        $display_header = ucwords(str_replace('_', ' ', $header));
        $output .= "<th>$display_header</th>";
    }
    
    $output .= "</tr></thead><tbody>";
    
    // Add rows
    foreach ($reportData as $row) {
        $output .= "<tr>";
        foreach ($row as $key => $value) {
            // Skip certain columns for PDF format
            if (in_array($key, ['id', 'created_at', 'updated_at'])) {
                continue;
            }
            
            // Format specific columns
            if ($key == 'appointment_date' || $key == 'start_date' || $key == 'dob') {
                $value = !empty($value) ? date('Y-m-d', strtotime($value)) : '';
            } elseif ($key == 'appointment_time') {
                $value = !empty($value) ? date('H:i', strtotime($value)) : '';
            } elseif ($key == 'status') {
                $value = ucfirst($value);
            } elseif ($key == 'eye_conditions' || $key == 'allergies' || $key == 'products' || $key == 'address' || $key == 'qualifications') {
                // Truncate long text fields for better PDF formatting
                if (strlen($value) > 100) {
                    $value = substr($value, 0, 97) . '...';
                }
                $value = nl2br(htmlspecialchars($value));
            }
            
            $output .= "<td>$value</td>";
        }
        $output .= "</tr>";
    }
    
    $output .= "</tbody></table>";
    
    return $output;
}

// Get summary statistics based on report type
function getReportSummary($reportData, $report_type) {
    $summary = "";
    
    switch ($report_type) {
        case 'appointments':
            $total = count($reportData);
            $statuses = [];
            
            foreach ($reportData as $row) {
                if (isset($row['status'])) {
                    $status = $row['status'];
                    if (!isset($statuses[$status])) {
                        $statuses[$status] = 0;
                    }
                    $statuses[$status]++;
                }
            }
            
            $summary .= "<p><strong>Total Appointments:</strong> $total</p>";
            foreach ($statuses as $status => $count) {
                $summary .= "<p><strong>" . ucfirst($status) . ":</strong> $count (" . round(($count/$total)*100, 1) . "%)</p>";
            }
            break;
            
        case 'patients':
            $total = count($reportData);
            $summary .= "<p><strong>Total Patients:</strong> $total</p>";
            break;
            
        case 'inventory':
            $total = count($reportData);
            $totalItems = 0;
            $lowStock = 0;
            
            foreach ($reportData as $row) {
                if (isset($row['quantity'])) {
                    $totalItems += (int)$row['quantity'];
                    if ((int)$row['quantity'] < (int)($row['reorder_level'] ?? 10)) {
                        $lowStock++;
                    }
                }
            }
            
            $summary .= "<p><strong>Total Products:</strong> $total</p>";
            $summary .= "<p><strong>Total Items in Stock:</strong> $totalItems</p>";
            $summary .= "<p><strong>Products Below Reorder Level:</strong> $lowStock</p>";
            break;
            
        default:
            $summary .= "<p><strong>Total Records:</strong> " . count($reportData) . "</p>";
    }
    
    return $summary;
}

$reportTitle = getReportTitle($report_type, $start_date, $end_date, $doctor, $status, $category);
$reportSummary = !empty($reportData) ? getReportSummary($reportData, $report_type) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $reportTitle; ?> - Eye Clinic</title>
    <style>
        /* PDF-friendly styles */
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .report-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        
        .report-header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        
        .report-header p {
            margin: 5px 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .report-summary {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .report-summary p {
            margin: 5px 0;
        }
        
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        
        .report-table th, 
        .report-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .report-table th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
        }
        
        .report-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .report-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .buttons {
            margin: 20px 0;
            text-align: center;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 15px;
            margin: 0 5px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-return {
            background-color: #95a5a6;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <h1><?php echo $reportTitle; ?></h1>
            <p>Generated on: <?php echo date('F d, Y H:i'); ?></p>
            <p>Eye Clinic Management System</p>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($reportData)): ?>
            <div class="report-summary">
                <h3>Summary</h3>
                <?php echo $reportSummary; ?>
            </div>
            
            <div class="report-content">
                <?php echo renderReportTable($reportData, $report_type); ?>
            </div>
            
            <div class="report-footer">
                <p>This report is confidential and intended for authorized personnel only.</p>
                <p>© <?php echo date('Y'); ?> Eye Clinic Management System</p>
            </div>
            
            <div class="buttons no-print">
                <button class="btn" onclick="window.print()">Print Report</button>
                <button class="btn" onclick="exportToPDF()">Export to PDF</button>
                <button class="btn btn-return" onclick="window.location.href='report.php'">Return to Reports</button>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>No data available for this report. Please adjust your filter criteria.</p>
                <div class="buttons no-print">
                    <button class="btn btn-return" onclick="window.location.href='report.php'">Return to Reports</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Function to export to PDF (requires additional library like jsPDF or browser's print-to-PDF)
        function exportToPDF() {
            window.print(); // Using browser's print for now
            // In a real implementation, you might use a JavaScript PDF library
            // or redirect to a server-side PDF generation script
        }
        
        // Auto-print when page loads (optional)
        window.onload = function() {
            // Uncomment the line below if you want the print dialog to appear automatically
            // window.print();
        };
    </script>
</body>
</html>