<?php
$access = $_SESSION['AccessLevel'];
$sql = "";
if (isset($_POST["search"])) {
  // get the search term parameter from post
  $search = mysqli_real_escape_string($link, htmlentities($_POST["search"]));
  $sql = "SELECT `Forename`, `Surname`, `MandateID`, `users`.`UserID` FROM users LEFT JOIN `paymentPreferredMandate` ON users.userID = paymentPreferredMandate.UserID WHERE Surname LIKE '%$search%' AND `AccessLevel` = 'Parent' ORDER BY Forename, Surname ASC;";
}

$target = $_POST['target'];

$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);
if ($count > 0) {
  $output = '
  <div class="table-md-responsive">
    <table class="table table-hover mb-0">
      <thead class="thead-light">
        <tr>
          <th>Name</th>
					<th>DD Status</th>
        </tr>
      </thead>
      <tbody>';
  $resultX = mysqli_query($link, $sql);
  for ($i = 0; $i < $count; $i++) {
    $row = mysqli_fetch_array($resultX, MYSQLI_ASSOC);
    $url = null;
    if ($target == "currentfees") {
      $url = autoUrl("payments/current/" . $row['UserID'] . "");
    } else if ($target == "transactionhistory") {
      $url = autoUrl("payments/history/users/" . $row['UserID'] . "");
    }
    $output .= "<tr>
      <td><a href=\"" . $url . "\">" . $row['Forename'] . " " . $row['Surname'] . "</a></td>";
			if ($row['MandateID'] == null || $row['MandateID'] == "") {
			$output .= "<td>No Direct Debit set up</td>";
		} else {
			$output .= "<td></td>";
		}
    $output .= "</tr>";
  }
  $output .= '
      </tbody>
    </table>
  </div>';
}
else {
  $output = "<div class=\"alert alert-warning mb-0\"><strong>No users found for that name</strong> <br>Please try another search</div>";
}
echo $output;
?>
