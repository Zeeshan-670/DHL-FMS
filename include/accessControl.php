<?php
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: index.php");
//     exit();
// }



// Retrieve access level
$access = isset($_SESSION['access']) ? strtolower($_SESSION['access']) : null;

// var_dump($_SESSION);
// var_dump($access);
// die;

// Handle access restrictions based on access level

// If access is 'full', the user can access all pages except 'vendor.php'
if ($access === 'full') {
    $allowedPages = ['dashboard.php','expense.php','expenseHistory.php','rfq.php','rfqHistory.php','issues.php','alert.php','vehicleDetail.php', 'workorder.php', 'workorderHistory.php', 'userRegistration.php','driver.php', 'mydetail.php']; 
    $currentPage = basename($_SERVER['PHP_SELF']);  // Get the current page's filename
    // Check if the current page is not in the allowed list
    if (!in_array($currentPage, $allowedPages)) {
        header("Location: index.php"); // Redirect if access is not allowed
        exit();
    }
}else if ($access === null) {
    $allowedPages = ['vendor.php', 'mydetail.php'];  // These are the only allowed pages
    $currentPage = basename($_SERVER['PHP_SELF']);  // Get the current page's filename
    // Check if the current page is not in the allowed list
    if (!in_array($currentPage, $allowedPages)) {
        header("Location: index.php"); // Redirect if access is not allowed
        exit();
    }
}else{
    $allowedPages = ['dashboard.php','expense.php','expenseHistory.php','rfq.php','rfqHistory.php','issues.php','alert.php','vehicleDetail.php', 'workorder.php', 'workorderHistory.php','driver.php', 'mydetail.php']; 
    $currentPage = basename($_SERVER['PHP_SELF']);  // Get the current page's filename
    // Check if the current page is not in the allowed list
    if (!in_array($currentPage, $allowedPages)) {
        header("Location: index.php"); // Redirect if access is not allowed
        exit();
    }
}

// If access is 'null', the user can access only 'vendor.php' and 'mydetail.php'


// If access is any other level, the user can access specific pages
// Access is allowed for 'vehicleDetail.php', 'workorder.php', 'workorderHistory.php', and 'mydetail.php'
// if ($access !== 'full' && $access !== null) {
//     $allowedPages = ['vehicleDetail.php', 'workorder.php', 'workorderHistory.php', 'mydetail.php'];  // Allowed pages for other users
//     $currentPage = basename($_SERVER['PHP_SELF']);  // Get the current page's filename
//     echo 3;

//     // Check if the current page is not in the allowed list
//     // if (!in_array($currentPage, $allowedPages)) {
//     //     header("Location: index.php"); // Redirect if access is not allowed
//     //     exit();
//     // }
// }
?>