<?php

$db = app()->db;
$tenant = app()->tenant;

$pagetitle = 'Session Booking';
include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('sessions')) ?>">Sessions</a></li>
        <li class="breadcrumb-item active" aria-current="page">Booking</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col">
        <h1>
          Book a session <span class="badge badge-info" title="This is a new service. Your feedback is always welcome - send an email to feedback@myswimmingclub.uk or call us on +44 191 249 4320">BETA</span>
        </h1>
        <p class="lead mb-0">
          Book numbers limited or pay as you go sessions
        </p>
      </div>
    </div>

  </div>
</div>

<div class="container">

  <div class="row">
    <div class="col-lg-8">
      <p class="lead">
        Session booking is a new feature which will be introduced in the next few weeks.
      </p>

      <p>
        The aim of our new session booking tools is to support clubs during this COVID era where numbers may be limited at sessions, or where prior indication of attendance is desired.
      </p>

      <p>
        Going forward, additional features such as support for pay as you go sessions will be added, with fees billed to a user's account and paid on their next Direct Debit.
      </p>
    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
