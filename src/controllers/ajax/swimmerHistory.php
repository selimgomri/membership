<?php

$db = app()->db;
$tenant = app()->tenant;

$access = $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'];
$count = 0;
if ($access == "Committee" || $access == "Admin" || $access == "Coach" || $access == "Galas") {
  $sql = "";
  $get = null;
  if ((isset($_POST["squadID"])) && (isset($_POST["search"]))) {
    // get the squadID parameter from post
    $squadID = $_POST["squadID"];
    // get the search term parameter from post
    $search = $_POST["search"];

    $getSquads = $db->prepare("SELECT SquadName FROM squads INNER JOIN squadMembers ON squadMembers.Squad = squads.SquadID WHERE squadMembers.Member = ?");

    // Search the database for the results
		if ($squadID == "allSquads") {
      $get = $db->prepare("SELECT members.MemberID, members.MForename, members.MSurname, members.ASANumber, members.DateOfBirth FROM members WHERE members.Tenant = ? AND members.MSurname COLLATE utf8mb4_general_ci LIKE ? ORDER BY members.MForename, members.MSurname ASC");
      $get->execute([
        $tenant->getId(),
        '%' . $search . '%'
      ]);
	  }
	  else {
      $get = $db->prepare("SELECT members.MemberID, members.MForename, members.MSurname, members.ASANumber, members.DateOfBirth FROM members INNER JOIN squadMembers ON members.MemberID = squadMembers.Member WHERE members.Tenant = ? AND squadMembers.Squad = ? AND members.MSurname COLLATE utf8mb4_general_ci LIKE ? ORDER BY members.MForename , members.MSurname ASC");
      $get->execute([
        $tenant->getId(),
        $squadID,
        '%' . $search . '%'
      ]);
	  }
  }

  $row = $get->fetch(PDO::FETCH_ASSOC);
  if ($row != null) { ?>
  <table class="table table-hover bg-white">
    <thead class="thead-light">
      <tr>
        <th>Name</th>
        <th>Squad</th>
        <th><abbr title="4 Week Rolling Attendance">Attendance</abbr></th>
      </tr>
    </thead>
    <tbody>
    <?php do {
      $getSquads->execute([
        $row['MemberID']
      ]);
      $squad = $getSquads->fetch(PDO::FETCH_ASSOC);
    ?>
    <tr>
      <td>
        <a href="<?=autoUrl("attendance/history/swimmers/" . $row['MemberID'])?>">
          <?=htmlspecialchars($row['MForename'] . " " . $row['MSurname'])?>
        </a>
      </td>
      <td>
      <?php if ($squad) { ?>
        <ul class="list-unstyled mb-0">
          <?php do { ?>
            <li><?= htmlspecialchars($squad['SquadName']) ?></li>
          <?php } while ($squad = $getSquads->fetch(PDO::FETCH_ASSOC)); ?>
        </ul>
      <?php } else { ?>
        No squads
      <?php } ?></td>
      <td><?=getAttendanceByID(null, $row['MemberID'], 4)?>%</td>
    </tr>
    <?php } while ($row = $get->fetch(PDO::FETCH_ASSOC)); ?>
    </tbody>
  </table>

<?php } else { ?>
  <div class="alert alert-warning">
    <strong>No members found for that squad</strong> <br>
    Please try another search
  </div>
<?php } ?>

<?php } else {
  halt(404);
}
