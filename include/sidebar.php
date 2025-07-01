<?php
session_start();

// Check if user_id exists in session, if not redirect to index.php
if (isset($_SESSION['user_id'])) {
    // User is logged in, retrieve access level
    $access = isset($_SESSION['access']) ? strtolower($_SESSION['access']) : null;
} else {
    // User is not logged in, redirect to index.php
    header("Location: index.php");
    exit(); // Ensure the script stops after redirection
}
?>

<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <!-- <li class="menu-title" key="t-menu">Menu</li> -->
               

                <?php
                // Check if user_id exists but access is null
                if ($access === null) {
                    // If user_id exists but access is null, show only Vendor Approval and My Details pages
                ?>
                <li>
                    <a href="./vendor.php" class="waves-effect" >
                        <i class="bx bx-car"></i>
                        <span key="vendor">Vendor Approval</span>
                    </a>
                </li>
                <li>
                    <a href="./mydetail.php" class="waves-effect" >
                        <i class="bx bxs-id-card"></i>
                        <span key="User Detail">My Details</span>
                    </a>
                </li>
                <?php
                } elseif ($access === "full") {
                    // Show all pages if access is "full"
                ?>
                 <li>
                    <a href="./dashboard.php" class="waves-effect" >
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboard">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="./vehicleDetail.php" class="waves-effect" >
                        <i class="bx bx-car"></i>
                        <span key="vehicle-detail">Vehicle Detail</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="fas fa-file-signature"></i>
                        <span key="t-dashboards">RFQ / Work Order</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="./rfq.php"  key="t-tui-calendar" class='text-white' style="font-size: 12px;"> RFQ / Workorder</a></li>
                        <li><a href="./rfqHistory.php"  key="t-full-calendar" class='text-white' style="font-size: 12px;">RFQ / Workorder History</a></li>
                    </ul>
                </li>

                <!-- <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="fas fa-file-signature"></i>
                        <span key="t-dashboards">Job / Work Order</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="./workorder.php"  key="t-tui-calendar" class='text-white' style="font-size: 12px;">Job / Work Order Request</a></li>
                        <li><a href="./workorderHistory.php"  key="t-full-calendar" class='text-white' style="font-size: 12px;">Job / Work Order History</a></li>
                    </ul>
                </li> -->
                <!-- <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="fas fa-file-invoice-dollar"></i>

                        <span key="t-dashboards">Expense</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="./expenseHistory.php" key="t-tui-calendar"  class='text-white' style="font-size: 12px;">Expense History</a></li>
                    </ul>
                </li> -->
                <li>
                    <a href="./expenseHistory.php" class="waves-effect" >
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span key="Alerst">Expense History</span>
                    </a>
                </li>
                <li>
                    <a href="./alert.php" class="waves-effect" >
                        <i class="mdi mdi-bell-alert"></i>
                        <span key="Alerst">Alerts</span>
                    </a>
                </li>
                <!-- <li>
                    <a href="./issues.php" class="waves-effect" >
                        <i class="fas fa-exclamation-circle"></i>
                        <span key="Alerst">Issues</span>
                    </a>
                </li> -->
                <li>
                    <a href="./driver.php" class="waves-effect" >
                        <i class="fa-solid fa-people-group"></i>
                        <span key="Driver">Driver</span>
                    </a>
                </li>
                <li>
                    <a href="./userRegistration.php" class="waves-effect" >
                        <i class="bx bx-user"></i>
                        <span key="User Registration">User Registration</span>
                    </a>
                </li>
                <li>
                    <a href="./mydetail.php" class="waves-effect" >
                        <i class="bx bxs-id-card"></i>
                        <span key="User Detail">My Details</span>
                    </a>
                </li>
                <?php
                } else {
                ?>
                <li>
                    <a href="./dashboard.php" class="waves-effect" >
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboard">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="./vehicleDetail.php" class="waves-effect" >
                        <i class="bx bx-car"></i>
                        <span key="vehicle-detail">Vehicle Detail</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="fas fa-file-signature"></i>
                        <span key="t-dashboards">RFQ / Work Order</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="./rfq.php"  key="t-tui-calendar" class='text-white' style="font-size: 12px;"> RFQ / Workorder</a></li>
                        <li><a href="./rfqHistory.php"  key="t-full-calendar" class='text-white' style="font-size: 12px;">RFQ / Workorder History</a></li>
                    </ul>
                </li>

                <!-- <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="fas fa-file-signature"></i>
                        <span key="t-dashboards">Job / Work Order</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="./workorder.php"  key="t-tui-calendar" class='text-white' style="font-size: 12px;">Job / Work Order Request</a></li>
                        <li><a href="./workorderHistory.php"  key="t-full-calendar" class='text-white' style="font-size: 12px;">Job / Work Order History</a></li>
                    </ul>
                </li> -->
                <!-- <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="fas fa-file-invoice-dollar"></i>

                        <span key="t-dashboards">Expense</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="./expenseHistory.php" key="t-tui-calendar"  class='text-white' style="font-size: 12px;">Expense History</a></li>
                    </ul>
                </li> -->
                <li>
                    <a href="./expenseHistory.php" class="waves-effect" >
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span key="Alerst">Expense History</span>
                    </a>
                </li>
                <li>
                    <a href="./alert.php" class="waves-effect" >
                        <i class="mdi mdi-bell-alert"></i>
                        <span key="Alerst">Alerts</span>
                    </a>
                </li>
                <!-- <li>
                    <a href="./issues.php" class="waves-effect" >
                        <i class="fas fa-exclamation-circle"></i>
                        <span key="Alerst">Issues</span>
                    </a>
                </li> -->
                <li>
                    <a href="./driver.php" class="waves-effect" >
                        <i class="fa-solid fa-people-group"></i>
                        <span key="Driver">Driver</span>
                    </a>
                </li>
                <li>
                    <a href="./mydetail.php" class="waves-effect" >
                        <i class="bx bxs-id-card"></i>
                        <span key="User Detail">My Details</span>
                    </a>
                </li>
                <?php
                }
                ?>
            </ul>
        </div>
    </div>
</div>