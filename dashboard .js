// Check if user is logged in
function checkAuth() {
    // In a real application, you would verify the session/token
    const currentPage = window.location.pathname;
    if (currentPage.includes('user-dashboard.html')) {
        const userEmail = sessionStorage.getItem('userEmail');
        if (!userEmail) {
            window.location.href = 'dashboard user.php';
        } else {
            document.getElementById('userEmail').textContent = userEmail;
        }
}}

// Logout function
function logout() {
    sessionStorage.clear();
    window.location.href = 'login.php';
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
});