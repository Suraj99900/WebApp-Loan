

$(document).ready(function () {


    $('#filterSearch').on('click', () => {
        var name = $('#filterBorrowerName').val();
        var paymentReceivedDate = $('#filterPaymentDate').val();
        var paymentMode = $('#filterPaymentMode').val();
        fetchLoanDetails(name,paymentReceivedDate,paymentMode);
    });

    $('#filterReset').on('click', () => {
        $('#filterBorrowerName').val("");
        $('#filterPaymentDate').val("");
        $('#filterPaymentMode').val("");
        fetchLoanDetails();
    });

    fetchLoanDetails();
    // Initialize Select2 with AJAX
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
                console.log(data.data);

                return {
                    results: data.data.map(function (item) {
                        console.log(item);

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

        $.ajax({
            url: 'ajaxFile/ajaxPayment.php?sFlag=fetchPaymentDetails',
            type: 'GET',
            data: { borrower_id: borrowerId },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    const paymentData = response.data[0];

                    // Populate the offcanvas form fields
                    $('#loanId').val(paymentData.loan_id || '');
                    $('#paymentAmount').val(paymentData.EMI_amount || '');
                    $('#penaltyAmount').val(paymentData.penalty_amount || 0);
                    $('#referralShare').val(paymentData.refShare || 0);
                    $('#interestAmountId').val(paymentData.interest_amount || '');
                    $('#paymentDueDateId').val(paymentData.payment_due_date || '');
                    $('#principalRepaidId').val(paymentData.principal_repaid || '');
                } else {
                    alert(response.message || 'No payment details found.');
                }
            },
            error: function () {
                alert('An error occurred while fetching payment details.');
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




function fetchLoanDetails(sName = '',dPaymentReceivedDate = '',sPaymentMode= '') {

    if ($.fn.DataTable.isDataTable('#paymentDetailsTable')) {
        // Destroy the existing DataTable instance
        $('#paymentDetailsTable').DataTable().clear().destroy();
    }

    $('#paymentDetailsTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: 'ajaxFile/ajaxPayment.php?sFlag=fetchPaymentData', // Replace with the actual URL of your server-side handler
            method: 'POST',
            data: function (d) {
                d.action = 'fetchPaymentData',
                d.name = sName
                d.paymentReceivedDate = dPaymentReceivedDate
                d.paymentMode = sPaymentMode
            }, // Send loan_id or other parameters
            dataSrc: function (json) {
                var returnData = new Array();
                for (var i = 0; i < json.data.length; i++) {
                    returnData.push([
                        i + 1, // Sr.no
                        json.data[i].name,
                        json.data[i].payment_amount,
                        json.data[i].penalty_amount,
                        json.data[i].referral_share_amount,
                        json.data[i].mode_of_payment,
                        json.data[i].received_date,
                        json.data[i].interest_date,
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
        "responsive": true
    });

}