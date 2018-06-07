<script>
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
        }
      }
    xmlhttp.open("GET", "../ajax/registerSessions.php?squadID=" + value, true);
    xmlhttp.send();
    }
  }
  /*function getRegister() {
    var e = document.getElementById("session");
    var value = e.options[e.selectedIndex].value;
      if (value == "") {
        document.getElementById("register").innerHTML = "<p>Loading</p>";
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
      xmlhttp.open("GET", "../ajax/registerSessions.php?sessionID=" + value, true);
      xmlhttp.send();
      }
    }*/
document.getElementById("squad").onchange=getSessions;
//document.getElementById("session").onchange=getRegister;
</script>

<?php
$sql = "SELECT * FROM (sessions INNER JOIN squads ON sessions.SquadID = squads.SquadID)";
$result = mysqli_query($link, $sql);
$swimmerCount = mysqli_num_rows($result);
$content .= '
<form method="post" action="register-get">
  <div class="form-group">
  <label for="squad">Select Squad</label>
  <select class="custom-select" name="squad" id="squad">
    <!--<option value="0">Choose your squad from the menu</option>-->
    <option value="1">A</option>
    <option value="2">B1</option>
    <option value="3">B2</option>
    <option value="4">B3</option>#
    <option value="5">C</option>
    <option value="6">D</option>
    <option value="7">E</option>
    <option value="8">Tadpoles</option>
    <option value="9">Frogs</option>
    <option value="10">Dolphins</option>
  </select>
  </div>
  <div class="form-group">
  <label for="session" onchange="getSessions()">Select Session</label>
  <select class="custom-select" id="session" name="session">
    <option selected>Choose the session from the menu</option>
  </select>
  </div>
  </form>';
  $sql = "SELECT * FROM (squads INNER JOIN members ON squads.SquadID = members.SquadID) WHERE members.SquadID = '1'";
  $result = mysqli_query($link, $sql);
  $swimmerCount = mysqli_num_rows($result);
  for ($i=0; $i<$swimmerCount; $i++) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $content .= "
    <div class=\"custom-control custom-checkbox mb-3\">
      <input type=\"checkbox\" class=\"custom-control-input\" name=\"Member-" . $row['MemberID'] . "\" value=\"1\" id=\"Member-" . $row['MemberID'] . "\">
      <label class=\"custom-control-label\" for=\"Member-" . $row['MemberID'] . "\">" . $row['MForename'] . " " . $row['MSurname'] . "</label>
    </div>";
  }
  $content .= '<p><button class="btn btn-outline-dark">Save Register</button> <button id="button">click</button></p>
  <script>
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
          }
        }
      xmlhttp.open("GET", "../ajax/registerSessions.php?squadID=" + value, true);
      xmlhttp.send();
      }
    }
  document.getElementById("squad").onchange=getSessions;
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
          }
        }
      xmlhttp.open("GET", "../ajax/registerSessions.php?squadID=" + value, true);
      xmlhttp.send();
      }
    }
  document.getElementById("squad").onchange=getSessions;
  </script>';
?>
