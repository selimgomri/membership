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

<?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
<!-- My Swimmers Section -->
<h2>My Swimmers</h2>
<?php
  echo $swimmerDisplayText;
  echo mySwimmersTable($link, $userID);
?>

<!-- My Fees Section -->
<h2>Your monthly fees</h2>
<?php
  echo myMonthlyFeeTable($link, $userID);
?>

<!-- My Swimmers Section -->
<h2>Entries for Upcoming Galas</h2>
<?php
  echo enteredGalas($link, $userID);
?>
<?php }
else { ?>
<h2>Notices for Coaches and Volunteers</h2>
<div class="card mb-3">
  <div class="card-body">
    <h5 class="card-title">Gala Entries coming soon to Membership Management</h5>
    <p class="card-text">You'll soon be able see and manage gala entries with our new membership management system.</p>
  </div>
</div>
<div class="card mb-3">
  <div class="card-body">
    <h5 class="card-title">New Messaging System coming soon</h5>
    <p class="card-text">Selected users will soon be able to easily send a message to parents of an entire squad or all parents in the club.</p>
  </div>
</div>
<?php } ?>
</div>


<?php include "footer.php"; ?>
