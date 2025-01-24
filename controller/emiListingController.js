document.addEventListener("DOMContentLoaded", function () {
    const borrowerSelect = document.getElementById("borrowerSelectId");
    const emiBodyId = document.getElementById("emiBodyId");

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


    // Initialize
    fetchBorrowers();
    initializeEMITable();
});




function initializeEMITable(borrowerId = '') {

    
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
                borrowerId: borrowerId
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
                render: (data) => data ? new Date(data).toLocaleDateString('en-IN') : 'N/A' // Format date
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
            }
        ],
        paging: true, // Enable pagination
        searching: true, // Enable searching
        lengthChange: true, // Enable change in number of rows per page
    });
}

// Call the function when needed
function fetchEMIs(borrowerId) {
    initializeEMITable(borrowerId);
}