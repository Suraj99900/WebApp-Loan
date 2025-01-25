$(document).ready(function() {
    // Function to fetch data when the page is ready
    fetchData();

    function fetchData() {
        $.ajax({
            url: 'ajaxFile/ajaxMIS.php?sFlag=fetchCount',  // URL of the PHP script that will return data
            type: 'GET',  // Using GET method for fetching data
            dataType: 'json',  // Expecting a JSON response
            success: function(response) {
                // Check if the response contains the necessary data
                if (response.status == "success") {
                    console.log(response);
                    
                    // Update the UI elements with the fetched data
                    $('#totalBorrowers').text(response.data.totalBorrowers);
                    $('#totalReferralUsers').text(response.data.totalReferralUsers);

                    // Convert to number and use toFixed(2) to display as currency
                    $('#totalPayments').text(formatAmount(response.data.totalPayments));
                    $('#totalRevenue').text(formatAmount(response.data.totalRevenue));
                } else {
                    console.log("Error fetching data");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + ", " + error);
            }
        });
    }
});
