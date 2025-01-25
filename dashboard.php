<?php
// Include necessary files for session and header
require_once "config.php";
include_once ABS_PATH_TO_PROJECT . "CDN_Header.php";
include_once ABS_PATH_TO_PROJECT . "classes/sessionCheck.php";

// Check if the user is logged in
$isLoggedIn = $oSessionManager->isLoggedIn ? $oSessionManager->isLoggedIn : false;

if (!$isLoggedIn) {
    header("Location: pages-login.php", true, 301);
    exit;
} else {
    $userID = $oSessionManager->iUserID;
}

include_once "NavBar.php";
include_once "leftBar.php";
?>

<style>
    .card {
        margin: 10px;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px); /* Frosted glass effect */
        border: 1px solid rgba(255, 255, 255, 0.1); /* Transparent border */
        color: white;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: scale(1.05); /* Zoom effect on hover */
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Stronger shadow on hover */
    }

    .card h4 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .card .count {
        font-size: 3rem;
        font-weight: bold;
        margin-top: 10px;
    }

    .card .label {
        font-size: 1.1rem;
        color: #ddd;
        margin-top: 10px;
    }

    /* Container styling */
    .container {
        max-width: 1200px;
        margin-top: 30px;
    }

    .row {
        display: flex;
        justify-content: space-between;
    }

    .col {
        flex: 1;
        min-width: 280px;
        margin: 10px;
    }

    /* Responsive styling for smaller screens */
    @media (max-width: 768px) {
        .row {
            flex-direction: column;
        }

        .col {
            margin-bottom: 20px;
        }
    }

    /* Background Styling */
    main#main {
        padding: 20px;
    }


    .container-box {
        box-shadow: 0px 0px 20px rgba(1, 41, 112, 0.1);
        background-color: #fff;
        border-radius: 2%;
    }
</style>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            <!-- Dashboard Cards -->
            <div class="col-lg-12">
                <div class="row">
                    <div class="container-box">
                        <div class="row">
                            <!-- Total Borrowers Card -->
                            <div class="col">
                                <div class="card" style="background-color: #4CAF50;">
                                    <h4>Total Borrowers</h4>
                                    <div class="count" id="totalBorrowers"></div>
                                    <div class="label">Users who have borrowed funds</div>
                                </div>
                            </div>

                            <!-- Total Referral Users Card -->
                            <div class="col">
                                <div class="card" style="background-color: #2196F3;">
                                    <h4>Total Referral Users</h4>
                                    <div class="count" id="totalReferralUsers"></div>
                                    <div class="label">Users referred by others</div>
                                </div>
                            </div>

                            <!-- Total Payments Card -->
                            <div class="col">
                                <div class="card" style="background-color: #FF5722;">
                                    <h4>Total Payments</h4>
                                    <div class="count" id="totalPayments"></div>
                                    <div class="label">Total amount of payments made</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Total Revenue Card -->
                            <div class="col">
                                <div class="card" style="background-color: #FF9800;">
                                    <h4>Total Revenue</h4>
                                    <div class="count" id="totalRevenue"></div>
                                    <div class="label">Total revenue generated</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Dashboard Cards -->

        </div>
    </section>

</main><!-- End #main -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<?php include_once "CDN_Footer.php"; ?>

<script src="controller/totalCount.js"></script>
