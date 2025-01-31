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
        <h1>Loan Payment Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Loan Payment Management</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="row">

                    <!-- Loan Payment Management -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto">

                            <div class="filter-section mb-4 py-4 px-4">
                                <div class="row align-items-center">
                                    <div class="col align-items-center me-3">
                                        <div class=" align-items-center me-3">
                                            <label for="filterBorrowerName" class="me-2 mb-0">Borrower Name</label>
                                            <input type="text" id="filterBorrowerName" class="form-control form-control-sm" placeholder="Search by Borrower Name">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <label for="filterLoanDate">From Date</label>
                                        <input type="date" id="filterFromDate" class="form-control">
                                    </div>
                                    <div class="col">
                                        <label for="filterLoanDate">To Date</label>
                                        <input type="date" id="filterToDate" class="form-control">
                                    </div>
                                    <div class="col align-items-center me-3">
                                        <div class=" align-items-center me-3">
                                            <label for="filterPaymentMode" class="me-2 mb-0">Payment Mode</label>
                                            <select id="filterPaymentMode" class="form-select form-select-sm">
                                                <option value="">All</option>
                                                <option value="Cash">Cash</option>
                                                <option value="UPI">UPI</option>
                                                <option value="Bank Transfer">Bank Transfer</option>
                                                <option value="Cheque">Cheque</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col align-items-center me-3">
                                        <div class=" align-items-center">
                                            <button id="filterSearch" class="btn btn-primary btn-sm mt-4">Search</button>
                                            <button id="filterReset" class="btn btn-primary btn-sm mt-4"><i class="fa-solid fa-arrows-rotate"></i></button>
                                        </div>
                                    </div>
                                    <!-- Add and Export Buttons Section -->
                                    <div class="col-auto ms-auto  justify-content-end">
                                        <button id="addPaymentID" data-bs-toggle="offcanvas" data-bs-target="#addPaymentOffcanvas" aria-controls="AddPaymentOffCanvasId" class="btn btn-primary btn-sm me-2 mt-4">Add Payment</button>
                                        <button id="closurePartPaymentButton" data-bs-toggle="offcanvas" data-bs-target="#closurePartPaymentOffcanvas" aria-controls="closurePartPaymentOffcanvas" class="btn btn-secondary btn-sm me-2 mt-4">Closure/Part Payment</button>

                                        <button id="exportPaymentExcel" class="btn btn-success btn-sm me-2 mt-4" title="Export to Excel"><i class="fa-solid fa-file-excel"></i></button>
                                        <button id="exportPaymentPDF" class="btn btn-success btn-sm mt-4" title="Export to PDF"><i class="fa-solid fa-file-pdf"></i></button>
                                    </div>
                                </div>
                            </div>


                            <div class="card-body">
                                <div class="row mb-5">
                                    <div class="col-12">
                                        <div class="p-2" style="overflow-x: scroll;">
                                            <table id="paymentDetailsTable" class="table table-striped table-hover table-bordered display">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>Sr.no</th>
                                                        <th>Borrower Name</th>
                                                        <th>Payment Amount</th>
                                                        <th>Penalty</th>
                                                        <th>Referral Share</th>
                                                        <th>Payment Mode</th>
                                                        <th>Comments</th>
                                                        <th>Received Date</th>
                                                        <th>Due Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="paymentBodyId">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div><!-- End Loan Payment Management -->

                </div>
            </div><!-- End Left side columns -->

        </div>
    </section>

</main><!-- End #main -->


<!-- Offcanvas for Add Loan Payment -->
<div class="offcanvas offcanvas-end dynamic-width" tabindex="-1" id="addPaymentOffcanvas" aria-labelledby="addPaymentOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 id="addPaymentOffcanvasLabel">Add Loan Payment Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="addLoanPaymentForm" method="POST" enctype="multipart/form-data">
            <!-- Row Layout Starts -->
            <div class="row g-3">
                <!-- Borrower Name -->
                <div class="col-md-6">
                    <label for="borrowerName" class="form-label">Borrower Name</label>
                    <select class="form-select" id="borrowerName" name="borrower_id" required>
                        <option value="">Select Borrower</option>
                        <!-- Borrower options populated dynamically -->
                    </select>
                </div>

                <div class="col-md-6" id="loanSelectionField" style="display:none;">
                    <label for="loanSelection" class="form-label">Select Loan</label>
                    <select class="form-select" id="loanSelection" name="loan_id">
                        <option value="">Select Loan</option>
                        <!-- Loan options will be populated dynamically -->
                    </select>
                </div>

                <!-- Loan ID (Hidden Field) -->
                <!-- <div class="col-md-6"> -->
                <!-- <label for="loanId" class="form-label">Loan ID</label> -->
                <input type="hidden" class="form-control" id="loanId" name="loan_id"  required>
                <input type="hidden" id="interestAmountId" name="interest_amount">
                <input type="hidden" id="principalRepaidId" name="principal_repaid">
                <!-- </div> -->

                <!-- Payment Amount -->
                <div class="col-md-6">
                    <label for="paymentAmount" class="form-label">Payment Amount</label>
                    <input type="number" class="form-control" id="paymentAmount" name="payment_amount" placeholder="Enter Payment Amount" step="0.01" required>
                </div>



                <!-- Penalty Amount -->
                <div class="col-md-6">
                    <label for="penaltyAmount" class="form-label">Penalty Amount</label>
                    <input type="number" class="form-control" id="penaltyAmount" name="penalty_amount" placeholder="Enter Penalty Amount" step="0.01">
                </div>

                <!-- Referral Share -->
                <div class="col-md-6">
                    <label for="referralShare" class="form-label">Referral Share</label>
                    <input type="number" class="form-control" id="referralShare" name="referral_share" placeholder="Enter Referral Share" step="0.001" required>
                </div>

                <!-- Payment Mode -->
                <div class="col-md-6">
                    <label for="paymentMode" class="form-label">Mode of Payment</label>
                    <select class="form-select" id="paymentMode" name="payment_mode" required>
                        <option value="UPI">UPI</option>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="sComments" class="form-label">Comments</label>
                    <textarea class="form-control" id="sComments" name="sComments" ></textarea>
                </div>

                <!-- Received Date -->
                <div class="col-md-6">
                    <label for="receivedDate" class="form-label">Received Date</label>
                    <input type="date" class="form-control" id="receivedDate" name="received_date" required>
                </div>

                <!-- Due Date -->
                <div class="col-md-6">
                    <label for="dueDate" class="form-label">Due Date</label>
                    <input type="date" class="form-control" id="paymentDueDateId" name="payment_due_date" readonly required>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary w-100">Save Payment</button>
            </div>
        </form>
    </div>
</div>

<!-- Offcanvas for Closure and Part Payment -->
<div class="offcanvas offcanvas-end dynamic-width" tabindex="-1" id="closurePartPaymentOffcanvas" aria-labelledby="closurePartPaymentOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 id="closurePartPaymentOffcanvasLabel">Closure/Part Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="closurePartPaymentForm" method="POST" enctype="multipart/form-data">
            <!-- Row Layout Starts -->
            <div class="row g-3">
                <!-- Borrower Name -->
                <div class="col-md-6">
                    <label for="borrowerNameForClosure" class="form-label">Borrower Name</label>
                    <select class="form-select" id="borrowerNameForClosure" name="borrower_id" required>
                        <option value="">Select Borrower</option>
                        <!-- Borrower options populated dynamically -->
                    </select>
                </div>

                <div class="col-md-6" id="loanSelectionForClosureFiled" style="display:none;">
                    <label for="loanSelection" class="form-label">Select Loan</label>
                    <select class="form-select" id="loanSelectionForClosure" name="loan_id">
                        <option value="">Select Loan</option>
                        <!-- Loan options will be populated dynamically -->
                    </select>
                </div>

                <!-- Loan ID -->
                <input type="hidden" class="form-control" id="loanIdForClosure" name="loan_id" readonly required>

                <!-- Payment Type -->
                <div class="col-md-6">
                    <label for="paymentType" class="form-label">Payment Type</label>
                    <select class="form-select" id="paymentType" name="payment_type" required>
                        <option value="1">Closure</option>
                        <option value="2">Part Payment</option>
                    </select>
                </div>

                <!-- Payment Amount -->
                <div class="col-md-6">
                    <label for="closurePartPaymentAmount" class="form-label">Payment Amount</label>
                    <input type="number" class="form-control" id="closurePartPaymentAmount" name="payment_amount" placeholder="Enter Payment Amount" step="0.01" required>
                </div>

                <!-- Payment Mode -->
                <div class="col-md-6">
                    <label for="paymentModeForClosure" class="form-label">Mode of Payment</label>
                    <select class="form-select" id="paymentModeForClosure" name="payment_mode" required>
                        <option value="UPI">UPI</option>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="sComments" class="form-label">Comments</label>
                    <textarea class="form-control" id="sComments" name="sComments" ></textarea>
                </div>

                <!-- Received Date -->
                <div class="col-md-6">
                    <label for="receivedDateForClosure" class="form-label">Received Date</label>
                    <input type="date" class="form-control" id="receivedDateForClosure" name="received_date" required>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary w-100">Save Payment</button>
            </div>
        </form>
    </div>
</div>





<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<?php include_once "CDN_Footer.php"; ?>

<script>
    $(document).ready(function() {


        // Export to Excel
        $('#exportPaymentExcel').on('click', function() {
            var name = $('#filterBorrowerName').val();
            const sFromDateFilter = $('#filterFromDate').val();
            const sToDateFilter = $('#filterToDate').val();
            var paymentMode = $('#filterPaymentMode').val();
            window.location.href = 'ExportPDFExcel/LoanPaymentReportExport.php?export=excel&name=' + name + '&paymentMode=' + paymentMode+'&sFromDate='+sFromDateFilter+"&sToDateFilter="+sToDateFilter;
        });

        // Export to PDF
        $('#exportPaymentPDF').on('click', function() {
            var name = $('#filterBorrowerName').val();
            const sFromDateFilter = $('#filterFromDate').val();
            const sToDateFilter = $('#filterToDate').val();
            var paymentMode = $('#filterPaymentMode').val();
            window.location.href = 'ExportPDFExcel/LoanPaymentReportExport.php?export=pdf&name=' + name + '&paymentMode=' + paymentMode+'&sFromDate='+sFromDateFilter+"&sToDateFilter="+sToDateFilter;

        });
    });
</script>

<script src="controller/loanPaymentController.js"></script>