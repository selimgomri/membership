<?php
$access = $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'];
if ($access != "Admin" && $access != "Coach" && $access != "Galas") {
  halt(404);
}

$db = app()->db;
$tenant = app()->tenant;

$swimmers = $db->prepare("SELECT members.MemberID, members.MForename, members.MSurname, members.ASANumber, squads.SquadName, members.AccessKey FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.Tenant = ? ORDER BY `members`.`MForename` , `members`.`MSurname` ASC");
$swimmers->execute([
  $tenant->getId()
]);
$updateASA = $db->prepare("UPDATE `members` SET ASANumber = ? WHERE `MemberID` = ?");

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php"; ?>

<div class="container">
  <h1>Member Access Keys</h1>
  <p class="lead">See access keys.</p>
  <p><a href="<?=autoUrl("members/access-keys.csv")?>" class="btn btn-dark" download>Download as a CSV for Mailmerge</a></p>

<?php

if ($row = $swimmers->fetch(PDO::FETCH_ASSOC)) { ?>
  <div class="table-responsive-md">
    <?php if (app('request')->isMobile()) {
      ?><table class="table table-sm"><?php
    } else {
      ?><table class="table table-hover"><?php
    }?>
      <thead class="thead-light">
        <tr>
          <th>Name</th>
          <th>Squad</th>
          <th>Swim England Number</th>
          <th>Access Key</th>
        </tr>
      </thead>
      <tbody>
  <?php do { ?>
    <tr>
      <td><?=htmlspecialchars($row['MForename'] . " " . $row['MSurname'])?></td>
      <td><?=htmlspecialchars($row['SquadName'])?></td>
      <?php if ($row['ASANumber'] == null) {
        $memID = $row['MemberID'];
        $asaN = CLUB_CODE . $memID;
        ?><td><span class="mono"><?=htmlspecialchars($asaN)?></span></td><?php
        $updateASA->execute([$asaN, $memID]);
      } else { ?>
        <td><span class="mono"><?=htmlspecialchars($row['ASANumber'])?></span></td>
      <?php } ?>
        <td><samp><?=htmlspecialchars($row['AccessKey'])?></samp></td>
    </tr>
  <?php } while ($row = $swimmers->fetch(PDO::FETCH_ASSOC)); ?>
      </tbody>
    </table>
  </div>
<?php } else { ?>
<div class="alert alert-warning">
  <strong>You have no registered members</strong><br>
  Add a member to get their access keys
</div>
<?php } ?>

</div>

<?php

  $footer = new \SCDS\Footer();
$footer->render();

  ?>
