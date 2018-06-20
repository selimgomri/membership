<?

$id = mysqli_real_escape_string($link, $id);

$sql = "SELECT * FROM ((`extrasRelations` INNER JOIN `members` ON members.MemberID = extrasRelations.MemberID) INNER JOIN `extras` ON extras.ExtraID = extrasRelations.ExtraID) WHERE `ExtraID` = '$id';";
$result = mysqli_query($link, $sql);

?>

<div class="my-3 p-3 bg-white rounded box-shadow">
  <? if (mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
  <h2 class="border-bottom border-gray p"><? echo $row['MForename'] . " " . $row['MSurname']; ?></h2>
  <? } else { ?>
  <div class="alert alert-info mb-0">
    <strong>There are no swimmers linked to this extra</strong>
  </div>
  <? } ?>
</div>
