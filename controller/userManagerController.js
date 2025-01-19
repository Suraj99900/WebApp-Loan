$(document).ready(() => {


    // Initialize DataTable
    const userTable = $('#userDetailsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'ajaxFile/ajaxUserManage.php?sFlag=fetch',
            data: function (d) {
                // Add custom filters
                d.userName = $('#filterUserName').val();
                d.email = $('#filterEmail').val();
                d.userType = $('#filterUserType').val();
            },
        },
        columns: [
            {
                data: null, // No direct data source for Sr. No
                title: 'Sr. No',
                orderable: false,
                render: function (data, type, row, meta) {
                    // meta.row gives the current row index, add 1 for Sr. No.
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
            },
            { data: 'user_id', title: 'User ID' },
            { data: 'staff_name', title: 'User Name' },
            { data: 'email', title: 'Email' },
            { data: 'phone', title: 'Phone' },
            {
                data: 'user_type',
                title: 'User Type',
                orderable: false,
                render: function (data, type, row) {
                    return data == 1 ? 'Admin' : 'Recovery User';

                }
            },
            {
                data: null,
                title: 'Actions',
                orderable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary edit-user" data-id="${row.id}">Edit</button>
                        <button class="btn btn-sm btn-danger delete-user" data-id="${row.id}">Delete</button>
                    `;
                },
            },
        ],
        order: [[0, 'asc']], // Sort by Sr. No
    });

    // Fetch Data on Filter Change
    $('#searchUser').on('click', () => {
        userTable.ajax.reload();
    });

    // Reset Filters
    $('#resetFilters').on('click', () => {
        $('#filterUserName').val('');
        $('#filterEmail').val('');
        $('#filterUserType').val('');
        userTable.ajax.reload();
    });


    $('#userDetailsTable tbody').on('click', '.delete-user', function () {
        const userId = $(this).data('id');
        if (confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                url: 'ajaxFile/ajaxUserManage.php?sFlag=delete',
                data: { user_id: userId },
                success: function (response) {
                    alert(response.message || 'User deleted successfully.');
                    userTable.ajax.reload();
                },
                error: function () {
                    alert('An error occurred while deleting the user.');
                },
            });
        }
    });

    $('#userForm').on('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission
        addUser(); // Call the addUser function
    });

    $('#updateUserForm').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: 'ajaxFile/ajaxUserManage.php?sFlag=updateUser',
            type: 'POST',
            data: formData,
            success: function (response) {
                alert(response.message || 'User updated successfully.');
                $('#UpdateUserOffCanvasId').offcanvas('hide');
                $('#userDetailsTable').DataTable().ajax.reload(); // Reload the DataTable
            },
            error: function () {
                alert('An error occurred while updating the user.');
            },
        });
    });

    // Open Update Off-Canvas and Prefill Data
    $(document).on('click', '.edit-user', function () {
        const id = $(this).data('id');

        // Fetch user details using AJAX
        $.ajax({
            url: 'ajaxFile/ajaxUserManage.php?sFlag=fetchById',
            type: 'POST',
            data: { id },
            success: function (response) {
                if (response.status == 200) {
                    const user = response.data;

                    // Prefill the update form
                    $('#updateUserId').val(user.id);
                    $('#updateUserName').val(user.staff_name);
                    $('#updateEmail').val(user.email);
                    $('#updatePhone').val(user.phone);
                    $('#updateUserType').val(user.user_type);

                    // Show the off-canvas
                    $('#UpdateUserOffCanvasId').offcanvas('show');
                } else {
                    alert(response.message || 'Failed to fetch user details.');
                }
            },
            error: function () {
                alert('An error occurred while fetching user details.');
            },
        });
    });

    $(document).on('click', '.deleteUserBtn', function () {
        const userId = $(this).data('id');
    
        if (confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                url: 'ajaxFile/ajaxUserManage.php?sFlag=deleteUser',
                type: 'POST',
                data: { userId },
                success: function (response) {
                    alert(response.message || 'User deleted successfully.');
                    $('#userDetailsTable').DataTable().ajax.reload(); // Reload the DataTable
                },
                error: function () {
                    alert('An error occurred while deleting the user.');
                },
            });
        }
    });
    


    function addUser() {
        // Serialize the form data into a URL-encoded string
        const formData = $('#userForm').serialize();
        const extraData = '&isDirect='+1;
        const finalData = formData + extraData;
        
        $.ajax({
            url: 'ajaxFile/ajaxUserManage.php?sFlag=addUser', // Backend URL
            type: 'POST',
            data: finalData, // Send serialized form data
            success: function (response) {
                if (response.status == 200) {
                    alert(response.message || 'User added/updated successfully.');
                    $('#AddUserOffCanvasId').offcanvas('hide'); // Close the offcanvas
                    $('#userForm')[0].reset(); // Reset the form
                    userTable.ajax.reload(); // Reload the DataTable
                } else {
                    alert(response.message || 'Failed to add/update user.');
                }
            },
            error: function () {
                alert('An error occurred while processing the request.');
            },
        });
    }

});




