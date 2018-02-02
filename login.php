<?php
  $pagetitle = "Login";
  $preventLoginRedirect = true;
  include "header.php";

  $errorState = false;

  if ( isset($_SESSION['ErrorState']) ) {
    $errorState = $_SESSION['ErrorState'];
    $username = $_SESSION['EnteredUsername'];
  }

  /*$errorMessage = "";
  $errorState = false;

  if (!empty($_POST['username']) && !empty($_POST['password'])) {
    // Let the user login
    $username = mysqli_real_escape_string($link, $_POST['username']);
    $password = mysqli_real_escape_string($link, $_POST['password']);

    $query = "SELECT * FROM users WHERE Username = '$username' AND Password = '$password' LIMIT 0, 30 ";
    $result = mysqli_query($link, $query);
    $count = mysqli_num_rows($result);

    if ($count == 1) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $email = $row['EmailAddress'];
      $forename = $row['Forename'];
      $surname = $row['Surname'];

      $_SESSION['Username'] = $username;
      $_SESSION['EmailAddress'] = $email;
      $_SESSION['Forename'] = $forename;
      $_SESSION['Surname'] = $surname;
      $_SESSION['LoggedIn'] = 1;

      header("Location: index.php");
    }
    else {
      $errorState = true;
    }
  }*/

?>
<div class="frontpage1" style="margin-top:-1rem;">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-sm-6 col-md-5 col-xl-4"  style="padding-top:5rem;">
        <div class="my-3 p-3 bg-white rounded box-shadow">
          <h2 class="border-bottom border-gray pb-2 mb-0">Please Login</h2>
          <div class="text-muted pt-3">
              <?php if ($errorState == true) { ?>
              <div class="alert alert-danger">
                <strong>Incorrect details</strong> <br>
                Please try again
              </div>
              <?php } ?>

              <form method="post" action="login-action.php" name="loginform" id="loginform">
                <div class="form-group">
                  <label for="username">Username</label>
                  <input type="text" name="username" id="username" class="form-control form-control-lg" value="<?php if ($errorState == true) { echo $username; } ?>" required autofocus>
                </div>
                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" name="password" id="password" class="form-control form-control-lg" required>
                </div>
                <p><input type="submit" name="login" id="login" value="Login" class="btn btn-lg btn-block btn-primary"></p>
                <span class="small text-center d-block"><a href="register.php">Create an account</a></span>
                <span class="small text-center d-block"><a href="forgot-password.php">Forgot password?</a></span>
              </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<?php

  if ( isset($_SESSION['ErrorState']) ) {
    unset($_SESSION['ErrorState']);
  }
  include "footer.php";
?>
