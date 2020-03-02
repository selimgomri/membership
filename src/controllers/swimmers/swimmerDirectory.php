<?php

global $db;

$squads = $db->query("SELECT SquadName name, SquadID id FROM squads ORDER BY SquadFee DESC, SquadName ASC");

$fluidContainer = true;
$squadID = $search = "";
parse_str($_SERVER['QUERY_STRING'], $queries);
if (isset($queries['squadID'])) {
  $squadID = (int) $queries['squadID'];
}
if (isset($queries['search'])) {
  $search = $queries['search'];
}

$pagetitle = "Members";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php";

if (isset($_POST['squad'])) {
  $squadID = $_POST['squad'];
} ?>
<div class="container-fluid">
  <h1 class="mb-3">Members</h1>

  <?php if ($_SESSION['AccessLevel'] == 'Admin') { ?>
  <p>
    <a href="<?=autoUrl("members/new")?>" class="btn btn-success">
      Add new member
    </a>
  </p>
  <?php } ?>

  <div class="d-print-none">
    <div class="form-row">
      <div class="col-md-6 mb-3">
        <label for="squad">Select a squad</label>
        <select class="custom-select" placeholder="Select a Squad" id="squad" name="squad">
          <option value="allSquads">Show All Squads</option>;
          <?php while ($squad = $squads->fetch(PDO::FETCH_ASSOC)) { ?>
          <option value="<?=$squad['id']?>" <?php if ($squad['id'] == $squadID) { ?>selected<?php } ?>>
            <?=htmlspecialchars($squad['name'])?> Squad
          </option>
          <?php } ?>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label for="search">Search by name</label>
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
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("output").innerHTML = this.responseText;
      window.history.replaceState("string", "Title", "<?=autoUrl("members")?>?squadID=" + squadValue + "&search=" + searchValue);
    }
  }
  xhttp.open("POST", "<?=autoUrl("members/ajax/swimmerDirectory")?>", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("squadID=" + squadValue + "&search=" + searchValue);
}
// Call getResult immediately
getResult();

document.getElementById("squad").onchange=getResult;
document.getElementById("search").oninput=getResult;
</script>

<?php

$footer = new \SCDS\Footer();
$footer->useFluidContainer();
$footer->render();
