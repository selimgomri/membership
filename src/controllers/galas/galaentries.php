<?php
$userID = $_SESSION['UserID'];
$pagetitle = "Enter a Gala";
$sql = "SELECT `MemberID` FROM `members` WHERE `members`.`UserID` = '$userID';";
$result = mysqli_query($link, $sql);
$swimCount = mysqli_num_rows($result);
include BASE_PATH . "views/header.php";
include "galaMenu.php";
?>
<div class="container">
  <h2>Enter a gala</h2>
  <p class="lead">Enter a gala quickly and easily, with fewer steps than before.</p>
  <?php if ($swimCount > 0) { ?>
    <div class="my-3 p-3 bg-white rounded shadow">
      <form method="post">
      <h2 class="border-bottom border-gray pb-2">Select Swimmer and Gala</h2>
      <div class="form-group row">
        <label for="swimmer" class="col-sm-2 col-form-label">Select Swimmer</label>
        <div class="col-sm-10">
          <select class="custom-select" id="swimmer" name="swimmer" required><option value="null" selected>Select</option>
          <?php
          $sql = "SELECT * FROM `members` WHERE `members`.`UserID` = '$userID' ORDER BY `members`.`MForename`, `members`.`MSurname` ASC;";
          $result = mysqli_query($link, $sql);
          $squadCount = mysqli_num_rows($result);
          for ($i = 0; $i < $squadCount; $i++) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
            <option value="<?php echo $row['MemberID']; ?>">
              <?php echo $row['MForename'] . " " . $row['MSurname']; ?></option>
          <?php } ?>
          </select>
        </div>
      </div>
      <div class="form-group row">
        <label for="gala" class="col-sm-2 col-form-label">Select Gala</label>
        <div class="col-sm-10">
          <select class="custom-select" id="gala" name="gala" required><option value="null" selected>Select</option>
          <?php
          $sql = "SELECT * FROM `galas` WHERE ClosingDate >= CURDATE() ORDER BY `galas`.`ClosingDate` ASC;";
          $result = mysqli_query($link, $sql);
          $squadCount = mysqli_num_rows($result);
          for ($i = 0; $i < $squadCount; $i++) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
            <option value="<?php echo $row['GalaID']; ?>"><?php echo $row['GalaName']; ?></option>
          <?php } ?>
          </select>
        </div>
      </div>
      <h2>Select Swims</h2>
      <div class="ajaxArea" id="output">
        <div class="ajaxPlaceholder">Select a swimmer and gala
        </div>
      </div>
      <p class="mb-0">
        <button type="submit" id="submit" class="btn btn-outline-dark">Submit</button>
      </p>
      </div>
      <script>
      function clearOutput() {
        document.getElementById("output").innerHTML = '<div class="ajaxPlaceholder">Select a swimmer and gala</div>';
      }
        function enableBtn(swimmer, gala) {
          var swimmer = document.getElementById("swimmer");
          var gala = document.getElementById("gala");
          if (swimmer.value != "null" && gala.value != "null") {
            document.getElementById("submit").disabled = false;
          }
          else {
            document.getElementById("submit").disabled = true;
          }
         }
        document.getElementById("submit").disabled = true;
        var swimmer = document.getElementById("swimmer");
        var gala = document.getElementById("gala");
        swimmer.addEventListener("change", enableBtn);
        gala.addEventListener("change", enableBtn);

        function getResult() {
          var gala = document.getElementById("gala");
          var swimmer = document.getElementById("swimmer");
          var swimmerValue = swimmer.value;
          var galaValue = gala.options[gala.selectedIndex].value;
          console.log(galaValue);
          if (galaValue=="null") {
            clearOutput();
          }
          else {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                console.log("We got here");
                document.getElementById("output").innerHTML = this.responseText;
                console.log(this.responseText);
              }
            }
            var ajaxRequest = "<?php echo autoURL('galas/ajax/entryForm'); ?>?galaID=" + galaValue + "&swimmer=" + swimmerValue;
            console.log(ajaxRequest);
            xmlhttp.open("GET", ajaxRequest, true);
            xmlhttp.send();
          }
        }
        document.getElementById("gala").onchange=clearOutput;
        document.getElementById("swimmer").onchange=clearOutput;
        document.getElementById("gala").onchange=getResult;
      </script>
    </form>
    <?php
  }
  else { ?>
    <div class="alert alert-warning">
      <strong>You don't have any swimmers associated with your account</strong> <br>
      Please <a href="<?php echo autoUrl("myaccount/addswimmer"); ?>" class="alert-link">add some swimmers in My Account</a>, then try again
    </div>
  <?php } ?>
</div>
<?php include BASE_PATH . "views/footer.php";
