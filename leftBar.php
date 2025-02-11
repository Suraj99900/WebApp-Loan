<?php
require_once "./config.php";
include_once ABS_PATH_TO_PROJECT . "classes/sessionCheck.php";
// if (session_status() == PHP_SESSION_NONE) {
//     $bIsLogin = $oSessionManager->isLoggedIn ? $oSessionManager->isLoggedIn : false;
// } else {
//     $bIsLogin = false;
// }
$iActive = isset($_GET['iActive']) ? $_GET['iActive'] : '';
?>

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">


        <li class="nav-item">
            <a class="nav-link collapsed" href="dashboard.php">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <?php if ($oSessionManager->iUserType == 1) { ?>
            <li class="nav-item">
                <a class="nav-link collapsed" href="BorrowerManagement.php">
                    <i class="fa-regular fa-user"></i>
                    <span>Borrower Management</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="ReferralUserManagement.php">
                    <i class="bi bi-person-arms-up"></i>
                    <span>Referral User Management</span>
                </a>
            </li>
        <?php } ?>
        <li class="nav-item">
            <a class="nav-link collapsed" href="LoanManagement.php">
                <i class="bi bi-currency-exchange"></i>
                <span>Loan Payment Management</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="PendingPaymentManagement.php">
                <i class="bi bi-credit-card-2-front"></i>
                <span>Pending Payment</span>
            </a>
        </li>
        <?php if ($oSessionManager->iUserType == 1) { ?>
            <li class="nav-item">
                <a class="nav-link collapsed" href="revenueReportManagement.php">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span>Revenue Report Management</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="totalRevenueMIS.php">
                    <i class="fa-solid fa-chart-column"></i>
                    <span>Overall Revenue Report</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="userManagement.php">
                    <i class="fa-solid fa-user"></i>
                    <span>User Management</span>
                </a>
            </li>
        <?php } ?>
    </ul>

</aside><!-- End Sidebar-->