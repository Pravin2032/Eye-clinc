<?php
// Database connection
$servername = "localhost";
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "eye_clinic"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $companyName = trim($_POST['companyName']);
    $contactPerson = trim($_POST['contactPerson']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $products = trim($_POST['products']);
    $terms = trim($_POST['terms']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    }
    // Validate phone number (10-digit format)
    elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error = "Invalid phone number format";
    }
    else {
        // Insert data into the database using prepared statements
        $sql = "INSERT INTO suppliers (companyName, contactPerson, email, phone, address, products, terms, quantity) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $companyName, $contactPerson, $email, $phone, $address, $products, $terms, $quantity);

        if ($stmt->execute()) {
            $success = "Supplier added successfully!";
            // Redirect to the same page to prevent form resubmission
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Fetch all suppliers
$sql = "SELECT * FROM suppliers ORDER BY companyName ASC";
$result = $conn->query($sql);
$suppliers = [];
while ($row = $result->fetch_assoc()) {
    $suppliers[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management - Eye Clinic Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Success notification -->
        <?php if (isset($_GET['success'])): ?>
        <div id="successNotification" class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>Supplier added successfully!</span>
                <button onclick="document.getElementById('successNotification').style.display='none'" class="ml-auto">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Error notification -->
        <?php if (isset($error)): ?>
        <div id="errorNotification" class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span><?= $error ?></span>
                <button onclick="document.getElementById('errorNotification').style.display='none'" class="ml-auto">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <?php endif; ?>

        <!-- Main Content -->
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Left Column: Add Supplier Form -->
            <div class="md:w-7/12 bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Supplier Management</h1>
                        <p class="text-gray-600">Add and manage suppliers for your clinic</p>
                    </div>
                    <a href="dashboard admin.php" class="flex items-center text-blue-600 hover:text-blue-800 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>

                <form id="supplierForm" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="space-y-6">
                    <!-- Company Information -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-building text-blue-600 mr-2"></i>
                            <h2 class="text-lg font-semibold">Company Information</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                                <input type="text" name="companyName" required class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person <span class="text-red-500">*</span></label>
                                <input type="text" name="contactPerson" required class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-address-card text-blue-600 mr-2"></i>
                            <h2 class="text-lg font-semibold">Contact Information</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input type="email" name="email" required class="w-full p-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400"></i>
                                    </div>
                                    <input type="tel" name="phone" required pattern="[0-9]{10}" placeholder="10 digits" class="w-full p-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                                    </div>
                                    <input type="text" name="address" required class="w-full p-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supply Details -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-boxes text-blue-600 mr-2"></i>
                            <h2 class="text-lg font-semibold">Supply Details</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Products/Services Offered <span class="text-red-500">*</span></label>
                                <textarea name="products" required rows="3" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Terms <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-file-invoice-dollar text-gray-400"></i>
                                    </div>
                                    <select name="terms" required class="w-full p-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                        <option value="">Select Terms</option>
                                        <option value="net15">Net 15</option>
                                        <option value="net30">Net 30</option>
                                        <option value="net45">Net 45</option>
                                        <option value="net60">Net 60</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-hashtag text-gray-400"></i>
                                    </div>
                                    <input type="number" name="quantity" required min="1" class="w-full p-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="reset" class="px-6 py-2 mr-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <i class="fas fa-times mr-1"></i> Clear
                        </button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-plus mr-1"></i> Add Supplier
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Column: Supplier List -->
            <div class="md:w-5/12 bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Supplier Directory</h2>
                        <p class="text-gray-600"><?= count($suppliers) ?> suppliers registered</p>
                    </div>
                    <button id="toggleListBtn" class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition">
                        <i class="fas fa-list mr-1"></i> View All
                    </button>
                </div>

                <!-- Search Input -->
                <div class="mb-4 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="searchInput" placeholder="Search suppliers..." 
                            class="w-full p-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Supplier Cards -->
                <div id="supplierList" class="space-y-4 max-h-[calc(100vh-300px)] overflow-y-auto pr-2">
                    <?php if (empty($suppliers)): ?>
                        <div class="p-4 bg-gray-50 rounded-lg text-center">
                            <i class="fas fa-info-circle text-blue-500 text-2xl mb-2"></i>
                            <p class="text-gray-600">No suppliers found. Add your first supplier using the form.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($suppliers as $supplier): ?>
                            <div class="supplier-card p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-blue-700"><?= htmlspecialchars($supplier['companyName']) ?></h3>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full"><?= htmlspecialchars($supplier['terms']) ?></span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div class="flex items-center">
                                        <i class="fas fa-user text-gray-400 mr-2"></i>
                                        <span><?= htmlspecialchars($supplier['contactPerson']) ?></span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                        <span><?= htmlspecialchars($supplier['email']) ?></span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-phone text-gray-400 mr-2"></i>
                                        <span><?= htmlspecialchars($supplier['phone']) ?></span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-hashtag text-gray-400 mr-2"></i>
                                        <span>Qty: <?= htmlspecialchars($supplier['quantity']) ?></span>
                                    </div>
                                </div>
                                <div class="mt-2 pt-2 border-t border-gray-200">
                                    <details class="supplier-details">
                                        <summary class="text-blue-600 hover:text-blue-800 cursor-pointer text-sm flex items-center">
                                            <i class="fas fa-info-circle mr-1"></i> More Details
                                        </summary>
                                        <div class="mt-2 text-sm">
                                            <p class="mb-1"><span class="font-medium">Address:</span> <?= htmlspecialchars($supplier['address']) ?></p>
                                            <p><span class="font-medium">Products:</span> <?= htmlspecialchars($supplier['products']) ?></p>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const supplierCards = document.querySelectorAll('.supplier-card');
            
            supplierCards.forEach(card => {
                const companyName = card.querySelector('h3').textContent.toLowerCase();
                const contactPerson = card.querySelector('.fas.fa-user').nextElementSibling.textContent.toLowerCase();
                const email = card.querySelector('.fas.fa-envelope').nextElementSibling.textContent.toLowerCase();
                const products = card.querySelector('.supplier-details div p:last-child').textContent.toLowerCase();
                
                if (companyName.includes(searchValue) || 
                    contactPerson.includes(searchValue) || 
                    email.includes(searchValue) || 
                    products.includes(searchValue)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
        
        // Toggle list view
        document.getElementById('toggleListBtn').addEventListener('click', function() {
            const supplierList = document.getElementById('supplierList');
            if (supplierList.style.maxHeight) {
                supplierList.style.maxHeight = '';
                this.innerHTML = '<i class="fas fa-list mr-1"></i> View All';
            } else {
                supplierList.style.maxHeight = 'none';
                this.innerHTML = '<i class="fas fa-compress-alt mr-1"></i> Collapse';
            }
        });
        
        // Close notifications after 5 seconds
        setTimeout(() => {
            const successNote = document.getElementById('successNotification');
            if (successNote) successNote.style.display = 'none';
        }, 5000);
    </script>
</body>
</html>