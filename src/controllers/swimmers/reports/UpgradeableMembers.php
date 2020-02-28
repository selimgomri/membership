<?php

global $db;

$date = new DateTime('-9 years last day of December', new DateTimeZone('Europe/London'));
$now = new DateTime('now', new DateTimeZone('Europe/London'));

$getMembers = $db->prepare("SELECT MemberID id, MForename fn, MSurname sn, SquadName squad, DateOfBirth dob, ASACategory cat FROM members INNER JOIN squads ON members.SquadID = squads.SquadID WHERE DateOfBirth <= ? AND ASACategory = ? ORDER BY MForename ASC, MSurname ASC");
$getMembers->execute([
  $date->format("Y-m-d"),
  1
]);
$member = $getMembers->fetch(PDO::FETCH_ASSOC);

$pagetitle = "Upgradeable Members";

$fluidContainer = true;

include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("members"))?>">Members</a></li>
      <li class="breadcrumb-item active" aria-current="page">Upgradeable Members</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>Upgradeable members</h1>
      <p class="lead">Upgradeable members are nine years old* by the end of the year and are Category 1 Swim England members.</p>

      <p>This report helps you identify which members will need to be upgraded to Category 2 membership for next year.</p>

      <?php if ($member) { ?>
      <form method="post">

        <?php if (isset($_SESSION['CatChangesSaveError']) && $_SESSION['CatChangesSaveError']) { ?>
          <div class="alert alert-danger">
            <p class="mb-0">
              <strong>An error has occurred</strong>
            </p>
            <p class="mb-0">
              We were unable to save your Swim England Membership Category changes for these members.
            </p>
          </div>
        <?php unset($_SESSION['CatChangesSaveError']); } ?> 

        <?php if (isset($_SESSION['CatChangesSavedSuccessfully']) && $_SESSION['CatChangesSavedSuccessfully']) { ?>
          <div class="alert alert-success">
            <p class="mb-0">
              <strong>Changes saved successfully</strong>
            </p>
            <p class="mb-0">
              Remember that members will not pay their new Swim England fee until their next registration/renewal.
            </p>
          </div>
        <?php unset($_SESSION['CatChangesSavedSuccessfully']); } ?> 

        <p>
          To change a member's Swim England category, select the new category from the dropdown menu. press <strong>Save Changes</strong> when you're finished.
        </p>

        <ul class="list-group mb-3">
          <?php do {
            $dob = new DateTime($member['dob'], new DateTimeZone('Europe/London'));
            $age = $dob->diff($now)->y; ?>
          <li class="list-group-item list-group-item-action">
            <div class="row align-items-center">
              <div class="col-md">
                <a href="<?=htmlspecialchars(autoUrl("members/" . $member['id']))?>" class="">
                  <strong><?=htmlspecialchars($member['fn'] . ' ' . $member['sn'])?></strong>, <?=htmlspecialchars($member['squad'])?>
                </a>
                <div class="mb-3 d-md-none"></div>
              </div>
              <div class="col-md">
                <label for="<?=htmlspecialchars("se-cat-" . $member['id'])?>" class="d-none">
                  Swim England Membership Category
                </label>
                <select class="custom-select" id="<?=htmlspecialchars("se-cat-" . $member['id'])?>" name="<?=htmlspecialchars("se-cat-" . $member['id'])?>">
                  <option value="0" <?php if ($member['cat'] == 0) { ?>selected<?php } ?>>Not an SE member</option>
                  <option value="1" <?php if ($member['cat'] == 1) { ?>selected<?php } ?>>SE Cat One</option>
                  <option value="2" <?php if ($member['cat'] == 2) { ?>selected<?php } ?>>SE Cat Two</option>
                  <option value="3" <?php if ($member['cat'] == 3) { ?>selected<?php } ?>>SE Cat Three</option>
                </select>
                <div class="mb-3 d-md-none"></div>
              </div>
              <div class="col-md text-md-right">
                <?=htmlspecialchars($dob->format("j F Y"))?> (<?=htmlspecialchars($age)?>)
              </div>
            </div>
          </li>
          <?php } while ($member = $getMembers->fetch(PDO::FETCH_ASSOC)); ?>
        </ul>

        <p>
          <button type="submit" class="btn btn-success">
            Save changes
          </button>
        </p>
      </form>
      <?php } else { ?>
        <div class="alert alert-info">
          <p class="mb-0"><strong>There are no upgradeable members at the moment.</strong></p>
          <p>Upgradeable members are nine years old by the end of the year and are Category 1 Swim England members.</p>
        </div>
      <?php } ?>

      <p>* Born on or after <?=htmlspecialchars($date->format("j F Y"))?></p>
    </div>
  </div>
</div>

<?php

$footer = new \SDCS\Footer();
$footer->useFluidContainer();
$footer->render();