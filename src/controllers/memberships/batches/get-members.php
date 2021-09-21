<?php

if (!isset($_POST['id'])) halt(404);

$id = $_POST['id'];

$user = app()->user;
$db = app()->db;

$getBatch = $db->prepare("SELECT membershipBatch.ID id, membershipYear.ID yearId, membershipBatch.Completed completed, DueDate due, Total total, membershipYear.Name yearName, membershipYear.StartDate yearStart, membershipYear.EndDate yearEnd, PaymentTypes payMethods, PaymentDetails payDetails, membershipBatch.User `user` FROM membershipBatch INNER JOIN membershipYear ON membershipBatch.Year = membershipYear.ID INNER JOIN users ON users.UserID = membershipBatch.User WHERE membershipBatch.ID = ? AND users.Tenant = ?");
$getBatch->execute([
  $id,
  app()->tenant->getId(),
]);

$batch = $getBatch->fetch(PDO::FETCH_OBJ);

if (!$batch) halt(404);

if (!$user->hasPermission('Admin')) halt(404);

// Get members for this user
$getMembers = $db->prepare("SELECT MForename, MSurname, MemberID FROM members WHERE UserID = ? AND Active ORDER BY MForename ASC, MSurname ASC;");
$getMembers->execute([
  $batch->user,
]);

ob_clean();
ob_start();

?>

<form id="add-membership-form" class="needs-validation" novalidate>

  <div class="">
    <label class="form-label" for="member">Select member</label>
    <select class="form-select" id="member" name="member" required>
      <option disabled selected value="null">Select a member</option>
      <?php while ($member = $getMembers->fetch(PDO::FETCH_OBJ)) { ?>
        <option value="<?= htmlspecialchars($member->MemberID) ?>"><?= htmlspecialchars($member->MForename . ' ' . $member->MSurname) ?></option>
      <?php } ?>
    </select>
  </div>

  <div id="add-membership-form-details"">

  </div>

</form>

<?php

$html = ob_get_clean();

// reportError($html);

header('content-type: application/json');
echo json_encode([
  'html' => $html
]);
