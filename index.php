<?php
  $pagetitle = "Homepage";
  include "header.php";
  $userID = $_SESSION['UserID'];

  $sqlSwim = "SELECT members.MemberID, members.MForename, members.MSurname, users.Forename, users.Surname, users.EmailAddress, members.ASANumber, squads.SquadName, squads.SquadFee FROM ((members INNER JOIN users ON members.UserID = users.UserID) INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.UserID = '$userID';";
  $result = mysqli_query($link, $sqlSwim);
  $swimmerCount = mysqli_num_rows($result);
  $swimmersRow = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $swimmerS = '';
  if ($swimmerCount == 0 || $swimmerCount > 1) {
    $swimmerS = 'swimmers';
  }
  else {
    $swimmerS = 'swimmer';
  }
  $swimmers = '<p class="lead">You have ' . $swimmerCount . ' ' . $swimmerS . '</p>
  ';
  $swimmerDisplayText = $swimmers;
?>
<div class="container">
<h1>Hello <?php echo $_SESSION['Forename'] . " " . $_SESSION['Surname'] ?></h1>
<p>Welcome to our new online membership system.</p>
<hr>

<!-- My Swimmers Section -->
<h2>My Swimmers</h2>
<?php
  echo $swimmerDisplayText;
  if ($swimmerCount > 0) {
    echo '
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Squad</th>
            <th>Fee</th>
            <th>ASA Number</th>
          </tr>
        </thead>
        <tbody>';
    $resultX = mysqli_query($link, $sqlSwim);
    for ($i = 0; $i < $swimmerCount; $i++) {
      $swimmersRowX = mysqli_fetch_array($resultX, MYSQLI_ASSOC);
      echo "<tr>
        <td>" . $swimmersRowX['MForename'] . " " . $swimmersRowX['MSurname'] . "</td>
        <td>" . $swimmersRowX['SquadName'] . "</td>
        <td>" . $swimmersRowX['SquadFee'] . "</td>
        <td><a href=\"https://www.swimmingresults.org/biogs/biogs_details.php?tiref=" . $swimmersRowX['ASANumber'] . "\" target=\"_blank\" title=\"ASA Biog\">" . $swimmersRowX['ASANumber'] . " <i class=\"fa fa-external-link\" aria-hidden=\"true\"></i></a></td>
      </tr>";
    }
    echo '
        </tbody>
      </table>
    </div>';
  }
?>
</div>

<?php include "footer.php"; ?>
