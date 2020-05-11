<?php

$stylesheet = "";
try {
  $hash = file_get_contents(BASE_PATH . 'cachebuster.json');
  $hash = json_decode($hash);
  $hash = $hash->resourcesHash;
  $stylesheet = autoUrl('public/compiled/css/generic.' . $hash . '.min.css');
} catch (Exception $e) {
  $stylesheet = autoUrl('public/compiled/css/generic.css');
}

if (env('CUSTOM_CSS_PATH')) {
  $stylesheet = env('CUSTOM_CSS_PATH');
}

header('Link: <' . autoUrl($stylesheet) . '>; rel=preload; as=style');
header('Link: <' . autoUrl($fa) . '>; rel=preload; as=style');

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

Chester-le-Street ASC
Swimming Club based in Chester-le-Street, North East England
https://github.com/Chester-le-Street-ASC/

web@chesterlestreetasc.co.uk

https://corporate.myswimmingclub.co.uk

Chester-le-Street ASC is a non profit unincorporated association.

-->
<html lang="en-gb">

<head>
  <meta charset="utf-8">
  <?php if (isset($pagetitle) && ($pagetitle != "" || $pagetitle != null))  { ?>
    <title><?=$pagetitle?> - <?=htmlspecialchars(app()->tenant->getKey('CLUB_NAME'))?> Membership</title>
  <?php } else { ?>
  <title><?=htmlspecialchars(app()->tenant->getKey('CLUB_NAME'))?> Membership</title>
  <?php } ?>
  <meta name="description"
    content="Your <?=app()->tenant->getKey('CLUB_NAME')?> Account lets you make gala entries online and gives you access to all your information about your swimmers, including attendance.">
  <meta name="viewport" content="width=device-width, initial-scale=1.0,
    user-scalable=no,maximum-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="apple-mobile-web-app-title" content="<?=htmlspecialchars(app()->tenant->getKey('CLUB_SHORT_NAME'))?> Accounts">
  <meta name="format-detection" content="telephone=no">
  <meta name="googlebot" content="noarchive, nosnippet">
  <meta name="X-CLSW-System" content="Membership">
  <meta name="og:type" content="website">
  <meta name="og:locale" content="en_GB">
  <meta name="og:site_name" content="<?=htmlspecialchars(app()->tenant->getKey('CLUB_NAME'))?> Account">
  <link rel="manifest" href="<?=autoUrl("manifest.webmanifest")?>">
  <?php
    // Check if user has opted out of tracking or has DNT headers set before serving Google Analytics
    if (env('GOOGLE_ANALYTICS_ID') && (!$_SESSION['DisableTrackers'] && !(isset($_SERVER['HTTP_DNT']) && $_SERVER['HTTP_DNT'] == 1))) {
    ?>
  <meta name="X-SCDS-Membership-Tracking" content="yes">
  <script async>
  (function(i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r;
    i[r] = i[r] || function() {
      (i[r].q = i[r].q || []).push(arguments)
    }, i[r].l = 1 * new Date();
    a = s.createElement(o),
      m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
  })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
  ga('create', '<?=htmlspecialchars(env('GOOGLE_ANALYTICS_ID'))?>', 'auto');
  <?php if (isset($_SESSION['LoggedIn'])) { ?>
  ga('set', 'userId', '<?=$_SESSION['UserID']?>');
  ga('send', 'event', 'authentication', 'user-id available');
  <?php } else { ?>
  ga('send', 'pageview');
  <?php } ?>
  </script>
  <?php } else { ?>
  <meta name="X-SCDS-Membership-Tracking" content="no">
  <?php } ?>
  <script src="https://js.stripe.com/v3/"></script>
  <link rel="stylesheet preload"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400i,700|Roboto+Mono|Merriweather:400,600">
  <link rel="stylesheet preload" href="<?=htmlspecialchars($stylesheet)?>">
  <link rel="stylesheet preload" href="<?=htmlspecialchars(autoUrl("public/css/colour.css"))?>">

  <!-- Generic icon first -->
  <?php if (env('CLUB_LOGO')) { ?>
  <link rel="icon" href="<?=htmlspecialchars(autoUrl(env('CLUB_LOGO')))?>">
  <?php } else { ?>
  <link rel="icon" href="<?=htmlspecialchars(autoUrl("public/img/corporate/scds.png"))?>">
  <?php } ?>

  <!-- For iPhone 6 Plus with @3× display: -->
  <link rel="apple-touch-icon-precomposed" sizes="180x180"
    href="<?=autoUrl("public/img/corporate/icons/apple-touch-icon-180x180.png")?>">
  <!-- For iPad with @2× display running iOS ≥ 7: -->
  <link rel="apple-touch-icon-precomposed" sizes="152x152"
    href="<?=autoUrl("public/img/corporate/icons/apple-touch-icon-152x152.png")?>">
  <!-- For iPad with @2× display running iOS ≤ 6: -->
  <link rel="apple-touch-icon-precomposed" sizes="144x144"
    href="<?=autoUrl("public/img/corporate/icons/apple-touch-icon-144x144.png")?>">
  <!-- For iPhone with @2× display running iOS ≥ 7: -->
  <link rel="apple-touch-icon-precomposed" sizes="120x120"
    href="<?=autoUrl("public/img/corporate/icons/apple-touch-icon-120x120.png")?>">
  <!-- For iPhone with @2× display running iOS ≤ 6: -->
  <link rel="apple-touch-icon-precomposed" sizes="114x114"
    href="<?=autoUrl("public/img/corporate/icons/apple-touch-icon-114x114.png")?>">
  <!-- For the iPad mini and the first- and second-generation iPad (@1× display) on iOS ≥ 7: -->
  <link rel="apple-touch-icon-precomposed" sizes="76x76"
    href="<?=autoUrl("public/img/corporate/icons/apple-touch-icon-76x76.png")?>">
  <!-- For the iPad mini and the first- and second-generation iPad (@1× display) on iOS ≤ 6: -->
  <link rel="apple-touch-icon-precomposed" sizes="72x72"
    href="<?=autoUrl("public/img/corporate/icons/apple-touch-icon-72x72.png")?>">
  <!-- For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: -->
  <link rel="apple-touch-icon-precomposed"
    href="<?=autoUrl("public/img/corporate/icons/apple-touch-icon.png")?>"><!-- 57×57px -->
  <!-- <link rel="mask-icon" href="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/img/chesterIcon.svg"
    color="#bd0000"> -->
  <script src="https://www.google.com/recaptcha/api.js"></script>

  <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  <style>
  .focus-highlight a:focus,
  .blog-sidebar a:focus,
  .event a:focus,
  .hentry a:focus,
  .blog-main a:focus {
    background: #ffbf47;
    outline: 3px solid #ffbf47;
    outline-offset: 0;
  }

  footer .focus-highlight a:focus,
  .cls-global-footer-inverse a:focus {
    color: #000 !important;
  }
  .festive {
    /*background:#005fbd;*/
    background-image: url("https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/img/christmas.png");
    background-size: 50% auto;
    /*padding: 1rem;*/
    color: #fff;
    text-shadow: 1px 1px 1px rgba(0, 0, 0, .5);
  }

  .top-3 {
    top: 1rem;
  }
  </style>

</head>