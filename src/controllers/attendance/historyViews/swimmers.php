<?php

$db = app()->db;
$tenant = app()->tenant;

$pagetitle = "Attendance History by Swimmer";

$squadID = $search = "";
parse_str($_SERVER['QUERY_STRING'], $queries);
if (isset($queries['squadID'])) {
  $squadID = (int) $queries['squadID'];
}
if (isset($queries['search'])) {
  $search = $queries['search'];
}

$squads = $db->prepare("SELECT SquadName name, SquadID id FROM squads WHERE Tenant = ? ORDER BY SquadFee DESC, SquadName ASC");
$squads->execute([
  $tenant->getId()
]);

include BASE_PATH . "views/header.php";
include BASE_PATH . "controllers/attendance/attendanceMenu.php"; ?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('attendance')) ?>">Attendance</a></li>
				<li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('attendance/history')) ?>">History</a></li>
        <li class="breadcrumb-item active" aria-current="page">Members</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col">
        <h1>
					Attendance history by member
        </h1>
        <p class="lead mb-0">
					View up to 20 weeks of attendance history
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="form-row">
    <div class="col-md-6 mb-3">
      <label class="sr-only" for="squad">Select a Squad</label>
      <select class="custom-select" placeholder="Select a Squad" id="squad" name="squad">
        <option value="allSquads">Show All Squads</option>
        <?php while ($squad = $squads->fetch(PDO::FETCH_ASSOC)) { ?>
        <option value="<?=$squad['id']?>" <?php if ($squad['id'] == $squadID) { ?>selected<?php } ?>>
          <?=htmlspecialchars($squad['name'])?> Squad
        </option>
        <?php } ?>
      </select>
    </div>
    <div class="col-md-6 mb-3">
      <label class="sr-only" for="search">Search by Surname</label>
      <input class="form-control" placeholder="Surname" id="search" name="search" value="<?=htmlspecialchars($search)?>">
    </div>
  </div>

  <div id="output">
    <div class="ajaxPlaceholder">
      <span class="h1 d-block">
        <i class="fa fa-spin fa-circle-o-notch" aria-hidden="true"></i><br>
        Loading Content
      </span>
      If content does not display, please turn on JavaScript
    </div>
  </div>

  <script>
  function getResult() {
    var squad = document.getElementById("squad");
    var squadValue = squad.options[squad.selectedIndex].value;
    var search = document.getElementById("search");
    var searchValue = search.value;
    console.log(squadValue);
    console.log(searchValue);
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          console.log("We got here");
          document.getElementById("output").innerHTML = this.responseText;
          console.log(this.responseText);
          window.history.replaceState("string", "Title", "<?=autoUrl("attendance/history/swimmers")?>?squadID=" + squadValue + "&search=" + searchValue);
        }
      }
      xhttp.open("POST", "<?=autoURL("attendance/history/ajax/swimmers")?>", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send("squadID=" + squadValue + "&search=" + searchValue);
      console.log("Sent");
  }
  // Call getResult immediately
  getResult();

  document.getElementById("squad").onchange=getResult;
  document.getElementById("search").oninput=getResult;
  </script>

</div>
<?php $footer = new \SCDS\Footer();
$footer->render();
?>
