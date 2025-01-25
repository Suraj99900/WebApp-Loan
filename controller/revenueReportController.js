$(document).ready(function () {
    // Select elements
    const revenueTableBody = $('#revenueBodyId');

    $('#exportToPDF').on('click',()=>{
        const borrowerSelect = $('#borrowerSelectId').val();
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        const loanStatus = $('#loanStatus').val();
        const principalAmount = $('#principalAmount').val();

        window.location.href = 'ExportPDFExcel/exportMISRevenueReport.php?export=pdf&borrowerId=' + borrowerSelect + '&sFromDate=' + startDate + '&sToDate=' + endDate + '&sLoanAmount=' + loanStatus+'&principalAmount='+principalAmount;
    });

    $('#exportToExcel').on('click',()=>{
        const borrowerSelect = $('#borrowerSelectId').val();
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        const loanStatus = $('#loanStatus').val();
        const principalAmount = $('#principalAmount').val();

        window.location.href = 'ExportPDFExcel/exportMISRevenueReport.php?export=excel&borrowerId=' + borrowerSelect + '&sFromDate=' + startDate + '&sToDate=' + endDate + '&sLoanAmount=' + loanStatus+'&principalAmount='+principalAmount;
    });

    $('#borrowerSelectId').select2({
        placeholder: 'Select Borrower',
        allowClear: true,
        // dropdownParent: $('#addPaymentOffcanvas'),
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

    $('#searchId').on('click', () => {
        const borrowerSelect = $('#borrowerSelectId').val();
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        const loanStatus = $('#loanStatus').val();
        const principalAmount = $('#principalAmount').val();
        
        fetchRevenueReport(borrowerSelect,startDate,endDate,loanStatus,principalAmount)
    });

    $("#resetId").on('click',()=>{
        $('#borrowerSelectId').val("");
        $('#startDate').val("");
        $('#endDate').val("");
        $('#loanStatus').val("");
        $('#principalAmount').val("");
        $('#revenueBodyId').val("");
        fetchRevenueReport();
    });

    // Initialize DataTable
    const revenueTable = $('#revenueDetailsTable').DataTable({
        "searching": true,   // Enable search
        "paging": true,      // Enable pagination
        "info": true,        // Show table info
        "ordering": true,    // Enable column sorting
        "lengthChange": true // Allow changing the number of entries per page
    });

    // Function to fetch the revenue report data
    function fetchRevenueReport(borrowerId = '',start = '',end='',status='',principal='') {


        // Perform AJAX request using jQuery
        $.ajax({
            url: 'ajaxFile/ajaxMIS.php?sFlag=fetchLegerReport&borrowerId='+borrowerId+'&startDate='+start+'&endDate='+end+'&status='+status+'&principalAmount='+principal,
            method: 'get',
            success: function (responseData) {
                if (responseData.status === 'success') {
                    renderRevenueTable(responseData.data);
                } else {
                    alert(responseData.message);
                }
            },
            error: function (error) {
                console.error('Error:', error);
            }
        });
    }

    // Function to render revenue data into table
    function renderRevenueTable(data) {
        revenueTable.clear(); // Clear existing table data

        if (data.length === 0) {
            revenueTable.row.add([
                'No data available', '', '', '', '', '', '', '', '', '', ''
            ]).draw();
            return;
        }

        // Function to format amount with rounding and commas
        function formatAmount(amount) {
            let roundedAmount = Math.round(amount);
            return "₹" + roundedAmount.toLocaleString(); // Format number with commas
        }

        // Populate table with new data
        $.each(data, function (index, item) {
            let statusColor = item.loan_status === 'done' ? 'green' : (item.loan_status === 'active' ? 'red' : 'black');

            revenueTable.row.add([
                index + 1,
                item.name+" <b>("+item.unique_borrower_id+")</b>",
                formatAmount(item.total_interest),
                formatAmount(item.penalty_income),
                formatAmount(item.referral_expense),
                formatAmount(item.net_revenue),
                item.emi_count ? item.emi_count : 0,
                formatAmount(item.outstanding_principal),
                formatAmount(item.total_paid_by_borrower),

                `<span style="color: ${statusColor};">${item.loan_status}</span>`,
                item.revenue_report_date
            ]).draw();
        });
    }



    // Initially fetch revenue report
    fetchRevenueReport();
});
