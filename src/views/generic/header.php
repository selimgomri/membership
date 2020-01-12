<?php

global $db;

require 'GlobalHead.php';

$bg = "bg-white";
if (isset($customBackground) && $customBackground) {
  $bg = $customBackground;
}
?>

<body class="<?=$bg?> account--body">

  <div class="sr-only sr-only-focusable">
    <a href="#maincontent">Skip to main content</a>
  </div>

  <div class="d-print-none">

    <noscript>
      <div class="bg-warning box-shadow py-3 d-print-none">
        <div class="<?=$container_class?>">
          <p class="h2">
            <strong>
              JavaScript is disabled or not supported
            </strong>
          </p>
          <p>
            It looks like you've got JavaScript disabled or your browser does
            not support it. JavaScript is essential for our website to function
            properly so we recommend you enable it or upgrade to a browser which
            supports it as soon as possible. <strong><a class="text-dark" href="https://browsehappy.com/"
                target="_blank">Upgrade your browser
                today <i class="fa fa-external-link" aria-hidden="true"></i></a></strong>.
          </p>
          <p class="mb-0">
            If JavaScript is not supported by your browser, <?=env('CLUB_NAME')?>
            recommends you <strong><a class="text-dark" href="https://www.firefox.com">install Firefox by
                Mozilla</a></strong>.
          </p>
        </div>
      </div>
    </noscript>

    <?php if ($_SESSION['Browser']['Name'] == "Internet Explorer") {?>
    <div class="bg-warning py-3 d-print-none">
      <div class="<?=$container_class?>">
        <p class="h2">
          <strong>
            Internet Explorer is not supported
          </strong>
        </p>
        <p>
          It looks like you're using Internet Explorer which we no longer support so we recommend you upgrade to a new browser which we do support as soon as possible. <strong><a class="text-dark" href="http://browsehappy.com/" target="_blank">Upgrade your browser today <i class="fa fa-external-link" aria-hidden="true"></i></a></strong>.
        </p>
        <p class="mb-0">
          <?=htmlspecialchars(env('CLUB_NAME'))?> recommends you <strong><a class="text-dark" href="https://www.firefox.com">install Firefox by Mozilla</a></strong>. Firefox has great protections for your privacy with built in features including tracking protection.
        </p>
      </div>
    </div>
    <?php } ?>

    <?php if (defined('IS_EVALUATION_COPY') && IS_EVALUATION_COPY) { ?>
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
    <div class="bg-dark text-white py-2 d-print-none">
      <div class="<?=$container_class?>">
        <p class="mb-0">
          <strong>
            You are in User Simulation Mode simulating <?=
              $_SESSION['UserSimulation']['SimUserName'] ?>
          </strong>
        </p>
        <p class="mb-0">
          <a href="<?=htmlspecialchars(autoUrl("users/simulate/exit"))?>" class="text-white">
            Exit User Simulation Mode
          </a>
        </p>
      </div>
    </div>
    <?php } ?>

    <?php
    $edit_link = null;
    if ((!isset($people) || !$people) && isset($allow_edit_id)) {
      $edit_link = autoUrl("posts/" . $allow_edit_id . "/edit");
    } else if (isset($people) && isset($page_is_mine) && $people && $page_is_mine) {
      $edit_link = autoUrl("people/me");
    }

    if (isset($allow_edit) && $allow_edit && (($_SESSION['AccessLevel'] != "Parent" &&
    $_SESSION['AccessLevel'] != "Coach" && $edit_link != null) || $page_is_mine)) { ?>
    <div class="bg-dark box-shadow py-2 d-print-none">
      <div class="<?=$container_class?>">
        <p class="mb-0">
          <a href="<?=htmlspecialchars($edit_link)?>" class="text-white">
            Edit this page
          </a>
        </p>
      </div>
    </div>
    <?php } ?>

    <div class="membership-header">
      <?php if (!isset($_SESSION['PWA']) || !$_SESSION['PWA']) { ?>
      <div class="club-name-header <?php if (date("m") == "12") { ?>festive<?php } ?>">
        <div class="<?=$container_class?>">
          <h1 class="d-none d-md-flex py-3 mb-0">
            <a href="<?=htmlspecialchars(autoUrl(""))?>" class="text-white text-decoration-none">
              <?=htmlspecialchars(mb_strtoupper(env('CLUB_NAME')))?>
            </a>
          </h1>
        </div>
      </div>
      <?php } ?>

      <?php if (!user_needs_registration($_SESSION['UserID'])) { ?>
      <div class="<?=$container_class?>">
        <div class="">
          <div class="">
            <nav class="navbar navbar-expand-lg navbar-dark
        d-print-none justify-content-between px-0" role="navigation">

              <a class="navbar-brand d-lg-none" href="<?=htmlspecialchars(autoUrl(""))?>">
                <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
                My Membership
                <?php } else { ?>
                Club Membership
                <?php } ?>
              </a>
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#chesterNavbar"
                aria-controls="chesterNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>

              <?php include BASE_PATH . 'views/menus/main.php'; ?>
            </nav>
          </div>
        </div>
      </div>
      <?php } ?>

    </div>

    <div id="maincontent"></div>

    <!-- END OF HEADERS -->
    <div class="mb-3"></div>
  
  </div>

    <?php if (!isset($_SESSION['PWA']) || !$_SESSION['PWA']) { ?>
    <div class="have-full-height">
    <?php } else { ?>
    <div class="have-full-height">
    <?php } ?>
