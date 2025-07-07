<?php
$access = isset($_SESSION['access']) ? strtolower($_SESSION['access']) : null;
if ($access === 'full') {
    $allowedPages = ['dashboard.php','expense.php','expenseHistory.php','rfq.php','rfqHistory.php','issues.php','alert.php','vehicleDetail.php', 'workorder.php', 'workorderHistory.php', 'userRegistration.php','driver.php', 'mydetail.php']; 
    $currentPage = basename($_SERVER['PHP_SELF']); 
    if (!in_array($currentPage, $allowedPages)) {
        header("Location: index.php");
        exit();
    }
}else if ($access === null) {
    $allowedPages = ['vendor.php', 'mydetail.php']; 
    $currentPage = basename($_SERVER['PHP_SELF']); 
    if (!in_array($currentPage, $allowedPages)) {
        header("Location: index.php");
        exit();
    }
}else{
    $allowedPages = ['dashboard.php','expense.php','expenseHistory.php','rfq.php','rfqHistory.php','issues.php','alert.php','vehicleDetail.php', 'workorder.php', 'workorderHistory.php','driver.php', 'mydetail.php']; 
    $currentPage = basename($_SERVER['PHP_SELF']);
    if (!in_array($currentPage, $allowedPages)) {
        header("Location: index.php");
        exit();
    }
}
?>