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
          Book a session
        </h1>
        <p class="lead mb-0">
          Book numbers limited or pay as you go sessions
        </p>
      </div>
    </div>

  </div>
</div>

<div class="container">

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
