<?

$id = mysqli_real_escape_string($link, $id);

if ($_POST['response'] == "getSwimmers") {
  $sql = "SELECT * FROM (((`targetedListMembers` INNER JOIN `members` ON
  members.MemberID = targetedListMembers.ReferenceID) INNER JOIN `targetedLists`
  ON targetedLists.ID = targetedListMembers.ListID) INNER JOIN `squads` ON
  members.SquadID = squads.SquadID) WHERE `targetedListMembers`.`ListID` =
  '$id';";
  $result = mysqli_query($link, $sql);
  $count = mysqli_num_rows($result);

  ?>

  <div class="cell">
    <? if ($count > 0) {
    for ($i = 0; $i < $count; $i++) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if ($i != $count-1) { ?>
    <div class="border-bottom border-gray pb-2 mb-2">
    <? } else { ?>
    <div class="">
    <? } ?>
      <div class="row align-items-center">
        <div class="col-auto">
          <p class="mb-0">
            <strong>
              <? echo $row['MForename'] . " " . $row['MSurname']; ?>
            </strong>
          </p>
          <p class="mb-0">
            <? echo $row['SquadName']; ?>
          </p>
        </div>
        <div class="col text-right">
          <button type="button" id="RelationDrop-<? echo $row['ReferenceID']; ?>"
            class="btn btn-link" value="<? echo $row['ReferenceID']; ?>">
            Remove
          </button>
        </div>
      </div>
    </div>
    <? }
    } else { ?>
    <div class="alert alert-info mb-0">
      <strong>There are no swimmers linked to this targeted list</strong>
    </div>
    <? } ?>
  </div>
<?
} else if ($_POST['response'] == "squadSelect") {
  $squad = mysqli_real_escape_string($link, $_POST['squadSelect']);
  if ($squad != "all") {
    $sql = "SELECT * FROM `members` WHERE `SquadID` = '$squad' ORDER BY `MForename` ASC, `MSurname` ASC;";
    $result = mysqli_query($link, $sql);

    ?>
    <option selected>
      Select a swimmer
    </option>
    <?
    for ($i = 0; $i < mysqli_num_rows($result); $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
      <option value="<? echo $row['MemberID']; ?>">
        <? echo $row['MForename'] . " " . $row['MSurname']; ?>
      </option>
    <? }
  } else {
    $sql = "SELECT * FROM `members` ORDER BY `MForename` ASC, `MSurname` ASC;";
    $result = mysqli_query($link, $sql);

    ?>
    <option selected>
      Select a swimmer
    </option>
    <?

    for ($i = 0; $i < mysqli_num_rows($result); $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
      <option value="<? echo $row['MemberID']; ?>">
        <? echo $row['MForename'] . " " . $row['MSurname']; ?>
      </option>
    <? }
  }
} else if ($_POST['response'] == "insert") {
  $swimmer = mysqli_real_escape_string($link, $_POST['swimmerInsert']);
  if ($swimmer != null && $swimmer != "") {
    $sql = "INSERT INTO `targetedListMembers` (`ListID`, `ReferenceID`, `ReferenceType`) VALUES ('$id', $swimmer, 'Member');";
    if (!mysqli_query($link, $sql)) {
      halt(500);
    }
  }
} else if ($_POST['response'] == "dropRelation") {
  $relation = mysqli_real_escape_string($link, $_POST['relation']);
  $sql = "DELETE FROM `targetedListMembers` WHERE `ReferenceID` = '$relation';";
  if (!mysqli_query($link, $sql)) {
    halt(500);
  }
} else {
  halt(404);
}
