<?php

global $db;

if ($_POST['response'] == "getSwimmers") {
  $swimmers = $db->prepare("SELECT MForename, MSurname, SquadName, ReferenceID FROM (((`targetedListMembers` INNER JOIN `members` ON
  members.MemberID = targetedListMembers.ReferenceID) INNER JOIN `targetedLists`
  ON targetedLists.ID = targetedListMembers.ListID) INNER JOIN `squads` ON
  members.SquadID = squads.SquadID) WHERE `targetedListMembers`.`ListID` =
  ? ORDER BY ReferenceID ASC");
  $swimmers->execute([$id]);
  $row = $swimmers->fetch(PDO::FETCH_ASSOC);

  ?>

  <div class="">
    <?php if ($row != null) { ?>
    <div class="card">
      <div class="card-header">
        List members
      </div>
      <ul class="list-group list-group-flush">
        <?php do { ?>
        <li class="list-group-item">
          <div class="row align-items-center">
            <div class="col-auto">
              <p class="mb-0">
                <strong>
                  <?=htmlspecialchars($row['MForename'] . " " . $row['MSurname'])?>
                </strong>
              </p>
              <p class="mb-0">
                <?=htmlspecialchars($row['SquadName'])?>
              </p>
            </div>
            <div class="col text-right">
              <button type="button" id="RelationDrop-<?=$row['ReferenceID']?>"
                class="btn btn-link" value="<?=$row['ReferenceID']?>">
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
      <strong>There are no swimmers linked to this targeted list</strong>
    </div>
    <?php } ?>
  </div>
<?php
} else if ($_POST['response'] == "squadSelect") {
  $squad = $_POST['squadSelect'];
  $members == null;
  if ($squad != "all") {
    $members = $db->prepare("SELECT MemberID, MForename, MSurname FROM `members` WHERE `SquadID` = ? ORDER BY `MForename` ASC, `MSurname` ASC");
    $members->execute([$squad]);
  } else {
    $members = $db->query("SELECT MemberID, MForename, MSurname FROM `members` ORDER BY `MForename` ASC, `MSurname` ASC");
  } ?>
  <option selected>
    Select a swimmer
  </option>
  <?php while ($row = $members->fetch(PDO::FETCH_ASSOC)) { ?>
    <option value="<?=$row['MemberID']?>">
      <?=htmlspecialchars($row['MForename'] . " " . $row['MSurname'])?>
    </option>
  <?php }
} else if ($_POST['response'] == "insert") {
  $swimmer = $_POST['swimmerInsert'];
  if ($swimmer != null && $swimmer != "") {
    try {
      $insert = $db->prepare("INSERT INTO `targetedListMembers` (`ListID`, `ReferenceID`, `ReferenceType`) VALUES (?, ?, ?)");
      $insert->execute([
        $id,
        $swimmer,
        'Member'
      ]);
    } catch (Exception $e) {
      halt(500);
    }
  }
} else if ($_POST['response'] == "dropRelation") {
  try {
    $drop = $db->prepare("DELETE FROM `targetedListMembers` WHERE `ReferenceID` = ?");
    $drop->execute([$_POST['relation']]);
  } catch (Exception $e) {
    halt(500);
  }
} else {
  halt(404);
}
