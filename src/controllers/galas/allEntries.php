<?php
$pagetitle = "Gala Entries";
$galaID = $surname = null;
$title = "View Gala Entries by Gala";

$use_white_background = true;

$galaIDParam = $search = $sex = "";
mysqli_real_escape_string(parse_str($_SERVER['QUERY_STRING'], $queries));
if (isset($queries['galaID'])) {
  $galaIDParam = intval($queries['galaID']);
}
if (isset($queries['search'])) {
  $search = $queries['search'];
}
if (isset($queries['sex'])) {
  $sex = $queries['sex'];
}

$content = "<div class=\"d-print-none\"><p class=\"lead\">Search entries for upcoming galas. Search by Gala or Gala and Surname.</p>";
$sql = "SELECT * FROM `galas` WHERE GalaDate >= CURDATE() ORDER BY `galas`.`GalaDate` DESC LIMIT 0, 30;";
$result = mysqli_query($link, $sql);
$galaCount = mysqli_num_rows($result);
$content .= "
<div class=\"form-group row\">
<label class=\"col-sm-2\" for=\"gala\">Select a Gala</label>
<div class=\"col\">
<select class=\"custom-select\" placeholder=\"Select a Gala\" id=\"galaID\" name=\"galaID\">
<option>Select a gala</option>

<option value=\"allGalas\" ";
if ($galaIDParam == "allGalas") {
    $content .= " selected ";
}
$content .= ">Show All Gala Entries</option>";
//$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
for ($i = 0; $i < $galaCount; $i++) {
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $content .= "<option value=\"" . $row['GalaID'] . "\"";
  if ($galaIDParam == $row['GalaID']) {
      $content .= " selected ";
  }
  $content .= ">" . $row['GalaName'] . "</option>";
}
$content .= "</select></div></div>
<div class=\"form-group row\">
  <label class=\"col-sm-2\" for=\"sex\">Select Sex</label>
  <div class=\"col\">
<select class=\"custom-select\" placeholder=\"Select Sex\" id=\"sex\" name=\"sex\">
<option value=\"all\"";
if ($sex == "all") {
  $content .= ' selected ';
}
$content .= "
>All Swimmers</option>
<option value=\"f\"";
if ($sex == "f") {
  $content .= ' selected ';
}
$content .= "
>Female</option>
<option value=\"m\"";
if ($sex == "m") {
  $content .= ' selected ';
}
$content .= ">Male</option>
</select></div></div>

<div class=\"form-group row\">
  <label class=\"col-sm-2\" for=\"gala\">Enter Surname</label>
  <div class=\"col\">
<input class=\"form-control\" name=\"search\" id=\"search\" value=\"" . $search . "\">
</div></div></div>";
$content .= "<div class=\"table-responsive-md\" id=\"output\"><div class=\"ajaxPlaceholder\"><strong>Select a Gala</strong> <br>Entries will appear here when you select a gala</div></div>";
$content .= '
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
        window.history.replaceState("string", "Title", "' .
        autoUrl("galas/entries") . '?galaID=" + galaValue + "&sex=" + sexValue +
        "&search=" + searchValue);
      }
    }
    var ajaxRequest = "' . autoURL("galas/ajax/entries") . '?galaID=" +
    galaValue + "&sex=" + sexValue + "&search=" + searchValue;
    xmlhttp.open("GET", ajaxRequest, true);
    xmlhttp.send();
}
// Call on page load
getResult();

document.getElementById("galaID").onchange=getResult;
document.getElementById("search").oninput=getResult;
document.getElementById("sex").oninput=getResult;
</script>';
$content .= '
<script>
document.querySelectorAll(\'*[id^="processedEntry-"]\');


var entryTable = document.querySelector("#output");
entryTable.addEventListener("click", clickPropogation, false);

function clickPropogation(e) {
    if (e.target !== e.currentTarget) {
        var clickedItem = e.target.id;
        var clickedItemChecked;
        if (clickedItem != "") {
          var clickedItemChecked = document.getElementById(clickedItem).checked;
          console.log(clickedItem);
          console.log(clickedItemChecked);
          markProcessed(clickedItem, clickedItemChecked);
        }
    }
    e.stopPropagation();
}

function markProcessed(clickedItem, clickedItemChecked) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById(clickedItem).innerHTML = "WORKED"/*this.responseText*/;
    }
  };
  xhttp.open("POST", "' . autoUrl("galas/ajax/entryProcessed") . '", true);
  console.log("POST", "' . autoUrl("galas/ajax/entryProcessed") . '", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("processedID=" + clickedItem + "&clickedItemChecked=" + clickedItemChecked + "&verify=markProcessed");
  console.log("processedID=" + clickedItem + "&clickedItemChecked=" + clickedItemChecked + "&verify=markProcessed")
  console.log("Sent");
}
</script>';

include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>
<div class="container">
<?php echo "<h1>" . $title . "</h1>";
echo $content; ?>
</div>
<?php include BASE_PATH . "views/footer.php";

?>
