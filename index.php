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
<h2>Your Entries for Upcoming Galas</h2>
<?php
  echo enteredGalas($link, $userID);
?>
<?php }
elseif ($_SESSION['AccessLevel'] == "Coach") { ?>
<h2>for Coaches</h2>
<p class="lead">Take a register for your squad</p>
<p><a class="btn btn-success" href="<?php echo autoUrl('attendance') ?>">Register</a></p>
<?php }
else { ?>
<h2>View Gala Entries</h2>
<p class="lead">Gala entries are online</p>
<p><a class="btn btn-success" href="<?php echo autoUrl('galas') ?>">View</a></p>
<?php } ?>
</div>


<?php include "footer.php"; ?>
