<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Eye Clinic</title>
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --text-color: #5a5c69;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --success: #1cc88a;
            --danger: #e74a3b;
            --card-radius: 8px;
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

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 15px;
        }

        .login-card {
            background-color: white;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow);
            padding: 40px 30px;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .clinic-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-icon {
            color: var(--primary-color);
            font-size: 40px;
        }

        h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 600;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 40px;
            border: 1px solid #e1e5eb;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            outline: none;
        }

        .input-group input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: #b1b5c3;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 5px;
            background-color: var(--primary-color);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .login-btn:hover {
            background-color: #3a5ecc;
        }

        .additional-links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 14px;
        }

        .additional-links a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s;
        }

        .additional-links a:hover {
            color: #3a5ecc;
            text-decoration: underline;
        }

        #error-message {
            color: var(--danger);
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            background-color: rgba(231, 74, 59, 0.1);
            font-size: 14px;
            display: none;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }

            .input-group input {
                padding: 12px 12px 12px 40px;
            }

            .login-btn {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="clinic-logo">
                <span class="logo-icon">👁️</span>
            </div>
            <h2>Admin Login</h2>
            
            <form id="loginForm" onsubmit="return login(event)">
                <div class="input-group">
                    <span class="input-icon">👤</span>
                    <input type="text" id="username" placeholder="Username" required autocomplete="username">
                </div>
                
                <div class="input-group">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password" placeholder="Password" required autocomplete="current-password">
                </div>
                
                <button type="submit" class="login-btn">Log In</button>
            </form>

            <div class="additional-links">
                <a href="index.php" class="back">Back to Home</a>
            </div>

            <div id="error-message">Invalid credentials. Please try again.</div>
        </div>
    </div>

    <script src="admin login.js"></script>
    <script>
        // This enhances the form functionality without changing the core logic
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const errorMessage = document.getElementById('error-message');
            
            // Clear error message when user starts typing
            document.getElementById('username').addEventListener('input', function() {
                errorMessage.style.display = 'none';
            });
            document.getElementById('password').addEventListener('input', function() {
                errorMessage.style.display = 'none';
            });
        });
    </script>
</body>
</html>