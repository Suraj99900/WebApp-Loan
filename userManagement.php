<?php

// Include header section of template
require_once "config.php";
include_once ABS_PATH_TO_PROJECT . "CDN_Header.php";
include_once ABS_PATH_TO_PROJECT . "classes/sessionCheck.php";

$bIsLogin = $oSessionManager->isLoggedIn ? $oSessionManager->isLoggedIn : false;

if (!$bIsLogin) {
    header("Location: pages-login.php", true, 301);
    exit;
} else {
    $iUserID = $oSessionManager->iUserID;
}
include_once "NavBar.php";
include_once "leftBar.php";
?>


<main id="main" class="main">

    <div class="pagetitle">
        <h1>User Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">User Management</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="row">

                    <!-- User Management -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto">

                            <!-- Filter Section -->
                            <div class="filter-section mb-4 py-4 px-4">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="filterUserName">User Name</label>
                                        <input type="text" id="filterUserName" class="form-control" placeholder="Search by name">
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="filterEmail">Email</label>
                                        <input type="text" id="filterEmail" class="form-control" placeholder="Search by email">
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="filterUserType">User Type</label>
                                        <select id="filterUserType" class="form-control">
                                            <option value="">Select User Type</option>
                                            <option value="1">Admin</option>
                                            <option value="2">Recovery User</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <button id="searchUser" class="btn btn-primary btn-sm mt-4"><i class="fa-solid fa-magnifying-glass"></i></button>
                                        <button id="resetFilters" class="btn btn-secondary btn-sm mt-4"><i class="fa-solid fa-arrows-rotate"></i></button>
                                    </div>
                                </div>
                                <div class="mt-3 float-end">
                                    <button id="addUser" data-bs-toggle="offcanvas" data-bs-target="#AddUserOffCanvasId" aria-controls="AddUserOffCanvasId" class="btn btn-primary">Add User</button>
                                    <button id="exportUserExcel" class="btn btn-success" title="Export to Excel"><i class="fa-solid fa-file-excel"></i></button>
                                    <button id="exportUserPDF" class="btn btn-success" title="Export to PDF"><i class="fa-solid fa-file-pdf"></i></button>
                                </div>
                            </div>
                            <!-- End Filter Section -->

                            <div class="card-body">
                                <div class="row mb-5">
                                    <div class="col12">
                                        <div class="p-2" style="overflow-x: scroll;">
                                            <table id="userDetailsTable" class="table table-striped table-hover table-bordered display">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th scope="col">Sr. No</th>
                                                        <th scope="col">User ID</th>
                                                        <th scope="col">User Name</th>
                                                        <th scope="col">Email</th>
                                                        <th scope="col">Phone</th>
                                                        <th scope="col">User Type</th>
                                                        <th scope="col">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="userBodyId">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div><!-- End User Management -->

                </div>
            </div><!-- End Left side columns -->

        </div>
    </section>

</main><!-- End #main -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Add/Update User Off-Canvas -->
<div class="offcanvas offcanvas-end dynamic-width" tabindex="-1" id="AddUserOffCanvasId" aria-labelledby="AddUserOffCanvasLabel">
    <div class="offcanvas-header">
        <h5 id="AddUserOffCanvasLabel">Add User</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="userForm">
            <input type="hidden" id="userId" name="userId" value="">
            <div class="row">
                <div class="col-lg col-sm-3 mb-3">
                    <label for="userName" class="form-label">User Name</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter user name" required>
                </div>

                <div class="col-lg col-sm-3 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                </div>
                <div class="col-lg col-sm-3 mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                </div>
                <div class="col-lg col-sm-3 mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phoneNumber" placeholder="Enter phone number" required>
                </div>
                <div class="col-lg col-sm-3 mb-3">
                    <label for="userType" class="form-label">User Type</label>
                    <select id="userType" name="userType" class="form-control" required>
                        <option value="">Select User Type</option>
                        <option value="1">Admin</option>
                        <option value="2">Recovery User</option>
                    </select>

                </div>


            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </form>
    </div>
</div>

<!-- Update User Off-Canvas -->
<div class="offcanvas offcanvas-end dynamic-width" tabindex="-1" id="UpdateUserOffCanvasId" aria-labelledby="UpdateUserOffCanvasLabel">
    <div class="offcanvas-header">
        <h5 id="UpdateUserOffCanvasLabel">Update User</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="updateUserForm">
            <input type="hidden" id="updateUserId" name="userId" value="">
            <div class="row">
                <div class="col-lg col-sm-3 mb-3">
                    <label for="updateUserName" class="form-label">User Name</label>
                    <input type="text" class="form-control" id="updateUserName" name="username" placeholder="Enter user name" required>
                </div>

                <div class="col-lg col-sm-3 mb-3">
                    <label for="updateEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="updateEmail" name="email" placeholder="Enter email" required>
                </div>
                <div class="col-lg col-sm-3 mb-3">
                    <label for="updatePassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="updatePassword" name="password" placeholder="Enter password">
                </div>
                <div class="col-lg col-sm-3 mb-3">
                    <label for="updatePhone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="updatePhone" name="phoneNumber" placeholder="Enter phone number" required>
                </div>
                <div class="col-lg col-sm-3 mb-3">
                    <label for="updateUserType" class="form-label">User Type</label>
                    <select id="updateUserType" name="userType" class="form-control" required>
                        <option value="">Select User Type</option>
                        <option value="1">Admin</option>
                        <option value="2">Recovery User</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </form>
    </div>
</div>


<?php include_once "CDN_Footer.php"; ?>

<script src="controller/userManagerController.js"></script>