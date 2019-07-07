<?php

global $db;
global $systemInfo;

$systemInfo->setSystemOption('ClubFeesType', 'Family/Individual');

$feeType = $systemInfo->getSystemOption('ClubFeesType');

$family = false;
$perMember = false;
$monthlyPrecept = false;

$feesArray = [];

if ($feeType == 'Family/Individual') {
  $family = true;
  $feesArray['Individual'] = $systemInfo->getSystemOption('ClubFeeIndividual');
  $feesArray['Family'] = $systemInfo->getSystemOption('ClubFeeFamily');
} else if ($feeType == 'PerMember') {
  $perMember = true;
  $feesArray['Individual'] = $systemInfo->getSystemOption('ClubFeeIndividual');
} else if ($feeType == 'MonthlyPrecept') {
  $monthlyPrecept = true;
  $feesArray['Precept'] = $systemInfo->getSystemOption('ClubFeeIndividual');
} else if ($feeType == 'MonthlyPreceptFamily') {
  $monthlyFamilyPrecept = true;
  $feesArray['Precept'] = $systemInfo->getSystemOption('ClubFeeFamily');
}

$pagetitle = "Club Membership Fee Options";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <main class="col-lg-8">
      <h1>Club Membership Fee Management</h1>
      <p class="lead">Set amounts for club membership fees</p>

      <?php if (isset($_SESSION['Update-Success']) && $_SESSION['Update-Success']) { ?>
      <div class="alert alert-success">Changes saved successfully</div>
      <?php unset($_SESSION['Update-Success']); } ?>

      <?php if (isset($_SESSION['Update-Error']) && $_SESSION['Update-Error']) { ?>
      <div class="alert alert-danger">Changes could not be saved</div>
      <?php unset($_SESSION['Update-Error']); } ?>

      <form method="post">

        <div class="form-group">
          <label for="indv">Individual Fee</label>
          <div class="input-group mono">
            <div class="input-group-prepend">
              <span class="input-group-text">&pound;</span>
            </div>
            <input type="number" class="form-control" id="indv" name="indv" placeholder="Enter amount" min="0"
              step="0.01" value="<?=number_format($feesArray['Individual']/100, 2, '.', '')?>">
          </div>
        </div>

        <div class="form-group">
          <label for="fam">Family Fee</label>
          <div class="input-group mono">
            <div class="input-group-prepend">
              <span class="input-group-text">&pound;</span>
            </div>
            <input type="number" class="form-control" id="fam" name="fam" placeholder="Enter amount" min="0"
              step="0.01" value="<?=number_format($feesArray['Family']/100, 2, '.', '')?>">
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

<?php

include BASE_PATH . 'views/footer.php';