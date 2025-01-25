<?php
// Include header section of the template
require_once "config.php";
include_once ABS_PATH_TO_PROJECT . "CDN_Header.php";
include_once ABS_PATH_TO_PROJECT . "classes/sessionCheck.php";

$bIsLogin = $oSessionManager->isLoggedIn ? $oSessionManager->isLoggedIn : false;
if (!$bIsLogin) {
    header("Location: pages-login.php", true, 301);
    exit;
} else {
    $iUserID = $oSessionManager->iUserID;
}

include_once "NavBar.php";
include_once "leftBar.php";
?>

<style>
    /* Transparent cards with blur effect */
    .card {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 10px;
    }

    .card-body {
        color: #fff;
    }

    /* Ensure the icons and text are readable */
    .info-box i {
        font-size: 24px;
        color: #fff;
    }

    .info-box h4 {
        color: #fff;
    }

    .info-box h5 {
        color: #fff;
    }

    /* Buttons for exporting */
    button {
        font-size: 16px;
        margin-right: 10px;
    }

    button:hover {
        opacity: 0.8;
    }

    /* Ensure smooth scrolling in tables */
    table {
        table-layout: fixed;
        width: 100%;
    }
</style>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Overall Revenue Report</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Revenue Report</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="row">
                    <!-- Revenue Summary Section -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px);">
                            <div class="card-body">
                                <h5 class="card-title">Revenue Summary</h5>
                                <div class="row">
                                    <!-- Total Revenue -->
                                    <div class="col-md-3">
                                        <div class="info-box text-center">
                                            <i class="bi bi-currency-dollar text-success"></i>
                                            <h4 class="text-dark">Total Revenue</h4>
                                            <h5 class="px-4 text-success" id="totalRevenue">Loading...</h5>
                                        </div>
                                    </div>
                                    <!-- Total Payment Done -->
                                    <div class="col-md-3">
                                        <div class="info-box text-center">
                                            <i class="bi bi-credit-card text-warning"></i>
                                            <h4 class="text-dark">Total Payment Done</h4>
                                            <h5 class="px-4 text-warning" id="totalPaymentDone">Loading...</h5>
                                        </div>
                                    </div>
                                    <!-- Total Penalty -->
                                    <div class="col-md-3">
                                        <div class="info-box text-center">
                                            <i class="bi bi-exclamation-circle text-danger"></i>
                                            <h4 class="text-dark">Total Penalty</h4>
                                            <h5 class="px-4 text-danger" id="totalPenalty">Loading...</h5>
                                        </div>
                                    </div>
                                    <!-- Total Referral -->
                                    <div class="col-md-3">
                                        <div class="info-box text-center">
                                            <i class="bi bi-share text-primary"></i>
                                            <h4 class="text-dark">Total Referral</h4>
                                            <h5 class="px-4 text-primary" id="totalReferral">Loading...</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Revenue Breakdown -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto">
                            <div class="card-body">
                                <div class="row py-3">
                                    <div class="col-md-3">
                                        <label for="fromDate" class="text-dark">From Date</label>
                                        <input type="date" id="fromDate" class="form-control" value="<?= date('Y-m-d', strtotime('-1 year')) ?>" />
                                    </div>
                                    <div class="col-md-3">
                                        <label for="toDate" class="text-dark">To Date</label>
                                        <input type="date" id="toDate" class="form-control" value="<?= date('Y-m-d') ?>" />
                                    </div>
                                    <div class="col-md-2  d-flex align-items-center justify-content-end">
                                        <button id="dateFilterForm" class="btn mt-3 btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                                    </div>
                                    <div class="col d-flex align-items-center justify-content-end">
                                    <button id="exportPdf" class="btn mt-3 btn-danger" title="Export to PDF"><i class="fa-solid fa-file-pdf"></i></button>
                                    <button id="exportExcel" class="btn mt-3 btn-success" title="Export to Excel"><i class="fa-solid fa-file-excel"></i></button>
                                    </div>
                                </div>
                                <h5 class="card-title">Monthly Revenue Breakdown</h5>
                                <div class="p-2" style="overflow-x: scroll;">
                                    <table id="monthlyRevenueTable" class="table table-striped table-hover table-bordered display">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Month</th>
                                                <th>Total Payment</th>
                                                <th>Total Interest Paid</th>
                                                <th>Total Referral Amount</th>
                                                <th>Total Penalty Amount</th>
                                                <th>Total Net</th>
                                                <th>Total Borrowers</th>
                                            </tr>
                                        </thead>
                                        <tbody id="monthlyRevenueBody">
                                            <!-- Monthly revenue data will be dynamically rendered here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Monthly Revenue Breakdown -->
                </div>
            </div><!-- End Left side columns -->
        </div>
    </section>
</main><!-- End #main -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<?php include_once "CDN_Footer.php"; ?>
<script src="controller/revenueSummaryController.js"></script>