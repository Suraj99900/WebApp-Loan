$(document).ready(() => {
    // Handling the login button click event
    $('#idLoginForm').on('submit', (event) => {
        event.preventDefault();
        login();
    });
    
    // Handling the register button click event
    document.getElementById('idRegister').addEventListener('submit', async () => {
        event.preventDefault();
        const sUserName = $('#userNameId').val().trim();
        const sEmail = $('#userEmailId').val().trim();
        const sPhoneNumber = $('#userPhoneId').val().trim();
        const sPassword = $('#userPasswordId').val();
        const sConfirmPassword = $('#confirmPasswordId').val();
        const iUserType = $('#userTypeId').val();
        const sKeyId = $('#KeyId').val().trim();
        
        // Validate all the fields
        if (!sUserName) {
            responsePop('Error', 'Username is required', 'error', 'ok');
            return;
        }

        if (!sEmail || !validateEmail(sEmail)) {
            responsePop('Error', 'A valid email is required', 'error', 'ok');
            return;
        }

        if (!sPhoneNumber || !validatePhoneNumber(sPhoneNumber)) {
            responsePop('Error', 'A valid phone number is required', 'error', 'ok');
            return;
        }

        if (!sPassword) {
            responsePop('Error', 'Password is required', 'error', 'ok');
            return;
        }

        if (sPassword.length < 6) {
            responsePop('Error', 'Password must be at least 6 characters long', 'error', 'ok');
            return;
        }

        if (sPassword !== sConfirmPassword) {
            responsePop('Error', 'Passwords do not match', 'error', 'ok');
            return;
        }

        if (!iUserType) {
            responsePop('Error', 'Please select a user type', 'error', 'ok');
            return;
        }

        if (!sKeyId) {
            responsePop('Error', 'Key ID is required', 'error', 'ok');
            return;
        }

        // Make the AJAX request if all fields are valid
        try {
            $.ajax({
                url: "ajaxFile/ajaxUserManage.php?sFlag=addUser",
                method: "POST",
                data: {
                    username: sUserName,
                    email: sEmail,
                    phoneNumber: sPhoneNumber,
                    password: sPassword,
                    userType: iUserType,
                    keyId: sKeyId,
                },
                success: function (data) {
                    console.log(data.status);
                    if (data.status == 200) {
                        responsePop('Success', data.message, 'success', 'ok');
                        
                        // Redirect after some delay
                        setTimeout(() => {
                            window.location.href = "pages-login.php";
                        }, 500);
                    } else {
                        responsePop('Error', data.message, 'error', 'ok');
                    }
                },
                error: function (error) {
                    responsePop('Error', 'Server Error', 'error', 'ok');
                }
            });
        } catch (error) {
            console.log('Fetch error:', error);
            responsePop('Error', 'An error occurred while making the request.', 'error', 'ok');
        }
    });
});

// Utility function to validate email
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Utility function to validate phone number
function validatePhoneNumber(phone) {
    const phoneRegex = /^[0-9]{10}$/; // Adjust regex as needed
    return phoneRegex.test(phone);
}


// Login function
function login() {
    const sUserId = document.getElementById('sUserId').value;
    const sPassword = document.getElementById('userPasswordId').value;
    
    try {
        $.ajax({
            url: "ajaxFile/ajaxUserManage.php?sFlag=login",
            method: "GET",
            data: {
                user_id: sUserId,
                password: sPassword,
            },
            success: function (data) {
                try {
                    const aData = data[0];
                    
                    if (data.status === 200) {
                        responsePop('Success', "Login Successfully", 'success', 'ok');

                        // Make another Ajax request for session.php
                        $.ajax({
                            url:"ajaxFile/ajaxSession.php?sFlag=setSessionData",
                            method: "POST",
                            data: {
                                "id": aData.id,
                                "username": aData.staff_name,
                                "phoneNumber": aData.phone,
                                "email":aData.email,
                                "user_id":aData.user_id,
                                "User_type":aData.user_type,
                                "login": 1,
                            },
                            dataType: "json",
                            success: function (sessionData) {
                                if (sessionData.iUserID != '') {
                                    window.location.href = "BorrowerManagement.php";
                                } else {
                                    responsePop('Error', 'Failed to log in', 'error', 'ok');
                                }
                            },
                            error: function (error) {
                                // Handle Ajax error for session.php
                                responsePop('Error', 'Failed to log in', 'error', 'ok');
                            }
                        });
                    } else {
                        responsePop('Error', data.message, 'error', 'ok');
                    }
                } catch (error) {
                    console.log(error);
                    responsePop('Error', 'Invalid response from the server', 'error', 'ok');
                }
            },
            error: function (error) {
                // Handle Ajax error for session.php
                responsePop('Error', 'Server Error', 'error', 'ok');
            }
        });
    } catch (error) {
        console.log('Fetch error:', error);
        responsePop('Error', 'An error occurred while making the request.', 'error', 'ok');
    }
}

// Reset the login form after successful login (optional)
function resetLoginForm() {
    document.getElementById('loginForm').reset(); // Use the form's ID for login form
}
