<?php
  $use_white_background = true;
  $require_email_auth = false;
  $pagetitle = "My Account";
  include BASE_PATH . "views/header.php";
  $username = $_SESSION['Username'];
  $userID = $_SESSION['UserID'];

  $forenameUpdate = false;
  $surnameUpdate = false;
  $emailUpdate = false;
  $mobileUpdate = false;
  $emailCommsUpdate = false;
  $mobileCommsUpdate = false;
  $successInformation = "";
  $emailChecked = "";
  $mobileChecked = "";

  $query = "SELECT * FROM users WHERE UserID = '$userID' ";
  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $email = $row['EmailAddress'];
  $forename = $row['Forename'];
  $surname = $row['Surname'];
  $access = $row['AccessLevel'];
  $userID = $row['UserID'];
  $mobile = $row['Mobile'];
  $emailComms = $row['EmailComms'];
  $mobileComms = $row['MobileComms'];

  if (!empty($_POST['forename'])) {
    $newForename = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['forename']))));
    if ($newForename != $forename) {
      $sql = "UPDATE `users` SET `Forename` = '$newForename' WHERE `UserID` = '$userID'";
      mysqli_query($link, $sql);
      $forenameUpdate = true;
    }
  }
  if (!empty($_POST['surname'])) {
    $newSurname = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['surname']))));
    if ($newSurname != $surname) {
      $sql = "UPDATE `users` SET `Surname` = '$newSurname' WHERE `UserID` = '$userID'";
      mysqli_query($link, $sql);
      $surnameUpdate = true;
    }
  }

  // Change Email is Temporarily Disabled

  if (!empty($_POST['mobile'])) {
    $newMobile = mysqli_real_escape_string($link, "+44" .
    ltrim(preg_replace('/\D/', '', str_replace('+44', '', $_POST['mobile'])),
    '0'));
    if ($newMobile != $mobile) {
      $sql = "UPDATE `users` SET `Mobile` = '$newMobile' WHERE `UserID` = '$userID'";
      mysqli_query($link, $sql);
      $mobileUpdate = true;
    }
  }
  $post = app('request')->body;
  if (app('request')->method == "POST") {
    if (isset($post['emailContactOK']) && $post['emailContactOK'] == 1) {
      $sql = "UPDATE `users` SET `EmailComms` = '1' WHERE `UserID` = '$userID'";
      mysqli_query($link, $sql);
      if ($emailComms != 1) {
        $emailCommsUpdate = true;
        $emailComms = 1;
      }
    } else {
      $sql = "UPDATE `users` SET `EmailComms` = '0' WHERE `UserID` = '$userID'";
      mysqli_query($link, $sql);
      if ($emailComms == 1) {
        $emailCommsUpdate = true;
        $emailComms = 0;
      }
    }
    if (isset($post['smsContactOK'])  && $post['smsContactOK'] == 1) {
      $sql = "UPDATE `users` SET `MobileComms` = '1' WHERE `UserID` = '$userID'";
      mysqli_query($link, $sql);
      if ($mobileComms != 1) {
        $mobileCommsUpdate = true;
        $mobileComms = 1;
      }
    } else {
      $sql = "UPDATE `users` SET `MobileComms` = '0' WHERE `UserID` = '$userID'";
      mysqli_query($link, $sql);
      if ($mobileComms == 1) {
        $mobileCommsUpdate = true;
        $mobileComms = 0;
      }
    }
  }

  if ($emailComms == 1) {
    $emailChecked = " checked ";
  }
  if ($mobileComms == 1) {
    $mobileChecked = " checked ";
  }
  //pre($_SESSION);
?>
<div class="container">
  <h1>Hello <?php echo $forename ?></h1>
  <p class="lead mb-0">Welcome to My Account where you can change your personal details, password, contact information and add swimmers to your account.</p>
  <?php if ($forenameUpdate || $surnameUpdate || $emailUpdate || $mobileUpdate) {
    $userID = mysqli_real_escape_string($link, $_SESSION['UserID']);
    $query = "SELECT * FROM users WHERE UserID = '$userID';";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $email = $row['EmailAddress'];
    $forename = $row['Forename'];
    $surname = $row['Surname'];
    $access = $row['AccessLevel'];
    $userID = $row['UserID'];
    $mobile = $row['Mobile'];
    $emailComms = $row['EmailComms'];
    $mobileComms = $row['MobileComms'];
    if ($emailComms==1) {
      $emailChecked = " checked ";
    }
    if ($mobileComms==1) {
      $mobileChecked = " checked ";
    }

  ?>
  <div class="alert alert-success mt-3 mb-0">
    <strong>We have updated</strong>
    <ul class="mb-0">
      <?php
      if ($forenameUpdate) { echo '<li>Your first name</li>'; }
      if ($surnameUpdate) { echo '<li>Your last name</li>'; }
      if ($emailUpdate) { echo '<li>Your email address</li>'; }
      if ($mobileUpdate) { echo '<li>Your mobile number</li>'; }
      if ($emailCommsUpdate) { echo '<li>Your email preferences</li>'; }
      if ($mobileCommsUpdate) { echo '<li>Your mobile preferences</li>'; }
      ?>
    </ul>
  </div>
  <?php  } ?>
  <?php
  if ($require_email_auth) {
    echo '
    <div class="alert alert-warning mt-3 mb-0">
    To complete your change of email address, please check the link in your inbox.
    </div>';
  }
  ?>
  <div class="row justify-content-center mt-3">
    <div class="col-12 col-lg-8">
      <div class="cell">
        <h2>Your Details</h2>
        <p class="border-bottom border-gray pb-2">What we know about you.</p>
        <form method="post">
          <div class="form-group">
              <label for="forename">Name</label>
              <input type="text" class="form-control" name="forename" id="forename" placeholder="Forename" value="<?php echo $forename ?>">
           </div>
           <div class="form-group">
              <label for="surname">Surname</label>
              <input type="text" class="form-control" name="surname" id="surname" placeholder="Surname" value="<?php echo $surname ?>">
          </div>
           <div class="form-group">
              <label for="email">Email</label>
              <input disabled type="email" class="form-control" name="email" id="emailbox" placeholder="Email Address" value="<?php echo $email ?>" aria-describedby="emailHelp">
              <small id="emailHelp" class="form-text text-muted">
                We've temporarily disabled the ability to change your email address while we make some changes to our systems.
              </small>
          </div>
          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" value="1" id="emailContactOK" aria-describedby="emailContactOKHelp" name="emailContactOK" <?php echo $emailChecked; ?> >
              <label class="custom-control-label" for="emailContactOK">Check this to receive news by email</label>
              <small id="emailContactOKHelp" class="form-text text-muted">You'll still receive emails relating to your account if you don't receive news</small>
            </div>
          </div>
          <div class="form-group">
            <label for="mobile">Mobile Number</label>
            <input type="tel" class="form-control" name="mobile" id="mobile" aria-describedby="mobileHelp" placeholder="Mobile Number" value="<?php echo $mobile ?>">
            <small id="mobileHelp" class="form-text text-muted">If you don't have a mobile, use your landline number.</small>
          </div>
          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" value="1" id="smsContactOK" aria-describedby="smsContactOKHelp" name="smsContactOK" <?php echo $mobileChecked; ?> >
              <label class="custom-control-label" for="smsContactOK">Check this if you would like to receive text messages</label>
              <small id="smsContactOKHelp" class="form-text text-muted">We'll still use this to contact you in an emergency</small>
            </div>
          </div>
          <div class="form-group" id="gravitar">
            <label for="mobile" class="d-block">Account Image</label>
            <?php
            $grav_url = "https://www.gravatar.com/avatar/" . md5( mb_strtolower( trim( $_SESSION['EmailAddress'] ) ) ) . "?d=" . urlencode("https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad-retina.png") . "&s=240";
            ?>
            <img class="mr-3" src="<?php echo $grav_url ?>" alt="" width="80" height="80">
            <small class="form-text text-muted">If you have an image linked to your email with Gravitar, we'll display it in the system</small>
          </div>
          <p class="mb-0"><input type="submit" class="btn btn-outline-dark" value="Save Changes"></p>
        </form>
        </div>

        <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
        <div class="cell">
          <h2>My Swimmers</h2>
          <p>Swimmers linked to your account</p>
          <?php echo mySwimmersTable($link, $userID) ?>
          <p class="mb-0"><a href="<?php echo autoUrl("my-account/addswimmer"); ?>" class="btn btn-outline-dark">Add a Swimmer</a></p>
        </div>
      <?php } ?>
    </div>
    <div class="col">
      <div class="cell">
        <h2>Password</h2>
        <p class="border-bottom border-gray pb-2">Change your password regularly to keep your account safe</p>
        <p class="mb-0"><a href="<?php echo autoUrl("my-account/password"); ?>" class="btn btn-outline-dark">Change my Password</a></p>
      </div>
      <?php
      if ($_SESSION['AccessLevel'] == "Parent") {
        $contacts = new EmergencyContacts($link);
        $contacts->byParent($userID);

        $contactsArray = $contacts->getContacts();
        ?>
        <div class="cell">
          <h2>My Emergency Contacts</h2>
          <p class="border-bottom border-gray pb-2 mb-0">
            These are your emergency contacts
          </p>
          <?php if (sizeof($contactsArray) == 0) { ?>
            <div class="alert alert-warning mt-3">
              <p class="mb-0">
                <strong>
                  You have no Emergency Contacts
                </strong>
              </p>
              <p class="mb-0">
                As a result, we'll only be able to try and contact you in an emergency
              </p>
            </div>
          <?php } else { ?>
          <div class="mb-3">
      		<?php for ($i = 0; $i < sizeof($contactsArray); $i++) {
      			?>
      			<div class="media pt-3">
      				<div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
      					<div class="row align-items-center	">
      						<div class="col-9">
      							<p class="mb-0">
      								<strong class="d-block">
      									<?php echo $contactsArray[$i]->getName(); ?>
      								</strong>
      								<a href="tel:<?php echo $contactsArray[$i]->getContactNumber(); ?>">
      									<?php echo $contactsArray[$i]->getContactNumber(); ?>
      								</a>
      							</p>
      						</div>
      						<div class="col text-sm-right">
      							<a href="<?php echo autoUrl("emergency-contacts/edit/" .
      							$contactsArray[$i]->getID()); ?>" class="btn btn-primary">
      								Edit
      							</a>
      						</div>
      					</div>
      				</div>
      			</div>
      			<?php
      		} ?>
      		</div>
          <?php } ?>
      		<p class="mb-0">
      			<a href="<?php echo autoUrl("emergency-contacts/new"); ?>" class="btn btn-outline-dark">
      				Add New
      			</a>
      		</p>
        </div>
        <?php
      } ?>
      <div class="cell">
        <h2>Technical Details</h2>
        <p class="border-bottom border-gray pb-2">Some things you can't change about your account</p>
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" class="form-control" name="username" id="username"
          placeholder="Username" value="<?php echo $username ?>" readonly>
          <small id="usernameHelp" class="form-text text-muted">You can't change your
          username. When we first introduced this software, we asked users to set a
          username. We no longer do this, but if you did originally set one, you can
          still use it to log in.</small>
        </div>
        <div class="form-group">
          <label for="id">Unique User Identifier</label>
          <input type="text" class="form-control mono" name="id" id="id" placeholder="ID" value="CLSU<?php echo $userID ?>" readonly>
        </div>
        <div class="form-group">
          <label for="access">Access Level</label>
          <input type="text" class="form-control" name="access" id="access" placeholder="Access Level" value="<?php echo $access ?>" readonly>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
