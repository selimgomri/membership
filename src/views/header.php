<!DOCTYPE html>
<!--

Copyright Chris Heppell & Chester-le-Street ASC 2017 2018. Bootstrap CSS
and JavaScript is Copyright Twitter Inc, 2011-2018, jQuery v3.1.0 is Copyright
jQuery Foundation 2016

Designed by Chris Heppell, www.chrisheppell.uk

Yes! We built this in house. Not many clubs do. We don't cheat.

Chester-le-Street ASC
Swimming Club based in Chester-le-Street, North East England
https://github.com/Chester-le-Street-ASC/

Chester-le-Street ASC is a non profit private members club.

-->
<html lang="en-gb">
  <head>
    <meta charset="utf-8">
    <?php if ($pagetitle != "" || $pagetitle != null)  { ?>
    <title><?php echo htmlspecialchars($pagetitle, ENT_QUOTES, 'UTF-8'); ?> - CLSASC Members</title>
    <?php }
    else { ?>
    <title>CLSASC Membership</title>
    <?php } ?>
    <meta name="description" content="Your Chester-le-Street ASC Account lets
    you make gala entries online and gives you access to all your information
    about your swimmers, including attendance.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,
    user-scalable=no,maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-title" content="CLS ASC Accounts">
    <script async>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-78812259-4', 'auto');
      ga('send', 'pageview');
    </script>
	  <script>var shiftWindow = function() { scrollBy(0, -50) }; if
	  (location.hash) shiftWindow(); window.addEventListener("hashchange",
	  shiftWindow);</script>
    <script src="<? echo autoUrl("/js/tinymce/tinymce.min.js"); ?>"></script>
    <link rel="stylesheet preload"
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,700|Roboto+Mono">
    <link rel="stylesheet preload" href="<?php echo autoUrl("css/chester-2.0.12.css") ?>">
    <link rel="stylesheet"
    href="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/font-awesome/css/font-awesome.min.css">
    <link rel="apple-touch-icon" href="<https://www.chesterlestreetasc.co.uk/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="76x76" href="https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad.png">
    <link rel="apple-touch-icon" sizes="120x120" href="https://www.chesterlestreetasc.co.uk/apple-touch-icon-iphone-retina.png">
    <link rel="apple-touch-icon" sizes="152x152" href="https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad-retina.png">
    <link rel="mask-icon" href="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/img/chesterIcon.svg" color="#bd0000">
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <style>.logo {background:
    url(https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/img/chesterLogo.svg)
    left center no-repeat;}
    .badge {
      font-family: "Helvetica", "Helvetica Neue", "Arial", "Roboto", san-serif;
    }
    .table-nomargin {
      margin: 0 -1rem -1rem -1rem;
      width: auto !important;
    }
    .nav-scroller .nav-underline .nav-link:hover, .nav-scroller .nav-underline .nav-link:focus {
      background: #e9ecef;
    }
    .nav-scroller .nav-underline .nav-link:active {
      background: #dee2e6;
    }
    </style>

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>
<body class="bg-light account-body">
  <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-primary
  d-print-none justify-content-between" role="navigation">
      <a class="navbar-brand" href="<?php echo autoUrl("") ?>">
        <?php if ((empty($_SESSION['LoggedIn']) || $_SESSION['AccessLevel'] ==
        "Parent")) { ?>Membership<?php } else { ?>CLS MMS<?php } ?>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse"
      data-target="#chesterNavbar" aria-controls="chesterNavbar"
      aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

	  <div class="collapse navbar-collapse offcanvas-collapse" id="chesterNavbar">
		<ul class="navbar-nav mr-auto">
		<?php if (!empty($_SESSION['LoggedIn'])) { ?>
		  <li class="nav-item">
			  <a class="nav-link" href="<?php echo autoUrl("") ?>">Dashboard</a>
		  </li>
		  <li class="nav-item">
			  <a class="nav-link" href="<?php echo autoUrl("myaccount") ?>">My Account</a>
		  </li>
      <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
      <li class="nav-item">
			  <a class="nav-link" href="<?php echo autoUrl("swimmers") ?>">My Swimmers</a>
		  </li>
      <?php }
      else { ?>
        <li class="nav-item">
  			  <a class="nav-link" href="<?php echo autoUrl("swimmers") ?>">Swimmers</a>
  		  </li>
        <li class="nav-item">
  			  <a class="nav-link" href="<?php echo autoUrl("squads") ?>">Squads</a>
  		  </li>
        <?php if ($_SESSION['AccessLevel'] == "Admin" ||
        $_SESSION['AccessLevel'] == "Coach" || $_SESSION['AccessLevel'] ==
        "Committee") { ?>
        <li class="nav-item">
  			  <a class="nav-link" href="<?php echo autoUrl("attendance") ?>">Attendance</a>
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
  			  <a class="nav-link" href="<?php echo autoUrl("payments") ?>">Payments</a>
  		  </li>
        <?php } ?>
        <?php if ($_SESSION['AccessLevel'] == "Admin") { ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="paymentsAdminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Payments
          </a>
          <div class="dropdown-menu" aria-labelledby="paymentsAdminDropdown">
            <a class="dropdown-item" href="<?php echo autoUrl("payments") ?>">Payments Home</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="https://manage.gocardless.com" target="_blank">
              GoCardless Live
            </a>
            <a class="dropdown-item" href="https://manage-sandbox.gocardless.com" target="_blank">
              GoCardless Sandbox
            </a>
          </div>
        </li>
        <?php } ?>
        <?php if ($_SESSION['AccessLevel'] == "Admin" || $_SESSION['AccessLevel'] == "Coach") { ?>
        <li class="nav-item">
  			  <a class="nav-link" href="<?php echo autoUrl("notify") ?>">Notify</a>
  		  </li>
        <?php } ?>
      <?php } ?>
      <li class="nav-item">
			  <a class="nav-link" href="<?php echo autoUrl("galas") ?>">Galas</a>
		  </li>
      <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
        <li class="nav-item">
          <a class="nav-link" data-toggle="modal"
          data-target="#paymentsBetaModal" href="#paymentsBetaModal">
            Payments <span class="badge badge-secondary">BETA</span>
          </a>
        </li>
        <li class="nav-item">
  			  <a class="nav-link" target="_blank"
  			  href="https://store.chesterlestreetasc.co.uk/">
            Store
          </a>
  		  </li>
      <? } ?>
		  <?php } ?>
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
    <a class="btn btn-outline-light my-2 my-sm-0" href="<?php echo autoUrl("logout") ?>">Logout</a>
    <?php } ?>
	  </div>

  </nav>

  <noscript>
    <div class="alert alert-danger">
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
