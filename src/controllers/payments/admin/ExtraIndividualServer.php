<?php

global $db;

if ($_POST['response'] == "getSwimmers") {
  $swimmers = $db->prepare("SELECT * FROM (((`extrasRelations` INNER JOIN `members` ON members.MemberID = extrasRelations.MemberID) INNER JOIN `extras` ON extras.ExtraID = extrasRelations.ExtraID) INNER JOIN `squads` ON members.SquadID = squads.SquadID) WHERE `extrasRelations`.`ExtraID` = ?");
  $swimmers->execute([$id]);

  $row = $swimmers->fetch(PDO::FETCH_ASSOC);

  ?>

  <div class="">
    <?php if ($row != null) { ?>
    <div class="card">
      <div class="card-header">
        Extra members
      </div>
      <ul class="list-group list-group-flush">
        <?php do { ?>
        <li class="list-group-item">
          <div class="row align-items-center">
            <div class="col-auto">
              <p class="mb-0">
                <strong>
                  <?php echo $row['MForename'] . " " . $row['MSurname']; ?>
                </strong>
              </p>
              <p class="mb-0">
                <?php echo $row['SquadName']; ?>
              </p>
            </div>
            <div class="col text-right">
              <button type="button" id="RelationDrop-<?php echo $row['RelationID']; ?>"
                class="btn btn-link" value="<?php echo $row['RelationID']; ?>">
                Remove
              </button>
            </div>
          </div>
        </li>
        <?php } while ($row = $swimmers->fetch(PDO::FETCH_ASSOC)); ?>
      </ul>
    </div>
    <?php } else { ?>
    <div class="alert alert-info mb-0">
      <strong>There are no swimmers linked to this extra</strong>
    </div>
    <?php } ?>
  </div>
<?php
} else if ($_POST['response'] == "squadSelect") {

  if ($_POST['squadSelect'] == 'Choose...') {
    echo json_encode([
      'state' => false,
      'swimmerSelectContent' => '<option value="null" selected>Please select a squad</option>'
    ]);
  } else {
    $getSwimmers = $db->prepare("SELECT MemberID, MForename, MSurname FROM `members` WHERE SquadID = ? ORDER BY `MForename` ASC, `MSurname` ASC");
    $getSwimmers->execute([$_POST['squadSelect']]);
    $output = '<option value="null" selected>Select a swimmer</option>';
    while ($row = $getSwimmers->fetch(PDO::FETCH_ASSOC)) {
      $output .= '<option value="' . htmlspecialchars($row['MemberID']) . '">' . htmlspecialchars($row['MForename'] . " " . $row['MSurname']) . '</option>';
    }
    echo json_encode([
      'state' => true,
      'swimmerSelectContent' => $output
    ]);
  }
} else if ($_POST['response'] == "insert") {

  $responseData = [];

  $swimmer = $_POST['swimmerInsert'];
  if ($swimmer != null && $swimmer != "") {
    try {
      $memberName = $db->prepare("SELECT MForename fn, MSurname sn FROM members WHERE MemberID = ?");
      $memberName->execute([$swimmer]);
      $name = $memberName->fetch(PDO::FETCH_ASSOC);
      

      if (!$name) {
        throw new Exception('There is no such member');
      }

      // Check not already there
      $getCount = $db->prepare("SELECT COUNT(*) FROM `extrasRelations` WHERE ExtraID = ? AND MemberID = ?");
      $getCount->execute([$id, $swimmer]);
      if ($getCount->fetchColumn() > 0) {
        throw new Exception($name['fn'] . ' ' . $name['sn'] . ' is already assigned to this extra');
      } else {
        $addToExtra = $db->prepare("INSERT INTO `extrasRelations` (`ExtraID`, `MemberID`) VALUES (?, ?)");
        $addToExtra->execute([$id, $swimmer]);

        $memberName = $db->prepare("SELECT MForename fn, MSurname sn FROM members WHERE MemberID = ?");
        $memberName->execute([$swimmer]);

        $responseData = [
          'alertClass' => 'alert-success',
          'alertContent' => '<p class="mb-0"><strong>' . htmlspecialchars($name['fn'] . ' ' . $name['sn']) . ' added to extra</strong></p>',
          'status' => true
        ];
      }
    } catch (Exception $e) {
      $responseData = [
        'alertClass' => 'alert-danger',
        'alertContent' => '<p class="mb-0"><strong>' . htmlspecialchars($e->getMessage()) . '</strong></p>',
        'status' => false
      ];
    }

    echo json_encode($responseData);

  }
} else if ($_POST['response'] == "dropRelation") {
  try {
    $delete = $db->prepare("DELETE FROM `extrasRelations` WHERE `RelationID` = ?");
    $delete->execute([$_POST['relation']]);
  } catch (Exception $e) {
    halt(500);
  }
} else {
  halt(404);
}
