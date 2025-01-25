$(document).ready(function () {
    const totalRevenueElement = $("#totalRevenue");
    const totalPaymentDoneElement = $("#totalPaymentDone");
    const totalPenaltyElement = $("#totalPenalty");
    const totalReferralElement = $("#totalReferral");
    const totalUsersElement = $("#totalUsers");
    const monthlyRevenueBody = $("#monthlyRevenueBody");

    // Function to fetch revenue data based on date range
    function fetchRevenueData(fromDate, toDate) {
        $.getJSON("ajaxFile/ajaxMIS.php", {
            sFlag: "overallMISReport",
            fromDate: fromDate,
            toDate: toDate
        }, function (data) {
            if (data.status === "success") {
                var iTotalRevenue = 0.0;
                var iTotalPaymentDone = 0.0;
                var iTotalPenalty = 0.0;
                var iTotalReferral = 0.0;

                // Clear previous rows
                monthlyRevenueBody.empty();

                // Populate monthly breakdown
                $.each(data.data.monthlyBreakdown, function (index, item) {
                    monthlyRevenueBody.append(`
                        <tr>
                            <td>${item.month}</td>
                            <td>${formatAmount(item.total_Payment)}</td>
                            <td>${formatAmount(item.total_interest_paid)}</td>
                            <td>${formatAmount(item.total_referral_amount)}</td>
                            <td>${formatAmount(item.total_penalty_amount)}</td>
                            <td>${formatAmount(item._total_net)}</td>
                            <td>${item.total_borrower}</td>
                        </tr>
                    `);

                    iTotalRevenue += parseFloat(item._total_net);
                    iTotalPaymentDone += parseFloat(item.total_Payment);
                    iTotalPenalty += parseFloat(item.total_penalty_amount);
                    iTotalReferral += parseFloat(item.total_referral_amount);
                });

                // Update total revenue, payment, penalty, referral, and total users
                totalRevenueElement.text(`${formatAmount(iTotalRevenue)}`);
                totalPaymentDoneElement.text(`${formatAmount(iTotalPaymentDone)}`);
                totalPenaltyElement.text(`${formatAmount(iTotalPenalty)}`);
                totalReferralElement.text(`${formatAmount(iTotalReferral)}`);
                totalUsersElement.text(data.data.totalUsers);
            } else {
                // Error handling in case of failure
                totalRevenueElement.text("Error loading data");
                totalPaymentDoneElement.text("Error loading data");
                totalPenaltyElement.text("Error loading data");
                totalReferralElement.text("Error loading data");
                totalUsersElement.text("Error loading data");
            }
        })
            .fail(function (error) {
                // In case of any AJAX error, update all fields with error
                totalRevenueElement.text("Error loading data");
                totalPaymentDoneElement.text("Error loading data");
                totalPenaltyElement.text("Error loading data");
                totalReferralElement.text("Error loading data");
                totalUsersElement.text("Error loading data");
                console.error("Error fetching revenue summary:", error);
            });
    }

    // Trigger AJAX request when the filter form is submitted
    $("#dateFilterForm").on("click", function (e) {
        e.preventDefault(); // Prevent the default form submission

        // Get the selected from date and to date values
        const fromDate = $("#fromDate").val();
        const toDate = $("#toDate").val();

        // Call the function to fetch data with the selected date range
        fetchRevenueData(fromDate, toDate);
    });

    const currentYear = new Date().getFullYear();

    // Set "From Date" to the start of the current year (January 1st)
    const defaultFromDate = new Date(`${currentYear}-01-01`);

    // Set "To Date" to the last day of the current year (December 31st)
    const defaultToDate = new Date(`${currentYear}-12-31`);

    // Set the default date range in the input fields
    $("#fromDate").val(defaultFromDate.toISOString().split('T')[0]);
    $("#toDate").val(defaultToDate.toISOString().split('T')[0]);


    // Fetch initial data for the default date range
    fetchRevenueData(defaultFromDate.toISOString().split('T')[0], defaultToDate.toISOString().split('T')[0]);

    // Export to PDF
    $("#exportPdf").click(function () {
        const fromDate = $("#fromDate").val();
        const toDate = $("#toDate").val();

        // Request to export the data as PDF
        window.location.href = `ExportPDFExcel/exportRevenueSummary.php?export=pdf&fromDate=${fromDate}&toDate=${toDate}`;
    });

    // Export to Excel
    $("#exportExcel").click(function () {
        const fromDate = $("#fromDate").val();
        const toDate = $("#toDate").val();

        // Request to export the data as Excel
        window.location.href = `ExportPDFExcel/exportRevenueSummary.php?export=excel&fromDate=${fromDate}&toDate=${toDate}`;
    });
});
