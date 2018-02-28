<?php

$squadID = $search = "";
parse_str($_SERVER['QUERY_STRING'], $queries);
if (isset($queries['squadID'])) {
  $squadID = intval($queries['squadID']);
}
if (isset($queries['search'])) {
  $search = $queries['search'];
}

$pagetitle = "Users";
//$squadID = null;
$title = "User Directory";
$content = "<p class=\"lead\">A list of users. Useful for changing account settings.</p>";
if ($access == "Committee" || $access == "Admin") {
  $content .= "<p><a href=\"../add-member\" class=\"btn btn-outline-dark\">Add member</a> <a href=\"../accesskeys\" class=\"btn btn-outline-dark\">Access Keys</a></p>";
}
$sql = "SELECT * FROM `squads` ORDER BY `squads`.`SquadFee` DESC;";
$result = mysqli_query($link, $sql);
$squadCount = mysqli_num_rows($result);
$content .= '
<div class="form-group">
  <label class="sr-only" for="search">Search by Surname</label>
  <input class="form-control" placeholder="Surname" id="search" name="search" value="' . $search . '">
</div>

<div id="output"><div class="ajaxPlaceholder"><span class="h1 d-block"><i class="fa fa-spin fa-circle-o-notch" aria-hidden="true"></i><br>Loading Content</span>If content does not display, please turn on JavaScript</div></div>

<script>
function getResult() {
  var search = document.getElementById("search");
  var searchValue = search.value;
  console.log(searchValue);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        console.log("We got here");
        document.getElementById("output").innerHTML = this.responseText;
        console.log(this.responseText);
        window.history.pushState("string", "Title", "' . autoUrl("users/filter/") . '?search=" + searchValue);
      }
    }
    xhttp.open("POST", "' . autoURL("ajax/userList.php") . '", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("search=" + searchValue);
    console.log("Sent");
}
// Call getResult immediately
getResult();

document.getElementById("search").oninput=getResult;
</script>';
?>