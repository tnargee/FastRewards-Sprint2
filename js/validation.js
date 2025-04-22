// Author: Kyle Vitayanuvatti

// Email validation using regex
const validateEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};

// Password validation - checks for minimum length
const validatePassword = (password, minLength = 6) => {
    return password.length >= minLength;
};

// Name validation - checks if name is not empty and contains only letters
const validateName = (name) => {
    return name.trim() !== '' && /^[A-Za-z\s\-']+$/.test(name);
};

// Form validation for signup
function validateSignupForm() {
    const firstName = document.getElementById('firstName').value;
    const lastName = document.getElementById('lastName').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    let isValid = true;
    let errorMessage = '';

    // Clear previous error messages
    const errorContainer = document.getElementById('validation-errors');
    if (errorContainer) {
        errorContainer.innerHTML = '';
        errorContainer.style.display = 'none';
    }

    // Validate first name
    if (!validateName(firstName)) {
        errorMessage += 'First name should contain only letters.<br>';
        document.getElementById('firstName').classList.add('invalid-input');
        isValid = false;
    } else {
        document.getElementById('firstName').classList.remove('invalid-input');
    }

    // Validate last name
    if (!validateName(lastName)) {
        errorMessage += 'Last name should contain only letters.<br>';
        document.getElementById('lastName').classList.add('invalid-input');
        isValid = false;
    } else {
        document.getElementById('lastName').classList.remove('invalid-input');
    }

    // Validate email
    if (!validateEmail(email)) {
        errorMessage += 'Please enter a valid email address.<br>';
        document.getElementById('email').classList.add('invalid-input');
        isValid = false;
    } else {
        document.getElementById('email').classList.remove('invalid-input');
    }

    // Validate password
    if (!validatePassword(password)) {
        errorMessage += 'Password must be at least 6 characters.<br>';
        document.getElementById('password').classList.add('invalid-input');
        isValid = false;
    } else {
        document.getElementById('password').classList.remove('invalid-input');
    }

    // Display error messages if any
    if (!isValid && errorContainer) {
        errorContainer.innerHTML = errorMessage;
        errorContainer.style.display = 'block';
    }

    return isValid;
}

// Form validation for signin
function validateSigninForm() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    let isValid = true;
    let errorMessage = '';

    // Clear previous error messages
    const errorContainer = document.getElementById('validation-errors');
    if (errorContainer) {
        errorContainer.innerHTML = '';
        errorContainer.style.display = 'none';
    }

    // Validate email
    if (!validateEmail(email)) {
        errorMessage += 'Please enter a valid email address.<br>';
        document.getElementById('email').classList.add('invalid-input');
        isValid = false;
    } else {
        document.getElementById('email').classList.remove('invalid-input');
    }

    // Validate password (only check if empty for signin)
    if (password.trim() === '') {
        errorMessage += 'Password cannot be empty.<br>';
        document.getElementById('password').classList.add('invalid-input');
        isValid = false;
    } else {
        document.getElementById('password').classList.remove('invalid-input');
    }

    // Display error messages if any
    if (!isValid && errorContainer) {
        errorContainer.innerHTML = errorMessage;
        errorContainer.style.display = 'block';
    }

    return isValid;
}

// Form validation for transfer form
function validateTransferForm() {
    const amount = document.getElementById('amount').value;
    const fromRestaurant = document.getElementById('fromRestaurant').value;
    const toRestaurant = document.getElementById('toRestaurant').value;
    let isValid = true;
    let errorMessage = '';

    // Clear previous error messages
    const errorContainer = document.getElementById('validation-errors');
    if (errorContainer) {
        errorContainer.innerHTML = '';
        errorContainer.style.display = 'none';
    }

    // Validate amount is a positive number
    if (isNaN(amount) || parseFloat(amount) <= 0) {
        errorMessage += 'Please enter a valid positive number for amount.<br>';
        document.getElementById('amount').classList.add('invalid-input');
        isValid = false;
    } else {
        document.getElementById('amount').classList.remove('invalid-input');
    }

    // Validate from and to restaurants are selected and different
    if (fromRestaurant === '' || toRestaurant === '') {
        errorMessage += 'Please select both from and to restaurants.<br>';
        isValid = false;
    } else if (fromRestaurant === toRestaurant) {
        errorMessage += 'From and To restaurants must be different.<br>';
        isValid = false;
    }

    // Display error messages if any
    if (!isValid && errorContainer) {
        errorContainer.innerHTML = errorMessage;
        errorContainer.style.display = 'block';
    }

    return isValid;
} 