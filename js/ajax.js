// User object to store user info
const User = {
    id: null,
    firstName: null,
    lastName: null,
    email: null,
    
    // Initialize from session data
    init: function() {
        // This would be populated from server-side data
        if (typeof userData !== 'undefined') {
            this.id = userData.id;
            this.firstName = userData.firstName;
            this.lastName = userData.lastName;
            this.email = userData.email;
        }
    }
};

// Fetch transactions using AJAX
const fetchTransactions = () => {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'index.php?command=getTransactionsJson', true);
        
        xhr.onload = function() {
            if (this.status === 200) {
                try {
                    const response = JSON.parse(this.responseText);
                    resolve(response);
                } catch (e) {
                    reject('Error parsing transaction data');
                }
            } else {
                reject('Error fetching transactions: ' + this.status);
            }
        };
        
        xhr.onerror = function() {
            reject('Network error occurred');
        };
        
        xhr.send();
    });
};

// Load transactions into the page
const loadTransactions = async () => {
    try {
        const transactionsContainer = document.getElementById('transactions-list');
        if (!transactionsContainer) return;
        
        // Show loading indicator
        transactionsContainer.innerHTML = '<div class="loading">Loading transactions...</div>';
        
        // Fetch transactions
        const response = await fetchTransactions();
        
        // Ensure we have an array of transactions
        const transactions = Array.isArray(response.transactions) ? response.transactions : [];
        
        // Update the DOM with the transactions
        updateTransactionsList(transactions);
    } catch (error) {
        console.error('Error loading transactions:', error);
        showNotification('Failed to load transactions. Please try again.', 'error');
    }
};

// Use jQuery to fetch point balances
const loadPointBalances = () => {
    $.ajax({
        url: 'index.php?command=getPointBalancesJson',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            // Update the rewards page with the data
            const restaurants = data.map(item => ({
                id: item.restaurant_id,
                name: item.restaurant_name,
                points: item.points,
                logo: item.logo || `restaurant${item.restaurant_id}.png`
            }));
            
            updateRewardsPage(restaurants);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching point balances:', error);
            showNotification('Failed to load rewards data. Please try again.', 'error');
        }
    });
};

// Submit transfer form using jQuery AJAX
function transferPoints(fromRestaurant, toRestaurant, points) {
    $.ajax({
        url: 'index.php?command=processTransfer',
        method: 'POST',
        data: {
            from_restaurant: fromRestaurant,
            to_restaurant: toRestaurant,
            points: points
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update point balances in the UI
                if (response.balances) {
                    Object.keys(response.balances).forEach(function(restaurantId) {
                        const balanceElement = document.querySelector(`#balance-${restaurantId}`);
                        if (balanceElement) {
                            balanceElement.textContent = response.balances[restaurantId];
                        }
                    });
                }
                
                // Show success message
                showNotification(response.message, 'success');
                
                // Reset form
                $('#transfer-form')[0].reset();
                
                // Update conversion rate and received points displays
                updateConversionRate();
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Transfer error:', error);
            showNotification('An error occurred during the transfer. Please try again.', 'error');
        }
    });
}

// Update points display after transfer
const updatePointsDisplay = (balances) => {
    balances.forEach(balance => {
        const balanceElement = document.querySelector(`.balance-${balance.restaurant_id}`);
        if (balanceElement) {
            balanceElement.textContent = `${balance.points} points`;
        }
    });
}; 