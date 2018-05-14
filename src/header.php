<?php include_once "database.php" ?>
<!DOCTYPE html>
<!-- 	Copyright Chris Heppell & Chester-le-Street ASC 2017. Bootstrap CSS and JavaScript is Copyright Twitter Inc, 2011-2017, jQuery v3.1.0 is Copyright jQuery Foundation 2016
		Designed by Chris Heppell, www.heppellit.com
        Yes! We built this in house. Not many clubs do. We don't cheat.	-->
<html lang="en-gb">
  <head>
    <meta charset="utf-8">
	    <title><?php echo htmlspecialchars($pagetitle, ENT_QUOTES, 'UTF-8'); ?> - CLSASC Members</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no,maximum-scale=1">
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
	  <script>var shiftWindow = function() { scrollBy(0, -50) }; if (location.hash) shiftWindow(); window.addEventListener("hashchange", shiftWindow);</script>
    <link rel="stylesheet preload" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,700">
    <link rel="stylesheet preload" href="<?php echo autoUrl("css/chester-2.0.9.css") ?>">
    <link rel="stylesheet" href="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/font-awesome/css/font-awesome.min.css">
    <link rel="apple-touch-icon" href="<https://www.chesterlestreetasc.co.uk/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="76x76" href="https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad.png">
    <link rel="apple-touch-icon" sizes="120x120" href="https://www.chesterlestreetasc.co.uk/apple-touch-icon-iphone-retina.png">
    <link rel="apple-touch-icon" sizes="152x152" href="https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad-retina.png">
    <link rel="mask-icon" href="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/img/chesterIcon.svg" color="#bd0000">
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <style>.logo {background: url(https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/img/chesterLogo.svg) left center no-repeat;}</style>

	<style>
    body {
      padding-top: 4.5rem;
    }
    .box-shadow {
      box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .10);
    }
    .nav-scroller {
      position: relative;
      z-index: 2;
      height: 2.75rem;
      overflow-y: hidden;
      margin: -1rem 0 1rem 0;
    }
    .nav-scroller .nav {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;
      -ms-flex-wrap: nowrap;
      flex-wrap: nowrap;
      padding-bottom: 1rem;
      margin-top: -1px;
      overflow-x: auto;
      color: rgba(255, 255, 255, .75);
      text-align: center;
      white-space: nowrap;
      -webkit-overflow-scrolling: touch;
    }
    .nav-underline .nav-link {
      padding-top: .75rem;
      padding-bottom: .75rem;
      line-height: 1.35rem;
    }
    @media print {
      .nav-scroller {
        display: none !important;
      }
    }
    .ajaxPlaceholder {
      padding: 10rem 0;
      margin: 0 0 1rem 0;
      text-align: center;
      background: #efefef;
    }
    .galaEntryTimes {
      display: -ms-grid;
      display: grid;
      -ms-grid-columns: (1fr)[1];
      grid-template-columns: repeat(1, 1fr);
      grid-column-gap: 20px;
      -webkit-column-gap: 20px;
      column-gap: 20px;
      grid-row-gap: 1rem;
      row-gap: 1rem;
    }
    @media (min-width: 992px) {
      .galaEntryTimes {
        -ms-grid-columns: (1fr)[2];
        grid-template-columns: repeat(2, 1fr);
      }
    }
    .chart {
      max-width: 100%;
    }
    #cookie-law {
      margin: -1rem 0 1rem 0;
    }
	</style>

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>
<body class="bg-light">
  <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-primary d-print-none justify-content-between" role="navigation">
      <a class="navbar-brand" href="<?php echo autoUrl("index.php") ?>">
        <?php if ((empty($_SESSION['LoggedIn']) || $_SESSION['AccessLevel'] == "Parent")) { ?>Membership<?php } else { ?>MMS<?php } ?>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#chesterNavbar" aria-controls="chesterNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

	  <div class="collapse navbar-collapse offcanvas-collapse" id="chesterNavbar">
		<ul class="navbar-nav mr-auto">
		<?php if (!empty($_SESSION['LoggedIn'])) { ?>
		  <li class="nav-item">
			  <a class="nav-link" href="<?php echo autoUrl("index.php") ?>">Dashboard</a>
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
  			  <a class="nav-link" href="<?php echo autoUrl("swimmers") ?>">Member Directory</a>
  		  </li>
        <li class="nav-item">
  			  <a class="nav-link" href="<?php echo autoUrl("squads") ?>">Squads</a>
  		  </li>
        <?php if ($_SESSION['AccessLevel'] == "Admin" || $_SESSION['AccessLevel'] == "Coach" || $_SESSION['AccessLevel'] == "Committee") { ?>
        <li class="nav-item">
  			  <a class="nav-link" href="<?php echo autoUrl("attendance") ?>">Attendance</a>
  		  </li>
        <?php } ?>
        <?php if ($_SESSION['AccessLevel'] == "Admin" || $_SESSION['AccessLevel'] == "Galas") { ?>
        <li class="nav-item">
  			  <a class="nav-link" href="<?php echo autoUrl("users") ?>">Users</a>
  		  </li>
        <?php } ?>
      <?php } ?>
      <li class="nav-item">
			  <a class="nav-link" href="<?php echo autoUrl("galas") ?>">Galas</a>
		  </li>
		  <?php } ?>
		  <?php if (empty($_SESSION['LoggedIn'])) { ?>
      <li class="nav-item">
			  <a class="nav-link" href="<?php echo autoUrl("login.php") ?>">Login</a>
		  </li>
      <li class="nav-item">
			  <a class="nav-link" href="<?php echo autoUrl("register.php") ?>">Create Account</a>
		  </li>
      <?php } ?>
      <!--<li class="nav-item">
			<a class="nav-link" href="https://store.chesterlestreetasc.co.uk/">Store</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="https://github.com/Chester-le-Street-ASC">GitHub</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="/software/sendmail">Notify</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link disabled" href="#">Payment Systems</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link disabled" href="#">Account Settings</a>
		  </li>-->
		</ul>
    <?php if (!empty($_SESSION['LoggedIn'])) { ?>
    <a class="btn btn-outline-light my-2 my-sm-0" href="<?php echo autoUrl("logout.php") ?>">Logout</a>
    <?php } ?>
	  </div>

  </nav>

  <!--<header class="container">
    <div class="row d-print-none align-items-center" style="margin-top:0px">
      <div class="col-md-8">
  	  <h1 class="mb-0">
        <a class="logo" alt="Chester-le-Street ASC" href="<?php echo autoUrl("") ?>"></a><span class="sr-only">"Chester&#8209;le&#8209;Street&nbsp;ASC</span>
      </h1>
  	</div>
  	<div class="col d-none d-md-block">
  	  <p class="slogan"><a href="https://www.chesterlestreetasc.co.uk/beta" target="_blank" class="badge badge-secondary">BETA</a></p>
  	</div>
    </div>
    <div class="row d-none d-print-block" style="margin-top:-60px">
      <div class="col-6">
        <img class="img-fluid" src="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/img/chesterLogo.svg"  alt="Chester-le-Street ASC Logo">
      </div>
      <div class="col-6 d-print-none">
    	  <p class="slogan"><a href="https://en.wikipedia.org/wiki/Software_release_life_cycle#Beta" target="_blank" class="badge badge-secondary">BETA</a></p>
    	</div>
    </div>
  	<hr>-->
      <!--[if !IE]><div class="alert alert-danger"><strong>Unsupported Browser</strong><br>You're using an unsupported browser and this website may not work properly with it. <a href="http://browsehappy.com/" class="alert-link" target="_blank">Upgrade your browser today <i class="fa fa-external-link" aria-hidden="true"></i> </a> or <a href="https://www.google.com/chrome/browser/desktop/index.html" class="alert-link" target="_blank">install Google Chrome <i class="fa fa-external-link" aria-hidden="true"></i> </a> to better experience this site.</p></div><hr><![endif]-->
      <noscript>
      <div class="alert alert-danger">
        <p class="mb-0"><strong>JavaScript is disabled or not supported</strong>
  		  <br>
  		  It looks like you've got JavaScript disabled or your browser does not support it. JavaScript is essential for our website to properly so we recommend you enable it or upgrade to a browser which supports it as soon as possible. <a href="http://browsehappy.com/" class="alert-link" target="_blank">Upgrade your browser today <i class="fa fa-external-link" aria-hidden="true"></i></a></p>
      </div>
      <hr>
    </noscript>
  <!--</header>-->
