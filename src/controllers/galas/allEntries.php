<?php

global $db;

$pagetitle = "Gala Entries";
$galaID = $surname = null;
$title = "View Gala Entries by Gala";

$use_white_background = true;

$galaIDParam = $search = $sex = "";
if (isset($_GET['galaID'])) {
  $galaIDParam = intval($_GET['galaID']);
}
if (isset($_GET['search'])) {
  $search = $_GET['search'];
}
if (isset($_GET['sex'])) {
  $sex = $_GET['sex'];
}

$galas = $db->query("SELECT GalaID, GalaName FROM `galas` WHERE GalaDate >= CURDATE() ORDER BY `galas`.`GalaDate` DESC");

include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>
<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas")?>">Galas</a></li>
      <li class="breadcrumb-item active" aria-current="page">Gala entries</li>
    </ol>
  </nav>
  <h1>Gala Entries</h1>


  <div class="d-print-none">
    <p class="lead">Search entries for upcoming galas. Search by Gala or Gala and Surname.</p>
    <div class="form-row">
      <div class="col-md-4">
        <div class="form-group">
          <label class="" for="gala">Select a Gala</label>
          <select class="custom-select" placeholder="Select a Gala" id="galaID" name="galaID">
            <option>Select a gala</option>
            <option value="allGalas" <?php if ($galaIDParam == "allGalas") { ?> selected <?php } ?>>Show All Gala
              Entries</option>

            <?php while ($row = $galas->fetch(PDO::FETCH_ASSOC)) { ?>
            <option value="<?=$row['GalaID']?>" <?php if ($galaIDParam == $row['GalaID']) { ?> selected <?php } ?>>
              <?=htmlspecialchars($row['GalaName'])?>
            </option>
            <?php } ?>

          </select>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="" for="sex">Select Sex</label>
          <select class="custom-select" placeholder="Select Sex" id="sex" name="sex">
            <option value="all" <?php if ($sex == "all") { ?> selected <?php } ?>>All Swimmers</option>
            <option value="f" <?php if ($sex == "f") { ?> selected <?php } ?>>
              Female
            </option>
            <option value="m" <?php if ($sex == "m") { ?> selected <?php } ?>>
              Male
            </option>
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          <label for="gala">Enter Surname</label>
          <input class="form-control" placeholder="Search" name="search" id="search"
            value="<?=htmlspecialchars($search)?>">
        </div>
      </div>
    </div>

    <div class="table-responsive-md" id="output">
      <div class="ajaxPlaceholder">
        <strong>Select a Gala</strong><br>
        Entries will appear here when you select a gala
      </div>
    </div>
  </div>
</div>

<script>
function getResult() {
  var gala = document.getElementById("galaID");
  var galaValue = gala.options[gala.selectedIndex].value;
  var search = document.getElementById("search");
  var searchValue = search.value;
  var sex = document.getElementById("sex");
  var sexValue = sex.value;
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("output").innerHTML = this.responseText;
      window.history.replaceState("string", "Title", "<?=autoUrl("galas/entries")?>?galaID=" + galaValue + "&sex=" +
        sexValue + "&search=" + searchValue);
    } else {
      console.log(this.status);
    }
  }
  var ajaxRequest = "<?=autoURL("galas/ajax/entries")?>?galaID=" + galaValue + "&sex=" + sexValue + "&search=" +
    searchValue;
  xmlhttp.open("GET", ajaxRequest, true);
  xmlhttp.send();
}
// Call on page load
getResult();

document.getElementById("galaID").onchange = getResult;
document.getElementById("search").oninput = getResult;
document.getElementById("sex").oninput = getResult;
</script>

<script>
document.querySelectorAll('*[id^="processedEntry-"]');

var entryTable = document.querySelector("#output");
entryTable.addEventListener("click", clickPropogation, false);

function clickPropogation(e) {
  if (e.target !== e.currentTarget) {
    var clickedItem = e.target.id;
    var clickedItemChecked;
    if (clickedItem != "") {
      var item = document.getElementById(clickedItem);
      var clickedItemChecked = item.checked;
      console.log(clickedItem);
      console.log(clickedItemChecked);
      if (item.dataset.buttonAction == "mark-processed") {
        markProcessed(clickedItem, clickedItemChecked);
      } else if (item.dataset.buttonAction == "mark-paid") {
        markPaid(clickedItem, clickedItemChecked);
      }
    }
  }
  e.stopPropagation();
}

function markProcessed(clickedItem, clickedItemChecked) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById(clickedItem).innerHTML = "WORKED" /*this.responseText*/ ;
    }
  };
  xhttp.open("POST", "<?=autoUrl("galas/ajax/entryProcessed")?>", true);
  console.log("POST", "<?=autoUrl("galas/ajax/entryProcessed")?>", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("processedID=" + clickedItem + "&clickedItemChecked=" + clickedItemChecked + "&verify=markProcessed");
  console.log("processedID=" + clickedItem + "&clickedItemChecked=" + clickedItemChecked + "&verify=markProcessed")
  console.log("Sent");
}

function markPaid(clickedItem, clickedItemChecked) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById(clickedItem).innerHTML = "WORKED" /*this.responseText*/ ;
    }
  };
  xhttp.open("POST", "<?=autoUrl("galas/ajax/entryProcessed")?>", true);
  console.log("POST", "<?=autoUrl("galas/ajax/entryProcessed")?>", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("processedID=" + clickedItem + "&clickedItemChecked=" + clickedItemChecked + "&verify=markPaid");
  console.log("processedID=" + clickedItem + "&clickedItemChecked=" + clickedItemChecked + "&verify=markPaid");
  console.log("Sent");
}
</script>
<?php include BASE_PATH . "views/footer.php";

?>