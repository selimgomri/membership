<?php

global $db;

if ($_POST['response'] == "getSwimmers") {
  $swimmers = $db->prepare("SELECT * FROM (((`extrasRelations` INNER JOIN `members` ON members.MemberID = extrasRelations.MemberID) INNER JOIN `extras` ON extras.ExtraID = extrasRelations.ExtraID) INNER JOIN `squads` ON members.SquadID = squads.SquadID) WHERE `extrasRelations`.`ExtraID` = ?");
  $swimmers->execute([$id]);

  $row = $swimmers->fetch(PDO::FETCH_ASSOC);

  ?>

  <div class="">
    <?php if ($row != null) { ?>
    <ul class="list-group">
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
    <?php } else { ?>
    <div class="alert alert-info mb-0">
      <strong>There are no swimmers linked to this extra</strong>
    </div>
    <?php } ?>
  </div>
<?php
} else if ($_POST['response'] == "squadSelect") {
  $getSwimmers = $db->prepare("SELECT * FROM `members` WHERE `SquadID` = ? ORDER BY `MForename` ASC, `MSurname` ASC");
  $getSwimmers->execute([$_POST['squadSelect']]);

  ?>
  <option selected>
    Select a swimmer
  </option>
  <?php
  while ($row = $getSwimmers->fetch(PDO::FETCH_ASSOC)) { ?>
    <option value="<?=htmlspecialchars($row['MemberID'])?>">
      <?=htmlspecialchars($row['MForename'] . " " . $row['MSurname'])?>
    </option>
  <?php }
} else if ($_POST['response'] == "insert") {
  $swimmer = $_POST['swimmerInsert'];
  if ($swimmer != null && $swimmer != "") {
    try {
      $addToExtra = $db->prepare("INSERT INTO `extrasRelations` (`ExtraID`, `MemberID`) VALUES (?, ?)");
      $addToExtra->execute([$id, $swimmer]);
    } catch (Exception $e) {
      halt(500);
    }
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
