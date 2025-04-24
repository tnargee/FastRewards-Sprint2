<?php
// Script to update navigation in all view files

$viewFiles = [
    'views/home.php',
    'views/scan.php', 
    'views/rewards.php',
    'views/transfer.php',
    'views/transactions.php',
    'views/order.php',
    'views/order_confirmation.php',
    'views/order_restaurants.php',
    'views/orders_history.php',
    'views/manage_deals.php'
];

foreach ($viewFiles as $file) {
    if (!file_exists($file)) {
        echo "File not found: $file\n";
        continue;
    }

    $content = file_get_contents($file);
    
    // Update collapsible menu for small screens
    $pattern = '/<li><a href="index\.php\?command=manage_deals" class="d-block py-1([^"]*)">(.*?)<\/a><\/li>\s*<\/ul>/s';
    $replacement = '<li><a href="index.php?command=manage_deals" class="d-block py-1$1">$2</a></li>' . "\n" .
                   '          <li><a href="index.php?command=manage_menu_items" class="d-block py-1">Manage Menu Items</a></li>' . "\n" .
                   '        </ul>';
    $content = preg_replace($pattern, $replacement, $content);

    // Update sidebar for large screens
    $pattern = '/<a href="index\.php\?command=manage_deals" class="list-group-item list-group-item-action([^"]*)">(.*?)<\/a>\s*<\/div>/s';
    $replacement = '<a href="index.php?command=manage_deals" class="list-group-item list-group-item-action$1">$2</a>' . "\n" .
                   '        <a href="index.php?command=manage_menu_items" class="list-group-item list-group-item-action">Manage Menu Items</a>' . "\n" .
                   '      </div>';
    $content = preg_replace($pattern, $replacement, $content);

    file_put_contents($file, $content);
    echo "Updated: $file\n";
}

echo "Navigation update complete!\n";
?> 