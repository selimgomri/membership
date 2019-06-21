<?php

global $db;

$access = $_SESSION['AccessLevel'];
$count = 0;
$selection = "";
$sql = "";
if ((isset($_POST["squadID"])) && (isset($_POST["search"]))) {
  // get the squadID parameter from post
  $squadID = $_POST["squadID"];
  // get the search term parameter from post
  $search = $_POST["search"];

  $search_terms = explode(' ', $search);
  $names = [];
  $sql = "";
  for ($i = 0; $i < sizeof($search_terms); $i++) {
    if ($i > 0) {
      $sql .= " OR ";
    }
    $sql .= " members.MForename COLLATE utf8mb4_general_ci LIKE ? OR members.MSurname COLLATE utf8mb4_general_ci LIKE ? ";
    for ($y = 0; $y < 2; $y++) {
      $names[] = "%" . $search_terms[$i] . "%";
    }
  }
  if (sizeof($search_terms) == 1 && $search_terms[0] == null) {
    $sql = " members.MemberID IS NOT NULL ";
    $names = [];
  }

  $selection = $sql;

  $query;

  if ($_POST['type'] == "orphan") {
    // Search the database for the results
		if ($squadID == "allSquads") {
      $query = $db->prepare("SELECT members.MemberID, members.MForename,
      members.MSurname, members.ASANumber, squads.SquadName,
      members.DateOfBirth, squads.SquadID FROM (members INNER JOIN squads ON
      members.SquadID = squads.SquadID) WHERE members.UserID IS NULL AND (" .
      $selection . ") ORDER BY `members`.`MForename`, `members`.`MSurname`
      ASC");
	  }
	  else {
      $query = $db->prepare("SELECT members.MemberID, members.MForename,
      members.MSurname, members.ASANumber, squads.SquadName,
      members.DateOfBirth FROM (members INNER JOIN squads ON members.SquadID =
      squads.SquadID) WHERE members.UserID IS NULL AND squads.SquadID = ? AND (" .
      $selection . ") ORDER BY `members`.`MForename` , `members`.`MSurname`
      ASC");
      $names = array_merge([$_POST["squadID"]], $names);
	  }
  } else {
    if ($squadID == "allSquads") {
      $query = $db->prepare("SELECT members.MemberID, members.MForename,
      members.MSurname, members.ASANumber, squads.SquadName,
      members.DateOfBirth, squads.SquadID FROM (members INNER JOIN squads ON
      members.SquadID = squads.SquadID) WHERE (" . $selection . ") ORDER BY
      `members`.`MForename` , `members`.`MSurname` ASC");
	  }
	  else {
      $query = $db->prepare("SELECT members.MemberID, members.MForename,
      members.MSurname, members.ASANumber, squads.SquadName,
      members.DateOfBirth FROM (members INNER JOIN squads ON members.SquadID =
      squads.SquadID) WHERE squads.SquadID = ? AND (" . $selection . ") ORDER
      BY `members`.`MForename` , `members`.`MSurname` ASC");
      $names = array_merge([$_POST["squadID"]], $names);
	  }
  }
}

$query->execute($names);

$count = 0;

$row = $query->fetch(PDO::FETCH_ASSOC);

?>

<?php if ($row != null) { ?>

<div class="list-group">

<?php do {
  $count += 1;
  // $row = mysqli_fetch_array($resultX, MYSQLI_ASSOC);
  $swimmerLink = autoUrl("swimmers/" . $row['MemberID'] . "");
  $DOB = date('j F Y', strtotime($row['DateOfBirth']));
  $age = date_diff(date_create($row['DateOfBirth']), date_create('today'))->y;
  //$ageEoY = date('Y') - date('Y', strtotime($row['DateOfBirth'])); ?>

  <a href="<?=autoUrl("swimmers/" . $row['MemberID'])?>" class="list-group-item list-group-item-action">
    <div class="row align-items-center">
      <div class="col-12 col-sm-4 col-md-3">
        <p class="mb-0">
          <strong class="text-primary">
            <?=htmlspecialchars($row['MForename'] . " " . $row['MSurname'])?>
          </strong>
        </p>
        <p class="mb-0"><?=htmlspecialchars($row['SquadName'])?> Squad</p>
        <div class="mb-2 d-sm-none"></div>
      </div>
      <div class="col text-sm-right">
        <p class="mb-0">
          <strong>Born:</strong> <?=$DOB?> (<em>Age <?=htmlspecialchars($age)?></em>)
        </p>
        <p class="mb-0">
          <strong>Attendance:</strong> <?=getAttendanceByID($link, $row['MemberID'], 4)?>%
        </p>
        <p class="mb-0">
          <strong>SE Number:</strong> <?=htmlspecialchars($row['ASANumber'])?>
        </p>
      </div>
    </div>
  </a>

<?php } while ($row = $query->fetch(PDO::FETCH_ASSOC)); ?>

</div>

<?php } else { ?>

<div class="alert alert-warning">
  <strong>No members found for that squad</strong><br>
  Please try another search
</div>

<?php } ?>
