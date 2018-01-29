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
  <div class=\"form-group row\">
  <label class=\"col-sm-2\" for=\"gala\">Select a Gala</label>
  <div class=\"col\">
  <select class=\"custom-select\" placeholder=\"Select a Gala\" id=\"galaID\" name=\"galaID\">
  <option selected >Select a gala</option>
  <option value=\"allGalas\" >Show All Gala Entries</option>";
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
  <input class=\"form-control\" name=\"search\" id=\"search\">
  </div></div>";
  $content .= "<div class=\"table-responsive\" id=\"output\"></div>";
  $content .= '
  <script>
  function getResult() {
    var gala = document.getElementById("galaID");
    var galaValue = gala.options[gala.selectedIndex].value;
    var search = document.getElementById("search");
    var searchValue = search.value;
    console.log(galaValue);
    console.log(searchValue);
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          console.log("We got here");
          document.getElementById("output").innerHTML = this.responseText;
          console.log(this.responseText);
        }
      }
      var ajaxRequest = "' . autoURL("ajax/galaEntries.php") . '?galaID=" + galaValue + "&search=" + searchValue;
      console.log(ajaxRequest);
      xmlhttp.open("GET", ajaxRequest, true);
      xmlhttp.send();
  }
  document.getElementById("galaID").onchange=getResult;
  document.getElementById("search").oninput=getResult;
  </script>';
  $content .= '
  <script>
  document.querySelectorAll(\'*[id^="processedEntry-"]\');


  /*var entryTable = document.querySelector("#output");
  entryTable.addEventListener("click", clickPropogation, false);

  function clickPropogation(e) {
      if (e.target !== e.currentTarget) {
          var clickedItem = e.target.id;
          console.log(clickedItem);
      }
      e.stopPropagation();
  }*/
  </script>';
}
?>
