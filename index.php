<?php
// Author: Tenzin Nargee
// URL: https://cs4640.cs.virginia.edu/ghf3ky/sprint4/index.php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'FastRewardsController.php';

$controller = new FastRewardsController();
$command = isset($_GET['command']) ? $_GET['command'] : '';

switch($command) {
    case 'signin':
        $controller->showSignIn();
        break;
    case 'processSignIn':
        $controller->processSignIn();
        break;
    case 'signup':
        $controller->showSignUp();
        break;
    case 'processSignUp':
        $controller->processSignUp();
        break;
    case 'home':
        $controller->showHome();
        break;
    case 'scan':
        $controller->showScan();
        break;
    case 'rewards':
        $controller->showRewards();
        break;
    case 'transfer':
        $controller->showTransfer();
        break;
    case 'processTransfer':
        $controller->processTransfer();
        break;
    case 'transactions':
        $controller->showTransactions();
        break;
    case 'getTransactionsJson':
        $controller->getTransactionsJson();
        break;
    case 'getPointBalancesJson':
        $controller->getPointBalancesJson();
        break;
    case 'manage_deals':
        $controller->showManageDeals();
        break;
    case 'process_deal':
        $controller->processDeal();
        break;
    case 'manage_menu_items':
        $controller->showManageMenuItems();
        break;
    case 'process_menu_item':
        $controller->processMenuItem();
        break;
    case 'order':
        $controller->showOrder();
        break;
    case 'process_order':
        $controller->processOrder();
        break;
    case 'order_confirmation':
        $controller->showOrderConfirmation();
        break;
    case 'orders_history':
        $controller->showOrdersHistory();
        break;
    case 'process_file_upload':
        $controller->processFileUpload();
        break;
    case 'logout':
        $controller->logout();
        break;
    default:
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?command=home");
            exit();
        } else {
            header("Location: index.php?command=signin");
            exit();
        }
}
?> 