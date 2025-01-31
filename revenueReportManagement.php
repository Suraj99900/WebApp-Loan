<?php

// Include header section of template
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

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Revenue Report Management</h1>
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

                    <!-- Revenue Report Section -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto">

                            <div class="filter-section mb-4 py-4 px-4">

                                <div class="row">
                                    <div class="col-sm-2">
                                        <label for="borrowerSelect">Borrower</label>
                                        <select id="borrowerSelectId" class="form-select form-select-sm">
                                            <option value="">Select Borrower</option>
                                            <!-- Populate dynamically -->
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="startDate">Start Date</label>
                                        <input type="date" id="startDate" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="endDate">End Date</label>
                                        <input type="date" id="endDate" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="loanStatus" class="me-2 mb-0">Loan Status</label>
                                        <select id="loanStatus" class="form-select form-select-sm">
                                            <option value="">All</option>
                                            <option value="active">Active</option>
                                            <option value="closed">Closed</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="principalAmount" class="me-2 mb-0">Principal Amount</label>
                                        <input type="number" id="principalAmount" class="form-control form-control-sm" placeholder="Min Amount">
                                    </div>

                                    <div class="col-sm-2 mt-3 float-end">
                                        <button id="searchId" class="btn btn-primary" title="Search Filter"><i class="bi bi-search"></i></button>
                                        <button id="resetId" class="btn btn-primary" title="Reset Filter"><i class="bi bi-arrow-clockwise"></i></button>
                                        <button id="exportToExcel" class="btn btn-success" title="Export to Excel"><i class="fa-solid fa-file-excel"></i></button>
                                        <button id="exportToPDF" class="btn btn-success" title="Export to PDF"><i class="fa-solid fa-file-pdf"></i></button>
                                    </div>
                                </div>
                               
                            </div>

                            <div class="card-body">
                                <div class="row mb-5">
                                    <div class="col-12">
                                        <div class="p-2" style="overflow-x: scroll;">
                                            <table id="revenueDetailsTable" class="table table-striped table-hover table-bordered display">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>Sr.no</th>
                                                        <th>Borrower Name</th>
                                                        <th>Interest Collected</th>
                                                        <th>Penalties Collected</th>
                                                        <th>Referral Collected</th>
                                                        <th>Net Revenue</th>
                                                        <th>EMI COUNT</th>
                                                        <th>Outstanding Principal</th>
                                                        <th>Principal Repaid</th>
                                                        <th>Total Payment Done</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="revenueBodyId">
                                                    <!-- Revenue data will be dynamically rendered here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div><!-- End Revenue Report Section -->

                </div>
            </div><!-- End Left side columns -->

        </div>
    </section>

</main><!-- End #main -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<?php include_once "CDN_Footer.php"; ?>

<script src="controller/revenueReportController.js"></script>