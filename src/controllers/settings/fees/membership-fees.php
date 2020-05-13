<?php

$db = app()->db;

$fluidContainer = true;

app()->tenant->setKey('ClubFeesType', 'NSwimmers');

$feeType = app()->tenant->getKey('ClubFeesType');

$feeUpgradeType = app()->tenant->getKey('ClubFeeUpgradeType');

$family = false;
$perMember = false;
$monthlyPrecept = false;

$feesArray = [];

if ($feeType == 'NSwimmers') {
  $feesArray = json_decode(app()->tenant->getKey('ClubFeeNSwimmers'), true);
}

$pagetitle = "Club Membership Fee Options";

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
      <main>
        <h1>Club Membership Fee Management</h1>
        <p class="lead">Set amounts for club membership fees</p>

        <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['Update-Success']) && $_SESSION['TENANT-' . app()->tenant->getId()]['Update-Success']) { ?>
        <div class="alert alert-success">Changes saved successfully</div>
        <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['Update-Success']); } ?>

        <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['Update-Error']) && $_SESSION['TENANT-' . app()->tenant->getId()]['Update-Error']) { ?>
        <div class="alert alert-danger">Changes could not be saved</div>
        <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['Update-Error']); } ?>

        <form method="post">

          <h2>Upgrade settings</h2>

          <p>When a new member is added to an existing account, how should the club membership fee be handled? You can choose to charge nothing, charge the difference between the amount previously paid for x members and the amount with new members or charge the full membership fee.</p>
          <div class="form-group">
            <label for="upgrade">Upgrade setings</label>
            <div class="custom-control custom-radio">
              <input type="radio" id="no-fee" value="None" name="upgrade" class="custom-control-input" <?php if ($feeUpgradeType == 'None') { ?>checked<?php } ?>>
              <label class="custom-control-label" for="no-fee">Don't charge an upgrade fee</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="topup" value="TopUp" name="upgrade" class="custom-control-input" <?php if ($feeUpgradeType == 'TopUp') { ?>checked<?php } ?>>
              <label class="custom-control-label" for="topup">Charge a topup fee (difference between amount previously paid and new total)</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="full" value="FullFee" name="upgrade" class="custom-control-input" <?php if ($feeUpgradeType == 'FullFee') { ?>checked<?php } ?>>
              <label class="custom-control-label" for="full">Charge the full fee</label>
            </div>
          </div>

          <h2>Fees</h2>

          <?php $i = 0; ?>
          <?php for ($i = 0; $i < sizeof($feesArray); $i++) { ?>
          <div class="form-group">
          <label for="<?=$i+1?>-swimmers">Swimmer <?=$i+1?><?php if ($i == sizeof($feesArray)-1) { ?> or more<?php } ?></label>
            <div class="input-group mono">
              <div class="input-group-prepend">
                <span class="input-group-text">&pound;</span>
              </div>
              <input type="number" class="form-control" id="<?=$i+1?>-swimmers" name="<?=$i+1?>-swimmers" placeholder="Enter amount" min="0"
                step="0.01" value="<?=number_format($feesArray[$i]/100, 2, '.', '')?>">
            </div>
          </div>
          <?php } ?>

          <div class="form-group">
          <label for="<?=$i+1?>-swimmers">Add fee for <?=$i+1?> or more swimmers</label>
            <div class="input-group mono">
              <div class="input-group-prepend">
                <span class="input-group-text">&pound;</span>
              </div>
              <input type="number" class="form-control" id="<?=$i+1?>-swimmers" name="<?=$i+1?>-swimmers" placeholder="Enter amount" min="0"
                step="0.01" value="">
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