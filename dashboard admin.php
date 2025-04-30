<?php
// Start session if needed
session_start();

// Database connection
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "eye_clinic";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get total number of patients
function getTotalPatients($conn) {
    $sql = "SELECT COUNT(*) as total FROM patients";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["total"];
    }
    return 0;
}

// Function to get today's appointments
function getTodaysAppointments($conn) {
    $today = date("Y-m-d");
    $sql = "SELECT COUNT(*) as total FROM appointments WHERE appointment_date = '$today'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["total"];
    }
    return 0;
}

// Function to get available doctors
function getAvailableDoctors($conn) {
    $sql = "SELECT COUNT(*) as total FROM doctors";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["total"];
    }
    return 0;
}

// Function to get pending reports (appointments with status pending)
function getPendingReports($conn) {
    $sql = "SELECT COUNT(*) as total FROM appointments WHERE status = 'pending'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["total"];
    }
    return 0;
}

// Function to get recent activities
function getRecentActivities($conn) {
    $activities = array();
    
    // Get latest patient registration
    $sql = "SELECT CONCAT(first_name, ' ', last_name) as name, created_at 
            FROM patients 
            ORDER BY created_at DESC 
            LIMIT 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $time = date("h:i A", strtotime($row["created_at"]));
        $activities[] = array(
            "time" => $time,
            "description" => "New patient registration: " . $row["name"]
        );
    }
    
    // Get latest appointment update
    $sql = "SELECT patient_name, appointment_date 
            FROM appointments 
            ORDER BY id DESC 
            LIMIT 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $time = date("h:i A");
        $activities[] = array(
            "time" => $time,
            "description" => "Appointment scheduled: " . $row["patient_name"]
        );
    }
    
    // Get low inventory items
    $sql = "SELECT product_name, quantity 
            FROM inventory 
            WHERE quantity < 10 
            ORDER BY quantity ASC 
            LIMIT 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $time = date("h:i A");
        $activities[] = array(
            "time" => $time,
            "description" => "Inventory alert: " . $row["product_name"] . " running low (" . $row["quantity"] . " left)"
        );
    }
    
    return $activities;
}

// Get counts for dashboard cards
$totalPatients = getTotalPatients($conn);
$todaysAppointments = getTodaysAppointments($conn);
$availableDoctors = getAvailableDoctors($conn);
$pendingReports = getPendingReports($conn);

// Get recent activities
$recentActivities = getRecentActivities($conn);

// Calculate patient growth
$lastMonth = date("Y-m-d", strtotime("-1 month"));
$sql = "SELECT COUNT(*) as total FROM patients WHERE created_at >= '$lastMonth'";
$result = $conn->query($sql);
$newPatients = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $newPatients = $row["total"];
}
$growthPercentage = ($totalPatients > 0) ? round(($newPatients / $totalPatients) * 100) : 0;

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Eye Clinic</title>
    <link rel="stylesheet" href="dashboard admin.css">
</head>
<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>Eye Clinic</h2>
                <p>Administrator</p>
            </div>
            <ul class="nav-links">
                <li class="active"><a href="#overview">Overview</a></li>
                <li><a href="admin patient.php">Patients</a></li>
                <li><a href="admin appointment.php">Appointments</a></li>
                <li><a href="admin doctor.php">Doctors</a></li>
                <li><a href="admin inv.php">Inventory</a></li>
                <li><a href="admin staff.php">Staff</a></li>
                <li><a href="admin supplier.php">Supplier</a></li>
                <li><a href="report.php">Reports</a></li>
                <li class="logout"><a href="login admin.php" onclick="logout()">Logout</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <header>
                <h1>Admin Dashboard</h1>
                <div class="user-info">
                    <span class="notification">🔔</span>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card">
                    <h3>Total Patients</h3>
                    <p class="stat"><?php echo $totalPatients; ?></p>
                    <p class="trend <?php echo ($growthPercentage > 0) ? 'positive' : 'negative'; ?>">
                        <?php echo ($growthPercentage > 0) ? '↑' : '↓'; ?> <?php echo abs($growthPercentage); ?>% this month
                    </p>
                </div>

                <div class="card">
                    <h3>Today's Appointments</h3>
                    <p class="stat"><?php echo $todaysAppointments; ?></p>
                    <button class="action-btn">View Schedule</button>
                </div>

                <div class="card">
                    <h3>Available Doctors</h3>
                    <p class="stat"><?php echo $availableDoctors; ?></p>
                    <button class="action-btn">Manage Staff</button>
                </div>

                <div class="card">
                    <h3>Pending Reports</h3>
                    <p class="stat"><?php echo $pendingReports; ?></p>
                    <button class="action-btn">View Reports</button>
                </div>
            </div>

            <div class="recent-activity">
                <h2>Recent Activity</h2>
                <div class="activity-list">
                    <?php foreach ($recentActivities as $activity): ?>
                    <div class="activity-item">
                        <span class="time"><?php echo $activity["time"]; ?></span>
                        <p><?php echo $activity["description"]; ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
    <script>
        // dashboard.js - JavaScript for Eye Clinic Admin Dashboard

        // Document ready function
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize dashboard components
            initializeDashboard();
            setupNavigation();
        });

        // Initialize dashboard elements
        function initializeDashboard() {
            // Setup notification click handler
            const notificationBtn = document.querySelector('.notification');
            if (notificationBtn) {
                notificationBtn.addEventListener('click', function() {
                    alert('Notifications feature coming soon!');
                });
            }
            
            // Setup action buttons
            const actionButtons = document.querySelectorAll('.action-btn');
            actionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.textContent.trim();
                    
                    switch(action) {
                        case 'View Schedule':
                            window.location.href = 'admin appointment.php';
                            break;
                        case 'Manage Staff':
                            window.location.href = 'admin doctor.php';
                            break;
                        case 'View Reports':
                            alert('Reports section is under development');
                            break;
                    }
                });
            });
        }

        // Handle navigation and active states
        function setupNavigation() {
            const navLinks = document.querySelectorAll('.nav-links li a');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    // Remove active class from all links
                    document.querySelectorAll('.nav-links li').forEach(item => {
                        item.classList.remove('active');
                    });
                    
                    // Add active class to parent of clicked link
                    this.parentElement.classList.add('active');
                });
            });
            
            // Set active navigation based on current page
            const currentPage = window.location.pathname.split('/').pop();
            
            navLinks.forEach(link => {
                const linkHref = link.getAttribute('href');
                if (linkHref === currentPage || (currentPage === 'dashboard_admin.php' && linkHref === '#overview')) {
                    // Remove active from all
                    document.querySelectorAll('.nav-links li').forEach(item => {
                        item.classList.remove('active');
                    });
                    // Add active to current
                    link.parentElement.classList.add('active');
                }
            });
        }

        // Logout function
        function logout() {
            // Perform any logout tasks here (clear session storage, etc)
            console.log('Logging out user...');
            localStorage.removeItem('adminLoggedIn');
            sessionStorage.clear();
            
            // No need to redirect as the href in the HTML will handle that
            // Returning true allows the default link behavior to continue
            return true;
        }

        // Function to handle refresh data
        function refreshDashboardData() {
            console.log('Refreshing dashboard data...');
            // Refresh the page to get new data
            location.reload();
        }
    </script>
</body>
</html>