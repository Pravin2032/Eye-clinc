<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection parameters
    $servername = "localhost";
    $dbUsername = "root";  // Change if needed
    $dbPassword = "";      // Change if needed
    $dbname = "eye_clinic";

    // Create database connection
    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Database Connection Failed: " . $conn->connect_error);
    }

    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if the fields are not empty
        if (!empty($email) && !empty($password)) {
            // Query the database to find the user
            $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();

                    // Compare the plain text password
                    if ($password === $user['password']) {
                        // Start a session for the user
                        $_SESSION['email'] = $user['email'];

                        // Redirect to dashboard user.php
                        header('Location: dashboard user.php');
                        exit();
                    } else {
                        $error = "Incorrect password.";
                    }
                } else {
                    $error = "No user found with that email.";
                }
            } else {
                $error = "Database query failed.";
            }
        } else {
            $error = "Please fill in all fields.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eye Clinic Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #f8f9fa;
            --accent-color: #7c4dff;
            --text-color: #333;
            --error-color: #e53935;
            --success-color: #43a047;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            position: relative;
            padding: 0;
        }

        .login-header {
            background-color: var(--primary-color);
            color: white;
            padding: 25px 20px;
            text-align: center;
        }

        .login-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.8rem;
        }

        .login-header .icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .login-form {
            padding: 30px 25px;
        }

        .form-group {
            margin-bottom: 22px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-group input:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(124, 77, 255, 0.2);
            outline: none;
        }

        .form-group .input-icon {
            position: relative;
        }

        .form-group .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        .form-group .input-icon input {
            padding-left: 45px;
        }

        .form-group .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
        }

        .login-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 14px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: var(--transition);
            margin-top: 10px;
        }

        .login-btn:hover {
            background-color: var(--accent-color);
            transform: translateY(-2px);
        }

        .error-message {
            background-color: rgba(229, 57, 53, 0.1);
            color: var(--error-color);
            padding: 12px 15px;
            border-radius: var(--border-radius);
            margin: 20px 0;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        .error-message i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .additional-links {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            font-size: 0.9rem;
        }

        .additional-links a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .additional-links a:hover {
            color: var(--accent-color);
            text-decoration: underline;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: var(--text-color);
            font-size: 0.85rem;
            border-top: 1px solid #eee;
            margin-top: 20px;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .container {
                border-radius: 0;
            }
            
            .login-form {
                padding: 25px 20px;
            }
            
            .login-header h2 {
                font-size: 1.5rem;
            }
            
            .form-group {
                margin-bottom: 18px;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-header">
            <div class="icon"><i class="fas fa-eye"></i></div>
            <h2>Eye Clinic Login</h2>
        </div>
        
        <div class="login-form">
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <span class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <button type="submit" name="login" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Log In
                </button>
            </form>

            <!-- Display error message if login fails -->
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Additional Links -->
            <div class="additional-links">
                <a href="forgot-password.html" class="forgot-pass">
                    <i class="fas fa-key"></i> Forgot Password?
                </a>
                <a href="index.php" class="back">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
            
            <div class="footer">
                <p>© <?php echo date('Y'); ?> Eye Clinic. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>