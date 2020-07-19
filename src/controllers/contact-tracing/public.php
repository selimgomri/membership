
<?php

$db = app()->db;
$tenant = app()->tenant;
$pagetitle = 'Contact Tracing';

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Tracing</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Contact Tracing
        </h1>
        <p class="lead mb-0">
          <?= htmlspecialchars($tenant->getName()) ?> <em>Supporting NHS Test and Trace</em>
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="row">
    <div class="col-12 mb-5">
      <div class="p-3 text-white bg-danger rounded h-100">
        <h2>
          Register your attendance
        </h2>
        <p class="lead">
          Together, we can help the NHS.
        </p>
        <p>
          <?= htmlspecialchars($tenant->getName()) ?> will use this data if required to support NHS Test and Trace. Data will be deleted automatically after 21 days.
        </p>
        <p class="mb-0">
          <a href="<?= htmlspecialchars(autoUrl('contact-tracing/check-in')) ?>" class="btn btn-outline-light">
            Register
          </a>
        </p>
      </div>
    </div>

    <div class="col-md-4 mb-3">
      <div class="p-3 bg-primary rounded text-white h-100" style="background-color: #005eb8 !important; display: grid;">
        <div>
          <h2>
            Coronavirus (COVID-19)
          </h2>
          <p class="lead">
            Find out about coronavirus, its symptoms and effects at NHS.UK.
          </p>
        </div>
        <p class="mb-0 mt-auto d-flex">
          <a href="https://www.nhs.uk/conditions/coronavirus-covid-19/" class="btn btn-light">
            <i class="fa fa-arrow-circle-right" aria-hidden="true"></i> NHS.UK
          </a>
        </p>
      </div>
    </div>

    <div class="col-md-4 mb-3">
      <div class="p-3 rounded border h-100" style="display: grid;">
        <div>
          <h2>
            The Rules
          </h2>
          <p class="lead">
            Find out the rules and guidance on what you can and can't do on GOV.UK.
          </p>
        </div>
        <p class="mb-0 mt-auto d-flex">
          <a href="https://www.nhs.uk/conditions/coronavirus-covid-19/" class="btn btn-dark">
            <i class="fa fa-arrow-circle-right" aria-hidden="true"></i> GOV.UK
          </a>
        </p>
      </div>
    </div>

    <div class="col-md-4 mb-3">
      <div class="p-3 rounded border h-100" style="display: grid;">
        <div>
          <h2>
            Swim England Return to Pool Guidance
          </h2>
          <p class="lead">
            Swim England have issued guidance to all clubs and pool operators on returning to the pool safely. Read it on the Swim England website.
          </p>
        </div>
        <p class="mb-0 mt-auto d-flex">
          <a href="https://www.swimming.org/swimengland/pool-return-guidance-documents/" class="btn btn-dark">
            <i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Guidance
          </a>
        </p>
      </div>
    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
