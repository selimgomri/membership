<?
$container_class;
if (isset($fluidContainer) && $fluidContainer == true) {
  $container_class = "container-fluid";
} else {
  $container_class = "container";
}?>
<!DOCTYPE html>
<!--

Copyright Chris Heppell & Chester-le-Street ASC 2016 - 2018.
Bootstrap CSS and JavaScript is Copyright Twitter Inc 2011-2018
jQuery v3.1.0 is Copyright jQuery Foundation 2016

Designed by Chris Heppell, www.chrisheppell.uk

Yes! We built this in house. Not many clubs do. We don't cheat.

Chester-le-Street ASC
Swimming Club based in Chester-le-Street, North East England
https://github.com/Chester-le-Street-ASC/

Chester-le-Street ASC is a non profit unincorporated association.

-->
<html lang="en-gb">
  <head>
    <meta charset="utf-8">
    <?php if ($pagetitle != "" || $pagetitle != null)  { ?>
    <title><?php echo htmlspecialchars($pagetitle, ENT_QUOTES, 'UTF-8'); ?> - <?=CLUB_SHORT_NAME?> Membership</title>
    <?php }
    else { ?>
    <title><?=CLUB_SHORT_NAME?> Membership</title>
    <?php } ?>
    <meta name="description" content="Your <?=CLUB_NAME?> Account lets you make gala entries online and gives you access to all your information about your swimmers, including attendance.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,
    user-scalable=no,maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-title" content="CLS ASC Accounts">
    <meta name="format-detection" content="telephone=no">
    <meta name="googlebot" content="noarchive, nosnippet">
    <meta name="X-CLSW-System" content="Membership">
    <meta name="twitter:site" content="@clsasc">
    <meta name="twitter:creator" content="@clsasc">
    <meta name="og:type" content="website">
    <meta name="og:locale" content="en_GB">
    <meta name="og:site_name" content="Chester-le-Street ASC Account">
    <meta name="X-CLSW-Tracking" content="yes">
    <script async>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-78812259-4', 'auto');
      <? if (isset($_SESSION['LoggedIn'])) { ?>
      ga('set', 'userId', <?= $_SESSION['UserID'] ?>);
      ga('send', 'event', 'authentication', 'user-id available');
      <? } else { ?>
      ga('send', 'pageview');
      <? } ?>
    </script>
	  <script>var shiftWindow = function() { scrollBy(0, -50) }; if
	  (location.hash) shiftWindow(); window.addEventListener("hashchange",
	  shiftWindow);</script>
    <script src="<? echo autoUrl("/js/tinymce/tinymce.min.js"); ?>"></script>
    <link rel="stylesheet preload"
    href="https://fonts.googleapis.com/css?family=Cabin+Condensed:300,400,400i,600,700|Roboto+Mono|Merriweather:400,600">
    <link rel="stylesheet preload" href="<?php echo autoUrl("css/chester-2.0.17.css") ?>">
    <link rel="stylesheet"
    href="<?php echo autoUrl("css/font-awesome/css/font-awesome.min.css")?>">
    <link rel="apple-touch-icon" href="<https://www.chesterlestreetasc.co.uk/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="76x76" href="https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad.png">
    <link rel="apple-touch-icon" sizes="120x120" href="https://www.chesterlestreetasc.co.uk/apple-touch-icon-iphone-retina.png">
    <link rel="apple-touch-icon" sizes="152x152" href="https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad-retina.png">
    <link rel="mask-icon" href="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/img/chesterIcon.svg" color="#bd0000">
    <script src='https://www.google.com/recaptcha/api.js'></script>

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>
  <? $bg = "bg-light";
  if ($use_white_background) {
    $bg = "bg-white";
  }
  ?>
<body class="<?=$bg?> account--body">

  <div class="sr-only sr-only-focusable">
    <a href="#maincontent">Skip to main content</a>
  </div>

  <div class="d-print-none">

    <!--<div class="text-dark py-2 top-bar bg-primary-dark" style="font-size:0.875rem;">
      <div class="<?=$container_class?> d-flex">
        <div class="mr-auto hide-a-underline">
          <span class="mr-2">
            <a href="https://www.twitter.com/CLSASC" target="_blank" class="text-dark" title="Twitter">
              <i class="fa fa-twitter fa-fw" aria-hidden="true"></i>
              <span class="sr-only">Chester-le-Street ASC on Twitter</span>
            </a>
          </span>

          <span class="mr-2">
            <a href="https://www.facebook.com/CLSASC" target="_blank" class="text-dark" title="Facebook">
              <i class="fa fa-facebook fa-fw" aria-hidden="true"></i>
              <span class="sr-only">Chester-le-Street ASC on Facebook</span>
            </a>
          </span>

          <span>
            <a href="https://www.chesterlestreetasc.co.uk/feed/" target="_blank" class="text-dark" title="Really Simple Syndication">
              <i class="fa fa-rss fa-fw" aria-hidden="true"></i>
              <span class="sr-only">Chester-le-Street ASC RSS Feeds</span>
            </a>
          </span>
        </div>

        <div class="ml-2 top-bar">
          <span>
            <a href="https://www.chesterlestreetasc.co.uk" class="text-dark" title="Club Website">
              Website
            </a>
          </span>
        </div>

        <? if ($_SESSION['LoggedIn']) { ?>
        <div class="ml-2 top-bar">
          <span>
            <a href="https://account.chesterlestreetasc.co.uk" class="text-dark" title="Your Club Membership Account">
              My Account
            </a>
          </span>
        </div>
        <? } else { ?>
        <div class="ml-2 top-bar">
          <span>
            <a href="https://account.chesterlestreetasc.co.uk" class="text-dark" title="Sign in to your Club Membership Account">
              Sign in
            </a>
          </span>
        </div>
        <? } ?>

        <div class="ml-2 top-bar d-lg-none">
          <span>
            <a data-toggle="collapse" href="#mobSearch" role="button" aria-expanded="false" aria-controls="mobSearch" class="text-dark" title="Search the site">
              Search
            </a>
          </span>
        </div>
      </div>
    </div>

    <div class="collapse" id="mobSearch">
      <div class="text-dark py-3 d-lg-none bg-primary-darker">
        <form class="container" action="https://www.chesterlestreetasc.co.uk" id="head-search" method="get">
          <label for="s" class="sr-only">Search</label>
          <div class="input-group">
            <input class="form-control bg-primary text-dark border-primary" id="s" name="s" placeholder="Search the site" type="search">
            <div class="input-group-append">
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-search"></i>
                <span class="sr-only">
                  Search
                </span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div> -->

    <div class="text-dark py-3 d-none d-lg-flex bg-primary-darker">
      <div class="<?=$container_class?>">
        <div class="row align-items-center">
          <div class="col">
            <a class="logowhite" href="<?=autoUrl("")?>" title="Membership Dashboard">
            <img src="<?=autoUrl("img/logo.jpg")?>" style="max-height:71px;"></a>
          </div>
          <div class="col d-none d-lg-flex">
            <p class="lead mb-0 ml-auto text-right">Club Membership System</p>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-primary">
      <div class="<?=$container_class?>">
    <nav class="navbar navbar-expand-lg navbar-light bg-primary
    d-print-none justify-content-between px-0" role="navigation">

        <a class="navbar-brand d-lg-none" href="<?php echo autoUrl("") ?>">
          <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
            <img src="<? echo autoUrl("img/chesterIcon.svg"); ?>" width="20" height="20"> My Membership
          <?php } else { ?>
            <img src="<? echo autoUrl("img/chesterIcon.svg"); ?>" width="20" height="20"> Club Membership
          <?php } ?>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse"
        data-target="#chesterNavbar" aria-controls="chesterNavbar"
        aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

  	  <div class="collapse navbar-collapse offcanvas-collapse" id="chesterNavbar">
      <? if (!user_needs_registration($_SESSION['UserID'])) { ?>
  		<ul class="navbar-nav mr-auto">
  		<?php if (!empty($_SESSION['LoggedIn'])) { ?>
        <li class="nav-item">
  			  <a class="nav-link" href="<?php echo autoUrl("") ?>">Home</a>
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
              <a class="dropdown-item" href="<?php echo autoUrl("swimmers") ?>">Swimmers Home</a>
              <? if (mysqli_num_rows($getSwimmers) > 0) { ?>
              <div class="dropdown-divider"></div>
              <h6 class="dropdown-header">My Swimmers</h6>
              <? for ($i = 0; $i < mysqli_num_rows($getSwimmers); $i++) {
                $getSwimmerRow = mysqli_fetch_array($getSwimmers, MYSQLI_ASSOC); ?>
                <a class="dropdown-item" href="<?php echo autoUrl("swimmers/" .
                $getSwimmerRow['MemberID']) ?>"><? echo
                $getSwimmerRow['MForename'] . " " . $getSwimmerRow['MSurname'];
                ?></a>
              <? } ?>
              <? } else { ?>
                <a class="dropdown-item" href="<?php echo autoUrl("myaccount/addswimmer") ?>">Add Swimmers</a>
              <? } ?>
            </div>
          </li>
        <li class="nav-item">
  			  <a class="nav-link" href="<?php echo autoUrl("emergencycontacts") ?>">Emergency Contacts</a>
  		  </li>
        <?php }
        else { ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="swimmerDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Swimmers &amp; Squads
            </a>
            <div class="dropdown-menu" aria-labelledby="swimmerDropdown">
              <a class="dropdown-item" href="<?php echo autoUrl("swimmers")?>">Swimmer Directory</a>
              <? if ($_SESSION['AccessLevel'] == "Admin") { ?>
              <a class="dropdown-item" href="<?php echo autoUrl("swimmers/addmember")?>">Add Member</a>
              <? } ?>
              <?php if ($_SESSION['AccessLevel'] != "Galas") { ?>
              <a class="dropdown-item" href="<?php echo autoUrl("squads")?>">Squads</a>
          		<a class="dropdown-item" href="<?php echo autoUrl("squads/moves")?>">Squad Moves</a>
              <? } ?>
              <a class="dropdown-item" href="<?php echo autoUrl("swimmers/accesskeys")?>">Access Keys</a>
              <? if ($_SESSION['AccessLevel'] == "Admin") { ?>
              <a class="dropdown-item" href="<?php echo autoUrl("renewal")?>">Membership Renewal</a>
              <a class="dropdown-item" href="<?php echo autoUrl("swimmers/orphaned")?>">Orphan Swimmers</a>
              <? } ?>
              <? if ($_SESSION['AccessLevel'] == "Coach") { ?>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?php echo autoUrl("payments/history/squads/" . date("Y/m")) ?>">
                Squad Fee Payments, <?=date("F Y")?>
              </a>
              <?
              $lm = date("Y/m", strtotime("first day of last month"));
              $lms = date("F Y", strtotime("first day of last month"));
              ?>
              <a class="dropdown-item" href="<?php echo autoUrl("payments/history/squads/" . $lm) ?>">
                Squad Fee Payments, <?=$lms?>
              </a>
              <? } ?>
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
              <a class="dropdown-item" href="<?php echo autoUrl("attendance")?>">Attendance Home</a>
              <a class="dropdown-item" href="<?php echo autoUrl("attendance/register")?>">Take Register</a>
              <?php if ($_SESSION['AccessLevel'] == "Admin" || $_SESSION['AccessLevel'] == "Committee") {?>
              <a class="dropdown-item" href="<?php echo autoUrl("attendance/sessions")?>">Manage Sessions</a>
              <?php } ?>
              <a class="dropdown-item" href="<?php echo autoUrl("attendance/history")?>">Attendance History</a>
              <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/squads/" target="_blank">Timetables</a>
            </div>
    		  </li>
          <?php } ?>
          <?php if ($_SESSION['AccessLevel'] == "Admin" ||
          $_SESSION['AccessLevel'] == "Galas") { ?>
          <li class="nav-item">
    			  <a class="nav-link" href="<?php echo autoUrl("users") ?>">Users</a>
    		  </li>
          <?php } ?>
          <?php if ($_SESSION['AccessLevel'] == "Galas") { ?>
          <li class="nav-item">
    			  <a class="nav-link" href="<?php echo autoUrl("payments") ?>">Pay</a>
    		  </li>
          <?php } ?>
          <?php if ($_SESSION['AccessLevel'] == "Admin") { ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="paymentsAdminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Pay
            </a>
            <div class="dropdown-menu" aria-labelledby="paymentsAdminDropdown">
              <a class="dropdown-item" href="<?php echo autoUrl("payments") ?>">Payments Home</a>
              <a class="dropdown-item" href="<?php echo autoUrl("payments/history") ?>">Payment Status</a>
              <a class="dropdown-item" href="<?php echo autoUrl("payments/extrafees")?>">Extra Fees</a>
              <div class="dropdown-divider"></div>
              <h6 class="dropdown-header"><? echo date("F Y"); ?></h6>
              <a class="dropdown-item" href="<?php echo autoUrl("payments/history/squads/" . date("Y/m")) ?>">
                Squad Fees
              </a>
              <a class="dropdown-item" href="<?php echo autoUrl("payments/history/extras/" . date("Y/m")) ?>">
                Extra Fees
              </a>
              <?
              $lm = date("Y/m", strtotime("first day of last month"));
              $lms = date("F Y", strtotime("first day of last month"));
              ?>
              <h6 class="dropdown-header"><? echo $lms; ?></h6>
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
            </div>
          </li>
          <?php } ?>
          <?php if ($_SESSION['AccessLevel'] == "Admin" || $_SESSION['AccessLevel'] == "Coach") { ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="notifyDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Notify
            </a>
            <div class="dropdown-menu" aria-labelledby="notifyDropdown">
              <a class="dropdown-item" href="<?php echo autoUrl("notify")?>">Notify Home</a>
          		<a class="dropdown-item" href="<?php echo autoUrl("notify/newemail")?>">New Message</a>
              <a class="dropdown-item" href="<?php echo autoUrl("notify/lists")?>">Targeted Lists</a>
              <? if ($_SESSION['AccessLevel'] == "Admin") { ?>
              <a class="dropdown-item" href="<?php echo autoUrl("notify/sms")?>">SMS Lists</a>
          		<a class="dropdown-item" href="<?php echo autoUrl("notify/email")?>">Pending Messages</a>
              <? } ?>
              <a class="dropdown-item" href="<?php echo autoUrl("notify/history")?>">Previous Messages</a>
            </div>
          </li>
          <?php } ?>
        <?php } ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="galaDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Galas
          </a>
          <div class="dropdown-menu" aria-labelledby="galaDropdown">
            <a class="dropdown-item" href="<?php echo autoUrl("galas")?>">Gala Home</a>
            <?php if ($_SESSION['AccessLevel'] == "Parent") {?>
            <a class="dropdown-item" href="<?php echo autoUrl("galas/entergala")?>">Enter a Gala</a>
            <a class="dropdown-item" href="<?php echo autoUrl("galas/entries")?>">My Entries</a>
            <?php } else {?>
            <a class="dropdown-item" href="<?php echo autoUrl("galas/addgala")?>">Add Gala</a>
            <a class="dropdown-item" href="<?php echo autoUrl("galas/entries")?>">View Entries</a>
            <?php } ?>
            <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/competitions/" target="_blank">Gala Website <i class="fa fa-external-link"></i></a>
            <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/competitions/category/galas/" target="_blank">Upcoming Galas <i class="fa fa-external-link"></i></a>
            <?php if ($access == "Parent") {?>
            <a class="dropdown-item" href="https://www.chesterlestreetasc.co.uk/competitions/enteracompetition/guidance/" target="_blank">Help with Entries <i class="fa fa-external-link"></i></a>
            <? } ?>
          </div>
  		  </li>
        <?php if (false)/*($_SESSION['AccessLevel'] == "Parent")*/ { ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="paymentsParentDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Pay
            </a>
            <div class="dropdown-menu" aria-labelledby="paymentsParentDropdown">
              <a class="dropdown-item" href="<?php echo autoUrl("payments") ?>">Payments Home</a>
              <a class="dropdown-item" href="<?php echo autoUrl("payments/fees") ?>">Extra Fees this month</a>
              <a class="dropdown-item" href="<?php echo autoUrl("payments/transactions")?>">Billing History</a>
              <a class="dropdown-item" href="<?php echo autoUrl("payments/transactions")?>">My Bank Account</a>
            </div>
          </li>
          <li class="nav-item">
    			  <a class="nav-link" target="_blank"
    			  href="https://store.chesterlestreetasc.co.uk/">
              Store
            </a>
    		  </li>
        <? } ?>
        <? if ($_SESSION['AccessLevel'] != "Parent" &&
    		$_SESSION['AccessLevel'] != "Coach") { ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="postDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Posts
          </a>
          <div class="dropdown-menu" aria-labelledby="postDropdown">
            <a class="dropdown-item" href="<?php echo autoUrl("posts")?>">Home</a>
        		<a class="dropdown-item" href="<?php echo autoUrl("posts/new")?>">New Page</a>
        		<? if ($allow_edit && $_SESSION['AccessLevel'] != "Parent" &&
        		$_SESSION['AccessLevel'] != "Coach") { ?>
        		<a class="dropdown-item" href="<?=app('request')->curl?>edit">Edit Current Page</a>
        		<? } ?>
        		<? if ($exit_edit && $_SESSION['AccessLevel'] != "Parent" &&
        		$_SESSION['AccessLevel'] != "Coach") { ?>
        		<a class="dropdown-item" href="<?=autoUrl("posts/" . $id)?>">View Page</a>
        		<? } ?>
          </div>
        </li>
  		  <?php }
          } ?>
  		  <?php if (empty($_SESSION['LoggedIn'])) { ?>
        <li class="nav-item">
  			  <a class="nav-link" href="<?php echo autoUrl("") ?>">Login</a>
  		  </li>
        <li class="nav-item">
  			  <a class="nav-link" href="<?php echo autoUrl("register") ?>">Create Account</a>
  		  </li>
        <?php } ?>
  		</ul>
      <?php if (!empty($_SESSION['LoggedIn'])) { ?>
      <? $user_name = str_replace(' ', '&nbsp;', htmlspecialchars(getUserName($_SESSION['UserID']))); ?>
      <ul class="navbar-nav">
        <!--<a class="btn btn-sm btn-outline-light my-2 my-sm-0" href="<?php echo autoUrl("logout") ?>">Logout</a>-->
        <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          <?= $user_name ?> <i class="fa fa-user-circle-o" aria-hidden="true"></i>
        </a>
          <div class="dropdown-menu dropdown-menu-right">
            <span class="dropdown-item-text">Signed&nbsp;in&nbsp;as&nbsp;<strong><?= $user_name ?></strong></span>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="<?php echo autoUrl("myaccount") ?>">Your Profile</a>
            <a class="dropdown-item" href="<?php echo autoUrl("myaccount/email") ?>">Your Email Options</a>
            <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
              <a class="dropdown-item" href="<?php echo autoUrl("emergencycontacts") ?>">Your Emergency Contacts</a>
            <? } ?>
            <a class="dropdown-item" href="<?php echo autoUrl("myaccount/password") ?>">Your Password</a>
            <? if ($_SESSION['AccessLevel'] == "Parent") { ?>
            <a class="dropdown-item" href="<?php echo autoUrl("myaccount/notifyhistory") ?>">Your Message History</a>
            <a class="dropdown-item" href="<?php echo autoUrl("myaccount/addswimmer") ?>">Add a Swimmer</a>
            <? } ?>
            <a class="dropdown-item" href="<?php echo autoUrl("myaccount/loginhistory") ?>">Your Login History</a>
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

  <? if (isset($_SESSION['UserSimulation'])) { ?>
    <div class="bg-secondary text-dark box-shadow mb-3 py-2 d-print-none">
      <div class="<?=$container_class?>">
        <p class="mb-0">
          <strong>
            You are in User Simulation Mode simulating <?=
            $_SESSION['UserSimulation']['SimUserName'] ?>
          </strong>
        </p>
        <p class="mb-0">
          <a href="<?=autoUrl("users/simulate/exit")?>" class="text-dark">
            Exit User Simulation Mode
          </a>
        </p>
      </div>
    </div>
  <? } ?>

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
