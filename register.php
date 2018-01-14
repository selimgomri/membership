<?php
  $pagetitle = "Register";
  $preventLoginRedirect = true;
  include "header.php";

  $errorMessage = "";
  $errorState = false;

?>
<div class="container">
  <h1>User Registration</h1>
  <p>We need a few details before we start.</p>
  <hr>
  <form method="post" action="registration.php" name="register" id="register">
    <h2>Personal Details</h2>
    <div class="form-group">
      <label for="forename">First Name</label>
      <input class="form-control" type="text" name="forename" id="forename" placeholder="First Name" required>
    </div>
    <div class="form-group">
      <label for="surname">Last Name</label>
      <input class="form-control" type="text" name="surname" id="surname" placeholder="Last Name" required>
    </div>
    <div class="form-group">
      <label for="email">Email Address</label>
      <input class="form-control mb-0" type="email" name="email" id="email" placeholder="yourname@example.com" required>
      <small id="emailHelp" class="form-text text-muted">Your email address will only be used inside Chester-le-Street ASC.</small>
    </div>
    <div class="form-group">
      <label for="mobile">Mobile Number</label>
      <input class="form-control" type="tel" name="mobile" id="mobile" placeholder="01234 567890" required>
      <small id="mobileHelp" class="form-text text-muted">If you don't have a mobile, use your landline number.</small>
    </div>
    <h2>Username and Password</h2>
    <div class="form-group">
      <label for="username">Username</label>
      <input class="form-control" type="text" name="username" id="username" placeholder="Username" required>
    </div>
    <div class="form-group">
      <label for="password1">Password</label>
      <input class="form-control" type="password" name="password1" id="password1" placeholder="Password" required>
    </div>
    <div class="form-group">
      <label for="password2">Confirm Password</label>
      <input class="form-control" type="password" name="password2" id="password2" placeholder="Password" required>
    </div>
    <input type="submit" class="btn btn-success mb-4" value="Register">
</div>

<?php include "footer.php" ?>
