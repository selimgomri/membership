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
  $swimmers = '<p class="lead">You have ' . $swimmerCount . ' ' . $swimmerS . '</p>';
  if ($swimmerCount == 0) {
    $swimmers .= '<p><a href="' . autoUrl("myaccount/add-swimmer.php") . '" class="btn btn-success">Add a Swimmer</a></p>'; 
  }
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
  echo mySwimmersTable($link, $userID);
?>
</div>

<?php include "footer.php"; ?>
