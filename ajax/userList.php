<?php
include_once "../database.php";
$access = $_SESSION['AccessLevel'];
$count = 0;
if ($access == "Admin" || $access == "Galas") {
  $sql = "";
  if (isset($_POST["search"])) {
    // get the search term parameter from post
    $search = mysqli_real_escape_string($link, htmlentities($_POST["search"]));
    $sql = "SELECT * FROM users WHERE Surname LIKE '%$search%' ORDER BY Forename, Surname ASC;";
  }

  $result = mysqli_query($link, $sql);
  $swimmerCount = mysqli_num_rows($result);
  if ($swimmerCount > 0) {
    $output = '
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Account Type</th>
          </tr>
        </thead>
        <tbody>';
    $resultX = mysqli_query($link, $sql);
    for ($i = 0; $i < $swimmerCount; $i++) {
      $swimmersRowX = mysqli_fetch_array($resultX, MYSQLI_ASSOC);
      $swimmerLink = autoUrl("users/" . $swimmersRowX['UserID'] . "");
      $output .= "<tr>
        <td><a href=\"" . $swimmerLink . "\">" . $swimmersRowX['Forename'] . " " . $swimmersRowX['Surname'] . "</a></td>
        <td>" . $swimmersRowX['AccessLevel'] . "</td>
      </tr>";
    }
    $output .= '
        </tbody>
      </table>
    </div>';
  }
  else {
    $output = "<div class=\"alert alert-warning\"><strong>No users found for that name</strong> <br>Please try another search</div>";
  }
	echo $output;
}
else {
  echo "Access not allowed";
}
?>
