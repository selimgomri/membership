<?php
$sqlSwim = "SELECT members.MemberID, members.MForename, members.MSurname, members.ASANumber, squads.SquadName, members.AccessKey FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) ORDER BY `members`.`MForename` , `members`.`MSurname` ASC;";
$result = mysqli_query(LINK, $sqlSwim);
$swimmerCount = mysqli_num_rows($result);
if ($swimmerCount > 0) {
  $content .= '
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Name</th>
          <th>Squad</th>
          <th>ASA Number</th>
          <th>Access Key</th>
        </tr>
      </thead>
      <tbody>';
  $resultX = mysqli_query(LINK, $sqlSwim);
  for ($i = 0; $i < $swimmerCount; $i++) {
    $swimmersRowX = mysqli_fetch_array($resultX, MYSQLI_ASSOC);
    $content .= "<tr>
      <td>" . $swimmersRowX['MForename'] . " " . $swimmersRowX['MSurname'] . "</td>
      <td>" . $swimmersRowX['SquadName'] . "</td>";
      if ($swimmersRowX['ASANumber'] == null) {
        $memID = $swimmersRowX['MemberID'];
        $asaN = "CLSX" . $memID;
        $content .= "<td><samp>" . $asaN . "<samp></td>";
        $sql = "UPDATE `members` SET ASANumber = '$asaN' WHERE `MemberID` = '$memID';";
        mysqli_query(LINK, $sql);
      }
      else {
        $content .= "<td><samp>" . $swimmersRowX['ASANumber'] . "</samp></td>";
      }
      $content .= "<td><samp>" . $swimmersRowX['AccessKey'] . "</samp></td>
    </tr>";
  }
  $content .= '
      </tbody>
    </table>
  </div>';
}
?>
