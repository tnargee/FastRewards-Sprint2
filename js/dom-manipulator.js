// Function to create a notification element and append it to the container
const showNotification = (message, type = 'info') => {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button class="close-btn">&times;</button>
    `;
    
    // Append to notification container (create if it doesn't exist)
    let notificationContainer = document.getElementById('notification-container');
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'notification-container';
        document.body.appendChild(notificationContainer);
    }
    
    // Add to DOM
    notificationContainer.appendChild(notification);
    
    // Add event listener to close button
    const closeButton = notification.querySelector('.close-btn');
    closeButton.addEventListener('click', () => {
        notification.remove();
    });
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
    
    return notification;
};

// Toggle visibility of an element
const toggleElementVisibility = (elementId) => {
    const element = document.getElementById(elementId);
    if (element) {
        if (element.style.display === 'none' || !element.style.display) {
            element.style.display = 'block';
        } else {
            element.style.display = 'none';
        }
    }
};

// Create and return a transaction card element
const createTransactionCard = (transaction) => {
    const card = document.createElement('div');
    card.className = 'transaction-card';
    
    const dateOptions = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };
    const formattedDate = new Date(transaction.created_at).toLocaleDateString('en-US', dateOptions);
    
    card.innerHTML = `
        <div class="transaction-icon transfer-icon"></div>
        <div class="transaction-details">
            <h3>${transaction.from_restaurant_name} → ${transaction.to_restaurant_name}</h3>
            <p class="transaction-date">${formattedDate}</p>
        </div>
        <div class="transaction-points">
            <p class="points transfer">Transferred: ${transaction.points_transferred} points (Received: ${transaction.points_received} points)</p>
        </div>
    `;
    
    return card;
};

// Update transactions list on the transactions page
const updateTransactionsList = (transactions) => {
    const transactionsContainer = document.getElementById('transactions-list');
    if (!transactionsContainer) return;
    
    // Clear existing transactions
    transactionsContainer.innerHTML = '';
    
    if (transactions.length === 0) {
        const emptyMessage = document.createElement('p');
        emptyMessage.className = 'empty-message';
        emptyMessage.textContent = 'No transactions found.';
        transactionsContainer.appendChild(emptyMessage);
        return;
    }
    
    // Add each transaction to the container
    transactions.forEach(transaction => {
        const card = createTransactionCard(transaction);
        transactionsContainer.appendChild(card);
    });
};

// Create a restaurant card for rewards page
const createRestaurantCard = (restaurant) => {
    const card = document.createElement('div');
    card.className = 'restaurant-card';
    card.dataset.restaurantId = restaurant.id;
    
    card.innerHTML = `
        <div class="restaurant-logo">
            <img src="assets/logos/${restaurant.logo}" alt="${restaurant.name} Logo">
        </div>
        <div class="restaurant-info">
            <h3>${restaurant.name}</h3>
            <p class="points-balance">${restaurant.points} points</p>
        </div>
        <button class="redeem-btn" data-restaurant-id="${restaurant.id}">Redeem</button>
    `;
    
    return card;
};

// Update rewards page with restaurant cards
const updateRewardsPage = (restaurants) => {
    const rewardsContainer = document.getElementById('rewards-container');
    if (!rewardsContainer) return;
    
    // Clear existing content
    rewardsContainer.innerHTML = '';
    
    // Add restaurant cards
    restaurants.forEach(restaurant => {
        const card = createRestaurantCard(restaurant);
        rewardsContainer.appendChild(card);
        
        // Add click event to redeem button
        const redeemBtn = card.querySelector('.redeem-btn');
        redeemBtn.addEventListener('click', () => {
            showRedeemOptions(restaurant);
        });
    });
};

// Show redemption options modal
const showRedeemOptions = (restaurant) => {
    // Create modal overlay
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    
    // Create modal content
    const modal = document.createElement('div');
    modal.className = 'redeem-modal';
    
    modal.innerHTML = `
        <h2>Redeem at ${restaurant.name}</h2>
        <p>You have <strong>${restaurant.points} points</strong> available</p>
        <div class="redeem-options">
            <div class="redeem-option" data-value="100">
                <h3>$5 Gift Card</h3>
                <p>100 points</p>
            </div>
            <div class="redeem-option" data-value="250">
                <h3>$10 Gift Card</h3>
                <p>250 points</p>
            </div>
            <div class="redeem-option" data-value="500">
                <h3>$25 Gift Card</h3>
                <p>500 points</p>
            </div>
        </div>
        <div class="modal-actions">
            <button id="cancel-redeem">Cancel</button>
        </div>
    `;
    
    // Add to DOM
    overlay.appendChild(modal);
    document.body.appendChild(overlay);
    
    // Add event listeners
    document.getElementById('cancel-redeem').addEventListener('click', () => {
        overlay.remove();
    });
    
    // Add click events to redeem options
    const options = modal.querySelectorAll('.redeem-option');
    options.forEach(option => {
        const pointValue = parseInt(option.dataset.value);
        
        // Disable option if not enough points
        if (restaurant.points < pointValue) {
            option.classList.add('disabled');
            option.title = 'Not enough points';
        } else {
            option.addEventListener('click', () => {
                // Here you would process the redemption
                showNotification(`Successfully redeemed ${pointValue} points at ${restaurant.name}!`, 'success');
                overlay.remove();
            });
        }
    });
}; 