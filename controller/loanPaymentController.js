

$(document).ready(function () {


    $('#filterSearch').on('click', () => {
        var name = $('#filterBorrowerName').val();
        const sFromDateFilter = $('#filterFromDate').val();
        const sToDateFilter = $('#filterToDate').val();
        var paymentMode = $('#filterPaymentMode').val();
        fetchLoanDetails(name, sFromDateFilter,sToDateFilter, paymentMode);
    });

    $('#filterReset').on('click', () => {
        $('#filterBorrowerName').val("");
        $('#filterFromDate').val("");
        $('#filterToDate').val("");
        $('#filterPaymentMode').val("");
        fetchLoanDetails();
    });

    fetchLoanDetails();
    // Initialize Select2 with AJAX

    $('#borrowerNameForClosure').select2({
        placeholder: 'Select Borrower',
        allowClear: true,
        dropdownParent: $('#closurePartPaymentOffcanvas'), // Ensures dropdown appears within the offcanvas
        ajax: {
            url: 'ajaxFile/ajaxBorrower.php?sFlag=fetchAllBorrowers',
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    name: params.term
                };
            },
            processResults: function (data) {

                return {
                    results: data.data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };

            },
            cache: true
        },
        minimumInputLength: 1
    });

    $('#borrowerName').select2({
        placeholder: 'Select Borrower',
        allowClear: true,
        dropdownParent: $('#addPaymentOffcanvas'), // Ensures dropdown appears within the offcanvas
        ajax: {
            url: 'ajaxFile/ajaxBorrower.php?sFlag=fetchAllBorrowers',
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    name: params.term
                };
            },
            processResults: function (data) {

                return {
                    results: data.data.map(function (item) {

                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };

            },
            cache: true
        },
        minimumInputLength: 1
    });




    $('#borrowerName').on('select2:select', function (e) {
        let borrowerId = e.params.data.id;

        // Fetch payment details for the selected borrower
        $.ajax({
            url: 'ajaxFile/ajaxPayment.php?sFlag=fetchPaymentDetails',
            type: 'GET',
            data: { borrower_id: borrowerId },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    const loans = response.data; // List of loans for the selected borrower

                    if (loans.length > 1) {
                        // If the borrower has more than one loan, show the loan selection dropdown
                        let loanOptions = loans.map(loan => `
                            <option value="${loan.loan_id}">Loan ID: ${loan.loan_id} - Principal: ${formatAmount(loan.principal_amount)} - Status: ${loan.loan_status}</option>
                        `).join('');

                        // Populate the loan dropdown with multiple options
                        $('#loanSelection').html(loanOptions).parent().show();
                        $('#loanSelection').prop('required', true);
                        $('#loanSelection').trigger('change');
                    } else {

                        const loan = loans[0];
                        populateLoanDetails(loan);
                        $('#loanSelection').parent().hide();  // Hide loan selection field
                        $('#loanSelection').prop('required', false);  // Make loan selection not required
                    }
                } else {
                    alert(response.message || 'No payment details found.');
                }
            },
            error: function () {
                alert('An error occurred while fetching payment details.');
            }
        });
    });

    // Function to populate the loan details in the form
    function populateLoanDetails(loan) {
        $('#loanId').val(loan.loan_id || '');
        $('#paymentAmount').val(loan.emi_amount || '');
        $('#penaltyAmount').val(loan.penalty_amount || 0);
        $('#referralShare').val(loan.refShare || 0);
        $('#interestAmountId').val(loan.interest_amount || '');
        $('#paymentDueDateId').val(loan.payment_due_date || '');
        $('#principalRepaidId').val(loan.principal_repaid || '');
    }

    // Handle loan change when multiple loans are selected from the dropdown
    $('#loanSelection').on('change', function () {
        const loanId = $(this).val();

        // Find the selected loan details from the loans array
        $.ajax({
            url: 'ajaxFile/ajaxPayment.php?sFlag=fetchPaymentDetails',
            type: 'GET',
            data: { borrower_id: $('#borrowerName').val(), loanId: loanId },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    const selectedLoan = response.data.find(loan => loan.loan_id == loanId);


                    if (selectedLoan) {
                        // Populate the form with the selected loan's details
                        populateLoanDetails(selectedLoan);
                    }
                } else {
                    alert(response.message || 'Failed to fetch loan details.');
                }
            },
            error: function () {
                alert('An error occurred while fetching loan details.');
            }
        });
    });

    // Format amount to include commas and decimals
    function formatAmount(amount) {
        return parseFloat(amount).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }


    $('#borrowerNameForClosure').on('select2:select', function (e) {
        let borrowerId = e.params.data.id;

        $.ajax({
            url: 'ajaxFile/ajaxPayment.php?sFlag=fetchPaymentDetails',
            type: 'GET',
            data: { borrower_id: borrowerId },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    const loans = response.data; // List of loans for the selected borrower

                    if (loans.length > 1) {
                        // If the borrower has more than one loan, show the loan selection dropdown
                        let loanOptions = loans.map(loan => `
                            <option value="${loan.loan_id}">Loan ID: ${loan.loan_id} - Principal: ${formatAmount(loan.principal_amount)} - Status: ${loan.loan_status}</option>
                        `).join('');

                        // Populate the loan dropdown with multiple options
                        $('#loanSelectionForClosure').html(loanOptions).parent().show();  // Show loan selection field
                        $('#loanSelectionForClosure').prop('required', true);  // Make loan selection required

                        // Auto trigger the 'change' event after populating options
                        $('#loanSelectionForClosure').trigger('change');  // Trigger change event to populate form fields
                    } else {
                        // If only one loan, proceed with the single loan details
                        const loan = loans[0];  // Only one loan available
                        populateClosurePartPaymentDetails(loan);  // Populate the form fields with loan details
                        $('#loanSelectionForClosure').parent().hide();  // Hide loan selection field
                        $('#loanSelectionForClosure').prop('required', false);  // Make loan selection not required
                    }
                } else {
                    alert(response.message || 'No payment details found.');
                }
            },
            error: function () {
                alert('An error occurred while fetching payment details.');
            }
        });
    });

    // Function to populate the closure/part payment details in the form
    function populateClosurePartPaymentDetails(loan) {
        $('#loanIdForClosure').val(loan.loan_id || '');
        $('#closurePartPaymentAmount').val(loan.ending_principal || '');
    }

    // Handle loan change when multiple loans are selected from the dropdown
    $('#loanSelectionForClosure').on('change', function () {
        const loanId = $(this).val();

        // Find the selected loan details from the loans array
        $.ajax({
            url: 'ajaxFile/ajaxPayment.php?sFlag=fetchPaymentDetails',
            type: 'GET',
            data: { borrower_id: $('#borrowerNameForClosure').val(), loanId: loanId },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    const selectedLoan = response.data.find(loan => loan.loan_id == loanId);
                    if (selectedLoan) {
                        // Populate the form with the selected loan's details
                        populateClosurePartPaymentDetails(selectedLoan);
                    }
                } else {
                    alert(response.message || 'Failed to fetch loan details.');
                }
            },
            error: function () {
                alert('An error occurred while fetching loan details.');
            }
        });
    });


});


// Submit the Add Payment form
$('#addLoanPaymentForm').on('submit', function (e) {
    e.preventDefault();

    const formData = $(this).serialize(); // Serialize form data for AJAX request

    $.ajax({
        url: 'ajaxFile/ajaxPayment.php?sFlag=addPayment',
        method: 'POST',
        data: formData,
        success: function (response) {
            alert('Payment added successfully!');
            location.reload(); // Reload the page or update the table dynamically
        },
        error: function (error) {
            console.error('Error adding payment:', error);
        },
    });
});

$('#closurePartPaymentForm').on('submit', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();
    $.ajax({
        url: 'ajaxFile/ajaxPayment.php?sFlag=partOrCloserPayment',
        type: 'POST',
        data: formData,
        success: function (response) {
            alert(response.message || 'payment submitted successfully!');
            $('#partPaymentOffcanvas').offcanvas('hide');
            location.reload();
        },
        error: function () {
            alert('An error occurred while submitting payment.');
        }
    });
});


function fetchLoanDetails(sName = '', sFromDateFilter='',sToDateFilter='', sPaymentMode = '') {

    if ($.fn.DataTable.isDataTable('#paymentDetailsTable')) {
        // Destroy the existing DataTable instance
        $('#paymentDetailsTable').DataTable().clear().destroy();
    }

    $('#paymentDetailsTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            url: 'ajaxFile/ajaxPayment.php?sFlag=fetchPaymentData', // Replace with the actual URL of your server-side handler
            method: 'POST',
            data: function (d) {
                d.action = 'fetchPaymentData',
                d.name = sName
                d.sFromDate = sFromDateFilter
                d.sToDate = sToDateFilter
                d.paymentMode = sPaymentMode
            }, // Send loan_id or other parameters
            dataSrc: function (json) {
                var returnData = new Array();
                for (var i = 0; i < json.data.length; i++) {
                    returnData.push([
                        i + 1, // Sr.no
                        json.data[i].name,
                        formatAmount(json.data[i].payment_amount),
                        formatAmount(json.data[i].penalty_amount),
                        formatAmount(json.data[i].referral_share_amount),
                        json.data[i].mode_of_payment,
                        moment(json.data[i].received_date).format('MMM DD YYYY'),
                        moment(json.data[i].interest_date).format('MMM DD YYYY'),
                        json.data[i].payment_status
                    ]);
                }
                return returnData;
            }
        },
        "columns": [
            { "title": "Sr.no" },
            { "title": "Borrower Name" },
            { "title": "Payment Amount" },
            { "title": "Penalty" },
            { "title": "Referral Share" },
            { "title": "Payment Mode" },
            { "title": "Received Date" },
            { "title": "Due Date" },
            { "title": "Status" }
        ],
        "responsive": true,
        "pageLength": 10,
        
    });

}