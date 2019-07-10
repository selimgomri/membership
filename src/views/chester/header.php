<?php

global $db;

require 'GlobalHead.php';

if (!isset($_SESSION['AlphaBeta'])) {
 if (rand() < 0.5) {
   $_SESSION['AlphaBeta'] = false;
 } else {
   $_SESSION['AlphaBeta'] = true;
 }
}

$bg = "bg-white";
if (isset($customBackground) && $customBackground) {
  $bg = $customBackground;
}
?>

<?php if (false /*$_SESSION['AlphaBeta']*/) { ?>
<style>
h1 {
  background: #bd0000;
  padding: 1rem;
  color: #fff;
  width: max-content;
  margin: 3rem 0 0 0;
}

p.lead {
  background: #bd0000;
  padding: 1rem;
  color: #fff;
  width: max-content;
  margin: 0 0 3rem 0;
}
</style>
<?php } ?>

<body class="<?=$bg?> account--body">

  <div class="sr-only sr-only-focusable">
    <a href="#maincontent">Skip to main content</a>
  </div>

  <div class="d-print-none">

    <?php if (isset($_SESSION['UserSimulation'])) { ?>
    <div class="bg-secondary text-white box-shadow py-2 d-print-none">
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
            supports it as soon as possible. <strong><a class="text-dark" href="http://browsehappy.com/"
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
          It looks like you're using Internet Explorer which we no longer support so we recommend you upgrade to a new
          browser which we do support as soon as possible. <strong><a href="http://browsehappy.com/"
              target="_blank">Upgrade your browser today <i class="fa fa-external-link"
                aria-hidden="true"></i></a></strong>.
        </p>
        <p class="mb-0">
          <?=htmlspecialchars(env('CLUB_NAME'))?> recommends you <strong><a href="https://www.firefox.com">install
              Firefox by Mozilla</a></strong>. Firefox has great protections for your privacy with built in features
          including tracking protection.
        </p>
      </div>
    </div>
    <?php } ?>

    <?php
    $edit_link = null;
    if (isset($allow_edit_id)) {
      $edit_link = autoUrl("posts/" . $allow_edit_id . "/edit");
    }

    if (isset($allow_edit) && $allow_edit && (($_SESSION['AccessLevel'] != "Parent" &&
    $_SESSION['AccessLevel'] != "Coach" && $edit_link != null) || $page_is_mine)) { ?>
    <div class="bg-dark text-white box-shadow py-2 d-print-none">
      <div class="<?=$container_class?>">
        <p class="mb-0">
          <a href="<?=$edit_link?>" class="text-white">
            Edit this page
          </a>
        </p>
      </div>
    </div>
    <?php } ?>

    <?php if (!isset($_SESSION['PWA']) || !$_SESSION['PWA']) { ?>
    <div class="text-white py-2 top-bar bg-primary-dark hide-a-underline" style="font-size:0.875rem;">
      <div class="<?=$container_class?> d-flex">
        <div class="mr-auto">
          <span class="mr-2">
            <a href="https://www.twitter.com/CLSASC" target="_blank" class="text-white" title="Twitter">
              <i class="fa fa-twitter fa-fw" aria-hidden="true"></i>
              <span class="sr-only">Chester-le-Street ASC on Twitter</span>
            </a>
          </span>

          <span class="mr-2">
            <a href="https://www.facebook.com/CLSASC" target="_blank" class="text-white" title="Facebook">
              <i class="fa fa-facebook fa-fw" aria-hidden="true"></i>
              <span class="sr-only">Chester-le-Street ASC on Facebook</span>
            </a>
          </span>
        </div>

        <span class="d-flex" id="top-bar-visible">
        </span>

        <?php if (isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn']) { ?>
        <span class="d-none" id="top-bar-login-status">1</span>
        <?php } else { ?>
        <span class="d-none" id="top-bar-login-status">0</span>
        <?php } ?>

        <div class="ml-2 top-bar d-lg-none">
          <span>
            <a data-toggle="collapse" href="#mobSearch" role="button" aria-expanded="false" aria-controls="mobSearch"
              class="text-white" title="Search the site">
              Search
            </a>
          </span>
        </div>

        <div class="ml-2 top-bar">
          <span>
            <a id="top-bar-more-link" href="#top-bar-more" data-toggle="collapse" role="button" aria-expanded="false"
              aria-controls="top-bar-more" class="text-white d-none" title="More Links">
              More <i class="fa fa-caret-down" aria-hidden="true"></i>
            </a>
          </span>
        </div>
      </div>
    </div>

    <div class="collapse " id="top-bar-more">
      <div class="bg-primary-dark py-2 border-top border-white hide-a-underline" style="font-size:0.875rem;">
        <div class="<?=$container_class?>">
          <div id="top-bar-more-content">
          </div>
        </div>
      </div>
    </div>

    <div class="collapse" id="mobSearch">
      <div class="text-white py-3 d-lg-none bg-primary-darker">
        <form class="container" action="https://www.chesterlestreetasc.co.uk" id="head-search" method="get">
          <label for="s" class="sr-only">Search</label>
          <div class="input-group">
            <input class="form-control bg-primary text-white border-primary" id="s" name="s"
              placeholder="Search the site" type="search">
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
    </div>

    <div class="text-white py-3 d-none d-lg-flex bg-primary-darker">
      <div class="<?=$container_class?>">
        <div class="row align-items-center">
          <div class="col">
            <a class="logowhite" href="<?=autoUrl("")?>" title="Membership Dashboard"></a>
          </div>
          <div class="col d-none d-lg-flex">
            <p class="lead mb-0 ml-auto text-right">Club Membership</p>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>

    <?php if (true || !isset($_SESSION['PWA']) || !$_SESSION['PWA']) { ?>
    <div
      class="bg-primary <?php if (isset($_SESSION['UserID']) && user_needs_registration($_SESSION['UserID'])) { ?>d-lg-none<?php } ?>">
      <div class="<?=$container_class?>">
    <?php } ?>
    <nav class="navbar <?php if (isset($_SESSION['PWA']) || $_SESSION['PWA']) { ?><?php } ?>  navbar-expand-lg navbar-dark bg-primary
    d-print-none justify-content-between px-0" <?php if ($use_website_menu) { ?>id="club-menu" <?php } ?>
          role="navigation">

          <a class="navbar-brand d-lg-none" href="<?=autoUrl("")?>">
            <?php if (isset($_SESSION['LoggedIn'])) { ?>
            <img src="<?php echo autoUrl("public/img/chesterIcon.svg"); ?>" width="20" height="20"> My Membership
            <?php } else { ?>
            <img src="<?php echo autoUrl("public/img/chesterIcon.svg"); ?>" width="20" height="20"> Club Membership
            <?php } ?>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#chesterNavbar"
            aria-controls="chesterNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

        <?php include BASE_PATH . 'views/menus/main.php'; ?>
          
        <?php if (true || !isset($_SESSION['PWA']) || !$_SESSION['PWA']) { ?>
      </div>

    </div>
    <?php } ?>

    <div id="maincontent"></div>

    <!-- END OF HEADERS -->
    <div class="mb-3"></div>

    <?php if (!isset($_SESSION['PWA']) || !$_SESSION['PWA']) { ?>
    <div class="have-full-height" style="min-height:70vh">
      <?php } else { ?>
      <div class="have-full-height" style="min-height:calc(100vh - 7rem);">
        <?php } ?>