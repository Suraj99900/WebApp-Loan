<?php
// Include header section of template
require_once "config.php";
include_once ABS_PATH_TO_PROJECT . "CDN_Header.php";

include_once ABS_PATH_TO_PROJECT . "classes/sessionCheck.php";

$bIsLogin = $oSessionManager->isLoggedIn ? $oSessionManager->isLoggedIn : false;

if ($bIsLogin) {
    header("Location: BorrowerManagement.php",true);
    exit;
}
?>

<style>
  .form-card {
    width: 40vw;
  }

  @media (max-width:767px) {
    .form-card {
      width: 80vw;
    }
  }
</style>

<main>
  <div class="container">

    <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

            <div class="d-flex justify-content-center py-4">
              <a href="BorrowerManagement.php" class="logo d-flex align-items-center w-auto">
                <img src="assets/img/logo.png" alt="">
                <span class="d-none d-lg-block"><?php echo ORG_NAME; ?></span>
              </a>
            </div><!-- End Logo -->

            <div class="card mb-3 form-card">

              <div class="card-body">

                <div class="pt-4 pb-2">
                  <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>

                </div>

                <form id="idRegister" class="row g-3 needs-validation" novalidate>

                  <div class="col-12">
                    <label for="UserName" class="form-label card-title-change"><i class="fa-solid fa-user fa-i"></i> Username</label>
                    <input type="text" class="form-control custom-control" id="userNameId" name="name" placeholder="Enter Username">
                  </div>

                  <!-- Email -->

                  <div class="col-12">
                    <label for="UserEmail" class="form-label card-title-change"><i class="fa-solid fa-envelope fa-i"></i> Email</label>
                    <input type="email" class="form-control custom-control" id="userEmailId" name="email" placeholder="Enter Email Address">
                  </div>

                  <!-- Phone Number -->

                  <div class="col-12">
                    <label for="UserPhone" class="form-label card-title-change"><i class="fa-solid fa-phone fa-i"></i> Phone Number</label>
                    <input type="text" class="form-control custom-control" id="userPhoneId" name="phone" placeholder="Enter Phone Number">
                  </div>

                  <!-- Password -->

                  <div class="col-12">
                    <label for="UserPassword" class="form-label card-title-change"><i class="fa-solid fa-lock fa-i"></i> Password</label>
                    <input type="password" class="form-control custom-control" id="userPasswordId" name="password" placeholder="Enter Password">
                  </div>

                  <!-- Confirm Password -->

                  <div class="col-12">
                    <label for="ConfirmPassword" class="form-label card-title-change"><i class="fa-solid fa-lock fa-i"></i> Confirm Password</label>
                    <input type="password" class="form-control custom-control" id="confirmPasswordId" name="confirmPassword" placeholder="Confirm Password">
                  </div>
                  <!-- User Type -->

                  <div class="col-sm-12">
                    <label for="UserType" class="form-label card-title-change"><i class="fa-solid fa-users fa-i"></i> User Type</label>
                    <select class="form-control custom-control" id="userTypeId" name="userType">
                      <option value="" disabled selected>Select User Type</option>
                      <option value="1">Admin</option>
                      <option value="2">Recovery User</option>
                    </select>
                  </div>

                  <div class="col-sm-12">
                    <label for="Key" class="form-label card-title-change"><i class="fa-solid fa-lock fa-i"></i> Secret Key</label>
                    <input type="password" class="form-control custom-control" id="KeyId" name="Key" placeholder="Secret Key">
                  </div>

                  <div class="col-12">
                    <button class="btn btn-primary w-100" type="submit">Create Account</button>
                  </div>
                  <div class="col-12">
                    <p class="small mb-0">Already have an account? <a href="pages-login.php">Log in</a></p>
                  </div>
                </form>

              </div>
            </div>

          </div>
        </div>
      </div>

    </section>

  </div>
</main><!-- End #main -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<?php include_once "CDN_Footer.php"; ?>


<script src="controller/poxLoginRegisterController.js"></script>