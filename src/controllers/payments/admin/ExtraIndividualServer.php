<?

$id = mysqli_real_escape_string($link, $id);

if ($_POST['response'] == "getSwimmers") {
  $sql = "SELECT * FROM (((`extrasRelations` INNER JOIN `members` ON members.MemberID = extrasRelations.MemberID) INNER JOIN `extras` ON extras.ExtraID = extrasRelations.ExtraID) INNER JOIN `squads` ON members.SquadID = squads.SquadID) WHERE `extrasRelations`.`ExtraID` = '$id';";
  $result = mysqli_query($link, $sql);
  $count = mysqli_num_rows($result);

  ?>

  <div class="my-3 p-3 bg-white rounded box-shadow">
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
          <button type="button" id="RelationDrop-<? echo $row['RelationID']; ?>"
            class="btn btn-link" value="<? echo $row['RelationID']; ?>">
            Remove
          </button>
        </div>
      </div>
    </div>
    <? }
    } else { ?>
    <div class="alert alert-info mb-0">
      <strong>There are no swimmers linked to this extra</strong>
    </div>
    <? } ?>
  </div>
<?
} else if ($_POST['response'] == "squadSelect") {
  $squad = mysqli_real_escape_string($link, $_POST['squadSelect']);
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
} else if ($_POST['response'] == "insert") {
  $swimmer = mysqli_real_escape_string($link, $_POST['swimmerInsert']);
  if ($swimmer != null && $swimmer != "") {
    $sql = "INSERT INTO `extrasRelations` (`ExtraID`, `MemberID`) VALUES ('$id', $swimmer);";
    if (!mysqli_query($link, $sql)) {
      halt(500);
    }
  }
} else if ($_POST['response'] == "dropRelation") {
  $relation = mysqli_real_escape_string($link, $_POST['relation']);
  $sql = "DELETE FROM `extrasRelations` WHERE `RelationID` = '$relation';";
  if (!mysqli_query($link, $sql)) {
    halt(500);
  }
} else {
  halt(404);
}
