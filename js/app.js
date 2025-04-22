// Author: Kyle Vitayanuvatti

document.addEventListener('DOMContentLoaded', () => {
    initApp();
    addGlobalEventListeners();
});

const initApp = () => {
    const path = window.location.pathname;
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const command = urlParams.get('command');
    
    // Initialize User object if we have user data
    if (typeof User !== 'undefined') {
        User.init();
    }
    
    // Handle specific pages
    switch (command) {
        case 'signup':
            initSignupPage();
            break;
        case 'signin':
            initSigninPage();
            break;
        case 'home':
            initHomePage();
            break;
        case 'rewards':
            initRewardsPage();
            break;
        case 'transfer':
            initTransferPage();
            break;
        case 'transactions':
            initTransactionsPage();
            break;
        case 'scan':
            initScanPage();
            break;
    }
    
    initNotificationsContainer();
};

// Add global event listeners
const addGlobalEventListeners = () => {
    // Add logout confirmation
    const logoutLink = document.querySelector('a[href*="command=logout"]');
    if (logoutLink) {
        logoutLink.addEventListener('click', (e) => {
            if (!confirm('Are you sure you want to log out?')) {
                e.preventDefault();
            }
        });
    }
    
    // Add theme toggle if present
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
};

// Initialize notifications container
const initNotificationsContainer = () => {
    if (!document.getElementById('notification-container')) {
        const container = document.createElement('div');
        container.id = 'notification-container';
        document.body.appendChild(container);
    }
};

// Toggle between light and dark theme
const toggleTheme = () => {
    document.body.classList.toggle('dark-theme');
    
    // Store theme preference
    if (document.body.classList.contains('dark-theme')) {
        localStorage.setItem('theme', 'dark');
    } else {
        localStorage.setItem('theme', 'light');
    }
};

// Check and apply saved theme
const applyTheme = () => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
};

// Initialize signup page
const initSignupPage = () => {
    const form = document.querySelector('form[action*="processSignUp"]');
    if (!form) return;
    
    // Add validation error container if not present
    if (!document.getElementById('validation-errors')) {
        const errorContainer = document.createElement('div');
        errorContainer.id = 'validation-errors';
        errorContainer.className = 'alert alert-danger';
        errorContainer.style.display = 'none';
        form.insertBefore(errorContainer, form.firstChild);
    }
    
    // Add form validation
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        if (validateSignupForm()) {
            form.submit();
        }
    });
    
    // Add real-time validation for email field
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', () => {
            if (emailInput.value && !validateEmail(emailInput.value)) {
                emailInput.classList.add('invalid-input');
            } else {
                emailInput.classList.remove('invalid-input');
            }
        });
    }
};

// Initialize signin page
const initSigninPage = () => {
    const form = document.querySelector('form[action*="processSignIn"]');
    if (!form) return;
    
    // Add validation error container if not present
    if (!document.getElementById('validation-errors')) {
        const errorContainer = document.createElement('div');
        errorContainer.id = 'validation-errors';
        errorContainer.className = 'alert alert-danger';
        errorContainer.style.display = 'none';
        form.insertBefore(errorContainer, form.firstChild);
    }
    
    // Add form validation
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        if (validateSigninForm()) {
            form.submit();
        }
    });
};

// Initialize home page
const initHomePage = () => {
    // Get quick action buttons
    const quickActions = document.querySelectorAll('.quick-action');
    
    // Add hover effects
    quickActions.forEach(action => {
        action.addEventListener('mouseenter', () => {
            action.classList.add('hover');
        });
        
        action.addEventListener('mouseleave', () => {
            action.classList.remove('hover');
        });
    });
};

// Initialize rewards page
const initRewardsPage = () => {
    // Use jQuery to load point balances if jQuery is available
    if (typeof $ !== 'undefined' && typeof loadPointBalances !== 'undefined') {
        loadPointBalances();
    }
};

// Initialize transfer page
const initTransferPage = () => {
    const form = document.querySelector('form[action*="processTransfer"]');
    if (!form) return;
    
    // Add validation error container if not present
    if (!document.getElementById('validation-errors')) {
        const errorContainer = document.createElement('div');
        errorContainer.id = 'validation-errors';
        errorContainer.className = 'alert alert-danger';
        errorContainer.style.display = 'none';
        form.insertBefore(errorContainer, form.firstChild);
    }
    
    // Add real-time validation and tooltips
    const amountInput = document.getElementById('amount');
    if (amountInput) {
        amountInput.addEventListener('input', () => {
            // Validate input is a number
            if (amountInput.value && (isNaN(amountInput.value) || parseFloat(amountInput.value) <= 0)) {
                amountInput.classList.add('invalid-input');
            } else {
                amountInput.classList.remove('invalid-input');
            }
        });
    }
    
    // Add form validation
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        if (validateTransferForm()) {
            // If jQuery is available, use AJAX to submit the form
            if (typeof $ !== 'undefined' && typeof submitTransfer !== 'undefined') {
                submitTransfer($(form).serialize());
            } else {
                form.submit();
            }
        }
    });
    
    // Add restaurant selection logic
    const fromSelect = document.getElementById('fromRestaurant');
    const toSelect = document.getElementById('toRestaurant');
    
    if (fromSelect && toSelect) {
        fromSelect.addEventListener('change', () => {
            // Disable the same restaurant in the "to" dropdown
            Array.from(toSelect.options).forEach(option => {
                option.disabled = option.value === fromSelect.value;
            });
            if (toSelect.value === fromSelect.value) {
                toSelect.value = '';
            }
        });
    }
};

// Initialize transactions page
const initTransactionsPage = () => {
    // Load transactions with AJAX if the function exists
    if (typeof loadTransactions !== 'undefined') {
        loadTransactions();
    }
    
    // Add filter functionality
    const filterSelect = document.getElementById('transaction-filter');
    if (filterSelect) {
        filterSelect.addEventListener('change', () => {
            const filter = filterSelect.value;
            const transactions = document.querySelectorAll('.transaction-card');
            
            if (filter === 'all') {
                transactions.forEach(card => card.style.display = 'flex');
            } else {
                transactions.forEach(card => {
                    if (card.querySelector(`.points.${filter}`)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
        });
    }
};

// Initialize scan page
const initScanPage = () => {
    const scanButton = document.getElementById('scan-button');
    if (!scanButton) return;
    
    scanButton.addEventListener('click', () => {
        // Simulate scanning animation
        const scanArea = document.querySelector('.scan-area');
        if (scanArea) {
            scanArea.classList.add('scanning');
            
            // Show success after 2 seconds
            setTimeout(() => {
                scanArea.classList.remove('scanning');
                showNotification('Scan completed successfully! Points added to your account.', 'success');
            }, 2000);
        }
    });
}; 