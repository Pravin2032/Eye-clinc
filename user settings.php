<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eye Clinic Settings</title>
    <style>
        :root {
    --primary-color: #3b82f6;
    --primary-hover: #2563eb;
    --text-color: #1f2937;
    --text-light: #6b7280;
    --bg-color: #f3f4f6;
    --card-bg: #ffffff;
    --border-color: #e5e7eb;
    --shadow-color: rgba(0, 0, 0, 0.1);
}

body {
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background-color: var(--bg-color);
    color: var(--text-color);
    line-height: 1.5;
}

body.dark-mode {
    --text-color: #f3f4f6;
    --text-light: #9ca3af;
    --bg-color: #111827;
    --card-bg: #1f2937;
    --border-color: #374151;
    --shadow-color: rgba(0, 0, 0, 0.3);
}

.container {
    min-height: 100vh;
    padding: 2rem;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

.settings-card {
    background: var(--card-bg);
    border-radius: 1rem;
    box-shadow: 0 4px 6px var(--shadow-color);
    padding: 2rem;
    width: 100%;
    max-width: 64rem;
}

.header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 2rem;
}

.icon {
    width: 2rem;
    height: 2rem;
    color: var(--primary-color);
}

.section-icon {
    width: 1.5rem;
    height: 1.5rem;
    color: var(--primary-color);
}

.small-icon {
    width: 1rem;
    height: 1rem;
}

h1 {
    font-size: 1.875rem;
    font-weight: bold;
    color: var(--text-color);
    margin: 0;
}

h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-color);
    margin: 0;
}

.section {
    padding: 1.5rem 0;
    border-top: 1px solid var(--border-color);
}

.section-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 768px) {
    .form-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.form-group {
    margin-bottom: 1rem;
}

label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-color);
    margin-bottom: 0.5rem;
}

input[type="text"],
input[type="email"],
input[type="password"],
select {
    width: 100%;
    padding: 0.5rem 0rem;
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    background-color: var(--card-bg);
    color: var(--text-color);
    font-size: 1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.toggle-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.toggle-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.toggle {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
}

.toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--border-color);
    transition: 0.4s;
    border-radius: 24px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: var(--primary-color);
}

input:checked + .slider:before {
    transform: translateX(20px);
}

.volume-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

input[type="range"] {
    width: 100%;
    margin: 0.5rem 0;
}

.volume-value {
    font-size: 0.875rem;
    color: var(--text-light);
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.btn {
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
    border: none;
}

.btn-primary:hover {
    background-color: var(--primary-hover);
}

.btn-secondary {
    background-color: transparent;
    color: var(--text-color);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background-color: var(--bg-color);
}
.btn btn-secondary{
    padding: 1.5rem;
    border-radius: 0.5rem;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
    position: absolute;
    top: 5rem;
    right: 1rem;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="settings-card">
            <div class="header">
                <svg class="icon" viewBox="0 0 24 24" width="24" height="24">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h1>Account Settings</h1>
            </div>
            <div style="position: relative; height: 50px;">
    <a href="dashboard user.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left btn-icon"></i> Back to Dashboard
    </a>
</div>


            <form id="settingsForm">
                <!-- Profile Section -->
                <section class="section">
                    <div class="section-header">
                        <svg class="section-icon" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="7" r="4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h2>Profile Information</h2>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" id="fullName" placeholder="John Doe">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" placeholder="john@example.com">
                        </div>
                    </div>
                </section>

                <!-- Preferences Section -->
                <section class="section">
                    <div class="section-header">
                        <svg class="section-icon" viewBox="0 0 24 24" width="24" height="24">
                            <circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 2v2m0 16v2M2 12h2m16 0h2m-4.6-8.4-1.4 1.4M7.4 7.4 6 6m12.6 12.6-1.4-1.4M7.4 16.6 6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h2>Display Preferences</h2>
                    </div>

                    <div class="form-group">
                        <div class="toggle-container">
                            <span class="toggle-label">
                                <svg class="small-icon" viewBox="0 0 24 24" width="16" height="16">
                                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Dark Mode</span>
                            </span>
                            <label class="toggle">
                                <input type="checkbox" id="darkMode">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="fontSize">Font Size</label>
                            <select id="fontSize">
                                <option value="small">Small</option>
                                <option value="medium" selected>Medium</option>
                                <option value="large">Large</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- Notifications Section -->
                <section class="section">
                    <div class="section-header">
                        <svg class="section-icon" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h2>Notifications</h2>
                    </div>

                    <div class="toggle-container">
                        <span>Enable Notifications</span>
                        <label class="toggle">
                            <input type="checkbox" id="notifications" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </section>

                <!-- Language & Accessibility -->
                <section class="section">
                    <div class="section-header">
                        <svg class="section-icon" viewBox="0 0 24 24" width="24" height="24">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h2>Language & Accessibility</h2>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="language">Language</label>
                            <select id="language">
                                <option value="english" selected>English</option>
                                <option value="spanish">Spanish</option>
                                <option value="french">French</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="volume-label">
                                <svg class="small-icon" viewBox="0 0 24 24" width="16" height="16">
                                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M15.54 8.46a5 5 0 0 1 0 7.07" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M19.07 4.93a10 10 0 0 1 0 14.14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Screen Reader Volume
                            </label>
                            <input type="range" id="volume" min="0" max="100" value="80">
                            <span class="volume-value">80%</span>
                        </div>
                    </div>
                </section>

                <!-- Security Section -->
                <section class="section">
                    <div class="section-header">
                        <svg class="section-icon" viewBox="0 0 24 24" width="24" height="24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h2>Security</h2>
                    </div>

                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword" placeholder="Enter current password">
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" placeholder="Enter new password">
                    </div>
                </section>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // DOM Elements
const settingsForm = document.getElementById('settingsForm');
const darkModeToggle = document.getElementById('darkMode');
const fontSizeSelect = document.getElementById('fontSize');
const notificationsToggle = document.getElementById('notifications');
const languageSelect = document.getElementById('language');
const volumeSlider = document.getElementById('volume');
const volumeValue = document.querySelector('.volume-value');
const cancelButton = document.querySelector('.btn-secondary');
const fullNameInput = document.getElementById('fullName');
const emailInput = document.getElementById('email');
const currentPasswordInput = document.getElementById('currentPassword');
const newPasswordInput = document.getElementById('newPassword');

// Load saved settings from localStorage
function loadSavedSettings() {
    // Dark mode
    const savedDarkMode = localStorage.getItem('darkMode') === 'true';
    darkModeToggle.checked = savedDarkMode;
    document.body.classList.toggle('dark-mode', savedDarkMode);
    
    // Font size
    const savedFontSize = localStorage.getItem('fontSize') || 'medium';
    fontSizeSelect.value = savedFontSize;
    document.body.style.fontSize = getFontSizeValue(savedFontSize);
    
    // Notifications
    const savedNotifications = localStorage.getItem('notifications') !== 'false';
    notificationsToggle.checked = savedNotifications;
    
    // Language
    const savedLanguage = localStorage.getItem('language') || 'english';
    languageSelect.value = savedLanguage;
    
    // Volume
    const savedVolume = localStorage.getItem('volume') || '80';
    volumeSlider.value = savedVolume;
    volumeValue.textContent = savedVolume + '%';
    
    // Profile information
    fullNameInput.value = localStorage.getItem('fullName') || '';
    emailInput.value = localStorage.getItem('email') || '';
}

// Get font size value based on selection
function getFontSizeValue(size) {
    switch(size) {
        case 'small': return '0.875rem';
        case 'large': return '1.125rem';
        default: return '1rem';
    }
}

// Save settings to localStorage
function saveSettings() {
    localStorage.setItem('darkMode', darkModeToggle.checked);
    localStorage.setItem('fontSize', fontSizeSelect.value);
    localStorage.setItem('notifications', notificationsToggle.checked);
    localStorage.setItem('language', languageSelect.value);
    localStorage.setItem('volume', volumeSlider.value);
    localStorage.setItem('fullName', fullNameInput.value);
    localStorage.setItem('email', emailInput.value);
    
    // Only save password if a new one is provided
    if (newPasswordInput.value) {
        // In a real app, you would send this to the server instead
        localStorage.setItem('passwordChanged', 'true');
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', loadSavedSettings);

// Form submission
settingsForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate form
    if (!validateForm()) {
        return;
    }
    
    // Save settings
    saveSettings();
    
    // Show success message
    showNotification('Settings saved successfully!', 'success');
});

// Dark mode toggle
darkModeToggle.addEventListener('change', function(e) {
    document.body.classList.toggle('dark-mode', e.target.checked);
});

// Font size change
fontSizeSelect.addEventListener('change', function(e) {
    document.body.style.fontSize = getFontSizeValue(e.target.value);
});

// Volume slider
volumeSlider.addEventListener('input', function(e) {
    volumeValue.textContent = e.target.value + '%';
});

// Cancel button
cancelButton.addEventListener('click', function() {
    loadSavedSettings();
    showNotification('Changes reverted!', 'info');
});

// Form validation
function validateForm() {
    let isValid = true;
    
    // Validate email format
    if (emailInput.value && !isValidEmail(emailInput.value)) {
        showNotification('Please enter a valid email address', 'error');
        emailInput.focus();
        isValid = false;
    }
    
    // Validate password if changing
    if (newPasswordInput.value && !currentPasswordInput.value) {
        showNotification('Please enter your current password', 'error');
        currentPasswordInput.focus();
        isValid = false;
    }
    
    // Check password strength
    if (newPasswordInput.value && newPasswordInput.value.length < 8) {
        showNotification('Password must be at least 8 characters long', 'error');
        newPasswordInput.focus();
        isValid = false;
    }
    
    return isValid;
}

// Email validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Notification display
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    // Apply styles
    Object.assign(notification.style, {
        position: 'fixed',
        bottom: '20px',
        right: '20px',
        padding: '12px 20px',
        backgroundColor: type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6',
        color: 'white',
        borderRadius: '8px',
        boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)',
        zIndex: '1000',
        transition: 'all 0.3s ease'
    });
    
    // Add to DOM
    document.body.appendChild(notification);
    
    // Remove after delay
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Back to dashboard button
const backButton = document.querySelector('.btn.btn-secondary');
if (backButton) {
    backButton.addEventListener('click', function(e) {
        e.preventDefault();
        // Confirm if there are unsaved changes
        const confirmed = confirm('Are you sure you want to go back? Any unsaved changes will be lost.');
        if (confirmed) {
            window.location.href = 'dashboard user.php';
        }
    });
}

// Theme preferences detection
function detectSystemPreferences() {
    // Check if system prefers dark mode
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        darkModeToggle.checked = true;
        document.body.classList.add('dark-mode');
    }
    
    // Listen for changes to system preferences
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
        darkModeToggle.checked = event.matches;
        document.body.classList.toggle('dark-mode', event.matches);
    });
}

// Initialize system preference detection
detectSystemPreferences();

// Auto-save functionality (debounced)
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func(...args);
        }, wait);
    };
}

const autoSave = debounce(function() {
    // Save all non-sensitive form values
    localStorage.setItem('fullName', fullNameInput.value);
    localStorage.setItem('email', emailInput.value);
    localStorage.setItem('darkMode', darkModeToggle.checked);
    localStorage.setItem('fontSize', fontSizeSelect.value);
    localStorage.setItem('notifications', notificationsToggle.checked);
    localStorage.setItem('language', languageSelect.value);
    localStorage.setItem('volume', volumeSlider.value);
}, 1000);

// Add autosave to form inputs
const formInputs = settingsForm.querySelectorAll('input:not([type="password"]), select');
formInputs.forEach(input => {
    input.addEventListener('input', autoSave);
    input.addEventListener('change', autoSave);
});
    </script>
</body>
</html>