<?php

if (isset($use_website_menu)) {
  define('USE_CLS_MENU', $use_website_menu && app()->tenant->isCLS());
}

if (!function_exists('chesterStandardMenu')) {
  function chesterStandardMenu()
  {

    $db = app()->db;
    $use_website_menu = false;
    if (defined('USE_CLS_MENU')) {
      $use_website_menu = USE_CLS_MENU;
    }
    global $allow_edit;
    global $exit_edit;
    global $edit_link;

    $canPayByCard = false;
    if (getenv('STRIPE') && app()->tenant->getStripeAccount() && app()->tenant->getBooleanKey('GALA_CARD_PAYMENTS_ALLOWED')) {
      $canPayByCard = true;
    }

    $renewalOpen = false;
    $renewalYear = null;
    if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel']) && $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent') {
      $date = new DateTime('now', new DateTimeZone('Europe/London'));
      $getRenewals = $db->prepare("SELECT COUNT(*) AS `Count`, `Year` FROM renewals WHERE Tenant = :tenant AND StartDate <= :today AND EndDate >= :today;");
      $getRenewals->execute([
        'tenant' => app()->tenant->getId(),
        'today' => $date->format('Y-m-d')
      ]);
      $renewals = $getRenewals->fetch(PDO::FETCH_ASSOC);
      if ($renewals['Count'] == 1) {
        $renewalOpen = true;
        $renewalYear = $renewals['Year'];
      }
    }

    $haveSquadReps = false;
    $getRepCount = $db->prepare("SELECT COUNT(*) FROM squadReps INNER JOIN users ON squadReps.User = users.UserID WHERE users.Tenant = ?");
    $getRepCount->execute([
      app()->tenant->getId(),
    ]);
    if ($getRepCount->fetchColumn() > 0) {
      $haveSquadReps = true;
    }

    $hasNotifyAccess = false;
    if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel']) && $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent') {
      $getNotify = $db->prepare("SELECT COUNT(*) FROM (SELECT User FROM squadReps UNION SELECT User FROM listSenders) AS T WHERE T.User = ?");
      $getNotify->execute([
        $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
      ]);
      if ($getNotify->fetchColumn()) {
        $hasNotifyAccess = true;
      }
    }

    $isTeamManager = false;
    if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel']) && $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent') {
      $date = new DateTime('-1 day', new DateTimeZone('Europe/London'));
      $getGalas = $db->prepare("SELECT COUNT(*) FROM teamManagers INNER JOIN galas ON teamManagers.Gala = galas.GalaID WHERE teamManagers.User = ? AND galas.GalaDate >= ?");
      $getGalas->execute([
        $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
        $date->format("Y-m-d")
      ]);
      if ($getGalas->fetchColumn()) {
        $isTeamManager = true;
      }
    }

?>

    <?php if (!(isset($_SESSION['TENANT-' . app()->tenant->getId()]['UserID']) && user_needs_registration($_SESSION['TENANT-' . app()->tenant->getId()]['UserID'])) && (!isset($use_website_menu) || !$use_website_menu)) { ?>
      <div class="collapse navbar-collapse offcanvas-collapse" id="chesterNavbar">
        <ul class="navbar-nav mr-auto">
          <?php if (!empty($_SESSION['TENANT-' . app()->tenant->getId()]['LoggedIn'])) { ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= htmlspecialchars(autoUrl('')) ?>">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= htmlspecialchars(autoUrl('covid')) ?>">COVID</a>
            </li>
            <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Parent") { ?>
              <?php
              $getSwimmers = $db->prepare("SELECT MForename Name, MSurname Surname, MemberID ID FROM `members` WHERE `UserID` = ? ORDER BY Name ASC, Surname ASC");
              $getSwimmers->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);
              ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="swimmersDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  My Members
                </a>
                <div class="dropdown-menu" aria-labelledby="swimmersDropdown">
                  <a class="dropdown-item" href="<?= htmlspecialchars(autoUrl("swimmers")) ?>">Members home</a>
                  <?php if ($swimmer = $getSwimmers->fetch(PDO::FETCH_ASSOC)) { ?>
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">My members</h6>
                    <?php do { ?>
                      <a class="dropdown-item" href="<?= autoUrl("swimmers/" . $swimmer['ID']) ?>">
                        <?= htmlspecialchars($swimmer['Name'] . " " . $swimmer['Surname']) ?>
                      </a>
                    <?php } while ($swimmer = $getSwimmers->fetch(PDO::FETCH_ASSOC)); ?>
                  <?php } else { ?>
                    <a class="dropdown-item" href="<?= autoUrl("my-account/add-member") ?>">Link a new member</a>
                  <?php } ?>
                </div>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?= htmlspecialchars(autoUrl('sessions')) ?>">
                  Timetable
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?= htmlspecialchars(autoUrl('sessions/booking')) ?>">
                  Booking
                </a>
              </li>
              <?php if (app()->tenant->getKey('ASA_CLUB_CODE') != 'UOSZ') { ?>
                <li class="nav-item">
                  <a class="nav-link" href="<?= htmlspecialchars(autoUrl("log-books")) ?>">Log Books</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo autoUrl("emergency-contacts") ?>">Emergency Contacts</a>
                </li>
              <?php } ?>
              <?php if ($renewalOpen) { ?>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo autoUrl("renewal") ?>">
                    Membership Renewal
                  </a>
                </li>
              <?php } ?>
            <?php } else { ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="swimmerDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Members &amp; Squads
                </a>
                <div class="dropdown-menu" aria-labelledby="swimmerDropdown">
                  <a class="dropdown-item" href="<?php echo autoUrl("members") ?>">Member directory</a>
                  <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Admin") { ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("members/new") ?>">Add member</a>
                  <?php } ?>
                  <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != "Galas") { ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("squads") ?>">Squads</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("squads/moves") ?>">Squad moves</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("members/access-keys") ?>">Access keys</a>
                  <?php } ?>
                  <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Admin") { ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("renewal") ?>">Membership renewal</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("members/orphaned") ?>">Orphan swimmers</a>
                  <?php } ?>
                  <a class="dropdown-item" href="<?php echo autoUrl("squad-reps") ?>">Squad reps</a>
                  <a class="dropdown-item" href="<?= htmlspecialchars(autoUrl("log-books")) ?>">Log books</a>
                  <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Coach") { ?>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo autoUrl("payments/history/squads/" . date("Y/m")) ?>">
                      Squad Fee Payments, <?= date("F Y") ?>
                    </a>
                    <?php
                    $lm = date("Y/m", strtotime("first day of last month"));
                    $lms = date("F Y", strtotime("first day of last month"));
                    ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("payments/history/squads/" . $lm) ?>">
                      Squad Fee Payments, <?= $lms ?>
                    </a>
                  <?php } ?>
                </div>
              </li>
              <?php if (
                $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Admin" ||
                $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Coach" || $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] ==
                "Committee"
              ) { ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="swimmerDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Registers
                  </a>
                  <div class="dropdown-menu" aria-labelledby="registerDropdown">
                    <a class="dropdown-item" href="<?php echo autoUrl("attendance") ?>">Attendance Home</a>
                    <a class="dropdown-item" href="<?= htmlspecialchars(autoUrl('sessions')) ?>">Timetable</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("attendance/register") ?>">Take Register</a>
                    <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Admin" || $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Committee") { ?>
                      <a class="dropdown-item" href="<?php echo autoUrl("attendance/sessions") ?>">Manage Squad Sessions</a>
                      <a class="dropdown-item" href="<?= autoUrl("attendance/venues") ?>">Manage Venues</a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("attendance/history") ?>">Attendance History</a>
                    <?php if (app()->tenant->isCLS()) { ?>
                      <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/squads/" target="_blank">Timetables</a>
                    <?php } ?>
                  </div>
                </li>
              <?php } ?>
              <?php if (
                $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Admin" ||
                $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Galas"
              ) { ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="usersMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Users
                  </a>
                  <div class="dropdown-menu" aria-labelledby="usersMenu">
                    <a class="dropdown-item" href="<?= autoUrl("users") ?>">
                      All Users
                    </a>
                    <a class="dropdown-item" href="<?= autoUrl("users/add") ?>">
                      Add a user
                    </a>
                    <a class="dropdown-item" href="<?= autoUrl("assisted-registration") ?>">
                      Assisted Account Registration
                    </a>
                    <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Admin') { ?>
                      <a class="dropdown-item" href="<?= autoUrl("payments/user-mandates") ?>">
                        User direct debit mandates
                      </a>
                    <?php } ?>
                  </div>
                </li>
              <?php } ?>
              <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Galas") { ?>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo autoUrl("payments") ?>">Pay</a>
                </li>
              <?php } ?>
              <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Admin") { ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="paymentsAdminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Pay
                  </a>
                  <div class="dropdown-menu" aria-labelledby="paymentsAdminDropdown">
                    <a class="dropdown-item" href="<?php echo autoUrl("payments") ?>">Payments Home</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("payments/history") ?>">Payment Status</a>
                    <!-- New feature coming soon -->
                    <!-- <a class="dropdown-item" href="<?= autoUrl("payments/confirmation") ?>">Payment Confirmation</a> -->
                    <a class="dropdown-item" href="<?php echo autoUrl("payments/extrafees") ?>">Extra Fees</a>
                    <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Admin' || $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Galas') { ?>
                      <a class="dropdown-item" href="<?= autoUrl("payments/galas") ?>">
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
                    <h6 class="dropdown-header">Service Provider Accounts</h6>
                    <a class="dropdown-item" href="https://manage.gocardless.com" target="_blank">
                      GoCardless Dashboard
                    </a>
                    <a class="dropdown-item" href="https://dashboard.stripe.com/" target="_blank">
                      Stripe Dashboard
                    </a>
                    <?php if (getenv('STRIPE') && app()->tenant->getStripeAccount()) { ?>
                      <div class="dropdown-divider"></div>
                      <h6 class="dropdown-header">Payment Cards</h6>
                      <a class="dropdown-item" href="<?= autoUrl("payments/cards") ?>">
                        Credit and Debit Cards
                      </a>
                      <a class="dropdown-item" href="<?= autoUrl("payments/card-transactions") ?>">
                        Card Transactions
                      </a>
                      <a class="dropdown-item" href="<?= autoUrl("payments/cards/add") ?>">
                        Add a Credit or Debit Card
                      </a>
                    <?php } ?>
                  </div>
                </li>
              <?php } ?>
              <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Admin" || $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Coach" || $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Galas") { ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="notifyDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Notify
                  </a>
                  <div class="dropdown-menu" aria-labelledby="notifyDropdown">
                    <a class="dropdown-item" href="<?php echo autoUrl("notify") ?>">Notify Home</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("notify/new") ?>">New Message</a>
                    <a class="dropdown-item" href="<?php echo autoUrl("notify/lists") ?>">Targeted Lists</a>
                    <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Admin") { ?>
                      <a class="dropdown-item" href="<?php echo autoUrl("notify/sms") ?>">SMS Lists</a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("notify/history") ?>">Previous Messages</a>
                  </div>
                </li>
              <?php } ?>
            <?php } ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="galaDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Galas
              </a>
              <div class="dropdown-menu" aria-labelledby="galaDropdown">
                <a class="dropdown-item" href="<?php echo autoUrl("galas") ?>">
                  Gala home
                </a>
                <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Parent") { ?>
                  <a class="dropdown-item" href="<?php echo autoUrl("galas/entergala") ?>">
                    Enter a gala
                  </a>
                  <a class="dropdown-item" href="<?php echo autoUrl("galas/entries") ?>">
                    My entries
                  </a>
                  <?php if ($canPayByCard) { ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("galas/pay-for-entries") ?>">
                      Pay for entries
                    </a>
                  <?php } ?>
                  <?php if ($isTeamManager) { ?>
                    <a class="dropdown-item" href="<?php echo autoUrl("team-managers") ?>">
                      Team manager dashboard
                    </a>
                  <?php } ?>
                <?php } else { ?>
                  <a class="dropdown-item" href="<?php echo autoUrl("galas/addgala") ?>">Add gala</a>
                  <a class="dropdown-item" href="<?php echo autoUrl("galas/entries") ?>">View entries</a>
                  <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Admin' || $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Galas') { ?>
                    <a class="dropdown-item" href="<?= autoUrl("payments/galas") ?>">
                      Charge or refund entries
                    </a>
                  <?php } ?>
                  <a class="dropdown-item" href="<?php echo autoUrl("team-managers") ?>">
                    Team manager dashboard
                  </a>
                <?php } ?>
                <?php if (app()->tenant->isCLS()) { ?>
                  <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/competitions/" target="_blank">Gala website <i class="fa fa-external-link"></i></a>
                  <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/competitions/category/galas/" target="_blank">Upcoming galas <i class="fa fa-external-link"></i></a>
                  <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Parent") { ?>
                    <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/competitions/enteracompetition/guidance/" target="_blank">Help with entries <i class="fa fa-external-link"></i></a>
                  <?php } ?>
                <?php } ?>
                <a class="dropdown-item" href="<?= htmlspecialchars(autoUrl("time-converter")) ?>">
                  Time converter
                </a>
                <a class="dropdown-item" href="<?= htmlspecialchars(autoUrl("galas/all-galas")) ?>">
                  Past and future galas
                </a>
              </div>
            </li>
            <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent' && $haveSquadReps) { ?>
              <li class="nav-item">
                <a class="nav-link" href="<?= autoUrl("squad-reps") ?>">
                  Squad Reps
                </a>
              </li>
            <?php } ?>
            <?php if ($hasNotifyAccess) { ?>
              <li class="nav-item">
                <a class="nav-link" href="<?= autoUrl("notify") ?>">
                  Notify
                </a>
              </li>
            <?php } ?>
            <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Parent" && app()->tenant->getKey('ASA_CLUB_CODE') != 'UOSZ') {
              $hasMandate = userHasMandates($_SESSION['TENANT-' . app()->tenant->getId()]['UserID']);
              $getCountNewMandates = $db->prepare("SELECT COUNT(*) FROM stripeMandates INNER JOIN stripeCustomers ON stripeMandates.Customer = stripeCustomers.CustomerID WHERE stripeCustomers.User = ? AND stripeMandates.MandateStatus != 'inactive';");
              $getCountNewMandates->execute([
                $_SESSION['TENANT-' . app()->tenant->getId()]['UserID']
              ]);
              $hasStripeMandate = $getCountNewMandates->fetchColumn() > 0;
            ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="paymentsParentDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Pay
                </a>
                <div class="dropdown-menu" aria-labelledby="paymentsParentDropdown">
                  <h6 class="dropdown-header">Direct Debit</h6>
                  <a class="dropdown-item" href="<?= autoUrl("payments") ?>">Payments Home</a>
                  <a class="dropdown-item" href="<?= autoUrl("payments/statements") ?>">My Billing History</a>
                  <?php if (app()->tenant->getGoCardlessAccessToken() && $hasMandate && !stripeDirectDebit(true)) { ?>
                    <a class="dropdown-item" href="<?= autoUrl("payments/mandates") ?>">My Bank Account (Old System)</a>
                  <?php } else if (app()->tenant->getGoCardlessAccessToken() && !stripeDirectDebit(true)) { ?>
                    <a class="dropdown-item" href="<?= autoUrl("payments/setup") ?>">
                      Setup a direct debit (Old System)
                    </a>
                  <?php } ?>
                  <?php if (stripeDirectDebit() && $hasStripeMandate) { ?>
                    <a class="dropdown-item" href="<?= autoUrl("payments/direct-debit") ?>">My Bank Account (New System)</a>
                  <?php } else if (stripeDirectDebit()) { ?>
                    <a class="dropdown-item" href="<?= autoUrl("payments/direct-debit/set-up") ?>">
                      Setup a direct debit (New System)
                    </a>
                  <?php } ?>
                  <a class="dropdown-item" href="<?= autoUrl("payments/statements/latest") ?>">My Latest Statement</a>
                  <a class="dropdown-item" href="<?= autoUrl("payments/fees") ?>">My Fees Since Last Bill</a>
                  <div class="dropdown-divider"></div>
                  <h6 class="dropdown-header">Fee information</h6>
                  <a class="dropdown-item" href="<?= autoUrl("payments/squad-fees") ?>">
                    Squad and extra fees
                  </a>
                  <a class="dropdown-item" href="<?= autoUrl("payments/membership-fees") ?>">
                    Annual membership fees
                  </a>
                  <?php if (getenv('STRIPE') && app()->tenant->getStripeAccount()) { ?>
                    <?php if (app()->tenant->getGoCardlessAccessToken()) { ?>
                      <div class="dropdown-divider"></div>
                    <?php } ?>
                    <h6 class="dropdown-header">Card payments</h6>
                    <a class="dropdown-item" href="<?= autoUrl("payments/cards") ?>">
                      Credit and Debit Cards
                    </a>
                    <a class="dropdown-item" href="<?= autoUrl("payments/card-transactions") ?>">
                      Card Transactions
                    </a>
                    <a class="dropdown-item" href="<?= autoUrl("payments/cards/add") ?>">
                      Add a Credit or Debit Card
                    </a>
                  <?php } ?>
                </div>
              </li>
            <?php } ?>
            <?php if (
              $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != "Parent" &&
              $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != "Coach"
            ) { ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="postDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Posts
                </a>
                <div class="dropdown-menu" aria-labelledby="postDropdown">
                  <a class="dropdown-item" href="<?php echo autoUrl("posts") ?>">Home</a>
                  <a class="dropdown-item" href="<?php echo autoUrl("posts/new") ?>">New Page</a>
                  <?php if (isset($allow_edit) && $allow_edit && (($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != "Parent" &&
                    $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != "Coach" && $edit_link != null))) { ?>
                    <a class="dropdown-item" href="<?= $edit_link ?>">Edit Current Page</a>
                  <?php } ?>
                  <?php if (
                    isset($exit_edit) && isset($id) && $exit_edit && $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != "Parent" &&
                    $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != "Coach"
                  ) { ?>
                    <a class="dropdown-item" href="<?= autoUrl("posts/" . $id) ?>">View Page</a>
                  <?php } ?>
                </div>
              </li>
              <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Admin') { ?>
                <li class="nav-item">
                  <a class="nav-link" href="<?= autoUrl("admin") ?>" title="Adminstrative tools">
                    Admin
                  </a>
                </li>
              <?php } ?>
              <!--
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="trialDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    Trials
                  </a>
                  <div class="dropdown-menu" aria-labelledby="trialDropdown">
                    <a class="dropdown-item" href="<?= autoUrl("trials") ?>">Requested Trials</a>
                    <a class="dropdown-item" href="<?= autoUrl("trials/accepted") ?>">Accepted Swimmers</a>
                  </div>
                </li>
                -->
          <?php }
          } ?>
          <?php if (empty($_SESSION['TENANT-' . app()->tenant->getId()]['LoggedIn'])) { ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= htmlspecialchars(autoUrl("login")) ?>">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= htmlspecialchars(autoUrl("covid/contact-tracing")) ?>">COVID-19 Contact Tracing</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= htmlspecialchars(autoUrl("timeconverter")) ?>">Time Converter</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= htmlspecialchars(autoUrl("log-books")) ?>">Log Books</a>
            </li>
            <!-- <li class="nav-item">
              <a class="nav-link" href="<?= htmlspecialchars(autoUrl("services/request-a-trial")) ?>">Request a Trial</a>
            </li> -->
          <?php } ?>
        </ul>
        <?php if (!empty($_SESSION['TENANT-' . app()->tenant->getId()]['LoggedIn'])) {
          $currentUser = app()->user;
          $user_name = preg_replace("/( +)/", '&nbsp;', htmlspecialchars($currentUser->getName())); ?>
          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <?= $user_name ?> <i class="fa fa-user-circle-o" aria-hidden="true"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right">
                <span class="dropdown-item-text">Signed&nbsp;in&nbsp;as&nbsp;<strong><?= $user_name ?></strong></span>
                <div class="dropdown-divider"></div>
                <?php $perms = $currentUser->getPrintPermissions();
                if (sizeof($perms) > 1) { ?>
                  <h6 class="dropdown-header">Switch account mode</h6>
                  <?php foreach ($perms as $perm => $name) { ?>
                    <a class="dropdown-item" href="<?= autoUrl("account-switch?type=" . urlencode($perm)) ?>"><?= htmlspecialchars($name) ?><?php if ($perm == $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel']) { ?> <i class="text-primary fa fa-check-circle fa-fw" aria-hidden="true"></i><?php } ?></a>
                  <?php } ?>
                  <div class="dropdown-divider"></div>
                <?php } ?>
                <?php if (isset(app()->tenant) && app()->tenant->getKey('ASA_CLUB_CODE') == 'UOSZ') { ?>
                  <h6 class="dropdown-header">UoS Swimming and Water Polo</h6>
                  <a class="dropdown-item" target="_blank" href="https://github.com/Swimming-Club-Data-Systems/Membership/issues?q=is%3Aissue+is%3Aopen+label%3AUOSSWPC">Feature Requests and Issues</a>
                  <a class="dropdown-item" target="_blank" href="https://www.facebook.com/groups/69294080736">Facebook Group</a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
                <h6 class="dropdown-header">Account settings</h6>
                <a class="dropdown-item" href="<?= autoUrl("my-account") ?>">Your Profile</a>
                <a class="dropdown-item" href="<?= autoUrl("my-account/email") ?>">Your Email Options</a>
                <a class="dropdown-item" href="<?= autoUrl("my-account/address") ?>">Your Address</a>
                <a class="dropdown-item" href="<?php echo autoUrl("my-account/general") ?>">Your General Options</a>
                <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Parent") { ?>
                  <a class="dropdown-item" href="<?php echo autoUrl("emergency-contacts") ?>">Your Emergency
                    Contacts</a>
                <?php } ?>
                <a class="dropdown-item" href="<?php echo autoUrl("my-account/password") ?>">Your Password</a>
                <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Parent") { ?>
                  <a class="dropdown-item" href="<?php echo autoUrl("my-account/notifyhistory") ?>">Your Message
                    History</a>
                  <a class="dropdown-item" href="<?php echo autoUrl("my-account/add-member") ?>">Add Member</a>
                <?php } ?>
                <a class="dropdown-item" href="<?php echo autoUrl("my-account/loginhistory") ?>">Your Login
                  History</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" target="_blank" href="<?= htmlspecialchars(autoUrl('help-and-support', false)) ?>">Help</a>
                <div class="dropdown-divider"></div>
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
