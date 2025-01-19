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
            emiBodyId.innerHTML = `<tr><td colspan="6" class="text-center">Please select a borrower.</td></tr>`;
        }
    });


    // Initialize
    fetchBorrowers();
});




function initializeEMITable(borrowerId) {

    
    // Destroy any existing instance of DataTable
    if ($.fn.DataTable.isDataTable('#emiDetailsTable')) {
        $('#emiDetailsTable').DataTable().destroy();
    }

    // Initialize DataTable
    $('#emiDetailsTable').DataTable({
        processing: true,
        serverSide: true,
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
            { data: 'EMI_amount', title: 'EMI Amount' },
            { data: 'principal_repaid', title: 'Principal Repaid' },
            { data: 'interest_amount', title: 'Interest Amount' },
            { data: 'ending_principal', title: 'Ending Principal' },
            { data: 'payment_due_date', title: 'Payment Due Date' },
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
        order: [[5, 'asc']] // Default sort by Payment Due Date
    });
}

// Call the function when needed
function fetchEMIs(borrowerId) {
    initializeEMITable(borrowerId);
}