<?php

$db = app()->db;

$fluidContainer = true;

$fees['County'][1] = app()->tenant->getKey('ASA-County-Fee-L1') / 100;
$fees['Region'][1] = app()->tenant->getKey('ASA-Regional-Fee-L1') / 100;
$fees['National'][1] = app()->tenant->getKey('ASA-National-Fee-L1') / 100;

$fees['County'][2] = app()->tenant->getKey('ASA-County-Fee-L2') / 100;
$fees['Region'][2] = app()->tenant->getKey('ASA-Regional-Fee-L2') / 100;
$fees['National'][2] = app()->tenant->getKey('ASA-National-Fee-L2') / 100;

$fees['County'][3] = app()->tenant->getKey('ASA-County-Fee-L3') / 100;
$fees['Region'][3] = app()->tenant->getKey('ASA-Regional-Fee-L3') / 100;
$fees['National'][3] = app()->tenant->getKey('ASA-National-Fee-L3') / 100;

foreach ($fees as $region => $value) {
  foreach ($value as $level => $amount) {
    if (!is_numeric($amount)) {
      $fees[$region][$level] = 0;
    }
  }
}

$pagetitle = "Swim England Fee Options";

include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">
  <div class="row justify-content-between">
    <aside class="col-md-3 d-none d-md-block">
      <?php
      $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/settings/SettingsLinkGroup.json'));
      echo $list->render('settings-fees');
      ?>
    </aside>
    <div class="col-md-9">

      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('settings')) ?>">Settings</a></li>
          <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('settings/fees')) ?>">Fees</a></li>
          <li class="breadcrumb-item active" aria-current="page">Swim England</li>
        </ol>
      </nav>

      <main>
        <h1>Swim England Fee Management</h1>
        <p class="lead">Set amounts for Swim England membership fees</p>

        <?php if ((isset($_SESSION['TENANT-' . app()->tenant->getId()]['COUNTY-SAVED']) && $_SESSION['TENANT-' . app()->tenant->getId()]['COUNTY-SAVED']) || (isset($_SESSION['TENANT-' . app()->tenant->getId()]['REGION-SAVED']) && $_SESSION['TENANT-' . app()->tenant->getId()]['REGION-SAVED']) || (isset($_SESSION['TENANT-' . app()->tenant->getId()]['NATIONAL-SAVED']) && $_SESSION['TENANT-' . app()->tenant->getId()]['NATIONAL-SAVED'])) { ?>
          <div class="alert alert-success">
            <p class="mb-0">We've saved:</p>
            <ul class="mb-0">
              <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['COUNTY-SAVED']) && $_SESSION['TENANT-' . app()->tenant->getId()]['COUNTY-SAVED']) { ?><li>County fees</li><?php } ?>
              <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['REGION-SAVED']) && $_SESSION['TENANT-' . app()->tenant->getId()]['REGION-SAVED']) { ?><li>Regional fees</li><?php } ?>
              <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['NATIONAL-SAVED']) && $_SESSION['TENANT-' . app()->tenant->getId()]['NATIONAL-SAVED']) { ?><li>National fees</li>
              <?php } ?>
            </ul>
          </div>
        <?php
          unset($_SESSION['TENANT-' . app()->tenant->getId()]['COUNTY-SAVED']);
          unset($_SESSION['TENANT-' . app()->tenant->getId()]['REGION-SAVED']);
          unset($_SESSION['TENANT-' . app()->tenant->getId()]['NATIONAL-SAVED']);
        } ?>

        <?php if ((isset($_SESSION['TENANT-' . app()->tenant->getId()]['COUNTY-ERROR']) && $_SESSION['TENANT-' . app()->tenant->getId()]['COUNTY-ERROR']) || (isset($_SESSION['TENANT-' . app()->tenant->getId()]['REGION-ERROR']) && $_SESSION['TENANT-' . app()->tenant->getId()]['REGION-ERROR']) || (isset($_SESSION['TENANT-' . app()->tenant->getId()]['NATIONAL-ERROR']) && $_SESSION['TENANT-' . app()->tenant->getId()]['NATIONAL-ERROR'])) { ?>
          <div class="alert alert-danger">
            <p class="mb-0">We were unable to save the changes for:</p>
            <ul class="mb-0">
              <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['COUNTY-ERROR']) && $_SESSION['TENANT-' . app()->tenant->getId()]['COUNTY-ERROR']) { ?><li>County fee</li><?php } ?>
              <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['REGION-ERROR']) && $_SESSION['TENANT-' . app()->tenant->getId()]['REGION-ERROR']) { ?><li>Regional fee</li><?php } ?>
              <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['NATIONAL-ERROR']) && $_SESSION['TENANT-' . app()->tenant->getId()]['NATIONAL-ERROR']) { ?><li>National fee</li>
              <?php } ?>
            </ul>
          </div>
        <?php
          unset($_SESSION['TENANT-' . app()->tenant->getId()]['COUNTY-ERROR']);
          unset($_SESSION['TENANT-' . app()->tenant->getId()]['REGION-ERROR']);
          unset($_SESSION['TENANT-' . app()->tenant->getId()]['NATIONAL-ERROR']);
        } ?>

        <form method="post">

          <h2>County fees</h2>
          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label class="form-label" for="county-1">Level 1 fee</label>
                <div class="input-group mono">
                  <div class="input-group-prepend">
                    <span class="input-group-text">&pound;</span>
                  </div>
                  <input type="number" class="form-control" id="county-1" name="county-1" placeholder="Enter amount" min="0" step="0.01" value="<?= number_format($fees['County'][1], 2, '.', '') ?>">
                </div>
              </div>
            </div>

            <div class="col">
              <div class="mb-3">
                <label class="form-label" for="county-2">Level 2 fee</label>
                <div class="input-group mono">
                  <div class="input-group-prepend">
                    <span class="input-group-text">&pound;</span>
                  </div>
                  <input type="number" class="form-control" id="county-2" name="county-2" placeholder="Enter amount" min="0" step="0.01" value="<?= number_format($fees['County'][2], 2, '.', '') ?>">
                </div>
              </div>
            </div>

            <div class="col">
              <div class="mb-3">
                <label class="form-label" for="county">Level 3 fee</label>
                <div class="input-group mono">
                  <div class="input-group-prepend">
                    <span class="input-group-text">&pound;</span>
                  </div>
                  <input type="number" class="form-control" id="county-3" name="county-3" placeholder="Enter amount" min="0" step="0.01" value="<?= number_format($fees['County'][3], 2, '.', '') ?>">
                </div>
              </div>
            </div>
          </div>

          <h2>Regional fees</h2>
          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label class="form-label" for="region-1">Level 1 fee</label>
                <div class="input-group mono">
                  <div class="input-group-prepend">
                    <span class="input-group-text">&pound;</span>
                  </div>
                  <input type="number" class="form-control" id="region-1" name="region-1" placeholder="Enter amount" min="0" step="0.01" value="<?= number_format($fees['Region'][1], 2, '.', '') ?>">
                </div>
              </div>
            </div>

            <div class="col">
              <div class="mb-3">
                <label class="form-label" for="region-2">Level 2 fee</label>
                <div class="input-group mono">
                  <div class="input-group-prepend">
                    <span class="input-group-text">&pound;</span>
                  </div>
                  <input type="number" class="form-control" id="region-2" name="region-2" placeholder="Enter amount" min="0" step="0.01" value="<?= number_format($fees['Region'][2], 2, '.', '') ?>">
                </div>
              </div>
            </div>

            <div class="col">
              <div class="mb-3">
                <label class="form-label" for="region-3">Level 3 fee</label>
                <div class="input-group mono">
                  <div class="input-group-prepend">
                    <span class="input-group-text">&pound;</span>
                  </div>
                  <input type="number" class="form-control" id="region-3" name="region-3" placeholder="Enter amount" min="0" step="0.01" value="<?= number_format($fees['Region'][3], 2, '.', '') ?>">
                </div>
              </div>
            </div>
          </div>

          <h2>National fees</h2>

          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label class="form-label" for="national-1">Level 1 fee</label>
                <div class="input-group mono">
                  <div class="input-group-prepend">
                    <span class="input-group-text">&pound;</span>
                  </div>
                  <input type="number" class="form-control" id="national-1" name="national-1" placeholder="Enter amount" min="0" step="0.01" value="<?= number_format($fees['National'][1], 2, '.', '') ?>">
                </div>
              </div>
            </div>

            <div class="col">
              <div class="mb-3">
                <label class="form-label" for="national-2">Level 2 fee</label>
                <div class="input-group mono">
                  <div class="input-group-prepend">
                    <span class="input-group-text">&pound;</span>
                  </div>
                  <input type="number" class="form-control" id="national-2" name="national-2" placeholder="Enter amount" min="0" step="0.01" value="<?= number_format($fees['National'][2], 2, '.', '') ?>">
                </div>
              </div>
            </div>

            <div class="col">
              <div class="mb-3">
                <label class="form-label" for="national-3">Level 3 fee</label>
                <div class="input-group mono">
                  <div class="input-group-prepend">
                    <span class="input-group-text">&pound;</span>
                  </div>
                  <input type="number" class="form-control" id="national-3" name="national-3" placeholder="Enter amount" min="0" step="0.01" value="<?= number_format($fees['National'][3], 2, '.', '') ?>">
                </div>
              </div>
            </div>
          </div>

          <p>
            <button class="btn btn-success" type="submit">
              Save
            </button>
          </p>
        </form>
      </main>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->useFluidContainer();
$footer->render();
