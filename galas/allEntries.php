<?php
$pagetitle = "Gala Entries";
$galaID = $surname = null;
$title = "View Gala Entries by Gala";
if (!isset($_SESSION['AllEntriesResponse'])) {
  $content = "<p class=\"lead\">Search entries for upcoming galas. Search by Gala or Gala and Surname.</p>";
  $sql = "SELECT * FROM `galas` ORDER BY `galas`.`GalaDate` DESC LIMIT 0, 15;";
  $result = mysqli_query($link, $sql);
  $galaCount = mysqli_num_rows($result);
  $content .= "
  <form method=\"post\" action=\"entries-action\">
  <div class=\"form-group row\">
  <label class=\"col-sm-2\" for=\"gala\">Select a Gala</label>
  <div class=\"col\">
  <select class=\"custom-select\" placeholder=\"Select a Gala\" id=\"galaID\" name=\"galaID\">";
  //$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  for ($i = 0; $i < $galaCount; $i++) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $lastDate = new DateTime($row['GalaDate']);
    $theDate = new DateTime('now');
    $lastDate = $lastDate->format('Y-m-d');
    $theDate = $theDate->format('Y-m-d');
    if ($lastDate >= $theDate) {
      $content .= "<option value=\"" . $row['GalaID'] . "\"";
      $content .= ">" . $row['GalaName'] . "</option>";
    }
  }
  $content .= "</select></div></div>
  <div class=\"form-group row\">
    <label class=\"col-sm-2\" for=\"gala\">Enter Surname</label>
    <div class=\"col\">
  <input class=\"form-control\" name=\"surname\" value=\"" . $surname . "\">
  </div></div><p><button type=\"submit\" class=\"btn btn-success\">
    Filter
  </button></p></form>";
  $content .= '
  <script>
  function getSessions() {
    var e = document.getElementById("galaID");
    var value = e.options[e.selectedIndex].value;
    console.log(value);
      if (value == "") {
        document.getElementById("session").innerHTML = "<option selected>Choose the session from the menu</option>";
        return;
      }
      else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("session").innerHTML = this.responseText;
            console.log(this.responseText);
          }
        }
      xmlhttp.open("GET", "../ajax/galaEntries.php?squadID=" + value, true);
      xmlhttp.send();
      }
    }
  document.getElementById("galaID").onchange=getSessions;
  function getRegister() {
    var e = document.getElementById("session");
    var value = e.options[e.selectedIndex].value;
    console.log(value);
      if (value == "") {
        document.getElementById("register").innerHTML = "<option selected>Choose the session from the menu</option>";
        return;
      }
      else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("register").innerHTML = this.responseText;
            console.log(this.responseText);
          }
        }
      xmlhttp.open("GET", "../ajax/galaEntries.php?sessionID=" + value, true);
      xmlhttp.send();
      }
    }
  document.getElementById("session").onchange=getRegister;
  </script>';
}

$content .= upcomingGalasBySearch($link, $searchSQL);
?>
