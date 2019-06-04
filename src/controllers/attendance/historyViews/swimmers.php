<?php

global $db;

$pagetitle = "Attendance History by Swimmer";

$squadID = $search = "";
parse_str($_SERVER['QUERY_STRING'], $queries);
if (isset($queries['squadID'])) {
  $squadID = intval($queries['squadID']);
}
if (isset($queries['search'])) {
  $search = $queries['search'];
}

$squads = $db->query("SELECT SquadName name, SquadID id FROM squads ORDER BY SquadFee DESC, SquadName ASC");

include BASE_PATH . "views/header.php";
include BASE_PATH . "controllers/attendance/attendanceMenu.php"; ?>

<div class="container">

  <h1>Attendance History by Swimmer</h1>
  <p class="lead">View Attendance History for a swimmer</p>

  <div class="form-row">
    <div class="col-md-6 mb-3">
      <label class="sr-only" for="squad">Select a Squad</label>
      <select class="custom-select" placeholder="Select a Squad" id="squad" name="squad">
        <option value="allSquads">Show All Squads</option>
        <?php while ($squad = $squads->fetch(PDO::FETCH_ASSOC)) { ?>
        <option value="<?=$squad['id']?>" <?php if ($squad['id'] == $id) { ?>selected<?php } ?>>
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
<?php include BASE_PATH . "views/footer.php";
?>
