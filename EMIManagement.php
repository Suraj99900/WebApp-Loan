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
        <h1>EMI Listing Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="BorrowerManagement.php">Home</a></li>
                <li class="breadcrumb-item active">EMI Listing Management</li>
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
                                <div class="row align-items-center">
                                    <!-- Borrower Dropdown -->
                                    <div class="col d-flex align-items-center">
                                        <label for="borrowerSelect" class="me-2 mb-0">Borrower</label>
                                        <select id="borrowerSelectId" class="form-select form-select-sm">
                                            <option value="">Select Borrower</option>
                                            <!-- Populate dynamically -->
                                        </select>
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
                                                        <th>EMI Amount</th>
                                                        <th>Principal Paid</th>
                                                        <th>Interest Paid</th>
                                                        <th>Balance</th>
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
