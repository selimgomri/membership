<?php

$pagetitle = "Membership Software by Swimming Club Data Systems";

include BASE_PATH . "views/root/header.php";

?>

<style>
  .bg-indigo {
    background: var(--purple);
  }
</style>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>Membership management software for swimming clubs</h1>
      <p class="lead">Manage your members, subscriptions, competition entries and more.</p>
    </div>
  </div>
</div>

<div class="bg-indigo text-light py-5">
  <div class="container">
    <div class="row">
      <div class="col-6">
        <h2>Members and squads</h2>
        <p class="lead">Manage your squads and members.</p>
      </div>
    </div>
  </div>
</div>

<div class="bg-light text-dark py-5">
  <div class="container">
    <div class="row">
      <div class="col-6">
        <h2>Member communications</h2>
        <p class="lead">Contact your members and parents easily.</p>
      </div>
    </div>
  </div>
</div>

<div class="bg-indigo text-light py-5">
  <div class="container">
    <div class="row">
      <div class="col-6">
        <h2>Automated payments</h2>
        <p class="lead">Collect payments by Direct Debit and one-off payments by card.</p>
      </div>
    </div>
  </div>
</div>

<div class="bg-light text-dark py-5">
  <div class="container">
    <div class="row">
      <div class="col-6">
        <h2>Online gala entries</h2>
        <p class="lead">Your members can enter their competitions online.</p>
      </div>
    </div>
  </div>
</div>

<div class="bg-indigo text-light py-5">
  <div class="container">
    <div class="row">
      <div class="col-6">
        <h2>Online registers</h2>
        <p class="lead">Take your registers online and keep your coaches up to date.</p>
      </div>
    </div>
  </div>
</div>

<div class="bg-light text-dark py-5">
  <div class="container">
    <div class="row">
      <div class="col-6">
        <h2>Much more</h2>
        <p class="lead">Online photo permissions, medical forms and more.</p>
      </div>
    </div>
  </div>
</div>

<?php $footer = new \SCDS\RootFooter();
$footer->render(); ?>