<?php

if (!isset($_POST['id']) || !isset($_POST['member'])) halt(404);

$id = $_POST['id'];

$user = app()->user;
$db = app()->db;
$tenant = app()->tenant;

$getBatch = $db->prepare("SELECT membershipBatch.ID id, membershipYear.ID yearId, membershipBatch.Completed completed, DueDate due, Total total, membershipYear.Name yearName, membershipYear.StartDate yearStart, membershipYear.EndDate yearEnd, PaymentTypes payMethods, PaymentDetails payDetails, membershipBatch.User `user` FROM membershipBatch INNER JOIN membershipYear ON membershipBatch.Year = membershipYear.ID INNER JOIN users ON users.UserID = membershipBatch.User WHERE membershipBatch.ID = ? AND users.Tenant = ?");
$getBatch->execute([
  $id,
  app()->tenant->getId(),
]);

$batch = $getBatch->fetch(PDO::FETCH_OBJ);

if (!$batch) halt(404);

if (!$user->hasPermission('Admin')) halt(404);

// Get members for this user
$getMembers = $db->prepare("SELECT MForename, MSurname, MemberID FROM members WHERE UserID = ? AND MemberID = ? AND Active ORDER BY MForename ASC, MSurname ASC;");
$getMembers->execute([
  $batch->user,
  $_POST['member']
]);

$member = $getMembers->fetch(PDO::FETCH_OBJ);

// Work out available memberships
$getMemberships = $db->prepare("SELECT `ID` `id`, `Name` `name`, `Description` `description`, `Fees` `fees`, `Type` `type` FROM `clubMembershipClasses` WHERE `Tenant` = ? AND `ID` NOT IN (SELECT `Membership` AS `ID` FROM `memberships` WHERE `Member` = ? AND `Year` = ?) AND `ID` NOT IN (SELECT `Membership` AS `ID` FROM `membershipBatchItems` INNER JOIN membershipBatch ON membershipBatchItems.Batch = membershipBatch.ID WHERE `Member` = ? AND `membershipBatch`.`ID` = ?)");
$getMemberships->execute([
  $tenant->getId(),
  $_POST['member'],
  $batch->yearId,
  $_POST['member'],
  $batch->id,
]);
$membership = $getMemberships->fetch(PDO::FETCH_OBJ);

ob_clean();
ob_start();

?>

<?php if ($membership) { ?>

  <div class="pt-3">
    <label class="form-label" for="membership">Select membership</label>
    <select class="form-select" id="membership" name="membership" required>
      <option disabled selected value="null">Select a membership</option>
      <?php do { ?>
        <?php
        $fee = MoneyHelpers::intToDecimal(json_decode($membership->fees)->fees[0]);
        ?>
        <option value="<?= htmlspecialchars($membership->id) ?>" data-id="<?= htmlspecialchars($membership->id) ?>" data-name="<?= htmlspecialchars($membership->name) ?>" data-description="<?= htmlspecialchars($membership->description) ?>" data-fee="<?= htmlspecialchars($fee) ?>" data-fees="<?= htmlspecialchars($membership->fees) ?>" data-type="<?= htmlspecialchars($membership->type) ?>"><?= htmlspecialchars($membership->name) ?></option>
      <?php } while ($membership = $getMemberships->fetch(PDO::FETCH_OBJ)); ?>
    </select>
  </div>

  <div id="add-membership-form-details-opts" class="collapse">

    <div class="pt-3">
      <div class="mb-3">
        <label for="membership-amount" class="form-label">Fee</label>
        <div class="input-group mb-3">
          <span class="input-group-text">&pound;</span>
          <input type="num" class="form-control" id="membership-amount" name="membership-amount" min="0" step="0.01" placeholder="0" value="0.00" required>
        </div>
      </div>

      <div class="">
        <label for="membership-notes" class="form-label">Notes <span class="text-muted">(optional)</span></label>
        <textarea class="form-control" id="membership-notes" name="membership-notes" rows="3"></textarea>
        <div class="small">Place explanatory notes here.</div>
      </div>
    </div>
  </div>

<?php } else { ?>

  <div class="alert alert-warning">
    <p class="mb-0">
      <strong>
        There are no additional memberships available to assign to <?= htmlspecialchars($member->MForename . ' ' . $member->MSurname) ?>
      </strong>
    </p>
    <p class="mb-0">
      This is because they already hold all available memberships for the current membership year, or available memberships have already been assigned to a batch for the <?= htmlspecialchars($batch->yearName) ?> membership year.
    </p>
  </div>

<?php } ?>

<?php

$html = ob_get_clean();

// reportError(htmlentities($html));

header('content-type: application/json');
echo json_encode([
  'html' => $html
]);
