<?php
$access = $_SESSION['AccessLevel'];
if ($access != "Admin" && $access != "Coach" && $access != "Galas") {
  halt(403);
}

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php"; ?>

<div class="container">
  <h1>Member Access Keys</h1>
  <p class="lead">See access keys.</p>
  <p><a href="<? echo autoUrl("swimmers/accesskeys-csv"); ?>" class="btn btn-outline-dark">Download as a CSV for Mailmerge</a></p>

<?php

$sqlSwim = "SELECT members.MemberID, members.MForename, members.MSurname, members.ASANumber, squads.SquadName, members.AccessKey FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) ORDER BY `members`.`MForename` , `members`.`MSurname` ASC;";
$result = mysqli_query($link, $sqlSwim);
$swimmerCount = mysqli_num_rows($result);
if ($swimmerCount > 0) { ?>
  <div class="table-responsive-md">
    <? if (app('request')->isMobile()) {
      ?><table class="table table-sm"><?
    } else {
      ?><table class="table table-hover"><?
    }?>
      <thead class="thead-light">
        <tr>
          <th>Name</th>
          <th>Squad</th>
          <th>ASA Number</th>
          <th>Access Key</th>
        </tr>
      </thead>
      <tbody>
  <?php
  $resultX = mysqli_query($link, $sqlSwim);
  for ($i = 0; $i < $swimmerCount; $i++) {
    $swimmersRowX = mysqli_fetch_array($resultX, MYSQLI_ASSOC); ?>
    <tr>
      <td><?php echo $swimmersRowX['MForename'] . " " . $swimmersRowX['MSurname'];?></td>
      <td><?php echo $swimmersRowX['SquadName'];?></td>
      <?php if ($swimmersRowX['ASANumber'] == null) {
        $memID = $swimmersRowX['MemberID'];
        $asaN = "CLSX" . $memID;
        ?><td><samp><?php echo $asaN;?><samp></td><?php
        $sql = "UPDATE `members` SET ASANumber = '$asaN' WHERE `MemberID` = '$memID';";
        mysqli_query($link, $sql);
      }
      else { ?>
        <td><samp><?php echo $swimmersRowX['ASANumber']; ?></samp></td>
      <?php } ?>
        <td><samp><?php echo $swimmersRowX['AccessKey']; ?></samp></td>
    </tr>
  <?php } ?>
      </tbody>
    </table>
  </div>
<?php } ?>

</div>

<?php

  include BASE_PATH . "views/footer.php";

  ?>
