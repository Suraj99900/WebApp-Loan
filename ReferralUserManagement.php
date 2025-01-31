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
        <h1>Referral User Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Referral User Management</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="row">

                    <!-- Referral Management -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto">

                            <div class="filter-section mb-4 py-4 px-4">
                                <div class="row align-items-center">
                                    <div class="col d-flex align-items-center">
                                        <!-- Search Section -->
                                        <div class="d-flex align-items-center me-3">
                                            <label for="filterReferralName" class="me-2 mb-0">Referral Name</label>
                                            <input type="text" id="filterReferralName" class="form-control form-control-sm" placeholder="Search by name">
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <button id="filterSearch" class="btn btn-primary btn-sm me-2">Search</button>
                                            <button id="filterReset" class="btn btn-primary btn-sm"><i class="fa-solid fa-arrows-rotate"></i></button>
                                        </div>
                                    </div>
                                    <!-- Add and Export Buttons Section -->
                                    <div class="col-auto ms-auto d-flex justify-content-end">
                                        <button id="addReferralID" data-bs-toggle="offcanvas" data-bs-target="#addReferralOffcanvas" aria-controls="AddReferralOffCanvasId" class="btn btn-primary btn-sm me-2">Add Referral</button>
                                        <button id="exportReferralExcel" class="btn btn-success btn-sm me-2" title="Export to Excel"><i class="fa-solid fa-file-excel"></i></button>
                                        <button id="exportReferralPDF" class="btn btn-success btn-sm" title="Export to PDF"><i class="fa-solid fa-file-pdf"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row mb-5">
                                    <div class="col-12">
                                        <div class="p-2" style="overflow-x: scroll;">
                                            <table id="referralDetailsTable" class="table table-striped table-hover table-bordered display">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>Sr.no</th>
                                                        <th>Referral Name</th>
                                                        <th>Phone Number</th>
                                                        <th>Percentage</th>
                                                        <th>Added On</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="referralBodyId">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div><!-- End Referral Management -->

                </div>
            </div><!-- End Left side columns -->

        </div>
    </section>

</main><!-- End #main -->



<!-- Offcanvas for Add Referral Details -->
<div class="offcanvas offcanvas-end dynamic-width" tabindex="-1" id="addReferralOffcanvas" aria-labelledby="addReferralOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 id="addReferralOffcanvasLabel">Add Referral Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="addReferralForm" method="POST" enctype="multipart/form-data">
            <!-- Referral Details -->
            <div class="row">
                <div class="col-lg-4 col-sm-12 mb-3">
                    <label for="refName" class="form-label">Referral Name</label>
                    <input type="text" class="form-control" id="refName" name="ref_name" placeholder="Enter Referral Name" required>
                </div>
                <div class="col-lg-4 col-sm-12 mb-3">
                    <label for="refPhoneNumber" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="refPhoneNumber" name="ref_phone_number" placeholder="Enter Phone Number" required>
                </div>
                <div class="col-lg-4 col-sm-12 mb-3">
                    <label for="refPercentage" class="form-label">Referral Percentage</label>
                    <input type="number" class="form-control" id="refPercentage" name="ref_percentage" placeholder="Enter Referral Percentage (e.g., 10.5)" step="0.01" required>
                </div>
            </div>

            <!-- Document Upload Section -->
            <div id="fileInputsContainer">
                <div class="row align-items-end mb-3">
                    <div class="col-lg-6 col-sm-12">
                        <label for="referralDocumentsUpload" class="form-label">Upload Document</label>
                        <input type="file" class="form-control" name="documents[]" required>
                    </div>
                    <div class="col-lg-4 col-sm-12">
                        <label for="referralDocumentsName" class="form-label">Document Name</label>
                        <input type="text" class="form-control" name="documentName[]" placeholder="Enter Document Name" required>
                    </div>
                    <div class="col-lg-2 col-sm-2 text-end">
                        <button type="button" id="addDocumentReferralId" class="btn btn-secondary"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <button type="submit" class="btn btn-primary w-100">Save Referral</button>
        </form>
    </div>
</div>


<!-- Offcanvas for Update Referral Details -->
<div class="offcanvas offcanvas-end dynamic-width" tabindex="-1" id="updateReferralOffcanvas" aria-labelledby="updateReferralOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 id="updateReferralOffcanvasLabel">Update Referral Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="updateReferralForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="referralId" name="referral_id">

            <!-- Update Referral Name -->
            <div class="row">
                <div class="col-lg-4 col-sm-12 mb-3">
                    <label for="updateRefName" class="form-label">Referral Name</label>
                    <input type="text" class="form-control" id="updateRefName" name="ref_name" placeholder="Enter Referral Name" required>
                </div>

                <!-- Update Phone Number -->
                <div class="col-lg-4 col-sm-12 mb-3">
                    <label for="updateRefPhoneNumber" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="updateRefPhoneNumber" name="ref_phone_number" placeholder="Enter Phone Number" required>
                </div>

                <!-- Update Referral Percentage -->
                <div class="col-lg-4 col-sm-12 mb-3">
                    <label for="updateRefPercentage" class="form-label">Referral Percentage</label>
                    <input type="number" class="form-control" id="updateRefPercentage" name="ref_percentage" placeholder="Enter Referral Percentage (e.g., 10.5)" step="0.01" required>
                </div>
            </div>

            <!-- Existing Document Section -->
            <div id="existingFileInputsContainer">
                <h6 class="mb-3">Existing Documents</h6>
                <div class="row align-items-end mb-3">
                    <div class="col-lg-6 col-sm-12">
                        <label class="form-label">Uploaded Document</label>
                        <input type="text" class="form-control" name="existing_document_name[]" value="Document1.pdf" readonly>
                    </div>
                    <div class="col-lg-4 col-sm-12">
                        <label class="form-label">Document Description</label>
                        <input type="text" class="form-control" name="existing_document_description[]" value="Loan Agreement" readonly>
                    </div>
                    <div class="col-lg-2 col-sm-2 text-end">
                        <button type="button" class="btn btn-danger removeDocumentBtn"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            </div>

            <!-- Add New Documents -->
            <div id="newFileInputsContainer">
                <h6 class="mt-4">Add New Documents</h6>
                <div class="row align-items-end mb-3">
                    <div class="col-lg-6 col-sm-12">
                        <label for="referralDocuments" class="form-label">Upload New Document</label>
                        <input type="file" class="form-control" name="new_documents[]">
                    </div>
                    <div class="col-lg-4 col-sm-12">
                        <label for="referralDocuments" class="form-label">Document Name</label>
                        <input type="text" class="form-control" name="new_document_name[]" placeholder="Enter Document Name">
                    </div>
                    <div class="col-lg-2 col-sm-2 text-end">
                        <button type="button" id="addNewDocumentReferralId" class="btn btn-secondary"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>
            </div>

            <!-- Update Button -->
            <button type="submit" class="btn btn-primary w-100 mt-4">Update Referral</button>
        </form>
    </div>
</div>






<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<?php include_once "CDN_Footer.php"; ?>

<script>
    $(document).ready(function() {
        // Export to Excel
        $('#exportReferralExcel').on('click', function() {
            var sName = $('#filterReferralName').val();
            window.location.href = 'ExportPDFExcel/ReferralExportController.php?export=excel&name='+sName;
        });

        // Export to PDF
        $('#exportReferralPDF').on('click', function() {
            var sName = $('#filterReferralName').val();
            window.location.href = 'ExportPDFExcel/ReferralExportController.php?export=pdf&name='+sName;
        });
    });
</script>

<script src="controller/referralUserController.js"></script>