<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$host = 'localhost'; // Database host (usually localhost)
$username = 'root';  // Database username
$password = '';      // Database password (if any)
$dbname = 'eye_clinic'; // Database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted for adding new inventory
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    // Sanitize and retrieve form data
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $batch_no = mysqli_real_escape_string($conn, $_POST['batch_no']);
    $supplier = mysqli_real_escape_string($conn, $_POST['supplier']);

    // Prepare SQL query to insert data into the inventory table
    $sql = "INSERT INTO inventory (product_name, category, quantity, price, batch_no, supplier) 
            VALUES ('$product_name', '$category', '$quantity', '$price', '$batch_no', '$supplier')";

    // Execute the query and check if the data is inserted
    if (mysqli_query($conn, $sql)) {
        $success_message = "Product added successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}

// Check if the delete or update action is triggered by product_name
if (isset($_GET['delete'])) {
    $product_name = mysqli_real_escape_string($conn, $_GET['delete']);
    $sql_delete = "DELETE FROM inventory WHERE product_name = '$product_name'";
    if (mysqli_query($conn, $sql_delete)) {
        // Redirect after deleting
        header("Location: admin inv.php");
        exit();
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}

if (isset($_POST['edit_product'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $new_product_name = mysqli_real_escape_string($conn, $_POST['new_product_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $batch_no = mysqli_real_escape_string($conn, $_POST['batch_no']);
    $supplier = mysqli_real_escape_string($conn, $_POST['supplier']);

    // Update the product data in the database
    $sql_update = "UPDATE inventory SET product_name='$new_product_name', category='$category', quantity='$quantity', 
                   price='$price', batch_no='$batch_no', supplier='$supplier' WHERE product_name='$product_name'";

    if (mysqli_query($conn, $sql_update)) {
        // Redirect to refresh and show updated data
        header("Location: admin inv.php");
        exit();
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}

// Fetch inventory data
$sql_fetch = "SELECT * FROM inventory";
$result = mysqli_query($conn, $sql_fetch);
$inventory_items = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $inventory_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Eye Clinic Admin</title>
    <!-- Add Font Awesome for better icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom CSS without Tailwind */
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #2563eb;
            --primary-light: #93c5fd;
            --success-color: #10b981;
            --error-color: #ef4444;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius: 8px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            line-height: 1.5;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 24px 16px;
        }

        /* Headers */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Buttons */
        .btn-row {
            display: flex;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            border-radius: var(--radius);
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background-color: white;
            color: var(--text-dark);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .btn-secondary:hover {
            background-color: var(--bg-light);
            box-shadow: var(--shadow-md);
        }

        .btn-danger {
            background-color: var(--error-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-icon {
            margin-right: 8px;
        }

        /* Form elements */
        .card {
            background-color: white;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            margin-bottom: 24px;
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 16px 24px;
        }

        .card-title {
            font-size: 20px;
            font-weight: 500;
        }

        .card-body {
            padding: 24px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 16px;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
        }

        /* Alert boxes */
        .alert {
            padding: 16px;
            border-radius: var(--radius);
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border-left: 4px solid var(--success-color);
            color: #065f46;
        }

        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border-left: 4px solid var(--error-color);
            color: #b91c1c;
        }

        .alert-close {
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            font-size: 16px;
        }

        /* Modal styling */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }

        .modal.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 1000px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
        }

        /* Table styling */
        .search-container {
            position: relative;
            margin-bottom: 24px;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .search-input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 16px;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }

        .table-container {
            overflow-x: auto;
            max-height: 500px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 10;
        }

        th {
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-light);
            border-bottom: 2px solid var(--border-color);
        }

        td {
            padding: 12px 16px;
            font-size: 14px;
            border-bottom: 1px solid var(--border-color);
        }

        tr:hover {
            background-color: rgba(243, 244, 246, 0.5);
        }

        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            padding: 4px;
            margin: 0 4px;
            transition: var(--transition);
        }

        .edit-btn {
            color: var(--primary-color);
        }

        .edit-btn:hover {
            color: var(--primary-dark);
        }

        .delete-btn {
            color: var(--error-color);
        }

        .delete-btn:hover {
            color: #dc2626;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .btn-row {
                width: 100%;
                justify-content: space-between;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Animation effects */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in {
            animation: fadeIn 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Page Header -->
        <header class="page-header">
            <h1 class="page-title">Inventory Management</h1>
            <div class="btn-row">
                <button id="viewInventoryBtn" class="btn btn-primary">
                    <i class="fas fa-box-open btn-icon"></i> View Inventory
                </button>
                <a href="dashboard admin.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left btn-icon"></i> Back to Dashboard
                </a>
            </div>
        </header>

        <?php if (isset($success_message)): ?>
        <div id="successAlert" class="alert alert-success fade-in">
            <div>
                <i class="fas fa-check-circle btn-icon"></i>
                <?php echo $success_message; ?>
            </div>
            <button onclick="document.getElementById('successAlert').style.display='none';" class="alert-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
        <div id="errorAlert" class="alert alert-error fade-in">
            <div>
                <i class="fas fa-exclamation-circle btn-icon"></i>
                <?php echo $error_message; ?>
            </div>
            <button onclick="document.getElementById('errorAlert').style.display='none';" class="alert-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- Add Product Form -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Add New Product</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="admin inv.php">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category" required class="form-control">
                                <option value="select">Select Category</option>
                                <option value="contact-lenses">Contact Lenses</option>
                                <option value="frames">Frames</option>
                                <option value="lenses">Lenses</option>
                                <option value="solutions">Solutions</option>
                                <option value="equipment">Equipment</option>
                                <option value="accessories">Accessories</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" required min="0" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Price (₹)</label>
                            <input type="number" name="price" required min="0" step="0.01" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Batch No</label>
                            <input type="text" name="batch_no" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Supplier</label>
                            <input type="text" name="supplier" required class="form-control">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo btn-icon"></i> Reset
                        </button>
                        <button type="submit" name="add_product" class="btn btn-primary">
                            <i class="fas fa-plus btn-icon"></i> Add Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Inventory Modal -->
    <div id="inventoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="card-title">Inventory List</h2>
                <button id="closeInventoryBtn" class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <!-- Search Bar -->
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" placeholder="Search inventory..." class="search-input">
                </div>

                <!-- Inventory Table -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Batch No</th>
                                <th>Supplier</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inventory_items as $item): ?>
                                <tr>
                                    <td><?php echo $item['product_name']; ?></td>
                                    <td><?php echo $item['category']; ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo '$' . $item['price']; ?></td>
                                    <td><?php echo $item['batch_no']; ?></td>
                                    <td><?php echo $item['supplier']; ?></td>
                                    <td style="text-align: center;">
                                        <button onclick="editProduct('<?php echo $item['product_name']; ?>', '<?php echo $item['category']; ?>', '<?php echo $item['quantity']; ?>', '<?php echo $item['price']; ?>', '<?php echo $item['batch_no']; ?>', '<?php echo $item['supplier']; ?>')" class="action-btn edit-btn">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="admin inv.php?delete=<?php echo $item['product_name']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2 class="card-title">Edit Product</h2>
                <button onclick="closeEditModal()" class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <!-- Edit Product Form -->
                <form action="admin inv.php" method="POST">
                    <input type="hidden" name="product_name" id="product_name" />
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="new_product_name" id="new_product_name" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category" id="category" required class="form-control">
                                <option value="contact-lenses">Contact Lenses</option>
                                <option value="frames">Frames</option>
                                <option value="lenses">Lenses</option>
                                <option value="solutions">Solutions</option>
                                <option value="equipment">Equipment</option>
                                <option value="accessories">Accessories</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" id="quantity" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Price ($)</label>
                            <input type="number" name="price" id="price" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Batch No</label>
                            <input type="text" name="batch_no" id="batch_no" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Supplier</label>
                            <input type="text" name="supplier" id="supplier" required class="form-control">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" onclick="closeEditModal()" class="btn btn-secondary">
                            <i class="fas fa-times btn-icon"></i> Cancel
                        </button>
                        <button type="submit" name="edit_product" class="btn btn-primary">
                            <i class="fas fa-save btn-icon"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal Controls
        const inventoryModal = document.getElementById('inventoryModal');
        const editProductModal = document.getElementById('editProductModal');

        document.getElementById('viewInventoryBtn').addEventListener('click', function() {
            inventoryModal.classList.add('show');
        });

        document.getElementById('closeInventoryBtn').addEventListener('click', function() {
            inventoryModal.classList.remove('show');
        });

        // Close the Edit Modal
        function closeEditModal() {
            editProductModal.classList.remove('show');
        }

        // Open the Edit Modal and prefill the data
        function editProduct(product_name, category, quantity, price, batch_no, supplier) {
            editProductModal.classList.add('show');
            document.getElementById('product_name').value = product_name;
            document.getElementById('new_product_name').value = product_name;
            
            // Set the category dropdown value
            var categorySelect = document.getElementById('category');
            for (var i = 0; i < categorySelect.options.length; i++) {
                if (categorySelect.options[i].value === category) {
                    categorySelect.selectedIndex = i;
                    break;
                }
            }
            
            document.getElementById('quantity').value = quantity;
            document.getElementById('price').value = price;
            document.getElementById('batch_no').value = batch_no;
            document.getElementById('supplier').value = supplier;
        }

        // Function to search inventory items
        document.getElementById('searchInput').addEventListener('input', function() {
            var searchTerm = this.value.toLowerCase();
            var rows = document.querySelectorAll('#inventoryModal table tbody tr');

            rows.forEach(function(row) {
                var productName = row.cells[0].textContent.toLowerCase();
                var category = row.cells[1].textContent.toLowerCase();
                var quantity = row.cells[2].textContent.toLowerCase();
                var price = row.cells[3].textContent.toLowerCase();
                var batchNo = row.cells[4].textContent.toLowerCase();
                var supplier = row.cells[5].textContent.toLowerCase();

                // Check if any of the columns match the search term
                if (productName.includes(searchTerm) || category.includes(searchTerm) ||
                    quantity.includes(searchTerm) || price.includes(searchTerm) ||
                    batchNo.includes(searchTerm) || supplier.includes(searchTerm)) {
                    row.style.display = ''; // Show the row
                } else {
                    row.style.display = 'none'; // Hide the row
                }
            });
        });

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === inventoryModal) {
                inventoryModal.classList.remove('show');
            }
            if (event.target === editProductModal) {
                editProductModal.classList.remove('show');
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            var successAlert = document.getElementById('successAlert');
            var errorAlert = document.getElementById('errorAlert');
            
            if (successAlert) successAlert.style.display = 'none';
            if (errorAlert) errorAlert.style.display = 'none';
        }, 5000);
    </script>
</body>
</html>