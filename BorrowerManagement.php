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

<style>
    /* Improved Styling */
    .form-range {
        background: linear-gradient(90deg, #0d6efd 50%, #ddd 50%);
        height: 8px;
        border-radius: 4px;
        outline: none;
        appearance: none;
    }

    .form-range::-webkit-slider-thumb {
        appearance: none;
        width: 16px;
        height: 16px;
        background: #0d6efd;
        border-radius: 50%;
        cursor: pointer;
    }

    .form-range::-moz-range-thumb {
        width: 16px;
        height: 16px;
        background: #0d6efd;
        border-radius: 50%;
        cursor: pointer;
    }

    .shadow-sm {
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    .rounded {
        border-radius: 8px;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>



<main id="main" class="main">

    <div class="pagetitle">
        <h1>Borrower Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="BorrowerManagement.php">Home</a></li>
                <li class="breadcrumb-item active">Borrower Management</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="row">


                    <!-- Recent Sales -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto">

                            <div class="filter-section mb-4 py-4 px-4">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="filterBorrowerName">Borrower Name</label>
                                        <input type="text" id="filterBorrowerName" class="form-control" placeholder="Search by name">
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="filterLoanAmount">Loan Amount</label>
                                        <input type="number" id="filterLoanAmount" class="form-control" placeholder="Search by amount">
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="filterLoanDate">Loan Date</label>
                                        <input type="date" id="filterLoanDate" class="form-control">
                                    </div>
                                    <div class="col-sm-3">
                                        <button id="searchId" class="btn btn-primary btn-sm mt-4"><i class="fa-solid fa-magnifying-glass"></i></button>
                                        <button id="filterRefresh" class="btn btn-primary btn-sm mt-4"><i class="fa-solid fa-arrows-rotate"></i></button>
                                    </div>
                                </div>
                                <div class="mt-3 float-end">
                                    <button id="addBorrowerID" data-bs-toggle="offcanvas" data-bs-target="#AddBorrowerOffCanvasId" aria-controls="AddBorrowerOffCanvasId" class="btn btn-primary">Add Borrower</button>
                                    <button id="exportToExcel" class="btn btn-success" title="Export to Excel"><i class="fa-solid fa-file-excel"></i></button>
                                    <button id="exportToPDF" class="btn btn-success" title="Export to PDF"><i class="fa-solid fa-file-pdf"></i></button>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row mb-5">
                                    <div class="col12">
                                        <div class=" p-2 " style="overflow-x: scroll;">
                                            <table id="borrowerDetailsTable" class="table table-striped table-hover table-bordered display">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th scope="col">Sr.no</th>
                                                        <th scope="col">Borrower Name</th>
                                                        <th scope="col">Phone Number</th>
                                                        <th scope="col">Email</th>
                                                        <th scope="col">Loan Amount</th>
                                                        <th scope="col">Disbursed Date</th>
                                                        <th scope="col">Closure Date</th>
                                                        <th scope="col">Loan Status</th>
                                                        <th scope="col">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="borrowerBodyId">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div><!-- End Recent Sales -->

                </div>
            </div><!-- End Left side columns -->

        </div>
    </section>

</main><!-- End #main -->


<div class="AddBorrowerOffcanvas offcanvas bg-card-high dynamic-width offcanvas-end" tabindex="-1" id="AddBorrowerOffCanvasId" aria-labelledby="AddBorrowerOffcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasRightLabel">Add Borrower</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Borrower Form -->
        <form id="addBorrowerForm" method="POST" enctype="multipart/form-data">
            <div class="row">


                <div class="mb-3 col-lg-4 col-sm-3">
                    <label for="borrowerName" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="borrowerName" name="name" required>
                </div>

                <div class="mb-3 col-lg-4 col-sm-3">
                    <label for="borrowerEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="borrowerEmail" name="email" required>
                </div>

                <div class="mb-3 col-lg-4 col-sm-3">
                    <label for="borrowerPhone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="borrowerPhone" name="phone" required>
                </div>

                <div class="mb-3 col-lg-4 col-sm-3">
                    <label for="borrowerAddress" class="form-label">Address</label>
                    <input type="text" class="form-control" id="borrowerAddress" name="address" required>
                </div>

                <!-- <div class="mb-3 col-lg-4 col-sm-3">
                    <label for="borrowerDob" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="borrowerDob" name="dob" required>
                </div> -->

                <div class="mb-3 col-lg-4 col-sm-3">
                    <label for="borrowerGender" class="form-label">Gender</label>
                    <select class="form-select" id="borrowerGender" name="gender" required>
                        <option value="" selected disabled>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div id="fileInputsContainer">
                    <div class="mb-3 col-sm-3 col-lg-12">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <label for="borrowerDocuments" class="form-label">Upload Document</label>
                                <input type="file" class="form-control" name="documents[]" required>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <label for="borrowerDocuments" class="form-label">Document Name</label>
                                <input type="text" class="form-control" name="documentName[]" required>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <button type="button" id="addDocumentBtn" class="btn btn-secondary"><i class="fa-solid fa-plus"></i></button>

            <button type="submit" class="btn btn-primary" id="submitBorrower">Add Borrower</button>
        </form>
    </div>
</div>

<div class="offcanvas offcanvas-end bg-card-high dynamic-width" tabindex="-1" id="viewBorrowerOffcanvas" aria-labelledby="viewBorrowerOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 id="viewBorrowerOffcanvasLabel">View Borrower Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div id="borrowerViewContent">
            <div class="text-center my-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end dynamic-width" tabindex="-1" id="updateBorrowerOffcanvas" aria-labelledby="updateBorrowerOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 id="updateBorrowerOffcanvasLabel">Update Borrower Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="updateBorrowerForm" enctype="multipart/form-data">
            <!-- Dynamic content for updating the borrower will be loaded here -->
        </form>
    </div>
</div>

<!-- Offcanvas for Update Loan Details -->
<div class="offcanvas offcanvas-end dynamic-width" tabindex="-1" id="updateLoanOffcanvas" aria-labelledby="updateLoanOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 id="updateLoanOffcanvasLabel">Update Loan Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="updateLoanForm">
            <!-- Hidden Fields -->
            <input type="hidden" id="hiddenUpdateBorrowerId" name="borrowerId" />
            <input type="hidden" id="hiddenLoanId" name="LoanId" />

            <!-- Row Layout for Inputs -->
            <div class="row g-3">
                <!-- Principal Amount -->
                <div class="col-md-6">
                    <label for="principalAmount" class="form-label">Principal Amount</label>
                    <input type="number" class="form-control" id="principalAmountUpdate" name="principalAmount" placeholder="Enter Principal Amount" required>
                </div>

                <!-- Loan Period -->
                <div class="col-md-6">
                    <label for="loanPeriodUpdate" class="form-label">Loan Period (Months)</label>
                    <input type="range" class="form-range" id="loanPeriodUpdate" name="loanPeriod" min="1" max="500" value="12">
                    <p class="text-center mb-0"><strong><span id="loanPeriodValueUpdate">12</span> Months</strong></p>
                </div>

                <!-- Interest Rate -->
                <div class="col-md-6">
                    <label for="interestRateUpdate" class="form-label">Interest Rate (%)</label>
                    <input type="range" class="form-range" id="interestRateUpdate" name="interestRate" min="1" max="50" step="0.1" value="10">
                    <p class="text-center mb-0"><strong><span id="interestRateValueUpdate">10</span>%</strong></p>
                </div>

                <!-- Disbursed Date -->
                <div class="col-md-6">
                    <label for="disbursedDateUpdate" class="form-label">Disbursed Date</label>
                    <input type="date" class="form-control" id="disbursedDateUpdate" name="disbursedDate" required>
                </div>

                <!-- Closure Date -->
                <div class="col-md-6">
                    <label for="closureDateUpdate" class="form-label">Closure Date</label>
                    <input type="date" class="form-control" id="closureDateUpdate" name="closureDate" readonly>
                </div>
            </div>

            <!-- EMI Calculation Section -->
            <div class="my-4 p-4 text-center shadow-sm rounded bg-light">
                <h5 class="mb-3">Updated EMI Calculation</h5>
                <div class="row g-2">
                    <div class="col-md-4">
                        <p><strong>Principal Amount:</strong> <span id="emiPrincipalUpdate">0</span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Interest Rate:</strong> <span id="emiInterestUpdate">0</span>%</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Loan Period:</strong> <span id="emiPeriodUpdate">0</span> Months</p>
                    </div>
                </div>
                <h4 class="mt-3">Updated EMI: <span id="emiAmountUpdate">0</span></h4>
            </div>

            <button type="submit" class="btn btn-primary w-100">Update Loan</button>
        </form>
    </div>
</div>


<!-- Offcanvas for Add Loan Details -->
<div class="offcanvas offcanvas-end dynamic-width" tabindex="-1" id="addLoanOffcanvas" aria-labelledby="addLoanOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 id="addLoanOffcanvasLabel">Add Loan Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="addLoanForm">
            <!-- Borrower ID -->
            <input type="hidden" id="hiddenBorrowerId" name="borrowerId" />

            <!-- Row Layout for Inputs -->
            <div class="row g-3">
                <!-- Principal Amount -->
                <div class="col-md-6">
                    <label for="loanPrincipalAmount" class="form-label">Principal Amount</label>
                    <input type="number" class="form-control" id="loanPrincipalAmount" name="loanPrincipalAmount" placeholder="Enter Principal Amount" required>
                </div>

                <!-- Loan Period -->
                <div class="col-md-6">
                    <label for="loanPeriod" class="form-label">Loan Period (Months)</label>
                    <input type="range" class="form-range" id="loanPeriod" name="loanPeriod" min="1" max="500" value="12">
                    <p class="text-center mb-0"><strong><span id="loanPeriodValue">12</span> Months</strong></p>
                </div>

                <!-- Interest Rate -->
                <div class="col-md-6">
                    <label for="loanInterestRate" class="form-label">Interest Rate (%)</label>
                    <input type="range" class="form-range" id="loanInterestRate" name="interestRate" min="1" max="50" step="0.1" value="10">
                    <p class="text-center mb-0"><strong><span id="interestRateValue">10</span>%</strong></p>
                </div>

                <!-- Disbursed Date -->
                <div class="col-md-6">
                    <label for="disbursedDate" class="form-label">Disbursed Date</label>
                    <input type="date" class="form-control" id="disbursedDate" name="disbursedDate" required>
                </div>

                <!-- Closure Date -->
                <div class="col-md-6">
                    <label for="closureDate" class="form-label">Closure Date</label>
                    <input type="date" class="form-control" id="closureDate" name="closureDate" readonly>
                </div>
            </div>

            <!-- EMI Calculation Section -->
            <div class="my-4 p-4 text-center shadow-sm rounded bg-light">
                <h5 class="mb-3">EMI Calculation</h5>
                <div class="row g-2">
                    <div class="col-md-4">
                        <p><strong>Principal Amount:</strong> <span id="emiPrincipal">0</span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Interest Rate:</strong> <span id="emiInterest">0</span>%</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Loan Period:</strong> <span id="emiPeriod">0</span> Months</p>
                    </div>
                </div>
                <h4 class="mt-3">Estimated EMI: <span id="monthlyInterestAmount">0</span></h4>
            </div>

            <button type="submit" class="btn btn-primary w-100">Add Loan</button>
        </form>
    </div>
</div>



<!-- Offcanvas for Add Referral Details -->
<div class="offcanvas offcanvas-end dynamic-width" tabindex="-1" id="addReferralOffcanvas" aria-labelledby="addReferralOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 id="addReferralOffcanvasLabel">Add Referral Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="addReferralForm">
            <div class="mb-3">
                <label for="refName" class="form-label">Referral Name</label>
                <input type="text" class="form-control" id="refName" name="ref_name" placeholder="Enter Referral Name" required>
            </div>
            <div class="mb-3">
                <label for="refPhoneNumber" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="refPhoneNumber" name="ref_phone_number" placeholder="Enter Phone Number" required>
            </div>
            <div class="mb-3">
                <label for="refPercentage" class="form-label">Referral Percentage</label>
                <input type="number" class="form-control" id="refPercentage" name="ref_percentage" placeholder="Enter Referral Percentage (e.g., 10.5)" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Save Referral</button>
        </form>
    </div>
</div>

<!-- Offcanvas for Map Referral to Borrower -->
<div class="offcanvas offcanvas-end dynamic-width" tabindex="-1" id="mapReferralOffcanvas" aria-labelledby="mapReferralOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 id="mapReferralOffcanvasLabel">Map Referral to Borrower</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="mapReferralForm">
            <input type="hidden" id="hiddenReferralBorrowerId" name="borrowerId" />
            <div class="mb-3">
                <label for="referralSelect" class="form-label">Select Referral</label>
                <select class="form-select" id="referralSelect" name="referralId" required>
                    <option value="">-- Select Referral --</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Map Referral</button>
        </form>
    </div>
</div>

<!-- Offcanvas for Map/Update Referral to Borrower -->
<div class="offcanvas offcanvas-end dynamic-width" tabindex="-1" id="mapReferralUpdateOffcanvas" aria-labelledby="mapReferralUpdateOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 id="mapReferralUpdateOffcanvasLabel">Map/Update Referral to Borrower</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="mapReferralUpdateForm">
            <input type="hidden" id="hiddenReferralBorrowerUpdateId" name="borrowerId" />
            <div class="mb-3">
                <label for="referralSelect" class="form-label">Select Referral</label>
                <select class="form-select" id="referralUpdateSelect" name="referralUpdateId" required>
                    <option value="">-- Select Referral --</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>





<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<?php include_once "CDN_Footer.php"; ?>

<script>
    $(document).ready(function() {
        // Update Loan Period Value
        $('#loanPeriod').on('input', function() {
            const periodValue = $(this).val();
            $('#loanPeriodValue').text(periodValue);

            calculateMonthlyInterest();
        });

        // Update Interest Rate Value
        $('#loanInterestRate').on('input', function() {
            const interestValue = $(this).val();
            $('#interestRateValue').text(interestValue);

            calculateMonthlyInterest();
        });

        // Update Principal Amount
        $('#loanPrincipalAmount').on('input', function() {
            const principalValue = $(this).val();

            calculateMonthlyInterest();
        });

        // EMI Calculation Function
        function calculateMonthlyInterest() {
            const principal = parseFloat($('#loanPrincipalAmount').val()) || 0;
            const rate = parseFloat($('#loanInterestRate').val()) || 0;
            const period = parseInt($('#loanPeriod').val()) || 0;

            // Display input values
            $('#emiPeriod').text(period);
            $('#emiInterest').text(rate);
            $('#emiPrincipal').text(formatAmount(principal));

            if (principal > 0 && rate > 0) {
                // Calculate monthly interest
                const monthlyInterest = (principal * rate) / 100 ; // Interest for one month
                $('#monthlyInterestAmount').text(formatAmount(monthlyInterest.toFixed(2)));
            } else {
                $('#monthlyInterestAmount').text(0);
            }
        }


        // Initialize EMI on Load
        calculateMonthlyInterest();
    });
</script>


<script>
    var ABS_URL = '<?php echo ABS_URL ?>';
    var iUserID = "<?php echo $iUserID; ?>";
</script>

<script src="controller/borrowerManagerController.js"></script>