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
        <h1>Pending Payment</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="BorrowerManagement.php">Home</a></li>
                <li class="breadcrumb-item active">Pending Payment</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="row">

                    <!-- EMI Listing Section -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto">

                            <div class="filter-section mb-4 py-4 px-4">
                                <div class="row">
                                    <div class="col-sm-3 col-lg">
                                        <label for="filterBorrowerName">Borrower Name</label>
                                        <select id="borrowerSelectId" class="form-select form-select-sm">
                                            <option value="">Select Borrower</option>
                                            <!-- Populate dynamically -->
                                        </select>
                                    </div>
                                    <div class="col-sm-3 col-lg">
                                        <label for="filterLoanAmount">Loan Amount</label>
                                        <input type="number" id="filterLoanAmount" class="form-control" placeholder="Search by amount">
                                    </div>
                                    <div class="col-sm-3 col-lg">
                                        <label for="filterLoanDate">Loan Date</label>
                                        <input type="date" id="filterLoanDate" class="form-control">
                                    </div>
                                    <div class="col-sm-3 col-lg">
                                        <button id="searchId" class="btn btn-primary btn-sm mt-4"><i class="fa-solid fa-magnifying-glass"></i></button>
                                        <button id="filterRefresh" class="btn btn-primary btn-sm mt-4"><i class="fa-solid fa-arrows-rotate"></i></button>
                                    </div>
                                    <div class="col-sm-3 col-lg">
                                        <div class="mt-3 float-end">
                                            <button id="exportToExcel" class="btn btn-success" title="Export to Excel"><i class="fa-solid fa-file-excel"></i></button>
                                            <button id="exportToPDF" class="btn btn-success" title="Export to PDF"><i class="fa-solid fa-file-pdf"></i></button>
                                        </div>
                                    </div>
                                </div>
                            
                            </div>

                            <div class="card-body">
                                <div class="row mb-5">
                                    <div class="col-12">
                                        <div class="p-2" style="overflow-x: scroll;">
                                            <table id="emiDetailsTable" class="table table-striped table-hover table-bordered display">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>Sr.no</th>
                                                        <th>Borrower Name</th>
                                                        <th>Pending Amount (Interest/EMI)</th>
                                                        <th>Principal Repaid</th>
                                                        <th>OutStanding Principal</th>
                                                        <th>Due Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="emiBodyId">
                                                    <!-- EMIs will be dynamically rendered here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div><!-- End EMI Listing Section -->

                </div>
            </div><!-- End Left side columns -->

        </div>
    </section>

</main><!-- End #main -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<?php include_once "CDN_Footer.php"; ?>

<script src="controller/emiListingController.js"></script>