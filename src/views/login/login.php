<?php

$db = app()->db;

$selectedClub = 'SCDS';
$club = null;
if (isset($_GET['club']) && mb_strlen((string) $_GET['club']) > 0) {
  $club = Tenant::fromCode((string) $_GET['club']);
  if (!$club) {
    $club = Tenant::fromId((int) $_GET['club']);
  }
}

$colour = null;
if ($club) {
  $colour = $club->getKey('SYSTEM_COLOUR');
  $selectedClub = $club->getName();
}

$pagetitle = "Sign in to " . htmlspecialchars($selectedClub) . " Membership";

include BASE_PATH . "views/root/head.php";

?>

<div class="bg-dark">
<div <?php if ($colour) { ?>style="background: <?=htmlspecialchars($colour)?>" <?php } ?>>
  <div style="background: rgba(255, 255, 255, .5)">

    <div class="container-fluid">
      <div class="row justify-content-center min-vh-100 align-items-center py-4">
        <div class="col-md-10 col-lg-8 col-xl-6">
          <div class="card card-body text-dark mb-0" id="central-card">
            <div class="row align-items-center mb-3">
              <div class="col-auto">
                <img src="<?=htmlspecialchars(autoUrl("public/img/corporate/scds.png"))?>" class="img-fluid rounded" style="height: 75px;">
              </div>
              <div class="col-auto">
                <h1 class="d-flex">
                  Login
                </h1>
              </div>
            </div>
            <p class="lead">
              Sign in with your <?=htmlspecialchars($selectedClub)?> account.
            </p>

            <form action="<?=htmlspecialchars(autoUrl("login"))?>" id="login-form" class="needs-validation" novalidate data-prefilled="<?=htmlspecialchars((int) isset($_GET['user']))?>">
              <div class="form-group">
                <label for="email-address">Email address</label>
                <input type="email" class="form-control form-control-lg text-lowercase" id="email-address" name="email-address" placeholder="yourname@example.com" required autocomplete="email" <?php if (isset($_GET['user'])) { ?>value="<?=htmlspecialchars(urldecode($_GET['user']))?>"<?php } else { ?> autofocus <?php }?>>
              </div>

              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control form-control-lg" required placeholder="Password"  autocomplete="current-password" <?php if (isset($_GET['user'])) { ?> autofocus <?php } ?>>
              </div>

              <?=\SCDS\CSRF::write()?>

              <p class="mb-0">
                <button type="submit" class="btn btn-primary btn-lg" id="submit" disabled>
                  Sign in
                </button>
              </p>
            </form>

            <hr>

            <p>
              Forgotten your account details?
            </p>

            <p class="mb-0">
              <button class="btn btn-light">
                Find account
              </button>
            </p>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<?php $footer = new \SCDS\RootFooter();
$footer->addJs('public/js/login/login.js');
$footer->chrome(false);
$footer->render(); ?>