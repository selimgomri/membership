<?php

$search = "";
parse_str($_SERVER['QUERY_STRING'], $queries);
if (isset($queries['search'])) {
  $search = $queries['search'];
}
$pagetitle = "Users";
include BASE_PATH . "views/header.php";
?>
<div class="container">
  <h1>User Directory</h1>
  <p class="lead">A list of users. Useful for changing account settings.</p>
  <div class="form-group">
    <label class="sr-only" for="search">Search by Surname</label>
    <input class="form-control" placeholder="Surname" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
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

</div>

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
        window.history.pushState("string", "Title", "<?php echo autoUrl("users"); ?>?search=" + searchValue);
      }
    }
    xhttp.open("POST", "<?php echo autoURL("users/ajax/userList"); ?>", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("search=" + searchValue);
    console.log("Sent");
}
// Call getResult immediately
getResult();

document.getElementById("search").oninput=getResult;
</script>

<?php include BASE_PATH . "views/footer.php";
