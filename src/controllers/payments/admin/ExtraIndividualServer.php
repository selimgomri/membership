<?

$id = mysqli_real_escape_string($link, $id);

if ($_POST['squadSelect'] == null) {
  $sql = "SELECT * FROM (((`extrasRelations` INNER JOIN `members` ON members.MemberID = extrasRelations.MemberID) INNER JOIN `extras` ON extras.ExtraID = extrasRelations.ExtraID) INNER JOIN `squads` ON members.SquadID = squads.SquadID) WHERE `extrasRelations`.`ExtraID` = '$id';";
  $result = mysqli_query($link, $sql);
  $count = mysqli_num_rows($result);

  ?>

  <div class="my-3 p-3 bg-white rounded box-shadow">
    <? if ($count > 0) {
    for ($i = 0; $i < $count; $i++) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if ($i != $count-1) { ?>
    <div class="border-bottom border-gray py-2">
    <? } else { ?>
    <div class="py-1">
    <? } ?>
      <p class="mb-0">
        <strong>
          <? echo $row['MForename'] . " " . $row['MSurname']; ?>
        </strong>
      </p>
      <p class="mb-0">
        <? echo $row['SquadName']; ?>
      </p>
    </div>
    <? }
    } else { ?>
    <div class="alert alert-info mb-0">
      <strong>There are no swimmers linked to this extra</strong>
    </div>
    <? } ?>
  </div>
<?
} else if ($_POST['squadSelect'] != null) {
  $squad = mysqli_real_escape_string($link, $_POST['squadSelect']);
  $sql = "SELECT * FROM `members` WHERE `SquadID` = '$squad' ORDER BY `MForename` ASC, `MSurname` ASC;";
  $result = mysqli_query($link, $sql);

  for ($i = 0; $i < mysqli_num_rows($result); $i++) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
    <option value="<? echo $row['MemberID']; ?>">
      <? echo $row['MForename'] . " " . $row['MSurname']; ?>
    </option>
  <? }
}
