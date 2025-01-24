$(document).ready(() => {
    fetchAllReferralDetails();
    eventClick();

    $('#filterReset').on('click', function () {
        $('#filterReferralName').val(" ");
        fetchAllReferralDetails();
    });

    $('#filterSearch').on('click', function () {
        var sName = $('#filterReferralName').val();
        console.log(sName);
        fetchAllReferralDetails(sName);
    });




    $('#addReferralForm').off('submit').on('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        // Gather form data
        const formData = new FormData(this);

        // Send data to server via AJAX
        $.ajax({
            url: 'ajaxFile/ajaxReferral.php?sFlag=addReferral',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === 'success') {
                    alert('Referral added successfully!');
                    $('#addReferralOffcanvas').offcanvas('hide'); // Hide the offcanvas
                    fetchAllReferralDetails();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                alert('There was an error processing your request.');
            }
        });
    });


    $(document).on('click', '#referralUpdateId', function () {
        var referralId = $(this).data('id');

        // Fetch referral details via AJAX
        $.ajax({
            url: 'ajaxFile/ajaxReferral.php?sFlag=getReferralDetailsById',
            type: 'GET',
            data: { referral_id: referralId },
            success: function (response) {
                if (response.status === 'success') {
                    var data = response.data;
                    var referral = data.ReferralDetails;
                    var documents = data.uploadedDocuments;

                    // Populate referral details in the form
                    $('#referralId').val(referral.reference_Id);
                    $('#updateRefName').val(referral.ref_name);
                    $('#updateRefPhoneNumber').val(referral.ref_phone_number);
                    $('#updateRefPercentage').val(referral.ref_percentage);

                    // Populate existing documents dynamically
                    $('#existingFileInputsContainer').empty();
                    if (documents.length > 0) {
                        documents.forEach(function (doc) {
                            var documentRow = `
                                <div class="row align-items-end mb-3">
                                     <div class="col-md-6">
                                        <b>${doc.document_name}:</b>
                                        <a href="${doc.document_path}" target="_blank" class="text-primary">View Document</a>
                                    </div>
                                    <div class="col-lg-2 col-sm-2 text-end">
                                        <button type="button" data-id="${doc.id}" class="btn btn-danger removeDocumentBtn"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </div>
                            `;
                            $('#existingFileInputsContainer').append(documentRow);
                        });
                    }

                    // Show the update offcanvas
                    $('#updateReferralOffcanvas').offcanvas('show');
                } else {
                    alert('Failed to fetch referral details.');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                alert('There was an error fetching referral details.');
            }
        });
    });


    // Submit event for the update form
    $('#updateReferralForm').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        // Update referral details
        $.ajax({
            url: 'ajaxFile/ajaxReferral.php?sFlag=updateReferralDetails',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === 'success') {
                    alert('Referral details updated successfully.');

                    // Close the offcanvas
                    $('#updateReferralOffcanvas').offcanvas('hide');

                    // Reload the referral listing
                    fetchAllReferralDetails();
                } else {
                    alert('Failed to update referral details.');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                alert('There was an error updating referral details.');
            }
        });
    });

    $(document).on('click', '.removeDocumentBtn', function () {
        if (!confirm('Are you sure you want to delete this document?')) {
            return;
        }
        var iDocumentId = $(this).data('id');
        

        // Send AJAX request to delete the document
        $.ajax({
            url: 'ajaxFile/ajaxReferral.php?sFlag=invalidDocument',
            type: 'POST',
            data: { id: iDocumentId },
            success: function (response) {
                if (response.status === 'success') {
                    // Remove the row from the DOM
                    
                    alert(response.message);
                    // Close the offcanvas
                    $('#updateReferralOffcanvas').offcanvas('hide');
                } else {
                    alert(response.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                alert('There was an error deleting the document.');
            }
        });
    });

});



function fetchAllReferralDetails(sName = '') {
    console.log(sName);

    $.ajax({
        url: 'ajaxFile/ajaxReferral.php?sFlag=fetchAllReferrals&sName=' + sName,
        type: 'get',
        processData: false,
        contentType: false,
        success: function (response) {
            var data = response;
            if (data.status === 'success') {
                var tableData = '';

                // Loop through each referral and create rows for the table
                data.data.forEach(function (referral, index) {
                    tableData += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${referral.ref_name}</td>
                        <td>${referral.ref_phone_number}</td>
                        <td>${referral.ref_percentage}%</td>
                        <td>${moment(referral.added_on).format('MMM DD YYYY')}</td>
                        <td style="display: flex;">
                            <button class="btn btn-primary btn-sm icon-box mx-2" id="referralUpdateId" data-id="${referral.reference_Id}" title="Update Referral Details">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </td>
                    </tr>`;
                });

                // Destroy DataTable if already initialized
                if ($.fn.DataTable.isDataTable('#referralDetailsTable')) {
                    $('#referralDetailsTable').DataTable().clear().destroy();
                }

                // Update table body content
                $('#referralBodyId').html(tableData);

                // Reinitialize DataTable
                $('#referralDetailsTable').DataTable({
                    paging: true,
                    searching: false,
                    ordering: true,
                    responsive: true,
                });
            } else {
                alert('No referrals found.');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('Error:', textStatus, errorThrown);
            alert('There was an error processing your request.');
        }
    });
}


function eventClick() {
    console.log("call");

    $('#addDocumentReferralId').on('click', function () {
        const newFileInput = `
            <div class="mb-3">
                <div class="row">
                    <div class="col-lg-6 col-sm-12">
                        <label for="referralDocuments" class="form-label">Upload Document</label>
                        <input type="file" class="form-control" name="documents[]" required>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <label for="referralDocuments" class="form-label">Document Name</label>
                        <input type="text" class="form-control" name="documentName[]" required>
                    </div>
                </div>
            </div>
        `;
        $('#fileInputsContainer').append(newFileInput);
    });
}


function eventClickUpdate() {
    $('#addNewDocumentReferralId').on('click', function () {
        const newFileInput = `
            <div class="mb-3">
                <div class="row">
                    <div class="col-lg-6 col-sm-12">
                        <label for="referralDocuments" class="form-label">Upload Document</label>
                        <input type="file" class="form-control" name="documents[]" required>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <label for="referralDocuments" class="form-label">Document Name</label>
                        <input type="text" class="form-control" name="documentName[]" required>
                    </div>
                </div>
            </div>
        `;
        $('#newFileInputsContainer').append(newFileInput);
    });
}