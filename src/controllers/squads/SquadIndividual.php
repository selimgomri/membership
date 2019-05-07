<?php


global $db;
$codesOfConduct = $db->query("SELECT Title, ID FROM posts WHERE `Type` = 'conduct_code' ORDER BY Title ASC");

$id = mysqli_real_escape_string($link, $id);
$access = $_SESSION['AccessLevel'];

$squadNameUpdate = $squadFeeUpdate = $squadCoachUpdate = $squadTimetableUpdate = $squadCoCUpdate = "";
$sql = "SELECT * FROM `squads` WHERE squads.SquadID = '$id';";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$squadName = $row['SquadName'];
$squadFee= $row['SquadFee'];
$squadCoach = $row['SquadCoach'];
$squadTimetable = $row['SquadTimetable'];
$squadCoC = $row['SquadCoC'];
$squadDeleteKey = $row['SquadKey'];

if (isset($_POST['squadName'])) {
  $postContent = mysqli_real_escape_string($link, trim((ucwords($_POST['squadName']))));
  if ($postContent != $squadName) {
    $sql = "UPDATE `squads` SET `SquadName` = '$postContent' WHERE `SquadID` = '$id'";
    mysqli_query($link, $sql);
    $squadNameUpdate = true;
    $update = true;
  }
}
if (isset($_POST['squadFee'])) {
  $postContent = mysqli_real_escape_string($link, number_format(trim((ucwords($_POST['squadFee']))),2,'.',''));
  if ($postContent != $squadFee) {
    $sql = "UPDATE `squads` SET `SquadFee` = '$postContent' WHERE `SquadID` = '$id'";
    mysqli_query($link, $sql);
    $squadFeeUpdate = true;
    $update = true;
  }
}
if (isset($_POST['squadCoach'])) {
  $postContent = mysqli_real_escape_string($link, trim((ucfirst($_POST['squadCoach']))));
  if ($postContent != $squadCoach) {
    $sql = "UPDATE `squads` SET `SquadCoach` = '$postContent' WHERE `SquadID` = '$id'";
    mysqli_query($link, $sql);
    $squadCoachUpdate = true;
    $update = true;
  }
}
if (isset($_POST['squadTimetable'])) {
  $postContent = mysqli_real_escape_string($link, trim((strtolower($_POST['squadTimetable']))));
  if ($postContent != $squadTimetable) {
    $sql = "UPDATE `squads` SET `SquadTimetable` = '$postContent' WHERE `SquadID` = '$id'";
    mysqli_query($link, $sql);
    $squadTimetableUpdate = true;
    $update = true;
  }
}
if (isset($_POST['squadCoC'])) {
  $postContent = mysqli_real_escape_string($link, trim((lcfirst($_POST['squadCoC']))));
  if ($postContent != $squadCoC) {
    $sql = "UPDATE `squads` SET `SquadCoC` = '$postContent' WHERE `SquadID` = '$id'";
    mysqli_query($link, $sql);
    $squadCoCUpdate = true;
    $update = true;
  }
}
if ($access == "Admin") {
  if (isset($_POST['squadDeleteDanger'])) {
    $postContent = mysqli_real_escape_string($link, trim((lcfirst($_POST['squadDeleteDanger']))));
    if ($postContent == $squadDeleteKey) {
      $sql = "DELETE FROM `squads` WHERE `SquadID` = '$id'";
      mysqli_query($link, $sql);
      header("Location: " . autoUrl("squads"));
    }
  }
}

$sql = "SELECT * FROM `squads` WHERE squads.SquadID = '$id';";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$title = $pagetitle = $row['SquadName'] . " Squad";


include BASE_PATH . "views/header.php";
include BASE_PATH . "views/squadMenu.php"; ?>

<div class="container">
  <h1><?=$title?></h1>

  <?php

if ($access == "Admin") { ?>

  <div class="row">
    <div class="col-md-6">
      <div class="cell">
        <form method="post">
        <h2>Details</h2>
        <p class="lead border-bottom border-gray pb-2">View or edit the squad details</p>
        <div class="form-group">
          <label for="squadName">Squad Name</label>
          <input type="text" class="form-control" id="squadName" name="squadName" placeholder="Enter Squad Name" value="<?=htmlspecialchars($row['SquadName'])?>">
        </div>
        <div class="form-group">
          <label for="squadFee" class="form-label">Squad Fee</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">&pound;</span>
            </div>
            <input type="text" class="form-control" id="squadFee" name="squadFee" aria-describedby="squadFeeHelp" placeholder="eg 50.00" value="<?=htmlspecialchars($row['SquadFee'])?>">
          </div>
          <small id="squadFeeHelp" class="form-text text-muted">A squad can have a fee of &pound;0.00 if it represents a group for non paying members</small>
        </div>
        <div class="form-group">
          <label for="squadCoach">Squad Coach</label>
          <input type="text" class="form-control" id="squadCoach" name="squadCoach" placeholder="Enter Squad Coach" value="<?=htmlspecialchars($row['SquadCoach'])?>">
        </div>
        <div class="form-group">
          <label for="squadTimetable">Squad Timetable</label>
          <input type="text" class="form-control" id="squadTimetable" name="squadTimetable" placeholder="Enter Squad Timetable Address" value="<?=htmlspecialchars($row['SquadTimetable'])?>">
        </div>
        <div class="form-group">
          <label for="squadCoC">Squad Code of Conduct</label>
          <select class="custom-select" id="squadCoC" name="squadCoC" aria-describedby="conductSelectHelpBlock">
          <?php while ($codeDetails = $codesOfConduct->fetch(PDO::FETCH_ASSOC)) { ?>
          <option value="<?=htmlspecialchars($codeDetails['ID'])?>" <?php if ($row['SquadCoC'] == codeDetails['ID']) { ?>selected<?php } ?>>
            <?=htmlspecialchars($codeDetails['Title'])?>
          </option>
          <?php } ?>
        </select>
        <small id="conductSelectHelpBlock" class="form-text text-muted">
          You can create a code of conduct in the <strong>Posts</strong> section of this system and select it here. It will be used in various parts of this system, including when new members sign up and when members renew.
        </small>
        </div>
        <div class="alert alert-danger">
          <div class="form-group mb-0">
            <label for="squadDeleteDanger"><strong>Danger Zone</strong> <br>Delete this Squad with this Key "<span class="mono"><?=$squadDeleteKey?></span>"</label>
            <input type="text" class="form-control mono" id="squadDeleteDanger" name="squadDeleteDanger" aria-describedby="squadDeleteDangerHelp" placeholder="Enter the key" onselectstart="return false" onpaste="return false;" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off>
            <small id="squadDeleteDangerHelp" class="form-text">Enter the key in quotes above and press submit. This will delete this squad.</small>
          </div>
        </div>
        <p class="mb-0">
          <button class="btn btn-success" type="submit">Update</button>
        </p>
      </form>
    </div>
  </div>

  <div class="col-md-6">

  <?php

  $sql = "SELECT `Gender` FROM `members` WHERE `SquadID` = '$id' AND `Gender` = 'Male';";
  $result = mysqli_query($link, $sql);
  $male = mysqli_num_rows($result);
  $sql = "SELECT `Gender` FROM `members` WHERE `SquadID` = '$id' AND `Gender` = 'Female';";
  $result = mysqli_query($link, $sql);
  $female = mysqli_num_rows($result);

    if ($male+$female>0) { ?>
        <div class="cell">
        <h2>Statistics</h2>
        <p class="lead border-bottom border-gray pb-2 mb-0">These statistics are gathered from our system</p>
        <canvas id="myChart"></canvas>
        </div></div>

    <?php } ?>
    </div>
  </div>
  <?php
  } else {
  ?>
  <div class="row">
    <div class="col-md-6">
      <div class="cell">
        <h2 class="border-bottom border-gray pb-2">Squad Details</h2>
        <ul class="mb-0">
        <?php
        if ($row['SquadFee'] > 0) { ?>
          <li>Squad Fee: &pound;<?=$row['SquadFee']?></li>
        <?php } else { ?>
          <li>There is no fee for this squad</li>
        <?php } ?>
          <li>Squad Coach: <?=$row['SquadCoach']?></li>
          <li><a href="<?=$row['SquadTimetable']?>">Squad Timetable</a></li>
          <li><a href="<?=$row['SquadCoC']?>">Squad Code of Conduct</a></li>
        </ul>
      </div>
    </div>

    <?php
      $sql = "SELECT `Gender` FROM `members` WHERE `SquadID` = '$id' AND `Gender` = 'Male';";
      $result = mysqli_query($link, $sql);
      $male = mysqli_num_rows($result);
      $sql = "SELECT `Gender` FROM `members` WHERE `SquadID` = '$id' AND `Gender` = 'Female';";
      $result = mysqli_query($link, $sql);
      $female = mysqli_num_rows($result);

        if ($male+$female>0) {
        ?>
            <div class="col-md-6">
              <div class="cell">
                <h2>Statistics</h2>
                <p class="lead border-bottom border-gray pb-2 mb-0">These statistics are gathered from our system</p>
                <canvas id="myChart"></canvas>
              </div>
            </div>
          <?php } ?>
          </div>
  <?php }

  $sql = "SELECT * FROM `members` WHERE `SquadID` = '$id' ORDER BY `MForename` ASC, `MSurname` ASC;";
  $result = mysqli_query($link, $sql);

  if (mysqli_num_rows($result) > 0) { ?>
    <div class="container">
      <div class="">
        <h2 class="">Swimmer in this Squad</h2>
        <table class="table">
          <tbody>
            <?php
    for ($i = 0; $i < mysqli_num_rows($result); $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
      <tr><td>
      <a href="<?=autoUrl("swimmers/" . $row['MemberID'])?>"><?=$row['MForename'] . ' ' . $row['MSurname']?></a></td></tr>
      <?php
    } ?>

          </tbody>
        </table>
      </div>
    </div>';
  <?php } ?>

  </div>
</div>

<script src="<?=autoUrl("public/js/Chart.min.js")?>"></script>
<script>
var ctx = document.getElementById('myChart').getContext('2d');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'pie',

    // The data for our dataset
    data: {
        labels: ["Male", "Female"],
        datasets: [{
            label: "<?=$row['SquadName']?> Split",
            data: [<?=$male?>, <?=$female?>],
            backgroundColor: [
    					'#bd0000',
    					'#005fbd'
    				],
        }],
    },

    // Configuration options go here
    options: {}
});
</script>

<?php include BASE_PATH . "views/footer.php";

?>
