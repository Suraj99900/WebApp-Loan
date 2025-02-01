document.addEventListener("DOMContentLoaded", function () {
    const borrowerSelect = document.getElementById("borrowerSelectId");
    const borrowerSelectOnlyPending = document.getElementById("borrowerSelectOnlyPendingId");
    const emiBodyId = document.getElementById("emiBodyId");

    $('#searchId').on('click',()=>{
        const sFilterBorrowerId = $('#borrowerSelectId').val();
        const sFilterLoanAmount = $('#filterLoanAmount').val();
        const sFilterFromDate = $('#filterFromDate').val();
        const sFilterToDate = $('#filterToDate').val();
        initializeEMITable(sFilterBorrowerId,sFilterLoanAmount,sFilterFromDate,sFilterToDate);
    });

    $('#filterRefresh').on('click',()=>{
        $('#borrowerSelectId').val('');
        $('#filterLoanAmount').val('');
        $('#filterFromDate').val('');
        $('#filterToDate').val('');
        $('#borrowerSelectOnlyPendingId').val('');
        initializeEMITable();
    });

    // Fetch borrowers and populate the dropdown
    function fetchBorrowers() {
        fetch("ajaxFile/ajaxBorrower.php?sFlag=fetchAllBorrowers")
            .then((response) => response.json())
            .then((data) => {
                if (data.status === "success" && data.data) {
                    data.data.forEach((borrower) => {
                        const option = document.createElement("option");
                        option.value = borrower.id;
                        option.textContent = borrower.name;
                        borrowerSelect.appendChild(option);
                    });
                    data.data.forEach((borrower) => {
                        const option = document.createElement("option");
                        option.value = borrower.id;
                        option.textContent = borrower.name;
                        borrowerSelectOnlyPending.appendChild(option);
                    });
                } else {
                    alert("Failed to load borrowers.");
                }
            })
            .catch((error) => {
                console.error("Error fetching borrowers:", error);
            });

        // $('#borrowerSelectId').select2();
    }

    borrowerSelect.addEventListener("change", function () {
        const borrowerId = this.value;
        if (borrowerId) {            
            initializeEMITable(borrowerId);
        } else {
            initializeEMITable();
            emiBodyId.innerHTML = `<tr><td colspan="6" class="text-center">Please select a borrower.</td></tr>`;
        }
    });

    borrowerSelectOnlyPending.addEventListener("change", function () {         
        initializeEMITable();
    });


    // Initialize
    fetchBorrowers();
    initializeEMITable();
});




function initializeEMITable(borrowerId = '',sFilterLoanAmount='',sFilterFromDate='',sFilterToDate='') {

    
    var iOnlyBorrowerId = $('#borrowerSelectOnlyPendingId').val();

    // Destroy any existing instance of DataTable
    if ($.fn.DataTable.isDataTable('#emiDetailsTable')) {
        $('#emiDetailsTable').DataTable().destroy();
    }

    // Initialize DataTable
    $('#emiDetailsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'ajaxFile/ajaxEMI.php',
            type: 'POST',
            data: {
                sFlag: 'fetchEMIListing',
                borrowerId: borrowerId,
                sFromDate: sFilterFromDate,
                sToDate: sFilterToDate,
                sLoanAmount: sFilterLoanAmount,
                sOnlyPending: iOnlyBorrowerId ? true: false,
                sOnlyBorrowerId : iOnlyBorrowerId
            }
        },
        columns: [
            { data: 'id', title: 'ID', render: (data, type, row, meta) => meta.row + 1 }, // Serial number
            {
                data: null, // Combine "name" and "unique_borrower_id"
                title: 'Borrower Name',
                render: (data) => `${data.name} <b>(${data.unique_borrower_id}) </b>` // Custom render
            },
            {
                data: 'emi_amount',
                title: 'Pending Amount (Interest/EMI)',
                render: (data) => data ? `${formatAmount(parseFloat(data))}` : 'N/A' // Format to 2 decimals
            },
            {
                data: 'principal_repaid',
                title: 'Principal Repaid',
                render: (data) => data ? `${formatAmount(parseFloat(data))}` : 'N/A' // Format to 2 decimals
            },
            {
                data: 'ending_principal',
                title: 'Outstanding Principal',
                render: (data) => data ? `${formatAmount(parseFloat(data))}` : 'N/A' // Format to 2 decimals
            },
            {
                data: 'payment_due_date',
                title: 'Payment Due Date',
                render: (data) => data ? moment(data).format('MMM DD YYYY') : 'N/A' // Format date
            },
            {
                data: 'payment_status',
                title: 'Payment Status',
                render: (data) => {
                    if (data === 'Completed') {
                        return `<span style="color: green;">${data.toUpperCase()}</span>`;
                    } else {
                        return `<span style="color: red;">${data.toUpperCase()}</span>`;
                    }
                }
            },
            {
                data: 'loan_id',
                title: 'Action',
                render: (data) => {
                    
                    return  `<button class="btn btn-danger btn-sm icon-box mx-2 delete" id="deleteId" data-id="${data}" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>`;
                }
            }
        ],
        paging: true, // Enable pagination
        searching: true, // Enable searching
        lengthChange: true, // Enable change in number of rows per page
        drawCallback: function () {
            console.log('Table Drawn. Binding event handlers.');
            eventClick(); // Ensure eventClick is called after each draw
        }
    });
}

function eventClick(){
    $('.delete').on('click',function (){
        const iLoanId = $(this).data('id'); // Get Borrower ID from button

    
        $.ajax({
            url: 'ajaxFile/ajaxEMI.php?sFlag=invalidData',
            type: 'GET',
            data: { iLoanId: iLoanId},
            success: function (response) {
                if (response.status === 'success') {
                    initializeEMITable();
                } else {
                    responsePop('Error', 'Failed to fetch loan details. Please try again.', 'error', 'ok');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                responsePop('Error', 'Error fetching loan details.', 'error', 'ok');
            }
        })
    });
}

// Call the function when needed
function fetchEMIs(borrowerId) {
    initializeEMITable(borrowerId);
}