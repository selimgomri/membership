<?php

if (isset($use_website_menu)) {
  define('USE_CLS_MENU', $use_website_menu && bool(env('IS_CLS')));
}

if (!function_exists('chesterStandardMenu')) {
  function chesterStandardMenu() {
    
    global $db;
    $use_website_menu = false;
    if (defined('USE_CLS_MENU')) {
      $use_website_menu = USE_CLS_MENU;
    }
    global $allow_edit;
    global $exit_edit;
    global $edit_link;

    $canPayByCard = false;
    if (env('STRIPE')) {
      $canPayByCard = true;
    }

    $renewalOpen = false;
    if ($_SESSION['AccessLevel'] == 'Parent') {
      $date = new DateTime('now', new DateTimeZone('Europe/London'));
      $getRenewals = $db->prepare("SELECT COUNT(*) FROM renewals WHERE StartDate <= :today AND EndDate >= :today;");
      $getRenewals->execute([
        'today' => $date->format('Y-m-d')
      ]);
      if ($getRenewals->fetchColumn() == 1) {
        $renewalOpen = true;
      }
    }
    
    ?>

  <?php if (!(isset($_SESSION['UserID']) && user_needs_registration($_SESSION['UserID'])) && (!isset($use_website_menu) || !$use_website_menu)) { ?>
            <div class="collapse navbar-collapse offcanvas-collapse" id="chesterNavbar">
              <ul class="navbar-nav mr-auto">
                <?php if (!empty($_SESSION['LoggedIn'])) { ?>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo autoUrl("") ?>">Home</a>
                </li>
                <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
                <?php
            $getSwimmers = $db->prepare("SELECT MForename Name, MSurname Surname, MemberID ID FROM `members` WHERE `UserID` = ? ORDER BY Name ASC, Surname ASC");
            $getSwimmers->execute([$_SESSION['UserID']]);
            ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="swimmersDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    My Swimmers
                  </a>
                  <div class="dropdown-menu" aria-labelledby="swimmersDropdown">
                    <a class="dropdown-item" href="<?php echo autoUrl("swimmers") ?>">Swimmers Home</a>
                    <?php if ($swimmer = $getSwimmers->fetch(PDO::FETCH_ASSOC)) { ?>
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">My Swimmers</h6>
                    <?php do { ?>
                    <a class="dropdown-item" href="<?=autoUrl("swimmers/" . $swimmer['ID'])?>">
                      <?=htmlspecialchars($swimmer['Name'] . " " . $swimmer['Surname'])?>
                    </a>
                    <?php } while ($swimmer = $getSwimmers->fetch(PDO::FETCH_ASSOC)); ?>
                    <?php } else { ?>
                    <a class="dropdown-item" href="<?=autoUrl("my-account/addswimmer")?>">Add a swimmer</a>
                    <?php } ?>
                  </div>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="<?=autoUrl("sessions")?>">
                    Sessions
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo autoUrl("emergency-contacts") ?>">Emergency Contacts</a>
                </li>
                <?php if ($renewalOpen) { ?>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo autoUrl("renewal") ?>">
                    Membership Renewal
                  </a>
                </li>
                <?php } ?>
                <?php }
          else { ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="swimmerDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    Swimmers &amp; Squads
                  </a>
                  <div class="dropdown-menu" aria-labelledby="swimmerDropdown">
                    <a class="dropdown-item" href="<?php echo autoUrl("swimmers")?>">Swimmer Directory</a>
                    <?php if ($_SESSION['AccessLevel'] == "Admin") { ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("swimmers/addmember")?>">Add Member</a>
                    <?php } ?>
                    <?php if ($_SESSION['AccessLevel'] != "Galas") { ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("squads")?>">Squads</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("squads/moves")?>">Squad Moves</a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("swimmers/accesskeys")?>">Access Keys</a>
                    <?php if ($_SESSION['AccessLevel'] == "Admin") { ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("renewal")?>">Membership Renewal</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("swimmers/orphaned")?>">Orphan Swimmers</a>
                    <?php } ?>
                    <?php if ($_SESSION['AccessLevel'] == "Coach") { ?>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo autoUrl("payments/history/squads/" . date("Y/m")) ?>">
                      Squad Fee Payments, <?=date("F Y")?>
                    </a>
                    <?php
                $lm = date("Y/m", strtotime("first day of last month"));
                $lms = date("F Y", strtotime("first day of last month"));
                ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("payments/history/squads/" . $lm) ?>">
                      Squad Fee Payments, <?=$lms?>
                    </a>
                    <?php } ?>
                  </div>
                </li>
                <?php if ($_SESSION['AccessLevel'] == "Admin" ||
            $_SESSION['AccessLevel'] == "Coach" || $_SESSION['AccessLevel'] ==
            "Committee") { ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="swimmerDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    Registers
                  </a>
                  <div class="dropdown-menu" aria-labelledby="registerDropdown">
                    <a class="dropdown-item" href="<?php echo autoUrl("attendance")?>">Attendance Home</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("attendance/register")?>">Take Register</a>
                    <?php if ($_SESSION['AccessLevel'] == "Admin" || $_SESSION['AccessLevel'] == "Committee") {?>
                    <a class="dropdown-item" href="<?php echo autoUrl("attendance/sessions")?>">Manage Sessions</a>
                    <a class="dropdown-item" href="<?=autoUrl("attendance/venues")?>">Manage Venues</a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("attendance/history")?>">Attendance History</a>
                    <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/squads/"
                      target="_blank">Timetables</a>
                  </div>
                </li>
                <?php } ?>
                <?php if ($_SESSION['AccessLevel'] == "Admin" ||
            $_SESSION['AccessLevel'] == "Galas") { ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="usersMenu" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    Users
                  </a>
                  <div class="dropdown-menu" aria-labelledby="usersMenu">
                    <a class="dropdown-item" href="<?=autoUrl("users")?>">
                      All Users
                    </a>
                    <a class="dropdown-item" href="<?=autoUrl("assisted-registration")?>">
                      Assisted Account Registration
                    </a>
                  </div>
                </li>
                <?php } ?>
                <?php if ($_SESSION['AccessLevel'] == "Galas") { ?>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo autoUrl("payments") ?>">Pay</a>
                </li>
                <?php } ?>
                <?php if ($_SESSION['AccessLevel'] == "Admin") { ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="paymentsAdminDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Pay
                  </a>
                  <div class="dropdown-menu" aria-labelledby="paymentsAdminDropdown">
                    <a class="dropdown-item" href="<?php echo autoUrl("payments") ?>">Payments Home</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("payments/history") ?>">Payment Status</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("payments/extrafees")?>">Extra Fees</a>
                    <?php if ($_SESSION['AccessLevel'] == 'Admin' || $_SESSION['AccessLevel'] == 'Galas') { ?>
                    <a class="dropdown-item" href="<?=autoUrl("payments/galas")?>">
                      Charge or refund gala entries
                    </a>
                    <?php } ?>
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header"><?php echo date("F Y"); ?></h6>
                    <a class="dropdown-item" href="<?php echo autoUrl("payments/history/squads/" . date("Y/m")) ?>">
                      Squad Fees
                    </a>
                    <a class="dropdown-item" href="<?php echo autoUrl("payments/history/extras/" . date("Y/m")) ?>">
                      Extra Fees
                    </a>
                    <?php
                $lm = date("Y/m", strtotime("first day of last month"));
                $lms = date("F Y", strtotime("first day of last month"));
                ?>
                    <h6 class="dropdown-header"><?php echo $lms; ?></h6>
                    <a class="dropdown-item" href="<?php echo autoUrl("payments/history/squads/" . $lm) ?>">
                      Squad Fees
                    </a>
                    <a class="dropdown-item" href="<?php echo autoUrl("payments/history/extras/" . $lm) ?>">
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
                    <?php if (env('STRIPE')) { ?>
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">Payment Cards</h6>
                    <a class="dropdown-item" href="<?=autoUrl("payments/cards")?>">
                      Credit and Debit Cards
                    </a>
                    <a class="dropdown-item" href="<?=autoUrl("payments/card-transactions")?>">
                      Card Transactions
                    </a>
                    <a class="dropdown-item" href="<?=autoUrl("payments/cards/add")?>">
                      Add a Credit or Debit Card
                    </a>
                    <?php } ?>
                  </div>
                </li>
                <?php } ?>
                <?php if ($_SESSION['AccessLevel'] == "Admin" || $_SESSION['AccessLevel'] == "Coach" || $_SESSION['AccessLevel'] == "Galas") { ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="notifyDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    Notify
                  </a>
                  <div class="dropdown-menu" aria-labelledby="notifyDropdown">
                    <a class="dropdown-item" href="<?php echo autoUrl("notify")?>">Notify Home</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("notify/newemail")?>">New Message</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("notify/lists")?>">Targeted Lists</a>
                    <?php if ($_SESSION['AccessLevel'] == "Admin") { ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("notify/sms")?>">SMS Lists</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("notify/email")?>">Pending Messages</a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("notify/history")?>">Previous Messages</a>
                  </div>
                </li>
                <?php } ?>
                <?php } ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="galaDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    Galas
                  </a>
                  <div class="dropdown-menu" aria-labelledby="galaDropdown">
                    <a class="dropdown-item" href="<?php echo autoUrl("galas")?>">
                      Gala home
                    </a>
                    <?php if ($_SESSION['AccessLevel'] == "Parent") {?>
                    <a class="dropdown-item" href="<?php echo autoUrl("galas/entergala")?>">
                      Enter a gala
                    </a>
                    <a class="dropdown-item" href="<?php echo autoUrl("galas/entries")?>">
                      My entries
                    </a>
                    <?php if ($canPayByCard) { ?>
                      <a class="dropdown-item" href="<?php echo autoUrl("galas/pay-for-entries")?>">
                        Pay for entries
                      </a>
                    <?php } ?>
                    <?php } else {?>
                    <a class="dropdown-item" href="<?php echo autoUrl("galas/addgala")?>">Add gala</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("galas/entries")?>">View entries</a>
                    <?php if ($_SESSION['AccessLevel'] == 'Admin' || $_SESSION['AccessLevel'] == 'Galas') { ?>
                    <a class="dropdown-item" href="<?=autoUrl("payments/galas")?>">
                      Charge or refund entries
                    </a>
                    <?php } ?>
                    <?php } ?>
                    <?php if (bool(env('IS_CLS'))) { ?>
                    <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/competitions/"
                      target="_blank">Gala website <i class="fa fa-external-link"></i></a>
                    <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/competitions/category/galas/"
                      target="_blank">Upcoming galas <i class="fa fa-external-link"></i></a>
                    <?php if ($_SESSION['AccessLevel'] == "Parent") {?>
                    <a class="dropdown-item"
                      href="https://www.chesterlestreetasc.co.uk/competitions/enteracompetition/guidance/"
                      target="_blank">Help with entries <i class="fa fa-external-link"></i></a>
                    <?php } ?>
                    <?php } ?>
                  </div>
                </li>
                <?php if ($_SESSION['AccessLevel'] == "Parent") {
            if (!userHasMandates($_SESSION['UserID'])) { ?>
                <li class="nav-item">
                  <a class="nav-link" href="<?=autoUrl("payments")?>">
                    Payments
                  </a>
                </li>
                <?php } else { ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="paymentsParentDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Pay
                  </a>
                  <div class="dropdown-menu" aria-labelledby="paymentsParentDropdown">
                    <a class="dropdown-item" href="<?=autoUrl("payments")?>">Payments Home</a>
                    <a class="dropdown-item" href="<?=autoUrl("payments/transactions")?>">My Billing History</a>
                    <a class="dropdown-item" href="<?=autoUrl("payments/mandates")?>">My Bank Account</a>
                    <a class="dropdown-item" href="<?=autoUrl("payments/statement/latest")?>">My Latest Statement</a>
                    <a class="dropdown-item" href="<?=autoUrl("payments/fees")?>">My Fees Since Last Bill</a>
                    <?php if (env('STRIPE') != null) { ?>
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">Payment Cards</h6>
                    <a class="dropdown-item" href="<?=autoUrl("payments/cards")?>">
                      Credit and Debit Cards
                    </a>
                    <a class="dropdown-item" href="<?=autoUrl("payments/card-transactions")?>">
                      Card Transactions
                    </a>
                    <a class="dropdown-item" href="<?=autoUrl("payments/cards/add")?>">
                      Add a Credit or Debit Card
                    </a>
                    <?php } ?>
                  </div>
                </li>
                <?php } } ?>
                <?php if ($_SESSION['AccessLevel'] != "Parent" &&
          $_SESSION['AccessLevel'] != "Coach") { ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="postDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    Posts
                  </a>
                  <div class="dropdown-menu" aria-labelledby="postDropdown">
                    <a class="dropdown-item" href="<?php echo autoUrl("posts")?>">Home</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("posts/new")?>">New Page</a>
                    <?php if (isset($allow_edit) && $allow_edit && (($_SESSION['AccessLevel'] != "Parent" &&
              $_SESSION['AccessLevel'] != "Coach" && $edit_link != null) || $page_is_mine)) { ?>
                    <a class="dropdown-item" href="<?=$edit_link?>">Edit Current Page</a>
                    <?php } ?>
                    <?php if (isset($exit_edit) && $exit_edit && $_SESSION['AccessLevel'] != "Parent" &&
              $_SESSION['AccessLevel'] != "Coach") { ?>
                    <a class="dropdown-item" href="<?=autoUrl("posts/" . $id)?>">View Page</a>
                    <?php } ?>
                  </div>
                </li>
                <!--
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="trialDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    Trials
                  </a>
                  <div class="dropdown-menu" aria-labelledby="trialDropdown">
                    <a class="dropdown-item" href="<?=autoUrl("trials")?>">Requested Trials</a>
                    <a class="dropdown-item" href="<?=autoUrl("trials/accepted")?>">Accepted Swimmers</a>
                  </div>
                </li>
                -->
                <?php }
            } ?>
                <?php if (empty($_SESSION['LoggedIn'])) { ?>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo autoUrl("login") ?>">Login</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo autoUrl("register") ?>">Create Account</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo autoUrl("timeconverter") ?>">Time Converter</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo autoUrl("services/request-a-trial") ?>">Request a Trial</a>
                </li>
                <?php } ?>
              </ul>
              <?php if (!empty($_SESSION['LoggedIn'])) {
          global $currentUser;
          $user_name = preg_replace("/( +)/" , '&nbsp;', htmlspecialchars($currentUser->getName())); ?>
              <ul class="navbar-nav">
                <!--<a class="btn btn-sm btn-outline-light my-2 my-sm-0" href="<?=autoUrl("logout")?>">Logout</a>-->
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                    aria-expanded="false">
                    <?= $user_name ?> <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <span class="dropdown-item-text">Signed&nbsp;in&nbsp;as&nbsp;<strong><?= $user_name ?></strong></span>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?=autoUrl("my-account")?>">Your Profile</a>
                    <a class="dropdown-item" href="<?=autoUrl("my-account/email")?>">Your Email Options</a>
                    <a class="dropdown-item" href="<?=autoUrl("my-account/address")?>">Your Address</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("my-account/general") ?>">Your General Options</a>
                    <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("emergency-contacts") ?>">Your Emergency
                      Contacts</a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("my-account/password") ?>">Your Password</a>
                    <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("my-account/notifyhistory") ?>">Your Message
                      History</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("my-account/addswimmer") ?>">Add a Swimmer</a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("my-account/loginhistory") ?>">Your Login
                      History</a>
                    <?php if ($_SESSION['AccessLevel'] == 'Admin') { ?>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?=autoUrl("settings")?>">
                      System Settings
                    </a>
                    <?php } ?>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" target="_blank"
                      href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/">Help</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?=autoUrl("my-account/linked-accounts")?>">Switch Account</a>
                    <a class="dropdown-item" href="<?= autoUrl("logout") ?>">Logout</a>
                  </div>
                </li>
              </ul>
              <?php } ?>
            </div>
          </nav>
          <?php } ?>

  <?php }
}

chesterStandardMenu();