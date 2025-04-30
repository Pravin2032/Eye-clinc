// login.js

function login(event) {
    event.preventDefault();  // Prevent form submission

    // Get username and password from input fields
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    // For simplicity, use a static user (in a real system, you'd check with a backend)
    const validUsername = "pravin18";
    const validPassword = "2032";

    // Check if entered credentials match the valid ones
    if (username === validUsername && password === validPassword) {
        // If valid, redirect to the dashboard
        window.location.href = "dashboard admin.php"; // Replace with actual dashboard page
    } else {
        // If invalid, show an error message
        document.getElementById('error-message').style.display = 'block';
    }
}
