$(document).ready(function () {

    // Fetch the Listing screen data...
    $('#filterRefresh').on('click', function () {
        $('#filterBorrowerName').val("");
        $('#filterLoanAmount').val("");
        $('#filterLoanDate').val("");
        fetchAllBorrowerDetails("");
    });
    $('#searchId').on('click', () => {
        const nameFilter = $('#filterBorrowerName').val();
        const amountFilter = $('#filterLoanAmount').val();
        const dateFilter = $('#filterLoanDate').val();
        fetchAllBorrowerDetails(nameFilter, amountFilter, dateFilter);
    });




    fetchAllBorrowerDetails();
    eventFunction();

    $(document).on('click', '#borrowerViewId', function () {
        const borrowerId = $(this).data('id');

        // Fetch and display borrower details in the view offcanvas
        $.ajax({
            url: 'ajaxFile/ajaxBorrower.php?sFlag=fetchBorrowerById',
            type: 'get',
            data: { id: borrowerId },
            success: function (response) {
                if (response.status === 'success') {
                    const borrower = response.data.borrowerDetails; // Main borrower details
                    const documents = response.data.uploadedDocuments; // Uploaded documents array

                    // Construct Borrower Details in Rows
                    let content = `
                        <h5 class="mt-2">Borrower Information</h5>
                        <div class="row g-4 mb-4 p-2 shadow-sm">
                            <div class="col-md-6"><b>Name:</b> ${borrower.name}</div>
                            <div class="col-md-6"><b>Phone:</b> ${borrower.phone_no}</div>
                            <div class="col-md-6"><b>Email:</b> ${borrower.email}</div>
                            <div class="col-md-6"><b>Gender:</b> ${borrower.gender}</div>
                            <div class="col-md-12"><b>Address:</b> ${borrower.address}</div>
                        </div>
    
                        <h5 class="mt-2">Loan Information</h5>
                        <div class="row g-4 mb-4 p-2 shadow-sm">
                            <div class="col-md-6"><b>Principal Amount:</b> ${formatAmount(borrower.principal_amount) || '-'}</div>
                            <div class="col-md-6"><b>Interest Rate:</b> ${borrower.interest_rate || '-'}%</div>
                            <div class="col-md-6"><b>EMI Amount:</b> ${formatAmount(borrower.EMI_amount) || '-'}</div>
                            <div class="col-md-6"><b>Loan Period:</b> ${borrower.loan_period || '-'} months</div>
                            <div class="col-md-6"><b>Disbursed Date:</b> ${borrower.disbursed_date || '-'}</div>
                            <div class="col-md-6"><b>Closure Date:</b> ${borrower.closure_date || '-'}</div>
                            <div class="col-md-6"><b>Loan Status:</b> ${borrower.loan_status || '-'}</div>
                        </div>
    
                        <h5 class="mt-2">Referral Information</h5>
                        <div class="row g-4 mb-4 p-2 shadow-sm">
                            <div class="col-md-6"><b>Referred By:</b> ${borrower.ref_name || '-'}</div>
                            <div class="col-md-6"><b>Referral Percentage:</b> ${borrower.ref_percentage || '-'}%</div>
                            <div class="col-md-6"><b>Referral Phone:</b> ${borrower.ref_phone_number || '-'}</div>
                        </div>
    
                        <h5 class="mt-2">Uploaded Documents</h5>
                        ${documents.length > 0 ? `
                            <div class="row g-4 mb-4 p-2 shadow-sm">
                                ${documents.map(doc => `
                                    <div class="col-md-6">
                                        <b>${doc.document_name}:</b>
                                        <a href="${doc.document_path}" target="_blank" class="text-primary">View Document</a>
                                    </div>
                                `).join('')}
                            </div>
                        ` : `<p class="text-danger">No documents uploaded.</p>`}
                    `;

                    // Load Content and Show Offcanvas
                    $('#borrowerViewContent').html(content);
                    const offcanvas = new bootstrap.Offcanvas($('#viewBorrowerOffcanvas'));
                    offcanvas.show();
                } else {
                    $('#borrowerViewContent').html('<p class="text-danger">Failed to load borrower details.</p>');
                }
            },
            error: function () {
                $('#borrowerViewContent').html('<p class="text-danger">An error occurred while fetching the details.</p>');
            }
        });
    });




    $(document).on('click', '#borrowerUpdateId', function () {
        const borrowerId = $(this).data('id');

        // Fetch borrower details to populate the update form
        $.ajax({
            url: 'ajaxFile/ajaxBorrower.php?sFlag=fetchBorrowerById',
            type: 'get',
            data: { id: borrowerId },
            success: function (response) {
                if (response.status === 'success') {
                    const borrower = response.data.borrowerDetails; // Main borrower details
                    const documents = response.data.uploadedDocuments; // Uploaded documents array

                    // Build the update form in a row-wise layout
                    let formContent = `
                        <div class="row g-4 p-2 mb-4 shadow-sm">
                            <div class="col-md-6">
                                <label for="borrowerName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="borrowerName" name="name" value="${borrower.name}" required>
                                <input type="hidden" class="form-control" id="borrowerId" name="id" value="${borrower.id}">
                            </div>
                            <div class="col-md-6">
                                <label for="borrowerPhone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="borrowerPhone" name="phone_no" value="${borrower.phone_no}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="borrowerEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="borrowerEmail" name="email" value="${borrower.email}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="borrowerAddress" class="form-label">Address</label>
                                <textarea class="form-control" id="borrowerAddress" name="address" required>${borrower.address}</textarea>
                            </div>
                        </div>
                        
                        <h5 class="mt-2">Uploaded Documents</h5>
                        <div class="row g-4 p-2 mb-4 shadow-sm" id="documentsList">
                            ${documents.length > 0 ? documents.map(doc => `
                                <div class="col-md-6 document-item" data-doc-id="${doc.id}">
                                    <label class="form-label">${doc.document_name}</label>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="${doc.document_path}" target="_blank" class="text-primary">View Document</a>
                                        <button type="button" class="btn btn-danger btn-sm delete-document" data-id="${doc.id}">Delete</button>
                                    </div>
                                </div>
                            `).join('') : '<p class="text-danger">No documents uploaded.</p>'}
                        </div>
                        
                        <h5 class="mt-2">Upload New Documents</h5>
                        <div class="row g-4 p-2 mb-4 shadow-sm mt-2" id="fileInputsContainerForUpdate">
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
                        <div class="row mb-4">
                            <div class="col-1">
                                <button type="button" id="addDocumentForUpdateBtn" class="btn btn-secondary" title="Add Another Document"><i class="fa-solid fa-plus"></i></button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary ">Update Borrower</button>
                            </div>
                        </div>
    
                    `;

                    $('#updateBorrowerForm').html(formContent);

                    // Show the offcanvas
                    const offcanvas = new bootstrap.Offcanvas($('#updateBorrowerOffcanvas'));
                    offcanvas.show();

                    $('#addDocumentForUpdateBtn').on('click', function () {
                        const newFileInput = `
                            <div class="mb-3">
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
                        `;
                        $('#fileInputsContainerForUpdate').append(newFileInput);
                    });
                } else {
                    alert('Failed to load borrower details.');
                }
            }
        });
    });


    // Handle document deletion
    $(document).on('click', '.delete-document', function () {
        const documentId = $(this).data('id');
        if (confirm('Are you sure you want to delete this document?')) {
            $.ajax({
                url: 'ajaxFile/ajaxBorrower.php?sFlag=invalidDocument',
                type: 'get',
                data: { id: documentId },
                success: function (response) {
                    if (response.status === 'success') {
                        $(`.document-item[data-doc-id="${documentId}"]`).remove(); // Remove document from UI
                        alert('Document deleted successfully.');
                    } else {
                        alert('Failed to delete document.');
                    }
                }
            });
        }
    });

    // Handle borrower update form submission
    $('#updateBorrowerForm').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('sFlag', 'updateBorrower');

        $.ajax({
            url: 'ajaxFile/ajaxBorrower.php?sFlag=updateBorrower',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === 'success') {
                    alert('Borrower updated successfully.');
                    location.reload(); // Reload page or refresh table
                } else {
                    alert('Failed to update borrower.');
                }
            }
        });
    });


    $(document).on('click', '#borrowerAddLoanId', function () {
        const borrowerId = $(this).data('id');
        console.log(borrowerId);

        // Populate the hidden input with the borrower ID
        $('#hiddenBorrowerId').val(borrowerId);

        // Show the offcanvas
        const offcanvas = new bootstrap.Offcanvas($('#addLoanOffcanvas'));
        offcanvas.show();
    });


    // Handle form submission
    $('#addLoanForm').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize(); // Serialize form data

        $.ajax({
            url: 'ajaxFile/ajaxLoan.php?sFlag=addLoanDetails',
            type: 'post',
            data: formData,
            success: function (response) {
                const data = response;

                if (data.status === 'success') {
                    setTimeout(() => {
                        fetchAllBorrowerDetails();
                    }, 1000);
                    alert('Loan details successfully added.');
                    $('#addLoanOffcanvas').offcanvas('hide'); // Close the offcanvas

                } else {
                    alert(data.message || 'Failed to add loan details.');
                }
            },
            error: function () {
                alert('An error occurred while processing the request.');
            }
        });
    });

    // Handle "Add Referral Details" button click
    $(document).on('click', '#borrowerAddReferralId', function () {
        const borrowerId = $(this).data('id');

        // Populate the hidden input with the borrower ID
        $('#hiddenReferralBorrowerId').val(borrowerId);

        // Fetch referral options dynamically
        $.ajax({
            url: 'ajaxFile/ajaxReferral.php?sFlag=fetchAllReferrals',
            type: 'get',
            success: function (response) {
                const data = response;

                if (data.status === 'success') {
                    let options = '<option value="">-- Select Referral --</option>';
                    data.data.forEach(function (referral) {
                        options += `<option value="${referral.reference_Id}">${referral.ref_name} (${referral.ref_phone_number})</option>`;
                    });

                    $('#referralSelect').html(options);
                } else {
                    alert('No referrals found. Please add referrals first.');
                    $('#referralSelect').html('<option value="">-- No Referrals Available --</option>');
                }
            },
            error: function () {
                alert('Failed to fetch referrals.');
            }
        });

        // Show the offcanvas
        const offcanvas = new bootstrap.Offcanvas($('#mapReferralOffcanvas'));
        offcanvas.show();
    });

    // Handle form submission for mapping referral to borrower
    $('#mapReferralForm').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize(); // Serialize form data for submission

        $.ajax({
            url: 'ajaxFile/ajaxBorrower.php?sFlag=mapReferralToBorrower',
            type: 'post',
            data: formData,
            success: function (response) {
                const data = response;

                if (data.status === 'success') {
                    alert('Referral successfully mapped to borrower.');
                    $('#mapReferralOffcanvas').offcanvas('hide'); // Close offcanvas
                    fetchAllBorrowerDetails();
                    // Optionally, reload the table or update UI
                } else {
                    alert(data.message || 'Failed to map referral to borrower.');
                }
            },
            error: function () {
                alert('An error occurred while processing the request.');
            }
        });
    });

    // Handle "Add/Update Referral Details" button click
    $(document).on('click', ' #borrowerUpdateReferralId', function () {
        const borrowerId = $(this).data('id');

        // Populate the hidden input with the borrower ID
        $('#hiddenReferralBorrowerUpdateId').val(borrowerId);

        // Fetch referral options dynamically
        $.ajax({
            url: 'ajaxFile/ajaxReferral.php?sFlag=fetchAllReferrals',
            type: 'get',
            success: function (response) {
                const data = response;

                if (data.status === 'success') {
                    let options = '<option value="">-- Select Referral --</option>';
                    data.data.forEach(function (referral) {
                        options += `<option value="${referral.reference_Id}">${referral.ref_name} (${referral.ref_phone_number})</option>`;
                    });

                    $('#referralUpdateSelect').html(options);

                } else {
                    alert('No referrals found. Please add referrals first.');
                    $('#referralUpdateSelect').html('<option value="">-- No Referrals Available --</option>');
                }
            },
            error: function () {
                alert('Failed to fetch referrals.');
            }
        });

        // Show the offcanvas
        const offcanvas = new bootstrap.Offcanvas($('#mapReferralUpdateOffcanvas'));
        offcanvas.show();
    });

    // Handle form submission for mapping or updating referral to borrower
    $('#mapReferralUpdateForm').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize(); // Serialize form data for submission

        $.ajax({
            url: 'ajaxFile/ajaxBorrower.php?sFlag=updateMapReferralToBorrower',
            type: 'post',
            data: formData,
            success: function (response) {
                const data = response;

                if (data.status === 'success') {
                    alert('Referral successfully mapped/updated for borrower.');
                    $('#mapReferralUpdateOffcanvas').offcanvas('hide'); // Close the offcanvas
                    // Optionally, reload the borrower table or update UI dynamically
                    fetchAllBorrowerDetails();
                } else {
                    alert(data.message || 'Failed to map/update referral for borrower.');
                }
            },
            error: function () {
                alert('An error occurred while processing the request.');
            }
        });
    });






    $('#addBorrowerForm').on('submit', function (event) {
        event.preventDefault();

        var formData = new FormData(this);
        formData.append('sFlag', 'addBorrower'); // Append the flag to identify the request

        $.ajax({
            url: 'ajaxFile/ajaxBorrower.php',
            type: 'POST',
            data: formData,
            processData: false, // Important for file upload
            contentType: false, // Important for file upload
            success: function (response) {
                var data = response;
                if (data.status == 'success') {
                    fetchAllBorrowerDetails();
                    alert('Borrower added successfully!');
                    // Close the offcanvas
                    $('#AddBorrowerOffCanvasId').offcanvas('hide');
                    // Optionally, reset the form
                    $('#addBorrowerForm')[0].reset();
                } else {
                    alert('Failed to add borrower: ' + data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                alert('There was an error processing your request.');
            }
        });
    });




});

function eventFunction() {

    $('#addDocumentBtn,#addDocumentForUpdateBtn').on('click', function () {
        const newFileInput = `
            <div class="mb-3">
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
        `;
        $('#fileInputsContainer,#fileInputsContainerForUpdate').append(newFileInput);
    });
}


function eventClick() {
    $('.loanAgreementClass').on('click', function () {
        // Use 'this' to refer to the clicked element
        const borrowerId = $(this).data('id');
        console.log('Borrower ID:', borrowerId);
        console.log($(this).data());

        $.ajax({
            url: 'ajaxFile/loanAgreementPDF.php?borrower_id=' + borrowerId,
            xhrFields: {
                responseType: 'blob', // Set response type to blob for PDF
            },
            success: function (blob) {
                console.log('Blob received:', blob);
                if (!(blob instanceof Blob)) {
                    console.error('Error: Response is not a Blob object');
                    return;
                }

                // Create a link to download the PDF
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'Loan_Agreement.pdf';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url); // Clean up
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
}


function fetchAllBorrowerDetails(sName = '', sAmount = '', sDate = '') {

    $.ajax({
        url: 'ajaxFile/ajaxBorrower.php?sFlag=fetchAllBorrowers&name=' + sName + '&amount=' + sAmount + '&date=' + sDate,
        type: 'get',
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.status === 'success') {
                const data = response.data;
                const tableSelector = '#borrowerDetailsTable';
                const dataTable = $(tableSelector).DataTable();

                // Clear existing data in the DataTable
                dataTable.clear();

                // Loop through the data and add rows
                data.forEach(function (borrower, index) {
                    const hasLoanDetails = borrower.loan_status && borrower.loan_status !== 'inactive';
                    const hasReferralDetails = borrower.ref_name || borrower.ref_percentage;

                    dataTable.row.add([
                        index + 1,
                        borrower.name,
                        borrower.phone_no,
                        borrower.email,
                        borrower.principal_amount || '-',
                        borrower.disbursed_date || '',
                        borrower.closure_date || '',
                        borrower.loan_status || 'inactive',
                        `
                            <div style="display: flex;">
                                <button class="btn btn-primary btn-sm icon-box mx-2" id="borrowerViewId" data-id="${borrower.id}" title="View Borrower Details">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                                <button class="btn btn-primary btn-sm icon-box mx-2" id="borrowerUpdateId" data-id="${borrower.id}" title="Update Borrower Details">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                
                                <button class="btn btn-primary btn-sm icon-box mx-2 ${borrower.bIsPaid == 1 ? 'd-none' : ''}"  id="${hasLoanDetails ? 'borrowerUpdateLoanId' : 'borrowerAddLoanId'}" data-id="${borrower.id}" data-loan-id="${borrower.loan_id}" title="${hasLoanDetails ? 'Update Loan Details' : 'Add Loan Details'}">
                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                </button>
                                <button class="btn btn-primary btn-sm icon-box mx-2" id="${hasReferralDetails ? 'borrowerUpdateReferralId' : 'borrowerAddReferralId'}" data-id="${borrower.id}" title="${hasReferralDetails ? 'Update Referral Details' : 'Add Referral Details'}">
                                    <i class="fa-solid fa-people-arrows"></i>
                                </button>

                                <button class="btn btn-primary btn-sm icon-box mx-2 loanAgreementClass  ${!hasLoanDetails ? 'd-none' : ''} " id="loanAgreementId" data-id="${borrower.id}" title="Export Agreement PDF">
                                   <i class="fa-solid fa-file-contract"></i>
                                </button>
                            </div>
                        `,
                    ]);
                });

                // Redraw the table with the updated data
                dataTable.draw();
                eventClick();
            } else {
                alert('No borrowers found');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('Error:', textStatus, errorThrown);
            alert('There was an error processing your request.');
        },
    });
}

// Initialize the DataTable only once when the page loads
$(document).ready(function () {
    $('#borrowerDetailsTable').DataTable({
        paging: true,
        searching: false,
        ordering: true,
        responsive: true,
    });
});



$(document).on('click', '#borrowerUpdateLoanId', function () {
    const borrowerId = $(this).data('id'); // Get Borrower ID from button
    const iLoanId = $(this).data('loan-id');
    $('#hiddenUpdateBorrowerId').val(borrowerId);
    $('#hiddenLoanId').val(iLoanId);

    // Fetch existing loan details for the borrower via AJAX
    $.ajax({
        url: 'ajaxFile/ajaxLoan.php?sFlag=fetchLoanDetails',
        type: 'GET',
        data: { borrowerId: borrowerId, 'LoanId': iLoanId },
        success: function (response) {
            if (response.status === 'success') {
                const loanDetails = response.data;

                // Populate the form with the fetched loan details
                $('#principalAmountUpdate').val(loanDetails.principal_amount);
                $('#interestRateUpdate').val(loanDetails.interest_rate);
                $('#interestRateValueUpdate').text(loanDetails.interest_rate);
                $('#loanPeriodUpdate').val(loanDetails.loan_period);
                $('#loanPeriodValueUpdate').text(loanDetails.loan_period);
                $('#disbursedDateUpdate').val(loanDetails.disbursed_date);
                $('#closureDateUpdate').val(loanDetails.closure_date);
                calculateUpdatedEMI();

                // Show the offcanvas
                const offcanvas = new bootstrap.Offcanvas($('#updateLoanOffcanvas'));
                offcanvas.show();
            } else {
                alert('Failed to fetch loan details. Please try again.');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('Error:', textStatus, errorThrown);
            alert('Error fetching loan details.');
        }
    });
});

// Handle the loan update form submission
$('#updateLoanForm').on('submit', function (e) {
    e.preventDefault(); // Prevent default form submission

    const formData = $(this).serialize(); // Serialize form data

    // Send the updated loan details via AJAX
    $.ajax({
        url: 'ajaxFile/ajaxLoan.php?sFlag=updateLoanDetails', // Replace with the correct endpoint
        type: 'POST',
        data: formData,
        success: function (response) {
            if (response.status === 'success') {
                alert('Loan details updated successfully!');
                location.reload(); // Reload the page to reflect changes (optional)
            } else {
                alert('Failed to update loan details. Please try again.');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('Error:', textStatus, errorThrown);
            alert('Error updating loan details.');
        }
    });
});

// Auto-calculate closure date when loan period or disbursed date changes
$(document).on('change', '#loanPeriod, #disbursedDate,#loanPeriodUpdate,#disbursedDateUpdate', function () {
    const loanPeriod = parseInt($('#loanPeriod').val()); // Loan period in months
    const disbursedDate = $('#disbursedDate').val(); // Disbursed date as a string

    if (loanPeriod && disbursedDate) {
        // Convert the disbursed date to a Date object
        const disbursedDateObj = new Date(disbursedDate);

        // Add loan period (in months) to the disbursed date
        disbursedDateObj.setMonth(disbursedDateObj.getMonth() + loanPeriod);

        // Format the closure date as yyyy-mm-dd
        const closureDate = disbursedDateObj.toISOString().split('T')[0];

        // Set the closure date input field
        $('#closureDate').val(closureDate);
    } else {
        $('#closureDate').val(''); // Clear the closure date if inputs are invalid
    }
});
$(document).on('change', '#loanPeriodUpdate,#disbursedDateUpdate', function () {
    const loanPeriod = parseInt($('#loanPeriodUpdate').val()); // Loan period in months
    const disbursedDate = $('#disbursedDateUpdate').val(); // Disbursed date as a string

    if (loanPeriod && disbursedDate) {
        // Convert the disbursed date to a Date object
        const disbursedDateObj = new Date(disbursedDate);

        // Add loan period (in months) to the disbursed date
        disbursedDateObj.setMonth(disbursedDateObj.getMonth() + loanPeriod);

        // Format the closure date as yyyy-mm-dd
        const closureDate = disbursedDateObj.toISOString().split('T')[0];

        // Set the closure date input field
        $('#closureDateUpdate').val(closureDate);
    } else {
        $('#closureDateUpdate').val(''); // Clear the closure date if inputs are invalid
    }
});


// Update Loan Period Value
$('#loanPeriodUpdate').on('input', function () {
    const periodValue = $(this).val();
    $('#loanPeriodValueUpdate').text(periodValue);

    calculateUpdatedEMI();
});

// Update Interest Rate Value
$('#interestRateUpdate').on('input', function () {
    const interestValue = $(this).val();
    $('#interestRateValueUpdate').text(interestValue);

    calculateUpdatedEMI();
});

// Update Principal Amount
$('#principalAmountUpdate').on('input', function () {
    const principalValue = $(this).val();

    calculateUpdatedEMI();
});

// EMI Calculation Function
function calculateUpdatedEMI() {
    const principal = parseFloat($('#principalAmountUpdate').val()) || 0;
    const rate = parseFloat($('#interestRateUpdate').val()) || 0;
    const period = parseInt($('#loanPeriodUpdate').val()) || 0;
    $('#emiPeriodUpdate').text(period);
    $('#emiInterestUpdate').text(rate);
    $('#emiPrincipalUpdate').text(formatAmount(principal));

    if (principal > 0 && rate > 0 && period > 0) {
        const monthlyRate = rate / (12 * 100);
        const emi = (principal * monthlyRate * Math.pow(1 + monthlyRate, period)) / (Math.pow(1 + monthlyRate, period) - 1);
        $('#emiAmountUpdate').text(formatAmount(emi.toFixed(2)));
    } else {
        $('#emiAmountUpdate').text(0);
    }
}

// Initialize EMI on Load
calculateUpdatedEMI();