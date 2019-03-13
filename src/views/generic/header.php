<?

require 'GlobalHead.php';

$bg = "bg-white";
?>
<body class="<?=$bg?> account--body">

  <div class="sr-only sr-only-focusable">
    <a href="#maincontent">Skip to main content</a>
  </div>

  <div class="d-print-none">

    <?php if (IS_EVALUATION_COPY === true) { ?>
      <div class="bg-secondary text-white py-2 d-print-none">
        <div class="<?=$container_class?>">
          <p class="mb-0">
            <strong>
              This is an evaluation copy of this software
            </strong>
          </p>
          <p class="mb-0">
            Your club is testing this system
          </p>
        </div>
      </div>
    <?php } ?>

    <?php if (isset($_SESSION['UserSimulation'])) { ?>
      <div class="bg-secondary text-white py-2 d-print-none">
        <div class="<?=$container_class?>">
          <p class="mb-0">
            <strong>
              You are in User Simulation Mode simulating <?=
              $_SESSION['UserSimulation']['SimUserName'] ?>
            </strong>
          </p>
          <p class="mb-0">
            <a href="<?=autoUrl("users/simulate/exit")?>" class="text-white">
              Exit User Simulation Mode
            </a>
          </p>
        </div>
      </div>
    <?php } ?>

    <div class="bg-dark">
      <div class="<?=$container_class?>">
    <nav class="navbar navbar-expand-lg navbar-dark
    d-print-none justify-content-between px-0" role="navigation">

        <a class="navbar-brand" href="<?=autoUrl("") ?>">
          <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
            My Membership
          <?php } else { ?>
            <?=CLUB_NAME?>
          <?php } ?>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse"
        data-target="#chesterNavbar" aria-controls="chesterNavbar"
        aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

  	  <div class="collapse navbar-collapse offcanvas-collapse" id="chesterNavbar">
      <?php if (!user_needs_registration($_SESSION['UserID'])) { ?>
  		<ul class="navbar-nav mr-auto">
  		<?php if (!empty($_SESSION['LoggedIn'])) { ?>
        <li class="nav-item">
  			  <a class="nav-link" href="<?=autoUrl("") ?>">Home</a>
  		  </li>
        <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
          <?
          $user = mysqli_real_escape_string($link, $_SESSION['UserID']);
          $getSwimmers = "SELECT * FROM `members` WHERE `UserID` = '$user' ORDER BY `MForename` ASC, `MSurname` ASC;";
          $getSwimmers = mysqli_query($link, $getSwimmers);
          ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="swimmersDropdown"
            role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
              My Swimmers
            </a>
            <div class="dropdown-menu" aria-labelledby="swimmersDropdown">
              <a class="dropdown-item" href="<?=autoUrl("swimmers") ?>">Swimmers Home</a>
              <?php if (mysqli_num_rows($getSwimmers) > 0) { ?>
              <div class="dropdown-divider"></div>
              <h6 class="dropdown-header">My Swimmers</h6>
              <?php for ($i = 0; $i < mysqli_num_rows($getSwimmers); $i++) {
                $getSwimmerRow = mysqli_fetch_array($getSwimmers, MYSQLI_ASSOC); ?>
                <a class="dropdown-item" href="<?=autoUrl("swimmers/" .
                $getSwimmerRow['MemberID']) ?>"><?php echo
                $getSwimmerRow['MForename'] . " " . $getSwimmerRow['MSurname'];
                ?></a>
              <?php } ?>
              <?php } else { ?>
                <a class="dropdown-item" href="<?=autoUrl("myaccount/addswimmer") ?>">Add Swimmers</a>
              <?php } ?>
            </div>
          </li>
        <li class="nav-item">
  			  <a class="nav-link" href="<?=autoUrl("emergencycontacts") ?>">Emergency Contacts</a>
  		  </li>
        <?php }
        else { ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="swimmerDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Swimmers &amp; Squads
            </a>
            <div class="dropdown-menu" aria-labelledby="swimmerDropdown">
              <a class="dropdown-item" href="<?=autoUrl("swimmers")?>">Swimmer Directory</a>
              <?php if ($_SESSION['AccessLevel'] == "Admin") { ?>
              <a class="dropdown-item" href="<?=autoUrl("swimmers/addmember")?>">Add Member</a>
              <?php } ?>
              <?php if ($_SESSION['AccessLevel'] != "Galas") { ?>
              <a class="dropdown-item" href="<?=autoUrl("squads")?>">Squads</a>
          		<a class="dropdown-item" href="<?=autoUrl("squads/moves")?>">Squad Moves</a>
              <?php } ?>
              <a class="dropdown-item" href="<?=autoUrl("swimmers/accesskeys")?>">Access Keys</a>
              <?php if ($_SESSION['AccessLevel'] == "Admin") { ?>
              <a class="dropdown-item" href="<?=autoUrl("swimmers/orphaned")?>">Orphan Swimmers</a>
              <?php } ?>
              <?php if ($_SESSION['AccessLevel'] == "Coach") { ?>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?=autoUrl("payments/history/squads/" . date("Y/m")) ?>">
                Squad Fee Payments, <?=date("F Y")?>
              </a>
              <?
              $lm = date("Y/m", strtotime("first day of last month"));
              $lms = date("F Y", strtotime("first day of last month"));
              ?>
              <a class="dropdown-item" href="<?=autoUrl("payments/history/squads/" . $lm) ?>">
                Squad Fee Payments, <?=$lms?>
              </a>
              <?php } ?>
            </div>
    		  </li>
          <?php if ($_SESSION['AccessLevel'] == "Admin" ||
          $_SESSION['AccessLevel'] == "Coach" || $_SESSION['AccessLevel'] ==
          "Committee") { ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="swimmerDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Registers
            </a>
            <div class="dropdown-menu" aria-labelledby="registerDropdown">
              <a class="dropdown-item" href="<?=autoUrl("attendance")?>">Attendance Home</a>
              <a class="dropdown-item" href="<?=autoUrl("attendance/register")?>">Take Register</a>
              <?php if ($_SESSION['AccessLevel'] == "Admin" || $_SESSION['AccessLevel'] == "Committee") {?>
              <a class="dropdown-item" href="<?=autoUrl("attendance/sessions")?>">Manage Sessions</a>
              <a class="dropdown-item" href="<?=autoUrl("attendance/venues")?>">Manage Venues</a>
              <?php } ?>
              <a class="dropdown-item" href="<?=autoUrl("attendance/history")?>">Attendance History</a>
              <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/squads/" target="_blank">Timetables</a>
            </div>
    		  </li>
          <?php } ?>
          <?php if ($_SESSION['AccessLevel'] == "Admin" ||
          $_SESSION['AccessLevel'] == "Galas") { ?>
          <li class="nav-item">
    			  <a class="nav-link" href="<?=autoUrl("users") ?>">Users</a>
    		  </li>
          <?php } ?>
          <?php if ($_SESSION['AccessLevel'] == "Galas") { ?>
          <li class="nav-item">
    			  <a class="nav-link" href="<?=autoUrl("payments") ?>">Pay</a>
    		  </li>
          <?php } ?>
          <?php if ($_SESSION['AccessLevel'] == "Admin") { ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="paymentsAdminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Pay
            </a>
            <div class="dropdown-menu" aria-labelledby="paymentsAdminDropdown">
              <a class="dropdown-item" href="<?=autoUrl("payments") ?>">Payments Home</a>
              <a class="dropdown-item" href="<?=autoUrl("payments/history") ?>">Payment Status</a>
              <a class="dropdown-item" href="<?=autoUrl("payments/extrafees")?>">Extra Fees</a>
              <div class="dropdown-divider"></div>
              <h6 class="dropdown-header"><?php echo date("F Y"); ?></h6>
              <a class="dropdown-item" href="<?=autoUrl("payments/history/squads/" . date("Y/m")) ?>">
                Squad Fees
              </a>
              <a class="dropdown-item" href="<?=autoUrl("payments/history/extras/" . date("Y/m")) ?>">
                Extra Fees
              </a>
              <?
              $lm = date("Y/m", strtotime("first day of last month"));
              $lms = date("F Y", strtotime("first day of last month"));
              ?>
              <h6 class="dropdown-header"><?php echo $lms; ?></h6>
              <a class="dropdown-item" href="<?=autoUrl("payments/history/squads/" . $lm) ?>">
                Squad Fees
              </a>
              <a class="dropdown-item" href="<?=autoUrl("payments/history/extras/" . $lm) ?>">
                Extra Fees
              </a>
              <div class="dropdown-divider"></div>
              <h6 class="dropdown-header">GoCardless Accounts</h6>
              <a class="dropdown-item" href="https://manage.gocardless.com" target="_blank">
                Live
              </a>
              <a class="dropdown-item" href="https://manage-sandbox.gocardless.com" target="_blank">
                Sandbox
              </a>
            </div>
          </li>
          <?php } ?>
          <?php if ($_SESSION['AccessLevel'] == "Admin" || $_SESSION['AccessLevel'] == "Coach") { ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="notifyDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Notify
            </a>
            <div class="dropdown-menu" aria-labelledby="notifyDropdown">
              <a class="dropdown-item" href="<?=autoUrl("notify")?>">Notify Home</a>
          		<a class="dropdown-item" href="<?=autoUrl("notify/newemail")?>">New Message</a>
              <a class="dropdown-item" href="<?=autoUrl("notify/lists")?>">Targeted Lists</a>
              <?php if ($_SESSION['AccessLevel'] == "Admin") { ?>
              <a class="dropdown-item" href="<?=autoUrl("notify/sms")?>">SMS Lists</a>
          		<a class="dropdown-item" href="<?=autoUrl("notify/email")?>">Pending Messages</a>
              <?php } ?>
              <a class="dropdown-item" href="<?=autoUrl("notify/history")?>">Previous Messages</a>
            </div>
          </li>
          <?php } ?>
        <?php } ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="galaDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Galas
          </a>
          <div class="dropdown-menu" aria-labelledby="galaDropdown">
            <a class="dropdown-item" href="<?=autoUrl("galas")?>">Gala Home</a>
            <?php if ($_SESSION['AccessLevel'] == "Parent") {?>
            <a class="dropdown-item" href="<?=autoUrl("galas/entergala")?>">Enter a Gala</a>
            <a class="dropdown-item" href="<?=autoUrl("galas/entries")?>">My Entries</a>
            <?php } else {?>
            <a class="dropdown-item" href="<?=autoUrl("galas/addgala")?>">Add Gala</a>
            <a class="dropdown-item" href="<?=autoUrl("galas/entries")?>">View Entries</a>
            <?php } ?>
            <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/competitions/" target="_blank">Gala Website <i class="fa fa-external-link"></i></a>
            <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/competitions/category/galas/" target="_blank">Upcoming Galas <i class="fa fa-external-link"></i></a>
            <?php if ($access == "Parent") {?>
            <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/competitions/enteracompetition/guidance/" target="_blank">Help with Entries <i class="fa fa-external-link"></i></a>
            <?php } ?>
          </div>
  		  </li>
        <?php if ($_SESSION['AccessLevel'] == "Parent") {
          if (!userHasMandates($_SESSION['UserID'])) { ?>
          <li class="nav-item">
    			  <a class="nav-link"  href="<?=autoUrl("payments")?>">
              Payments
    			  </a>
    		  </li>
          <?php } else { ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="paymentsParentDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Pay
            </a>
            <div class="dropdown-menu" aria-labelledby="paymentsParentDropdown">
              <a class="dropdown-item" href="<?=autoUrl("payments")?>">Payments Home</a>
              <a class="dropdown-item" href="<?=autoUrl("payments/transactions")?>">My Billing History</a>
              <a class="dropdown-item" href="<?=autoUrl("payments/mandates")?>">My Bank Account</a>
              <a class="dropdown-item" href="<?=autoUrl("payments/fees")?>">My Current Statement</a>
            </div>
          </li>
        <?php } } ?>
        <?php if ($_SESSION['AccessLevel'] != "Parent" &&
    		$_SESSION['AccessLevel'] != "Coach") { ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="postDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Posts
          </a>
          <div class="dropdown-menu" aria-labelledby="postDropdown">
            <a class="dropdown-item" href="<?=autoUrl("posts")?>">Home</a>
        		<a class="dropdown-item" href="<?=autoUrl("posts/new")?>">New Page</a>
        		<?php if ($allow_edit && $_SESSION['AccessLevel'] != "Parent" &&
        		$_SESSION['AccessLevel'] != "Coach") { ?>
        		<a class="dropdown-item" href="<?=app('request')->curl?>edit">Edit Current Page</a>
        		<?php } ?>
        		<?php if ($exit_edit && $_SESSION['AccessLevel'] != "Parent" &&
        		$_SESSION['AccessLevel'] != "Coach") { ?>
        		<a class="dropdown-item" href="<?=autoUrl("posts/" . $id)?>">View Page</a>
        		<?php } ?>
          </div>
        </li>
  		  <?php }
          } ?>
  		  <?php if (empty($_SESSION['LoggedIn'])) { ?>
        <li class="nav-item">
  			  <a class="nav-link" href="<?=autoUrl("login") ?>">Login</a>
  		  </li>
        <li class="nav-item">
  			  <a class="nav-link" href="<?=autoUrl("register") ?>">Create Account</a>
  		  </li>
        <li class="nav-item">
  			  <a class="nav-link" href="<?=autoUrl("services/request-a-trial") ?>">Request a Trial</a>
  		  </li>
        <li class="nav-item">
  			  <a class="nav-link" href="<?=autoUrl("timeconverter") ?>">Time Converter</a>
  		  </li>
        <?php } ?>
  		</ul>
      <?php if (!empty($_SESSION['LoggedIn'])) { ?>
      <?php $user_name = str_replace(' ', '&nbsp;', htmlspecialchars(getUserName($_SESSION['UserID']))); ?>
      <ul class="navbar-nav">
        <!--<a class="btn btn-sm btn-outline-light my-2 my-sm-0" href="<?=autoUrl("logout") ?>">Logout</a>-->
        <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          <?= $user_name ?> <i class="fa fa-user-circle-o" aria-hidden="true"></i>
        </a>
          <div class="dropdown-menu dropdown-menu-right">
            <span class="dropdown-item-text">Signed&nbsp;in&nbsp;as&nbsp;<strong><?= $user_name ?></strong></span>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="<?=autoUrl("myaccount") ?>">Your Profile</a>
            <a class="dropdown-item" href="<?=autoUrl("myaccount/email") ?>">Your Email Options</a>
            <a class="dropdown-item" href="<?=autoUrl("myaccount/general") ?>">Your General Options</a>
            <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
            <a class="dropdown-item" href="<?=autoUrl("emergencycontacts") ?>">Your Emergency Contacts</a>
            <?php } ?>
            <a class="dropdown-item" href="<?=autoUrl("myaccount/password") ?>">Your Password</a>
            <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
            <a class="dropdown-item" href="<?=autoUrl("myaccount/notifyhistory") ?>">Your Message History</a>
            <a class="dropdown-item" href="<?=autoUrl("myaccount/addswimmer") ?>">Add a Swimmer</a>
            <?php } ?>
            <a class="dropdown-item" href="<?=autoUrl("myaccount/loginhistory") ?>">Your Login History</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/">Help</a>
            <a class="dropdown-item" href="<?= autoUrl("logout") ?>">Logout</a>
          </div>
        </li>
      </ul>
      <?php }
      }?>
    </nav>
  </div>
  </div>

</div>

<div id="maincontent"></div>

  <noscript>
    <div class="alert alert-danger d-print-none">
      <p class="mb-0">
        <strong>
          JavaScript is disabled or not supported
        </strong>
      </p>
      <p class="mb-0">
  	    It looks like you've got JavaScript disabled or your browser does not
  	    support it. JavaScript is essential for our website to properly so we
  	    recommend you enable it or upgrade to a browser which supports it as
  	    soon as possible. <a href="http://browsehappy.com/" class="alert-link"
  	    target="_blank">Upgrade your browser today <i class="fa
  	    fa-external-link" aria-hidden="true"></i></a>
      </p>
    </div>
    <hr>
  </noscript>

<!-- END OF HEADERS -->
<div class="mb-3"></div>

<div class="focus-highlight have-full-height" style="min-height:70vh">
