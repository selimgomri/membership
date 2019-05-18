<?php

global $db;

$user = $_SESSION['UserID'];

$list = $db->prepare("SELECT * FROM `targetedLists` WHERE `ID` = ?");
$list->execute([$id]);
$row = $list->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
  halt(404);
}

$squads = $db->query("SELECT * FROM `squads` ORDER BY `SquadFee` DESC, `SquadName` ASC");

$pagetitle = htmlspecialchars($row['Name']) . " - Lists";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

 ?>

<div class="container">
  <div class="row align-items-center mb-3">
    <div class="col-md-6">
	    <h1><?=htmlspecialchars($row['Name'])?></h1>
    </div>
    <div class="col text-right">
      <a href="<?=autoUrl("notify/lists/" . $id . "/edit")?>"
        class="btn btn-dark">Edit</a>
      <a href="<?=autoUrl("notify/lists/" . $id . "/delete")?>"
        class="btn btn-danger">Delete</a>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div id="output" class="mb-3">
        <div class="ajaxPlaceholder">
          <span class="h1 d-block">
            <i class="fa fa-spin fa-circle-o-notch" aria-hidden="true"></i>
            <br>Loading Content
          </span>If content does not display, please turn on JavaScript
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card">
        <form class="card-body">
          <div class="form-group">
            <label for="squadSelect">Select Squad (Optional)</label>
            <select class="custom-select" id="squadSelect" name="squadSelect">
              <option value="all" selected>Choose...</option>
              <?php while ($squadsRow = $squads->fetch(PDO::FETCH_ASSOC)) { ?>
              <option value="<?=$squadsRow['SquadID']?>">
                <?=htmlspecialchars($squadsRow['SquadName'])?>
              </option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="swimmerSelect">Select Swimmer</label>
            <select class="custom-select" id="swimmerSelect" name="swimmerSelect">
              <option selected>Select squad first</option>
            </select>
          </div>
            <button type="button" class="btn btn-success" id="addSwimmer">
              Add Swimmer to Targeted List
            </button>
            <div id="status">
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function getSwimmers() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("output").innerHTML = this.responseText;
    }
  }
  xhttp.open("POST", "<?php echo autoUrl("notify/lists/ajax/" . $id); ?>", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("response=getSwimmers");
}

function getSwimmersForSquad() {
  var squad = (document.getElementById("squadSelect")).value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("swimmerSelect").innerHTML = this.responseText;
    }
  }
  xhttp.open("POST", "<?php echo autoUrl("notify/lists/ajax/" . $id); ?>", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("response=squadSelect&squadSelect=" + squad);
}

function addSwimmerToExtra() {
  var swimmer = (document.getElementById("swimmerSelect")).value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      getSwimmers();
      getSwimmersForSquad();
      document.getElementById("status").innerHTML =
      '<div class="mt-3 mb-0 alert alert-success alert-dismissible fade show" role="alert">' +
      '<strong>Successfully Added Swimmer</strong>'  +
      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
      '<span aria-hidden="true">&times;</span>' +
      '</button>' +
      '</div>';
    } else {
      document.getElementById("status").innerHTML =
      '<div class="mt-3 mb-0 alert alert-warning alert-dismissible fade show" role="alert">' +
      '<strong>Unable to add swimmer</strong>' +
      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
      '<span aria-hidden="true">&times;</span>' +
      '</button>' +
      '</div>';
    }
  }
  xhttp.open("POST", "<?php echo autoUrl("notify/lists/ajax/" . $id); ?>", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("response=insert&swimmerInsert=" + swimmer);
}

function dropSwimmerFromExtra(relation) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      getSwimmers();
    }
  }
  xhttp.open("POST", "<?php echo autoUrl("notify/lists/ajax/" . $id); ?>", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("response=dropRelation&relation=" + relation);
  console.log(relation);
}

var entryTable = document.querySelector("#output");
entryTable.addEventListener("click", clickPropogation, false);

function clickPropogation(e) {
  if (e.target !== e.currentTarget) {
    var clickedItem = e.target.id;
    var clickedItemValue;
    if (clickedItem != "") {
      var clickedItemValue = document.getElementById(clickedItem).value;
      dropSwimmerFromExtra(clickedItemValue);
    }
  }
  e.stopPropagation();
}

// Call getResult immediately
getSwimmers();
getSwimmersForSquad();
document.getElementById("squadSelect").onchange=getSwimmersForSquad;
document.getElementById("addSwimmer").onclick=addSwimmerToExtra;
</script>

<?php include BASE_PATH . "views/footer.php";
