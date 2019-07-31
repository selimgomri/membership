<?php

global $db;

$fluidContainer = true;
$squadID = $search = "";

if (isset($_GET['squadID'])) {
  $squadID = intval($_GET['squadID']);
}
if (isset($_GET['search'])) {
  $search = $_GET['search'];
}

$pagetitle = "Swimmers";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php";

if (isset($_POST['squad'])) {
  $squadID = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['squad'])));
} ?>
<div class="container-fluid">
  <h1>Swimmers with no connected parent</h1>
  <div class="d-print-none">
    <p class="lead">A list of swimmers.</p>
    <?php
  $sql = $db->query("SELECT SquadID, SquadName FROM `squads` ORDER BY `squads`.`SquadFee` DESC;");
  ?>
  <div class="form-row">
  <div class="col-md-6 mb-3">
  <label class="sr-only" for="squad">Select a Squad</label>
  <select class="custom-select" placeholder="Select a Squad" id="squad" name="squad">
  <option value="allSquads">Show All Squads</option>;
  <?php while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['SquadID']; ?>
      <option value="<?=$row['SquadID']?>" <?php if ($squadID == $id) { ?>selected<?php } ?>><?=htmlspecialchars($row['SquadName'])?></option><?php
    } ?>
    </select></div>
    <div class="col-md-6 mb-3">
      <label class="sr-only" for="search">Search by Name</label>
      <input class="form-control" placeholder="Search by name" id="search" name="search" value="<?=htmlspecialchars($search)?>">
    </div>

  </div>

  </div>

  <div id="output">
    <div class="ajaxPlaceholder">
      <span class="h1 d-block">
        <i class="fa fa-spin fa-circle-o-notch" aria-hidden="true"></i>
        <br>Loading Content
      </span>If content does not display, please turn on JavaScript
    </div>
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
        document.getElementById("output").innerHTML = this.responseText;
        window.history.replaceState("string", "Title", "<?php echo autoUrl("swimmers/orphaned"); ?>?squadID=" + squadValue + "&search=" + searchValue);
      }
    }
    xhttp.open("POST", <?=json_encode(autoUrl("swimmers/ajax/swimmerDirectory"))?>, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("type=orphan&squadID=" + squadValue + "&search=" + searchValue);
    console.log("Sent");
}
// Call getResult immediately
getResult();

document.getElementById("squad").onchange=getResult;
document.getElementById("search").oninput=getResult;
</script>

<?php

include BASE_PATH . "views/footer.php";
