<?php
$sql = "SELECT * FROM (sessions INNER JOIN squads ON sessions.SquadID = squads.SquadID)";
$result = mysqli_query($link, $sql);
$swimmerCount = mysqli_num_rows($result);
$content .= '
<form method="post" action="register-get">
  <div class="form-group">
  <label for="squad">Select Squad</label>
  <select class="custom-select" name="squad">
    <option selected>Choose your squad from the menu</option>
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
  <label for="session">Select Session</label>
  <select class="custom-select" name="session">
    <option selected>Choose the session from the menu</option>
    ';
    for ($i=0; $i<$swimmerCount; $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $content .= "<option value=\"" . $row['SessionID'] . "\">" . $row['SessionName'] . "</option>";
    }
    $content .= '
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
  $content .= '<p><button class="btn btn-success">Save Register</button></p>';
?>
