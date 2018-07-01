<?php
$pagetitle = "Register";
$title = "Register";
$content = "<p class=\"lead\">Take the register for your Squad</p>";
if (isset($_SESSION['return'])) {
  $content .= '<div class="alert alert-success">' . $_SESSION['return'] . '</div>';
  unset($_SESSION['return']);
}
$content .= '
<form method="post">
<div class="my-3 p-3 bg-white rounded box-shadow">
  <h2 class="border-bottom border-gray pb-2">Select Session</h2>
  <div class="form-group">
  <label for="session">Select Week</label>
  <select class="custom-select" name="date" id="date">';
    // Get the date of the week beginning
    $day = date('w');
    $week_start = date('Y-m-d', strtotime('-'.$day.' days'));

    $sql = "SELECT * FROM sessionsWeek ORDER BY WeekDateBeginning DESC LIMIT 4";
    $result = mysqli_query($link, $sql);
    $count = mysqli_num_rows($result);
    for ($i = 0; $i < $count; $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $content .= "<option value=\"" . $row['WeekID'] . "\"";
      $content .= ">Week Beginning " . date('j F Y', strtotime($row['WeekDateBeginning'])) . "</option>";
    }
  $content .= '</select>
  </div>
  <div class="form-group">
  <label for="squad">Select Squad</label>
  <select class="custom-select" name="squad" id="squad">
    <option value="0">Choose your squad from the menu</option>';
    $sql = "SELECT SquadID, SquadName FROM squads ORDER BY SquadFee DESC, SquadName ASC";
    $result = mysqli_query($link, $sql);
    $count = mysqli_num_rows($result);
    if ($count > 0) {
      for ($i = 0; $i < $count; $i++) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $content .= "<option value=\"" . $row['SquadID'] . "\"";
        $content .= ">" . $row['SquadName'] . "</option>";
      }
    }
  $content .= '</select>
  </div>
  <div class="form-group mb-0">
  <label for="session">Select Session</label>
  <select class="custom-select" id="session" name="session">
    <option selected>No squad selected</option>
  </select>
  </div>
  </div>

  <div class="my-3 p-3 bg-white rounded box-shadow">
  <div id="register">
  <div class="ajaxPlaceholder mb-0">Fill in the details above and we can load the register</div>
  </div>
  </div>
  </form>
  <script>
  function resetRegisterArea() {
    var register = document.getElementById("register");
    register.innerHTML = \'<div class="ajaxPlaceholder mb-0">Fill in the details above and we can load the register</div>\';
  }
  function getSessions() {
    var e = document.getElementById("squad");
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
            resetRegisterArea();
          }
        }
      xmlhttp.open("GET", "' . autoUrl("attendance/ajax/register/sessions") . '?squadID=" + value, true);
      xmlhttp.send();
      }
    }
  document.getElementById("squad").onchange=getSessions;
  function getRegister() {
    var e = document.getElementById("session");
    var value = e.options[e.selectedIndex].value;
    var date = document.getElementById("date");
    var dateValue = date.options[date.selectedIndex].value;
    console.log(value);
    console.log(dateValue);
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
      xmlhttp.open("GET", "' . autoUrl("attendance/ajax/register/sessions") . '?sessionID=" + value + "&date=" + dateValue, true);
      xmlhttp.send();
      }
    }
  document.getElementById("session").onchange=getRegister;
  </script>';
include BASE_PATH . "views/header.php";
include "attendanceMenu.php"; ?>
<div class="container-fluid">
<?php echo "<h1>" . $title . "</h1>";
echo $content; ?>
</div>
<?php
$fluidContainer = true;
include BASE_PATH . "views/footer.php";
