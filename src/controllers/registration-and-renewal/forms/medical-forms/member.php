<?php

use SCDS\Footer;

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

$getRenewal = $db->prepare("SELECT renewalData.ID, renewalPeriods.ID PID, renewalPeriods.Name, renewalPeriods.Year, renewalData.User, renewalData.Document FROM renewalData LEFT JOIN renewalPeriods ON renewalPeriods.ID = renewalData.Renewal LEFT JOIN users ON users.UserID = renewalData.User WHERE renewalData.ID = ? AND users.Tenant = ?");
$getRenewal->execute([
  $id,
  $tenant->getId(),
]);
$renewal = $getRenewal->fetch(PDO::FETCH_ASSOC);

if (!$renewal) {
  halt(404);
}

if (!$user->hasPermission('Admin') && $renewal['User'] != $user->getId()) {
  halt(404);
}

$ren = Renewal::getUserRenewal($id);

$memberData = null;
try {
  $memberData = $ren->getMember($member);
} catch (Exception $e) {
  halt(404);
}

if (!bool($memberData['current'])) {
  halt(404);
}

$getMedical = $db->prepare("SELECT * FROM `memberMedical` WHERE memberMedical.MemberID = ?");
$getMedical->execute([$id]);
$medical = $getMedical->fetch(PDO::FETCH_ASSOC);

$getMemberInfo = $db->prepare("SELECT MForename, MSurname FROM members WHERE MemberID = ?");
$getMemberInfo->execute([
  $member,
]);
$memberInfo = $getMemberInfo->fetch(PDO::FETCH_ASSOC);
$name = $memberInfo['MForename'];
$yes = $no = '';

$pagetitle = htmlspecialchars("Medical Form - " . htmlspecialchars($memberData['name']) . " - " . $ren->getRenewalName());

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal')) ?>">Registration</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id)) ?>"><?= htmlspecialchars($ren->getRenewalName()) ?></a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/medical-forms')) ?>">Medical Forms</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars(mb_substr($memberInfo['MForename'], 0, 1)) ?><?= htmlspecialchars(mb_substr($memberInfo['MSurname'], 0, 1)) ?></li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Medical Forms
        </h1>
        <p class="lead mb-0">
          Check your details are still up to date
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="row justify-content-between">
    <div class="col-lg-8">

      <p class="lead">
        Please update any incorrect details.
      </p>

      <form method="post" class="needs-validation" novalidate>

        <div class="mb-2">
          <p class="mb-2">
            Does <?= htmlspecialchars($name) ?> have any specific medical conditions
            or disabilities?
          </p>

          <?php if ($medical && $medical['Conditions'] != "") {
            $yes = " checked ";
            $no = "";
          } else {
            $yes = "";
            $no = " checked ";
          } ?>

          <div class="custom-control custom-radio">
            <input required type="radio" value="0" <?= $no ?> id="medConDisNo" name="medConDis" class="custom-control-input" onclick="toggleState('medConDisDetails', 'medConDis')">
            <label class="custom-control-label" for="medConDisNo">No</label>
          </div>
          <div class="custom-control custom-radio">
            <input type="radio" value="1" <?= $yes ?> id="medConDisYes" name="medConDis" class="custom-control-input" onclick="toggleState('medConDisDetails', 'medConDis')">
            <label class="custom-control-label" for="medConDisYes">Yes</label>
          </div>
        </div>

        <div class="form-group">
          <label for="medConDisDetails">If yes give details</label>
          <textarea class="form-control" id="medConDisDetails" name="medConDisDetails" rows="3" <?php if ($yes == "") { ?>disabled<?php } ?>><?php if ($medical) { ?><?= htmlspecialchars($medical['Conditions']) ?><?php } ?></textarea>
        </div>

        <!-- -->

        <div class="mb-2">
          <p class="mb-2">Does <?= htmlspecialchars($name) ?> have any allergies?</p>

          <?php if ($medical && $medical['Allergies'] != "") {
            $yes = " checked ";
            $no = "";
          } else {
            $yes = "";
            $no = " checked ";
          } ?>

          <div class="custom-control custom-radio">
            <input required type="radio" value="0" <?= $no ?> id="allergiesNo" name="allergies" class="custom-control-input" onclick="toggleState('allergiesDetails', 'allergies')">
            <label class="custom-control-label" for="allergiesNo">No</label>
          </div>
          <div class="custom-control custom-radio">
            <input type="radio" value="1" <?= $yes ?> id="allergiesYes" name="allergies" class="custom-control-input" onclick="toggleState('allergiesDetails', 'allergies')">
            <label class="custom-control-label" for="allergiesYes">Yes</label>
          </div>
        </div>

        <div class="form-group">
          <label for="allergiesDetails">If yes give details</label>
          <textarea class="form-control" id="allergiesDetails" name="allergiesDetails" rows="3" <?php if ($yes == "") { ?>disabled<?php } ?>><?php if ($medical) { ?><?= htmlspecialchars($medical['Allergies']) ?><?php } ?></textarea>
        </div>

        <!-- -->

        <div class="mb-2">
          <p class="mb-2">Does <?= htmlspecialchars($name) ?> take any regular medication?</p>

          <?php if ($medical && $medical['Medication'] != "") {
            $yes = " checked ";
            $no = "";
          } else {
            $yes = "";
            $no = " checked ";
          } ?>

          <div class="custom-control custom-radio">
            <input required type="radio" value="0" <?= $no ?> id="medicineNo" name="medicine" class="custom-control-input" onclick="toggleState('medicineDetails', 'medicine')">
            <label class="custom-control-label" for="medicineNo">No</label>
          </div>
          <div class="custom-control custom-radio">
            <input type="radio" value="1" <?= $yes ?> id="medicineYes" name="medicine" class="custom-control-input" onclick="toggleState('medicineDetails', 'medicine')">
            <label class="custom-control-label" for="medicineYes">Yes</label>
          </div>
        </div>

        <div class="form-group">
          <label for="medConDisDetails">If yes give details</label>
          <textarea class="form-control" id="medicineDetails" name="medicineDetails" rows="3" <?php if ($yes == "") { ?>disabled<?php } ?>><?php if ($medical) { ?><?= htmlspecialchars($medical['Medication']) ?><?php } ?></textarea>
        </div>

        <?= \SCDS\CSRF::write() ?>

        <p>
          <button type="submit" class="btn btn-success">Save and complete section</button>
        </p>
      </form>

    </div>
    <div class="col col-xl-3">
      <?= CLSASC\BootstrapComponents\RenewalProgressListGroup::renderLinks($ren, 'medical-forms') ?>
    </div>
  </div>
</div>

<?php

$footer = new Footer();
$footer->addJs("public/js/medical-forms/MedicalForm.js");
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
